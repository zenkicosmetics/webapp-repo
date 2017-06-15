<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author DungNT
 */
class APContext {

    /**
     * The Settings Construct
     */
    public function __construct() {
        ci()->load->library(array(
            "Exceptions/BusinessException",
            "Exceptions/SystemException",
            "Exceptions/DAOException",
            "Exceptions/ThirdPartyException",
        ));
    }

    /**
     * Get parent customer logged information.
     */
    public static function getParentCustomerLoggedIn() {
        $customer = ci()->session->userdata(APConstants::SESSION_PARENT_CUSTOMER_KEY);
        return $customer;
    }

    /**
     * Get customer logged information.
     */
    public static function getCustomerLoggedIn() {
        $customer = ci()->session->userdata(APConstants::SESSION_CUSTOMER_KEY);
        return $customer;
    }

    /**
     * Check if admin direct access customer view
     */
    public static function isAdminDirectAccessCustomerView() {
        $flag = ci()->session->userdata(APConstants::DIRECT_ACCESS_CUSTOMER_KEY);
        return $flag;
    }

    /**
     * Get customer logged information.
     */
    public static function getCustomerByID($customer_id) {
        ci()->load->model('customers/customer_m');
        $customer = ci()->customer_m->get_by_many(array('customer_id' => $customer_id));
        return $customer;
    }

    /**
     * Get admin logged information.
     */
    public static function isAdminUser() {
        $user = ci()->session->userdata(APConstants::SESSION_USERADMIN_KEY);
        $groups = ci()->session->userdata(APConstants::SESSION_GROUP_USERS_ROLE);
        if ($user != null && $groups && (in_array("0", $groups) || in_array("1", $groups) )) {
            return true;
        }
        return false;
    }

    /**
     * Get admin logged information. (Does not have this role now)
     */
    public static function isAdminParner() {
        $user = ci()->session->userdata(APConstants::SESSION_USERADMIN_KEY);
        $groups = ci()->session->userdata(APConstants::SESSION_GROUP_USERS_ROLE);

        if ($user != null && $groups && in_array("3", $groups)) {
            return true;
        }
        return false;
    }

    public static function isAdminServiceParner() {
        $user = ci()->session->userdata(APConstants::SESSION_USERADMIN_KEY);
        $groups = ci()->session->userdata(APConstants::SESSION_GROUP_USERS_ROLE);

        if ($user != null && $groups && in_array("5", $groups)) {
            return true;
        }
        return false;
    }

    /**
     * Get admin logged information. (Does not have this role now)
     */
    public static function isServiceParner() {
        $user = ci()->session->userdata(APConstants::SESSION_USERADMIN_KEY);
        $groups = ci()->session->userdata(APConstants::SESSION_GROUP_USERS_ROLE);

        if ($user != null && $groups && in_array(APConstants::GROUP_SERVICE_PARTNER_ADMIN, $groups)) {
            return true;
        }
        return false;
    }

    /**
     * Get admin logged information.
     */
    public static function isAdminLocation() {
        $user = ci()->session->userdata(APConstants::SESSION_USERADMIN_KEY);
        $groups = ci()->session->userdata(APConstants::SESSION_GROUP_USERS_ROLE);

        if ($user != null && $groups && in_array("4", $groups)) {
            return true;
        }

        return false;
    }

    /**
     * Get worker admin logged information.
     */
    public static function isWorkerAdmin() {
        $user = ci()->session->userdata(APConstants::SESSION_USERADMIN_KEY);
        $groups = ci()->session->userdata(APConstants::SESSION_GROUP_USERS_ROLE);

        if ($user != null && $groups && in_array("2", $groups)) {
            return true;
        }

        return false;
    }

    /**
     * Get admin logged information.
     */
    public static function isSupperAdminUser() {
        $user = ci()->session->userdata(APConstants::SESSION_USERADMIN_KEY);
        $groups = ci()->session->userdata(APConstants::SESSION_GROUP_USERS_ROLE);

        if ($user != null && $groups && in_array("0", $groups)) {
            return true;
        }

        return false;
    }

