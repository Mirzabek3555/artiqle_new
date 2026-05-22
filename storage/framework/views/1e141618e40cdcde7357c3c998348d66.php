

<?php $__env->startSection('page-title', 'Konferensiya to\'plamlari'); ?>

<?php $__env->startSection('content'); ?>


<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-funnel me-2"></i>Filtrlash</span>

    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('admin.articles.index')); ?>" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label text-muted small mb-1">Davlat bo'yicha</label>
                <select name="country_id" class="form-select">
                    <option value="">— Barcha davlatlar —</option>
                    <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($country->id); ?>" <?php echo e(request('country_id') == $country->id ? 'selected' : ''); ?>>
                            <?php echo e($country->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label text-muted small mb-1">Oy bo'yicha</label>
                <select name="month_year" class="form-select">
                    <option value="">— Barcha oylar —</option>
                    <?php
                        $monthNames = [
                            '01'=>'Yanvar','02'=>'Fevral','03'=>'Mart',
                            '04'=>'Aprel','05'=>'May','06'=>'Iyun',
                            '07'=>'Iyul','08'=>'Avgust','09'=>'Sentabr',
                            '10'=>'Oktabr','11'=>'Noyabr','12'=>'Dekabr',
                        ];
                    ?>
                    <?php $__currentLoopData = $availableMonths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $my): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php [$yr, $mo] = explode('-', $my); ?>
                        <option value="<?php echo e($my); ?>" <?php echo e(request('month_year') === $my ? 'selected' : ''); ?>>
                            <?php echo e($monthNames[$mo] ?? $mo); ?> <?php echo e($yr); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-search me-1"></i>Filtrlash
                </button>
                <?php if(request()->hasAny(['country_id','month_year'])): ?>
                    <a href="<?php echo e(route('admin.articles.index')); ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<?php if($conferences->isEmpty()): ?>
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-inbox display-4 d-block mb-3"></i>
            <h5>Hech qanday konferensiya to'plami topilmadi</h5>
            <p class="mb-0">Yangi maqola qo'shsangiz, avtomatik to'plam yaratiladi.</p>
        </div>
    </div>
