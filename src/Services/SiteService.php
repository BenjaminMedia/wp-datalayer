<?php

namespace BonnierDataLayer\Services;

use BonnierDataLayer\BonnierDataLayer;
use BonnierDataLayer\Controllers\SettingsController;

class SiteService
{
    /** @var SettingsController */
    protected $settings;

    public function __construct()
    {
        $this->settings = BonnierDataLayer::instance()->getSettings();
    }

    public function brandCode()
    {
        return $this->stringOrNull($this->settings->get_setting_value('brand_code'));
    }

    public function siteType()
    {
        return $this->stringOrNull($this->settings->get_setting_value('site_type'));
    }

    public function pageCMS()
    {
        return $this->stringOrNull($this->settings->get_setting_value('page_cms'));
    }

    public function pageMarket()
    {
        return $this->locale_to_country_code(get_locale());
    }

    public function userLoginStatus()
    {
        if (isset($_COOKIE['bp_oauth_token'])) {
            return 'true';
        }

        return 'false';
    }

    private function locale_to_country_code($locale)
    {
        if (strlen($locale) > 3) {
            return mb_substr($locale, 3, 2);
        }

        return $locale;
    }

    /**
     * Checks if the variable is empty. If it is, return null
     * @param $check
     * @return null | mixed
     */
    private function stringOrNull($check)
    {
        if (empty($check)) {
            return null;
        }

        return $check;
    }
}