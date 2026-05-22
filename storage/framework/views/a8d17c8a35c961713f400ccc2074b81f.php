<?php $__env->startSection('title', 'Sitemap'); ?>
<?php $__env->startSection('description', 'Complete sitemap of all countries, conferences, and articles in the International Scientific Online Conference platform.'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container py-5">
        <h1 class="section-title text-center mb-5">
            <i class="bi bi-map me-2"></i>Sitemap
        </h1>

        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="card">
                    <div class="card-body p-4">
                        
                        <div class="row text-center mb-5">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="p-3 rounded" style="background: var(--light-blue);">
                                    <h2 class="display-5 fw-bold" style="color: var(--primary-blue);">
                                        <?php echo e($countries->count()); ?>

                                    </h2>
                                    <p class="mb-0 text-muted">Countries</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="p-3 rounded" style="background: #dcfce7;">
                                    <h2 class="display-5 fw-bold text-success">
                                        <?php echo e($countries->sum(fn($c) => $c->conferences->count())); ?>

                                    </h2>
                                    <p class="mb-0 text-muted">Conferences</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded" style="background: #fef3c7;">
                                    <h2 class="display-5 fw-bold" style="color: #d97706;">
                                        <?php echo e($countries->sum(fn($c) => $c->conferences->sum(fn($conf) => $conf->articles->count()))); ?>

                                    </h2>
                                    <p class="mb-0 text-muted">Articles</p>
                                </div>
                            </div>
                        </div>

                        
                        <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mb-4">
                                <h2 class="h4 mb-3 d-flex align-items-center">
                                    <?php if($country->flag_url): ?>
                                        <img src="<?php echo e(Storage::url($country->flag_url)); ?>" alt="<?php echo e($country->name); ?>"
                                            style="width: 32px; height: 22px; object-fit: cover; border-radius: 3px; margin-right: 10px;">
                                    <?php endif; ?>
                                    <a href="<?php echo e(route('country.show', $country)); ?>" class="text-decoration-none"
                                        style="color: var(--primary-blue);">
                                        <?php echo e($country->name_en ?? $country->name); ?>

                                    </a>
                                </h2>

                                <?php $__currentLoopData = $country->conferences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conference): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="ms-4 mb-3">
                                        <h3 class="h5 mb-2">
                                            <i class="bi bi-journal-bookmark me-2 text-muted"></i>
                                            <a href="<?php echo e(route('conference.show', $conference)); ?>" class="text-decoration-none">
                                                <?php echo e($conference->title); ?>

                                            </a>
                                            <small class="text-muted">(<?php echo e($conference->conference_date->format('M Y')); ?>)</small>
                                        </h3>

                                        <?php if($conference->articles->count() > 0): ?>
                                            <ul class="list-unstyled ms-4">
                                                <?php $__currentLoopData = $conference->articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li class="mb-2">
                                                        <i class="bi bi-file-earmark-text me-2 text-muted"></i>
                                                        <a href="<?php echo e($article->url); ?>" class="text-decoration-none">
                                                            <?php echo e(Str::limit($article->title, 80)); ?>

                                                        </a>
                                                        <small class="text-muted">
                                                            - <?php echo e($article->author_display_name); ?>

                                                            <?php if($article->page_range): ?>
                                                                (pp. <?php echo e($article->page_range); ?>)
                                                            <?php endif; ?>
                                                        </small>
                                                    </li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                        <?php else: ?>
                                            <p class="text-muted ms-4"><small>No articles published yet.</small></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>

                            <?php if(!$loop->last): ?>
                                <hr class="my-4">
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                
                <div class="card mt-4">
                    <div class="card-body">
                        <h3 class="h5 mb-3"><i class="bi bi-code-slash me-2"></i>Machine-Readable Sitemaps</h3>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <a href="<?php echo e(url('/sitemap.xml')); ?>" target="_blank" class="text-decoration-none">
                                    <i class="bi bi-file-code me-2"></i>sitemap.xml
                                </a>
                                <span class="text-muted">- Complete XML sitemap</span>
                            </li>
                            <li>
                                <a href="<?php echo e(url('/sitemap-articles.xml')); ?>" target="_blank" class="text-decoration-none">
                                    <i class="bi bi-file-code me-2"></i>sitemap-articles.xml
                                </a>
                                <span class="text-muted">- Articles only (for Google Scholar)</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/artiqle_new/resources/views/sitemap/html.blade.php ENDPATH**/ ?>