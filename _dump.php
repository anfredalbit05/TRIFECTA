<?php
$f = __DIR__ . '/resources/views/livewire/admin-pages/report-settings/general.blade.php';
$lines = file($f);
foreach (range(176, 196) as $n) {
    echo $n . ': ' . json_encode($lines[$n - 1] ?? '', JSON_UNESCAPED_UNICODE) . PHP_EOL;
}
