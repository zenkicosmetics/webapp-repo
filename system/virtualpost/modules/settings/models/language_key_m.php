<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Group model
 */
class Language_key_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->set_table_name('language_keys');
        $this->profile_table = $this->db->dbprefix('language_keys');
        $this->primary_key = 'id';
    }

    /**
     * Get all paging data
     *
     * @param unknown_type $array_where
     *            The array of condition (array ('name' => 'KhoiLV', 'age' => 30))
     * @param unknown_type $start
     *            The offset paging
     * @param unknown_type $limit
     *            The number of record per page
     * @param unknown_type $sort_column
     *            The sort column
     * @param unknown_type $sort_type
     *            The sort type
     * @return The array object array('total' => '9999', 'data' => '');
     */
    public function get_list_language_paging(array $array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        $total_record = $this->count_by_many($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $this->db->select('language_keys.id, language_keys.key, language_text.code_id, language_text.value');
        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }
        $this->db->limit($limit);
        if (!empty($sort_column)) {
            $this->db->order_by($sort_column, $sort_type);
        }

        $this->db->join('language_text', 'language_text.key_id = language_keys.id', 'left');
        $this->db->group_by('language_keys.key');

        $rows = $this->db->get($this->_table, $limit, $start)->result();

        return array(
            "total" => $total_record,
            "data" => $rows
        );
    }

    public function get_full_list($array_where = NULL) {
        $this->db->select('language_keys.id, language_keys.key, language_text.code_id, language_text.value');
        if ($array_where) {
            // Search all data with input condition
            foreach ($array_where as $key => $value) {
                $this->db->where($key, $value);
            }
        }
        $this->db->join('language_text', 'language_text.key_id = language_keys.id', 'left');
        $this->db->group_by('language_keys.key');
        $rows = $this->db->get($this->_table)->result();
        return $rows;
    }

    public function count_by_many($array_where) {
        $this->db->select('language_keys.id, language_keys.key, language_text.code_id, language_text.value');
        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }

        $this->db->join('language_text', 'language_text.key_id = language_keys.id', 'left');
        $this->db->group_by('language_keys.key');

        $rows = $this->db->get($this->_table)->result();
        return count($rows);
    }

    public function change_key_name($old, $new, $insert = NULL) {
        $this->db->where('key', $old)->update('language_keys', ['key' => $new]);
        return $new;
    }

}