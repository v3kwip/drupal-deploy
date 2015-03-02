<?php

namespace AtPHP\TestCases;

use AtPHP\DrupalDeploy\Worker;
use GuzzleHttp\Client;
use PHPUnit_Framework_TestCase;

class DeployTest extends PHPUnit_Framework_TestCase
{
    private $drupalOriginRoot;
    private $drupalProduction = 'http://localhost:8989';
    private $drupalProductionRoot;
    private $drupalStaging = 'http://localhost:8990';
    private $drupalStagingRoot;
    private $deployInstance = 'http://localhost:8991';

    /** @var  Client */
    private $client;

    /** @var Worker */
    private $worker;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->drupalOriginRoot = APP_ROOT . '/../../drupal-origin';
        $this->drupalProductionRoot = APP_ROOT . '/../../drupal-productdion';
        $this->drupalStagingRoot = APP_ROOT . '/../../drupal-staging';
    }

    private function getClient()
    {
        if (null === $this->client) {
            $this->client = new Client();
        }
        return $this->client;
    }

    public function getWorker()
    {
        if (null === $this->worker) {
            $config = require APP_ROOT . '/config.php';
            $this->worker = new Worker($config);
        }
        return $this->worker;
    }

    /**
     * Make sure the instances are running.
     *
     * @dataProvider dataSourceStatues
     */
    public function testStatuses($path, $code)
    {
        $this->assertEquals($code, $this->getClient()->get($path)->getStatusCode());
    }

    public function dataSourceStatues()
    {
        return [
            [$this->drupalProduction . '/misc/drupal.js', 200],
            [$this->drupalProduction . '/admin', 403],
            [$this->drupalStaging . '/misc/drupal.js', 200],
            [$this->drupalStaging . '/admin', 403],
            [$this->deployInstance, 200]
        ];
    }

    /**
     * Check /info/:projectName/:environment
     *
     * @dataProvider dataSourceDrupalInfo
     */
    public function testDrupalInfo($path, $root)
    {
        $info = json_decode($this->getClient()->get($path)->getBody());
        $this->assertEquals($root, $info['root']);
    }

    public function dataSourceDrupalInfo()
    {
        return [
            [$this->drupalProduction . '/info/example/production', $this->drupalProductionRoot],
            [$this->drupalStaging . '/info/example/staging', $this->drupalStagingRoot],
        ];
    }

    /**
     * Check SQL sync functionality: Sync SQL from production to staging site.
     */
    public function testSQLSync()
    {
        $siteName = 'New production website';

        // Change the site name on production
        $this
            ->getWorker()
            ->drush('@production vset site_name "' . $siteName . '"');

        // Sync SQL from production to staging
        $this
            ->getClient()
            ->get($this->deployInstance . ' /sync-sql/example/production/staging');

        // Check the site name on staging site
        $this->assertEquals($siteName, $this->getWorker()->drush('@staging vget site_name'));
    }

    public function testGitPull()
    {
        // Before git pull, expecting v1.
        $before = trim($this->getClient()->get($this->drupalStaging . '/foo.txt')->getBody());
        $this->assertEquals('v1', $before);

        // Execute git pull web hook
        $this->getClient()->get($this->deployInstance . '/git-pull/example/origin/master');

        // After git pull, expectign v2.
        $after = trim($this->getClient()->get($this->drupalStaging . '/foo.txt')->getBody());
        $this->assertEquals('v2', $after);
    }
}
