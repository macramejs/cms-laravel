<?php

namespace Macrame\Admin\Foundation\Console;

use Symfony\Component\Process\Process;

class MakeAdminCommand extends BaseMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Admin application.';

    /**
     * The module that is being published.
     *
     * @var string
     */
    protected $publishesModule = 'admin';

    public function handle()
    {
        // $this->publishModule($this->publishesModule);

        // Copy Admin files
        $this->files->ensureDirectoryExists(base_path('admin'));
        $this->files->copyDirectory($this->publishesPath('admin'), base_path('admin'));
        $this->files->copyDirectory($this->publishesPath('routes'), base_path('routes'));

        // Copy App files
        $this->files->copyDirectory($this->publishesPath('app'), app_path());

        // Copy CORS Configuration
        $this->files->copyDirectory($this->publishesPath('config'), config_path());
        $this->files->copyDirectory($this->publishesPath('migrations'), database_path('migrations'));

        // Register Routes
        $this->replaceInFile(
            "                ->group(base_path('routes/web.php'));",
            "                ->group(base_path('routes/web.php'));

            Route::prefix('admin')
                ->group(base_path('routes/admin.php'));",
            app_path('providers/RouteServiceProvider.php')
        );

        $this->replaceInFile(
            '            "App\\\\": "app/",',
            '            "App\\\\": "app/",
            "Admin\\\\": "admin/",',
            base_path('composer.json')
        );

        (new Process(['composer', 'dump-autoload'], base_path()))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            });

        $composerPackages = [
            'laravel/sanctum',
            'macramejs/macrame-laravel:dev-main',
            'owen-it/laravel-auditing',
        ];

        foreach ($composerPackages as $package) {
            (new Process(['composer', 'require', $package], base_path()))
                ->setTimeout(null)
                ->run(function ($type, $output) {
                    $this->output->write($output);
                });
        }

        return 0;
    }

    protected function replaceInFile($search, $replace, $path)
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }
}
