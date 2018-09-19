<?php
namespace Grav\Plugin\Problems;

use Grav\Plugin\Problems\Base\Problem;

class Apache extends Problem
{
    public function __construct()
    {
        $this->id = 'Apache Configuration';
        $this->order = 1;
        $this->level = Problem::LEVEL_CRITICAL;
        $this->status = true;
        $this->help = 'https://learn.getgrav.org/basics/requirements#apache-requirements';
    }

    public function process()
    {
        // Perform some Apache checks
        if (strpos(php_sapi_name(), 'apache') !== false) {

            $require_apache_modules = ['mod_rewrite2'];
            $apache_modules = apache_get_modules();

            $apache_errors = [];
            $apache_success = [];

            foreach ($require_apache_modules as $module) {
                if (in_array($module, $apache_modules)) {
                    $apache_success[$module] = 'Apache module required but not enabled';
                } else {
                    $apache_errors[$module] = 'Apache module is not installed or enabled';
                }
            }

            if (empty($apache_errors)) {
                $this->status = true;
                $this->msg = 'All folders look good!';
            } else {
                $this->status = false;
                $this->msg = 'There were problems with required Apache modules:';
            }

            $this->details = ['errors' => $apache_errors, 'success' => $apache_success];
        }

        return $this;
    }
}
