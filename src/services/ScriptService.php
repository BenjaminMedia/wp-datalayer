<?php

namespace BonnierDataLayer\Services;

use BonnierDataLayer\BonnierDataLayer;

class ScriptService
{
    protected $pluginPath;

    public function __construct($pluginPath)
    {
        $this->pluginPath = $pluginPath;
    }

    public function bootstrap() {
        add_action('wp_enqueue_scripts', [$this, 'loadScript'], 1);
        add_action('wp_head', [$this, 'gtmContainer'], 10);
    }

    public function loadScript()
    {
        wp_register_script('bp-datalayer', $this->pluginPath . 'assets/datalayer.js', [], '1.0', false);
        wp_localize_script('bp-datalayer', 'bpDatalayer', BonnierDataLayer::instance()->data());

        wp_register_script('bp-datalayer-depth', $this->pluginPath . 'assets/scrollDepthDataLayer.js', [], '1.1', true);
        wp_register_script('common-datalayer', '//europe-west1-bonnier-big-data.cloudfunctions.net/commonBonnierDataLayer', [], '1.0', false);

        wp_enqueue_script('bp-datalayer');
        wp_enqueue_script('bp-datalayer-depth');
        wp_enqueue_script('common-datalayer');
    }

    public function gtmContainer()
    {
        $gtmContainerId = getenv('GTM_CONTAINER_ID') ? getenv('GTM_CONTAINER_ID') : false;

        $gtm = '<!-- Google Tag Manager wp-datalayer -->';
        $gtm .= '
<script data-cfasync="false">//<![CDATA[
(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({\'gtm.start\':
new Date().getTime(),event:\'gtm.js\'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!=\'dataLayer\'?\'&l=\'+l:\'\';j.async=true;j.src=
\'https://www.googletagmanager.com/gtm.js?id=\'+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,\'script\',\'dataLayer\',\'' . $gtmContainerId . '\');//]]>
</script>';
        $gtm .= '<!-- End Google Tag Manager -->';
        echo $gtm;
    }
}