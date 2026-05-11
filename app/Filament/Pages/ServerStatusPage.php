<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

class ServerStatusPage extends Page
{
    protected static ?string $slug = 'server-status';

    protected static ?string $navigationLabel = 'Server status';

    protected static ?int $navigationSort = 60;

    protected static string|\UnitEnum|null $navigationGroup = 'site';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedServer;

    protected string $view = 'filament.pages.server-status';

    /** @var list<string> */
    public array $warnings = [];

    /** @var array<string, mixed> */
    public array $status = [];

    public function mount(): void
    {
        $this->warnings = [];
        $dbOk = false;

        try {
            DB::connection()->getPdo();
            $dbOk = true;
        } catch (\Throwable $e) {
            $this->warnings[] = 'Database connection failed: '.$e->getMessage();
        }

        $storageRoot = storage_path();
        $free = disk_free_space($storageRoot);

        $this->status = [
            'phpVersion' => PHP_VERSION,
            'laravelVersion' => app()->version(),
            'environment' => config('app.env'),
            'debug' => (bool) config('app.debug'),
            'timezone' => config('app.timezone'),
            'cacheDriver' => config('cache.default'),
            'sessionDriver' => config('session.driver'),
            'queueDriver' => config('queue.default'),
            'mailer' => config('mail.default'),
            'dbConnection' => config('database.default'),
            'dbOk' => $dbOk,
            'disk' => config('filesystems.default'),
            'storageWritable' => is_writable($storageRoot),
            'publicWritable' => is_writable(public_path()),
            'freeDiskHuman' => $free === false ? 'n/a' : number_format($free / 1024 / 1024 / 1024, 2).' GB',
        ];
    }
}
