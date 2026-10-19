<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ImportWebsiteImages extends Command
{
    protected $signature = 'media:import-website';

    protected $description = 'Copy bundled website images into the media library, grouped by file year';

    public function handle(): int
    {
        $disk = Storage::disk('public');
        $count = 0;

        if (! File::isDirectory(public_path('images'))) {
            $this->info('No website images to import.');

            return self::SUCCESS;
        }

        foreach (File::allFiles(public_path('images')) as $file) {
            if (! preg_match('/\.(jpe?g|png|webp|gif)$/i', $file->getFilename())) {
                continue;
            }

            $contents = File::get($file->getPathname());
            $year = \Carbon\Carbon::createFromTimestamp($file->getMTime(), 'Asia/Bangkok')->format('Y');
            $path = 'media/'.$year.'/website/'.pathinfo($file->getFilename(), PATHINFO_FILENAME)
                .'-'.substr(hash('sha256', $contents), 0, 12).'.'.strtolower($file->getExtension());

            if (! $disk->exists($path)) {
                if (! $disk->put($path, $contents)) {
                    $this->error('Could not import '.$file->getFilename());

                    return self::FAILURE;
                }
                $count++;
            }
        }

        $this->info('Imported '.$count.' website images into the media library.');

        return self::SUCCESS;
    }
}
