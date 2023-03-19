# Laravel Svelte Preset

A Laravel frontend preset for initial Svelte scaffolding.

## Composer Terminology

If we are trying to download a package from a url which is not hosted on [packagist.org](https://packagist.org/), Composer will offer a GIT repositories as our PHP project as a repository not been created as a package on packagist.

- **Project**: The custom software we are building. This can be a website, a command line utility, application, or anything else we dream up.
- **Package**: Any 3rd party software we want to download and use within our project. It is goint to be a library in this case.
- **Git repository**: The version control host for a package. Common hosts are: GitHub, GitLab, or Bitbucket; but any URL accessible Git repository will also work.
- **Composer repositories**: In a `composer.json` file there is an optional property named *repositories*. This property is where we can define new places for Composer to look when downloading packages.

_Read only permissions are sufficient for this personal access token as it is used by composer to only download the package from our private github repository._

_When adding a Git repo to our project with composer there are two situations we can find ourselves in: the repo contains a `composer.json` file and it defines how the repo should be handled when required, or it does not. Regardless, in both cases we are able to add the Git repository to our project._

```bash
{
    "name": "laravel-inertiajs-svelte-preset",
    "type": "library"
}
```
- **name**: The package's namespaced name. In this case daggerhart is the namespace for the package my-custom-library.
- **type**: The type of package the repo represents. [Package types](https://getcomposer.org/doc/04-schema.md#type) are used for installation logic. Out of the box, composer allows for the following package types: *library*, *project*, *metapackage*, or *composer-plugin*.

Now that we have a github personal access token with read privileges to the private repository, We need to inform composer to connect to our private github repository using this personal access token. This can be done using the `auth.json` file.

Once you created a new file called `auth.json` in your project root directory and add the following code to it.

Into `auth.json` replace file `your-github-token` with your newly created github personal access token.

_You should never commit this file to github. Doing so will give unauthorized users access to your github repositories if the token is compromised._

## Installation

By default, Composer will try to download packages from **packagist.org**. Since, our packages are packages hosted on github, they won't be available on packagist.org to download as said in the previous paragraph. Thus, we need to instruct composer about which github repository to look for in order to find the package it is trying to download.

You can install the package via composer using the require array and repositories array in your project `composer.json` file:

```bash
"require": {
    ...
    "garata/laravel-inertiajs-svelte-preset": "dev-main"
},


...
...

"repositories": [
    {
        "type": "git",
        "url": "https://github.com/garata/laravel-inertiajs-svelte-preset.git"
    }
]
```
_By adding the above code, we are instructing composer to look for the package in **laravel-inertiajs-svelte-preset** GIT repository hosted on the specified url._
_The package version is a part of the require statement, and not a part of the repository property. We can require a specific branch of a repo by choosing a version named **dev-<branch name>**. Composer will check GitHub last *commit hash* release and use it as the package versioning logic, but also [Composer version](https://getcomposer.org/doc/04-schema.md#version) is relevant when versioning is evaluated._

Assuming that you are installing a project that already contains a `composer.json` file, after the aforementioned `require` and `repositories` modifications running `composer update` will sync **garata/laravel-inertiajs-svelte-preset** whole depencies. So, if we were to run `composer` command in the context of this file, composer would look for a project at the defined URL, and if that URL represents a Git repo that contains a `composer.json` file that defines its name and type, composer will download that package to our project and place it in the appropriate location.

Another approach could be based on the `package` definition within your project `composer.json` file, which does not update the commit references as main drawback:

```bash
"repositories": [{
  "type": "package",
  "package": {
    "name": "garata-laravel-inertiajs-svelte-preset", // Composer requires a unique project package name
    "version": "1.0", // Composer will not update the package unless you change the version field
    "source": {
      "url": "https://github.com/garata/laravel-inertiajs-svelte-preset.git", // git url
      "type": "git",
      "reference": "main" // git branch-name
    }
  }
}],
"require": {
  "garata-laravel-inertiajs-svelte-preset": "1.0"
}
```

After `composer.json` file grinding, run the following commands, which will install the Bootstrap UI Auth views (Login, Register, ...) dependencies and provide you with the initial scaffolding of the project:

```bash
php artisan ui bootstrap --auth
php artisan ui svelte
```

To install the JavaScript dependencies, run:

```bash
npm install && npm run dev
```

The package will provide you with the initial set of files:

- `/js/app.js`
- `/js/components/App.svelte`
- `webpack.mix.js`

needed to start developing with Laravel & Svelte.

### Usage

```html
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    ...
    <!-- Include the app.js file -->
    <script src="{{ mix('js/app.js') }}" defer></script>
  </head>
  <body>
    <!-- Include your App Component -->
    <App />
  </body>
</html>
```

### Registering Custom Svelte Components

If you wish to use custom components, note you cannot use regular svelte components. Doing so will result in an invalid constructor error for the svelte component.

Please follow these general conventions when creating your custom components:

- Component name must be two or more words joined by the '-' character e.g. 'my-test-component'.
- Components can be accessed in blade file like a regular html tag e.g. `<my-test-component></my-test-component>`
- Closing tag is necessary because its a web component.

If you wish to register a custom component and use it within your `blade.php` files, you can do it like so:

#### Step 0: Update Web Routes

Let's update `web.php` routes adding two new namespaces.

```php
use Inertia\Inertia;
use Tightenco\Ziggy\Ziggy;
```

Then update `web.php` routes commenting out already present routes.

```php
//Route::get('/', function () { return view('welcome'); });
//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
```

And lastly add *Inertia* route to the `web.php`.

```php
Route::get('api/ziggy', fn () => response()->json(new Ziggy));

Route::get('/', function () {
    return Inertia('Home');
})->name('home');

Auth::routes();
```

Add `HandleInertiaRequests` middleware `app/Http/Kernel.php` that should be last `$middlewareGroups` array element.

```php
'web' => [	
	// InertiaJS Middleware should be last
	\App\Http\Middleware\HandleInertiaRequests::class,
],
```

#### Step 1: Override Auth Scaffolding Controller

In `App/Http/Controllers/LoginController.php`, in order to reference Inertia Svelte View, add `showLoginForm()` function overriding the trait `AuthenticatesUsers` function.

```diff
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
	
+   /**
+    * Show the application's login form.
+    *
+    * @return \Inertia\Response
+    */
+   public function showLoginForm()
+   {
+       // Return Inertia Svelte Rendered Component instead of standard blade view
+       return Inertia::render('Auth/Login'); // components/auth/Login.svelte
+   }
}
```

#### Step 2: Generate Ziggy Routes

Add new routes to your laravel app and refresh `resources/js/ziggy.js`, that will be included in `app.js` as this step may be repeated every time we need it.

```bash
php artisan ziggy:generate
```

#### Step 3: Import the component to your app.js

Then within your `app.js` file, import the MyTestComponent like so:

```diff
require('./bootstrap');

import App from "./components/App.svelte";
+ import MyTestComponent from "./components/MyTestComponent.svelte";

const app = new App({
  target: document.body
});

window.app = app;

+ customElements.define('my-test-component', MyTestComponent);
export default app;
```

#### Step 4: Convert your App component to a custom component

```diff
require('./bootstrap');

import App from './components/App.svelte';
import MyTestComponent from './components/MyTestComponent.svelte';

+ customElements.define('my-app', App);
customElements.define('my-test-component', MyTestComponent);

- const app = new App({
-     target: document.body,
- });

- window.app = app;
- export default app;
```

#### Step 5: Use the new component in your `blade.php`file

```diff
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        ...
        <!-- Include the app.js file -->
        <script src="{{ mix('js/app.js') }}" defer></script>
    </head>
    <body>
        <!-- Include your App Component -->
-       <App />
+       <my-app></my-app>
+       <my-test-component></my-test-component>
    </body>
</html>
```

Additionally, you may also define the tag within your svelte component instead of with `customElement.define` as so:

`<svelte:options tag="my-test-component" />`

## License

ISC License. Please see [License File](LICENSE.md) for more information.
