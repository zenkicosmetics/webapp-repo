<?php defined('BASEPATH') or exit('No direct script access allowed');

class Settings
{
    protected $ci;
    /**
     * Settings cache
     *
     * @var array
     */
    private static $cache = array();
    /**
     * The settings table columns
     *
     * @var array
     */
    private $columns = array(
        'SettingKey',
        'SettingCode',
        'DefaultValue',
        'ActualValue',
        'LabelValue',
        'ModuleName',
        'SettingOrder',
        'IsRequired',
        'Description'
    );

    /**
     * The Settings Construct
     */
    public function __construct()
    {
        ci()->load->model('settings/settings_m');
        ci()->load->library(array(
            'pyrocache'
        ));

        $this->ci = &get_instance();
        $this->ci->lang->load('settings/settings');

        $this->get_all();
    }

    /**
     * Getter Gets the setting value requested
     *
     * @param string $name
     */
    public function __get($name)
    {
        return self::get($name);
    }

    /**
     * Setter Sets the setting value requested
     *
     * @param string $name
     * @param string $value
     * @return bool
     */
    public function __set($name, $value)
    {
        return self::set($name, $value);
    }

    /**
     * Gets a setting.
     *
     * @param string $name
     * @return ActualValue field of Settings table.
     */
    public static function get($code)
    {
        if (isset(self::$cache [$code])) {
            return self::$cache [$code];
        }

        $setting = ci()->settings_m->get_by(array(
            'SettingCode' => $code
        ));

        // Setting doesn't exist, maybe it's a config option
        $value = $setting ? $setting->ActualValue : config_item($code);

        // Store it for later
        self::$cache [$code] = $value;

        return $value;
    }

    /**
     * Gets a label of value.
     *
     * @param string $name
     * @return ActualValue field of Settings table.
     */
    public static function get_label($code, $value)
    {
        $list_value = Settings::get_list($code);
        if (!empty($list_value)) {
            foreach ($list_value as $val) {
                if ($val->ActualValue === $value) {
                    return $val->LabelValue;
                }
            }
        }
        return "";
    }
    
    /**
     * Gets a label of value.
     *
     * @return ActualValue field of Settings table.
     */
    public static function getAlias01ByCode($code)
    {
        $setting = ci()->settings_m->get_by(array(
            'SettingCode' => $code
        ));

        // Setting doesn't exist, maybe it's a config option
        $value = $setting ? $setting->Alias01 : '';
        return $value;

    }

    /**
     * Gets a label of value.
     *
     * @param string $name
     * @return ActualValue field of Settings table.
     */
    public static function get_alias01($code, $value)
    {
        $list_value = Settings::get_list($code);
        if (!empty($list_value)) {
            foreach ($list_value as $val) {
                if ($val->ActualValue === $value) {
                    return $val->Alias01;
                }
            }
        }
        return "";
    }

    /**
     * Gets a label of value.
     *
     * @param string $name
     * @return ActualValue field of Settings table.
     */
    public static function get_alias02($code, $value)
    {
        $list_value = Settings::get_list($code);
        if (!empty($list_value)) {
            foreach ($list_value as $val) {
                if ($val->ActualValue === $value) {
                    return $val->Alias02;
                }
            }
        }
        return "";
    }

    /**
     * Gets a label of value.
     *
     * @param string $name
     * @return ActualValue field of Settings table.
     */
    public static function get_alias03($code, $value)
    {
        $list_value = Settings::get_list($code);
        if (!empty($list_value)) {
            foreach ($list_value as $val) {
                if ($val->ActualValue === $value) {
                    return $val->Alias03;
                }
            }
        }
        return "";
    }

    /**
     * Gets a label of value.
     *
     * @param string $name
     * @return ActualValue field of Settings table.
     */
    public static function get_alias04($code, $value)
    {
        $list_value = Settings::get_list($code);
        if (!empty($list_value)) {
            foreach ($list_value as $val) {
                if ($val->ActualValue === $value) {
                    return $val->Alias04;
                }
            }
        }
        return "";
    }

    /**
     * Gets a setting.
     *
     * @param string $name
     * @return The list of [ActualValue] of Settings tables.
     */
    public static function get_list($code)
    {
        if (isset(self::$cache [$code])) {
            return self::$cache [$code];
        }

        // Using cache to improve performance
        $setting = ci()->pyrocache->model('settings_m', 'get_many_by_many', array(array(
            'SettingCode' => $code
        )));

        // Store it for later
        self::$cache [$code] = $setting;

        return $setting;
    }

