<?php

$aliases['production'] = array(
    'uri'              => 'drupal-example.com',
    'db-url'           => 'mysql://usernames:password@localhost/dbname',
    'db-allows-remote' => TRUE,
    'root'             => '/path/to/drupal/root/',
    #'remote-host'      => 'drupal-example.com',
    #'remote-user'      => 'ubuntu',
    #'ssh-options'      => sprintf('-p %d -i %s', 22, dirname(__DIR__) . '/pem/example.pem'),
    #'path-aliases'     => array(
    #    '%files'        => '/path/to/drupal/root/sites/default/files',
    #    '%drush-script' => '/usr/local/bin/drush',
    #),
);

$aliases['staging'] = array(
    'uri'              => 'staging.drupal-example.com',
    'db-url'           => 'mysql://usernames:password@localhost/dbname_staging',
    'db-allows-remote' => TRUE,
    'root'             => '/path/to/drupal-staging/root/',
    #'remote-host'      => 'drupal-example.com',
    #'remote-user'      => 'ubuntu',
    #'ssh-options'      => sprintf('-p %d -i %s', 22, dirname(__DIR__) . '/pem/example.pem'),
    #'path-aliases'     => array(
    #    '%files'        => '/path/to/drupal-staging/root/sites/default/files',
    #    '%drush-script' => '/usr/local/bin/drush',
    #),
);
