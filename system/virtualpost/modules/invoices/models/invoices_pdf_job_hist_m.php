<?php defined('BASEPATH') or exit('No direct script access allowed');

class invoices_pdf_job_hist_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('invoices_pdf_job_hist');
        $this->primary_key = 'invoice_code';
    }

    /**
     * Get all pending invoice_summary object did not generate invoice
     */
    public function getAllPendingInvoice($customer_id = '')
    {
        $this->db->select('invoice_summary.*')->distinct();
        $this->db->from('invoice_summary');
        // $this->db->where('invoice_summary.invoice_code NOT IN (SELECT DISTINCT invoices_pdf_job_hist.invoice_code FROM invoices_pdf_job_hist)');
        $this->db->where('invoice_summary.invoice_file_path IS NULL');
        $this->db->where('invoice_summary.total_invoice > 0', null);
        $this->db->where('(invoice_summary.invoice_type is null OR invoice_summary.invoice_type <> 2)', null);
        $this->db->where("substr( invoice_month, 1, 6 ) < '" . APUtils::getCurrentYear() . APUtils::getCurrentMonth() . "'", null);
        $this->db->order_by('invoice_summary.total_invoice', 'DESC');
        
        // we will get all invoices for 4th of every month.
        if(date('d') != 4){
            $this->db->limit(800);
        }
        if (!empty($customer_id)) {
            $this->db->where('invoice_summary.customer_id', $customer_id);
        }

        return $this->db->get()->result();
    }
}