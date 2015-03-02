<?php

namespace AtPHP\DrupalDeploy;

use Symfony\Component\Process\Process;

class Worker
{

    private $data;
    private $projectName;
    private $drushOpen = 'DRUSH_BACKEND_OUTPUT_START>>>';
    private $drushClose = '<<<DRUSH_BACKEND_OUTPUT_END';

    public function __construct(array $data, $token)
    {
        if (!empty($data['token']) && ($token !== $data['token'])) {
            throw new RuntimeException('Invalid token: ' . $token);
        }
        $this->data = $data;
    }

    public function setProjectName($projectName)
    {
        $this->projectName = $projectName;
        return $this;
    }

    public function getProjectProductionName()
    {
        return $this->data['projects'][$this->projectName]['production'];
    }

    public function getProjectStagingName()
    {
        return $this->data['projects'][$this->projectName]['staging'];
    }

    /**
     * Run git command on remote host. We run this using Drush because it
     * provides very nice 'ssh' command:
     *
     *  drush @site ssh \
     *      'git  --working-tree=/path/to/x --git-dir=/path/to/x.git $GIT_COMMAND'
     *
     * @TODO Replace `git` with configurable value.
     * @param string $target
     * @param string $command
     * @return mixed|string
     */
    public function git($target, $command)
    {
        $workingTree = '@TODO';
        $gitDir = '@TODO';
        $git = sprintf('git --working-tree=%s --git-dir=%s', $workingTree, $gitDir);
        return $this->drush(" @{$target} ssh {$git} {$command}");
    }

    /**
     * Run drush command.
     *
     * @param $cmd
     * @return array|string
     */
    public function drush($cmd)
    {
        $drush = $this->data['drush'] . ' --backend --alias-path=' . dirname(__DIR__) . '/files/aliases';
        $process = new Process("$drush $cmd --format=json");
        $process->run();
        $output = $process->getOutput();
        if ((FALSE === strpos($output, $this->drushOpen)) || (FALSE === strpos($output, $this->drushClose))) {
            return $output;
        }
        return $this->parseOutput($output);
    }

    /**
     * Parse drush output to object.
     *
     * @param string $output
     * @return array
     */
    private function parseOutput($output)
    {
        $start = strpos($output, $this->drushOpen) + strlen($this->drushOpen);
        $length = strpos($output, $this->drushClose) - $start;
        return json_decode(substr($output, $start, $length));
    }
}
