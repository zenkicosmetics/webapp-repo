<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Group model
 */
class Language_code_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->set_table_name('language_codes');
        $this->profile_table = $this->db->dbprefix('language_codes');
        $this->primary_key = 'id';
    }

    /**
     * Return list active languages
     * @return array
     */
    public function getActiveLanguages()
    {
        $languages = $this->get_many_by_many(array('status' => 1),'', true);

        return $languages;
    }
}