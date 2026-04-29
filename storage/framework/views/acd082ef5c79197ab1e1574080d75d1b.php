

<?php $__env->startSection('title', $country->conference_name ?? $country->name); ?>

<?php $__env->startSection('content'); ?>
    <!-- Journal Header -->
    <section class="journal-header py-3" style="background: #fff; border-bottom: 3px solid #1a5276;">
        <div class="container">
            <h1 class="journal-title mb-2" style="color: #1a5276; font-size: 1.8rem; font-weight: 600; line-height: 1.3;">
                <?php echo e($country->conference_name ?? 'International Scientific Conference Proceedings'); ?>

            </h1>
            <!-- Navigation -->
            <nav class="journal-nav">
                <a href="#current" class="journal-nav-link active">Current</a>
                <a href="<?php echo e(route('archive')); ?>" class="journal-nav-link">Archives</a>
                <a href="#" class="journal-nav-link">About</a>
            </nav>
        </div>
    </section>

    <div class="container py-4">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Current Issue -->
                <section id="current" class="mb-5">
                    <h2 class="section-heading">Current Issue</h2>

                    <div class="issue-info mb-4">
                        <h3 class="issue-title" style="color: #1a5276; font-size: 1.1rem; font-weight: 600;">
                            Vol. <?php echo e(date('Y')); ?> No. <?php echo e(date('m')); ?> (<?php echo e(date('Y')); ?>):
                            <?php echo e($country->conference_name ?? $country->name . ' - Scientific Conference Proceedings'); ?>

                        </h3>

                        <div class="row mt-4">
                            <!-- Cover Image -->
                            <div class="col-md-4 mb-3 mb-md-0">
                                <!-- Cover Header -->
                                <div class="position-relative shadow-sm" style="height: 320px; overflow: hidden; background: #fff; border: 1px solid #eee; border-radius: 4px;">
                                    <?php if($country->cover_image): ?>
                                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: url('<?php echo e(asset($country->cover_image)); ?>'); background-size: contain; background-position: center; background-repeat: no-repeat; z-index: 1;"></div>
                                    <?php else: ?>
                                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg, #1a3a5f 0%, #0d2137 100%); z-index: 1; display: flex; align-items: center; justify-content: center; flex-direction: column; color: white;">
                                            <?php if($country->flag_url): ?>
                                                <img src="<?php echo e(Storage::url($country->flag_url)); ?>" style="width: 80px; height: 50px; object-fit: cover; border-radius: 4px; margin-bottom: 10px;">
                                            <?php endif; ?>
                                            <h4 style="font-weight: bold; margin: 0; text-align: center; padding: 0 10px;"><?php echo e(strtoupper($country->name_en ?? $country->name)); ?></h4>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <!-- Description -->
                            <div class="col-md-8">
                                <p class="text-muted" style="line-height: 1.7;">
                                    <?php if($country->conference_description): ?>
                                        <?php echo e($country->conference_description); ?>

                                    <?php else: ?>
                                        The Proceedings of the scientific conference on multidisciplinary research are an
                                        electronic conference series.
                                        The materials of this conference publish the original research work presented by the
                                        conference participants.
                                    <?php endif; ?>
                                </p>
                                <p class="mt-3">
                                    <strong>Published:</strong> <?php echo e(now()->format('Y-m-d')); ?>

                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Articles Section -->
                <section id="articles" class="articles-section">
                    <fieldset class="articles-fieldset">
                        <legend>Articles</legend>

                        <?php $__empty_1 = true; $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="article-item py-3 <?php echo e(!$loop->last ? 'border-bottom' : ''); ?>">
                                <h4 class="article-title">
                                    <a href="<?php echo e(route('article.show', $article)); ?>">
                                        <?php echo e(strtoupper($article->title)); ?>

                                    </a>
                                </h4>
                                <p class="article-authors text-muted mb-2">
                                    <?php echo e($article->author_name ?? $article->author_display_name); ?>

                                    <?php if($article->author_affiliation): ?>
                                        <br><small class="text-secondary"><?php echo e($article->author_affiliation); ?></small>
                                    <?php endif; ?>
                                </p>
                                <div class="d-flex align-items-center gap-3">
                                    <?php if($article->formatted_pdf_path || $article->pdf_path): ?>
                                        <a href="<?php echo e(Storage::url($article->formatted_pdf_path ?? $article->pdf_path)); ?>"
                                            class="btn btn-outline-secondary btn-sm" target="_blank"
                                            download="<?php echo e($article->page_range ? $article->page_range . '.pdf' : 'article.pdf'); ?>">
                                            <i class="bi bi-file-pdf me-1"></i>download
                                        </a>
                                    <?php endif; ?>
                                    <span class="text-muted ms-auto"><?php echo e($article->page_range); ?></span>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-file-earmark-x display-4 text-muted"></i>
                                <p class="mt-3 text-muted">No articles published yet.</p>
                            </div>
                        <?php endif; ?>
                    </fieldset>
                </section>

                <!-- Pagination -->
                <?php if($articles->hasPages()): ?>
                    <div class="d-flex justify-content-center mt-4">
                        <?php echo e($articles->links()); ?>

                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Information -->
                <div class="sidebar-block mb-4">
                    <h5 class="sidebar-title">Information</h5>
                    <ul class="sidebar-links">
                        <li><a href="#"><i class="bi bi-book me-2"></i>For Readers</a></li>
                        <li><a href="#"><i class="bi bi-pencil me-2"></i>For Authors</a></li>
                        <li><a href="#"><i class="bi bi-building me-2"></i>For Librarians</a></li>
                    </ul>
                </div>

                <!-- Country Info -->
                <div class="sidebar-block mb-4">
                    <h5 class="sidebar-title">Conference Country</h5>
                    <div class="country-info text-center py-3">
                        <?php if($country->flag_url): ?>
                            <img src="<?php echo e(Storage::url($country->flag_url)); ?>" alt="<?php echo e($country->name); ?>"
                                style="width: 100px; height: 65px; object-fit: cover; border-radius: 6px; box-shadow: 0 3px 10px rgba(0,0,0,0.15);">
                        <?php endif; ?>
                        <h6 class="mt-3 mb-1"><?php echo e($country->name); ?></h6>
                        <p class="text-muted small mb-0"><?php echo e($country->name_en); ?></p>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="sidebar-block mb-4">
                    <h5 class="sidebar-title">Statistics</h5>
                    <ul class="stats-list">
                        <li>
                            <span class="stat-label"><i class="bi bi-file-earmark-text me-2"></i>Total Articles</span>
                            <span class="stat-value"><?php echo e($articles->total()); ?></span>
                        </li>
                        <li>
                            <span class="stat-label"><i class="bi bi-calendar-check me-2"></i>Published</span>
                            <span class="stat-value"><?php echo e(date('Y')); ?></span>
                        </li>
                        <li>
                            <span class="stat-label"><i class="bi bi-award me-2"></i>Certificate</span>
                            <span class="stat-value text-success">Available</span>
                        </li>
                    </ul>
                </div>

                <!-- QR Code -->
                <?php if($country->cover_image): ?>
                    <div class="sidebar-block">
                        <h5 class="sidebar-title">Quick Access</h5>
                        <div class="text-center py-3">
                            <div class="qr-placeholder mx-auto"
                                style="width: 120px; height: 120px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-qr-code" style="font-size: 3rem; color: #999;"></i>
                            </div>
                            <p class="text-muted small mt-2 mb-0">Scan to access</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <style>
        /* Journal Header */
        .journal-title {
            margin: 0;
        }

        .journal-nav {
            display: flex;
            gap: 5px;
            margin-top: 15px;
        }

        .journal-nav-link {
            padding: 8px 16px;
            text-decoration: none;
            color: #555;
            font-size: 0.9rem;
            border-radius: 4px 4px 0 0;
            transition: all 0.2s;
        }

        .journal-nav-link:hover,
        .journal-nav-link.active {
            background: #1a5276;
            color: #fff;
        }

        /* Section Heading */
        .section-heading {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }

        /* Articles Fieldset */
        .articles-fieldset {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 20px;
            margin: 0;
        }

        .articles-fieldset legend {
            font-size: 1rem;
            font-weight: 600;
            color: #555;
            padding: 0 10px;
            width: auto;
            margin-bottom: 0;
        }

        /* Article Item */
        .article-item {
            transition: background 0.2s;
        }

        .article-item:hover {
            background: #fafafa;
        }

        .article-title {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .article-title a {
            color: #1a5276;
            text-decoration: none;
        }

        .article-title a:hover {
            text-decoration: underline;
        }

        .article-authors {
            font-size: 0.88rem;
        }

        /* Sidebar */
        .sidebar-block {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            overflow: hidden;
        }

        .sidebar-title {
            background: #f8f9fa;
            padding: 12px 15px;
            margin: 0;
            font-size: 0.95rem;
            font-weight: 600;
            color: #1a5276;
            border-bottom: 1px solid #e0e0e0;
        }

        .sidebar-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-links li {
            border-bottom: 1px solid #f0f0f0;
        }

        .sidebar-links li:last-child {
            border-bottom: none;
        }

        .sidebar-links a {
            display: block;
            padding: 10px 15px;
            color: #1a5276;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.2s;
        }

        .sidebar-links a:hover {
            background: #f8f9fa;
        }

        /* Stats List */
        .stats-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .stats-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.88rem;
        }

        .stats-list li:last-child {
            border-bottom: none;
        }

        .stat-label {
            color: #666;
        }

        .stat-value {
            font-weight: 600;
            color: #333;
        }

        /* Breadcrumb Section */
        .breadcrumb-section {
            background: #f8f9fa;
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\artiqle\resources\views/public/countries/show.blade.php ENDPATH**/ ?>