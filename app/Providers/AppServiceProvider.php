<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
  /**
   * Register any application services.
   */
  public function register(): void {
    //
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void {
    RateLimiter::for('events', function (Request $request) {
      $routeName = $request->route()->getName();
      $excludedRoutes = ['events.index', 'events.show'];

      if (in_array($routeName, $excludedRoutes)) {
        return Limit::none();
      }

      return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
    });

    RateLimiter::for('attendees', function (Request $request) {
      $routeName = $request->route()->getName();
      $excludedRoutes = ['events.attendees.index', 'events.attendees.show'];

      if (in_array($routeName, $excludedRoutes)) {
        return Limit::none();
      }

      return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
    });
  }
}
