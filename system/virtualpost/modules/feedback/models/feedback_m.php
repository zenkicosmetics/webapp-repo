<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @copyright Copyright (c) 2012-2013 
 * @author Bui Duc Tien <tienbd@gmail.com>
 * @website http://www.flightpedia.org
 * @created 2/19/2013
 */


class feedback_m extends MY_Model {
	protected $_table = 'feedback';
	protected $primary_key = 'FeedbackID';
	
	function get_all()
	{
		$this->db->select('feedback.*');
		return $this->db->get('feedback')->result();
	}

	function get($id)
	{
		return $this->db->select('feedback.*')
				->where(array('feedback.FeedbackID' => $id))
				->get('feedback')
				->row();
	}
	
	function publish($id = 0)
	{
		$this->db->select('feedback.status')
			 ->where(array('feedback.FeedbackID' => $id));
		$feedback = $this->db->get($this->_table)->row();
		if($feedback->status == '1')
			return parent::update($id, array('status' => '0'));
		else
			return parent::update($id, array('status' => '1'));
	}
	
	public function delete($id)
	{	
		return $this->db->delete('feedback', array('FeedbackID' => $id));
	}
	
	function check_exists($field, $value = '', $id = 0)
	{
		if (is_array($field))
		{
			$params = $field;
			$id = $value;
		}
		else
		{
			$params[$field] = $value;
		}
		$params['FeedbackID !='] = (int) $id;

		return parent::count_by($params) == 0;
	}

	/**
     * Get all paging data
     *
     * @param unknown_type $array_where
     * The array of condition (array ('name' => 'TienBD', 'age' => 30))
     * @param unknown_type $start the offset paging
     * @param unknown_type $limit the number of record per page
     * @param unknown_type $sort_column the sort column
     * @param unknown_type $sort_type the sort type
     * @return The array object array('total' => '9999', 'data' => '');
     */
    public function get_feedback_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC') {
        // Count all record with input condition
        $total_record = $this->count_by_many($array_where);
        if ($total_record == 0) {
            return array (
                    "total" => 0,
                    "data" => array ()
            );
        }
    
        $this->db->select('Feedback.*');
        // Search all data with input condition
        foreach ( $array_where as $key => $value ) {
            $this->db->where($key, $value);
        }
        $this->db->limit($limit);
        if (! empty($sort_column)) {
            $this->db->order_by($sort_column, $sort_type);
        }
        $data = $this->db->get($this->_table, $limit, $start)->result();
    
        return array (
                "total" => $total_record,
                "data" => $data
        );
    }
		
}

/* End of file feedback.php */
