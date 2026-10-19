<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WebsiteTransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_and_import_preserve_accounts_settings_and_files(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $path = Storage::disk('local')->path('deployment/test.json');
        $user = User::factory()->create(['role' => 'admin']);
        $hash = $user->getRawOriginal('password');
        SiteSetting::putMany(['company_name' => 'Test company']);
        Storage::disk('public')->put('media/2026/test.png', 'test image');

        try {
            $this->artisan('website:transfer', ['action' => 'export', '--file' => $path])->assertSuccessful();
            $this->artisan('website:transfer', ['action' => 'import', '--file' => $path])->assertFailed();
            DB::table('site_settings')->delete();
            DB::table('users')->delete();
            Storage::disk('public')->delete('media/2026/test.png');
            $this->artisan('website:transfer', ['action' => 'import', '--file' => $path])->assertSuccessful();
            $this->assertSame($hash, User::findOrFail($user->id)->getRawOriginal('password'));
            $this->assertSame('Test company', SiteSetting::values()['company_name']);
            $this->assertSame('test image', Storage::disk('public')->get('media/2026/test.png'));
        } finally {
            File::delete($path);
        }
    }
}