    /**
     * Check if current customer is normal user
     */
    public static function isNormalCustomerUser() {
        $customer_user_role = ci()->session->userdata(APConstants::GROUP_CUSTOMER_ROLE_KEY);
        if ($customer_user_role != null && $customer_user_role == APConstants::GROUP_CUSTOMER_USER) {
            return true;
        }
        return false;
    }

    /**
     * Check if current customer is normal user
     */
    public static function isAdminCustomerUser() {
        $customer = APContext::getCustomerLoggedIn();

        if (!empty($customer) && $customer->role_flag == APConstants::ON_FLAG) {
            return true;
        }

        return false;
    }

    /**
     * Check if current customer is enterprise
     */
    public static function isEnterpriseCustomer() {
        $customer = APContext::getParentCustomerLoggedIn();
        if ($customer != null && $customer->account_type == APConstants::ENTERPRISE_CUSTOMER) {
            return true;
        }
        return false;
    }

    /**
     * Check if current customer is enterprise
     */
    public static function isEnterpriseCustomerByID($parent_customer_id) {
        $customer = APContext::getCustomerByID($parent_customer_id);
        if ($customer != null && $customer->account_type == APConstants::ENTERPRISE_CUSTOMER) {
            return true;
        }
        return false;
    }

    /**
     * Check if current customer is primary user (can view many phone number)
     */
    public static function isPrimaryCustomerByID($parent_customer_id) {
        $customer = APContext::getCustomerByID($parent_customer_id);
        $isEnterpriseCustomer = false;
        if ($customer != null && $customer->account_type == APConstants::ENTERPRISE_CUSTOMER) {
            $isEnterpriseCustomer = true;
        }
        if (empty($customer->parent_customer_id) && $isEnterpriseCustomer) {
            return true;
        }
        return false;
    }

    /**
     * Check if current customer is enterprise
     */
    public static function isStandardCustomer() {
        return !APContext::isEnterpriseCustomer();
    }

    /**
     * Check if current customer is primary user (can view many phone number)
     */
    public static function isPrimaryCustomerUser() {
        $isEnterpriseCustomer = APContext::isEnterpriseCustomer();
        $customer = APContext::getCustomerLoggedIn();
        if (empty($customer->parent_customer_id) && $isEnterpriseCustomer) {
            return true;
        }
        return false;
    }

    /**
     * Get admin logged information.
     */
    public static function getAdminLoggedIn() {
        $user = ci()->session->userdata(APConstants::SESSION_USERADMIN_KEY);
        return $user;
    }

    /**
     * Get admin logged information. (Partner ID)
     */
    public static function getParnerIDLoggedIn() {
        $user = ci()->session->userdata(APConstants::SESSION_USERADMIN_KEY);
        if ($user) {
            return $user->partner_id;
        }
        return '';
    }

    /**
     * Get admin logged information. (Location Available ID)
     */
    public static function getLocationIDLoggedIn() {
        $user = ci()->session->userdata(APConstants::SESSION_USERADMIN_KEY);
        if ($user) {
            return $user->location_available_id;
        }
        return '';
    }

    /**
     * Get admin logged information.
     */
    public static function getAdminIdLoggedIn() {
        $user = ci()->session->userdata(APConstants::SESSION_USERADMIN_KEY);
        if ($user) {
            return $user->id;
        }

        return "";
    }

    /**
     * Get admin logged information.
     */
    public static function getCurrentInstanceId() {
        $instance_id = ci()->session->userdata(APConstants::SESSION_INSTANCE_ID_KEY);
        return $instance_id;
    }

    /**
     * Get customer setting information.
     */
    public static function getCustomerSetting($key) {
        $customer_setting = ci()->session->userdata(APConstants::SESSION_CUSTOMER_SETTING_KEY);

        if (!empty($customer_setting)) {
            foreach ($customer_setting as $setting) {
                if ($setting->SettingName === $key) {
                    return $setting->SettingValue;
                }
            }
        }
        return "";
    }

