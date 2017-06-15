<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * A base model to provide the basic CRUD actions for all models that inherit
 * from it.
 *
 */
class MY_Model extends CI_Model
{
    /**
     * To be applied for Singleton design pattern.
     *
     * @var object
     */
    private static $connection;

    /**
     * The database table to use, only set if you want to bypass the magic.
     *
     * @var string
     */
    protected $_table;

    /**
     * The primary key, by default set to `id`, for use in some functions.
     *
     * @var string
     */
    protected $primary_key = 'id';

    /**
     * An array of functions to be called before a record is created.
     *
     * @var array
     */
    protected $before_create = array();

    /**
     * An array of functions to be called after a record is created.
     *
     * @var array
     */
    protected $after_create = array();

    /**
     * An array of validation rules
     *
     * @var array
     */
    protected $validate = array();

    /**
     * Skip the validation
     *
     * @var bool
     */
    protected $skip_validation = FALSE;

    /**
     * The class constructor, tries to guess the table name.
     *
     * @author Jamie Rumbelow
     */
    public function __construct()
    {
        parent::__construct();
        if (isset($_SERVER['SERVER_NAME'])){
        	$domain_name = isset($_SERVER['HTTP_HOST'])? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
        }else {
        	$domain_name = "https://dev.eu.clevvermail.com";
        }
        
        if (self::$connection) {
            $this->db = self::$connection;
        } else {
            $this->db = $this->load->database($domain_name, TRUE);
            self::$connection = $this->db;
        }

        $this->load->helper('inflector');
        $this->_fetch_table();
    }

    public function __destruct()
    {
        if ($this->db) $this->db->close();
    }

    /**
     *
     * @todo Provide short description.
     * @param string $method
     * @param array $arguments
     * @return \MY_Model
     * @throws Exception
     */
    public function __call($method, $arguments)
    {
        $db_method = array(
            $this->db,
            $method
        );

        if (is_callable($db_method)) {
            $result = call_user_func_array($db_method, $arguments);

            if (is_object($result) && $result === $this->db) {
                return $this;
            }

            return $result;
        }

        throw new Exception("class '" . get_class($this) . "' does not have a method '" . $method . "'");
    }

    /**
     * Get table name
     *
     * @param boolean $prefix
     *            Whether the table name should be prefixed or not.
     * @return string
     */
    public function table_name($prefix = TRUE)
    {
        return $prefix ? $this->db->dbprefix($this->_table) : $this->_table;
    }

    /**
     * Set table name
     *
     * @param string $name
     *            The name for the table.
     * @return string
     */
    public function set_table_name($name = NULL)
    {
        return $this->_table = $name;
    }

    /**
     * Get all paging data
     *
     * @param unknown_type $array_where
     *            The array of condition (array ('name' => 'DungNT', 'age' =>
     *            30))
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
    public function get_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC', $group_by = '')
    {
        // Count all record with input condition
        $total_record = $this->count_by_many($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $this->db->select('*');
        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }
        $this->db->limit($limit, $start);
        if (!empty($sort_column)) {
            $this->db->order_by($sort_column, $sort_type);
        }
        if (!empty($group_by)) {
            $this->db->group_by($group_by);
        }
        $data = $this->db->get($this->_table, $limit, $start)->result();

        return array(
            "total" => $total_record,
            "data" => $data
        );
    }

    /**
     * Get a single record by creating a WHERE clause with a value for your
     * primary key.
     *
     * @author Phil Sturgeon
     * @param string $id
     *            The value of your primary key
     * @return object
     */
    public function get($id)
    {
        return $this->db->where($this->primary_key, $id)->get($this->_table)->row();
    }

    /**
     * Get a single record by creating a WHERE clause with the key of $key and
     * the value of $val.
     *
     * @todo What are the ghost parameters this accepts?
     * @author Phil Sturgeon
     * @return object
     */
    public function get_by()
    {
        $where = &func_get_args();
        $this->_set_where($where);

        return $this->db->get($this->_table)->row();
    }

    /**
     * Similar to get_by(), but returns a result array of many result objects.
     * The function accepts ghost parameters, fetched via func_get_args().
     * Those are:
     * 1. string `$key` The key to search by.
     * 2. string `$value` The value of that key.
     * They are used in the query in the where statement something like:
     * <code>[...] WHERE {$key}={$value} [...]</code>
     *
     * @author Phil Sturgeon
     * @return array
     */
    public function get_by_many($array_where)
    {
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }

        return $this->db->get($this->_table)->row();
    }

    /**
     * Similar to get_by(), but returns a result array of many result objects.
     * The function accepts ghost parameters, fetched via func_get_args().
     * Those are:
     * 1. string `$key` The key to search by.
     * 2. string `$value` The value of that key.
     * They are used in the query in the where statement something like:
     * <code>[...] WHERE {$key}={$value} [...]</code>
     *
     * @author Phil Sturgeon
     * @return array
     */
    public function get_by_many_order($array_where, $order_by = array())
    {
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }

        if (count($order_by) > 0) {
            foreach ($order_by as $key => $value) {
                $this->db->order_by($key, $value);
            }
        }

        return $this->db->get($this->_table)->row();
    }

    /**
     * Get many result objects in an array.
     * Similar to get(), but returns a result array of many result objects.
     *
     * @author Phil Sturgeon
     * @param string $primary_value
     *            The value of your primary key
     * @return array
     */
    public function get_many($primary_value)
    {
        $this->db->where($this->primary_key, $primary_value);
        return $this->get_all();
    }

    /**
     * Similar to get_by(), but returns a result array of many result objects.
     * The function accepts ghost parameters, fetched via func_get_args().
     * Those are:
     * 1. string `$key` The key to search by.
     * 2. string `$value` The value of that key.
     * They are used in the query in the where statement something like:
     * <code>[...] WHERE {$key}={$value} [...]</code>
     *
     * @author Phil Sturgeon
     * @return array
     */
    public function get_many_by_many($array_where, $select = '', $distinct = false, $order_by = array())
    {
        if (!empty($select)) {
            $this->db->select($select);
        }
        if ($distinct) {
            $this->db->distinct();
        }
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }
        if (count($order_by) > 0) {
            foreach ($order_by as $key => $value) {
                $this->db->order_by($key, $value);
            }
        }

        return $this->get_all();
    }

    /**
     * Similar to get_by(), but returns a result array of many result objects.
     * The function accepts ghost parameters, fetched via func_get_args().
     * Those are:
     * 1. string `$key` The key to search by.
     * 2. string `$value` The value of that key.
     * They are used in the query in the where statement something like:
     * <code>[...] WHERE {$key}={$value} [...]</code>
     *
     * @author Phil Sturgeon
     * @return array
     */
    public function get_many_by()
    {
        $where = &func_get_args();
        $this->_set_where($where);

        return $this->get_all();
    }

    /**
     * Get all records in the database
     *
     * @author Jamie Rumbelow
     * @return object
     */
    public function get_all()
    {
        return $this->db->get($this->_table)->result();
    }

    /**
     * Similar to get_by(), but returns a result array of many result objects.
     * The function accepts ghost parameters, fetched via func_get_args().
     * Those are:
     * 1. string `$key` The key to search by.
     * 2. string `$value` The value of that key.
     * They are used in the query in the where statement something like:
     * <code>[...] WHERE {$key}={$value} [...]</code>
     *
     * @author Phil Sturgeon
     * @return array
     */
    public function count_by_many($array_where)
    {
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }

        return $this->db->count_all_results($this->_table);
    }

    /**
     * Similar to get_by(), but returns a result array of many result objects.
     * The function accepts ghost parameters, fetched via func_get_args().
     * Those are:
     * 1. string `$key` The key to search by.
     * 2. string `$value` The value of that key.
     * They are used in the query in the where statement something like:
     * <code>[...] WHERE {$key}={$value} [...]</code>
     *
     * @author Phil Sturgeon
     * @return array
     */
    public function count_distinct_by_many($array_where, $count_column)
    {
        $arr_select = array('count(distinct ' . $count_column . ') as count_val');
        $this->db->select($arr_select);
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }
        $row = $this->db->get($this->_table)->row();
        return $row->count_val;
    }

    /**
     * Similar to get_by(), but returns a result array of many result objects.
     * The function accepts ghost parameters, fetched via func_get_args().
     * Those are:
     * 1. string `$key` The key to search by.
     * 2. string `$value` The value of that key.
     * They are used in the query in the where statement something like:
     * <code>[...] WHERE {$key}={$value} [...]</code>
     *
     * @author Phil Sturgeon
     * @return array
     */
    public function sum_by_many($array_where, $sum_column)
    {
        $arr_select = array('sum(' . $sum_column . ') as sum_val');
        $this->db->select($arr_select);
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }
        $row = $this->db->get($this->_table)->row();
        if (empty($row) || empty($row->sum_val)) {
            return 0;
        }
        return $row->sum_val;
    }

    /**
     * Similar to get_by(), but returns a result array of many result objects.
     * The function accepts ghost parameters, fetched via func_get_args().
     * Those are:
     * 1. string `$key` The key to search by.
     * 2. string `$value` The value of that key.
     * They are used in the query in the where statement something like:
     * <code>[...] WHERE {$key}={$value} [...]</code>
     *
     * @author Phil Sturgeon
     * @return array
     */
    public function count_by()
    {
        $where = &func_get_args();
        $this->_set_where($where);

        return $this->db->count_all_results($this->_table);
    }

    /**
     * Get all records in the database
     *
     * @author Phil Sturgeon
     * @return array
     */
    public function count_all()
    {
        return $this->db->count_all($this->_table);
    }

    /**
     * Insert a new record into the database, calling the before and after
     * create callbacks.
     *
     * @author Jamie Rumbelow
     * @author Dan Horrigan
     * @param array $data
     *            Information
     * @param boolean $skip_validation
     *            Whether we should skip the validation of the data.
     * @return integer true insert ID
     */
    public function insert($data, $skip_validation = FALSE)
    {
        if ($skip_validation === FALSE) {
            if (!$this->_run_validation($data)) {
                return FALSE;
            }
        }

        $data = $this->_run_before_create($data);
        $this->db->insert($this->_table, $data);
        $this->_run_after_create($data, $this->db->insert_id());

        $this->skip_validation = FALSE;

        return $this->db->insert_id();
    }

    /**
     * Insert multiple rows at once.
     * Similar to insert(), just passing an array to insert multiple rows at
     * once.
     *
     * @author Jamie Rumbelow
     * @param array $data
     *            Array of arrays to insert
     * @param boolean $skip_validation
     *            Whether we should skip the validation of the data.
     * @return array An array of insert IDs.
     */
    public function insert_many($data, $skip_validation = FALSE)
    {
        $ids = array();

        foreach ($data as $row) {
            if ($skip_validation === FALSE) {
                if (!$this->_run_validation($data)) {
                    $ids [] = FALSE;

                    continue;
                }
            }

            $data = $this->_run_before_create($row);
            $this->db->insert($this->_table, $row);
            $this->_run_after_create($row, $this->db->insert_id());

            $ids [] = $this->db->insert_id();
        }

        $this->skip_validation = FALSE;
        return $ids;
    }

    /**
     * Update a record, specified by an ID.
     *
     * @author Jamie Rumbelow
     * @param integer $primary_value
     *            The primary key basically the row's ID.
     * @param array $data
     *            The data to update.
     * @param boolean $skip_validation
     *            Whether we should skip the validation of the data.
     * @return boolean
     */
    public function update($primary_value, $data, $skip_validation = FALSE)
    {
        if ($skip_validation === FALSE) {
            if (!$this->_run_validation($data)) {
                return FALSE;
            }
        }

        $this->skip_validation = FALSE;

        return $this->db->where($this->primary_key, $primary_value)->set($data)->update($this->_table);
    }

    /**
     * Update a record, specified by $key and $val.
     * The function accepts ghost parameters, fetched via func_get_args().
     * Those are:
     * 1. string `$key` The key to update with.
     * 2. string `$value` The value to match.
     * 3. array `$data` The data to update with.
     * The first two are used in the query in the where statement something
     * like:
     * <code>UPDATE {table} SET {$key}={$data} WHERE {$key}={$value}</code>
     *
     * @author Jamie Rumbelow
     * @return boolean
     */
    public function update_by()
    {
        $args = &func_get_args();
        $data = array_pop($args);
        $this->_set_where($args);

        if (!$this->_run_validation($data)) {
            return FALSE;
        }

        $this->skip_validation = FALSE;

        return $this->db->set($data)->update($this->_table);
    }

    /**
     * Update a record, specified by $key and $val.
     * The function accepts ghost parameters, fetched via func_get_args().
     * Those are:
     * 1. string `$key` The key to update with.
     * 2. string `$value` The value to match.
     * 3. array `$data` The data to update with.
     * The first two are used in the query in the where statement something
     * like:
     * <code>UPDATE {table} SET {$key}={$data} WHERE {$key}={$value}</code>
     *
     * @author Jamie Rumbelow
     * @return boolean
     */
    public function update_by_many($array_where, $data)
    {
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }

        if (!$this->_run_validation($data)) {
            return FALSE;
        }

        $this->skip_validation = FALSE;

        return $this->db->set($data)->update($this->_table);
    }

    /**
     * Updates many records, specified by an array of IDs.
     *
     * @author Phil Sturgeon
     * @param array $primary_values
     *            The array of IDs
     * @param array $data
     *            The data to update
     * @param boolean $skip_validation
     *            Whether we should skip the validation of the data.
     * @return boolean
     */
    public function update_many($primary_values, $data, $skip_validation = FALSE)
    {
        if ($skip_validation === FALSE) {
            if (!$this->_run_validation($data)) {
                return FALSE;
            }
        }

        $this->skip_validation = FALSE;

        return $this->db->where_in($this->primary_key, $primary_values)->set($data)->update($this->_table);
    }

    /**
     * Updates all records
     *
     * @author Phil Sturgeon
     * @param array $data
     *            The data to update
     * @return bool
     */
    public function update_all($data)
    {
        return $this->db->set($data)->update($this->_table);
    }

    /**
     * Delete a row from the database table by ID.
     *
     * @author Jamie Rumbelow
     * @param integer $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->db->where($this->primary_key, $id)->delete($this->_table);
    }

    /**
     * Delete a row from the database table by the key and value.
     *
     * @author Phil Sturgeon
     * @return bool
     */
    public function delete_by()
    {
        $where = &func_get_args();
        $this->_set_where($where);

        return $this->db->delete($this->_table);
    }

    /**
     * Delete a row from the database table by the key and value.
     *
     * @author Phil Sturgeon
     * @return bool
     */
    public function delete_by_many($array_where)
    {
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }

        return $this->db->delete($this->_table);
    }

    /**
     * Delete many rows from the database table by an array of IDs passed.
     *
     * @author Phil Sturgeon
     * @param array $primary_values
     * @return bool
     */
    public function delete_many($primary_values)
    {
        return $this->db->where_in($this->primary_key, $primary_values)->delete($this->_table);
    }

    /**
     * Generate the dropdown options.
     *
     * @return array The options for the dropdown.
     */
    function dropdown()
    {
        $args = &func_get_args();

        if (count($args) == 2) {
            list ($key, $value) = $args;
        } else {
            $key = $this->primary_key;
            $value = $args [0];
        }

        $query = $this->db->select(array(
            $key,
            $value
        ))->get($this->_table);

        $options = array();
        foreach ($query->result() as $row) {
            $options [$row->{$key}] = $row->{$value};
        }

        return $options;
    }

    /**
     * Orders the result set by the criteria, using the same format as
     * CodeIgniter's AR library.
     *
     * @author Jamie Rumbelow
     * @param string $criteria
     *            The criteria to order by
     * @param string $order
     *            the order direction
     * @return \MY_Model
     */
    public function order_by($criteria, $order = 'ASC')
    {
        $this->db->order_by($criteria, $order);
        return $this;
    }

    /**
     * Limits the result set.
     * Pass an integer to set the actual result limit.
     * Pass a second integer set the offset.
     *
     * @author Jamie Rumbelow
     * @param int $limit
     *            The number of rows
     * @param int $offset
     *            The offset
     * @return \MY_Model
     */
    public function limit($limit, $offset = 0)
    {
        $limit = &func_get_args();
        $this->_set_limit($limit);
        return $this;
    }

    /**
     * Removes duplicate entries from the result set.
     *
     * @author Phil Sturgeon
     * @return \MY_Model
     */
    public function distinct()
    {
        $this->db->distinct();
        return $this;
    }

    /**
     * Run validation only using the
     * same rules as insert/update will
     *
     * @return bool
     */
    public function validate($data)
    {
        return $this->_run_validation($data);
    }

    /**
     * Return only the keys from the validation array
     *
     * @return array
     */
    public function fields()
    {
        $keys = array();

        if ($this->validate) {
            foreach ($this->validate as $key) {
                $keys [] = $key ['field'];
            }
        }

        return $keys;
    }

    /**
     * Runs the before create actions.
     *
     * @author Jamie Rumbelow
     * @param array $data
     *            The array of actions
     * @return mixed
     */
    private function _run_before_create($data)
    {
        foreach ($this->before_create as $method) {
            $data = call_user_func_array(array(
                $this,
                $method
            ), array(
                $data
            ));
        }

        return $data;
    }

    /**
     * Runs the after create actions.
     *
     * @author Jamie Rumbelow
     * @param array $data
     *            The array of actions
     * @param int $id
     */
    private function _run_after_create($data, $id)
    {
        foreach ($this->after_create as $method) {
            call_user_func_array(array(
                $this,
                $method
            ), array(
                $data,
                $id
            ));
        }
    }

    /**
     * Runs validation on the passed data.
     *
     * @author Dan Horrigan
     * @author Jerel Unruh
     * @param array $data
     * @return boolean
     */
    private function _run_validation($data)
    {
        if ($this->skip_validation) {
            return TRUE;
        }

        if (empty($this->validate)) {
            return TRUE;
        }

        $this->load->library('form_validation');

        // only set the model if it can be used for callbacks
        if ($class = get_class($this) and $class !== 'MY_Model') {
            // make sure their MY_Form_validation is set up for it
            if (method_exists($this->form_validation, 'set_model')) {
                $this->form_validation->set_model($class);
            }
        }

        $this->form_validation->set_data($data);

        if (is_array($this->validate)) {
            $this->form_validation->set_rules($this->validate);
            return $this->form_validation->run();
        } else {
            $this->form_validation->run($this->validate);
        }
    }

    /**
     * Fetches the table from the pluralised model name.
     *
     * @author Jamie Rumbelow
     */
    private function _fetch_table()
    {
        if ($this->_table == NULL) {
            $class = preg_replace('/(_m|_model)?$/', '', get_class($this));
            $this->_table = plural(strtolower($class));
        }
    }

    /**
     * Sets where depending on the number of parameters
     *
     * @author Phil Sturgeon
     * @param array $params
     */
    private function _set_where($params)
    {
        if (count($params) == 1) {
            $this->db->where($params [0]);
        } else {
            $this->db->where($params [0], $params [1]);
        }
    }

    /**
     * Sets limit depending on the number of parameters
     *
     * @author Phil Sturgeon
     * @param array $params
     */
    private function _set_limit($params)
    {
        if (count($params) == 1) {
            if (is_array($params [0])) {
                $this->db->limit($params [0] [0], $params [0] [1]);
            } else {
                $this->db->limit($params [0]);
            }
        } else {
            $this->db->limit(( int )$params [0], ( int )$params [1]);
        }
    }
}


