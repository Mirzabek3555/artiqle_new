<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * DOCX → HTML konverter (formulalar saqlanadi)
 * 
 * Pipeline:
 * 1. DOCX ichidan OMML formulalarni topish va MathML/LaTeX ga aylantirish
 * 2. DOCX ni modifikatsiya qilish — formulalar o'rniga placeholder matn qo'yish
 * 3. Mammoth.js orqali modifikatsiya qilingan DOCX → HTML
 * 4. Placeholder larni formula HTML bilan almashtirish
 * 5. Kimyoviy formulalar → subscript HTML
 */
class DocxProcessorService
{
    private string $scriptsPath;
    private string $nodePath;

    public function __construct()
    {
        $this->scriptsPath = base_path('scripts');
        $this->nodePath = $this->findNodePath();
    }

    /**
     * Asosiy metod: DOCX faylni HTML ga aylantirish
     * Formulalar, jadvallar, rasmlar (linked + embedded) saqlanadi
     */
    public function processDocx(string $docxPath): array
    {
        if (!file_exists($docxPath)) {
            throw new \Exception('DOCX fayl topilmadi: ' . $docxPath);
        }

        // 0. Linked rasmlarni embed qilingan DOCX ga aylantirish
        $embeddedDocxPath = $this->embedLinkedImages($docxPath);
        $workingDocxPath = $embeddedDocxPath ?: $docxPath;

        // 0.1 XML dan rasmlar va jadvallar metadatasini (o'lchamlari, float va chegaralari) olish
        $imagesMetadata = [];
        $tablesMetadata = [];
        try {
            $zip = new \ZipArchive();
            if ($zip->open($workingDocxPath) === true) {
                $originalXml = $zip->getFromName('word/document.xml');
                $zip->close();
                if ($originalXml !== false) {
                    $imagesMetadata = $this->parseImagesMetadata($originalXml);
                    $tablesMetadata = $this->parseTablesMetadata($originalXml);
                }
            }
        } catch (\Exception $e) {
            Log::warning('DOCX rasmlar va jadvallar metadatasini olishda xatolik: ' . $e->getMessage());
        }

        // 1. OMML formulalarni DOCX ichidan olish va placeholder qo'yish
        $formulaData = $this->extractFormulasAndCreateModifiedDocx($workingDocxPath);
        $formulas = $formulaData['formulas'];
        $modifiedDocxPath = $formulaData['modified_docx_path'];

        // 2. Mammoth orqali DOCX → HTML (modifikatsiya qilingan DOCX ishlatiladi)
        $docxForMammoth = $modifiedDocxPath ?: $workingDocxPath;
        $html = $this->convertDocxToHtml($docxForMammoth);

        // 3. Placeholder larni formula HTML bilan almashtirish
        if (!empty($formulas)) {
            $html = $this->replacePlaceholdersWithFormulas($html, $formulas);
        }

        // 4. Kimyoviy formulalarni to'g'rilash (H2O → H₂O)
        $html = $this->processChemicalFormulas($html);

        // 5. HTML ni tozalash va optimallashtirish
        $html = $this->cleanHtml($html);

        // 6. Rang markerlarini CSS span larga aylantirish (w:shd, w:highlight)
        $html = $this->convertColorMarkersToHtml($html);

        // 6. HTML ichidagi broken linked rasmlarni base64 bilan almashtirish
        $html = $this->fixLinkedImagesInHtml($html);

        // 7. Rasmlarga Word'dagi o'lcham va float uslublarini qo'llash
        if (!empty($imagesMetadata)) {
            $html = $this->applyImagesMetadataToHtml($html, $imagesMetadata);
        }

        // 8. Jadvallarga chegarasiz uslublarni qo'llash
        if (!empty($tablesMetadata)) {
            $html = $this->applyTablesMetadataToHtml($html, $tablesMetadata);
        }

        // Vaqtincha fayllarni o'chirish
        if ($modifiedDocxPath && file_exists($modifiedDocxPath)) {
            @unlink($modifiedDocxPath);
        }
        if ($embeddedDocxPath && file_exists($embeddedDocxPath)) {
            @unlink($embeddedDocxPath);
        }

        return [
            'html' => $html,
            'has_formulas' => !empty($formulas),
            'formula_count' => count($formulas),
        ];
    }

