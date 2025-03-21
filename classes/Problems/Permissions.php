<?php

namespace Grav\Plugin\Problems;

use Grav\Plugin\Problems\Base\Problem;

/**
 * Class Permissions
 * @package Grav\Plugin\Problems
 */
class Permissions extends Problem
{
    public function __construct()
    {
        $this->id = 'Permissions Setup';
        $this->class = get_class($this);
        $this->order = -1;
        $this->level = Problem::LEVEL_WARNING;
        $this->status = false;
        $this->help = 'https://learn.getgrav.org/troubleshooting/permissions';
    }

    /**
     * @return $this
     */
    public function process()
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $this->msg = 'Permission check is not available for Windows.';
            $this->status = true;
            return $this;
        }

        umask($umask = umask(022));

        $msg = 'Your default file umask is <strong>%s</strong> which %s';

        if (($umask & 2) !== 2) {
            $this->msg = sprintf($msg, decoct($umask), 'is potentially dangerous');
            $this->status = false;
        } else {
            $this->msg = sprintf($msg, decoct($umask), 'looks good!');
            $this->status = true;
        }

        return $this;
    }
}