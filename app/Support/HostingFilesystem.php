<?php

namespace App\Support;

use Illuminate\Filesystem\LocalFilesystemAdapter;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter as LocalAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use League\MimeTypeDetection\ExtensionMimeTypeDetector;

class HostingFilesystem
{
    public static function make(array $config): LocalFilesystemAdapter
    {
        $visibility = PortableVisibilityConverter::fromArray(
            $config['permissions'] ?? [],
            $config['directory_visibility'] ?? $config['visibility'] ?? 'private',
        );
        $adapter = new LocalAdapter(
            $config['root'],
            $visibility,
            $config['lock'] ?? LOCK_EX,
            ($config['links'] ?? null) === 'skip' ? LocalAdapter::SKIP_LINKS : LocalAdapter::DISALLOW_LINKS,
            new ExtensionMimeTypeDetector(),
        );

        return (new LocalFilesystemAdapter(new Filesystem($adapter, $config), $adapter, $config))
            ->shouldServeSignedUrls($config['serve'] ?? false, fn () => app('url'));
    }
}