class MY_SupperAdminModel extends CI_Model
{
    /**
     * To be applied for Singleton design pattern.
     *
     * @var object
     */
    private static $connection;

    /**
     * The database table to use, only set if you want to bypass the magic.
     *
     * @var string
     */
    protected $_table;

    /**
     * The primary key, by default set to `id`, for use in some functions.
     *
     * @var string
     */
    protected $primary_key = 'id';

    /**
     * An array of functions to be called before a record is created.
     *
     * @var array
     */
    protected $before_create = array();

    /**
     * An array of functions to be called after a record is created.
     *
     * @var array
     */
    protected $after_create = array();

    /**
     * An array of validation rules
     *
     * @var array
     */
    protected $validate = array();

    /**
     * Skip the validation
     *
     * @var bool
     */
    protected $skip_validation = FALSE;

    /**
     * Wrapper to __construct for when loading class is a superclass to a
     * regular
     * controller, i.e.
     * - extends Base not extends Controller.
     *
     * @return void
     * @author Jamie Rumbelow
     */
    public function MY_Model()
    {
        $this->__construct();

    }

    // Declare supprt admin database instance
    private $supperadmin_db;

    /**
     * The class constructor, tries to guess the table name.
     *
     * @author Jamie Rumbelow
     */
    public function __construct()
    {
        parent::__construct();

        if (self::$connection) {
          $this->supperadmin_db = self::$connection;
        } else {
            $this->supperadmin_db = $this->load->database('supper_admin', TRUE);
            self::$connection = $this->supperadmin_db;
        }

        $this->load->helper('inflector');
        $this->_fetch_table();
    }

