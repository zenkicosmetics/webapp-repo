<?php defined('BASEPATH') or exit('No direct script access allowed');

class script_m extends MY_Model
{
    const TABLE_PREFIX = 'temp_';
    const MAX_RECORDS_BULK_INSERT = 100;
    const MAX_RECORDS_SELECT = 1000;
    const MSG_CREATE_TEMP_TABLE_SUCCESS = "DONE! Now, please check data in the temporary table '%s'.";
    const MSG_DATA_EMPTY = "EMPTY! Your SQL query returns no data.";

    private $tableName;
    private $fieldList;

    public function __construct()
    {
        parent::__construct();
    }

    public function createTempTableFromSQLQuery($tableName, $sql, $drop = false)
    {
        $data = $this->getDataFromSQLQuery($sql);
        if ($data) {
            $columns = array_keys($data[0]);

            $this->setTempTableName($tableName);
            $this->setFieldList($columns);
            $this->createTempTable($columns, $drop);

            $records = array();
            $recordNum = 0;
            foreach ($data as $record) {
                $records[] = $record;
                if (++$recordNum >= self::MAX_RECORDS_BULK_INSERT) {
                    $this->bulkInsertDataToTempTable($records, $columns);
                    $recordNum = 0;
                    unset($records);
                    $records = array();
                }
            }
            if ($records) {
                $this->bulkInsertDataToTempTable($records, $columns);
                unset($records);
            }
            unset($data);
            echo sprintf(self::MSG_CREATE_TEMP_TABLE_SUCCESS, $this->tableName);
        } else {
            echo self::MSG_DATA_EMPTY;
        }
    }

    public function getDataFromTempTable($tableName = '', $loop = false)
    {
        $tableName = $this->getTempTableName($tableName);
        if ($loop) {
            $limit = self::MAX_RECORDS_SELECT;
            $sql = "SELECT * FROM {$tableName} WHERE read_flag = 0 LIMIT 0, {$limit}";
        } else {
            $sql = "SELECT * FROM {$tableName}";
        }

        $query = $this->db->query($sql);
        $rows = ($query->num_rows() > 0) ? $query->result() : false;

        if ($loop && $rows) {
            $arrayIDs = array();
            foreach ($rows as $row) $arrayIDs[] = $row->_id;
            $this->updateTempTable($tableName, $arrayIDs);
        }

        return $rows;
    }

    public function executeQuery($sql, $returnResult = false, $resultArray = false)
    {
        if ($returnResult) {
            $query = $this->db->query($sql);
            $rows = ($resultArray) ? $query->result_array() : $query->result();

            return $rows;
        } else {
            $this->db->query($sql);

            return true;
        }
    }

    /**
     * @Description: get list customers have been deleted but remain postbox is not deleted
     */
    public function  getListCustomers(){

        $query =    "SELECT customers.customer_id, email,
                    STATUS , postbox_id, deleted, count( * ) AS totalPostbox
                    FROM customers
                    INNER JOIN postbox ON customers.customer_id = postbox.customer_id
                    WHERE customers.status =1
                    AND postbox.deleted =0
                    GROUP BY customer_id
                    ORDER BY totalPostbox DESC";

        $result = $this->db->query($query)->result();

        return $result;

    }

    private function createTempTable(array $columns, $drop = false)
    {
        if ($this->existsTempTable()) {
            if ($drop) {
                $sql = "DROP TABLE IF EXISTS {$this->tableName}";
                $this->db->query($sql);
                $sql = $this->makeSQLCreateTable($columns);
                $this->db->query($sql);
            } else {
                //$sql = "DELETE FROM {$this->tableName};";
                $sql = "TRUNCATE TABLE {$this->tableName};";
                $this->db->query($sql);
            }
        } else {
            $sql = $this->makeSQLCreateTable($columns);
            $this->db->query($sql);
        }
    }

    private function existsTempTable()
    {
        $sql = "SHOW TABLES LIKE '{$this->tableName}'";
        $query = $this->db->query($sql);
        $existed = $query->result();

        return empty($existed) ? false : true;
    }

