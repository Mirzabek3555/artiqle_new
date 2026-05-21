<?php
$img = imagecreatefromstring(file_get_contents('public/images/certificates/assets/template.jpg'));
$w = imagesx($img);
$h = imagesy($img);

$x_start = 300;
$x_end = 1000;
$y_start = 1400;
$y_end = 1700;

// Find the bounding box of the signature (black-ish pixels)
$sig_min_x = 9999;
$sig_max_x = 0;
$sig_min_y = 9999;
$sig_max_y = 0;

for ($y = $y_start; $y < $y_end; $y++) {
    for ($x = $x_start; $x < $x_end; $x++) {
        $rgb = imagecolorat($img, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        
        // If it's dark (signature ink is black)
        if ($r < 150 && $g < 150 && $b < 150) {
            if ($x < $sig_min_x) $sig_min_x = $x;
            if ($x > $sig_max_x) $sig_max_x = $x;
            if ($y < $sig_min_y) $sig_min_y = $y;
            if ($y > $sig_max_y) $sig_max_y = $y;
        }
    }
}

echo "Signature bounds (dark pixels): X: $sig_min_x to $sig_max_x, Y: $sig_min_y to $sig_max_y\n";

// Find Google Scholar (blue/yellow/red pixels or dark pixels starting further right)
$gs_min_x = 9999;
for ($x = $sig_max_x; $x < 1200; $x++) {
    for ($y = 1450; $y < 1650; $y++) {
        $rgb = imagecolorat($img, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        if ($r < 240 || $g < 240 || $b < 240) { // non-white
            if ($x < $gs_min_x) $gs_min_x = $x;
        }
    }
}
echo "Google scholar starts at X: $gs_min_x\n";
