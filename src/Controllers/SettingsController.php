<?php

namespace BonnierDataLayer\Controllers;

class SettingsController
{
    const SETTINGS_KEY = 'bonnier_datalayer_settings';
    const SETTINGS_GROUP = 'bonnier_datalayer_settings_group';
    const SETTINGS_SECTION = 'bonnier_datalayer_settings_section';
    const SETTINGS_PAGE = 'bonnier_datalayer_settings_page';
    const Settings_PAGE_NAME = 'Bonnier Datalayer';
    const Settings_PAGE_TITLE = 'Bonnier DataLayer settings:';
    const NOTICE_PREFIX = 'Bonnier DataLayer:';

    private $settingsValues;

    private $currentLocale = null;

    private $settingsFields;

    public function __construct()
    {
        $this->bootstrap();
        $this->settingsFields = [
            'brand_code' => [
                'type' => 'text',
                'name' => 'Site brand code',
            ],
            'page_cms' => [
                'type' => 'text',
                'name' => 'CMS name',
            ],
            'site_type' => [
                'type' => 'select',
                'name' => 'Site Type',
                'options' => [
                    [
                        'value' => 'app',
                        'label' => 'App',
                    ],
                    [
                        'value' => 'brand',
                        'label' => 'Brand',
                    ],
                    [
                        'value' => 'corporate',
                        'label' => 'Corporate',
                    ],
                    [
                        'value' => 'shop',
                        'label' => 'Shop',
                    ],
                    [
                        'value' => 'blog',
                        'label' => 'Blog',
                    ],
                    [
                        'value' => 'supportive',
                        'label' => 'Supportive',
                    ],
                ],
            ],
            'disabled' => [
                'type' => 'checkbox',
                'name' => 'Disable GTM tags on site'
            ]
        ];
    }

