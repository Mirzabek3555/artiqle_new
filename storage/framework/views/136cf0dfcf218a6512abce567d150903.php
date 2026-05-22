<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Foydalanilgan adabiyotlar - <?php echo e($article->title); ?></title>
    <style>
        @page {
            margin: 0;
            size: A4 portrait;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Serif', 'Times New Roman', Georgia, serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #1a1a1a;
            background: #ffffff;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            position: relative;
            background: #ffffff;
            padding: 0;
            overflow: hidden;
        }

        /* Header Strip */
        .header-strip {
            background: linear-gradient(135deg,
                    <?php echo e($colors['primary'] ?? '#1a5276'); ?>

                    0%,
                    <?php echo e($colors['secondary'] ?? '#2980b9'); ?>

                    100%);
            height: 6mm;
            width: 100%;
        }

        /* Left Sidebar with vertical text */
        .left-sidebar {
            position: absolute;
            left: 0;
            top: 6mm;
            width: 8mm;
            height: calc(100% - 10mm);
            background: linear-gradient(180deg,
                    <?php echo e($colors['primary'] ?? '#1a5276'); ?>

                    0%,
                    <?php echo e($colors['secondary'] ?? '#2980b9'); ?>

                    70%,
                    transparent 100%);
        }

        .vertical-text {
            position: absolute;
            left: 1mm;
            top: 50%;
            transform: rotate(-90deg) translateX(-50%);
            transform-origin: left center;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 6pt;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 2px;
            text-transform: uppercase;
            white-space: nowrap;
            opacity: 0.9;
        }

        /* Content Section */
        .content-section {
            padding: 10mm 20mm 15mm 25mm;
        }

        /* Section Title */
        .section-title {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 14pt;
            font-weight: 700;
            color:
                <?php echo e($colors['primary'] ?? '#1a5276'); ?>

            ;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-align: center;
            margin-bottom: 6mm;
            padding-bottom: 3mm;
            border-bottom: 2px solid
                <?php echo e($colors['primary'] ?? '#1a5276'); ?>

            ;
        }

        /* Reference Item */
        .reference-item {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 10pt;
            color: #333333;
            line-height: 1.6;
            margin-bottom: 4mm;
            padding-left: 6mm;
            text-indent: -6mm;
            text-align: justify;
        }

        .reference-number {
            font-weight: 700;
            color:
                <?php echo e($colors['primary'] ?? '#1a5276'); ?>

            ;
        }
    </style>
</head>

<body>
    <div class="page">
        <!-- Header Strip -->
        <div class="header-strip"></div>

        <!-- Left Sidebar with Vertical Text -->
        <div class="left-sidebar">
            <div class="vertical-text">INTERNATIONALSCIENTIFICCONFERENCES</div>
        </div>

        <!-- Content Section -->
        <div class="content-section">
            <div class="section-title">FOYDALANILGAN ADABIYOTLAR:</div>

            <?php
                $references = array_filter(explode("\n", $article->references ?? ''));
                $refNumber = 1;
            ?>

            <?php $__currentLoopData = $references; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reference): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(trim($reference)): ?>
                    <div class="reference-item">
                        <?php
                            $trimmedRef = trim($reference);
                            // Agar raqam bilan boshlanmasa, raqam qo'shamiz
                            if (!preg_match('/^\d+[\.\)]/', $trimmedRef)) {
                                echo '<span class="reference-number">[' . $refNumber . ']</span> ' . $trimmedRef;
                            } else {
                                echo $trimmedRef;
                            }
                            $refNumber++;
                        ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>


    </div>
</body>

</html><?php /**PATH /var/www/artiqle_new/resources/views/pdf/article-references-page.blade.php ENDPATH**/ ?>