    public function __destruct()
    {
        if ($this->supperadmin_db) $this->supperadmin_db->close();
    }

    /**
     *
     * @todo Provide short description.
     * @param string $method
     * @param array $arguments
     * @return \MY_Model
     * @throws Exception
     */
    public function __call($method, $arguments)
    {
        $db_method = array(
            $this->supperadmin_db,
            $method
        );

        if (is_callable($db_method)) {
            $result = call_user_func_array($db_method, $arguments);

            if (is_object($result) && $result === $this->supperadmin_db) {
                return $this;
            }

            return $result;
        }

        throw new Exception("class '" . get_class($this) . "' does not have a method '" . $method . "'");
    }

    /**
     * Get table name
     *
     * @param boolean $prefix
     *            Whether the table name should be prefixed or not.
     * @return string
     */
    public function table_name($prefix = TRUE)
    {
        return $prefix ? $this->supperadmin_db->dbprefix($this->_table) : $this->_table;
    }

    /**
     * Set table name
     *
     * @param string $name
     *            The name for the table.
     * @return string
     */
    public function set_table_name($name = NULL)
    {
        return $this->_table = $name;
    }

    /**
     * Get all paging data
     *
     * @param unknown_type $array_where
     *            The array of condition (array ('name' => 'DungNT', 'age' =>
     *            30))
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
    public function get_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        $total_record = $this->count_by_many($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $this->supperadmin_db->select('*');
        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            $this->supperadmin_db->where($key, $value);
        }
        $this->supperadmin_db->limit($limit, $start);
        if (!empty($sort_column)) {
            $this->supperadmin_db->order_by($sort_column, $sort_type);
        }
        $data = $this->supperadmin_db->get($this->_table, $limit, $start)->result();

        return array(
            "total" => $total_record,
            "data" => $data
        );
    }

    /**
     * Get a single record by creating a WHERE clause with a value for your
     * primary key.
     *
     * @author Phil Sturgeon
     * @param string $id
     *            The value of your primary key
     * @return object
     */
    public function get($id)
    {
        return $this->supperadmin_db->where($this->primary_key, $id)->get($this->_table)->row();
    }