    private function bootstrap()
    {
        $this->settingsValues = get_option(self::SETTINGS_KEY);
        add_action('admin_menu', [$this, 'add_plugin_page']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function get_brand_code($locale = null)
    {
        return $this->get_setting_value('brand_code', $locale) ?: '';
    }

    public function languages_is_enabled()
    {
        return function_exists('Pll') && PLL()->model->get_languages_list();
    }

    public function get_localized_setting_key($settingKey, $locale = null)
    {
        if ($locale === null && $this->languages_is_enabled()) {
            $locale = $this->get_current_locale();
        }

        if ($locale) {
            return $this->locale_to_lang_code($locale) . '_' . $settingKey;
        }

        return $settingKey;
    }

    public function get_current_locale()
    {
        if ($this->currentLocale !== null) {
            return $this->currentLocale;
        }
        $currentLang = $this->get_current_language();

        return $currentLang ? $currentLang->locale : null;
    }

    /**
     * Get the current language by looking at the current HTTP_HOST
     *
     * @return null|PLL_Language
     */
    public function get_current_language()
    {
        if ($this->languages_is_enabled()) {
            return PLL()->model->get_language(pll_current_language());
        }
        return null;
    }

    /**
     * Returns the language code from locale ie. 'da_DK' becomes 'da'
     *
     * @param $locale
     * @return string
     */
    public function locale_to_lang_code($locale)
    {
        return substr($locale, 0, 2);
    }

    public function get_languages()
    {
        if ($this->languages_is_enabled()) {
            return PLL()->model->get_languages_list();
        }
        return false;
    }

    private function enable_language_fields()
    {
        $languageEnabledFields = [];

        foreach ($this->get_languages() as $language) {
            foreach ($this->settingsFields as $fieldKey => $settingsField) {
                $localeFieldKey = $this->get_localized_setting_key($fieldKey, $language->locale);
                $languageEnabledFields[$localeFieldKey] = $settingsField;
                $languageEnabledFields[$localeFieldKey]['name'] .= ' ' . $language->locale;
                $languageEnabledFields[$localeFieldKey]['locale'] = $language->locale;
            }
        }

        $this->settingsFields = $languageEnabledFields;
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin',
            self::Settings_PAGE_NAME,
            'manage_options',
            self::SETTINGS_PAGE,
            [$this, 'create_admin_page']
        );
    }

    /**
     * Register and add settings
     */
    public function register_settings()
    {
        if ($this->languages_is_enabled()) {
            $this->enable_language_fields();
        }

        register_setting(
            self::SETTINGS_GROUP, // Option group
            self::SETTINGS_KEY, // Option name
            [$this, 'sanitize'] // Sanitize
        );

        add_settings_section(
            self::SETTINGS_SECTION, // ID
            self::Settings_PAGE_TITLE, // Title
            [$this, 'print_section_info'], // Callback
            self::SETTINGS_PAGE // Page
        );

        foreach ($this->settingsFields as $settingsKey => $settingField) {
            add_settings_field(
                $settingsKey, // ID
                $settingField['name'], // Title
                [$this, $settingsKey], // Callback
                self::SETTINGS_PAGE, // Page
                self::SETTINGS_SECTION // Section
            );
        }
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     * @return array
     */
    public function sanitize($input)
    {
        $sanitizedInput = [];
        foreach ($this->settingsFields as $fieldKey => $settingsField) {
            if (isset($input[$fieldKey])) {
                if ($settingsField['type'] === 'checkbox') {
                    $sanitizedInput[$fieldKey] = absint($input[$fieldKey]);
                }
                if ($settingsField['type'] === 'text' || $settingsField['type'] === 'select') {
                    $sanitizedInput[$fieldKey] = sanitize_text_field($input[$fieldKey]);
                }
            }
        }
        return $sanitizedInput;
    }

    public function print_error($error)
    {
        $out = "<div class='error settings-error notice is-dismissible'>";
        $out .= "<strong>" . self::NOTICE_PREFIX . "</strong><p>$error</p>";
        $out .= "</div>";
        print $out;
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /**
     * Catch callbacks for creating setting fields
     * @param string $function
     * @param array $arguments
     * @return bool
     */
    public function __call($function, $arguments)
    {
        if ( !isset($this->settingsFields[$function])) {
            return false;
        }

        $field = $this->settingsFields[$function];
        $this->create_settings_field($field, $function);
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property?>
        <div class="wrap">
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields(self::SETTINGS_GROUP);
                do_settings_sections(self::SETTINGS_PAGE);
                submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function get_setting_value($settingKey, $locale = null)
    {
        if (!$this->settingsValues) {
            $this->settingsValues = get_option(self::SETTINGS_KEY);
        }

        $settingKey = $this->get_localized_setting_key($settingKey, $locale);
        if (isset($this->settingsValues[$settingKey]) && !empty($this->settingsValues[$settingKey])) {
            return $this->settingsValues[$settingKey];
        }
        return false;
    }

    private function get_select_field_options($field)
    {
        if (isset($field['options_callback'])) {
            $options = $this->{$field['options_callback']}($field['locale']);
            if ($options) {
                return $options;
            }
        }

        if (isset($field['options'])) {
            return $field['options'];
        }

        return [];
    }

    private function create_settings_field($field, $fieldKey)
    {
        $fieldName = self::SETTINGS_KEY . "[$fieldKey]";
        $fieldOutput = false;
        if ($field['type'] === 'text') {
            $fieldValue = isset($this->settingsValues[$fieldKey]) ? esc_attr($this->settingsValues[$fieldKey]) : '';
            $fieldOutput = "<input type='text' name='$fieldName' value='$fieldValue' class='regular-text' />";
        }
        if ($field['type'] === 'checkbox') {
            $checked = isset($this->settingsValues[$fieldKey]) && $this->settingsValues[$fieldKey] ? 'checked' : '';
            $fieldOutput = "<input type='hidden' value='0' name='$fieldName'>";
            $fieldOutput .= "<input type='checkbox' value='1' name='$fieldName' $checked />";
        }
        if ($field['type'] === 'select') {
            $fieldValue = isset($this->settingsValues[$fieldKey]) ? $this->settingsValues[$fieldKey] : '';
            $fieldOutput = "<select name='$fieldName'>";
            $options = $this->get_select_field_options($field);

            foreach ($options as $option) {

                $selected = ($option['value'] === $fieldValue) ? 'selected' : '';
                $fieldOutput .= "<option value='" . $option['value'] . "' $selected >" . $option['label'] . "</option>";
            }
            $fieldOutput .= "</select>";
        }
        if ($field['type'] === 'callback') {
            $fieldValue = isset($this->settingsValues[$fieldKey]) ? $this->settingsValues[$fieldKey] : [];
            call_user_func_array($field['callback'], [$fieldName, $fieldValue]);
        }
        if ($fieldOutput) {
            print $fieldOutput;
        }
    }
}