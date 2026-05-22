<?php $__env->startSection('title', 'Ro\'yxatdan o\'tish'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-plus text-primary" style="font-size: 4rem;"></i>
                        <h3 class="mt-3 fw-bold">Ro'yxatdan o'tish</h3>
                        <p class="text-muted">Yangi hisob yarating</p>
                    </div>

                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div><?php echo e($error); ?></div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo e(route('register')); ?>">
                        <?php echo csrf_field(); ?>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Ism</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo e(old('name')); ?>" required autofocus>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo e(old('email')); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Parol</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Parolni tasdiqlang</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" class="form-control" id="password_confirmation" 
                                       name="password_confirmation" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-person-plus me-1"></i>Ro'yxatdan o'tish
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <p class="text-muted">
                            Hisobingiz bormi? 
                            <a href="<?php echo e(route('login')); ?>" class="text-primary">Kirish</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/artiqle_new/resources/views/auth/register.blade.php ENDPATH**/ ?>