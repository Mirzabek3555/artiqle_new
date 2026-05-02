<?php
$countCountry = 0;
foreach(App\Models\Country::all() as $c) { 
    if ($c->conference_name && strpos($c->conference_name, ',') !== false) { 
        $c->update(['conference_name' => str_replace(',', '', $c->conference_name)]); 
        $countCountry++;
    } 
} 
$countConf = 0;
foreach(App\Models\Conference::all() as $c) { 
    if ($c->title && strpos($c->title, ',') !== false) { 
        $c->update(['title' => str_replace(',', '', $c->title)]); 
        $countConf++;
    } 
} 
echo "Barcha davlatlar ({$countCountry}) va konferensiyalardan ({$countConf}) vergullar olib tashlandi.\n";
