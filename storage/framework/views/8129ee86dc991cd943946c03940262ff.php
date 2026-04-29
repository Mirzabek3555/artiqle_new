

<?php $__env->startSection('title', $article->title); ?>
<?php $__env->startSection('description', Str::limit($article->abstract ?? '', 160)); ?>
<?php $__env->startSection('canonical', route('article.show', $article)); ?>


<?php $__env->startSection('scholar_meta'); ?>
    <!-- Primary Citation Meta Tags -->
    <meta name="citation_title" content="<?php echo e($article->title); ?>">

    <?php if($article->author_name): ?>
        <meta name="citation_author" content="<?php echo e($article->author_name); ?>">
    <?php elseif($article->author): ?>
        <meta name="citation_author" content="<?php echo e($article->author->name); ?>">
    <?php endif; ?>

    <?php if($article->author_affiliation): ?>
        <meta name="citation_author_institution" content="<?php echo e($article->author_affiliation); ?>">
    <?php endif; ?>

    <?php if($article->author && $article->author->email): ?>
        <meta name="citation_author_email" content="<?php echo e($article->author->email); ?>">
    <?php endif; ?>

    <!-- Publication Information -->
    <meta name="citation_publication_date" content="<?php echo e($article->created_at->format('Y/m/d')); ?>">
    <meta name="citation_journal_title" content="<?php echo e($article->conference->title ?? 'International Scientific Conferences'); ?>">
    <meta name="citation_conference_title" content="<?php echo e($article->conference->country->conference_name ?? $article->conference->title); ?>">
    <meta name="citation_publisher" content="International Scientific Online Conference (ISOC)">

    <!-- Abstract (Important for indexing) -->
    <?php if($article->abstract): ?>
        <meta name="citation_abstract" content="<?php echo e($article->abstract); ?>">
    <?php endif; ?>
    <meta name="citation_abstract_html_url" content="<?php echo e(url('article/' . $article->id)); ?>">

    <!-- Keywords -->
    <?php if($article->keywords): ?>
        <meta name="citation_keywords" content="<?php echo e($article->keywords); ?>">
    <?php endif; ?>

    <!-- PDF URL (CRITICAL - Must be publicly accessible) -->
    <meta name="citation_pdf_url" content="<?php echo e(url('storage/' . ($article->formatted_pdf_path ?? $article->pdf_path))); ?>">

    <!-- Page Numbers -->
    <?php if($article->start_page): ?>
        <meta name="citation_firstpage" content="<?php echo e($article->start_page); ?>">
    <?php endif; ?>
    <?php if($article->end_page): ?>
        <meta name="citation_lastpage" content="<?php echo e($article->end_page); ?>">
    <?php endif; ?>

    <!-- Language -->
    <meta name="citation_language" content="<?php echo e($article->language ?? 'en'); ?>">

    <!-- Public URL -->
    <meta name="citation_public_url" content="<?php echo e(route('article.show', $article)); ?>">

    <!-- Optional: DOI (if available) -->
    <?php if($article->doi): ?>
        <meta name="citation_doi" content="<?php echo e($article->doi); ?>">
    <?php endif; ?>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('og_meta'); ?>
    <meta property="og:type" content="article">
    <meta property="og:title" content="<?php echo e($article->title); ?>">
    <meta property="og:description"
        content="<?php echo e(Str::limit($article->abstract ?? 'Research article from International Scientific Online Conference', 200)); ?>">
    <meta property="og:url" content="<?php echo e(route('article.show', $article)); ?>">
    <?php if($article->conference->country->cover_image): ?>
        <meta property="og:image" content="<?php echo e(url($article->conference->country->cover_image)); ?>">
    <?php endif; ?>
    <meta property="article:published_time"
        content="<?php echo e($article->published_at ? $article->published_at->toIso8601String() : $article->created_at->toIso8601String()); ?>">
    <meta property="article:author" content="<?php echo e($article->author_name ?? $article->author->name ?? ''); ?>">
<?php $__env->stopSection(); ?>


