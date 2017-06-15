<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Group model
 */
class envelope_shipping_tracking_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->_table = $this->db->dbprefix('envelope_shipping_tracking');
        $this->primary_key = 'id';
    }

    public function saveTrackingNumber($envelope_id, $shipping_services_id, $tracking_number, $package_id){
        //Get current tracking number of this envelope
        $check_shipping_tracking = $this->get_by("envelope_id",$envelope_id);
        //If existed, update it
        if(!empty($check_shipping_tracking)){
            $this->update_by_many(
            array(
                "envelope_id" => $envelope_id
            ),        
            array(
                "tracking_number" => $tracking_number,
                "shipping_services_id" => $shipping_services_id,
                "package_id"           => $package_id
            ));
        } else { 
            //If does not existe yet, create new
            $this->insert(array(
                "envelope_id" => $envelope_id,
                "tracking_number" => $tracking_number,
                "shipping_services_id" => $shipping_services_id,
                "package_id"           => $package_id,
                "created_date" => now()
            ));
        }
    }
}