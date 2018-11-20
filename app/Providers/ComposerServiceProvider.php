<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

use App\Menu;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Using Closure based composers...
        View::composer('layouts.master', function ($view) {

            $loggedInUser = auth()->user();
            $userMenus = Menu::getMenusForFole($loggedInUser->fk_role_id);

            $view->with('userMenus', $userMenus);
           
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
