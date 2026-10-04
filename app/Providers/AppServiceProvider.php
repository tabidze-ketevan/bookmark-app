<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.layout', function (\Illuminate\View\View $view): void {
            $user = auth()->user();

            $view->with([
                'sidebarCollections' => $user->collections()->withCount('bookmarks')->orderBy('title')->get(),
                'sidebarTags' => $user->tags()->withCount('bookmarks')->orderBy('title')->get(),
                'sidebarCounts' => [
                    'all' => $user->bookmarks()->count(),
                    'unsorted' => $user->bookmarks()->whereNull('collection_id')->count(),
                    'favorites' => $user->bookmarks()->where('is_favorite', true)->count(),
                ],
            ]);
        });
    }
}
