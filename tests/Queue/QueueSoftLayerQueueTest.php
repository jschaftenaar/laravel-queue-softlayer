<?php

use Mockery as m;

class QueueSoftLayerQueueTest extends PHPUnit_Framework_TestCase {

    public function tearDown()
    {
        m::close();
    }

    public function testPushProperlyPushesJobOntoSoftLayer()
    {
        $queue = new \Nathanmac\LaravelQueueSoftLayer\Queue\SoftLayerQueue($softlayer = m::mock('\SoftLayer\Messaging'), 'default');
        $softlayer->shouldReceive('queue')->once()->with('default')->andReturn($softlayer_queue = m::mock('\SoftLayer\Messaging\Queue'));
        $softlayer_queue->shouldReceive('create')->once()->andReturnSelf();
        $softlayer_queue->shouldReceive('message')->once()->with(json_encode(array('job' => 'foo', 'data' => array(1, 2, 3))))->andReturn($softlayer_message = m::mock('\SoftLayer\Messaging\Message'));
        $softlayer_message->shouldReceive('create')->once()->andReturnSelf();
        $queue->push('foo', array(1, 2, 3));
    }

    public function testDelayedPushProperlyPushesJobOntoSoftLayer()
    {
        $queue = new \Nathanmac\LaravelQueueSoftLayer\Queue\SoftLayerQueue($softlayer = m::mock('\SoftLayer\Messaging'), 'default');
        $softlayer->shouldReceive('queue')->once()->with('default')->andReturn($softlayer_queue = m::mock('\SoftLayer\Messaging\Queue'));
        $softlayer_queue->shouldReceive('create')->once()->andReturnSelf();
        $softlayer_queue->shouldReceive('message')->once()->with(json_encode(array(
            'job' => 'foo', 'data' => array(1, 2, 3)
        )))->andReturn($softlayer_message = m::mock('\SoftLayer\Messaging\Message'));
        $softlayer_message->shouldReceive('create')->once()->andReturnSelf();
        $softlayer_message->shouldReceive('setVisibilityDelay')->once();

        $queue->later(5, 'foo', array(1, 2, 3));
    }

    public function testPopProperlyPopsJobOffOfSoftLayer()
    {
        $queue = new \Nathanmac\LaravelQueueSoftLayer\Queue\SoftLayerQueue($softlayer = m::mock('\SoftLayer\Messaging'), 'default');
        $queue->setContainer(m::mock('Illuminate\Container\Container'));
        $softlayer->shouldReceive('queue')->once()->with('default')->andReturn($softlayer_queue = m::mock('\SoftLayer\Messaging\Queue'));
        $softlayer_queue->shouldReceive('fetch')->once()->andReturnSelf();
        $softlayer_queue->shouldReceive('messages')->once()->with(1)->andReturn([$job = m::mock('\SoftLayer\Messaging\Message')]);
        $result = $queue->pop();
        $this->assertInstanceOf('\Nathanmac\LaravelQueueSoftLayer\Queue\Jobs\SoftLayerJob', $result);
    }

    public function testDeleteProperlyRemoveJobsOffSoftLayer()
    {
        $queue = new \Nathanmac\LaravelQueueSoftLayer\Queue\SoftLayerQueue($softlayer = m::mock('\SoftLayer\Messaging'), 'default');
        $softlayer->shouldReceive('queue')->once()->with('default')->andReturn($softlayer_queue = m::mock('\SoftLayer\Messaging\Queue'));
        $softlayer_queue->shouldReceive('fetch')->once()->andReturnSelf();
        $softlayer_queue->shouldReceive('message')->once()->andReturn($softlayer_message = m::mock('\SoftLayer\Messaging\Message'));
        $softlayer_message->shouldReceive('delete')->once()->with(1)->andReturnSelf();
        $queue->deleteMessage('default', 1);
    }
}