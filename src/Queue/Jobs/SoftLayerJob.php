<?php namespace Nathanmac\LaravelQueueSoftLayer\Queue\Jobs;

use Illuminate\Queue\Jobs\Job;
use Nathanmac\LaravelQueueSoftLayer\Queue\SoftLayerQueue;
use Queue;

class SoftLayerJob extends Job
{

    protected $queue;
    protected $envelope;
    protected $job;

    public function __construct($container, SoftLayerQueue $queue, $job)
    {
        $this->container = $container;
        $this->queue = $queue;
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

        $this->queue->deleteMessage($this->getQueue(), $this->job->getId());
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
            Queue::later($delay, $job, $data, $this->getQueue());
        } else {
            Queue::push($job, $data, $this->getQueue());
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
     * Get the name of the queue the job belongs to.
     *
     * @return string
     */
    public function getQueue()
    {
        return $this->queue->getQueue(null);//array_get(json_decode($this->job->getBody(), true), 'queue');
    }

}