    /**
     * Get a single record by creating a WHERE clause with the key of $key and
     * the value of $val.
     *
     * @todo What are the ghost parameters this accepts?
     * @author Phil Sturgeon
     * @return object
     */
    public function get_by()
    {
        $where = &func_get_args();
        $this->_set_where($where);

        return $this->supperadmin_db->get($this->_table)->row();
    }

    /**
     * Similar to get_by(), but returns a result array of many result objects.
     * The function accepts ghost parameters, fetched via func_get_args().
     * Those are:
     * 1. string `$key` The key to search by.
     * 2. string `$value` The value of that key.
     * They are used in the query in the where statement something like:
     * <code>[...] WHERE {$key}={$value} [...]</code>
     *
     * @author Phil Sturgeon
     * @return array
     */
    public function get_by_many($array_where)
    {
        foreach ($array_where as $key => $value) {
            $this->supperadmin_db->where($key, $value);
        }

        return $this->supperadmin_db->get($this->_table)->row();
    }

    /**
     * Similar to get_by(), but returns a result array of many result objects.
     * The function accepts ghost parameters, fetched via func_get_args().
     * Those are:
     * 1. string `$key` The key to search by.
     * 2. string `$value` The value of that key.
     * They are used in the query in the where statement something like:
     * <code>[...] WHERE {$key}={$value} [...]</code>
     *
     * @author Phil Sturgeon
     * @return array
     */
    public function get_by_many_order($array_where, $order_by = array())
    {
        foreach ($array_where as $key => $value) {
            $this->supperadmin_db->where($key, $value);
        }

        if (count($order_by) > 0) {
            foreach ($order_by as $key => $value) {
                $this->supperadmin_db->order_by($key, $value);
            }
        }

        return $this->supperadmin_db->get($this->_table)->row();
    }

