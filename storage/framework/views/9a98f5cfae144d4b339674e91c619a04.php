<?php $__env->startSection('title', 'Mening maqolalarim'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold">
                <i class="bi bi-file-text text-primary me-2"></i>Mening maqolalarim
            </h1>
            <a href="<?php echo e(route('user.dashboard')); ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Dashboard
            </a>
        </div>

        <?php if($articles->count() > 0): ?>
            <div class="row g-4">
                <?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h5 class="mb-1"><?php echo e($article->title); ?></h5>
                                        <p class="text-muted mb-2">
                                            <i class="bi bi-calendar-event me-1"></i><?php echo e($article->conference->title); ?>

                                            <span class="mx-2">•</span>
                                            <i class="bi bi-geo-alt me-1"></i><?php echo e($article->conference->country->name); ?>

                                        </p>
                                        <div>
                                            <?php if($article->status === 'published'): ?>
                                                <span class="badge bg-success">Nashr etilgan</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Kutilmoqda</span>
                                            <?php endif; ?>
                                            <span class="badge bg-secondary ms-1"><?php echo e($article->page_range); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <a href="<?php echo e(route('user.article.download', $article)); ?>" class="btn btn-primary">
                                            <i class="bi bi-download me-1"></i>PDF
                                        </a>
                                        <?php if($article->certificate): ?>
                                            <a href="<?php echo e(route('user.certificate.download', $article)); ?>" class="btn btn-success">
                                                <i class="bi bi-award me-1"></i>Sertifikat
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
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
                <i class="bi bi-file-x text-muted" style="font-size: 5rem;"></i>
                <h4 class="mt-3 text-muted">Hozircha maqolalar yo'q</h4>
                <p class="text-muted">Sizning maqolalaringiz admin tomonidan joylanganda bu yerda ko'rinadi.</p>
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/artiqle_new/resources/views/user/articles.blade.php ENDPATH**/ ?>