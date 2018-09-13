<?php

namespace BonnierDataLayer\Services;

use BonnierDataLayer\BonnierDataLayer;

class SiteService
{
    protected $settings;

    public function __construct()
    {
        $this->settings = BonnierDataLayer::instance()->getSettings();
    }

    public function brandCode()
    {
        return $this->settings->get_setting_value('brand_code');
    }

    public function siteType()
    {
        return $this->settings->get_setting_value('site_type');
    }
}