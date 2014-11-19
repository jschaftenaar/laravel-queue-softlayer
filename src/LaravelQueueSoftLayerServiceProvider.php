<?php namespace Nathanmac\LaravelQueueSoftLayer;

use Illuminate\Support\ServiceProvider;
use NathanMac\LaravelQueueSoftLayer\Queue\Connectors\SoftLayerConnector;
use Queue;

class LaravelQueueSoftLayerServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->booted(function () {

            Queue::extend('softlayer', function () {
                return new SoftLayerConnector();
            });

        });
    }
}