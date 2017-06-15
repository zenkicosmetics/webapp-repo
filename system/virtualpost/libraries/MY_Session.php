<?php

if (! defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * MY_Email - Allows for email config settings to be stored in the db.
 */
class MY_Session extends CI_Session {
    
    /**
     * Update an existing session
     *
     * @access    public
     * @return    void
     */
    function sess_update()
    {
        // skip the session update if this is an AJAX call!
        if ( !IS_AJAX )
        {
            parent::sess_update();
        }
    }
}
/* End of file MY_Email.php */