<?php $__env->startSection('title', $conference->title); ?>

<?php $__env->startSection('content'); ?>
    <!-- Breadcrumb -->
    <section class="breadcrumb-section">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>"><i class="bi bi-house me-1"></i>Bosh
                            sahifa</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('countries')); ?>">Davlatlar</a></li>
                    <li class="breadcrumb-item"><a
                            href="<?php echo e(route('country.show', $conference->country)); ?>"><?php echo e($conference->country->name); ?></a>
                    </li>
                    <li class="breadcrumb-item active"><?php echo e(Str::limit($conference->title, 30)); ?></li>
                </ol>
            </nav>
        </div>
    </section>

    <div class="container py-5">
        <!-- Conference Header -->
        <div class="card journal-card mb-4">
            <div class="card-header d-flex align-items-center">
                <?php if($conference->country->flag_url): ?>
                    <img src="<?php echo e(Storage::url($conference->country->flag_url)); ?>" alt="<?php echo e($conference->country->name); ?>"
                        style="width: 60px; height: 40px; object-fit: cover; border-radius: 6px; margin-right: 15px;">
                <?php endif; ?>
                <div class="flex-grow-1">
                    <h1 class="card-title fs-4 mb-1"><?php echo e($conference->title); ?></h1>
                    <p class="text-muted mb-0">
                        <i class="bi bi-geo-alt me-1"></i><?php echo e($conference->country->name); ?>

                        <span class="mx-2">•</span>
                        <i class="bi bi-calendar me-1"></i><?php echo e($conference->conference_date->format('d F Y')); ?>

                    </p>
                </div>
                <div>
                    <?php if($conference->status === 'active'): ?>
                        <span class="badge bg-success fs-6">
                            <i class="bi bi-check-circle me-1"></i>Faol
                        </span>
                    <?php elseif($conference->status === 'completed'): ?>
                        <span class="badge bg-secondary fs-6">
                            <i class="bi bi-check2-all me-1"></i>Yakunlangan
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <?php if($conference->description): ?>
                    <p class="mb-0"><?php echo e($conference->description); ?></p>
                <?php else: ?>
                    <p class="text-muted mb-0">
                        Ko'p sohali tadqiqotlar bo'yicha ilmiy konferensiya materiallari elektron konferensiya seriyalari
                        hisoblanadi.
                        Ushbu konferensiya materiallari konferensiya ishtirokchilari tomonidan taqdim etilgan original tadqiqot
                        ishlarini nashr etadi.
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Collection Download -->
        <?php if($conference->collection_pdf_path): ?>
            <div class="alert alert-info d-flex align-items-center justify-content-between mb-4 border-0"
                style="background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);">
                <div>
                    <i class="bi bi-file-pdf-fill me-2 fs-4 text-danger"></i>
                    <strong>Oy yakunidagi to'plam mavjud!</strong>
                    <span class="text-muted ms-2">Barcha maqolalar bitta PDF faylda.</span>
                </div>
                <a href="<?php echo e(Storage::url($conference->collection_pdf_path)); ?>" class="btn btn-primary" target="_blank">
                    <i class="bi bi-download me-1"></i>Yuklab olish
                </a>
            </div>
        <?php endif; ?>

        <!-- Articles List -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="section-title mb-0">
                <i class="bi bi-file-earmark-text me-2"></i>Maqolalar
            </h3>
            <span class="badge badge-primary fs-6">
                <?php echo e($conference->articles->count()); ?> ta maqola
            </span>
        </div>

        <div class="row g-4">
            <?php $__empty_1 = true; $__currentLoopData = $conference->articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="col-12">
                    <div class="card article-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span
                                        class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 45px; height: 45px; font-size: 1.1rem;">
                                        <?php echo e($article->order_number); ?>

                                    </span>
                                </div>
                                <div class="col">
                                    <h5 class="mb-1">
                                        <a href="<?php echo e($article->url); ?>" class="article-title">
                                            <?php echo e($article->title); ?>

                                        </a>
                                    </h5>
                                    <p class="article-meta mb-0">
                                        <i class="bi bi-person me-1"></i><?php echo e($article->author_display_name); ?>

                                        <span class="mx-2">•</span>
                                        <i class="bi bi-file-earmark me-1"></i><?php echo e($article->page_range); ?>

                                        (<?php echo e($article->page_count); ?> bet)
                                        <?php if($article->published_at): ?>
                                            <span class="mx-2">•</span>
                                            <i class="bi bi-calendar me-1"></i><?php echo e($article->published_at->format('d.m.Y')); ?>

                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="col-auto">
                                    <a href="<?php echo e($article->url); ?>" class="btn-view">
                                        <i class="bi bi-eye me-1"></i>Ko'rish
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-file-earmark-x display-1 text-muted"></i>
                        <h4 class="mt-3 text-muted">Hozircha maqolalar mavjud emas</h4>
                        <p class="text-muted">Tez orada yangi maqolalar qo'shiladi.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/artiqle_new/resources/views/public/conferences/show.blade.php ENDPATH**/ ?>