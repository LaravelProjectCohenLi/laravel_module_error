<?php

namespace Modules;

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    private $middlewares = [

    ];

    private $commands = [

    ];

    public function boot()
    {
        $modules = $this->getModules();
        if (!empty($modules)) {
            foreach ($modules as $module) {
                $this->registerModule($module);
            }
        }
    }

    public function register()
    {
        //Configs
        $modules = $this->getModules();
        if (!empty($modules)) {
            foreach ($modules as $module) {
                $this->registerConfig($module);
            }
        }

        //Middleware
        $this->registerMiddlewares();

        //Commands
        $this->commands($this->commands);

        $this->app->singleton(
            UserRepository::class
        );

        // $modules = $this->getModules();
        // if (!empty($modules)) {
        //     foreach ($modules as $module) {
        //         $this->registerControllers($module);
        //     }
        // }
    }


    private function getModules()
    {
        $directories = array_map('basename', File::directories(__DIR__));
        return $directories;
    }

    //registerModule
    private function registerModule($module)
    {
        $modulePath = __DIR__."/{$module}";

        //Khai báo Routes
        if (File::exists($modulePath. '/routes/routes.php')) {
            $this->loadRoutesFrom($modulePath.'/routes/routes.php');
        }

        //Khai báo migrations
        if (File::exists($modulePath. '/migrations')) {
            $this->loadMigrationsFrom($modulePath.'/migrations');
        }

        //Khai báo languages
        if (File::exists($modulePath. '/resources/lang')) {
            $this->loadTranslationsFrom($modulePath.'/resources/lang', strtolower($module));

            $this->loadJSONTranslationsFrom($modulePath.'/resources/lang');
        }

        //Khai báo views
        if (File::exists($modulePath. '/resources/views')) {
            $this->loadViewsFrom($modulePath.'/resources/views', strtolower($module));
        }

        //Khai báo helpers
        if (File::exists($modulePath. '/helpers')) {
            $helperList = File::allFiles($modulePath. '/helpers');
            if (!empty($helperList)) {
                foreach ($helperList as $helper) {
                    $file = $helper->getPathName();
                    require $file;
                }
            }
        }
    }

    //register configs
    private function registerConfig($module)
    {
        $configPath = __DIR__.'/'.$module.'/configs';

        if (File::exists($configPath)) {
            $configFiles = array_map('basename', File::allFiles($configPath));

            foreach ($configFiles as $config) {
                $alias = basename($config, '.php');

                $this->mergeConfigFrom($configPath.'/'.$config, $alias);
            }
        }
    }

    // register middlewares
    private function registerMiddlewares()
    {
        if (!empty($this->middlewares)) {
            foreach ($this->middlewares as $key => $middleware) {
                $this->app['router']->pushMiddlewareToGroup($key, $middleware);
            }
        }
    }

    // register controller
    // private function registerControllers($module)
    // {
    //     $controllerPath = __DIR__ . '/' . $module . '/controllers';

    //     if (File::exists($controllerPath)) {
    //         $controllers = File::allFiles($controllerPath);

    //         foreach ($controllers as $controller) {
    //             $controllerName = $controller->getBasename('.php');
    //             $controllerNamespace = 'Modules\\' . $module . '\\Controllers\\' . $controllerName;

    //             // Đăng ký controller với Router
    //             app('router')->namespace('Modules\\' . $module . '\\Controllers')
    //                 ->group(function (Router $router) use ($controllerName, $controllerNamespace) {
    //                     $router->middleware('web')->group(function () use ($router, $controllerName, $controllerNamespace) {
    //                         $router->get('/' . $controllerName, $controllerNamespace . '@index');
    //                         // Thêm các route khác tương ứng với action trong controller
    //                     });
    //                 });
    //         }
    //     }
    // }
}