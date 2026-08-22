<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== STORAGE URL GENERATION ===\n";
$img = DB::table('room_images')->first();
if ($img) {
    $url = \Storage::disk('public')->url($img->path);
    echo "Path: {$img->path}\n";
    echo "URL: {$url}\n";
    echo "Public file exists: " . (file_exists(public_path('storage/' . $img->path)) ? 'YES' : 'NO') . "\n";
}

echo "\n=== GALLERY URL ===\n";
$g = DB::table('galleries')->first();
if ($g) {
    $url = \Storage::disk('public')->url($g->path);
    echo "Path: {$g->path}\n";
    echo "URL: {$url}\n";
    echo "Public file exists: " . (file_exists(public_path('storage/' . $g->path)) ? 'YES' : 'NO') . "\n";
}

echo "\n=== APP URL ===\n";
echo "APP_URL: " . config('app.url') . "\n";
echo "Filesystem disk public url: " . config('filesystems.disks.public.url') . "\n";

echo "\n=== ROOM TYPE IMAGES (via model) ===\n";
$types = App\Models\RoomType::with('images')->get();
foreach ($types as $t) {
    echo "\nRoom: {$t->name}\n";
    foreach ($t->images as $img) {
        echo "  - path: {$img->path} | url: {$img->url}\n";
    }
    if ($t->images->isEmpty()) echo "  - NO IMAGES\n";
}
