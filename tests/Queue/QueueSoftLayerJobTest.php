<?php

use Mockery as m;

class QueueSoftLayerJobTest extends PHPUnit_Framework_TestCase {

    public function tearDown()
    {
        m::close();
    }

    public function testFireProperlyCallsTheJobHandler()
    {
        $job = $this->getJob();
        $job->getSoftLayerJob()->shouldReceive('getBody')->once()->andReturn(json_encode(array('job' => 'foo', 'data' => array('data'))));
        $job->getContainer()->shouldReceive('make')->once()->with('foo')->andReturn($handler = m::mock('StdClass'));
        $handler->shouldReceive('fire')->once()->with($job, array('data'));
        $job->fire();
    }

    protected function getJob()
    {
        return new Nathanmac\LaravelQueueSoftLayer\Queue\Jobs\SoftLayerJob(
            m::mock('Illuminate\Container\Container'),
            m::mock('Nathanmac\LaravelQueueSoftLayer\Queue\SoftLayerQueue'),
            m::mock('SoftLayer\Messaging\Message')
        );
    }

}