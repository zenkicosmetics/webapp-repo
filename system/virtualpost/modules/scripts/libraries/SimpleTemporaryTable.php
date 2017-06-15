<?php defined('BASEPATH') or exit('No direct script access allowed');

class SimpleTemporaryTable
{
    const TABLE_PREFIX = 'temp_';
    const MAX_RECORDS_BULK_INSERT = 100;
    const MAX_RECORDS_SELECT = 1000;
    const MSG_CREATE_TEMP_TABLE_SUCCESS = "DONE! Now, please check data in the temporary table '%s'.";
    const MSG_DATA_EMPTY = "EMPTY! Your SQL query returns no data.";

    private $tableName;
    private $targetTableName;
    private $columns;
    private $columnNames;
    private $sqlFilter;

    public function __construct(array $params)
    {
        ci()->load->model('scripts/script_m');

        $this->tableName = $params['temporary_table'];
        $this->targetTableName = $params['target_table'];
        $this->sqlFilter = $params['sql_filter'];

        if (ci()->script_m->checkTableExists($this->targetTableName) == false) {
            throw new Exception("The target table '{$this->targetTableName}' does not exist!");
        }
        if (ci()->script_m->checkTableExists($this->tableName) == true) {
            throw new Exception("The temporary table '{$this->tableName}' existed already!");
        }

        $this->initTemporaryTableColumns();
        $this->createTemporaryTable();
        $this->bulkInsertDataToTemporaryTable();
    }

    public function getDataFromTemporaryTable()
    {
        $sql = "SELECT {$this->columnNames} FROM {$this->tableName}";
        $rows = ci()->script_m->executeQuery($sql, true);

        return $rows;
    }

    private function initTemporaryTableColumns()
    {
        $fields = ci()->script_m->getTableFields($this->targetTableName);
        $columns = array();
        $columnNames = array();
        foreach ($fields as $fieldInfo) {
            $fieldName = $fieldInfo->Field;
            $fieldType = strtoupper($fieldInfo->Type);
            $null = ($fieldInfo->Null == 'YES') ? 'NULL' : 'NOT NULL';
            if ($fieldInfo->Default) {
                $defaultValue = ($fieldInfo->Default == 'NULL') ? 'NULL' : "'{$fieldInfo->Default}'";
            } else {
                $defaultValue = '';
            }
            $default = $defaultValue ? "DEFAULT {$defaultValue}" : '';

            $column = "{$fieldName} {$fieldType} {$null} {$default}";
            $columns[] = trim($column);
            $columnNames[] = $fieldName;

        }
        $this->columns = implode(',', $columns);
        $this->columnNames = implode(', ', $columnNames);
        unset($columns);
        unset($columnNames);
    }

    private function createTemporaryTable()
    {
        $sql = array();
        $sql[] = "CREATE TABLE {$this->tableName} (";
        $sql[] = $this->columns . ",read_flag tinyint NOT NULL DEFAULT '0'";
        $sql[] = ") ENGINE=InnoDB DEFAULT CHARSET= utf8;";
        $sql = implode('', $sql);
        ci()->script_m->executeQuery($sql);
    }

    private function bulkInsertDataToTemporaryTable()
    {
        $rows = ci()->script_m->executeQuery($this->sqlFilter, true, true);
        if ($rows) {
            $rowNum = 0;
            $records = array();
            foreach ($rows as $row) {
                ++$rowNum;
                if (++$rowNum <= self::MAX_RECORDS_BULK_INSERT) {
                    $records[] = "('" . implode("','", array_map('addslashes', array_values($row))) . "')";
                } else {
                    $sql = "INSERT INTO {$this->tableName}({$this->columnNames}) VALUES " . implode(',', $records);
                    ci()->script_m->executeQuery($sql);
                    $rowNum = 0;
                    unset($records);
                    $records = array();
                }
            }
            if ($records) {
                $sql = "INSERT INTO {$this->tableName}({$this->columnNames}) VALUES " . implode(',', $records);
                ci()->script_m->executeQuery($sql);
                unset($records);
            }
            unset($rows);
        }
    }
}