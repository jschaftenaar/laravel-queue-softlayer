<?php namespace Nathanmac\LaravelQueueSoftLayer\Queue\Connectors;

use Nathanmac\LaravelQueueSoftLayer\Queue\SoftLayerQueue;
use SoftLayer;
use Illuminate\Queue\Connectors\ConnectorInterface;

class SoftLayerConnector implements ConnectorInterface
{

    /**
     * Establish a queue connection.
     *
     * @param  array $config
     *
     * @return \Illuminate\Queue\QueueInterface
     */
    public function connect(array $config)
    {
        $endpoint = isset($config['endpoint']) ? $config['endpoint'] : 'dal05';
        $private = isset($config['private']) && $config['private'] == true ? true : false;

        // Create Connection with SoftLayer
        $connection = new SoftLayer\Messaging($endpoint, $private);
        $connection->authenticate($config['account'], $config['username'], $config['token']);

        return new SoftLayerQueue(
            $connection,
            $config['queue']
        );
    }
}