    /**
     * Get parent customer logged information.
     */
    public static function getParentCustomerCodeLoggedIn() {
        $customer = ci()->session->userdata(APConstants::SESSION_PARENT_CUSTOMER_KEY);
        if ($customer) {
            return $customer->customer_id;
        }
        return '';
    }

    /**
     * Get customer logged information.
     */
    public static function getCustomerCodeLoggedIn() {
        $customer = ci()->session->userdata(APConstants::SESSION_CUSTOMER_KEY);
        if ($customer) {
            return $customer->customer_id;
        }
        return '';
    }

    /**
     * Get customer logged information based on envelope
     */
    public static function getCustomerCodeLoggedInMailbox($input_list_envelope_id) {
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();

        // check if standard customer. return customer_id
        if(APContext::isStandardCustomer()){
            return $parent_customer_id;
        }

        $list_envelope_id = array();
        if (is_array($input_list_envelope_id)) {
            if (empty($input_list_envelope_id) || count($input_list_envelope_id) == 0) {
                return 0;
            }
            $list_envelope_id = $input_list_envelope_id;
        } else {
            if (empty($input_list_envelope_id)) {
                return 0;
            }
            $list_envelope_id = array($input_list_envelope_id);
        }

        ci()->load->model('customers/customer_m');
        ci()->load->model('scans/envelope_m');

        $list_customer_id = ci()->envelope_m->get_many_by_many(array(
            "id IN ('" . implode("','", $list_envelope_id) . "')" => null
                ), 'envelopes.to_customer_id', true);
        if (count($list_customer_id) == 0 || count($list_customer_id) > 1) {
            return APContext::getCustomerCodeLoggedIn();
            // TODO:
            //throw new BusinessException("The system can not support to process this request.");
        }
        $customer_id = $list_customer_id[0]->to_customer_id;

        $customer_check = ci()->customer_m->get_by_many(array(
            'customer_id' => $customer_id,
            // TODO: check customer existed
            "(parent_customer_id IS NULL OR parent_customer_id='" . $parent_customer_id . "')" => null
        ));
        if (empty($customer_check)) {
            return 0;
        }
        return $customer_id;
    }

    /**
     * Get customer logged information based on postbox
     */
    public static function getCustomerCodeLoggedInMailboxByPostbox($postbox_id) {
        ci()->load->model('customers/customer_m');
        ci()->load->model('mailbox/postbox_m');
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();

        // check if standard customer. return customer_id
        if(APContext::isStandardCustomer()){
            return $parent_customer_id;
        }

        $list_customer_id = ci()->postbox_m->get_many_by_many(array(
            "postbox_id" => $postbox_id
                ), 'postbox.customer_id', true);
        if (count($list_customer_id) == 0 || count($list_customer_id) > 1) {
            throw new BusinessException("The system can not support to process this request.");
        }
        $customer_id = $list_customer_id[0]->customer_id;

        // Only check if this is user loggin
        if ($parent_customer_id != $customer_id) {
            $customer_check = ci()->customer_m->get_by_many(array(
                'customer_id' => $customer_id,
                // TODO: check customer existed
                "(parent_customer_id IS NULL OR parent_customer_id='" . $parent_customer_id . "')" => null
            ));
            if (empty($customer_check)) {
                return 0;
            }
        }
        return $customer_id;
    }

    /**
     * Get list user of customer
     *
     * @return type
     */
    public static function getListPhoneUser() {
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        ci()->load->model('customers/phone_customer_user_m');
        $isPrimaryCustomer = APContext::isPrimaryCustomerUser();
        if ($isPrimaryCustomer) {
            $list_users = ci()->phone_customer_user_m->get_many_by_many(array(
                'parent_customer_id' => $parent_customer_id
            ));
        } else {
            $list_users = ci()->phone_customer_user_m->get_many_by_many(array(
                'customer_id' => $customer_id,
                'parent_customer_id' => $parent_customer_id
            ));
        }
        // $list_users = APContext::setSessionValue(APConstants::GROUP_CUSTOMER_LISTUSER_KEY, $list_users);
        return $list_users;
    }

