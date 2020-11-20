# Laravel SNS
Laravel SNS allow you to broadcast and listen to SNS events. 
It implements a driver on BroadcastManager to make the use of SNS as easy as Laravel Events. 
It also implements a controller, using `rennokki/laravel-sns-events` package, that is made to properly listen to SNS HTTP(s) webhooks and trigger events on which you can listen to.

If you are not familiar with Laravel Events & Listeners, make sure you check the [documentation section on laravel.com](https://laravel.com/docs/7.x/events) because this package will need you to understand this concept.

Make sure to also check `rennokki/laravel-sns-events` [documentation](https://github.com/rennokki/laravel-sns-events).

## Install
```bash
$ composer require bridit/laravel-sns
```

## Configuration
You will need an AWS account and make sure you have it properly configured on Laravel, following the steps of official [documentation](https://github.com/aws/aws-sdk-php-laravel).

Then you need to register a connection for SNS on broadcasting.php, you can do this adding code above on connections section of broadcasting.php:
```
...

'sns' => [
  'driver' => 'sns',
  'route' => '/aws/sns',
  'key' => env('AWS_ACCESS_KEY_ID'), // optional
  'secret' => env('AWS_SECRET_ACCESS_KEY'), // optional
  'region' => env('SNS_REGION'), // optional
  'arn_prefix' => 'arn:aws:sns:' .
    env('AWS_SNS_REGION', env('AWS_REGION', 'us-east-1')) . ':' . env('AWS_ACCOUNT_ID'),
],
```

We made use of env variables to build arn_prefix, but you can also specify a simple string. You can find your arn on AWS platform, on any topic you own.

This package auto register the necessary routes for you, on web middleware, using 'route' attribute of broadcasting.php. 
You don't need to do it by yourself.

SNS sends data through POST, so you will need to whitelist your route in your `VerifyCsrfToken.php`:
```php
protected $except = [
  ...
  '/aws/sns', // Or other route that you set on broadcasting.php
];
```

If you want this to be your default broadcasting driver set it on your .env:
```editorconfig
BROADCAST_DRIVER=sns
```

Now you are ready to go. Just create a topic and set up a subscription for HTTP(s) protocol that will point out to the route you just registered.
Click the confirmation button from the AWS Dashboard. In a short while, if you implemented the route well, you'll be seeing that your endpoint is registered.


## Usage
To send SNS events just use default Laravel dispatcher, like:
```
event(new OrderShipped($order));
```

The package comes with two event classes:
* `Rennokki\LaravelSnsEvents\Events\SnsNotification` - triggered on each SNS message
* `Rennokki\LaravelSnsEvents\Events\SnsSubscriptionConfirmation` - triggered when the subscription is confirmed

To process the events, you should add the events in your `app/Providers/EventServiceProvider.php`:
```php
use Rennokki\LaravelSnsEvents\Events\SnsNotification;
use Rennokki\LaravelSnsEvents\Events\SnsSubscriptionConfirmation;

...

protected $listen = [
    ...
    SnsNotification::class => [
        // add your listeners here for SNS events
    ],
    SnsSubscriptionConfirmation::class => [
        // add your listeners here in case you want to listen to subscription confirmation
    ],
]
```

You will be able to access the SNS message from your listeners like this:
```php
class MyListener
{
    ...

    public function handle($event)
    {
        // $event->payload is an array
    }
}
```

## License
The MIT License (MIT).
