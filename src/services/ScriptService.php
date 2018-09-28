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
        wp_register_script('bp-datalayer', $this->pluginPath . 'assets/datalayer.js', [], '1.0', false);
        wp_register_script('bp-datalayer-depth', $this->pluginPath . 'assets/scrollDepthDataLayer.js', [], '1.1', true);

        wp_enqueue_script('bp-datalayer');
        wp_enqueue_script('bp-datalayer-depth');
    }
}