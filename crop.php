<?php
$img = imagecreatefromstring(file_get_contents('public/images/certificates/assets/template.jpg'));
$crop = imagecrop($img, ['x' => 50, 'y' => 1400, 'width' => 600, 'height' => 320]);
@mkdir('public/images/tmp', 0777, true);
imagejpeg($crop, 'public/images/tmp/crop.jpg');
echo "Done\n";
