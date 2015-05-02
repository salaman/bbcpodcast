<?php

require '../vendor/autoload.php';

use Rhubarb\Exception\TimeoutException;

$options = array(
    'broker' => array(
        'type' => 'Predis',
        'options' => array(
            'exchange' => 'celery',
            'uri' => 'redis://localhost:6379/0',
            'connection' => 'redis://localhost:6379/0',
        )
    ),
    'result_store' => array(
        'type' => 'Predis',
        'options' => array(
            'exchange' => 'celery',
            'uri' => 'redis://localhost:6379/1',
            'connection' => 'redis://localhost:6379/1',
        )
    )
);

$rhubarb = new \Rhubarb\Rhubarb($options);

$task = $rhubarb->sendTask('bbcpodcast.tasks.add', array(2,2));
$task->delay();
$result = $task->get();

echo $result;