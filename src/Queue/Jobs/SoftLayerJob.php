<?php namespace Nathanmac\LaravelQueueSoftLayer\Queue\Jobs;

use Illuminate\Queue\Jobs\Job;
use Nathanmac\LaravelQueueSoftLayer\Queue\SoftLayerQueue;
use Queue;

class SoftLayerJob extends Job
{

    protected $softlayer;
    protected $envelope;
    protected $job;

    public function __construct($container, SoftLayerQueue $queue, \SoftLayer\Messaging\Message $job)
    {
        $this->container = $container;
        $this->softlayer = $queue;
        $this->job = $job;
    }

    /**
     * Fire the job.
     *
     * @return void
     */
    public function fire()
    {
        $this->resolveAndFire(json_decode($this->job->getBody(), true));
    }

    /**
     * Get the raw body string for the job.
     *
     * @return string
     */
    public function getRawBody()
    {
        return $this->job->getBody();
    }

    /**
     * Delete the job from the queue.
     *
     * @return void
     */
    public function delete()
    {
        parent::delete();

        $this->softlayer->deleteMessage($this->getSoftlayer(), $this->job->getId());
    }

    /**
     * Release the job back into the queue.
     *
     * @param  int $delay
     *
     * @return void
     */
    public function release($delay = 0)
    {
        $this->delete();

        $body = $this->job->getBody();
        $body = json_decode($body, true);

        $attempts = $this->attempts();

        // write attempts to body
        $body['data']['attempts'] = $attempts + 1;

        $job = $body['job'];
        $data = $body['data'];

        // push back to a queue
        if ($delay > 0) {
            Queue::later($delay, $job, $data, $this->getSoftlayer());
        } else {
            Queue::push($job, $data, $this->getSoftlayer());
        }
    }

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function attempts()
    {
        $body = json_decode($this->job->getBody(), true);

        return isset($body['data']['attempts']) ? $body['data']['attempts'] : 0;
    }

    /**
     * Get the job identifier.
     *
     * @return string
     */
    public function getJobId()
    {
        return $this->job->getId();
    }

    /**
     * Get the IoC container instance.
     *
     * @return \Illuminate\Container\Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Get the underlying SoftLayer instance.
     *
     * @return \SoftLayer\Messaging
     */
    public function getSoftLayer()
    {
        return $this->softlayer;
    }
    /**
     * Get the underlying SoftLayer Message job.
     *
     * @return \SoftLayer\Messaging\Message
     */
    public function getSoftLayerJob()
    {
        return $this->job;
    }
}