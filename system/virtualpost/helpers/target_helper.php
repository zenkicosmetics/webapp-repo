<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Load list html target attributes
 */
if (! function_exists('list_target')) {
    /**
     * Load list html target attributes
     */
    function list_target() {
        $targets = array("_blank","_self","_parent","_top","framename");
        $arrs = array();
        foreach($targets as $tar) {
            $arrs[$tar] = $tar;
        }
        return $arrs;
    }
}

