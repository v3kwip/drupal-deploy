<?php

namespace AtPHP\DrupalDeploy;

use Silex\Application as App;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class Application extends App
{

    private $userSettings = [];

    /** @var Worker */
    private $worker;

    public function __construct(array $values = [])
    {
        $this->userSettings = $values;
        parent::__construct($values);

        $this->worker = new Worker($values, $this['token']);
        $this->configRouting();
    }

    private function configRouting()
    {
        $this->get('/', [$this, 'onGetDefault']);
        $this->get('/info/{project}/{source}', [$this, 'onGetInfo']);
        $this->get('/sync-sql/{project}/{source}/{target}', [$this, 'onGetSyncSQL']);
        $this->get('/git-pull/{project}/{target}/{branch}', [$this, 'onGetPull']);
    }

    public function onGetDefault(Request $request)
    {
        if (!($cmds = $request->get('cmds')) && !is_array($cmds)) {
            return $this->json([]);
        }

        /* @var $response Response */
        foreach (array_values(array_unique($cmds)) as $cmd) {
            $request = Request::create('/' . trim($cmd, '/'));
            $response = $this->handle($request, HttpKernelInterface::SUB_REQUEST);
            $responses[$cmd] = [
                'status'  => $response->getStatusCode(),
                'content' => $response->getContent(),
            ];
        }

        return $this->json($responses);
    }

    public function onGetInfo($project, $source)
    {
        $cmd = strtr('@:source status', [':source' => $source]);
        $info = $this
            ->worker
            ->setProjectName($project)
            ->drush($cmd);
        return is_string($info) ? $info : $this->json($info);
    }

    public function onGetSyncSQL($project, $source = null, $target = null)
    {
        $cmd = strtr('sql-sync @:source @:target', [
            ':source' => null === $source ? $this->worker->getProjectProductionName() : $source,
            ':target' => null === $target ? $this->worker->getProjectStagingName() : $target
        ]);

        return $this
            ->worker
            ->setProjectName($project)
            ->drush($cmd);
    }

    public function onGetPull($project, $target = null, $branch = null)
    {
        $cmd = "pull {$branch}";

        return $this
            ->worker
            ->setProjectName($project)
            ->git($target ? $target : $this->worker->getProjectStagingName(), $cmd);
    }
}