    /**
     * Get current user id login
     */
    public static function getUserIdLoggedin() {
        if (APContext::isNormalCustomerUser()) {
            $list_users = APContext::getSessionValue(APConstants::GROUP_CUSTOMER_LISTUSER_KEY);
            $user = $list_users[0];
            return $user->id;
        }
        return '';
    }

    /**
     * Get current user id login
     */
    public static function getUserPhoneIdLoggedin() {
        if (APContext::isNormalCustomerUser()) {
            $list_users = APContext::getSessionValue(APConstants::GROUP_CUSTOMER_LISTUSER_KEY);
            $user = $list_users[0];
            return $user->phone_user_id;
        }
        return '';
    }

    /**
     * get sub account id of sontel account.
     */
    public static function getSubAccountId($customer_id) {
        ci()->load->model('phones/phone_customer_subaccount_m');
        $account = ci()->phone_customer_subaccount_m->get_by('customer_id', $customer_id);

        if (empty($account)) {
            return "";
        }

        return $account->account_id;
    }

    /**
     * Reload list customer user.
     *
     * @param type $customer_id
     */
    public static function reloadListUser($parent_customer_id, $customer_user_id = '') {
        ci()->load->model('customers/customer_user_m');
        $condition = array(
            'parent_customer_id' => $parent_customer_id,
            'activated_flag' => APConstants::ON_FLAG
        );
        if (!empty($customer_user_id)) {
            $condition['parent_customer_id'] = $customer_user_id;
        } else {
            $condition['parent_customer_id'] = 0;
        }
        $list_user = ci()->customer_user_m->get_many_by_many($condition);
        ci()->session->set_userdata(APConstants::GROUP_CUSTOMER_LISTUSER_KEY, $list_user);
    }

    /**
     * Get customer logged information.
     */
    public static function getCustomerEUMemberFlag() {
        $address = ci()->session->userdata(APConstants::SESSION_CUSTOMER_ADDRESS_KEY);
        if ($address) {
            return $address->eu_member_flag;
        }
        return '';
    }

    /**
     * Get customer logged information.
     */
    public static function reloadCustomerLoggedIn() {
        $customer_id = APContext::getCustomerCodeLoggedIn();
        if ($customer_id) {
            $new_customer = APContext::getCustomerByID($customer_id);
            ci()->session->unset_userdata(APConstants::SESSION_CUSTOMER_KEY);
            ci()->session->unset_userdata(APConstants::SESSION_PARENT_CUSTOMER_KEY);
            ci()->session->set_userdata(APConstants::SESSION_CUSTOMER_KEY, $new_customer);

            if ($new_customer->role_flag == APConstants::ON_FLAG) {
                ci()->session->set_userdata(APConstants::GROUP_CUSTOMER_ROLE_KEY, APConstants::GROUP_CUSTOMER_ADMIN);
            } else {
                ci()->session->set_userdata(APConstants::GROUP_CUSTOMER_ROLE_KEY, APConstants::GROUP_CUSTOMER_USER);
            }
            $parent_customer_id = $new_customer->parent_customer_id;

            if (empty($parent_customer_id)) {
                ci()->session->set_userdata(APConstants::SESSION_PARENT_CUSTOMER_KEY, $new_customer);
            } else {
                $parent_customer = APContext::getCustomerByID($parent_customer_id);
                ci()->session->set_userdata(APConstants::SESSION_PARENT_CUSTOMER_KEY, $parent_customer);
                APContext::reloadListUser($parent_customer_id, $parent_customer->customer_id);
            }
        }
        APContext::setSessionValue('get_all_postbox_list', null);
    }

    /**
     * Get admin logged information. (#1058 add multi dimension capability for admin )
     */
    public static function reloadAdminLoggedIn() {
        $user_id = APContext::getAdminIdLoggedIn();
        ci()->load->model('users/user_m');
        $user = ci()->user_m->get_user_info(array(
            "id" => $user_id
        ));
        ci()->session->set_userdata(APConstants::SESSION_USERADMIN_KEY, $user);
    }

