<?php $__env->startSection('title', 'Konferensiyalar'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Breadcrumb -->
    <section class="breadcrumb-section">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>"><i class="bi bi-house me-1"></i>Bosh
                            sahifa</a></li>
                    <li class="breadcrumb-item active">Konferensiyalar</li>
                </ol>
            </nav>
        </div>
    </section>

    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="section-title d-inline-block">
                <i class="bi bi-journals me-2"></i>Barcha Konferensiyalar
            </h1>
            <p class="text-muted mt-3">Turli davlatlar bo'yicha xalqaro ilmiy konferensiyalar</p>
        </div>

        <div class="row g-4">
            <?php $__empty_1 = true; $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="col-md-6">
                    <a href="<?php echo e(route('country.show', $country)); ?>" class="text-decoration-none">
                        <div class="card conference-card h-100 border-0 shadow-sm overflow-hidden">
                            <!-- Cover Image Header -->
                            <div class="card-img-top position-relative" style="height: 220px; overflow: hidden;">
                                <?php if($country->cover_image): ?>
                                    <img src="<?php echo e(asset($country->cover_image)); ?>" 
                                         alt="<?php echo e($country->name); ?>"
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #1a3a5f 0%, #0d2137 100%); display: flex; align-items: center; justify-content: center;">
                                        <?php if($country->flag_url): ?>
                                            <img src="<?php echo e(Storage::url($country->flag_url)); ?>" 
                                                 alt="<?php echo e($country->name); ?>"
                                                 style="width: 120px; height: 80px; object-fit: cover; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.4);">
                                        <?php else: ?>
                                            <div class="text-center">
                                                <i class="bi bi-globe-americas text-white" style="font-size: 5rem; opacity: 0.5;"></i>
                                                <div class="text-white mt-2"><?php echo e($country->code); ?></div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <!-- Country badge overlay -->
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge bg-white text-dark shadow-sm px-3 py-2" style="font-size: 0.9rem;">
                                        <?php if($country->flag_url): ?>
                                            <img src="<?php echo e(Storage::url($country->flag_url)); ?>" 
                                                 style="width: 24px; height: 16px; object-fit: cover; border-radius: 3px; margin-right: 6px;">
                                        <?php endif; ?>
                                        <?php echo e($country->name); ?>

                                    </span>
                                </div>
                                <!-- Articles count -->
                                <div class="position-absolute bottom-0 start-0 m-3">
                                    <span class="badge bg-primary px-3 py-2">
                                        <i class="bi bi-file-earmark-text me-1"></i><?php echo e($country->articles_count ?? 0); ?> maqola
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Card Body -->
                            <div class="card-body p-4">
                                <h5 class="card-title mb-2" style="color: var(--primary-blue); font-weight: 700; font-size: 1.2rem;">
                                    <?php echo e($country->conference_name ?? 'Bu yerda konferensiya nomi yoziladi'); ?>

                                </h5>
                                <p class="text-muted small mb-3">
                                    <i class="bi bi-geo-alt me-1"></i><?php echo e($country->name); ?> (<?php echo e($country->name_en); ?>)
                                </p>
                                <?php if($country->conference_description): ?>
                                    <p class="card-text text-muted mb-3" style="font-size: 0.9rem;">
                                        <?php echo e(Str::limit($country->conference_description, 150)); ?>

                                    </p>
                                <?php else: ?>
                                    <p class="card-text text-muted mb-3" style="font-size: 0.9rem;">
                                        Ko'p sohali tadqiqotlar bo'yicha ilmiy konferensiya materiallari. Ushbu konferensiya materiallari konferensiya ishtirokchilari tomonidan taqdim etilgan original tadqiqot ishlarini nashr etadi.
                                    </p>
                                <?php endif; ?>
                                
                                <div class="d-flex justify-content-end">
                                    <span class="btn btn-primary">
                                        <i class="bi bi-arrow-right me-1"></i>Maqolalarni ko'rish
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-globe-americas display-1 text-muted"></i>
                        <h4 class="mt-3 text-muted">Hozircha konferensiyalar mavjud emas</h4>
                        <p class="text-muted">Tez orada yangi konferensiyalar qo'shiladi.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <style>
        .conference-card {
            transition: all 0.3s ease;
            border-radius: 15px;
        }
        .conference-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
        }
        .conference-card .card-img-top img {
            transition: transform 0.5s ease;
        }
        .conference-card:hover .card-img-top img {
            transform: scale(1.05);
        }
    </style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/artiqle_new/resources/views/public/countries/index.blade.php ENDPATH**/ ?>