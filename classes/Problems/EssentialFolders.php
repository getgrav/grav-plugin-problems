<?php

namespace Grav\Plugin\Problems;

use Grav\Plugin\Problems\Base\Problem;

class EssentialFolders extends Problem
{
    public function __construct()
    {
        $this->id = 'Essential Folders';
        $this->class = get_class($this);
        $this->order = 100;
        $this->level = Problem::LEVEL_CRITICAL;
        $this->status = false;
        $this->help = 'https://learn.getgrav.org/basics/folder-structure';
    }

    public function process()
    {
        $essential_folders = [
            (str_starts_with(GRAV_BACKUP_PATH, '/') ? '' : ROOT_DIR) . GRAV_BACKUP_PATH => true,
            (str_starts_with(GRAV_CACHE_PATH, '/') ? '' : ROOT_DIR) . GRAV_CACHE_PATH => true,
            (str_starts_with(GRAV_LOG_PATH, '/') ? '' : ROOT_DIR) . GRAV_LOG_PATH => true,
            GRAV_WEBROOT . '/images' => true,
            GRAV_WEBROOT . '/assets' => true,
            (str_starts_with(GRAV_SYSTEM_PATH, '/') ? '' : ROOT_DIR) . GRAV_SYSTEM_PATH => false,
            USER_DIR . 'data' => true,
            USER_DIR . 'pages' => false,
            USER_DIR . 'config' => false,
            USER_DIR . 'plugins/error' => false,
            USER_DIR . 'plugins' => false,
            USER_DIR . 'themes' => false,
            ROOT_DIR . 'vendor' => false,
            (str_starts_with(GRAV_TMP_PATH, '/') ? '' : ROOT_DIR) . GRAV_TMP_PATH => true,
        ];

        // Check for essential files & perms
        $file_errors = [];
        $file_success = [];

        foreach ($essential_folders as $file_path => $check_writable) {
            if (!file_exists($file_path)) {
                $file_errors[$file_path] = 'does not exist';
            } elseif (!$check_writable) {
                $file_success[$file_path] = 'exists';
            } else {
                if (is_writable($file_path)) {
                    $file_success[$file_path] = 'exists and is writable';
                } else {
                    $file_errors[$file_path] = 'exists but is <strong>not writeable</strong>';
                }
            }
        }

        if (empty($file_errors)) {
            $this->status = true;
            $this->msg = 'All folders look good!';
        } else {
            $this->status = false;
            $this->msg = 'There were problems with required folders:';
        }

        $this->details = ['errors' => $file_errors, 'success' => $file_success];

        return $this;
    }
}
