<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo e($conference->title); ?> - Table of Contents</title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 33mm;
            size: A4 portrait;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body { 
            font-family: 'Times New Roman', Times, serif; 
            background: #fff;
        }

        .date {
            text-align: center;
            color: red;
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 16px;
        }

        .toc-table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 14px; 
            page-break-inside: auto;
        }

        .toc-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .toc-header {
            background-color: #a394c6;
            color: #041E4F;
            text-align: center;
            font-weight: bold;
            border: 1px solid #7eaac8;
            font-size: 16px;
            padding: 5px;
            text-transform: uppercase;
        }

        .toc-cell {
            border: 1px solid #7eaac8;
            padding: 8px;
            text-align: center;
            color: #000;
        }

        .toc-page {
            border: 1px solid #7eaac8;
            padding: 8px;
            text-align: center;
            vertical-align: top;
            width: 65px;
            white-space: nowrap;
            font-weight: bold;
            font-size: 14px;
            color: #000;
        }
    </style>
</head>

<body>
    <!-- Date is drawn by the PDF service header, removed from here to prevent duplication -->
    
    <table class="toc-table">
        <thead>
            <tr>
                <th colspan="2" class="toc-header">
                    ARTICLES:
                </th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="toc-cell">
                    <div style="font-weight: bold;">
                        <?php echo e(trim($article->author_display_name ?? $article->author_name)); ?>

                        <?php if($article->co_authors): ?>
                            <?php
                                $coLines = array_filter(explode("\n", trim($article->co_authors)));
                                $prefixes = ['ilmiy rahbar:', 'ilmiy rahbar :', 'scientific advisor:', 'supervisor:'];
                            ?>
                            <?php $__currentLoopData = $coLines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $caLine): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $trimmedLine = trim($caLine);
                                    // Prefiksni olib tashlab faqat ismni qoldirish
                                    foreach ($prefixes as $prefix) {
                                        if (stripos($trimmedLine, $prefix) === 0) {
                                            $trimmedLine = trim(substr($trimmedLine, strlen($prefix)));
                                            break;
                                        }
                                    }
                                    $caName = trim(explode(',', $trimmedLine)[0]);
                                ?>
                                <?php if($caName): ?>
                                    <br><?php echo e($caName); ?>

                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </div>
                    <div style="text-transform: uppercase; margin-top: 5px;">
                        <?php echo e($article->title); ?>

                    </div>
                </td>
                <td class="toc-page">
                    <?php echo e($article->page_range ?? '—'); ?>

                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html><?php /**PATH /var/www/artiqle_new/resources/views/pdf/table-of-contents.blade.php ENDPATH**/ ?>