    /**
     * Get dropbox setting information.
     * @param null $setting
     * @return
     */
    public static function getDropbox($setting = null) {
        $customer_setting = (array)ci()->session->userdata(APConstants::SESSION_CLOUD_CUSTOMER_KEY);

        if(!empty($customer_setting)){
            if (empty($setting['access_token'])) {
                $setting['access_token'] = empty($customer_setting['access_token']) ? '' : $customer_setting['access_token'];
            }
            if (empty($setting['folder_name'])) {
                $setting['folder_name'] = empty($customer_setting['folder_name']) ? '' : $customer_setting['folder_name'];
            }
        }

        $dropboxV2 = ci()->load->library('DropboxV2', $setting);

        return $dropboxV2;
    }

    /**
     * Get dropbox setting information.
     */
    public static function isDropboxSetting() {
        $setting = ci()->session->userdata(APConstants::SESSION_CLOUD_CUSTOMER_KEY);
        if ($setting) {
            return true;
        }
        return false;
    }

    /**
     * Get session value
     */
    public static function getSessionValue($key) {
        $val = ci()->session->userdata($key);
        return $val;
    }

    /**
     * Get session value
     */
    public static function setSessionValue($key, $val) {
        ci()->session->set_userdata($key, $val);
        return $val;
    }

    /**
     * Check customer logged in or not.
     */
    public static function isCustomerLoggedIn() {
        $customer = APContext::getCustomerLoggedIn();
        return $customer ? true : false;
    }

    /**
     * Get assest path url.
     */
    public static function getAssetPath() {
        return ci()->config->item('asset_url');
    }

    /**
     * Get full path url.
     */
    public static function getFullBasePath() {
        return ci()->config->item('full_url');
    }

    /**
     * Get full path url of load balancer.
     */
    public static function getFullBalancerPath() {
        return ci()->config->item('loadbalance_url');
    }

    /**
     * Get image path of default theme
     */
    public static function getImagePath($is_admin = FALSE) {
        // Get asset url
        $asset_url = ci()->config->item('asset_url');

        if ($is_admin) {
            // Get current themse
            $current_backend_theme = Settings::get(APConstants::ADMIN_THEMES_CODE);

            // Build image path
            return $asset_url . 'system/virtualpost/themes/' . $current_backend_theme . '/images';
        }

        // Get current themse
        $current_fontend_theme = Settings::get(APConstants::FRONTEND_THEMES_CODE);

        // Build image path
        return $asset_url . 'system/virtualpost/themes/' . $current_fontend_theme . '/images';
    }

    /**
     * update limit into user_paging.
     *
     * @param unknown_type $limit
     */
    public static function updatePagingSetting($limit) {
        ci()->load->model('mailbox/user_paging_m');

        // set limit
        $paging = ci()->user_paging_m->get_by_many(array(
            'user_id' => APContext::getCustomerCodeLoggedIn(),
            'setting_key' => APConstants::USER_PAGING_SETTING
        ));

        if ($paging) {
            ci()->user_paging_m->update($paging->id, array(
                'setting_value' => $limit
            ));
        } else {
            ci()->user_paging_m->insert(array(
                'user_id' => APContext::getCustomerCodeLoggedIn(),
                'setting_key' => APConstants::USER_PAGING_SETTING,
                'setting_value' => $limit
            ));
        }

        // update session.
        //ci()->session->set_userdata(APConstants::SESSION_PAGING_SETTING, $limit);
    }

    /**
     * get paging setting of user.
     */
    public static function getPagingSetting() {
        ci()->load->model('mailbox/user_paging_m');

        $paging = ci()->user_paging_m->get_by_many(array(
            'user_id' => APContext::getCustomerCodeLoggedIn(),
            'setting_key' => APConstants::USER_PAGING_SETTING
        ));

        if ($paging) {
            return $paging->setting_value;
        } else {
            return APConstants::DEFAULT_PAGE_ROW;
        }
    }

