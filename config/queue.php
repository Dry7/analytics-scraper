<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Queue Connection Name
    |--------------------------------------------------------------------------
    |
    | Laravel's queue API supports an assortment of back-ends via a single
    | API, giving you convenient access to each back-end using the same
    | syntax for every one. Here you may define a default connection.
    |
    */

    'default' => env('QUEUE_DRIVER', 'sync'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection information for each server that
    | is used by your application. A default configuration has been added
    | for each back-end shipped with Laravel. You are free to add more.
    |
    | Drivers: "sync", "database", "beanstalkd", "sqs", "redis", "null"
    |
    */

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 90,
        ],

        'beanstalkd' => [
            'driver' => 'beanstalkd',
            'host' => 'localhost',
            'queue' => 'default',
            'retry_after' => 90,
        ],

        'sqs' => [
            'driver' => 'sqs',
            'key' => env('SQS_KEY', 'your-public-key'),
            'secret' => env('SQS_SECRET', 'your-secret-key'),
            'prefix' => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/your-account-id'),
            'queue' => env('SQS_QUEUE', 'your-queue-name'),
            'region' => env('SQS_REGION', 'us-east-1'),
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'default',
            'retry_after' => 90,
            'block_for' => null,
        ],

        'rabbitmq' => [
            'driver' => 'rabbitmq',
            'dsn' => env('RABBITMQ_DSN', null),
            /*
             * Could be one a class that implements \Interop\Amqp\AmqpConnectionFactory for example:
             *  - \EnqueueAmqpExt\AmqpConnectionFactory if you install enqueue/amqp-ext
             *  - \EnqueueAmqpLib\AmqpConnectionFactory if you install enqueue/amqp-lib
             *  - \EnqueueAmqpBunny\AmqpConnectionFactory if you install enqueue/amqp-bunny
             */
            'factory_class' => Enqueue\AmqpLib\AmqpConnectionFactory::class,
            'host' => env('RABBITMQ_HOST', '127.0.0.1'),
            'port' => env('RABBITMQ_PORT', 5672),
            'vhost' => env('RABBITMQ_VHOST', '/'),
            'login' => env('RABBITMQ_LOGIN', 'admin'),
            'password' => env('RABBITMQ_PASSWORD', 'admin'),
            'options' => [
                'exchange' => [
                    'name' => env('RABBITMQ_EXCHANGE_NAME'),
                    /*
                    * Determine if exchange should be created if it does not exist.
                    */
                    'declare' => env('RABBITMQ_EXCHANGE_DECLARE', true),
                    /*
                    * Read more about possible values at https://www.rabbitmq.com/tutorials/amqp-concepts.html
                    */
                    'type' => env('RABBITMQ_EXCHANGE_TYPE', \Interop\Amqp\AmqpTopic::TYPE_DIRECT),
                    'passive' => env('RABBITMQ_EXCHANGE_PASSIVE', false),
                    'durable' => env('RABBITMQ_EXCHANGE_DURABLE', true),
                    'auto_delete' => env('RABBITMQ_EXCHANGE_AUTODELETE', false),
                    'arguments' => env('RABBITMQ_EXCHANGE_ARGUMENTS'),
                ],
                'queue' => [
                    /*
                    * The name of default queue.
                    */
                    'name' => env('RABBITMQ_QUEUE', 'default'),
                    /*
                    * Determine if queue should be created if it does not exist.
                    */
                    'declare' => env('RABBITMQ_QUEUE_DECLARE', true),
                    /*
                    * Determine if queue should be binded to the exchange created.
                    */
                    'bind' => env('RABBITMQ_QUEUE_DECLARE_BIND', true),
                    /*
                    * Read more about possible values at https://www.rabbitmq.com/tutorials/amqp-concepts.html
                    */
                    'passive' => env('RABBITMQ_QUEUE_PASSIVE', false),
                    'durable' => env('RABBITMQ_QUEUE_DURABLE', true),
                    'exclusive' => env('RABBITMQ_QUEUE_EXCLUSIVE', false),
                    'auto_delete' => env('RABBITMQ_QUEUE_AUTODELETE', false),
                    'arguments' => env('RABBITMQ_QUEUE_ARGUMENTS'),
                ],
            ],
            /*
             * Determine the number of seconds to sleep if there's an error communicating with rabbitmq
             * If set to false, it'll throw an exception rather than doing the sleep for X seconds.
             */
            'sleep_on_error' => env('RABBITMQ_ERROR_SLEEP', 5),
            /*
             * Optional SSL params if an SSL connection is used
             */
            'ssl_params' => [
                'ssl_on' => env('RABBITMQ_SSL', false),
                'cafile' => env('RABBITMQ_SSL_CAFILE', null),
                'local_cert' => env('RABBITMQ_SSL_LOCALCERT', null),
                'local_key' => env('RABBITMQ_SSL_LOCALKEY', null),
                'verify_peer' => env('RABBITMQ_SSL_VERIFY_PEER', true),
                'passphrase' => env('RABBITMQ_SSL_PASSPHRASE', null),
            ],
        ]

    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of failed queue job logging so you
    | can control which database and table are used to store the jobs that
    | have failed. You may change them to any database / table you wish.
    |
    */

    'failed' => [
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'failed_jobs',
    ],

];
