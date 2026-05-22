<?php $__env->startSection('page-title', $article->title); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-file-text me-2"></i>Maqola ma'lumotlari
                </div>
                <div class="card-body">
                    <h4 class="text-primary"><?php echo e($article->title); ?></h4>
                    <?php if($article->abstract): ?>
                        <p class="text-muted"><?php echo e($article->abstract); ?></p>
                    <?php endif; ?>
                    <hr>

                    <!-- Muallif ma'lumotlari -->
                    <div class="card border-warning mb-3">
                        <div class="card-header bg-warning bg-opacity-10">
                            <i class="bi bi-person-badge me-2"></i>Muallif ma'lumotlari
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong><i class="bi bi-person me-1"></i>Muallif ismi:</strong><br>
                                        <span class="fs-5"><?php echo e($article->author_display_name); ?></span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <?php if($article->author_affiliation): ?>
                                        <p class="mb-2">
                                            <strong><i class="bi bi-building me-1"></i>Tashkilot:</strong><br>
                                            <?php echo e($article->author_affiliation); ?>

                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if($article->author): ?>
                                <div class="alert alert-info mb-0 mt-2">
                                    <small><i class="bi bi-link me-1"></i>Tizim foydalanuvchisi:
                                        <strong><?php echo e($article->author->name); ?></strong> (<?php echo e($article->author->email); ?>)
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <p><strong><i class="bi bi-calendar-event me-1"></i>Konferensiya:</strong>
                                <?php echo e($article->conference->title); ?></p>
                            <p><strong><i class="bi bi-globe me-1"></i>Davlat:</strong>
                                <?php echo e($article->conference->country->name); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><i class="bi bi-file-text me-1"></i>Sahifalar:</strong> <?php echo e($article->page_range); ?>

                                (<?php echo e($article->page_count); ?> bet)</p>
                            <p><strong><i class="bi bi-sort-numeric-up me-1"></i>Tartib raqami:</strong>
                                <?php echo e($article->order_number); ?></p>
                        </div>
                    </div>

                    <hr>

                    <!-- PDF yuklab olish tugmalari -->
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?php echo e(Storage::url($article->pdf_path)); ?>" class="btn btn-primary" target="_blank">
                            <i class="bi bi-file-pdf me-1"></i>Asl PDF
                        </a>

                        <?php if($article->formatted_pdf_path): ?>
                            <a href="<?php echo e(route('admin.articles.download-formatted', $article)); ?>" class="btn btn-success">
                                <i class="bi bi-file-earmark-pdf me-1"></i>Formatlangan PDF
                            </a>
                        <?php else: ?>
                            <form action="<?php echo e(route('admin.articles.reformat', $article)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-outline-success">
                                    <i class="bi bi-magic me-1"></i>Formatlangan PDF yaratish
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if($article->formatted_pdf_path): ?>
                            <form action="<?php echo e(route('admin.articles.reformat', $article)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-outline-warning">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Qayta formatlash
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if($article->article_link): ?>
                            <a href="<?php echo e($article->article_link); ?>" class="btn btn-outline-primary" target="_blank">
                                <i class="bi bi-link me-1"></i>Article Link
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-info-circle me-1"></i>Status
                </div>
                <div class="card-body text-center">
                    <?php if($article->status === 'published'): ?>
                        <span class="badge bg-success fs-5 px-4 py-2">
                            <i class="bi bi-check-circle me-1"></i>Nashr etilgan
                        </span>
                        <p class="text-muted mt-2 mb-0"><?php echo e($article->published_at?->format('d.m.Y H:i')); ?></p>
                    <?php else: ?>
                        <span class="badge bg-warning fs-5 px-4 py-2">
                            <i class="bi bi-clock me-1"></i>Kutilmoqda
                        </span>
                        <form action="<?php echo e(route('admin.articles.publish', $article)); ?>" method="POST" class="mt-3">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-success btn-lg w-100">
                                <i class="bi bi-send me-1"></i>Nashr qilish
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sertifikat -->
            <div class="card mb-4">
                <div class="card-header bg-warning bg-opacity-10">
                    <i class="bi bi-award me-1"></i>Sertifikat
                </div>
                <div class="card-body text-center">
                    <?php if($article->certificate): ?>
                        <i class="bi bi-award text-warning" style="font-size:4rem;"></i>
                        <p class="mt-2 mb-1">
                            <strong class="text-primary"><?php echo e($article->certificate->certificate_number); ?></strong>
                        </p>
                        <p class="text-muted small mb-3">
                            Berilgan sana: <?php echo e($article->certificate->issue_date->format('d.m.Y')); ?>

                        </p>
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="<?php echo e(route('admin.certificates.download', $article)); ?>" class="btn btn-success">
                                <i class="bi bi-download me-1"></i>Yuklab olish
                            </a>
                            <form action="<?php echo e(route('admin.certificates.regenerate', $article)); ?>" method="POST"
                                class="d-inline">
                                <?php echo csrf_field(); ?>
                                <button class="btn btn-outline-warning" title="Qayta yaratish">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <i class="bi bi-question-circle text-muted" style="font-size:3rem;"></i>
                        <p class="text-muted mt-2">Sertifikat mavjud emas</p>
                        <?php if($article->status === 'published'): ?>
                            <form action="<?php echo e(route('admin.certificates.generate', $article)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-warning w-100">
                                    <i class="bi bi-award me-1"></i>Sertifikat yaratish
                                </button>
                            </form>
                        <?php else: ?>
                            <small class="text-muted">Avval maqolani nashr qiling</small>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Amallar -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-gear me-1"></i>Amallar
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?php echo e(route('admin.articles.edit', $article)); ?>" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i>Tahrirlash
                        </a>
                        <a href="<?php echo e(route('admin.conferences.show', $article->conference)); ?>"
                            class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Konferensiyaga qaytish
                        </a>
                        <form action="<?php echo e(route('admin.articles.destroy', $article)); ?>" method="POST"
                            onsubmit="return confirm('Haqiqatan ham o\'chirmoqchimisiz?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash me-1"></i>O'chirish
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/artiqle_new/resources/views/admin/articles/show.blade.php ENDPATH**/ ?>