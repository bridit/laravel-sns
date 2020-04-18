<?php

return [

  /*
  |--------------------------------------------------------------------------
  | Default Broadcaster
  |--------------------------------------------------------------------------
  |
  | This option controls the default broadcaster that will be used by the
  | framework when an event needs to be broadcast. You may set this to
  | any of the connections defined in the "connections" array below.
  |
  | Supported: "pusher", "redis", "log", "null"
  |
  */

  'default' => env('BROADCAST_DRIVER', 'sns'),

  /*
  |--------------------------------------------------------------------------
  | Broadcast Connections
  |--------------------------------------------------------------------------
  |
  | Here you may define all of the broadcast connections that will be used
  | to broadcast events to other systems or over websockets. Samples of
  | each available type of connection are provided inside this array.
  |
  */

  'connections' => [

    'pusher' => [
      'driver' => 'pusher',
      'key' => env('PUSHER_APP_KEY'),
      'secret' => env('PUSHER_APP_SECRET'),
      'app_id' => env('PUSHER_APP_ID'),
      'options' => [
        'cluster' => env('PUSHER_APP_CLUSTER'),
        'useTLS' => true,
      ],
    ],

    'redis' => [
      'driver' => 'redis',
      'connection' => 'default',
    ],

    'log' => [
      'driver' => 'log',
    ],

    'null' => [
      'driver' => 'null',
    ],

    'sns' => [
      'driver' => 'sns',
      'route' => '/aws/sns',
      'key' => env('AWS_ACCESS_KEY_ID'), // optional
      'secret' => env('AWS_SECRET_ACCESS_KEY'), // optional
      'region' => env('SNS_REGION'),
      'arn_prefix' => 'arn:aws:sns:' .
        env('AWS_SNS_REGION', env('AWS_REGION', 'us-east-1')) . ':' . env('AWS_ACCOUNT_ID'),
    ],
  ],

];
