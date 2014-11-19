<?php namespace Nathanmac\LaravelQueueSoftLayer\Queue;

use Illuminate\Queue\Queue;
use Illuminate\Queue\QueueInterface;
use SoftLayer\Messaging;
use Nathanmac\LaravelQueueSoftLayer\Queue\Jobs\SoftLayerJob;


class SoftLayerQueue extends Queue implements QueueInterface
{

    protected $connection;
    protected $default;

    /**
     * @param Messaging $softlayer
     * @param string    $default
     */
    public function __construct(Messaging $softlayer, $default)
    {
        $this->connection = $softlayer;
        $this->default = $default;
    }

    /**
     * Push a new job onto the queue.
     *
     * @param  string $job
     * @param  mixed  $data
     * @param  string $queue
     *
     * @return bool
     */
    public function push($job, $data = '', $queue = null)
    {
        return $this->pushRaw($this->createPayload($job, $data, $queue), $queue);
    }

    /**
     * Push a raw payload onto the queue.
     *
     * @param  string $payload
     * @param  string $queue
     * @param  array  $options
     *
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = [])
    {
        $queue = $this->connection->queue($this->getQueue($queue))->create();
        $message = $queue->message($payload);

        if (isset($options['delay'])) $message->setVisibilityDelay($options['delay']);

        return $message->create();
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param  \DateTime|int $delay
     * @param  string        $job
     * @param  mixed         $data
     * @param  string        $queue
     *
     * @return mixed
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        $delay = $this->getSeconds($delay);
        $payload = $this->createPayload($job, $data, $queue);
        return $this->pushRaw($payload, $queue, compact('delay'));
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param string|null $queue
     *
     * @return \Illuminate\Queue\Jobs\Job|null
     */
    public function pop($queue = null)
    {
        $queue = $this->connection->queue($this->getQueue($queue))->fetch();

        $messages = $queue->messages(1);

        if ( ! is_null($messages) && ! empty($messages))
        {
            $job = $messages[0];
            return new SoftLayerJob($this->container, $this, $job);
        }
    }

    /**
     * Delete a message from the queue.
     *
     * @param  string  $queue
     * @param  string  $id
     * @return void
     */
    public function deleteMessage($queue, $id)
    {
        $queue = $this->connection->queue($this->getQueue($queue))->fetch();
        $queue->message()->delete($id);
    }

    /**
     * @param $queue
     *
     * @return string
     */
    public function getQueue($queue)
    {
        return $queue ? : $this->default;
    }
}