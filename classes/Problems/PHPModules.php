<?php

namespace Grav\Plugin\Problems;

use Grav\Common\Grav;
use Grav\Plugin\Problems\Base\Problem;

/**
 * Class PHPModules
 * @package Grav\Plugin\Problems
 */
class PHPModules extends Problem
{
    protected $modules_errors  = [];
    protected $modules_warning = [];
    protected $modules_success = [];

    public function __construct()
    {
        $this->id = 'PHP Modules';
        $this->class = get_class($this);
        $this->order = 101;
        $this->level = Problem::LEVEL_CRITICAL;
        $this->status = false;
        $this->help = 'https://learn.getgrav.org/basics/requirements#php-requirements';
    }

    /**
     * @param string $module           PHP module name.
     * @param bool   $required         If it is required for grav.
     * @param string $module_show_name More common module name to display.
     * @return void
     */
    protected function check_php_module(string $module, bool $required, string $module_show_name = ''): void{
        $msg = 'PHP ';
        $msg .= (($module_show_name!=='') ? $module_show_name : $module);
        $msg .= ' is %s installed';
        if(extension_loaded($module)){
            $this->modules_success[$module] = sprintf($msg, 'successfully');
        }else if($required){
            $this->modules_errors[$module] = sprintf($msg, 'required but not');
        }else{
            $this->modules_warning[$module] = sprintf($msg, 'recommended but not');
        }
    }

    /**
     * @param string $module           PHP cache module name.
     * @param string $module_show_name More common module name to display.
     * @return void
     */
    protected function check_cache_module(string $module, string $module_show_name = ''): void{
        $msg = 'PHP (optional) Cache ';
        $msg .= (($module_show_name!=='') ? $module_show_name : $module);
        $msg .= ' is %s installed';
        if( extension_loaded($module) ){
            $this->modules_success[$module] = sprintf($msg, 'successfully');
        } else {
            $this->modules_warning[$module] = sprintf($msg, 'not');
        }
    }

    /**
     * @return $this
     */
    public function process()
    {
        // Check for PHP CURL library
        $this->check_php_module('curl', true, 'Curl (Data Transfer Library)');

        // Check for PHP Ctype library
        $this->check_php_module('ctype', true, 'Ctype');

        // Check for PHP Dom library
        $this->check_php_module('dom', true, 'DOM');

        // Check for PHP fileinfo library
        $this->check_php_module('fileinfo', false);

        // Check for GD library
        $msg = 'PHP GD (Image Manipulation Library) is %s installed';
        if (defined('GD_VERSION') && function_exists('gd_info')) {

            $msg = sprintf($msg, 'successfully');

            // Extra checks for Image support
            $ginfo = gd_info();
            $gda = array('PNG Support', 'JPEG Support', 'FreeType Support', 'GIF Read Support', 'WebP Support', 'AVIF Support');
            $gda_msg = '';
            $problems_found = false;

            foreach ($gda as $image_type) {
                if (!array_key_exists($image_type, $ginfo)) {
                    $problems_found = true;
                    if($gda_msg !== '') {
                        $gda_msg .= ', ';
                    }
                    $gda_msg .= $image_type;
                }
            }

            if ($problems_found) {
                $this->modules_warning['gd'] = $msg . ' but missing ' . $gda_msg;
            }

            $this->modules_success['gd'] = $msg;
        } else {
            $this->modules_errors['gd'] = sprintf($msg, 'required but not');
        }

        // Check for PHP MbString library
        $this->check_php_module('mbstring', true, 'Mbstring (Multibyte String Library)');

        // Check for PHP iconv library
        $this->check_php_module('iconv', false);

        // Check for PHP intl library
        $required = Grav::instance()['config']->get('system.intl_enabled');
        $this->check_php_module('intl', $required, 'intl (Internationalization Functions)');

        // Check for PHP Open SSL library
        $this->check_php_module('openssl', true, 'OpenSSL (Secure Sockets Library)');

        // Check for PHP JSON library
        $this->check_php_module('json', true, 'JSON Library');

        // Check for PHP libraries for symfony
        $this->check_php_module('PCRE', true, 'PCRE (Perl Compatible Regular Expressions)');
        $this->check_php_module('session', true);

        // Check for PHP XML libraries
        $this->check_php_module('libxml', true, 'libxml Library');
        $this->check_php_module('simplexml', true, 'SimpleXML Library');
        $this->check_php_module('xml', true, 'XML Library');

        // Check for PHP yaml library
        $this->check_php_module('yaml', false);

        // Check for PHP Zip library
        $this->check_php_module('zip', true, 'Zip extension');

        // Check Exif if enabled
        $required = Grav::instance()['config']->get('system.media.auto_metadata_exif');
        $this->check_php_module('exif', $required, 'Exif (Exchangeable Image File Format)');

        // Check cache modules
        $this->check_cache_module('apcu', 'APC User Cache');
        $this->check_cache_module('memcache');
        $this->check_cache_module('memcached');
        $this->check_cache_module('redis');
        $this->check_cache_module('wincache', 'WinCache');
        $this->check_cache_module('zend opcache', 'Zend OPcache');

        if (empty($this->modules_errors)) {
            $this->status = true;
            $this->msg = 'All required modules look good!';
            if(!empty($this->modules_warning)) {
                $this->msg .= ' Some recommendations do exist.';
            }
        } else {
            $this->status = false;
            $this->msg = 'There were problems with required modules:';
        }

        $this->details = ['errors' => $this->modules_errors, 'warning' => $this->modules_warning, 'success' => $this->modules_success];

        return $this;
    }
}

