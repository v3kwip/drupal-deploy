<?php

return [
    'drush'    => '/usr/local/bin/drush',
    'git'      => '/usr/bin/git',
    'aliases'  => __DIR__ . '/files/aliases',
    'token'    => 'SAMPLE_TOKEN',
    'debug'    => true,
    'projects' => [
        'caaa' => [
            'production' => 'caaa.production',
            'staging'    => 'caaa.staging',
        ],
    ],
];