<?php else: ?>
    
    <div class="accordion" id="conferencesAccordion">
        <?php
            $groupedConferences = $conferences->groupBy('month_year');
        ?>

        <?php $__currentLoopData = $groupedConferences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $monthYear => $monthConferences): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                [$confYear, $confMonth] = explode('-', $monthYear);
                $monthLabel = ($monthNames[$confMonth] ?? $confMonth) . ' ' . $confYear;
            ?>
            
            <div class="accordion-item mb-3 border-0 shadow-sm rounded">
                <h2 class="accordion-header" id="heading-<?php echo e($monthYear); ?>">
                    <button class="accordion-button collapsed fw-bold fs-5 bg-white border border-bottom-0 rounded-top" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo e($monthYear); ?>" aria-expanded="false" aria-controls="collapse-<?php echo e($monthYear); ?>">
                        <i class="bi bi-calendar-check me-2 text-primary"></i> <?php echo e($monthLabel); ?> (<?php echo e($monthConferences->count()); ?> ta to'plam)
                    </button>
                </h2>
                <div id="collapse-<?php echo e($monthYear); ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?php echo e($monthYear); ?>" data-bs-parent="#conferencesAccordion">
                    <div class="accordion-body bg-light border border-top-0 rounded-bottom p-3">
                        <?php $__currentLoopData = $monthConferences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conference): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $totalArticles = $conference->articles->count();
                                $publishedCount = $conference->articles->where('status', 'published')->count();
                                $pendingCount   = $totalArticles - $publishedCount;
                            ?>

        <div class="card mb-4 shadow-sm" id="conf-<?php echo e($conference->id); ?>">
            
            <div class="card-header"
                style="background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%); color: #fff;">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <span class="badge bg-light text-dark me-2 fs-6">
                            <i class="bi bi-calendar3 me-1"></i><?php echo e($monthLabel); ?>

                        </span>
                        <strong class="fs-6">
                            <?php echo e($conference->country->name); ?>

                            <?php if($conference->country->conference_name): ?>
                                — <?php echo e($conference->country->conference_name); ?>

                            <?php endif; ?>
                        </strong>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-white text-dark">
                            <i class="bi bi-files me-1"></i><?php echo e($totalArticles); ?> maqola
                        </span>
                        <?php if($publishedCount > 0): ?>
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle me-1"></i><?php echo e($publishedCount); ?> nashr
                            </span>
                        <?php endif; ?>
                        <?php if($pendingCount > 0): ?>
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-hourglass me-1"></i><?php echo e($pendingCount); ?> kutmoqda
                            </span>
                        <?php endif; ?>
                        
                        <?php if($conference->status === 'completed'): ?>
                            <span class="badge bg-secondary">Yakunlangan</span>
                        <?php elseif($conference->status === 'active'): ?>
                            <span class="badge bg-success">Faol</span>
                        <?php else: ?>
                            <span class="badge bg-light text-dark">Loyiha</span>
                        <?php endif; ?>
                        <div class="btn-group btn-group-sm">
                            <a href="<?php echo e(route('admin.conferences.edit', $conference)); ?>" 
                               class="btn btn-light text-primary" title="To'plamni tahrirlash">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php if($conference->status !== 'completed'): ?>
                                <form action="<?php echo e(route('admin.conferences.complete', $conference)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Ushbu konferensiyani yakunlashni tasdiqlaysizmi? Yakunlangach asosiy sahifadagi Arxiv bo\'limiga o\'tadi.')">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-light text-success" title="Yakunlash">
                                        <i class="bi bi-flag-fill"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                            <form action="<?php echo e(route('admin.conferences.destroy', $conference)); ?>" 
                                  method="POST" class="d-inline" 
                                  onsubmit="return confirm('DIQQAT! Ushbu to\'plamni o\'chirish undagi BARCHA maqolalarning o\'chib ketishiga olib kelishi mumkin. Tasdiqlaysizmi?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-light text-danger" title="To'plamni o'chirish">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            <a href="<?php echo e(route('admin.articles.create', ['conference_id' => $conference->id])); ?>"
                               class="btn btn-light text-success" title="Maqola qo'shish">
                                <i class="bi bi-plus-lg"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="card-body p-0">
                <?php if($conference->articles->isEmpty()): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-folder-x me-2"></i>Bu oy uchun maqolalar mavjud emas
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3" style="width:40px">#</th>
                                    <th>Maqola sarlavhasi</th>
                                    <th>Muallif(lar)</th>
                                    <th>Yuklangan sana</th>
                                    <th>Betlar</th>
                                    <th>Fayl</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Amallar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $conference->articles->sortBy('order_number'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="ps-3 text-muted small"><?php echo e($article->order_number); ?></td>
                                        <td>
                                            <strong><?php echo e(Str::limit($article->title, 55)); ?></strong>
                                        </td>
                                        <td class="small">
                                            <span><?php echo e($article->author_name); ?></span>
                                            <?php if($article->co_authors): ?>
                                                <br><span class="text-muted">+ <?php echo e(Str::limit($article->co_authors, 30)); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="small text-muted text-nowrap">
                                            <?php echo e($article->created_at->format('d.m.Y')); ?>

                                        </td>
                                        <td class="small"><?php echo e($article->page_range); ?></td>
                                        <td>
                                            <?php if($article->pdf_path): ?>
                                                <a href="<?php echo e(route('admin.articles.download-formatted', $article)); ?>"
                                                   class="btn btn-sm btn-outline-danger" title="PDF yuklab olish" target="_blank">
                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($article->status === 'published'): ?>
                                                <span class="badge bg-success">Nashr</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Kutmoqda</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-3">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo e(route('admin.articles.show', $article)); ?>"
                                                   class="btn btn-outline-info" title="Ko'rish">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?php echo e(route('admin.articles.edit', $article)); ?>"
                                                   class="btn btn-outline-primary" title="Tahrirlash">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <?php if($article->status === 'pending'): ?>
                                                    <form action="<?php echo e(route('admin.articles.publish', $article)); ?>"
                                                          method="POST" class="d-inline">
                                                        <?php echo csrf_field(); ?>
                                                        <button class="btn btn-sm btn-success" title="Nashr qilish">
                                                            <i class="bi bi-check-lg"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                <form action="<?php echo e(route('admin.articles.destroy', $article)); ?>"
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Maqolani o\'chirishni tasdiqlaysizmi?')">
                                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                    <button class="btn btn-sm btn-outline-danger" title="O'chirish">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            
            <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2 py-2">
                <small class="text-muted">
                    <i class="bi bi-calendar-event me-1"></i>
                    <?php echo e($conference->conference_date ? $conference->conference_date->format('d.m.Y') : $monthLabel); ?>

                </small>
                <div class="d-flex gap-2">
                    <?php if($conference->collection_pdf_path): ?>
                        <a href="<?php echo e(route('admin.conferences.download-collection', $conference)); ?>"
                           class="btn btn-sm btn-outline-success">
                            <i class="bi bi-download me-1"></i>To'plamni yuklab olish
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo e(route('admin.conferences.show', $conference)); ?>"
                       class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-folder me-1"></i>To'plam tafsilotlari
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php endif; ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\artiqle\resources\views/admin/articles/index.blade.php ENDPATH**/ ?>