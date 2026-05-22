<?php $__env->startSection('page-title', 'Yangi konferensiya'); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-header"><i class="bi bi-calendar-event me-2"></i>Yangi konferensiya yaratish</div>
        <div class="card-body">
            <?php if($errors->any()): ?>
                <div class="alert alert-danger">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div><?php echo e($error); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('admin.conferences.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Davlat</label>
                        <select class="form-select" name="country_id" id="countrySelect" required>
                            <option value="">Tanlang...</option>
                            <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($country->id); ?>" 
                                    data-conf-name="<?php echo e($country->conference_name); ?>" 
                                    data-conf-desc="<?php echo e($country->conference_description); ?>"
                                    <?php echo e(old('country_id') == $country->id ? 'selected' : ''); ?>>
                                    <?php echo e($country->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Boshlanish Sanasi (ixtiyoriy)</label>
                        <input type="date" class="form-control" name="start_date" value="<?php echo e(old('start_date')); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tugash Sanasi (ixtiyoriy)</label>
                        <input type="date" class="form-control" name="end_date" value="<?php echo e(old('end_date')); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Asosiy Sana</label>
                        <input type="date" class="form-control" name="conference_date" value="<?php echo e(old('conference_date')); ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sarlavha</label>
                    <input type="text" class="form-control" name="title" id="titleInput" value="<?php echo e(old('title')); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tavsif</label>
                    <textarea class="form-control" name="description" id="descInput" rows="3"><?php echo e(old('description')); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status" required>
                        <option value="draft" <?php echo e(old('status') == 'draft' ? 'selected' : ''); ?>>Qoralama</option>
                        <option value="active" <?php echo e(old('status') == 'active' ? 'selected' : ''); ?>>Faol</option>
                        <option value="completed" <?php echo e(old('status') == 'completed' ? 'selected' : ''); ?>>Yakunlangan</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check me-1"></i>Saqlash</button>
                    <a href="<?php echo e(route('admin.conferences.index')); ?>" class="btn btn-secondary">Bekor qilish</a>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const countrySelect = document.getElementById('countrySelect');
        const titleInput = document.getElementById('titleInput');
        const descInput = document.getElementById('descInput');

        countrySelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const confName = selectedOption.getAttribute('data-conf-name');
                const confDesc = selectedOption.getAttribute('data-conf-desc');
                
                if (confName) titleInput.value = confName;
                if (confDesc) descInput.value = confDesc;
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/artiqle_new/resources/views/admin/conferences/create.blade.php ENDPATH**/ ?>