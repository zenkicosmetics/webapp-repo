<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Group model
 */
class Language_text_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->set_table_name('language_text');
        $this->profile_table = $this->db->dbprefix('language_text');
        $this->primary_key = 'id';
    }


    public function get_language($language_code, $language_key) {
        $this->db->select('*');
        $this->db->from($this->_table);
        $this->db->where('code_id', $language_code);
        $this->db->where('key_id', $language_key);

        $query = $this->db->get();

        return $query->row();
    }

    /**
     * Display language in system
     */
    public function language($language_code, $language_key){
        $this->db->select('*');
        $this->db->from($this->_table);
        $this->db->join('language_codes', 'language_codes.id = language_text.code_id');
        $this->db->join('language_keys', 'language_keys.id = language_text.key_id');
        $this->db->where('language_codes.code', $language_code);
        $this->db->where('language_keys.key', $language_key);

        $query = $this->db->get();

        return $query->row();
    }


    public function insertLanguageData() {

        $tmp_data = $this->db->query("SELECT * FROM tmp_language_keys, tmp_language_text WHERE tmp_language_keys.id = tmp_language_text.key_id")->result();

        if (empty($tmp_data)) {
            echo 'Please insert language data from your local table into tmp_language_keys and tmp_language_text';
            return;
        }

        //print_r($tmp_data);die;

        $current_data = $this->db->query("SELECT * FROM language_keys")->result_array();
        $current_language_key = array_column($current_data, 'key');

        //print_r($current_language_key);die;

        //Add new key from tmp_language_keys to language_keys
        $insert_data_key = array();
        $insert_data_text = array();
        foreach ($tmp_data as $item) {
            $insert_data_key_value = array_column($insert_data_key, 'key');
            if (!in_array($item->key, $current_language_key) && !in_array($item->key, $insert_data_key_value)) {
                $insert_data_key[] = array('key' => $item->key);
                $insert_data_text[] = array('key' => $item->key, 'value' => $item->value, 'code_id' => $item->code_id);
            }
        }

        //Add db
         if (empty($insert_data_key)) {
            echo 'Only insert different key. All different key from local already have been inserted to DEV';
            return;
        }

        $this->db->insert_batch('language_keys', $insert_data_key);

        //Insert data to table language text
        foreach($insert_data_text as $item) {
            //get key id
            $language_key = $this->db->select('*')->where(array('key' => $item['key']))->get('language_keys')->row();
            $key_id = empty($language_key->id) ? '0' : $language_key->id;
            if (!empty($key_id)) {
                //Insert language text
                $this->db->insert($this->_table, array('code_id' => $item['code_id'], 'key_id' => $key_id, 'value' => $item['value']));
            }
        }

        return 'Already insert '.count($insert_data_key).' key to table language_keys and '.count($insert_data_text).' text to table language_text';
    }

    public function addToDbLanguages($data, $code_id = 1) {
        // Insert language key
        $keys = array_keys($data);
        $key_ids = [];
        for($i = 0; $i < count($keys); $i ++) {
            $this->db->insert('language_keys',['key' => $keys[$i]]);
            $id = $this->db->insert_id();
            $key_ids[] = $id;
            if ($id) {
                // Insert in language text
                $this->db->insert('language_text', [
                        'code_id'   =>  $code_id,
                        'key_id'    =>  $id,
                        'value' =>  $data[$keys[$i]]
                    ]);
            }
        }
        return $key_ids;
    }
    public function update_batch_languages($data, $where)
    {
        foreach ($data as $key => $value) {
            $update = [];
            foreach ($value as $v_key => $v_value) {
                if (in_array($v_key, $where)) {
                   $this->db->where(["$v_key = $v_value" => null]);
                } else {
                    $update = ["$v_key" => $v_value];
                }
            }
            $this->db->update('language_text', $update);
        }
    }
}