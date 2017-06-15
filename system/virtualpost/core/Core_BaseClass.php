<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Code here is run before frontend controllers
 */
class Core_BaseClass  {
    
    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     */
    public function __construct() {
    }
    
    protected  function get_paging_output($total, $limit, $page)
    {
        $response = new stdClass();
        $response->page = $page;
        if ($total > 0) {
            $total_pages = ceil($total / $limit);
        } else {
            $total_pages = 0;
        }
        $response->total = $total_pages;
        $response->records = $total;

        return $response;
    }
}
