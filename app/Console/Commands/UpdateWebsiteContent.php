<?php

namespace App\Console\Commands;

use App\Models\SiteSetting;
use App\Support\PageContent;
use App\Support\TeamProducts;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UpdateWebsiteContent extends Command
{
    protected $signature = 'website:update-content {--overwrite : Replace supplied text content; preserve selected images and existing products}';

    protected $description = 'Install website content and product artwork, with a backup before replacing text';

    public function handle(): int
    {
        $defaults = PageContent::defaults();
        $imageKeys = [];
        foreach (config('page-content', []) as $fields) {
            foreach ($fields as $key => $field) {
                if ($field[1] === 'image') {
                    $imageKeys[] = $key;
                }
            }
        }
        $productKey = TeamProducts::PREFIX.'cardiovascular-solutions-overview';
        $imagePath = 'media/2026/products/cardiovascular-solutions.png';
        $source = public_path('images/cardiovascular-solutions.png');
        if (! is_file($source)) {
            $this->error('Missing bundled product image: public/images/cardiovascular-solutions.png');

            return self::FAILURE;
        }

        try {
            $saved = SiteSetting::values();
            $backup = 'deployment/backups/content-'.now()->format('Ymd-His').'-'.Str::random(8).'.json';
            $snapshot = [
                'created_at' => now()->toIso8601String(),
                'existing' => array_intersect_key($saved, array_flip([...array_keys($defaults), $productKey])),
                'previously_missing' => array_values(array_diff([...array_keys($defaults), $productKey], array_keys($saved))),
            ];
            if (! Storage::disk('local')->put($backup, json_encode($snapshot, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR))) {
                throw new \RuntimeException('Could not save content backup; no database changes were made.');
            }
            $this->line('Backup: '.Storage::disk('local')->path($backup));

            $disk = Storage::disk('public');
            if (! $disk->exists($imagePath) && ! $disk->put($imagePath, file_get_contents($source))) {
                throw new \RuntimeException('Could not install product image; no database changes were made.');
            }
            $product = [
                'name' => 'Giải pháp can thiệp tim mạch',
                'name_en' => 'Cardiovascular intervention solutions',
                'description' => 'Stent nong mạch vành · Bóng nong mạch vành · Dụng cụ can thiệp tim mạch',
                'description_en' => 'Coronary stents · Coronary balloon catheters · Cardiovascular intervention devices',
                'image_path' => $imagePath,
                'sort_order' => 0,
                'is_published' => 1,
            ];
            DB::transaction(function () use ($defaults, $imageKeys, $productKey, $product): void {
                foreach ($defaults as $key => $value) {
                    if ($this->option('overwrite') && ! in_array($key, $imageKeys, true)) {
                        SiteSetting::putMany([$key => $value]);
                    } else {
                        SiteSetting::firstOrCreate(['key' => $key], ['value' => $value]);
                    }
                }
                SiteSetting::firstOrCreate(['key' => $productKey], [
                    'value' => json_encode($product, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                ]);
            });
            $this->info($this->option('overwrite') ? 'Updated supplied text content. Image selections and existing products were preserved.' : 'Installed missing content. Existing content was preserved.');

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}