    public static function get_list_by_location($locationID)
    {
        
        ci()->load->model('addresses/location_envelope_types_m');

        $listTypeAvailable = ci()->location_envelope_types_m->getAvailbleTypeByLocation($locationID);
       
        return $listTypeAvailable;
    }

    /**
     * Set Sets a config item
     *
     * @param string $name
     * @param string $value
     * @return bool
     */
    public static function set($code, $value)
    {
        if (is_string($code)) {
            if (is_scalar($value)) {
                $setting = ci()->pyrocache->model('settings_m', 'get_by', array(array(
                    'SettingCode' => $code
                )));
                if (!empty($setting)) {
                    ci()->pyrocache->model('settings_m', 'update', array($code, array(
                        'ActualValue' => $value
                    )));
                } else {
                    ci()->pyrocache->model('settings_m', 'insert', array(array(
                         'SettingCode' => $code,
                        'ActualValue' => $value
                    )));
                }
            }

            self::$cache [$code] = $value;

            return TRUE;
        }

        return FALSE;
    }
    
    /**
     * Set setting with key and label.
     * @param type $code
     * @param type $label
     * @param type $value
     * @return boolean
     */
    public static function setByLabel($code,$label, $value)
    {
        if (is_string($code)) {
            if (is_scalar($value)) {
                $setting = ci()->pyrocache->model('settings_m', 'get_by', array(array(
                    'SettingCode' => $code,
                    "LabelValue" => $label
                )));
                if (!empty($setting)) {
                    ci()->pyrocache->model('settings_m', 'update_by_many', array(array(
                        'SettingCode' => $code,
                        "LabelValue" => $label
                    ), array(
                        'ActualValue' => $value
                    )));
                } else {
                    ci()->pyrocache->model('settings_m', 'insert', array(array(
                        'SettingCode' => $code,
                        'ActualValue' => $value,
                        "LabelValue" => $label
                    )));
                }
            }

            self::$cache [$code] = $value;

            return TRUE;
        }

        return FALSE;
    }

    /**
     * Temp Changes a setting for this request only. Does not modify the database
     *
     * @param string $name
     * @param string $value
     * @return bool
     */
    public static function temp($code, $value)
    {
        // store the temp value in the cache so that all subsequent calls
        // for this request will use it instead of the database value
        self::$cache [$code] = $value;
    }

    /**
     * Item Old way of getting an item.
     *
     * @deprecated v1.0    Use either __get or Settings::get() instead
     * @param string $name
     * @return bool
     */
    public function item($name)
    {
        return $this->__get($name);
    }

    /**
     * Set Item Old way of getting an item.
     *
     * @deprecated v1.0    Use either __set or Settings::set() instead
     * @param string $name
     * @param string $value
     * @return bool
     */
    public function set_item($name, $value)
    {
        return $this->__set($name, $value);
    }

    /**
     * All Gets all the settings
     *
     * @return array
     */
    public function get_all()
    {
        if (self::$cache) {
            return self::$cache;
        }

        $settings = ci()->pyrocache->model('settings_m', 'get_many_by', array(array()));

        foreach ($settings as $setting) {
            self::$cache [$setting->SettingKey] = $setting->ActualValue;
        }

        return self::$cache;
    }

    /**
     * Add Setting Adds a new setting to the database
     *
     * @param array $setting
     * @return int
     */
    public function add($setting)
    {
        if (!$this->_check_format($setting)) {
            return FALSE;
        }
        
        return ci()->pyrocache->model('settings_m', 'insert', array($setting));
    }

    /**
     * Delete Setting Deletes setting to the database
     *
     * @param string $name
     * @return bool
     */
    public function delete($name)
    {
        return ci()->settings_m->delete_by(array(
            'SettingKey' => $name
        ));
    }

    /**
     * Format Options Formats the options for a setting into an associative array.
     *
     * @param array $options
     * @return array
     */
    private function _format_options($options = array())
    {
        $select_array = array();

        foreach ($options as $option) {
            list ($value, $name) = explode('=', $option);

            if ($this->ci->lang->line('settings_form_option_' . $name) !== FALSE) {
                $name = $this->ci->lang->line('settings_form_option_' . $name);
            }

            $select_array [$value] = $name;
        }

        return $select_array;
    }

    /**
     * Check Format This assures that the setting is in the correct format. Works with arrays or objects (it is PHP 5.3 safe)
     *
     * @param string $setting
     * @return bool the setting is the correct format
     */
    private function _check_format($setting)
    {
        if (!isset($setting)) {
            return FALSE;
        }
        foreach ($setting as $key => $value) {
            if (!in_array($key, $this->columns)) {
                return FALSE;
            }
        }

        return TRUE;
    }
}

/* End of file Settings.php */