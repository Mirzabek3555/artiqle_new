<?php

// Load Laravel bootstrap to use services
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\DocxProcessorService;

$processor = new DocxProcessorService();
$docxPath = 'storage/app/public/articles/docx/5ZZ7CIRNyfy7gZYJuO5Wh4qR7Lf079jOJhFbiMfg.docx';

$result = $processor->processDocx($docxPath);
$html = $result['html'];

// Find all <table> tags in the processed HTML
if (preg_match_all('/<table[^>]*>/is', $html, $matches)) {
    echo "Found " . count($matches[0]) . " tables in processed HTML.\n";
    foreach ($matches[0] as $i => $tbl) {
        echo "Table $i HTML tag: " . htmlspecialchars($tbl) . "\n";
    }
} else {
    echo "No tables found in HTML.\n";
}

// Let's also see if the word "borderless-table" is in the HTML
$count = substr_count($html, 'borderless-table');
echo "Number of 'borderless-table' classes found: $count\n";

// Let's print a small snippet of the HTML around a table to see if it has borders
if (preg_match('/<table[^>]*>.*?<\/table>/is', $html, $m)) {
    echo "First table HTML snippet:\n" . htmlspecialchars(substr($m[0], 0, 500)) . "\n";
}
