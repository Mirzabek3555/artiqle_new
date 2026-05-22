<?php $__env->startSection('title', 'Mening sertifikatlarim'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold">
                <i class="bi bi-award text-primary me-2"></i>Mening sertifikatlarim
            </h1>
            <a href="<?php echo e(route('user.dashboard')); ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Dashboard
            </a>
        </div>

        <?php if($articles->count() > 0): ?>
            <div class="row g-4">
                <?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-award text-warning" style="font-size: 4rem;"></i>
                                <h5 class="mt-3"><?php echo e($article->certificate->certificate_number); ?></h5>
                                <p class="text-muted"><?php echo e($article->title); ?></p>
                                <p class="small text-muted">
                                    <i class="bi bi-geo-alt me-1"></i><?php echo e($article->conference->country->name); ?>

                                    <br>
                                    <i class="bi bi-calendar me-1"></i><?php echo e($article->certificate->issue_date->format('d.m.Y')); ?>

                                </p>
                                <a href="<?php echo e(route('user.certificate.download', $article)); ?>" class="btn btn-success">
                                    <i class="bi bi-download me-1"></i>Yuklab olish
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                <?php echo e($articles->links()); ?>

            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-award text-muted" style="font-size: 5rem;"></i>
                <h4 class="mt-3 text-muted">Hozircha sertifikatlar yo'q</h4>
                <p class="text-muted">Maqolangiz nashr etilganda sertifikat olasiz.</p>
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/artiqle_new/resources/views/user/certificates.blade.php ENDPATH**/ ?>