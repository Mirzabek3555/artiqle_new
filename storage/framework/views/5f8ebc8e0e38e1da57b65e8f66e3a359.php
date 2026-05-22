<?php $__env->startSection('page-title', 'Maqolani tahrirlash'); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-header">
            <i class="bi bi-pencil me-2"></i><?php echo e(Str::limit($article->title, 50)); ?> - tahrirlash
        </div>
        <div class="card-body">
            <?php if($errors->any()): ?>
                <div class="alert alert-danger">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div><?php echo e($error); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('admin.articles.update', $article)); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

                <!-- Davlat va Konferensiya -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-globe me-1"></i>Davlat
                        </label>
                        <select class="form-select form-select-lg" id="edit_country_id" name="country_id" required>
                            <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($country->id); ?>" <?php echo e($article->conference->country_id == $country->id ? 'selected' : ''); ?>>
                                    <?php echo e($country->name); ?> - <?php echo e($country->conference_name ?? 'Konferensiya nomi berilmagan'); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <small class="text-muted">Maqola qaysi davlat konferensiyasiga tegishli ekanligini tanlang</small>
                    </div>

                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-calendar-month me-1"></i>
                            Konferensiya oyi <span class="text-danger">*</span>
                        </label>
                        <input
                            type="month"
                            class="form-control form-select-lg <?php $__errorArgs = ['month_year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            id="edit_month_year"
                            name="month_year"
                            value="<?php echo e(old('month_year', $article->conference->month_year)); ?>"
                            required
                        >
                        <?php $__errorArgs = ['month_year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div class="form-text text-info">
                            <i class="bi bi-info-circle me-1"></i>
                            O'tgan yoki kelgusi oyni kiritish mumkin. Maqola avtomatik tegishli oy to'plamiga biriktiriladi.
                        </div>
                    </div>

                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-calendar-check me-1"></i>
                            Konferensiya aniq sanasi <span class="text-danger">*</span>
                        </label>
                        <input
                            type="date"
                            class="form-control form-select-lg <?php $__errorArgs = ['conference_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            id="edit_conference_date"
                            name="conference_date"
                            value="<?php echo e(old('conference_date', $article->conference->conference_date?->format('Y-m-d'))); ?>"
                            required
                        >
                        <?php $__errorArgs = ['conference_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div class="form-text text-warning">
                            <i class="bi bi-calendar3 me-1"></i>
                            Masalan: <strong>12-mart</strong>, <strong>20-aprel</strong>. PDF da aniq sana chiqadi.
                        </div>
                    </div>
                </div>

                <!-- Muallif ma'lumotlari -->
                <div class="card mb-4 border-warning">
                    <div class="card-header bg-warning bg-opacity-10">
                        <i class="bi bi-person-badge me-2"></i>Muallif ma'lumotlari
                        <small class="text-muted">(Qo'lda kiritiladi)</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-person me-1"></i>Muallif ismi (to'liq) <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg" name="author_name" 
                                       value="<?php echo e(old('author_name', $article->author_name)); ?>" 
                                       placeholder="Masalan: Karimov Abdulla Rashidovich" required>
                                <small class="text-muted">Sertifikatda ko'rsatiladigan ism</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-building me-1"></i>Tashkilot / Universitet
                                </label>
                                <input type="text" class="form-control" name="author_affiliation" 
                                       value="<?php echo e(old('author_affiliation', $article->author_affiliation)); ?>"
                                       placeholder="Masalan: Tashkent University, Uzbekistan">
                                <small class="text-muted">Muallif ish joyi yoki o'quv muassasasi</small>
                            </div>
                        </div>

                        <!-- Mavjud foydalanuvchini bog'lash (ixtiyoriy) -->
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-link me-1"></i>Tizim foydalanuvchisiga bog'lash (ixtiyoriy)
                                </label>
                                <select class="form-select" name="author_id">
                                    <option value="">Bog'lamaslik</option>
                                    <?php $__currentLoopData = $authors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $author): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($author->id); ?>" <?php echo e($article->author_id == $author->id ? 'selected' : ''); ?>>
                                            <?php echo e($author->name); ?> (<?php echo e($author->email); ?>)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <small class="text-muted">Foydalanuvchi o'z kabinetida maqolasini ko'rishi uchun</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Maqola ma'lumotlari -->
                <div class="card mb-4">
                    <div class="card-header bg-primary bg-opacity-10">
                        <i class="bi bi-journal-text me-2"></i>Maqola ma'lumotlari
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-type me-1"></i>Maqola sarlavhasi <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg" name="title" 
                                   value="<?php echo e(old('title', $article->title)); ?>" 
                                   placeholder="Maqolaning to'liq nomini kiriting" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-card-text me-1"></i>Annotatsiya
                            </label>
                            <textarea class="form-control" name="abstract" rows="4" 
                                      placeholder="Maqolaning qisqacha mazmuni..."><?php echo e(old('abstract', $article->abstract)); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-tags me-1"></i>Kalit so'zlar
                            </label>
                            <textarea class="form-control" name="keywords" rows="3" 
                                      placeholder="Kalit so'zlar"><?php echo e(old('keywords', $article->keywords)); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-book me-1"></i>Adabiyotlar (References)
                            </label>
                            <textarea class="form-control" name="references" rows="5" 
                                      placeholder="Foydalanilgan adabiyotlar..."><?php echo e(old('references', $article->references)); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Fayl yuklash -->
                <div class="card mb-4 border-success">
                    <div class="card-header bg-success bg-opacity-10">
                        <i class="bi bi-file-earmark-word me-2"></i>Maqola faylini yangilash (Word docx)
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-upload me-1"></i>Yangi DOCX fayl (agar o'zgartirmoqchi bo'lsangiz)
                            </label>
                            <input type="file" class="form-control form-control-lg" name="docx_file" accept=".docx,.doc">
                            <small class="text-muted">
                                Fayl yuklamasangiz, maqolaning joriy fayli saqlanib qoladi.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Amallar -->
                <div class="d-flex gap-2 justify-content-between">
                    <a href="<?php echo e(route('admin.articles.show', $article)); ?>" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-arrow-left me-1"></i>Bekor qilish
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg me-1"></i>Yangilash
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/artiqle_new/resources/views/admin/articles/edit.blade.php ENDPATH**/ ?>