    /**
     * Gets hiden panes layout
     */
    public static function getHidePanesSetting() {
        ci()->load->model('mailbox/user_paging_m');

        $paging = ci()->user_paging_m->get_by_many(array(
            'user_id' => APContext::getCustomerCodeLoggedIn(),
            'setting_key' => APConstants::USER_HIDE_PANES_LAYOUT
        ));

        if ($paging) {
            return $paging->setting_value;
        } else {
            return "";
        }
    }

    /**
     * update hide panes setting
     */
    public static function updateHidePanesSetting($hide_flag = "0") {
        ci()->load->model('mailbox/user_paging_m');

        // set limit
        $paging = ci()->user_paging_m->get_by_many(array(
            'user_id' => APContext::getCustomerCodeLoggedIn(),
            'setting_key' => APConstants::USER_HIDE_PANES_LAYOUT
        ));

        if ($paging) {
            ci()->user_paging_m->update($paging->id, array(
                'setting_value' => $hide_flag
            ));
        } else {
            ci()->user_paging_m->insert(array(
                'user_id' => APContext::getCustomerCodeLoggedIn(),
                'setting_key' => APConstants::USER_HIDE_PANES_LAYOUT,
                'setting_value' => $hide_flag
            ));
        }
    }

    public static function updateAdminPagingSetting($limit) {
        ci()->load->model('mailbox/user_paging_m');

        // set limit
        $paging = ci()->user_paging_m->get_by_many(array(
            'user_id' => APContext::getAdminIdLoggedIn(),
            'setting_key' => APConstants::USER_PAGING_SETTING,
            'user_type' => '1'
        ));

        if ($paging) {
            ci()->user_paging_m->update($paging->id, array(
                'setting_value' => $limit
            ));
        } else {
            ci()->user_paging_m->insert(array(
                'user_id' => APContext::getAdminIdLoggedIn(),
                'setting_key' => APConstants::USER_PAGING_SETTING,
                'setting_value' => $limit,
                'user_type' => '1'
            ));
        }
    }

    public static function getAdminPagingSetting() {
        ci()->load->model('mailbox/user_paging_m');

        $paging = ci()->user_paging_m->get_by_many(array(
            'user_id' => APContext::getAdminIdLoggedIn(),
            'setting_key' => APConstants::USER_PAGING_SETTING,
            'user_type' => '1'
        ));

        if ($paging) {
            return $paging->setting_value;
        } else {
            return APConstants::DEFAULT_PAGE_ROW;
        }
    }

    /**
     * Get postbox setting by customer id and postbox_id.
     *
     * @param unknown_type $customer_id
     * @param unknown_type $postbox_id
     */
    public static function getPostboxSetting($customer_id, $postbox_id) {
        ci()->load->model('mailbox/postbox_setting_m');
        // Get setting of customer id
        $postbox_setting = ci()->postbox_setting_m->get_by_many(array(
            'customer_id' => $customer_id,
            "postbox_id" => $postbox_id
        ));

        if (empty($postbox_setting)) {
            $postbox_setting = new stdClass();
            $postbox_setting->always_scan_envelope = '0';
            $postbox_setting->always_scan_envelope_vol_avail = '0';
            $postbox_setting->always_scan_incomming = '0';
            $postbox_setting->always_scan_incomming_vol_avail = '0';
            $postbox_setting->email_scan_notification = '0';
            $postbox_setting->email_notification = '0';
            $postbox_setting->invoicing_cycle = '0';
            $postbox_setting->collect_mail_cycle = '0';
            $postbox_setting->weekday_shipping = '0';
        }

        return $postbox_setting;
    }

    /**
     * Logout
     */
    public static function logout() {
        ci()->session->unset_userdata(APConstants::SESSION_CUSTOMER_KEY);
        ci()->session->unset_userdata(APConstants::SESSION_MEMBERS_KEY);
        ci()->session->unset_userdata(APConstants::SESSION_CUSTOMER_ADDRESS_KEY);
        ci()->session->unset_userdata(APConstants::SESSION_CLOUD_CUSTOMER_KEY);
        ci()->session->unset_userdata(APConstants::SESSION_CUSTOMER_SETTING_KEY);
        ci()->session->unset_userdata(APConstants::SESSION_SHOW_MOBILE_ADV_FIRST_LOGIN);
        ci()->session->unset_userdata(APConstants::SESSION_GROUP_USERS_ROLE);
        ci()->session->unset_userdata(APConstants::SESSION_PARENT_CUSTOMER_KEY);
        //ci()->session->sess_destroy();
    }

