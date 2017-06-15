<?php

/**
 * #926 [IMPROVEMENT] Change character set encoding (collation) in all Tables and Fields from “lantin1_swedish_ci” to “utf8_general_ci
 * https://clevvermail.unfuddle.com/a#/projects/1086/tickets/by_number/926?cycle=true
 */
class DbCollation
{
    const FROM_COLLATION = 'latin1_swedish_ci';
    const TO_COLLATION = 'utf8_general_ci';

    private $tableList;

    public function __construct()
    {
        ci()->load->library('scripts/scripts_api');
        $this->initTables();
    }

    public function changeCharacterSet()
    {
        $sql = "ALTER TABLE %s CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;";
        if ($this->tableList) {
            $length = count($this->tableList);
            for ($i = 0; $i < $length; $i++) {
                $sqlChange = sprintf($sql, $this->tableList[$i]);
                scripts_api::executeQuery($sqlChange);
                echo ($i + 1) . '/ ' . $sqlChange . '<br>';
                scripts_api::outputBuffer();
            }
        }
    }

    private function initTables()
    {
        $sql = "SHOW TABLE STATUS";
        $tables = scripts_api::executeQuery($sql, true);
        if ($tables) {
            $this->tableList = array();
            foreach ($tables as $table) {
                /*
                if ($table->Collation != self::TO_COLLATION) {
                    $this->tableList[] = $table->Name;
                }
                */
                $this->tableList[] = $table->Name;
            }
        }
    }
}