    /**
     * Get many result objects in an array.
     * Similar to get(), but returns a result array of many result objects.
     *
     * @author Phil Sturgeon
     * @param string $primary_value
     *            The value of your primary key
     * @return array
     */
    public function get_many($primary_value)
    {
        $this->supperadmin_db->where($this->primary_key, $primary_value);
        return $this->get_all();
    }

    /**
     * Similar to get_by(), but returns a result array of many result objects.
     * The function accepts ghost parameters, fetched via func_get_args().
     * Those are:
     * 1. string `$key` The key to search by.
     * 2. string `$value` The value of that key.
     * They are used in the query in the where statement something like:
     * <code>[...] WHERE {$key}={$value} [...]</code>
     *
     * @author Phil Sturgeon
     * @return array
     */
    public function get_many_by_many($array_where, $select = '', $distinct = false, $order_by = array())
    {
        if (!empty($select)) {
            $this->supperadmin_db->select($select);
        }
        if ($distinct) {
            $this->supperadmin_db->distinct();
        }
        foreach ($array_where as $key => $value) {
            $this->supperadmin_db->where($key, $value);
        }
        if (count($order_by) > 0) {
            foreach ($order_by as $key => $value) {
                $this->supperadmin_db->order_by($key, $value);
            }
        }

        return $this->get_all();
    }

    /**
     * Similar to get_by(), but returns a result array of many result objects.
     * The function accepts ghost parameters, fetched via func_get_args().
     * Those are:
     * 1. string `$key` The key to search by.
     * 2. string `$value` The value of that key.
     * They are used in the query in the where statement something like:
     * <code>[...] WHERE {$key}={$value} [...]</code>
     *
     * @author Phil Sturgeon
     * @return array
     */
    public function get_many_by()
    {
        $where = &func_get_args();
        $this->_set_where($where);

        return $this->get_all();
    }

    /**
     * Get all records in the database
     *
     * @author Jamie Rumbelow
     * @return object
     */
    public function get_all()
    {
        return $this->supperadmin_db->get($this->_table)->result();
    }

    /**
     * Similar to get_by(), but returns a result array of many result objects.
     * The function accepts ghost parameters, fetched via func_get_args().
     * Those are:
     * 1. string `$key` The key to search by.
     * 2. string `$value` The value of that key.
     * They are used in the query in the where statement something like:
     * <code>[...] WHERE {$key}={$value} [...]</code>
     *
     * @author Phil Sturgeon
     * @return array
     */
    public function count_by_many($array_where)
    {

        foreach ($array_where as $key => $value) {
            $this->supperadmin_db->where($key, $value);
        }
        return $this->supperadmin_db->count_all_results($this->_table);
    }

    /**
     * Similar to get_by(), but returns a result array of many result objects.
     * The function accepts ghost parameters, fetched via func_get_args().
     * Those are:
     * 1. string `$key` The key to search by.
     * 2. string `$value` The value of that key.
     * They are used in the query in the where statement something like:
     * <code>[...] WHERE {$key}={$value} [...]</code>
     *
     * @author Phil Sturgeon
     * @return array
     */
    public function sum_by_many($array_where, $sum_column)
    {
        $this->supperadmin_db->select('sum(' . $sum_column . ') as sum_val');
        foreach ($array_where as $key => $value) {
            $this->supperadmin_db->where($key, $value);
        }
        $row = $this->supperadmin_db->get($this->_table)->row();
        return $row->sum_val;
    }

    /**
     * Similar to get_by(), but returns a result array of many result objects.
     * The function accepts ghost parameters, fetched via func_get_args().
     * Those are:
     * 1. string `$key` The key to search by.
     * 2. string `$value` The value of that key.
     * They are used in the query in the where statement something like:
     * <code>[...] WHERE {$key}={$value} [...]</code>
     *
     * @author Phil Sturgeon
     * @return array
     */
    public function count_by()
    {
        $where = &func_get_args();
        $this->_set_where($where);

        return $this->supperadmin_db->count_all_results($this->_table);
    }

    /**
     * Get all records in the database
     *
     * @author Phil Sturgeon
     * @return array
     */
    public function count_all()
    {
        return $this->supperadmin_db->count_all($this->_table);
    }

    /**
     * Insert a new record into the database, calling the before and after
     * create callbacks.
     *
     * @author Jamie Rumbelow
     * @author Dan Horrigan
     * @param array $data
     *            Information
     * @param boolean $skip_validation
     *            Whether we should skip the validation of the data.
     * @return integer true insert ID
     */
    public function insert($data, $skip_validation = FALSE)
    {
        if ($skip_validation === FALSE) {
            if (!$this->_run_validation($data)) {
                return FALSE;
            }
        }

        $data = $this->_run_before_create($data);
        $this->supperadmin_db->insert($this->_table, $data);
        $this->_run_after_create($data, $this->supperadmin_db->insert_id());

        $this->skip_validation = FALSE;

        return $this->supperadmin_db->insert_id();
    }