<?php $__env->startSection('structured_data'); ?>
    <script type="application/ld+json">
                {
                    "@context": "https://schema.org",
                    "@type": "ScholarlyArticle",
                    "mainEntityOfPage": {
                        "@type": "WebPage",
                        "@id": "<?php echo e(route('article.show', $article)); ?>"
                    },
                    "headline": "<?php echo e($article->title); ?>",
                    "author": {
                        "@type": "Person",
                        "name": "<?php echo e($article->author_name ?? ($article->author ? $article->author->name : '')); ?>"
                        <?php if($article->author_affiliation): ?>
                            ,"affiliation": {
                                "@type": "Organization",
                                "name": "<?php echo e($article->author_affiliation); ?>"
                            }
                        <?php endif; ?>
                        <?php if($article->author && $article->author->email): ?>
                            ,"email": "<?php echo e($article->author->email); ?>"
                        <?php endif; ?>
                    },
                    <?php if($article->abstract): ?>
                        "abstract": "<?php echo e(addslashes($article->abstract)); ?>",
                    <?php endif; ?>
                    "datePublished": "<?php echo e($article->published_at ? $article->published_at->format('Y-m-d') : $article->created_at->format('Y-m-d')); ?>",
                    "dateModified": "<?php echo e($article->updated_at->format('Y-m-d')); ?>",
                    "publisher": {
                        "@type": "Organization",
                        "name": "International Scientific Online Conference (ISOC)",
                        "url": "https://artiqle.uz",
                        "logo": {
                            "@type": "ImageObject",
                            "url": "<?php echo e(asset('images/logo.png')); ?>"
                        }
                    },
                    <?php if($article->keywords): ?>
                        "keywords": "<?php echo e($article->keywords); ?>",
                    <?php endif; ?>
                    "inLanguage": "en",
                    "isAccessibleForFree": true,
                    <?php if($article->page_range): ?>
                        "pagination": "<?php echo e($article->page_range); ?>",
                    <?php endif; ?>
                    "isPartOf": {
                        "@type": "PublicationEvent",
                        "name": "<?php echo e($article->conference->title); ?>",
                        "location": {
                            "@type": "Place",
                            "name": "<?php echo e($article->conference->country->name_en ?? $article->conference->country->name); ?>"
                        },
                        "startDate": "<?php echo e($article->conference->conference_date->format('Y-m-d')); ?>"
                    }
                    <?php if($article->pdf_path): ?>
                        ,"associatedMedia": {
                            "@type": "MediaObject",
                            "contentUrl": "<?php echo e(url(Storage::url($article->formatted_pdf_path ?? $article->pdf_path))); ?>",
                            "encodingFormat": "application/pdf"
                        }
                    <?php endif; ?>
                }
                </script>

    <!-- Breadcrumb Structured Data -->
    <script type="application/ld+json">
                {
                    "@context": "https://schema.org",
                    "@type": "BreadcrumbList",
                    "itemListElement": [
                        {
                            "@type": "ListItem",
                            "position": 1,
                            "name": "Home",
                            "item": "<?php echo e(route('home')); ?>"
                        },
                        {
                            "@type": "ListItem",
                            "position": 2,
                            "name": "<?php echo e($article->conference->country->name); ?>",
                            "item": "<?php echo e(route('country.show', $article->conference->country)); ?>"
                        },
                        {
                            "@type": "ListItem",
                            "position": 3,
                            "name": "<?php echo e($article->conference->title); ?>",
                            "item": "<?php echo e(route('conference.show', $article->conference)); ?>"
                        },
                        {
                            "@type": "ListItem",
                            "position": 4,
                            "name": "<?php echo e($article->title); ?>"
                        }
                    ]
                }
                </script>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>
    <!-- Breadcrumb -->
    <section class="breadcrumb-section">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" itemscope itemtype="https://schema.org/BreadcrumbList">
                    <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="<?php echo e(route('home')); ?>" itemprop="item">
                            <span itemprop="name"><i class="bi bi-house me-1"></i>Home</span>
                        </a>
                        <meta itemprop="position" content="1" />
                    </li>
                    <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="<?php echo e(route('country.show', $article->conference->country)); ?>" itemprop="item">
                            <span itemprop="name"><?php echo e($article->conference->country->name); ?></span>
                        </a>
                        <meta itemprop="position" content="2" />
                    </li>
                    <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="<?php echo e(route('conference.show', $article->conference)); ?>" itemprop="item">
                            <span itemprop="name"><?php echo e(Str::limit($article->conference->title, 25)); ?></span>
                        </a>
                        <meta itemprop="position" content="3" />
                    </li>
                    <li class="breadcrumb-item active" itemprop="itemListElement" itemscope
                        itemtype="https://schema.org/ListItem">
                        <span itemprop="name"><?php echo e(Str::limit($article->title, 30)); ?></span>
                        <meta itemprop="position" content="4" />
                    </li>
                </ol>
            </nav>
        </div>
    </section>

    <div class="container py-5">
        <div class="row">
            <!-- Article Content (Main Column) -->
            <div class="col-lg-8">
                <article itemscope itemtype="https://schema.org/ScholarlyArticle">
                    <div class="card mb-4">
                        <div class="card-body p-4">
                            <!-- Article Type Badge -->
                            <div class="mb-3">
                                <span class="badge bg-primary px-3 py-2">
                                    <i class="bi bi-file-earmark-text me-1"></i>Research Article
                                </span>
                                <?php if($article->status === 'published'): ?>
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="bi bi-check-circle me-1"></i>Published
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Article Title (H1 is CRITICAL for Google Scholar) -->
                            <h1 class="mb-4" itemprop="headline"
                                style="font-family: 'Roboto Slab', serif; color: var(--primary-dark); font-size: 1.75rem; line-height: 1.4;">
                                <?php echo e($article->title); ?>

                            </h1>

                            <!-- Author Information (MUST be visible) -->
                            <div class="author-section mb-4 p-3"
                                style="background: #f8f9fa; border-radius: 8px; border-left: 4px solid var(--primary-blue);">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="author-avatar me-3"
                                        style="width: 50px; height: 50px; border-radius: 50%; background: var(--gradient-blue); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.3rem;">
                                        <?php echo e(strtoupper(substr($article->author_display_name, 0, 1))); ?>

                                    </div>
                                    <div>
                                        <h4 class="mb-0 fs-5" itemprop="author" itemscope
                                            itemtype="https://schema.org/Person">
                                            <span
                                                itemprop="name"><?php echo e($article->author_name ?? $article->author_display_name); ?></span>
                                        </h4>
                                        <?php if($article->author_affiliation): ?>
                                            <p class="text-muted mb-0 small" itemprop="affiliation">
                                                <i class="bi bi-building me-1"></i><?php echo e($article->author_affiliation); ?>

                                            </p>
                                        <?php endif; ?>
                                        <?php if($article->author && $article->author->email): ?>
                                            <p class="text-muted mb-0 small">
                                                <i class="bi bi-envelope me-1"></i>
                                                <a href="mailto:<?php echo e($article->author->email); ?>" itemprop="email"
                                                    class="text-decoration-none">
                                                    <?php echo e($article->author->email); ?>

                                                </a>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Publication Metadata (MUST be visible) -->
                            <div class="d-flex flex-wrap gap-3 mb-4">
                                <?php if($article->published_at): ?>
                                    <span class="badge bg-light text-dark border px-3 py-2">
                                        <i class="bi bi-calendar-fill me-1 text-success"></i>
                                        <time datetime="<?php echo e($article->published_at->toIso8601String()); ?>"
                                            itemprop="datePublished">
                                            <?php echo e($article->published_at->format('F d, Y')); ?>

                                        </time>
                                    </span>
                                <?php endif; ?>
                                <span class="badge bg-light text-dark border px-3 py-2">
                                    <i class="bi bi-file-earmark-fill me-1 text-warning"></i>
                                    Pages: <span itemprop="pagination"><?php echo e($article->page_range); ?></span>
                                    (<?php echo e($article->page_count); ?> pages)
                                </span>
                                <span class="badge bg-light text-dark border px-3 py-2">
                                    <i class="bi bi-globe me-1 text-info"></i>
                                    <?php echo e($article->conference->country->name_en ?? $article->conference->country->name); ?>

                                </span>
                            </div>

                            <!-- Abstract Section (CRITICAL - Must be plain text, NOT image) -->
                            <?php if($article->abstract): ?>
                                <section class="abstract-section mb-4" id="abstract">
                                    <div class="p-4"
                                        style="background: var(--light-blue); border-radius: 10px; border-left: 4px solid var(--primary-blue);">
                                        <h2 class="h5 fw-bold mb-3" style="color: var(--primary-dark);">
                                            <i class="bi bi-text-paragraph me-2"></i>Abstract
                                        </h2>
                                        <p class="mb-0" itemprop="abstract" style="line-height: 1.8; text-align: justify;">
                                            <?php echo e($article->abstract); ?>

                                        </p>
                                    </div>
                                </section>
                            <?php endif; ?>

                            <!-- Keywords Section -->
                            <?php if($article->keywords): ?>
                                <section class="keywords-section mb-4" id="keywords">
                                    <h3 class="h6 fw-bold mb-2" style="color: var(--primary-dark);">
                                        <i class="bi bi-tags me-2"></i>Keywords
                                    </h3>
                                    <div itemprop="keywords">
                                        <?php $__currentLoopData = explode(',', $article->keywords); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $keyword): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="badge bg-light text-dark border me-1 mb-1"><?php echo e(trim($keyword)); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </section>
                            <?php endif; ?>

                            <!-- PDF Viewer Section -->
                            <section class="pdf-section mb-4" id="pdf-viewer">
                                <h3 class="h5 fw-bold mb-3" style="color: var(--primary-dark);">
                                    <i class="bi bi-file-pdf me-2 text-danger"></i>Full Article
                                </h3>
                                <div class="ratio"
                                    style="--bs-aspect-ratio: 130%; border-radius: 10px; overflow: hidden; box-shadow: var(--shadow-md);">
                                    <iframe src="<?php echo e(Storage::url($article->formatted_pdf_path ?? $article->pdf_path)); ?>"
                                        frameborder="0" style="background: #f5f5f5;"
                                        title="Article PDF: <?php echo e($article->title); ?>" loading="lazy">
                                    </iframe>
                                </div>
                            </section>

                            <!-- Download Buttons -->
                            <section class="download-section">
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="<?php echo e(Storage::url($article->formatted_pdf_path ?? $article->pdf_path)); ?>"
                                        class="btn btn-primary btn-lg" target="_blank" itemprop="url" rel="noopener"
                                        download="<?php echo e($article->page_range ? $article->page_range . '.pdf' : 'article.pdf'); ?>">
                                        <i class="bi bi-download me-2"></i>Download PDF
                                    </a>
                                    <button type="button" class="btn btn-info btn-lg" onclick="copyArticleLink()"
                                        id="copyLinkBtn">
                                        <i class="bi bi-link-45deg me-2"></i>Maqola havolasi
                                    </button>

                                </div>
                            </section>
                        </div>
                    </div>

                    <!-- Citation Section -->
                    <div class="card mb-4">
                        <div class="card-header" style="background: var(--light-blue);">
                            <h3 class="h6 mb-0"><i class="bi bi-quote me-2"></i>How to Cite</h3>
                        </div>
                        <div class="card-body">
                            <div class="citation-box p-3"
                                style="background: #f8f9fa; border-radius: 8px; font-family: 'Courier New', monospace; font-size: 0.9rem;">
                                <?php echo e($article->author_name ?? $article->author_display_name); ?>

                                (<?php echo e($article->published_at ? $article->published_at->format('Y') : date('Y')); ?>).
                                <?php echo e($article->title); ?>.
                                <em><?php echo e($article->conference->country->conference_name ?? $article->conference->title); ?></em>,
                                <?php echo e($article->page_range); ?>.
                                <?php echo e($article->conference->country->name_en ?? $article->conference->country->name); ?>.
                                <?php if($article->doi): ?>
                                    https://doi.org/<?php echo e($article->doi); ?>

                                <?php endif; ?>
                            </div>
                            <div class="mt-3">
                                <button class="btn btn-sm btn-outline-secondary" onclick="copyCitation()">
                                    <i class="bi bi-clipboard me-1"></i>Copy Citation
                                </button>
                            </div>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Conference Info -->
                <div class="card mb-4">
                    <div class="card-header" style="background: var(--gradient-blue); color: white;">
                        <i class="bi bi-journal-bookmark me-1"></i>Conference
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <?php if($article->conference->country->flag_url): ?>
                                <img src="<?php echo e(Storage::url($article->conference->country->flag_url)); ?>"
                                    alt="<?php echo e($article->conference->country->name); ?>"
                                    style="width: 50px; height: 34px; object-fit: cover; border-radius: 4px; margin-right: 12px;">
                            <?php endif; ?>
                            <div>
                                <h6 class="mb-1">
                                    <a href="<?php echo e(route('conference.show', $article->conference)); ?>"
                                        class="text-decoration-none" style="color: var(--primary-blue);">
                                        <?php echo e($article->conference->title); ?>

                                    </a>
                                </h6>
                                <p class="text-muted small mb-0">
                                    <i class="bi bi-geo-alt me-1"></i><?php echo e($article->conference->country->name); ?>

                                </p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between text-muted small">
                            <span><i
                                    class="bi bi-calendar me-1"></i><?php echo e($article->conference->conference_date->format('F d, Y')); ?></span>
                            <span><i class="bi bi-file-text me-1"></i><?php echo e($article->conference->articles->count()); ?>

                                articles</span>
                        </div>
                    </div>
                </div>

                <!-- Article Metrics -->
                <div class="card mb-4">
                    <div class="card-header"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <i class="bi bi-graph-up me-1"></i>Article Info
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Article Number</span>
                                <strong><?php echo e($article->order_number); ?></strong>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Pages</span>
                                <strong><?php echo e($article->page_range); ?></strong>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Published</span>
                                <strong><?php echo e($article->published_at ? $article->published_at->format('M d, Y') : 'Pending'); ?></strong>
                            </li>
                            <li class="d-flex justify-content-between py-2">
                                <span class="text-muted">Language</span>
                                <strong>English</strong>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Author Card -->
                <div class="card mb-4">
                    <div class="card-header"
                        style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                        <i class="bi bi-person me-1"></i>Author
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="d-flex align-items-center justify-content-center me-3"
                                style="width: 50px; height: 50px; border-radius: 50%; background: var(--gradient-blue); color: white; font-weight: 700; font-size: 1.2rem;">
                                <?php echo e(strtoupper(substr($article->author_display_name, 0, 1))); ?>

                            </div>
                            <div>
                                <h6 class="mb-0"><?php echo e($article->author_display_name); ?></h6>
                                <?php if($article->author_affiliation): ?>
                                    <small class="text-muted"><?php echo e($article->author_affiliation); ?></small>
                                <?php elseif($article->author): ?>
                                    <small class="text-muted"><?php echo e($article->author->email); ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Share Buttons -->
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-3"><i class="bi bi-share me-2"></i>Share This Article</h6>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="https://t.me/share/url?url=<?php echo e(urlencode(request()->url())); ?>&text=<?php echo e(urlencode($article->title)); ?>"
                                class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">
                                <i class="bi bi-telegram"></i>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo e(urlencode(request()->url())); ?>"
                                class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo e(urlencode(request()->url())); ?>&text=<?php echo e(urlencode($article->title)); ?>"
                                class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">
                                <i class="bi bi-twitter-x"></i>
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo e(urlencode(request()->url())); ?>"
                                class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">
                                <i class="bi bi-linkedin"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-secondary"
                                onclick="navigator.clipboard.writeText('<?php echo e(request()->url()); ?>'); alert('Link copied!');">
                                <i class="bi bi-link-45deg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        function copyCitation() {
            const citation = `<?php echo e($article->author_name ?? $article->author_display_name); ?> (<?php echo e($article->published_at ? $article->published_at->format('Y') : date('Y')); ?>). <?php echo e($article->title); ?>. <?php echo e($article->conference->country->conference_name ?? $article->conference->title); ?>, <?php echo e($article->page_range); ?>. <?php echo e($article->conference->country->name_en ?? $article->conference->country->name); ?>.`;
            navigator.clipboard.writeText(citation).then(() => {
                alert('Citation copied to clipboard!');
            });
        }

        function copyArticleLink() {
            const articleUrl = '<?php echo e(route('article.show', $article)); ?>';
            navigator.clipboard.writeText(articleUrl).then(() => {
                const btn = document.getElementById('copyLinkBtn');
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check-lg me-2"></i>Nusxalandi!';
                btn.classList.remove('btn-info');
                btn.classList.add('btn-success');

                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-info');
                }, 2000);
            }).catch(err => {
                // Agar clipboard ishlamasa, prompt yordamida ko'rsatamiz
                prompt('Maqola havolasi:', articleUrl);
            });
        }
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\artiqle\resources\views/public/articles/show.blade.php ENDPATH**/ ?>