    /**
     * chekc access
     */
    public static function check_instance_admin_access() {
        $groups = ci()->session->userdata(APConstants::SESSION_GROUP_USERS_ROLE);

        if (in_array(APConstants::INSTANCE_ADMIN, $groups)) {
            return true;
        }

        return false;
    }

    /**
     * chekc access
     */
    public static function check_partner_admin_access() {
        $groups = ci()->session->userdata(APConstants::SESSION_GROUP_USERS_ROLE);

        if (in_array(APConstants::PARTNER_ADMIN, $groups)) {
            return true;
        }

        return false;
    }

    /**
     * chekc access
     */
    public static function check_location_admin_access() {
        $groups = ci()->session->userdata(APConstants::SESSION_GROUP_USERS_ROLE);
        if (in_array(APConstants::LOCATION_ADMIN, $groups)) {
            return true;
        }

        return false;
    }

    /**
     * update location selection
     *
     * @param unknown_type $limit
     */
    public static function updateLocationUserSetting($location_id) {
        ci()->load->model('mailbox/user_paging_m');

        // set limit
        $paging = ci()->user_paging_m->get_by_many(array(
            'user_id' => APContext::getAdminIdLoggedIn(),
            'setting_key' => APConstants::LOCATION_USER_SELECTION_SETTING_TYPE
        ));

        if ($paging) {
            ci()->user_paging_m->update($paging->id, array(
                'setting_value' => $location_id
            ));
        } else {
            ci()->user_paging_m->insert(array(
                'user_id' => APContext::getAdminIdLoggedIn(),
                'setting_key' => APConstants::LOCATION_USER_SELECTION_SETTING_TYPE,
                'setting_value' => $location_id
            ));
        }
    }

    /**
     * get selected location.
     */
    public static function getLocationUserSetting() {
        ci()->load->model('mailbox/user_paging_m');

        $paging = ci()->user_paging_m->get_by_many(array(
            'user_id' => APContext::getAdminIdLoggedIn(),
            'setting_key' => APConstants::LOCATION_USER_SELECTION_SETTING_TYPE
        ));

        if ($paging) {
            return $paging->setting_value;
        } else {
            return "";
        }
    }

    /**
     * Gets location ID of user phone number.
     *
     * @param type $customer_id
     * @param type $user_id
     * @return string
     */
    public static function getLocationIDUserPhone($parent_customer_id, $customer_id) {
        // TODO: return berlin location first.
        return "1";
    }

    /**
     * Check user enterprise of customer.
     */
    public static function isUserEnterprise($customer_id) {
        ci()->load->model('customers/customer_m');
        $customer = ci()->customer_m->get($customer_id);

        if (!empty($customer) && $customer->account_type == APConstants::ENTERPRISE_CUSTOMER && !empty($customer->parent_customer_id)) {
            return true;
        }

        return false;
    }

    /**
     * Gets title of site.
     * @return string
     */
    public static function getTitleSite($template) {
        $title = "";
        $isEnterpriseCustomer = APContext::isEnterpriseCustomer();
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();

        if ($isEnterpriseCustomer) {
            $title = AccountSetting::get($parent_customer_id, APConstants::SITE_NAME_CODE);
            if (empty($title)) {
                $title = Settings::get(APConstants::SITE_NAME_CODE);
            }
            $title .= ' - ' . $template['title'];
        } else {
            $title = Settings::get(APConstants::SITE_NAME_CODE) . ' - ' . $template['title'];
        }

        return $title;
    }