    /**
     * Insert multiple rows at once.
     * Similar to insert(), just passing an array to insert multiple rows at
     * once.
     *
     * @author Jamie Rumbelow
     * @param array $data
     *            Array of arrays to insert
     * @param boolean $skip_validation
     *            Whether we should skip the validation of the data.
     * @return array An array of insert IDs.
     */
    public function insert_many($data, $skip_validation = FALSE)
    {
        $ids = array();

        foreach ($data as $row) {
            if ($skip_validation === FALSE) {
                if (!$this->_run_validation($data)) {
                    $ids [] = FALSE;

                    continue;
                }
            }

            $data = $this->_run_before_create($row);
            $this->supperadmin_db->insert($this->_table, $row);
            $this->_run_after_create($row, $this->supperadmin_db->insert_id());

            $ids [] = $this->supperadmin_db->insert_id();
        }

        $this->skip_validation = FALSE;
        return $ids;
    }

    /**
     * Update a record, specified by an ID.
     *
     * @author Jamie Rumbelow
     * @param integer $primary_value
     *            The primary key basically the row's ID.
     * @param array $data
     *            The data to update.
     * @param boolean $skip_validation
     *            Whether we should skip the validation of the data.
     * @return boolean
     */
    public function update($primary_value, $data, $skip_validation = FALSE)
    {
        if ($skip_validation === FALSE) {
            if (!$this->_run_validation($data)) {
                return FALSE;
            }
        }

        $this->skip_validation = FALSE;

        return $this->supperadmin_db->where($this->primary_key, $primary_value)->set($data)->update($this->_table);
    }

    /**
     * Update a record, specified by $key and $val.
     * The function accepts ghost parameters, fetched via func_get_args().
     * Those are:
     * 1. string `$key` The key to update with.
     * 2. string `$value` The value to match.
     * 3. array `$data` The data to update with.
     * The first two are used in the query in the where statement something
     * like:
     * <code>UPDATE {table} SET {$key}={$data} WHERE {$key}={$value}</code>
     *
     * @author Jamie Rumbelow
     * @return boolean
     */
    public function update_by()
    {
        $args = &func_get_args();
        $data = array_pop($args);
        $this->_set_where($args);

        if (!$this->_run_validation($data)) {
            return FALSE;
        }

        $this->skip_validation = FALSE;

        return $this->supperadmin_db->set($data)->update($this->_table);
    }

    /**
     * Update a record, specified by $key and $val.
     * The function accepts ghost parameters, fetched via func_get_args().
     * Those are:
     * 1. string `$key` The key to update with.
     * 2. string `$value` The value to match.
     * 3. array `$data` The data to update with.
     * The first two are used in the query in the where statement something
     * like:
     * <code>UPDATE {table} SET {$key}={$data} WHERE {$key}={$value}</code>
     *
     * @author Jamie Rumbelow
     * @return boolean
     */
    public function update_by_many($array_where, $data)
    {
        foreach ($array_where as $key => $value) {
            $this->supperadmin_db->where($key, $value);
        }

        if (!$this->_run_validation($data)) {
            return FALSE;
        }

        $this->skip_validation = FALSE;

        return $this->supperadmin_db->set($data)->update($this->_table);
    }

    /**
     * Updates many records, specified by an array of IDs.
     *
     * @author Phil Sturgeon
     * @param array $primary_values
     *            The array of IDs
     * @param array $data
     *            The data to update
     * @param boolean $skip_validation
     *            Whether we should skip the validation of the data.
     * @return boolean
     */
    public function update_many($primary_values, $data, $skip_validation = FALSE)
    {
        if ($skip_validation === FALSE) {
            if (!$this->_run_validation($data)) {
                return FALSE;
            }
        }

        $this->skip_validation = FALSE;

        return $this->supperadmin_db->where_in($this->primary_key, $primary_values)->set($data)->update($this->_table);
    }

    /**
     * Updates all records
     *
     * @author Phil Sturgeon
     * @param array $data
     *            The data to update
     * @return bool
     */
    public function update_all($data)
    {
        return $this->supperadmin_db->set($data)->update($this->_table);
    }

    /**
     * Delete a row from the database table by ID.
     *
     * @author Jamie Rumbelow
     * @param integer $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->supperadmin_db->where($this->primary_key, $id)->delete($this->_table);
    }

    /**
     * Delete a row from the database table by the key and value.
     *
     * @author Phil Sturgeon
     * @return bool
     */
    public function delete_by()
    {
        $where = &func_get_args();
        $this->_set_where($where);

        return $this->supperadmin_db->delete($this->_table);
    }

    /**
     * Delete a row from the database table by the key and value.
     *
     * @author Phil Sturgeon
     * @return bool
     */
    public function delete_by_many($array_where)
    {
        foreach ($array_where as $key => $value) {
            $this->supperadmin_db->where($key, $value);
        }

        return $this->supperadmin_db->delete($this->_table);
    }

