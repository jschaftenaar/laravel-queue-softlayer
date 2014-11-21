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

    public function testReturnJobPayloadRawBodyData()
    {
        $job = $this->getJob();
        $job->getSoftLayerJob()->shouldReceive('getBody')->once()->andReturn(json_encode(array('job' => 'foo', 'data' => array('data'))));
        $job->getRawBody();
    }

    public function testReturnTheNumberOfAttempts()
    {
        // No Attempt Data
        $job = $this->getJob();
        $job->getSoftLayerJob()->shouldReceive('getBody')->once()->andReturn(json_encode(array('job' => 'foo', 'data' => array('data'))));
        $this->assertEquals(0, $job->attempts());

        // Attempt Data - Value (3)
        $job = $this->getJob();
        $job->getSoftLayerJob()->shouldReceive('getBody')->once()->andReturn(json_encode(array('job' => 'foo', 'data' => array('attempts' => 3))));
        $this->assertEquals(3, $job->attempts());
    }

    public function testSoftLayerInstanceGetter()
    {
        $job = $this->getJob();
        $result = $job->getSoftLayer();
        $this->assertInstanceOf('\Nathanmac\LaravelQueueSoftLayer\Queue\SoftLayerQueue', $result);
    }

    public function testFetchJobId()
    {
        $job = $this->getJob();
        $job->getSoftLayerJob()->shouldReceive('getId')->once()->andReturn(1);
        $this->assertEquals(1, $job->getJobId());
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