<?php
/**
 * Plugin Name: Wp DataLayer
 * Plugin URI: http://bonnierpublications.com
 * Description: WordPress Datalayer implementation
 * Version: 0.1.3
 * Author: Michael Sørensen
 * Author URI: http://bonnierpublications.com
 */

namespace BonnierDataLayer;

use BonnierDataLayer\Controllers\DataLayerController;
use BonnierDataLayer\Controllers\SettingsController;
use BonnierDataLayer\Services\ScriptService;
use BonnierDataLayer\Services\SiteService;

defined('ABSPATH') or die('No script kiddies please!');

require_once(__DIR__ . '/autoload.php');
require_once(__DIR__ . '/vendor/autoload.php');

class BonnierDataLayer
{
    private static $instance;

    private $settings;
    private $datalayer;
    private $script;

    public $file;

    public $basename;

    public $plugin_dir;

    public $plugin_url;

    protected $siteService;

    private function __construct()
    {
        $this->file = __FILE__;
        $this->basename = plugin_basename($this->file);
        $this->plugin_dir = plugin_dir_path($this->file);
        $this->plugin_url = plugin_dir_url($this->file);
    }

    private function bootstrap()
    {
        $this->settings = new SettingsController();
        $this->datalayer = new DataLayerController();
        $this->siteService = new SiteService();
        $this->script = new ScriptService($this->plugin_url);
        $this->script->bootstrap();
    }

    public function getSettings()
    {
        return $this->settings;
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

    public function data()
    {
        return $this->datalayer->gatherData();
    }
}

function instance()
{
    return BonnierDataLayer::instance();
}

add_action('plugins_loaded', __NAMESPACE__.'\instance', 0);