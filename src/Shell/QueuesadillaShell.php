<?php
declare(strict_types = 1);
namespace App\Shell;

use Cake\Datasource\ConnectionManager;
use Josegonzalez\CakeQueuesadilla\Queue\Queue;
use josegonzalez\Queuesadilla\Engine\Base as BaseEngine;
use josegonzalez\Queuesadilla\Worker\Base as BaseWorker;
use Monitor\Error\SentryHandler;
use Psr\Log\LoggerInterface;

class QueuesadillaShell extends \Josegonzalez\CakeQueuesadilla\Shell\QueuesadillaShell
{
    /**
     * Retrieves a queue worker
     *
     * @param \josegonzalez\Queuesadilla\Engine\Base $engine engine to run
     * @param \Psr\Log\LoggerInterface $logger logger
     * @return \josegonzalez\Queuesadilla\Worker\Base
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     */
    public function getWorker($engine, $logger): BaseWorker
    {
        $worker = parent::getWorker($engine, $logger);
        $worker->attachListener('Worker.job.exception', function ($event): void {
            $exception = $event->data['exception'];
            $exception->job = $event->data['job'];
            $sentryHandler = new SentryHandler();
            $sentryHandler->handle($exception);
        });
        $worker->attachListener('Worker.job.success', function ($event): void {
            ConnectionManager::get('default')->disconnect();
        });
        $worker->attachListener('Worker.job.failure', function ($event): void {
            $failedJob = $event->data['job'];
            $failedItem = $failedJob->item();
            $options = [
                'queue' => 'failed',
                'failedJob' => $failedJob
            ];
            Queue::push($failedItem['class'], $failedJob->data(), $options);
            ConnectionManager::get('default')->disconnect();
        });

        return $worker;
    }
}
