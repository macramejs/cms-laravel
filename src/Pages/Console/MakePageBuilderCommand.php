<?php

namespace Macrame\Admin\Pages\Console;

use Illuminate\Support\Str;
use Macrame\Admin\Foundation\Console\BaseMakeCommand;
use Symfony\Component\Console\Input\InputArgument;

class MakePageBuilderCommand extends BaseMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:page-builder';

    protected $publishes = 'pages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Macrame Admin page builder.';

    public function handle()
    {
        $this->makeAppFiles();
        $this->makeResourcesFiles();

        return 0;
    }

    protected function replaces(): array
    {
        return [
            'app'       => $this->app(),
            'name'      => $this->name(),
            'route'     => $this->route(),
            'page'      => $this->page(),
            'namespace' => $this->namespace(),
            'time'      => date('Y_m_d_His', time()),
            'model'     => $this->model(),
            'table'     => $this->tableName(),
        ];
    }

    protected function model()
    {
        return ucfirst(Str::camel($this->argument('name')));
    }

    protected function tableName()
    {
        return Str::snake(Str::plural($this->argument('name')));
    }

    protected function makeAppFiles()
    {
        // Admin-Controllers
        $this->publishDir(
            from: $this->publishesPath('app/controllers'),
            to: $this->appPath('Http/Controllers')
        );
        // App-Controllers
        $this->publishDir(
            from: $this->publishesPath('controllers'),
            to: app_path('Http/Controllers')
        );

        // Indexes
        $this->publishDir(
            from: $this->publishesPath('app/indexes'),
            to: $this->appPath('Http/Indexes')
        );

        // Resources
        $this->publishDir(
            from: $this->publishesPath('app/resources'),
            to: $this->appPath('Http/Resources')
        );

        // Migrations
        $this->publishDir(
            from: $this->publishesPath('migrations'),
            to: database_path('migrations')
        );

        // Models
        $this->publishDir(
            from: $this->publishesPath('app/models'),
            to: app_path('Models')
        );

        $model = $this->model();
        $insert = '
            Page::routes(PageController::class);';
        $after = "Route::middleware('web')
        ->namespace(\$this->namespace)
        ->group(base_path('routes/web.php'));";

        $providerPath = app_path('Providers/RouteServiceProvider.php');
        $this->insertAfter($providerPath, $insert, $after);

        $insert = "\nuse App\Http\Controllers\\{$model}Controller;";
        $after = "namespace App\Providers;";

        $this->insertAfter($providerPath, $insert, $after);

        $route = $this->route();
        $insert = "
    // {$route}
    Route::get('/{$route}', [{$model}Controller::class, 'index'])->name('{$route}.index');
    Route::get('/{$route}/items', [{$model}Controller::class, 'items'])->name('{$route}.items');
    Route::get('/{$route}/{page}', [{$model}Controller::class, 'show'])->name('{$route}.show');
    Route::post('/{$route}', [{$model}Controller::class, 'store'])->name('{$route}.store');
    Route::post('/{$route}/order', [{$model}Controller::class, 'order'])->name('{$route}.order');
    Route::post('/{$route}/{page}', [{$model}Controller::class, 'update'])->name('{$route}.update');
    Route::post('/{$route}/{page}/upload', [{$model}Controller::class, 'upload'])->name('{$route}.upload');";
        $before = '});';

        $routesPath = base_path('routes/'.$this->app().'.php');
        $this->insertBefore($routesPath, $insert, $before);

        $insert = "use Admin\Http\Controllers\\{$model}Controller;";
        $before = "use Illuminate\Support\Facades\Route;";

        $this->insertBefore($routesPath, $insert, $before);
    }

    protected function makeResourcesFiles()
    {
        // Pages
        $this->publishDir(
            from: $this->publishesPath('resources/Pages'),
            to: resource_path($this->app().'/js/Pages/'.$this->page())
        );

        // Types
        $model = $this->model();
        $insert = "// {$model}
        
export type {$model} = {
    content: { [key: string]: any };
    id?: number;
    name: string;
};

export type {$model}ListItem = RawListItem<{$model}>;
export type {$model}Resource = Resource<{$model}>;
export type {$model}CollectionResource = CollectionResource<{$model}>;
export type {$model}ListResource = Resource<{$model}ListItem>;
export type {$model}ListCollectionResource = CollectionResource<{$model}ListItem>;";
        $this->insertAtEnd(
            resource_path($this->app().'/js/types/resources.ts'),
            $insert
        );

        $this->insertAtStart(
            resource_path($this->app().'/js/types/resources.ts'),
            'import { RawListItem } from "@macramejs/macrame-vue3";'
        );
    }

    protected function resourcesDir()
    {
        return $this->name();
    }

    protected function appPath($path = '')
    {
        return base_path(lcfirst($this->app())).($path != '' ? DIRECTORY_SEPARATOR.$path : '');
    }

    protected function namespace()
    {
        return ucfirst(Str::camel($this->argument('app')));
    }

    protected function app()
    {
        return Str::kebab($this->argument('app'));
    }

    protected function page()
    {
        return ucfirst(Str::camel($this->argument('name')));
    }

    protected function name()
    {
        return Str::kebab($this->argument('name'));
    }

    protected function route()
    {
        return Str::kebab(Str::plural($this->argument('name')));
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            // ['all', 'a', InputOption::VALUE_NONE, 'Generate a migration, seeder, factory, policy, and resource controller for the model'],
            // ['controller', 'c', InputOption::VALUE_NONE, 'Create a new controller for the model'],
        ];
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['app', InputArgument::REQUIRED, 'The name of the admin application.'],
            ['name', InputArgument::REQUIRED, 'The name of the page builder.'],
        ];
    }
}