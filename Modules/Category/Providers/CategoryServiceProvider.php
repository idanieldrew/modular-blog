<?php

namespace Module\Category\Providers;

use Carbon\Laravel\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Module\Category\Models\Category;
use Module\Category\Observers\CategoryObserver;

class CategoryServiceProvider extends ServiceProvider
{
    // namespace
    private $namespace = 'Module\Category\Http\Controllers';

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Migrations
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        // Routes
        Route::prefix('api/category')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(__DIR__ . '/../Routes/category_route.php');

        // Observer Category
        Category::observe(CategoryObserver::class);
    }
}