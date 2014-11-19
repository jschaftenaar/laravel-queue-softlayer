<?php namespace NathanMac\LaravelQueueSoftLayer\Queue\Connectors;

use NathanMac\LaravelQueueSoftLayer\Queue\SoftLayerQueue;
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
        // Create Connection with SoftLayer
        $connection = new SoftLayer\Messaging();
        $connection->authenticate($config['account'], $config['username'], $config['token']);

        return new SoftLayerQueue(
            $connection,
            $config['queue']
        );
    }
}