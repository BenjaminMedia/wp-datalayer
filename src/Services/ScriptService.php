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

    public function bootstrap()
    {
        add_action('wp_enqueue_scripts', [$this, 'loadScript'], 1);
        add_action('wp_head', [$this, 'gtmContainer'], 10);
        add_action('gtm_body', [$this, 'gtmBody'], 10);
    }

    public function loadScript()
    {
        if (!BonnierDataLayer::instance()->getSettings()->get_setting_value('disabled')) {
            wp_register_script('common-datalayer', '//europe-west1-bonnier-big-data.cloudfunctions.net/commonBonnierDataLayer', [], '1.0', false);
            wp_enqueue_script('common-datalayer');
        }
        wp_register_script('bp-datalayer', $this->pluginPath . 'assets/datalayer.js', [], '1.0', false);
        wp_localize_script('bp-datalayer', 'bpDatalayer', BonnierDataLayer::instance()->data());

        wp_register_script('bp-datalayer-depth', $this->pluginPath . 'assets/scrollDepthDataLayer.js', [], '1.1', true);

        wp_enqueue_script('bp-datalayer');
        wp_enqueue_script('bp-datalayer-depth');
    }

    public function gtmContainer()
    {
        if (BonnierDataLayer::instance()->getSettings()->get_setting_value('disabled')) {
            return;
        }

        $gtmContainerId = $this->getGtmCotainerId();

        if (!$gtmContainerId) {
            return;
        }

        $gtm = '<!-- Google Tag Manager -->';
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

    public function gtmBody()
    {
        if (BonnierDataLayer::instance()->getSettings()->get_setting_value('disabled')) {
            return;
        }

        $gtmContainerId = $this->getGtmCotainerId();

        if (!$gtmContainerId) {
            return;
        }

        $gtm = '<!-- Google Tag Manager (noscript) -->';
        $gtm .= '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . $gtmContainerId . '" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>';
        $gtm .= '<!-- End Google Tag Manager (noscript) -->';
        echo $gtm;
    }

    private function getGtmCotainerId()
    {
        if ($this->getEnvGtmContainerId() !== false) {
            return $this->getEnvGtmContainerId();
        }

        if ($this->getDbGtmCotainerId() !== false) {
            return $this->getDbGtmCotainerId();
        }

        return false;
    }

    private function getEnvGtmContainerId()
    {
        return getenv('GTM_CONTAINER_ID');
    }

    private function getDbGtmCotainerId()
    {
        return !empty(BonnierDataLayer::instance()->getSettings()->get_setting_value('gtm_code'))
            ? BonnierDataLayer::instance()->getSettings()->get_setting_value('gtm_code')
            : false;
    }
}