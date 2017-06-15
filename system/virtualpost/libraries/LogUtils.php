<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Log history for all deleted tables.
 */
class LogUtils {

    /**
     * log history all envelope of postbox.
     */
    public static function log_envelope_history($customer_id) {
        $stmt = "INSERT INTO envelopes_history(
                                            envelope_id,envelope_code,from_customer_name,to_customer_id, postbox_id,envelope_type_id,weight,weight_unit,completed_by,
                                            completed_date,incomming_date,incomming_date_only,last_updated_date,category_type,invoice_flag,shipping_type,
                                            include_estamp_flag,sync_cloud_flag,sync_cloud_date,envelope_scan_flag,item_scan_flag,item_scan_date,direct_shipping_flag,
                                            direct_shipping_date,collect_shipping_flag,collect_shipping_date,trash_flag,trash_date,storage_flag,storage_date,completed_flag,
                                            email_notification_flag,package_id,shipping_id,new_notification_flag,incomming_letter_flag,location_id)
                    (SELECT
                            id,  envelope_code,  from_customer_name,  to_customer_id, postbox_id,  envelope_type_id,  weight,  weight_unit,  completed_by,
                            completed_date, incomming_date,  incomming_date_only,  last_updated_date,  category_type,  invoice_flag,  shipping_type,  include_estamp_flag,
                            sync_cloud_flag, sync_cloud_date,  envelope_scan_flag,  item_scan_flag,  item_scan_date,  direct_shipping_flag,  direct_shipping_date,
                            collect_shipping_flag,  collect_shipping_date,  trash_flag,  trash_date,  storage_flag,  storage_date,  completed_flag,  email_notification_flag,
                            package_id,  shipping_id,  new_notification_flag,  incomming_letter_flag,  location_id
                    FROM envelopes
                    WHERE to_customer_id='{$customer_id}')";
        ci()->db->query($stmt);
    }
    
    /**
     * log history for delete envelope by id
     */
    public static function log_delete_envelope_by_id($envelope_id, $deleted_by) {
        // log envelope
        $stmt = "INSERT INTO envelopes_history(
                    envelope_id,envelope_code,from_customer_name,to_customer_id, postbox_id,envelope_type_id,weight,weight_unit,completed_by,
                    completed_date,incomming_date,incomming_date_only,last_updated_date,category_type,invoice_flag,shipping_type,
                    include_estamp_flag,sync_cloud_flag,sync_cloud_date,envelope_scan_flag,item_scan_flag,item_scan_date,direct_shipping_flag,
                    direct_shipping_date,collect_shipping_flag,collect_shipping_date,trash_flag,trash_date,storage_flag,storage_date,completed_flag,
                    email_notification_flag,package_id,shipping_id,new_notification_flag,incomming_letter_flag,location_id, deleted_by)
        (SELECT
                    id,  envelope_code,  from_customer_name,  to_customer_id, postbox_id,  envelope_type_id,  weight,  weight_unit,  completed_by,
                    completed_date, incomming_date,  incomming_date_only,  last_updated_date,  category_type,  invoice_flag,  shipping_type,  include_estamp_flag,
                    sync_cloud_flag, sync_cloud_date,  envelope_scan_flag,  item_scan_flag,  item_scan_date,  direct_shipping_flag,  direct_shipping_date,
                    collect_shipping_flag,  collect_shipping_date,  trash_flag,  trash_date,  storage_flag,  storage_date,  completed_flag,  email_notification_flag,
                    package_id,  shipping_id,  new_notification_flag,  incomming_letter_flag,  location_id, '{$deleted_by}'
        FROM envelopes
        WHERE id='{$envelope_id}')";
        ci()->db->query($stmt);
        
        // log envelope file.
        $stmt2 = "INSERT INTO envelope_files_history (`envelope_file_id` ,`envelope_id` ,`customer_id` ,`file_name` ,`public_file_name` ,`local_file_name` ,`amazon_path` ,
                                                        `amazon_relate_path` ,`file_size` ,`created_date` ,`type` ,`updated_date` ,`number_page`)
                                                    (SELECT `id` ,`envelope_id` ,`customer_id` ,`file_name` ,`public_file_name` ,`local_file_name` ,`amazon_path` ,
                                                        `amazon_relate_path` ,`file_size` ,`created_date` ,`type` ,`updated_date` ,`number_page`
                                                     FROM envelope_files 
                                                    WHERE envelope_id='{$envelope_id}' )";

        ci()->db->query($stmt2);
    }

    /**
     * Log envelope file.
     *
     * @param unknown $customer_id
     */
    public static function log_envelope_file_history($customer_id) {
        $stmt = "INSERT INTO envelope_files_history (`envelope_file_id` ,`envelope_id` ,`customer_id` ,`file_name` ,`public_file_name` ,`local_file_name` ,`amazon_path` ,
                                                        `amazon_relate_path` ,`file_size` ,`created_date` ,`type` ,`updated_date` ,`number_page`)
                                                    (SELECT `id` ,`envelope_id` ,`customer_id` ,`file_name` ,`public_file_name` ,`local_file_name` ,`amazon_path` ,
                                                        `amazon_relate_path` ,`file_size` ,`created_date` ,`type` ,`updated_date` ,`number_page`
                                                     FROM envelope_files 
                                                    WHERE customer_id='{$customer_id}' )";

        ci()->db->query($stmt);
    }

    /**
     * Log envelope shipping
     *
     * @param unknown $customer_id
     */
    public static function log_envelope_shipping_history($customer_id) {
        $stmt = "INSERT INTO envelope_shipping_history (`envelope_shipping_id`, `envelope_id`, `customer_id`, `postbox_id`, `shipping_name`, `shipping_company`,
                                                 `shipping_street`, `shipping_postcode`, `shipping_city`, `shipping_region`, `shipping_country`, `estamp_url`, `lable_size_id`, 
                                                 `package_letter_size`, `package_letter_size_id`, `printer_id`, `shipping_type_id`, `shipping_date`, `shipping_fee`)
                                            (SELECT `id`, `envelope_id`, `customer_id`, `postbox_id`, `shipping_name`, `shipping_company`, `shipping_street`, 
                                                `shipping_postcode`, `shipping_city`, `shipping_region`, `shipping_country`, `estamp_url`, `lable_size_id`,
                                                 `package_letter_size`, `package_letter_size_id`, `printer_id`, `shipping_type_id`, 
                                                `shipping_date`, `shipping_fee` 
                                            FROM envelope_shipping
                                            WHERE customer_id='{$customer_id}' )";

        ci()->db->query($stmt);
    }

    /**
     * Log envelope completed
     *
     * @param unknown $customer_id
     */
    public static function log_envelope_completed_history($customer_id) {
        $stmt = "INSERT INTO envelopes_completed_history (`envelopes_completed_id` ,`envelope_id` ,`from_customer_name` ,`to_customer_id` ,`activity_id` ,`activity_name` ,
                                                        `postbox_id` ,`envelope_type_id` ,`weight` ,`weight_unit` ,`last_updated_date` ,`completed_by` ,`completed_date` ,`incomming_date` ,`category_type` ,`invoice_flag` ,
                                                        `shipping_type` ,`include_estamp_flag` ,`sync_cloud_flag` ,`envelope_scan_flag` ,`item_scan_flag` ,`direct_shipping_flag` ,`collect_shipping_flag` ,`trash_flag` ,
                                                        `storage_flag` ,`completed_flag` ,`email_notification_flag` ,`activity_code` ,`location_id`)
        (SELECT `id` ,`envelope_id` ,`from_customer_name` ,`to_customer_id` ,`activity_id` ,`activity_name` ,`postbox_id` ,`envelope_type_id` ,`weight` ,`weight_unit` ,
            `last_updated_date` ,`completed_by` ,`completed_date` ,`incomming_date` ,`category_type` ,`invoice_flag` ,`shipping_type` ,`include_estamp_flag` ,
            `sync_cloud_flag` ,`envelope_scan_flag` ,`item_scan_flag` ,`direct_shipping_flag` ,`collect_shipping_flag` ,`trash_flag` ,`storage_flag` ,`completed_flag` ,
            `email_notification_flag` ,`activity_code` ,`location_id` 
        FROM envelopes_completed 
        WHERE to_customer_id='{$customer_id}' )";

        ci()->db->query($stmt);
    }

    /**
     * Log postbox.
     *
     * @param unknown $postbox_id
     */
    public static function log_envelope_package_history($customer_id) {
        $stmt = "INSERT INTO envelope_package_history (`package_id`, `customer_id`, `location_available_id`, `package_date`, `package_price`)
        (SELECT `package_id`, `customer_id`, `location_available_id`, `package_date`, `package_price`
        FROM envelope_package 
        WHERE customer_id='{$customer_id}' )";

        ci()->db->query($stmt);
    }
    
    /**
     * Log postbox.
     *
     * @param unknown $postbox_id
     */
    public static function log_postbox_history($postbox_id) {
        $stmt = "INSERT INTO postbox_history (`postbox_id`, `postbox_code`, `customer_id`, `postbox_name`, `location_available_id`, `type`, `name`,
                        `company`, `deleted`, `is_main_postbox`, `plan_deleted_date`, `updated_date`, `apply_date`, `new_postbox_type`,
                        `plan_date_change_postbox_type`, `first_location_flag`, customer_code, email, invoice_name,invoice_company )
        (SELECT 
            `postbox_id`, `postbox_code`, postbox.`customer_id`, `postbox_name`, `location_available_id`, `type`, `name`, `company`, `deleted`,
            `is_main_postbox`, `plan_deleted_date`, `updated_date`, `apply_date`, `new_postbox_type`, `plan_date_change_postbox_type`, `first_location_flag`
            , customers.customer_code, customers.email, invoicing_address_name, invoicing_company
        FROM postbox
        LEFT JOIN customers ON customers.customer_id=postbox.customer_id
        LEFT JOIN customers_address ON customers_address.customer_id=postbox.customer_id
        WHERE postbox.postbox_id='{$postbox_id}' )";
    
        ci()->db->query($stmt);
    }
    
    /**
     * Log postbox.
     *
     * @param unknown $postbox_id
     */
    public static function log_postbox_history_by($customer_id) {
        $stmt = "INSERT INTO postbox_history (`postbox_id`, `postbox_code`, `customer_id`, `postbox_name`, `location_available_id`, `type`, `name`,
            `company`, `deleted`, `is_main_postbox`, `plan_deleted_date`, `updated_date`, `apply_date`, `new_postbox_type`,
            `plan_date_change_postbox_type`, `first_location_flag`, customer_code, email, invoice_name,invoice_company)
        (SELECT `postbox_id`, `postbox_code`, postbox.`customer_id`, `postbox_name`, `location_available_id`, `type`, `name`, `company`, `deleted`,
            `is_main_postbox`, `plan_deleted_date`, `updated_date`, `apply_date`, `new_postbox_type`, `plan_date_change_postbox_type`, `first_location_flag`
            , customers.customer_code, customers.email, invoicing_address_name, invoicing_company
        FROM postbox
        LEFT JOIN customers ON customers.customer_id=postbox.customer_id
        LEFT JOIN customers_address ON customers_address.customer_id=postbox.customer_id
        WHERE postbox.customer_id='{$customer_id}' )";
    
        ci()->db->query($stmt);
    }

    /**
     * log customer cloud history.
     *
     * @param unknown $customer_id
     */
    public static function log_customer_cloud_history($customer_id) {
        $stmt = "INSERT INTO customer_cloud_history (customer_id, cloud_id, auto_save_flag, settings)
        (SELECT customer_id, cloud_id, auto_save_flag, settings FROM customer_cloud WHERE customer_id='{$customer_id}' )";

        ci()->db->query($stmt);
    }
}
?>