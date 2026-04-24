<?php
$php    = PHP_BINARY;
$artisan = __DIR__ . DIRECTORY_SEPARATOR . 'artisan';

echo "PHP: {$php}" . PHP_EOL;
echo "Artisan: {$artisan}" . PHP_EOL;

$cmd = "cmd /c start /B \"\" \"{$php}\" \"{$artisan}\" queue:work --stop-when-empty >NUL 2>&1";
echo "Command: {$cmd}" . PHP_EOL;

pclose(popen($cmd, 'r'));
echo "popen fired — waiting 3s to check if worker picks up jobs..." . PHP_EOL;
sleep(3);
echo "Done." . PHP_EOL;
