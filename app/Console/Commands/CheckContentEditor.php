<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\ContentController;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class CheckContentEditor extends Command
{
    protected $signature = 'website:check-editor {section=trang-chu}';

    protected $description = 'Read-only hosting checks and rendering diagnosis for the content editor';

    public function handle(): int
    {
        $section = $this->argument('section');
        if (!in_array($section, ['trang-chu', 'gioi-thieu', 'doi-ngu', 'tuyen-dung', 'tin-tuc', 'lien-he', 'mau-sac', 'thuong-hieu'], true)) {
            $this->error('Tên trang không hợp lệ.');
            return self::FAILURE;
        }
        $this->line('PHP: '.PHP_VERSION.' | Laravel: '.app()->version());
        $this->line('Config cache: '.(app()->configurationIsCached() ? 'ON' : 'OFF'));
        $this->line('Public path: '.public_path());
        $missing = false;
        foreach (['public/css/visual-editor.css', 'public/js/visual-editor.js', 'config/visual-editor.php', 'resources/views/admin/content/visual-editor.blade.php', 'app/Support/VisualEditor.php'] as $file) {
            $path = str_starts_with($file, 'public/') ? public_path(substr($file, 7)) : base_path($file);
            $exists = is_readable($path);
            $this->line(($exists ? '[OK] ' : '[MISSING] ').$file);
            $missing = $missing || !$exists;
        }
        $this->line('Page content config: '.(config('page-content.trang-chu') ? 'OK' : 'MISSING'));
        $this->line('Visual editor config: '.(config('visual-editor.trang-chu') ? 'OK' : 'MISSING'));
        $this->line('View cache writable: '.(is_writable(storage_path('framework/views')) ? 'YES' : 'NO'));
        try {
            $this->line('Database: '.\Illuminate\Support\Facades\DB::getDriverName());
            $valueType = \Illuminate\Support\Facades\Schema::getColumnType('site_settings', 'value');
            $this->line('site_settings.value: '.$valueType);
            if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql' && $valueType === 'text') {
                $this->warn('Cột TEXT có thể không đủ cho bản nháp/lịch sử. Chạy php artisan migrate --force để cập nhật lên LONGTEXT.');
            }
            // An ephemeral administrator and in-memory session; no account or website data is changed.
            config(['session.driver' => 'array']);
            $session = app('session')->driver('array');
            app()->instance('session.store', $session);
            $request = Request::create('/admin/noi-dung/'.$section);
            $route = app('router')->getRoutes()->match($request);
            $request->setRouteResolver(fn () => $route);
            $request->setLaravelSession($session);
            app()->instance('request', $request);
            app('url')->setRequest($request);
            $user = new User(['name' => 'Editor diagnostic', 'email' => 'diagnostic@example.test', 'role' => 'admin', 'is_active' => true]);
            auth()->setUser($user);
            view()->share('errors', new \Illuminate\Support\ViewErrorBag());
            app(ContentController::class)->edit($section)->render();
            $this->info('Editor render: OK (không thay đổi nội dung website)');
            return $missing ? self::FAILURE : self::SUCCESS;
        } catch (\Throwable $exception) {
            $this->error('Editor render: FAILED');
            $cause = $exception;
            while ($cause->getPrevious()) $cause = $cause->getPrevious();
            $this->line(get_class($cause).': '.$cause->getMessage());
            $this->line('File: '.$cause->getFile().':'.$cause->getLine());
            return self::FAILURE;
        }
    }
}
