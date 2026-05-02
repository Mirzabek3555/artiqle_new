<?php
use App\Models\Article;
use App\Models\Country;

// Fetch all countries
$countries = Country::all();

foreach ($countries as $country) {
    // Get all articles for this country, ordered by creation date
    $articles = Article::whereHas('conference', function($query) use ($country) {
        $query->where('country_id', $country->id);
    })->orderBy('created_at', 'asc')->get();

    $number = 1;
    foreach ($articles as $article) {
        $article->update(['country_article_number' => $number]);
        $number++;
    }
    echo "Updated " . count($articles) . " articles for country: " . $country->code . "\n";
}
echo "Backfill complete.\n";
