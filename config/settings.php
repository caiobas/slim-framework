<?php

return [
    'settings' => [
        'slim' => [
            'displayErrorDetails' => true,
            'logErrors' => true,
            'logErrorDetails' => true,
        ],
        'swift_mailer' => [
            'host' => $_ENV['MAILER_HOST'] ?? 'smtp.mailtrap.io',
            'port' => intval($_ENV['MAILER_PORT']) ?? 465,
            'username' => $_ENV['MAILER_USERNAME'] ?? 'test',
            'password' => $_ENV['MAILER_PASSWORD'] ?? 'test',
        ],
        'doctrine' => [
            'dev_mode' => true,
            'cache_dir' => __DIR__ . '/../var/doctrine',
            'metadata_dirs' => [__DIR__ . '/../src/Entity'],
            'connection' => [
                'driver' => 'pdo_pgsql',
                'host' => $_ENV['DB_HOST'],
                'port' => $_ENV['DB_PORT'],
                'dbname' => $_ENV['DB_NAME'],
                'user' => $_ENV['DB_USER'],
                'password' => $_ENV['DB_PASS'],
                'charset' => 'utf-8'
            ],
            'migrations' => [
                'table_storage' => [
                    'table_name' => 'doctrine_migration_versions',
                    'version_column_name' => 'version',
                    'version_column_length' => 191,
                    'executed_at_column_name' => 'executed_at',
                    'execution_time_column_name' => 'execution_time',
                ],
                'migrations_paths' => [
                    'migrations' => dirname(__DIR__) . '/migrations',
                ],
                'all_or_nothing' => true,
                'check_database_platform' => true,
            ],
        ],
        'stooq_url' => $_ENV['STOOQ_URL'],
        'rabbitmq' => [
            'connection' => [
                'host' => $_ENV['RMQ_HOST'],
                'port' => $_ENV['RMQ_PORT'],
                'username' => $_ENV['RMQ_USERNAME'],
                'password' => $_ENV['RMQ_PASSWORD'],
            ]
        ]
    ]
];
