<?php
namespace Grav\Plugin\Problems\Base;

class ProblemChecker
{
    protected $problems = [];

    public function check($problems_dir)
    {
        $problems = [];
        $problems_found = false;

        foreach (new \DirectoryIterator($problems_dir) as $file) {
            if ($file->isDot() || $file->isDir()) {
                continue;
            }
            $classname = 'Grav\\Plugin\\Problems\\' . $file->getBasename('.php');
            $problem = new $classname();
            $problems[$problem->getOrder()] = $problem;
        }

        // Get the problems in order
        krsort($problems);

        // run the process methods in new order
        foreach ($problems as $problem) {
            $problem->process();
            if ($problem->getStatus() === false) {
                $problems_found = true;
            }
        }

        $this->problems = $problems;

        return $problems_found;
    }

    public function getProblems()
    {
        if (empty($this->problems)) {
            $this->check();
        }

        $problems = $this->problems;

        usort($problems, function($a, $b) {
            return $a->getStatus() - $b->getStatus();
        });

        return $problems;

    }

}