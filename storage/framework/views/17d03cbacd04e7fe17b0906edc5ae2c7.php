<?php $__env->startSection('page-title', 'Konferensiyani tahrirlash'); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-header"><i class="bi bi-pencil me-2"></i><?php echo e(Str::limit($conference->title, 40)); ?> - tahrirlash
        </div>
        <div class="card-body">
            <?php if($errors->any()): ?>
                <div class="alert alert-danger">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div><?php echo e($error); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('admin.conferences.update', $conference)); ?>" method="POST">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Davlat</label>
                        <select class="form-select" name="country_id" required>
                            <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($country->id); ?>" <?php echo e($conference->country_id == $country->id ? 'selected' : ''); ?>><?php echo e($country->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Boshlanish Sanasi (ixtiyoriy)</label>
                        <input type="date" class="form-control" name="start_date"
                            value="<?php echo e(optional($conference->start_date)->format('Y-m-d')); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tugash Sanasi (ixtiyoriy)</label>
                        <input type="date" class="form-control" name="end_date"
                            value="<?php echo e(optional($conference->end_date)->format('Y-m-d')); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Asosiy Sana</label>
                        <input type="date" class="form-control" name="conference_date"
                            value="<?php echo e($conference->conference_date->format('Y-m-d')); ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sarlavha</label>
                    <input type="text" class="form-control" name="title" value="<?php echo e(old('title', $conference->title)); ?>"
                        required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tavsif</label>
                    <textarea class="form-control" name="description"
                        rows="3"><?php echo e(old('description', $conference->description)); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status" required>
                        <option value="draft" <?php echo e($conference->status == 'draft' ? 'selected' : ''); ?>>Qoralama</option>
                        <option value="active" <?php echo e($conference->status == 'active' ? 'selected' : ''); ?>>Faol</option>
                        <option value="completed" <?php echo e($conference->status == 'completed' ? 'selected' : ''); ?>>Yakunlangan
                        </option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check me-1"></i>Yangilash</button>
                    <a href="<?php echo e(route('admin.conferences.index')); ?>" class="btn btn-secondary">Bekor qilish</a>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/artiqle_new/resources/views/admin/conferences/edit.blade.php ENDPATH**/ ?>