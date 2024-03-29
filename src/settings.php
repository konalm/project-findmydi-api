<?php 

return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        'uploadDir' => __DIR__ . '/uploads/',

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        // db settings
        'db' => [
            'host' => 'localhost',
            'user' => 'connor',
            'pass' => '$$superstar',
            'dbname' => 'findmydi_dev',
            'port' => '5432'
        ],
    ],
]; 