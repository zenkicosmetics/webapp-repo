<?php defined('BASEPATH') or exit('No direct script access allowed');

class Invoice extends Admin_Controller
{
    public function __construct()
    {
        ini_set('max_execution_time', 86400);
        error_reporting(E_ALL);
        ini_set('display_errors', '1');

        parent::__construct();

        $this->load->model('script_m');
        $this->load->library('SQLConfigs');
        $this->load->helper('functions');
        
		$this->load->model(array(
            "report/report_m",
            "customers/customer_m",
            "payment/payone_tran_hist_m",
            "payment/external_tran_hist_m"
        ));
        
		$this->load->library('scans/scans_api');
	
    }
    
    public function index(){
        
    }

    public function convert_deactivated_days(){
        // 59 day ago: 20161007 -> 20160810 / 1470784907
        // gets all deactivated customers.
        $customers = $this->customer_m->get_many_by_many(array(
            "status" => APConstants::OFF_FLAG,
            "deactivated_type" => "auto",
            "activated_flag" => APConstants::OFF_FLAG
        ));

        foreach($customers as $customer){
            // get last day of payment
            $payone = $this->payone_tran_hist_m->order_by("id", "desc")->get_by_many(array(
                'customer_id' => $customer->customer_id,
                "(txaction = 'paid')" => null
            ));
            
            $payment= $this->external_tran_hist_m->order_by("id", "desc")->get_by_many(array(
                'customer_id' => $customer->customer_id,
                "status" => "OK",
            ));
            
            $deactivated_date = date("Ymd", $customer->deactivated_date);
            if((empty($payone) && empty($payment)) || ($deactivated_date < "20160810") ){
                echo "customer_id: ". $customer->customer_id.", current date: ".$deactivated_date."<br/>";
                $this->customer_m->update_by_many(array(
                    "customer_id" => $customer->customer_id
                ), array(
                    "deactivated_date" => 1470784907
                ));
                continue;
            }
            
            $last_updated_date = 0;
            if($payone){
                $last_updated_date = $payone->last_update_date;
            }
            if($payment){
                if($payment->created_date > $last_updated_date){
                    $last_updated_date = $payment->created_date;
                }
            }
            
            $tmp_date = date("Ymd", $last_updated_date);
            if($last_updated_date > 0 && $tmp_date < '20160810' ){
                echo "customer_id: ". $customer->customer_id.", current date: ".$deactivated_date."<br/>";
                $this->customer_m->update_by_many(array(
                    "customer_id" => $customer->customer_id
                ), array(
                    "deactivated_date" => 1470784907
                ));
                continue;
            }
        }
        
        echo "done";
        exit();
    }
    
    
    public function get_customer_VAT_change(){
        $stmt = "SELECT customers . *
            FROM `customers`
            JOIN customers_address ON customers_address.customer_id = customers.customer_id
            WHERE customers.vat_number IS NOT NULL
            AND STATUS =1
            AND customers_address.vat_number IS NULL ";
        
        
        $stmt2 = "select * from customers where customer_id in (1,593,33394,33694,33986,34268,34281,34375,34428,34851,35025,35229,35335,36661,36783,37106,37142,37556,37960,38030,38343,38417,38419,38444,38768,39008,39710,39796,39862,39925,39931,40008,40052,40131,40354,40687,40706,41160,41195,41858,42488,42571,45586)";
        
        $result = $this->customer_m->db->query($stmt2)->result();
        
        foreach($result as $customer){
            $vat = APUtils::getVatRateOfCustomer($customer->customer_id);
            if($vat->rate == 0){
                echo "customer-id: ".$customer->customer_id.", VAT: ".json_encode($vat)."<br/>";
            }
        }
        
        echo "done";
    }
    
}