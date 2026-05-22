<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
    
    
<?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <url>
        <loc><?php echo e($article->url); ?></loc>
        <lastmod><?php echo e($article->updated_at->toAtomString()); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>1.0</priority>
    </url>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</urlset>
<?php /**PATH /var/www/artiqle_new/resources/views/sitemap/articles.blade.php ENDPATH**/ ?>