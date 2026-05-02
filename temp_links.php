<?php
foreach(App\Models\Article::all() as $a) { 
    if($a->conference) { 
        $a->generateLink(); 
    } 
} 
echo 'Barcha maqolalar havolalari yangilandi.';
