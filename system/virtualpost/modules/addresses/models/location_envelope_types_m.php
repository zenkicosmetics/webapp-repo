<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * PyroCMS Settings Model
 *
 * Allows for an easy interface for site settings
 *
 * @author		
 * @package		
 */

class location_envelope_types_m extends MY_Model {
  
    function __construct() {
        parent::__construct();
        $this->profile_table = $this->db->dbprefix('location_envelope_types');
		$this->primary_key = 'id';
    }

    public function getAvailbleTypeByLocation($locationID){

        ci()->load->model('settings/Settings_m');
        ci()->load->model('addresses/location_m');

        $this->db->select('location.*, settings.ActualValue, settings.LabelValue, settings.Alias01, settings.Alias02, settings.Alias03, settings.Alias04, settings.Alias05');
        
        $this->db->from('location_envelope_types');
        $this->db->join('location', 'location_envelope_types.location_id = location.id','inner');
        $this->db->join('settings', 'settings.ActualValue = location_envelope_types.type_id','inner');
        $this->db->where('settings.SettingCode', APConstants::ENVELOPE_TYPE_CODE);
        
        if(!empty($locationID)){
            $this->db->where('location.id', $locationID);
        }

        $this->db->order_by("location_envelope_types.id", "ASC");
		return $this->db->get()->result();
    }

}