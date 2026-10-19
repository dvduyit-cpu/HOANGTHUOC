<?php

namespace Tests\Feature;

use App\Support\HostingFilesystem;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class HostingFilesystemTest extends TestCase
{
    public function test_storage_fallback_can_read_write_and_delete_without_content_mime_detection(): void
    {
        $root = storage_path('framework/testing/hosting-'.uniqid());
        try {
            $disk = HostingFilesystem::make(['root' => $root, 'visibility' => 'public', 'url' => 'https://example.com/storage']);
            $this->assertTrue($disk->put('media/test.png', 'image bytes'));
            $this->assertSame('image bytes', $disk->get('media/test.png'));
            $this->assertSame('image/png', $disk->mimeType('media/test.png'));
            $this->assertSame('https://example.com/storage/media/test.png', $disk->url('media/test.png'));
            $this->assertTrue($disk->delete('media/test.png'));
        } finally {
            File::deleteDirectory($root);
        }
    }
}
