<?php

use AtPHP\DrupalDeploy\Application;

require_once dirname(__DIR__) . '/vendor/autoload.php';

(new Application(require_once dirname(__DIR__) . '/config.php'))->run();