    /**
     * Delete many rows from the database table by an array of IDs passed.
     *
     * @author Phil Sturgeon
     * @param array $primary_values
     * @return bool
     */
    public function delete_many($primary_values)
    {
        return $this->supperadmin_db->where_in($this->primary_key, $primary_values)->delete($this->_table);
    }

    /**
     * Generate the dropdown options.
     *
     * @return array The options for the dropdown.
     */
    function dropdown()
    {
        $args = &func_get_args();

        if (count($args) == 2) {
            list ($key, $value) = $args;
        } else {
            $key = $this->primary_key;
            $value = $args [0];
        }

        $query = $this->supperadmin_db->select(array(
            $key,
            $value
        ))->get($this->_table);

        $options = array();
        foreach ($query->result() as $row) {
            $options [$row->{$key}] = $row->{$value};
        }

        return $options;
    }

    /**
     * Orders the result set by the criteria, using the same format as
     * CodeIgniter's AR library.
     *
     * @author Jamie Rumbelow
     * @param string $criteria
     *            The criteria to order by
     * @param string $order
     *            the order direction
     * @return \MY_Model
     */
    public function order_by($criteria, $order = 'ASC')
    {
        $this->supperadmin_db->order_by($criteria, $order);
        return $this;
    }

    /**
     * Limits the result set.
     * Pass an integer to set the actual result limit.
     * Pass a second integer set the offset.
     *
     * @author Jamie Rumbelow
     * @param int $limit
     *            The number of rows
     * @param int $offset
     *            The offset
     * @return \MY_Model
     */
    public function limit($limit, $offset = 0)
    {
        $limit = &func_get_args();
        $this->_set_limit($limit);
        return $this;
    }

    /**
     * Removes duplicate entries from the result set.
     *
     * @author Phil Sturgeon
     * @return \MY_Model
     */
    public function distinct()
    {
        $this->supperadmin_db->distinct();
        return $this;
    }

    /**
     * Run validation only using the
     * same rules as insert/update will
     *
     * @return bool
     */
    public function validate($data)
    {
        return $this->_run_validation($data);
    }

    /**
     * Return only the keys from the validation array
     *
     * @return array
     */
    public function fields()
    {
        $keys = array();

        if ($this->validate) {
            foreach ($this->validate as $key) {
                $keys [] = $key ['field'];
            }
        }

        return $keys;
    }

    /**
     * Runs the before create actions.
     *
     * @author Jamie Rumbelow
     * @param array $data
     *            The array of actions
     * @return mixed
     */
    private function _run_before_create($data)
    {
        foreach ($this->before_create as $method) {
            $data = call_user_func_array(array(
                $this,
                $method
            ), array(
                $data
            ));
        }

        return $data;
    }

    /**
     * Runs the after create actions.
     *
     * @author Jamie Rumbelow
     * @param array $data
     *            The array of actions
     * @param int $id
     */
    private function _run_after_create($data, $id)
    {
        foreach ($this->after_create as $method) {
            call_user_func_array(array(
                $this,
                $method
            ), array(
                $data,
                $id
            ));
        }
    }

    /**
     * Runs validation on the passed data.
     *
     * @author Dan Horrigan
     * @author Jerel Unruh
     * @param array $data
     * @return boolean
     */
    private function _run_validation($data)
    {
        if ($this->skip_validation) {
            return TRUE;
        }

        if (empty($this->validate)) {
            return TRUE;
        }

        $this->load->library('form_validation');

        // only set the model if it can be used for callbacks
        if ($class = get_class($this) and $class !== 'MY_Model') {
            // make sure their MY_Form_validation is set up for it
            if (method_exists($this->form_validation, 'set_model')) {
                $this->form_validation->set_model($class);
            }
        }

        $this->form_validation->set_data($data);

        if (is_array($this->validate)) {
            $this->form_validation->set_rules($this->validate);
            return $this->form_validation->run();
        } else {
            $this->form_validation->run($this->validate);
        }
    }

    /**
     * Fetches the table from the pluralised model name.
     *
     * @author Jamie Rumbelow
     */
    private function _fetch_table()
    {
        if ($this->_table == NULL) {
            $class = preg_replace('/(_m|_model)?$/', '', get_class($this));
            $this->_table = plural(strtolower($class));
        }
    }

    /**
     * Sets where depending on the number of parameters
     *
     * @author Phil Sturgeon
     * @param array $params
     */
    private function _set_where($params)
    {
        if (count($params) == 1) {
            $this->supperadmin_db->where($params [0]);
        } else {
            $this->supperadmin_db->where($params [0], $params [1]);
        }
    }

    /**
     * Sets limit depending on the number of parameters
     *
     * @author Phil Sturgeon
     * @param array $params
     */
    private function _set_limit($params)
    {
        if (count($params) == 1) {
            if (is_array($params [0])) {
                $this->supperadmin_db->limit($params [0] [0], $params [0] [1]);
            } else {
                $this->supperadmin_db->limit($params [0]);
            }
        } else {
            $this->supperadmin_db->limit(( int )$params [0], ( int )$params [1]);
        }
    }
}