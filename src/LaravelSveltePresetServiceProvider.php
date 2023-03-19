<?php

namespace Garata\LaravelSveltePreset;

use Illuminate\Support\ServiceProvider;
use Laravel\Ui\UiCommand;
use Illuminate\Contracts\Http\Kernel;
use App\Http\Middleware;

use App\Http\Middleware\HandleInertiaRequests;

class LaravelSveltePresetServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(Kernel $kernel)
    {
        UiCommand::macro('svelte', function ($command) {
            SveltePreset::install($command);
			(fn() => $this->components->info('Svelte scaffolding installed successfully.'))->call($command);
			(fn() => $this->components->warn('Please update "web.php" adding "ziggy-js" and "inertiajs" routes.'))->call($command);
			(fn() => $this->components->warn('Please run [npm install && npm run dev] to compile your fresh scaffolding.'))->call($command);
        });
    }
}