<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
    
    
    <url>
        <loc><?php echo e(url('/')); ?></loc>
        <lastmod><?php echo e(now()->toAtomString()); ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    
    
    <url>
        <loc><?php echo e(route('countries')); ?></loc>
        <lastmod><?php echo e(now()->toAtomString()); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    
    
<?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <url>
        <loc><?php echo e(route('country.show', $country)); ?></loc>
        <lastmod><?php echo e($country->updated_at->toAtomString()); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    
    
<?php $__currentLoopData = $conferences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conference): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <url>
        <loc><?php echo e(route('conference.show', $conference)); ?></loc>
        <lastmod><?php echo e($conference->updated_at->toAtomString()); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    
    
<?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <url>
        <loc><?php echo e($article->url); ?></loc>
        <lastmod><?php echo e($article->updated_at->toAtomString()); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.9</priority>
    </url>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    
    
    <url>
        <loc><?php echo e(url('/sitemap')); ?></loc>
        <lastmod><?php echo e(now()->toAtomString()); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.5</priority>
    </url>
</urlset>
<?php /**PATH /var/www/artiqle_new/resources/views/sitemap/xml.blade.php ENDPATH**/ ?>