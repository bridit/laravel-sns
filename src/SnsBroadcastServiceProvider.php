<?php

namespace Bridit\SNS;

use Aws\Laravel\AwsFacade as Aws;
use Illuminate\Support\ServiceProvider;
use Bridit\SNS\Services\SnsBroadcaster;
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
    if ($this->app instanceof LaravelApplication) {
      $this->app['router']
        ->middleware('web')
        ->any('/aws/sns', 'Rennokki\LaravelSnsEvents\Http\Controllers\SnsController@handle');
    } elseif ($this->app instanceof LumenApplication) {
      $this->app['router']->group([], function ($router) {
        $router->get('/aws/sns', 'Rennokki\LaravelSnsEvents\Http\Controllers\SnsController@handle');
        $router->post('/aws/sns', 'Rennokki\LaravelSnsEvents\Http\Controllers\SnsController@handle');
        $router->put('/aws/sns', 'Rennokki\LaravelSnsEvents\Http\Controllers\SnsController@handle');
        $router->patch('/aws/sns', 'Rennokki\LaravelSnsEvents\Http\Controllers\SnsController@handle');
        $router->delete('/aws/sns', 'Rennokki\LaravelSnsEvents\Http\Controllers\SnsController@handle');
        $router->options('/aws/sns', 'Rennokki\LaravelSnsEvents\Http\Controllers\SnsController@handle');
      });
    }
  }

}