    private function makeSQLCreateTable(array $columns)
    {
        $sql = array();

        $sql[] = "CREATE TABLE IF NOT EXISTS {$this->tableName} (";
        $sql[] = '_id BIGINT NOT NULL AUTO_INCREMENT,';
        foreach ($columns as $column) {
            $sql[] = "{$column} VARCHAR(255) DEFAULT NULL,";
        }
        $sql[] = 'read_flag TINYINT DEFAULT 0,';
        $sql[] = 'PRIMARY KEY (_id)';
        $sql[] = ') ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1';
        $sql = implode(PHP_EOL, $sql);

        return $sql;
    }

    private function bulkInsertDataToTempTable(array $data, array $columns = array())
    {
        if ($columns) {
            $columns = array_keys($data[0]);
            $fieldList = implode(',', $columns);
        } else {
            $fieldList = $this->fieldList;
        }

        $sql = array();
        $sql[] = "INSERT INTO {$this->tableName}({$fieldList}) VALUES ";
        foreach ($data as $record) {
            $sql[] = "('" . implode("','", array_map('addslashes', array_values($record))) . "'),";
        }
        $sql = implode(PHP_EOL, $sql);
        $sql = substr($sql, 0, strlen($sql) - 1);
        $sql .= ";";

        $this->db->query($sql);
    }

    /*
     * Returns the query result as a pure array, or an empty array when no result is produced.
     * Typically you'll use this in a foreach loop
     */
    private function getDataFromSQLQuery($sql)
    {
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    private function setFieldList(array $columns)
    {
        $this->fieldList = implode(',', $columns);
    }

    private function setTempTableName($tableName)
    {
        if (!$this->hasTablePrefix($tableName)) {
            $tableName = self::TABLE_PREFIX . $tableName;
        }
        $this->tableName = strtolower($tableName);
    }

    private function getTempTableName($tableName)
    {
        if ($tableName) {
            if (!$this->hasTablePrefix($tableName)) {
                $tableName = self::TABLE_PREFIX . $tableName;
                $this->tableName = strtolower($tableName);
            }
        } else {
            $tableName = $this->tableName;
        }

        return $tableName;
    }

    private function hasTablePrefix($tableName)
    {
        $tableName = strtolower($tableName);
        return (strpos($tableName, self::TABLE_PREFIX) === 0);
    }

    private function updateTempTable($tableName, array $arrayIDs)
    {
        $tableName = $this->getTempTableName($tableName);
        $arrayIDs = implode(',', $arrayIDs);
        $sql = "UPDATE {$tableName} SET read_flag = 1 WHERE _id IN ($arrayIDs)";
        $this->db->query($sql);
    }

    // ---------------------------- NEW METHODS -----------------------------------

    public function checkTableExists($tableName)
    {
        $sql = "SHOW TABLES LIKE '{$tableName}'";
        $query = $this->db->query($sql);
        $existed = $query->result();

        return empty($existed) ? false : true;
    }

    public function getTableFields($targetTable)
    {
        $sql = "SHOW COLUMNS FROM {$targetTable}";
        $query = $this->db->query($sql);
        $rows = $query->result();

        return $rows;
    }

    public function get_list_customer_vat()
    {
        
        $sql = "select t.*, count(customer_id) as total FROM (SELECT id, customer_id, vat FROM `invoice_summary` GROUP BY customer_id, vat) as t group by customer_id ORDER by customer_id asc ";

        $query = $this->db->query($sql);

        $rows  = $query->result();

        return $rows;
    }

    public function get_invoice_detail($customer_id){

        $sql = "select c.* from (select t.*, count(customer_id) as total FROM (SELECT id,customer_id,invoice_summary_id,location_id,envelope_id,activity_type FROM `invoice_detail` where (location_id is not null)  GROUP by customer_id, location_id) as t group by customer_id) as c WHERE customer_id = ".$customer_id;

        $query = $this->db->query($sql);

        $rows  = $query->result();

        return $rows;
    }


}