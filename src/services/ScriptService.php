<?php


namespace BonnierDataLayer\Services;


class ScriptService
{
    protected $pluginPath;

    public function __construct($pluginPath)
    {
        $this->pluginPath = $pluginPath;
    }

    public function bootstrap() {
        add_action('wp_enqueue_scripts', [$this, 'loadScript']);
    }

    public function loadScript()
    {
        $scriptSrc = $this->pluginPath . 'assets/datalayer.js';
        wp_register_script('bp-datalayer', $scriptSrc, [], '1.0', false);
        wp_enqueue_script('bp-datalayer');
    }
}