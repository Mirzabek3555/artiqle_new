<?php $__env->startSection('page-title', $country->name . ' - davlat ma\'lumotlari'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <!-- Davlat ma'lumotlari -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span>
                        <i class="bi bi-globe me-2"></i><?php echo e($country->name); ?> (<?php echo e($country->name_en); ?>)
                    </span>
                    <div>
                        <a href="<?php echo e(route('admin.countries.edit', $country)); ?>" class="btn btn-light btn-sm">
                            <i class="bi bi-pencil me-1"></i>Tahrirlash
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Cover Image -->
                        <div class="col-md-5 mb-4">
                            <?php if($country->cover_image): ?>
                                <img src="<?php echo e(asset($country->cover_image)); ?>" alt="<?php echo e($country->name); ?>"
                                    class="img-fluid rounded shadow" style="max-height: 300px; object-fit: contain;">
                            <?php elseif($country->flag_url): ?>
                                <div class="text-center p-4 bg-light rounded">
                                    <img src="<?php echo e(Storage::url($country->flag_url)); ?>" alt="<?php echo e($country->name); ?>"
                                        style="max-height: 100px; border-radius: 5px; box-shadow: 0 3px 10px rgba(0,0,0,0.2);">
                                </div>
                            <?php else: ?>
                                <div class="text-center p-5 bg-light rounded">
                                    <i class="bi bi-globe display-1 text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Ma'lumotlar -->
                        <div class="col-md-7">
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 40%;"><i class="bi bi-hash me-1"></i>ID:</th>
                                    <td><?php echo e($country->id); ?></td>
                                </tr>
                                <tr>
                                    <th><i class="bi bi-type me-1"></i>Nom (O'zbekcha):</th>
                                    <td><?php echo e($country->name); ?></td>
                                </tr>
                                <tr>
                                    <th><i class="bi bi-translate me-1"></i>Nom (Inglizcha):</th>
                                    <td><?php echo e($country->name_en); ?></td>
                                </tr>
                                <tr>
                                    <th><i class="bi bi-code me-1"></i>Kod:</th>
                                    <td><span class="badge bg-secondary"><?php echo e($country->code); ?></span></td>
                                </tr>
                                <tr>
                                    <th><i class="bi bi-toggle-on me-1"></i>Holat:</th>
                                    <td>
                                        <?php if($country->is_active): ?>
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Faol</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Faol emas</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th><i class="bi bi-calendar me-1"></i>Yaratilgan:</th>
                                    <td><?php echo e($country->created_at->format('d.m.Y H:i')); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Konferensiya ma'lumotlari -->
            <div class="card mb-4">
                <div class="card-header bg-success bg-opacity-10">
                    <i class="bi bi-journal-bookmark me-2"></i>Konferensiya ma'lumotlari
                </div>
                <div class="card-body">
                    <h5 class="mb-3">
                        <?php echo e($country->conference_name ?? 'Konferensiya nomi kiritilmagan'); ?>

                    </h5>
                    <?php if($country->conference_description): ?>
                        <p class="text-muted mb-0"><?php echo e($country->conference_description); ?></p>
                    <?php else: ?>
                        <p class="text-muted mb-0 fst-italic">Tavsif kiritilmagan</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Statistika -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-info bg-opacity-10">
                    <i class="bi bi-bar-chart me-2"></i>Statistika
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span><i class="bi bi-journal me-2"></i>Konferensiyalar:</span>
                        <span class="badge bg-primary fs-6"><?php echo e($country->conferences->count()); ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span><i class="bi bi-file-text me-2"></i>Jami maqolalar:</span>
                        <span class="badge bg-success fs-6"><?php echo e($country->conferences->sum('articles_count')); ?></span>
                    </div>
                </div>
            </div>

            <!-- Konferensiyalar ro'yxati -->
            <?php if($country->conferences->count() > 0): ?>
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-list me-2"></i>Konferensiyalar
                    </div>
                    <div class="list-group list-group-flush">
                        <?php $__currentLoopData = $country->conferences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conference): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('admin.conferences.show', $conference)); ?>"
                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold"><?php echo e($conference->month); ?>/<?php echo e($conference->year); ?></div>
                                    <small class="text-muted"><?php echo e($conference->articles_count); ?> maqola</small>
                                </div>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Orqaga tugma -->
    <div class="mt-4">
        <a href="<?php echo e(route('admin.countries.index')); ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Davlatlar ro'yxatiga qaytish
        </a>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/artiqle_new/resources/views/admin/countries/show.blade.php ENDPATH**/ ?>