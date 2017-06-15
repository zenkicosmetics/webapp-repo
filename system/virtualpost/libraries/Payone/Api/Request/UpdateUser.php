<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GNU General Public License (GPL 3)
 * that is bundled with this package in the file LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Payone to newer
 * versions in the future. If you wish to customize Payone for your
 * needs please refer to http://www.payone.de for more information.
 *
 * @category        Payone
 * @package         Payone_Api
 * @subpackage      Request
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @author          Matthias Walter <info@noovias.com>
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.noovias.com
 */

/**
 *
 * @category        Payone
 * @package         Payone_Api
 * @subpackage      Request
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.noovias.com
 */
class Payone_Api_Request_UpdateUser extends Payone_Api_Request_Abstract
{
    protected $request = Payone_Api_Enum_RequestType::UPDATEUSER;

    /**
     * @var string
     */
    protected $userid = null;
    
    /**
     * @var string
     */
    protected $email = null;
    
    protected $customerid = null;
    
    protected $lastname = null;
    
    protected $company = null;
    
    protected $country = null;
    
    
    

    function getEmail() {
        return $this->email;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function getUserid() {
        return $this->userid;
    }

    function setUserid($userid) {
        $this->userid = $userid;
    }
    
    function getCustomerid() {
        return $this->customerid;
    }

    function setCustomerid($customerid) {
        $this->customerid = $customerid;
    }
    
    function getLastname() {
        return $this->lastname;
    }

    function setLastname($lastname) {
        $this->lastname = $lastname;
    }
    
    function getCompany() {
        return $this->company;
    }

    function setCompany($company) {
        $this->company = $company;
    }
    
    function getCountry() {
        return $this->country;
    }

    function setCountry($country) {
        $this->country = $country;
    }
}
