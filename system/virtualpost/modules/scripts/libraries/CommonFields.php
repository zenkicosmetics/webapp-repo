<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * #894 [NEW_FEATURE] Add common fields to all tables in DB
 * https://clevvermail.unfuddle.com/a#/projects/1086/tickets/by_number/894
 */
class CommonFields
{
    private $excludedTables;
    private $tableList;
    private $sqlAddFieldStatements;
    private $sqlRenameFieldsStatements;

    public function __construct()
    {
        ci()->load->model('scripts/script_m');

        $this->excludedTables = array('ci_sessions', 'mobile_sessions');

        $this->initTableList();
        $this->initSqlStatements();
    }

    public function addCommonFields()
    {
        $startTime = time();
        foreach ($this->tableList as $tableName) {
            foreach ($this->sqlAddFieldStatements as $fieldName => $sqlStatement) {
                if (!in_array($tableName, $this->excludedTables) && !$this->isDuplicatedField($tableName, $fieldName)) {
                    $sql = sprintf($sqlStatement, $tableName);
                    ci()->script_m->executeQuery($sql);
                }
            }
        }
        $endTime = time();
        $timeDiffInMinutes = round(($endTime - $startTime) / 60);
        echo "IT TAKES TIME: $timeDiffInMinutes (minutes)";
    }

    public function renameCommonFields()
    {
        $startTime = time();
        foreach ($this->tableList as $tableName) {
            foreach ($this->sqlRenameFieldsStatements as $fieldName => $sqlStatement) {
                if (!in_array($tableName, $this->excludedTables) && $this->isFieldSameType($tableName, $fieldName)) {
                    $sql = sprintf($sqlStatement, $tableName);
                    ci()->script_m->executeQuery($sql);
                }
            }
        }
        $endTime = time();
        $timeDiffInMinutes = round(($endTime - $startTime) / 60);
        echo "IT TAKES TIME: $timeDiffInMinutes (minutes)";
        $this->isFieldSameType('shipping_carriers', 'created_by_type');
    }

    private function initTableList()
    {
        $this->tableList = array();
        $sql = "SHOW TABLES";
        $rows = ci()->script_m->executeQuery($sql, true);
        foreach ($rows as $row) {
            $row = (array)$row;
            foreach ($row as $tableName) {
                if (substr($tableName, 0, 5) != 'temp_' && stripos($tableName, 'log') === false) {
                    $this->tableList[] = $tableName;
                }
            }
        }
    }

    private function initSqlStatements()
    {
        $this->sqlAddFieldStatements = array(
            'created_date' => 'ALTER TABLE %s ADD created_date DATETIME NULL DEFAULT NULL',
            'created_by_type' => 'ALTER TABLE %s ADD created_by_type TINYINT NULL DEFAULT NULL', // 0: admin, 1: customer, 2: auto by cronjob, 3: web service (API)
            'created_by_id' => 'ALTER TABLE %s ADD created_by_id BIGINT NULL DEFAULT NULL',
            'last_modified_date' => 'ALTER TABLE %s ADD last_modified_date DATETIME NULL DEFAULT NULL',
            'last_modified_by_type' => 'ALTER TABLE %s ADD last_modified_by_type TINYINT NULL DEFAULT NULL', // 0: admin, 1: customer, 2: auto by cronjob, 3: web service (API)
            'last_modified_by_id' => 'ALTER TABLE %s ADD last_modified_by_id BIGINT NULL DEFAULT NULL',
            'deleted_flag' => 'ALTER TABLE %s ADD deleted_flag TINYINT DEFAULT 0' // 1: deleted, 0: not deleted
        );
        $this->sqlRenameFieldsStatements = array(
            'created_by_type' => 'ALTER TABLE %s CHANGE COLUMN created_by_type created_by_type TINYINT NULL DEFAULT NULL',
            'created_by_id' => 'ALTER TABLE %s CHANGE COLUMN created_by_id created_by_id BIGINT NULL DEFAULT NULL',
            'last_modified_date' => 'ALTER TABLE %s CHANGE COLUMN last_modified_date last_modified_date DATETIME NULL DEFAULT NULL',
            'last_modified_by_type' => 'ALTER TABLE %s CHANGE COLUMN last_modified_by_type last_modified_by_type TINYINT NULL DEFAULT NULL',
            'last_modified_by_id' => 'ALTER TABLE %s CHANGE COLUMN last_modified_by_id last_modified_by_id BIGINT NULL DEFAULT NULL',
        );
    }

    private function isDuplicatedField($tableName, $fieldName)
    {
        $sql = "SHOW COLUMNS FROM $tableName WHERE Field = '{$fieldName}'";
        $result = ci()->script_m->executeQuery($sql, true);

        return ($result) ? true : false;
    }

    private function isFieldSameType($tableName, $fieldName)
    {
        $fieldTypes = array(
            'created_by_type' => 'TINYINT',
            'created_by_id' => 'BIGINT',
            'last_modified_date' => 'DATETIME',
            'last_modified_by_type' => 'TINYINT',
            'last_modified_by_id' => 'BIGINT',
        );
        if (isset($fieldTypes[$fieldName])) {
            $fieldType = strtolower($fieldTypes[$fieldName]);
            $sql = "SHOW COLUMNS FROM $tableName WHERE Field = '{$fieldName}' AND Type LIKE '{$fieldType}%'";
            $result = ci()->script_m->executeQuery($sql, true);

            return ($result) ? true : false;
        }

        return false;
    }
}