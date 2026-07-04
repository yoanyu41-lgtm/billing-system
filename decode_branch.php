<?php
$data = "H4sIAAAAAAAAAwXB6w6AEBgA0DeikPnaWr96DqMLSi6lmbfvHFtKekeMqwvmjmFvSKWEvAsXbkvNpvvyyfQspX5UWK08fKzSbVM%2FAFAAQohgwAVwyn6ewwcUSwAAAA%3D%3D";
$decoded = base64_decode($data);
$unzipped = @gzdecode($decoded);
echo "UNZIPPED: " . $unzipped . "\n";

$referrer2 = "H4sIAAAAAAAAA8soKSkottLXL8%2FMS8%2FNz0ut1EssKNDLyczL1q90LS9MNygtzDJJAgBsdaWxJgAAAA%3D%3D";
$decoded2 = base64_decode(urldecode($referrer2));
$unzipped2 = gzdecode($decoded2);
echo "REFERRER 2: " . $unzipped2 . "\n";