    /**
     * Gets list colors of site.
     * @return type
     */
    public static function getListColorOfSite() {
        $isEnterpriseCustomer = APContext::isEnterpriseCustomer();
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        if ($isEnterpriseCustomer) {
            $logo_url = AccountSetting::get($parent_customer_id, APConstants::SITE_LOGO_CODE);
            //$main_color = AccountSetting::get($parent_customer_id, APConstants::MAIN_COLOR_CODE);
            //$secondary_color = AccountSetting::get($parent_customer_id, APConstants::MAIN_COLOR_CODE);
            if (empty($logo_url)) {
                $logo_url = Settings::get(APConstants::SITE_LOGO_CODE);
            }
            //if (empty($main_color)) {
            //    $main_color = Settings::get(APConstants::MAIN_COLOR_CODE);
            //}
            //if (empty($secondary_color)) {
            //    $secondary_color = Settings::get(APConstants::MAIN_COLOR_CODE);
            //}
        } else {
            $logo_url = Settings::get(APConstants::SITE_LOGO_CODE);
            //$main_color = Settings::get(APConstants::MAIN_COLOR_CODE);
            //$secondary_color = Settings::get(APConstants::MAIN_COLOR_CODE);
        }

        return array(
            'logo_url' => $logo_url,
            //'main_color' => $main_color,
            //'secondary_color' => $secondary_color
        );
    }

    /**
     * Gets all list colors of system.
     * @param type $admin_site
     * @return type
     */
    public static function getListColors($admin_site = false){
        $result = array();
        $listColors = Settings::get_list(APConstants::COLORS_LIST_KEY);
        foreach($listColors as $color){
            $result[$color->LabelValue] = $color->ActualValue;
        }

        if(!$admin_site){
            // Gets list color of front-end site.
            $isEnterpriseCustomer = APContext::isEnterpriseCustomer();
            $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();

            if($isEnterpriseCustomer){
                $listColors = AccountSetting::get_many($parent_customer_id);
                foreach($listColors as $color){
                    if(strpos($color->setting_key, 'COLOR') !== false && !empty($color->setting_value)){
                        $result[$color->setting_key] = $color->setting_value;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Gets customer id by case
     * @param type $case_id
     */
    public static function getCustomerByCase($case_id) {
        ci()->load->model('cases/cases_m');
        $case = ci()->cases_m->get($case_id);

        if (!empty($case)) {
            return $case->customer_id;
        }

        return 0;
    }

    /**
     * #1296 add receipt scan/upload to receipts
     * Get partner logged information.
     */
    public static function getPartnerByID($partner_id) {
        ci()->load->model('partner/partner_m');
        $partner = ci()->partner_m->get_by_many(array('partner_id' => $partner_id));
        return $partner;
    }

    /**
    * People based on German
    * Have at least a postbox in german
    * Or invoice address is German
    */
    public static function basedOnGermany($customer_id = null)
    {
        ci()->load->model('addresses/location_m');
        ci()->load->model('mailbox/postbox_m');

        $result = false;
        $location_ids = [];
        $location_germany = ci()->location_m->get_public_location_ids([
            "location.country_id" => APConstants::GERMANY_COUNTRY_ID
            ]);
        $location_german_ids = [];
        foreach ($location_germany as $key => $value) {
            $location_german_ids[] = $value->id;
        }
        if (! $customer_id) {
            $customer_id = self::getCustomerCodeLoggedIn();
        }

        $list_postboxs = ci()->postbox_m->get_postboxes_by($customer_id);
        if ($list_postboxs) {
            foreach ($list_postboxs as $key => $value) {
                $location_ids[] = $value->location_available_id;
            }
        }
        // Check have at least one postbox in German
        $intersec = array_intersect($location_german_ids, $location_ids);
        $result = ! empty($intersec); // Check if have a post box at German

        if (! $result) {
            // Check customer invoice address at German
            ci()->load->model('customers/customer_m');
            $customer_info = ci()->customer_m->get_customer_info($customer_id);
            if($customer_info) {
                $result = $customer_info->invoicing_country == APConstants::GERMANY_COUNTRY_ID;
            }
        }
        return $result;
    }
}
