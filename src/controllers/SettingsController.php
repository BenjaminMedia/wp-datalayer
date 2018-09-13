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

    private $settingsFields;

    public function __construct()
    {
        $this->bootstrap();
        $this->settingsFields = [
            'brand_code' => [
                'type' => 'text',
                'name' => 'Site brand code',
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
        ];
    }

    private function bootstrap()
    {
        $this->settingsValues = get_option(self::SETTINGS_KEY);
        add_action('admin_menu', [$this, 'add_plugin_page']);
        add_action('admin_init', [$this, 'register_settings']);
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

        return true;
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

    public function get_setting_value($settingKey)
    {
        if (!$this->settingsValues) {
            $this->settingsValues = get_option(self::SETTINGS_KEY);
        }
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