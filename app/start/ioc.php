<?php

App::singleton('rhubarb', function() {
    $options = array(
        'broker' => array(
            'type' => 'Predis',
            'options' => array(
                'exchange' => 'celery',
                'connection' => 'redis://localhost:6379/0',
            )
        ),
        'result_store' => array(
            'type' => 'Predis',
            'options' => array(
                'exchange' => 'celery',
                'connection' => 'redis://localhost:6379/0',
            )
        )
    );

    return new \Rhubarb\Rhubarb($options);
});