    /**
     * DOCX ichidagi linked (tashqi fayl yo'li bilan bog'langan) rasmlarni
     * embed qilib yangi DOCX fayl yaratish
     *
     * Word faylida rasm "Insert → Picture → Link" bilan qo'shilganda,
     * rasm DOCX ichiga saqlanmaydi — faqat fayl yo'li saqlanadi.
     * Bu metod o'sha rasmlarni o'qib, DOCX ichiga joylashtiradi.
     */
    private function embedLinkedImages(string $docxPath): ?string
    {
        try {
            $zip = new \ZipArchive();
            if ($zip->open($docxPath) !== true) {
                return null;
            }

            // word/_rels/document.xml.rels — barcha bog'lanmalarni ko'rish
            $relsXml = $zip->getFromName('word/_rels/document.xml.rels');
            $documentXml = $zip->getFromName('word/document.xml');
            $zip->close();

            if ($relsXml === false || $documentXml === false) {
                return null;
            }

            // Linked rasmlarni topish (Target o'zi fayl yo'li bo'lgan munosabatlar)
            $dom = new \DOMDocument();
            @$dom->loadXML($relsXml);
            $relationships = $dom->getElementsByTagName('Relationship');

            $linkedImages = [];
            foreach ($relationships as $rel) {
                $type = $rel->getAttribute('Type');
                $target = $rel->getAttribute('Target');
                $targetMode = $rel->getAttribute('TargetMode');
                $relId = $rel->getAttribute('Id');

                // Linked rasm: TargetMode="External" va image type
                if ($targetMode === 'External' && strpos($type, 'image') !== false) {
                    // File:/// protokolini olib tashlash
                    $filePath = preg_replace('/^file:\/\/\//i', '', $target);
                    $filePath = urldecode($filePath);
                    // Windows yo'li normalizatsiya
                    $filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);

                    if (file_exists($filePath)) {
                        $linkedImages[$relId] = [
                            'path' => $filePath,
                            'target' => $target,
                        ];
                    } else {
                        Log::warning('Linked rasm topilmadi: ' . $filePath);
                    }
                }
            }

            if (empty($linkedImages)) {
                // Linked rasm yo'q — hech narsa o'zgartirmasak ham bo'ladi
                return null;
            }

            Log::info('Linked rasmlar topildi', ['count' => count($linkedImages)]);

            // Yangi DOCX yaratish — asl DOCX dan nusxa
            $tempDir = storage_path('app/private/temp_word');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            $newDocxPath = $tempDir . '/' . uniqid('embedded_') . '.docx';
            copy($docxPath, $newDocxPath);

            $zipNew = new \ZipArchive();
            if ($zipNew->open($newDocxPath) !== true) {
                return null;
            }

            // Yangilangan rels XML yaratish
            $newRelsXml = $relsXml;

            foreach ($linkedImages as $relId => $imgInfo) {
                $imgPath = $imgInfo['path'];
                $ext = strtolower(pathinfo($imgPath, PATHINFO_EXTENSION));
                if (empty($ext)) $ext = 'png';

                // Rasm mazmunini o'qish
                $imgData = file_get_contents($imgPath);
                if ($imgData === false) continue;

                // DOCX ichiga yangi ot bilan saqlash
                $mediaName = 'word/media/linked_' . $relId . '.' . $ext;
                $zipNew->addFromString($mediaName, $imgData);

                // Rels XML da External → Internal o'zgartirish
                // TargetMode="External" ni olib tashlash va Target ni media ichidagi yo'lga o'zgartirish
                $internalTarget = 'media/linked_' . $relId . '.' . $ext;

                // Regex bilan ushbu relId uchun Relationship elementini yangilash
                $newRelsXml = preg_replace(
                    '/(<Relationship[^>]*Id="' . preg_quote($relId, '/') . '"[^>]*)TargetMode="External"([^>]*)Target="[^"]*"([^>]*\/?>)/i',
                    '$1$2Target="' . $internalTarget . '"$3',
                    $newRelsXml
                );
                // Agar birinchi pattern ishlamasa, ikkinchi tartib bilan ham sinab ko'ramiz
                $newRelsXml = preg_replace(
                    '/(<Relationship[^>]*Id="' . preg_quote($relId, '/') . '"[^>]*)Target="[^"]*"([^>]*)TargetMode="External"([^>]*\/?>)/i',
                    '$1Target="' . $internalTarget . '"$2$3',
                    $newRelsXml
                );
            }

            $zipNew->addFromString('word/_rels/document.xml.rels', $newRelsXml);
            $zipNew->close();

            Log::info('Linked rasmlar embed qilindi', ['new_docx' => $newDocxPath, 'count' => count($linkedImages)]);
            return $newDocxPath;

        } catch (\Exception $e) {
            Log::warning('embedLinkedImages xatosi: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * HTML ichidagi broken linked rasmlarni (fayl yo'li ko'rsatilgan src) base64 ga o'girish
     * Bu fallback: agar embedLinkedImages ishlamasa, bu metod rasmlarni to'g'rilaydi.
     */
    private function fixLinkedImagesInHtml(string $html): string
    {
        // <img src="C:\Users\...\ file.jpg"> yoki <img src="file:///C:/..."> ni topish
        // is — case-insensitive + dotall (newline ni ham qamrab olsin)
        return preg_replace_callback(
            '/<img\s([^>]*?)src="([^"]+)"([^>]*?)\/?>|<img\s([^>]*?)src=\'([^\']+)\'([^>]*?)\/?>|<img([^>]*?)src="([^"]+)"([^>]*?)>/is',
            function ($matches) {
                // Qaysi guruhlar to'ldirilganini aniqlash
                if (!empty($matches[2])) {
                    $before = $matches[1] ?? '';
                    $src    = $matches[2];
                    $after  = $matches[3] ?? '';
                } elseif (!empty($matches[5])) {
                    $before = $matches[4] ?? '';
                    $src    = $matches[5];
                    $after  = $matches[6] ?? '';
                } elseif (!empty($matches[8])) {
                    $before = $matches[7] ?? '';
                    $src    = $matches[8];
                    $after  = $matches[9] ?? '';
                } else {
                    return $matches[0];
                }

                // Agar src allaqachon base64 yoki http bo'lsa — o'zgartirmaslik
                if (strpos($src, 'data:') === 0 || strpos($src, 'http') === 0) {
                    return $matches[0];
                }

                // file:/// protokolini olib tashlash
                $filePath = preg_replace('/^file:\/\/\//i', '', $src);
                $filePath = urldecode($filePath);
                // Windows yo'li normalizatsiya
                $filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);

                if (!file_exists($filePath)) {
                    // Rasm topilmadi — placeholder
                    Log::warning('HTML da broken rasm: ' . $filePath);
                    return '<img ' . $before . 'src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==" style="border:1px dashed #ccc;min-width:60px;min-height:40px;" />';
                }

                // Rasmni o'qib base64 ga aylantirish
                $imgData = @file_get_contents($filePath);
                if ($imgData === false) {
                    return $matches[0];
                }

                $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                $mimeMap = [
                    'jpg'  => 'image/jpeg', 'jpeg' => 'image/jpeg',
                    'png'  => 'image/png',  'gif'  => 'image/gif',
                    'bmp'  => 'image/bmp',  'webp' => 'image/webp',
                    'svg'  => 'image/svg+xml',
                    'tiff' => 'image/tiff', 'tif'  => 'image/tiff',
                ];
                $mime   = $mimeMap[$ext] ?? 'image/jpeg';
                $base64 = base64_encode($imgData);

                Log::info('Linked rasm base64 ga aylantildi: ' . basename($filePath));
                return '<img ' . $before . 'src="data:' . $mime . ';base64,' . $base64 . '" ' . $after . '>';
            },
            $html
        );
    }

    /**
     * DOCX dan formulalarni chiqarib olish va placeholder li yangi DOCX yaratish
     * 
     * Mammoth <m:oMath> elementlarni tanimaydi — ularni o'tkazib yuboradi.
     * Shuning uchun biz DOCX XML ni o'zgartiramiz:
     * - <m:oMath> / <m:oMathPara> → {{FORMULA_0}}, {{FORMULA_1}}, ...
     * Mammoth bu placeholder matnni oddiy paragraf sifatida chiqaradi.
     * Keyin biz placeholder larni formula HTML bilan almashtiramiz.
     */
    public function extractFormulasAndCreateModifiedDocx(string $docxPath): array
    {
        $formulas = [];
        $modifiedDocxPath = null;

        try {
            $zip = new \ZipArchive();
            if ($zip->open($docxPath) !== true) {
                Log::warning('DOCX faylni ZIP sifatida ochib bo\'lmadi: ' . $docxPath);
                return ['formulas' => [], 'modified_docx_path' => null];
            }

            $documentXml = $zip->getFromName('word/document.xml');
            $zip->close();

            if ($documentXml === false) {
                return ['formulas' => [], 'modified_docx_path' => null];
            }

            // OMML formulalarni topish va placeholder bilan almashtirish
            $formulaIdx = 0;
            $modifiedXml = $documentXml;

            // MAMMOTH FIX: Convert floating/wrapped images (<wp:anchor>) to inline (<wp:inline>)
            $modifiedXml = str_replace(['<wp:anchor', '</wp:anchor>'], ['<wp:inline', '</wp:inline>'], $modifiedXml);

            // RANG FIX: w:shd va w:highlight elementlarini maxsus markerlar bilan belgilash
            $modifiedXml = $this->addColorMarkersToXml($modifiedXml);

            // 1. Avval <m:oMathPara> elementlarni topish (display formulalar)
            $modifiedXml = preg_replace_callback(
                '/<m:oMathPara[^>]*>.*?<\/m:oMathPara>/is',
                function ($matches) use (&$formulas, &$formulaIdx) {
                    $ommlBlock = $matches[0];
                    $formulaHtml = $this->convertOmmlToHtml($ommlBlock);
                    
                    $placeholder = '{{FORMULA_' . $formulaIdx . '}}';
                    $formulas[$formulaIdx] = [
                        'placeholder' => $placeholder,
                        'html' => $formulaHtml,
                        'type' => 'display',
                    ];
                    $formulaIdx++;

                    // Placeholder ni w:r/w:t ichiga o'rash — mammoth uni matn sifatida chiqaradi
                    return '<w:r><w:t>' . $placeholder . '</w:t></w:r>';
                },
                $modifiedXml
            );

            // 2. Keyin <m:oMath> elementlarni topish (inline formulalar)
            $modifiedXml = preg_replace_callback(
                '/<m:oMath[^>]*>.*?<\/m:oMath>/is',
                function ($matches) use (&$formulas, &$formulaIdx) {
                    $ommlBlock = $matches[0];
                    $formulaHtml = $this->convertOmmlToHtml($ommlBlock);
                    
                    $placeholder = '{{FORMULA_' . $formulaIdx . '}}';
                    $formulas[$formulaIdx] = [
                        'placeholder' => $placeholder,
                        'html' => $formulaHtml,
                        'type' => 'inline',
                    ];
                    $formulaIdx++;

                    return '<w:r><w:t>' . $placeholder . '</w:t></w:r>';
                },
                $modifiedXml
            );

            if ($modifiedXml !== $documentXml) {
                // Modifikatsiya qilingan DOCX yaratish
                $tempDir = storage_path('app/private/temp_word');
                if (!is_dir($tempDir)) {
                    mkdir($tempDir, 0755, true);
                }
                $modifiedDocxPath = $tempDir . '/' . uniqid('modified_') . '.docx';

                // Asl DOCX ni nusxalash
                copy($docxPath, $modifiedDocxPath);

                // Yangi XML ni yozish
                $zipMod = new \ZipArchive();
                if ($zipMod->open($modifiedDocxPath) === true) {
                    $zipMod->addFromString('word/document.xml', $modifiedXml);
                    $zipMod->close();
                    Log::info('DOCX modified (formulas or floating images)', [
                        'formulas_count' => count($formulas),
                        'modified_path' => $modifiedDocxPath,
                    ]);
                } else {
                    Log::warning('Modified DOCX ni ochib bo\'lmadi');
                    $modifiedDocxPath = null;
                }
            }
        } catch (\Exception $e) {
            Log::warning('OMML formulalarni olishda xatolik: ' . $e->getMessage());
        }

        return [
            'formulas' => $formulas,
            'modified_docx_path' => $modifiedDocxPath,
        ];
    }

    /**
     * OMML blokni HTML ga aylantirish
     * XSLT mavjud bo'lsa MathML, aks holda oddiy matn
     */
    private function convertOmmlToHtml(string $omml): string
    {
        // Avval XSLT orqali MathML ga aylantirish
        $xslPath = resource_path('xslt/OMML2MathML.xsl');

        if (file_exists($xslPath) && extension_loaded('xsl')) {
            try {
                $xmlString = '<?xml version="1.0" encoding="UTF-8"?>'
                    . '<root xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math"'
                    . ' xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
                    . $omml
                    . '</root>';

                $xml = new \DOMDocument();
                $xml->loadXML($xmlString, LIBXML_NOERROR | LIBXML_NOWARNING);

                $xsl = new \DOMDocument();
                $xsl->load($xslPath);

                $processor = new \XSLTProcessor();
                $processor->importStyleSheet($xsl);

                $result = $processor->transformToXMl($xml);
                if ($result) {
                    if (preg_match('/<math[^>]*>.*<\/math>/is', $result, $m)) {
                        return '<span class="math-formula">' . $m[0] . '</span>';
                    }
                    return '<span class="math-formula">' . $result . '</span>';
                }
            } catch (\Exception $e) {
                Log::warning('XSLT konvertatsiyada xatolik: ' . $e->getMessage());
            }
        }

        // Fallback: OMML dan oddiy matnni chiqarib olish
        return $this->extractMathTextFromOmml($omml);
    }

    /**
     * OMML ichidan matnni oddiy ko'rinishda olish (fallback)
     * Formulani italik va qavs ichida ko'rsatadi
     */
    private function extractMathTextFromOmml(string $omml): string
    {
        $text = '';

        // m:t taglar ichidagi matnni olish
        preg_match_all('/<m:t[^>]*>([^<]*)<\/m:t>/i', $omml, $matches);
        if (!empty($matches[1])) {
            $text = implode('', $matches[1]);
        }

        if (empty($text)) {
            $text = strip_tags($omml);
            $text = preg_replace('/\s+/', ' ', trim($text));
        }

        if (empty($text)) {
            return '<em>[formula]</em>';
        }

        // Raqamlarni subscript/superscript bilan formatlash
        // Daraja: ^2, ^3, etc.
        $text = preg_replace('/\^(\d+)/', '<sup>$1</sup>', $text);
        // Indeks: _2, _3, etc.
        $text = preg_replace('/_(\d+)/', '<sub>$1</sub>', $text);

        return '<span class="math-formula" style="font-style: italic; font-family: \'Times New Roman\', serif;">' . htmlspecialchars($text, ENT_NOQUOTES) . '</span>';
    }

    /**
     * HTML ichidagi placeholder larni formula HTML bilan almashtirish
     */
    private function replacePlaceholdersWithFormulas(string $html, array $formulas): string
    {
        foreach ($formulas as $idx => $formula) {
            $placeholder = '{{FORMULA_' . $idx . '}}';
            $formulaHtml = $formula['html'];
            
            // Display formulalar uchun blok element
            if ($formula['type'] === 'display') {
                $formulaHtml = '<div class="formula-display" style="text-align: center; margin: 8pt 0; font-style: italic;">' . $formulaHtml . '</div>';
            }

            $html = str_replace($placeholder, $formulaHtml, $html);
            // HTML encoded versiya ham (mammoth ba'zan encode qiladi)
            $html = str_replace(htmlspecialchars($placeholder), $formulaHtml, $html);
        }

        return $html;
    }

    /**
     * Mammoth.js orqali DOCX → HTML
     */
    public function convertDocxToHtml(string $docxPath): string
    {
        $scriptPath = $this->scriptsPath . DIRECTORY_SEPARATOR . 'convert-docx.cjs';

        if (!file_exists($scriptPath)) {
            throw new \Exception('convert-docx.js skripti topilmadi');
        }

        $command = sprintf(
            '%s %s %s 2>&1',
            escapeshellarg($this->nodePath),
            escapeshellarg($scriptPath),
            escapeshellarg($docxPath)
        );

        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        $result = implode("\n", $output);

        if ($returnCode !== 0) {
            Log::error('Mammoth konvertatsiyada xatolik', ['output' => $result]);
            throw new \Exception('DOCX → HTML konvertatsiya xatosi: ' . $result);
        }

        $json = json_decode($result, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Mammoth javobini parse qilib bo\'lmadi: ' . $result);
        }

        if (isset($json['error'])) {
            throw new \Exception('Mammoth xatosi: ' . $json['error']);
        }

        // Ogohlantirishlarni log qilish
        if (!empty($json['messages'])) {
            foreach ($json['messages'] as $msg) {
                Log::info('Mammoth: ' . ($msg['message'] ?? ''), ['type' => $msg['type'] ?? 'info']);
            }
        }

        return $json['html'] ?? '';
    }

