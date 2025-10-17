<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\Bid;
use App\Models\Booking;
use App\Models\Task;
use App\Models\Category;
use App\Policies\UserPolicy;
use App\Policies\BidPolicy;
use App\Policies\BookingPolicy;
use App\Policies\TaskPolicy;
use App\Policies\CategoryPolicy;

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
        $this->registerPolicies();
    }

    /**
     * Register the application's policies.
     */
    protected function registerPolicies(): void
    {
        \Illuminate\Support\Facades\Gate::policy(User::class, UserPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(Bid::class, BidPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(Booking::class, BookingPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(Task::class, TaskPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(Category::class, CategoryPolicy::class);
    }
}
