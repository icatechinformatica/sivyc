<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$res = DB::select("SELECT asistencia_tecnica FROM tbl_instituto WHERE id = 1 LIMIT 1");
$json = json_decode($res[0]->asistencia_tecnica, true);
print_r($json['E2026']['MARZO'] ?? 'No MARZO');
print_r("\n");
print_r($json['E2026']['ABRIL'] ?? 'No ABRIL');