    /**
     * Kimyoviy formulalarni to'g'ri formatlash
     * H2O → H₂O, CO2 → CO₂, C6H12O6 → C₆H₁₂O₆
     */
    public function processChemicalFormulas(string $html): string
    {
        // HTML ni taglar va matnlarga ajratamiz, faqat matn qismiga regex qollaymiz
        // Bu <img src="data:image/..."> kabi attributlar ichidagi base64 kodlarini buzib qoyishini oldini oladi
        $parts = preg_split('/(<[^>]*>)/', $html, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        
        $result = '';
        foreach ($parts as $part) {
            if (str_starts_with($part, '<')) {
                $result .= $part;
            } else {
                // Faqat matn qismida H2O, CO2, P72 kabi kimyoviy element+raqam qolipini qidiramiz
                $result .= preg_replace_callback(
                    '/\b([A-Z][a-z]?)(\d+)(?=[A-Z\s,\.\)\(;:<\/]|$)/u',
                    function ($matches) {
                        return $matches[1] . '<sub>' . $matches[2] . '</sub>';
                    },
                    $part
                );
            }
        }

        return $result;
    }

    /**
     * HTML ni tozalash va optimallashtirish
     */
    private function cleanHtml(string $html): string
    {
        // Bo'sh paragraflarni olib tashlash
        $html = preg_replace('/<p>\s*<\/p>/', '', $html);

        // Ortiqcha bo'shliqlarni tozalash
        $html = preg_replace('/\s{3,}/', '  ', $html);

        // Word-specific class nomlarini olib tashlash
        $html = preg_replace('/\s+class="MsoNormal"/', '', $html);
        $html = preg_replace('/\s+class="Mso[^"]*"/', '', $html);

        // Style attributlarni tozalash (mammoth qoldirishi mumkin)
        $html = preg_replace('/\s+style="[^"]*mso-[^"]*"/', '', $html);

        return trim($html);
    }

    /**
     * DOCX XML da w:shd (background fill) va w:highlight elementlarini
     * maxsus [[BGSTART:RRGGBB]] ... [[BGEND]] markerlari bilan belgilash.
     * Paragraph va Table Cell darajasidagi fons ham o'tkaziladi.
     */
    private function addColorMarkersToXml(string $xml): string
    {
        // Standard Word highlight nomlarini hex rangga moslashtirish
        $highlightMap = [
            'yellow'      => 'FFFF00', 'cyan'        => '00FFFF',
            'magenta'     => 'FF00FF', 'blue'        => '0563C1',
            'red'         => 'FF0000', 'green'       => '00FF00',
            'darkBlue'    => '00008B', 'darkCyan'    => '008B8B',
            'darkGreen'   => '006400', 'darkMagenta' => '8B008B',
            'darkRed'     => '8B0000', 'darkYellow'  => '808000',
            'darkGray'    => '808080', 'lightGray'   => 'C0C0C0',
        ];

        // 1) Paragraph shading (<w:p> darajasidagi orqa fon rangi)
        $xml = preg_replace_callback(
            '/<w:p(?:\s[^>]*)?>.*?<\/w:p>/is',
            function ($matches) {
                $para = $matches[0];
                if (preg_match('/<w:pPr[^>]*>.*?<w:shd[^>]+w:fill="([0-9A-Fa-f]{6})"[^>]*\/?>.*?<\/w:pPr>/is', $para, $m)) {
                    $bgColor = strtoupper($m[1]);
                    if ($bgColor !== 'FFFFFF' && $bgColor !== 'AUTO' && $bgColor !== '000000') {
                        $tCount = preg_match_all('/<w:t(\s[^>]*)?>([^<]*)<\/w:t>/i', $para, $tMatches);
                        if ($tCount > 0) {
                            $firstT = $tMatches[0][0];
                            $lastT = $tMatches[0][$tCount - 1];

                            if ($tCount === 1) {
                                $newT = preg_replace('/(<w:t[^>]*>)(.*?)(<\/w:t>)/i', '$1[[PGBGSTART:' . $bgColor . ']]$2[[PGBGEND]]$3', $firstT);
                                $para = str_replace($firstT, $newT, $para);
                            } else {
                                $newFirstT = preg_replace('/(<w:t[^>]*>)(.*?)(<\/w:t>)/i', '$1[[PGBGSTART:' . $bgColor . ']]$2$3', $firstT);
                                $newLastT = preg_replace('/(<w:t[^>]*>)(.*?)(<\/w:t>)/i', '$1$2[[PGBGEND]]$3', $lastT);

                                $posFirst = strpos($para, $firstT);
                                if ($posFirst !== false) {
                                    $para = substr_replace($para, $newFirstT, $posFirst, strlen($firstT));
                                }
                                $posLast = strrpos($para, $lastT);
                                if ($posLast !== false) {
                                    $para = substr_replace($para, $newLastT, $posLast, strlen($lastT));
                                }
                            }
                        }
                    }
                }
                return $para;
            },
            $xml
        );

        // 2) Table cell shading (<w:tc> jadval katagi darajasidagi orqa fon rangi)
        $xml = preg_replace_callback(
            '/<w:tc(?:\s[^>]*)?>.*?<\/w:tc>/is',
            function ($matches) {
                $cell = $matches[0];
                if (preg_match('/<w:tcPr[^>]*>.*?<w:shd[^>]+w:fill="([0-9A-Fa-f]{6})"[^>]*\/?>.*?<\/w:tcPr>/is', $cell, $m)) {
                    $bgColor = strtoupper($m[1]);
                    if ($bgColor !== 'FFFFFF' && $bgColor !== 'AUTO' && $bgColor !== '000000') {
                        $tCount = preg_match_all('/<w:t(\s[^>]*)?>([^<]*)<\/w:t>/i', $cell, $tMatches);
                        if ($tCount > 0) {
                            $firstT = $tMatches[0][0];
                            $lastT = $tMatches[0][$tCount - 1];

                            if ($tCount === 1) {
                                $newT = preg_replace('/(<w:t[^>]*>)(.*?)(<\/w:t>)/i', '$1[[TCBGSTART:' . $bgColor . ']]$2[[TCBGEND]]$3', $firstT);
                                $cell = str_replace($firstT, $newT, $cell);
                            } else {
                                $newFirstT = preg_replace('/(<w:t[^>]*>)(.*?)(<\/w:t>)/i', '$1[[TCBGSTART:' . $bgColor . ']]$2$3', $firstT);
                                $newLastT = preg_replace('/(<w:t[^>]*>)(.*?)(<\/w:t>)/i', '$1$2[[TCBGEND]]$3', $lastT);

                                $posFirst = strpos($cell, $firstT);
                                if ($posFirst !== false) {
                                    $cell = substr_replace($cell, $newFirstT, $posFirst, strlen($firstT));
                                }
                                $posLast = strrpos($cell, $lastT);
                                if ($posLast !== false) {
                                    $cell = substr_replace($cell, $newLastT, $posLast, strlen($lastT));
                                }
                            }
                        }
                    }
                }
                return $cell;
            },
            $xml
        );

        // 3) Run-level shading (<w:r> matn darajasidagi orqa fon va highlight)
        $xml = preg_replace_callback(
            '/<w:r(?:\s[^>]*)?>.*?<\/w:r>/is',
            function ($matches) use ($highlightMap) {
                $run = $matches[0];
                $bgColor = null;

                if (preg_match('/<w:shd[^>]+w:fill="([0-9A-Fa-f]{6})"[^>]*\/?>/i', $run, $m)) {
                    $col = strtoupper($m[1]);
                    if ($col !== 'FFFFFF' && $col !== 'AUTO' && $col !== '000000') {
                        $bgColor = $col;
                    }
                }

                if (!$bgColor && preg_match('/<w:highlight[^>]+w:val="([^"]+)"[^>]*\/?>/i', $run, $m)) {
                    $hName = strtolower(trim($m[1]));
                    if ($hName !== 'none' && isset($highlightMap[$hName])) {
                        $bgColor = $highlightMap[$hName];
                    }
                }

                if (!$bgColor) return $run;

                return preg_replace_callback(
                    '/<w:t(\s[^>]*)?>([^<]*)<\/w:t>/i',
                    function ($tm) use ($bgColor) {
                        $attr = $tm[1] ?? '';
                        $text = $tm[2];
                        if (trim($text) === '') return $tm[0];
                        return '<w:t' . $attr . '>[[BGSTART:' . $bgColor . ']]' . $text . '[[BGEND]]</w:t>';
                    },
                    $run
                );
            },
            $xml
        );

        return $xml;
    }

    /**
     * HTML da markerlarni CSS background-color ga aylantirish.
     */
    private function convertColorMarkersToHtml(string $html): string
    {
        // 1) Paragraph shading (robust callback matching to prevent style overrides and handle attributes)
        $html = preg_replace_callback(
            '/<p([^>]*)>\s*\[\[PGBGSTART:([0-9A-Fa-f]{6})\]\](.*?)\[\[PGBGEND\]\]\s*<\/p>/is',
            function ($m) {
                $attrs = $m[1];
                $color = $m[2];
                $content = $m[3];
                $styleStr = 'background-color: #' . $color . '; padding: 6px 10px; border-radius: 4px; text-indent: 0;';
                if (preg_match('/style="([^"]*)"/i', $attrs, $sm)) {
                    $style = rtrim($sm[1], ';') . '; ' . $styleStr;
                    $attrs = preg_replace('/style="[^"]*"/i', 'style="' . $style . '"', $attrs);
                } else {
                    $attrs .= ' style="' . $styleStr . '"';
                }
                return '<p' . $attrs . '>' . $content . '</p>';
            },
            $html
        );

        // 2) Table cell shading (td elementga style background-color berish)
        $html = preg_replace_callback(
            '/<td([^>]*)>(?:\s*<p[^>]*>)?\s*\[\[TCBGSTART:([0-9A-Fa-f]{6})\]\](.*?)\[\[TCBGEND\]\]\s*(?:<\/p>)?\s*<\/td>/is',
            function ($m) {
                $attrs = $m[1];
                $color = $m[2];
                $content = $m[3];
                if (preg_match('/style="([^"]*)"/i', $attrs, $sm)) {
                    $style = rtrim($sm[1], ';') . '; background-color: #' . $color . ';';
                    $attrs = preg_replace('/style="[^"]*"/i', 'style="' . $style . '"', $attrs);
                } else {
                    $attrs .= ' style="background-color: #' . $color . ';"';
                }
                return '<td' . $attrs . '>' . $content . '</td>';
            },
            $html
        );

        // 3) Character-level highlight & shading
        $html = preg_replace(
            '/\[\[BGSTART:([0-9A-Fa-f]{6})\]\](.*?)\[\[BGEND\]\]/s',
            '<span style="background-color:#$1">$2</span>',
            $html
        );

        return $html;
    }

    /**
     * XML ichidan barcha rasmlar o'lchamlarini va joylashuvini olish
     */
    private function parseImagesMetadata(string $documentXml): array
    {
        $imagesInfo = [];
        if (preg_match_all('/<w:drawing[^>]*>(.*?)<\/w:drawing>/is', $documentXml, $matches)) {
            foreach ($matches[1] as $drawingContent) {
                $info = [
                    'width' => null,
                    'height' => null,
                    'float' => null,
                    'align' => null,
                    'is_anchor' => false,
                ];

                // 1. O'lchamlarni olish (cx va cy)
                $cx = null;
                $cy = null;
                if (preg_match('/cx="(\d+)"/i', $drawingContent, $cxM)) {
                    $cx = (int)$cxM[1];
                }
                if (preg_match('/cy="(\d+)"/i', $drawingContent, $cyM)) {
                    $cy = (int)$cyM[1];
                }

                if ($cx && $cy) {
                    // EMU to pixels: cx / 9525
                    $info['width'] = round($cx / 9525);
                    $info['height'] = round($cy / 9525);
                }

                // 2. wp:anchor (floating) ekanligini aniqlash
                if (preg_match('/<wp:anchor/i', $drawingContent)) {
                    $info['is_anchor'] = true;
                    // Sukut bo'yicha float left
                    $info['float'] = 'left';
                }

                // 3. Alignment aniqlash (positionH ichidan)
                if (preg_match('/<wp:positionH[^>]*>.*?<wp:align>([^<]+)<\/wp:align>/is', $drawingContent, $alignM)) {
                    $align = strtolower(trim($alignM[1]));
                    if ($align === 'left' || $align === 'right') {
                        $info['float'] = $align;
                    } elseif ($align === 'center') {
                        $info['align'] = 'center';
                        $info['float'] = null;
                    }
                }

                $imagesInfo[] = $info;
            }
        }
        return $imagesInfo;
    }

    /**
     * HTML ichidagi <img> teglariga Word'dagi o'lcham va float uslublarini qo'shish
     */
    private function applyImagesMetadataToHtml(string $html, array $imagesMetadata): string
    {
        if (empty($imagesMetadata)) {
            return $html;
        }

        $imgIndex = 0;
        // is — case-insensitive + dotall
        return preg_replace_callback(
            '/<img([^>]*?)src="([^"]+)"([^>]*?)\/?>|<img([^>]*?)src=\'([^\']+)\'([^>]*?)\/?>/is',
            function ($matches) use ($imagesMetadata, &$imgIndex) {
                // To'liq mos tushgan img tegi
                $fullTag = $matches[0];
                
                // Agar bu indeksda metadata bo'lsa
                if (isset($imagesMetadata[$imgIndex])) {
                    $meta = $imagesMetadata[$imgIndex];
                    $imgIndex++;

                    $width = $meta['width'];
                    $height = $meta['height'];
                    $float = $meta['float'];
                    $align = $meta['align'];

                    // Agar o'lchamlar juda katta bo'lsa (A4 kengligidan katta), ularni cheklaymiz
                    // A4 matn maydoni kengligi taxminan 680px
                    if ($width && $width > 680) {
                        $ratio = 680 / $width;
                        $width = 680;
                        if ($height) {
                            $height = round($height * $ratio);
                        }
                    }

                    // CSS stillarini qurish
                    $styles = [];
                    if ($width) {
                        $styles[] = "width: {$width}px";
                    }
                    if ($height) {
                        $styles[] = "height: {$height}px";
                    }
                    if ($float) {
                        $styles[] = "float: {$float}";
                        // Floating bo'lganda yonidan chiroyli masofa qoldirish
                        if ($float === 'left') {
                            $styles[] = "margin: 4px 12px 4px 0";
                        } else {
                            $styles[] = "margin: 4px 0 4px 12px";
                        }
                        $styles[] = "display: inline";
                    } elseif ($align === 'center') {
                        $styles[] = "display: block";
                        $styles[] = "margin: 6px auto";
                    } else {
                        $styles[] = "display: inline-block";
                        $styles[] = "margin: 4px";
                    }

                    $styleStr = implode('; ', $styles);

                    // inline style atributini qo'shish yoki mavjudini yangilash
                    if (preg_match('/style="([^"]*)"/i', $fullTag, $styleMatch)) {
                        $existingStyle = rtrim($styleMatch[1], ';');
                        $newStyle = $existingStyle ? $existingStyle . '; ' . $styleStr : $styleStr;
                        $fullTag = preg_replace('/style="[^"]*"/i', 'style="' . $newStyle . '"', $fullTag);
                    } else {
                        // src dan keyin style qo'shamiz
                        $fullTag = preg_replace('/<img/i', '<img style="' . $styleStr . '"', $fullTag);
                    }
                } else {
                    $imgIndex++;
                }

                return $fullTag;
            },
            $html
        );
    }

    /**
     * XML ichidan barcha jadvallar chegaralarini (borders) aniqlash
     */
    private function parseTablesMetadata(string $documentXml): array
    {
        $tablesInfo = [];
        if (preg_match_all('/<w:tbl[^>]*>(.*?)<\/w:tbl>/is', $documentXml, $matches)) {
            foreach ($matches[1] as $tblContent) {
                $hasBorders = true;

                if (preg_match('/<w:tblBorders[^>]*>(.*?)<\/w:tblBorders>/is', $tblContent, $borderMatch)) {
                    $bordersXml = $borderMatch[1];
                    preg_match_all('/w:val="([^"]+)"/i', $bordersXml, $valMatches);
                    if (!empty($valMatches[1])) {
                        $allNone = true;
                        foreach ($valMatches[1] as $val) {
                            $valLower = strtolower($val);
                            if ($valLower !== 'none' && $valLower !== 'nil') {
                                $allNone = false;
                                break;
                            }
                        }
                        if ($allNone) {
                            $hasBorders = false;
                        }
                    }
                } else {
                    $hasBorders = false;
                }

                $tablesInfo[] = [
                    'has_borders' => $hasBorders
                ];
            }
        }
        return $tablesInfo;
    }

    /**
     * HTML ichidagi <table> teglariga chegarali uslublarni qo'llash
     * Faqat Word'da aniq chegara belgilangan jadvallarga 'bordered-table' klassi beriladi
     */
    private function applyTablesMetadataToHtml(string $html, array $tablesMetadata): string
    {
        if (empty($tablesMetadata)) {
            return $html;
        }

        $tblIndex = 0;
        return preg_replace_callback(
            '/<table([^>]*?)>/is',
            function ($matches) use ($tablesMetadata, &$tblIndex) {
                $fullTag = $matches[0];

                if (isset($tablesMetadata[$tblIndex])) {
                    $meta = $tablesMetadata[$tblIndex];
                    $tblIndex++;

                    if ($meta['has_borders']) {
                        if (preg_match('/class="([^"]*)"/i', $fullTag, $classMatch)) {
                            $newClass = trim($classMatch[1] . ' bordered-table');
                            $fullTag = preg_replace('/class="[^"]*"/i', 'class="' . $newClass . '"', $fullTag);
                        } else {
                            $fullTag = preg_replace('/<table/i', '<table class="bordered-table"', $fullTag);
                        }
                    }
                } else {
                    $tblIndex++;
                }

                return $fullTag;
            },
            $html
        );
    }

    /**
     * Node.js yo'lini topish
     */
    private function findNodePath(): string
    {
        $nodePath = env('NODE_PATH', '');
        if (!empty($nodePath) && file_exists($nodePath)) {
            return $nodePath;
        }

        $possiblePaths = [
            'C:\\Program Files\\nodejs\\node.exe',
            'C:\\Program Files (x86)\\nodejs\\node.exe',
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        $output = [];
        exec('where node 2>&1', $output);
        if (!empty($output[0]) && file_exists(trim($output[0]))) {
            return trim($output[0]);
        }

        return 'node';
    }

    /**
     * KaTeX CSS yo'lini olish (PDF uchun inline qilish kerak)
     */
    public function getKatexCssPath(): string
    {
        $katexCss = base_path('node_modules/katex/dist/katex.min.css');
        if (file_exists($katexCss)) {
            return $katexCss;
        }
        return '';
    }

    /**
     * KaTeX CSS kontentini olish (inline uchun)
     */
    public function getKatexCssContent(): string
    {
        $cssPath = $this->getKatexCssPath();
        if (!empty($cssPath) && file_exists($cssPath)) {
            return file_get_contents($cssPath);
        }
        return '';
    }
}
