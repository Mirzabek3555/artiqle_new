<?php
$countries = App\Models\Country::pluck('conference_name', 'code')->toArray();
echo "Countries:\n";
print_r($countries);

$conferences = App\Models\Conference::pluck('title', 'id')->toArray();
echo "\nConferences:\n";
print_r($conferences);
