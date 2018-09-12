<?php
/**
 * Plugin Name: Wp DataLayer
 * Plugin URI: http://bonnierpublications.com
 * Description: WordPress Datalayer implementation
 * Version: 0.0.1
 * Author: Michael Sørensen
 * Author URI: http://bonnierpublications.com
 */

namespace BonnierDataLayer;

use BonnierDataLayer\Controllers\SettingsController;

defined('ABSPATH') or die('No script kiddies please!');

require_once(__DIR__ . '/autoload.php');
require_once(__DIR__ . '/vendor/autoload.php');

class BonnierDataLayer
{
    private static $instance;

    public $settings;

    public $file;

    public $basename;

    public $plugin_dir;

    public $plugin_url;

    private function __construct()
    {
        $this->file = __FILE__;
        $this->basename = plugin_basename($this->file);
        $this->plugin_dir = plugin_dir_path($this->file);
        $this->plugin_url = plugin_dir_url($this->file);
        $this->settings = new SettingsController();
    }

    private function bootstrap()
    {
        //Post::watch_post_changes($this->settings);
        //CacheApi::bootstrap($this->settings);
        //PostMetaBox::register($this->settings);
    }

    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
            global $bonnierDataLayer;
            $bonnierDataLayer = self::$instance;
            self::$instance->bootstrap();

            do_action('bonnier_datalayer_loaded');
        }

        return self::$instance;
    }
}

function instance()
{
    return BonnierDataLayer::instance();
}

add_action('plugins_loaded', __NAMESPACE__.'\instance', 0);