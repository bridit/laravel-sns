<?php

namespace Bridit\Sns\Services;

use Aws\Sns\SnsClient;
use Illuminate\Http\Request;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Broadcasting\Broadcasters\Broadcaster;

class SnsBroadcaster extends Broadcaster
{

  /**
   * @var SnsClient
   */
  protected $client;

  /**
   * @var Repository
   */
  protected $config;

  /**
   * SnsBroadcaster constructor.
   * @param SnsClient $client
   * @param Repository $config
   */
  public function __construct(SnsClient $client, Repository $config)
  {
    $this->client = $client;
    $this->config = $config;
  }

  /**
   * @inheritDoc
   */
  public function broadcast(array $channels, $event, array $payload = [])
  {
    $arnPrefix = $this->config->get('broadcasting.connections.sns.arn_prefix');
    $message = json_encode(['data' => $payload]);

    foreach ($channels as $channel)
    {
      $this
        ->client
        ->publish([
            'TopicArn' => $arnPrefix . ':' . $channel,
            'Message' => $message,
            'Subject' => $event,
          ]
        );
    }
  }

  /**
   * @inheritDoc
   */
  public function auth($request)
  {
    return true;
  }

  /**
   * @inheritDoc
   */
  public function validAuthenticationResponse($request, $result)
  {
    if (is_bool($result)) {
      return json_encode($result);
    }

    return json_encode(['channel_data' => [
      'user_id' => $this->retrieveUser($request, $request->channel_name)->getAuthIdentifier(),
      'user_info' => $result,
    ]]);
  }
}
