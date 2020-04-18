<?php

namespace Bridit\Sns;

use Aws\Laravel\AwsFacade as Aws;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Bridit\Sns\Services\SnsBroadcaster;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Broadcasting\BroadcastManager;
use Laravel\Lumen\Application as LumenApplication;
use Illuminate\Foundation\Application as LaravelApplication;

class SnsBroadcastServiceProvider extends ServiceProvider
{

  public function boot()
  {
    $this
      ->app
      ->make(BroadcastManager::class)
      ->extend('sns', function ($app, $config) {
        return new SnsBroadcaster(
          Aws::createClient('sns'),
          $app->make(Repository::class)
        );
      });
  }

  public function register()
  {
    $this->mergeConfigFrom(__DIR__ . '/../config/broadcasting.php', 'broadcasting');

    $this->snsRouteRegister();

    if ($this->app instanceof LumenApplication) {
      $this->app->bind(\Illuminate\Broadcasting\BroadcastManager::class, function ($app, $config) {
        return new \Illuminate\Broadcasting\BroadcastManager($app);
      });
    }
  }

  /**
   * Register web routes to receive messages.
   */
  private function snsRouteRegister()
  {
    $route = Config::get('broadcasting.connections.sns.route', '/aws/sns');

    if ($this->app instanceof LaravelApplication) {
      $this->app['router']
        ->middleware('web')
        ->any($route, 'Rennokki\LaravelSnsEvents\Http\Controllers\SnsController@handle');
    } elseif ($this->app instanceof LumenApplication) {
      $this->app['router']->group([], function ($router) use ($route) {
        $router->get($route, 'Rennokki\LaravelSnsEvents\Http\Controllers\SnsController@handle');
        $router->post($route, 'Rennokki\LaravelSnsEvents\Http\Controllers\SnsController@handle');
        $router->put($route, 'Rennokki\LaravelSnsEvents\Http\Controllers\SnsController@handle');
        $router->patch($route, 'Rennokki\LaravelSnsEvents\Http\Controllers\SnsController@handle');
        $router->delete($route, 'Rennokki\LaravelSnsEvents\Http\Controllers\SnsController@handle');
        $router->options($route, 'Rennokki\LaravelSnsEvents\Http\Controllers\SnsController@handle');
      });
    }
  }

}
