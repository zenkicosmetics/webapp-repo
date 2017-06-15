<?php defined('BASEPATH') or exit('No direct script access allowed');

class settings_api
{
    public static function getCountryByID($countryID)
    {
        ci()->load->model('settings/countries_m');

        $country = ci()->countries_m->get($countryID);

        return $country;
    }

    public static function getCountryNameByID($countryID)
    {
        ci()->load->model('settings/countries_m');

        $country = ci()->countries_m->get($countryID);

        return empty($country) ? '' : $country->country_name;
    }

    public static function getCountryCodeByID($countryID)
    {
        ci()->load->model('settings/countries_m');

        $country = ci()->countries_m->get($countryID);

        return empty($country) ? '' : $country->country_code;
    }

    public static function getCurrencyByID($currencyID)
    {
        ci()->load->model('settings/currencies_m');

        $currency = ci()->currencies_m->get($currencyID);

        return $currency;
    }

    public static function getCurrenciesMany()
    {
        ci()->load->model('settings/currencies_m');

        $manyCurrencies = ci()->currencies_m->get_many_by_many(array('active_flag' => APConstants::ON_FLAG), 'currency_id, currency_short');

        return $manyCurrencies;
    }

    public static function getAllCountriesForDropDownList()
    {
        ci()->load->model('settings/countries_m');

        $rows = ci()->countries_m->getAllCountriesForDropDownList();

        return $rows;
    }

    public static function getAllCountries()
    {
        ci()->load->model('settings/countries_m');

        $countries =  ci()->countries_m->get_all();

        return $countries;
    }

    public static function getAllCurrenciesForDropDownList()
    {
        ci()->load->model('settings/currencies_m');

        $rows = ci()->currencies_m->getAllCurrenciesForDropDownList();

        return $rows;
    }

    /**
     * Gets currency.
     * @return unknown
     */
    public static function getAllCurrencies()
    {
        ci()->load->model('settings/currencies_m');

        $rows = ci()->currencies_m->get_all();

        return $rows;
    }

    /**
     * Get customs.
     * @return unknown
     */
    public static function getAllCustoms($from_list_country, $to_list_country)
    {
    	ci()->load->model('settings/customs_matrix_m');
    	$customs = ci()->customs_matrix_m->get_custom($from_list_country, $to_list_country);
    	return $customs;
    }

    /**
     * Gets term & condition.
     * @return type
     */
    public static function getTermAndCondition(){
        ci()->load->model("settings/terms_service_m");
        // get term and condition
        $query_result = ci()->terms_service_m->get_system_term_service(array(
            "type" => '1',
            "use_flag" => '1'
        ));

        $terms_and_conditions = '';
        if ($query_result) {
            $terms_and_conditions = $query_result->content;
        }

        return $terms_and_conditions;
    }

    /**
     * Gets term & condition.
     * @return type
     */
    public static function getPrivacyOfSystem(){
        ci()->load->model("settings/terms_service_m");
        // get term and condition
        $query_result = ci()->terms_service_m->get_system_term_service(array(
            "type" => '2',
            "use_flag" => '1'
        ));

        $terms_and_conditions = '';
        if ($query_result) {
            $terms_and_conditions = $query_result->content;
        }

        return $terms_and_conditions;
    }

    /**
     * Gets term & condition.
     * @return type
     */
    public static function getTermAndConditionEnterprise($customer_id){
        ci()->load->model("settings/terms_service_m");

        if(AccountSetting::get($customer_id, APConstants::CUSTOMER_TERM_CONDITION_SETTING) != APConstants::ON_FLAG){
            return self::getTermAndCondition();
        }

        // get term and condition
        $query_result = ci()->terms_service_m->get_by_many(array(
            "type" => '1',
            "use_flag" => '1',
            'customer_id' => $customer_id
        ));

        $terms_and_conditions = '';
        if ($query_result) {
            $terms_and_conditions = $query_result->content;
        }
        return $terms_and_conditions;
    }

    /**
     * Gets term & condition of system or enteprrise customer.
     * @param type $customer_id
     * @return type
     */
    public static function getTermAndConditionBy($customer_id=''){
        ci()->load->model("settings/terms_service_m");

        if(!empty($customer_id) && APContext::isUserEnterprise($customer_id)){
            $result = ci()->terms_service_m->get_by_many(array(
                "type" => '1',
                "use_flag" => '1',
                'customer_id' => $customer_id
            ));
        }else{
            // get default term and condition
            $result = ci()->terms_service_m->get_system_term_service(array(
                "type" => '1',
                "use_flag" => '1'
            ));
        }

        return $result;
    }

    /**
     * insert default term & conditon of enteprirse customer.
     * @param type $customer_id
     */
    public static function insertDefaultTermAndConditionOfEnterprise($customer_id){
        ci()->load->model("settings/terms_service_m");

        // get system term & condition.
        $term = self::getTermAndCondition();

        // Insert new record
        ci()->terms_service_m->insert(array (
            "type" => '1',
            "file_name" => "Terms&Conditions",
            "use_flag" => '1',
            "customer_id" => $customer_id,
            "created_date" => now(),
            "content" => $term
        ));
    }
}