<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Article;
use App\Models\Conference;

class RecalculatePageRanges extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'articles:recalculate-page-ranges
                            {--conference= : Faqat bitta konferensiya ID si uchun qayta hisoblash}
                            {--dry-run : Haqiqatda saqlamay, faqat ko\'rsatish}';

    /**
     * The console command description.
     */
    protected $description = 'Barcha maqolalar page_range ni konferensiya ketma-ketligiga asoslanib qayta hisoblash';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $conferenceId = $this->option('conference');

        $query = Conference::withCount('articles')->with(['articles' => function ($q) {
            $q->orderBy('order_number')->orderBy('id');
        }]);

        if ($conferenceId) {
            $query->where('id', $conferenceId);
        }

        $conferences = $query->get();

        if ($conferences->isEmpty()) {
            $this->warn('Konferensiya topilmadi.');
            return 0;
        }

        $totalUpdated = 0;

        foreach ($conferences as $conference) {
            $articles = $conference->articles;

            if ($articles->isEmpty()) {
                continue;
            }

            $this->line('');
            $this->info("📋 Konferensiya #{$conference->id}: {$conference->title} ({$conference->month_year})");
            $this->line("   Maqolalar soni: {$articles->count()}");

            $cumulativePage = 1;

            foreach ($articles as $article) {
                $pageCount = max(1, (int) $article->page_count);
                $startPage  = $cumulativePage;
                $endPage    = $cumulativePage + $pageCount - 1;
                $newRange   = $startPage . '-' . $endPage;
                $oldRange   = $article->page_range;

                $changed = ($oldRange !== $newRange);

                $marker = $changed ? '✏️ ' : '   ';
                $this->line(sprintf(
                    "   %s[%d] %-50s | page_count=%d | eski: %-8s → yeni: %s",
                    $marker,
                    $article->id,
                    mb_strimwidth($article->title ?? '', 0, 50, '…'),
                    $pageCount,
                    $oldRange ?? '(null)',
                    $newRange
                ));

                if ($changed && !$isDryRun) {
                    $article->updateQuietly(['page_range' => $newRange]);
                    $totalUpdated++;
                }

                $cumulativePage += $pageCount;
            }
        }

        $this->line('');

        if ($isDryRun) {
            $this->warn('⚠️  Dry-run rejimi: hech narsa saqlanmadi. Haqiqiy yangilash uchun --dry-run bayrog\'ini olib tashlang.');
        } else {
            $this->info("✅ Jami {$totalUpdated} ta maqola page_range yangilandi.");
        }

        return 0;
    }
}
