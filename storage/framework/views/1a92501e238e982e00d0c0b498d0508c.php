<?php $__env->startSection('page-title', 'Yangi davlat'); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-header">
            <i class="bi bi-globe me-2"></i>Yangi davlat qo'shish
        </div>
        <div class="card-body">
            <?php if($errors->any()): ?>
                <div class="alert alert-danger">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div><?php echo e($error); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('admin.countries.store')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nomi (O'zbekcha)</label>
                        <input type="text" class="form-control" name="name" value="<?php echo e(old('name')); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nomi (Inglizcha)</label>
                        <input type="text" class="form-control" name="name_en" value="<?php echo e(old('name_en')); ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Davlat kodi</label>
                        <input type="text" class="form-control" name="code" value="<?php echo e(old('code')); ?>" maxlength="3"
                            required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Bayroq</label>
                        <input type="file" class="form-control" name="flag" accept="image/*">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Taqsimot Tartibi (kun)</label>
                        <input type="number" class="form-control" name="schedule_order" value="<?php echo e(old('schedule_order', 0)); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Status</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
                            <label class="form-check-label" for="is_active">Faol</label>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check me-1"></i>Saqlash
                    </button>
                    <a href="<?php echo e(route('admin.countries.index')); ?>" class="btn btn-secondary">Bekor qilish</a>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/artiqle_new/resources/views/admin/countries/create.blade.php ENDPATH**/ ?>