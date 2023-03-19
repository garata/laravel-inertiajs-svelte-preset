<?php

namespace Garata\LaravelSveltePreset;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Laravel\Ui\Presets\Preset;

class SveltePreset extends Preset
{
	static protected $command;
	
    public static function install($command)
    {
		static::$command = $command;
		
        static::ensureComponentDirectoryExists();
        static::updatePackages();
        static::updateMix();
        static::setupBabelConfig();
		static::instalNpmPackages();
		static::updateMiddlewares();
        static::updateScripts();
		static::setupLaravelMix();
        static::removeNodeModules();
    }

	/**
	 * Check if user has node installed
	 * 
	 * @return boolean
	 */
	public static function hasNode()
	{
		$node = shell_exec('node -v');

		return preg_match('/^v(\d+\.)?(\d+\.)?(\*|\d+)$/', $node);
	}

	/**
	 * Check if user has npm (node package manager) installed
	 * 
	 * @return boolean
	 */
	public static function hasNpm()
	{
		$npm = shell_exec('npm -v');

		return preg_match('/^(\d+\.)?(\d+\.)?(\*|\d+)$/', $npm);
	}

	public static function instalNpmPackages()
	{
		// If user has both node and npm installed continue
		if(!self::hasNode() || !self::hasNpm())
		{
			static::$command->error('It appears that either node or npm is not installed. Please install those dependencies and try again!');
			exit();
		}
		
		shell_exec('npm install @inertiajs/inertia @inertiajs/inertia-svelte');
		shell_exec('npm install @babel/plugin-syntax-dynamic-import');
		shell_exec('npm install --save svelte svelte-loader');
		shell_exec('npm install ziggy-js');
	}
	
    public static function updatePackageArray($packages)
    {
        return array_merge(
            [
                'svelte' => '^3.1.0',
                'laravel-mix-svelte' => '^0.5.0',
                'svelte-loader' => '^2.13.4',
            ],
            Arr::except($packages, [
                '@babel/preset-react',
                'react',
                'react-dom',
                'vue',
                'vue-template-compiler',
            ])
        );
    }

    public static function updateMix()
    {
        copy(__DIR__.'/stubs/webpack.mix.js', base_path('webpack.mix.js'));
    }

    public static function setupBabelConfig()
    {
        copy(__DIR__.'/stubs/.babelrc', base_path('.babelrc'));
    }

    public static function updateMiddlewares()
    {
        copy(__DIR__.'/stubs/HandleInertiaRequests.php', base_path('app/Http/Middleware/HandleInertiaRequests.php'));
    }

	/**
	 * Replace Vite with Mix in Laravel `package.json`
	 * 
	 * 
	 */
	public static function setupLaravelMix()
	{
		$json_data = json_decode(file_get_contents(base_path('package.json')), true);
		
		foreach ($json_data as $k => $v) {
			if (is_array($v) && $k === "scripts" && (isset($v["dev"]) || isset($v["build"]))) {
				unset($v["dev"]);
				unset($v["build"]);
				
				$json_data["scripts"] = [
					"dev" => "npm run development",
					"development" => "mix",
					"watch" =>  "mix watch",
					"watch-poll" => "mix watch -- --watch-options-poll=1000",
					"hot" => "mix watch --hot",
					"prod" => "npm run production",
					"production" => "mix --production"
				];
				
				break;
			}
		}
		
		$json = json_encode($json_data, JSON_PRETTY_PRINT);
		file_put_contents(base_path('package.json'), $json);
	}

    public static function updateScripts()
    {
        File::cleanDirectory(resource_path('js'));

		//resources/js/app.js
        copy(__DIR__.'/stubs/app.js', resource_path('js/app.js'));
		//resources/js/bootstrap.js
        copy(__DIR__.'/stubs/bootstrap.js', resource_path('js/bootstrap.js'));

		//resources/css
		//resources/css/app.css
		if (!File::exists(resource_path('css')))
			File::makeDirectory(resource_path('css'), 0755, true);
        copy(__DIR__.'/stubs/app.css', resource_path('css/app.css'));
		
		//resources/js/components
		if (!File::exists(resource_path('js/components')))
			File::makeDirectory(resource_path('js/components'), 0755, true);
        File::cleanDirectory(resource_path('js/components'));
		//resources/js/components/Home.svelte
        copy(__DIR__.'/stubs/Home.svelte', resource_path('js/components/Home.svelte'));
		
		//resources/js/components/error
		if (!File::exists(resource_path('js/components/error')))
			File::makeDirectory(resource_path('js/components/error'), 0755, true);
        File::cleanDirectory(resource_path('js/components/error'));
		//resources/js/components/error/Error.svelte
        copy(__DIR__.'/stubs/Error.svelte', resource_path('js/components/error/Error.svelte'));
		
		//resources/js/components/auth
		if (!File::exists(resource_path('js/components/auth')))
			File::makeDirectory(resource_path('js/components/auth'), 0755, true);
        File::cleanDirectory(resource_path('js/components/auth'));
		//resources/js/components/auth/Login.svelte
        copy(__DIR__.'/stubs/Login.svelte', resource_path('js/components/auth/Login.svelte'));

		//resources/js/components/layouts
		if (!File::exists(resource_path('js/components/layouts')))
			File::makeDirectory(resource_path('js/components/layouts'), 0755, true);
        File::cleanDirectory(resource_path('/js/components/layouts'));
		//resources/js/components/layouts/AppLayout.svelte
        copy(__DIR__.'/stubs/AppLayout.svelte', resource_path('js/components/layouts/AppLayout.svelte'));
		//resources/js/components/layouts/GuestLayout.svelte
        copy(__DIR__.'/stubs/GuestLayout.svelte', resource_path('js/components/layouts/GuestLayout.svelte'));
		
		if (!File::exists(resource_path('views/layouts')))
			File::makeDirectory(resource_path('views/layouts'), 0755, true);
        copy(__DIR__.'/stubs/app.blade.php', resource_path('views/layouts/app.blade.php'));

		//resources/sass
		if (!File::exists(resource_path('sass')))
			File::makeDirectory(resource_path('sass'), 0755, true);
        copy(__DIR__.'/stubs/app.scss', resource_path('sass/app.scss'));
        copy(__DIR__.'/stubs/_variables.scss', resource_path('sass/_variables.scss'));

        copy(__DIR__.'/stubs/App.svelte', resource_path('js/components/App.svelte'));	
	
		\Artisan::call('inertia:middleware');
		\Artisan::call('ziggy:generate');
    }
}
