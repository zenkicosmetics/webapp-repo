<?php defined('BASEPATH') or exit('No direct script access allowed');

class scans_api
{
    const SCAN_TYPE_ENVELOPE = 1;
    const SCAN_TYPE_DOCUMENT = 2;
    
    public function __construct() {
        
    }

   /**
     * Get all envelope has trash_flag = 1 or in trash folder over 30 days
     */
    public static function getAllEnvelopesNeedToTrashed()
    {
        ci()->load->model('scans/envelope_m');

        // Get all postbox need delete today (yyyyMMdd)
        $before31days = now() - 31 * 24 * 60 * 60;
        $deletedEnvelopes = ci()->envelope_m->get_many_by_many(
            array(
                "trash_flag IN (1, 0 , 6)" => null,
                "trash_date <= " => $before31days
            )
        );

        return $deletedEnvelopes;
    }

    public static function checkShppingAdress($customerID, $shipping_address_id){

        ci()->load->model('scans/envelope_m');
        /*
        $check = ci()->envelope_m->get_many_by_many(array(
            'to_customer_id' => $customerID,
            'shipping_address_id' => $shipping_address_id
        ),'from_customer_name');
        echo "<pre>";print_r($check);exit;
        */
        $allowDelete = ci()->envelope_m->count_by_many(array(
            'to_customer_id' => $customerID,
            'shipping_address_id' => $shipping_address_id
        ));
      
        if ($allowDelete) {
            return false;
        }
        return true;

    }

    public static function deleteEnvelope($customerID, $envelopeID)
    {
        ci()->load->model('scans/envelope_m');
        ci()->load->model('scans/envelope_file_m');

        // Updating the delete status of envelope
        ci()->envelope_m->delete_by_many(
            array(
                "id" => $envelopeID,
                "to_customer_id" => $customerID
            )
        );

        // Physical delete scan file stored on local Server
        $scanFiles = ci()->envelope_file_m->get_many_by_many(
            array(
                "envelope_id" => $envelopeID,
                "customer_id" => $customerID
            )
        );
        if ($scanFiles) {
            foreach ($scanFiles as $scanFile) {
                $localFile = $scanFile->local_file_name;
                // $default_bucket_name = $this->config->item('default_bucket');
                // $result = S3::deleteObject($default_bucket_name,$scanFile->file_name);
                if (file_exists($localFile)) unlink($localFile);
            }
        }

        // You can freely delete the record now
        ci()->envelope_file_m->delete_by_many(
            array(
                "envelope_id" => $envelopeID,
                "customer_id" => $customerID
            )
        );
    }

    public static function saveShippingAddress($shipping_address_id = 0, $envelope_id, $customer_id, $included_all_flag = 0, $green_flag= 0)
    {
        ci()->load->model('scans/envelope_m');
        ci()->load->model('scans/envelope_shipping_request_m');
        ci()->load->library('shipping/shipping_api');
        
        $envelope = ci()->envelope_m->get($envelope_id);
        
        // fix collect shippment item with included all items.
        if($included_all_flag == APConstants::ON_FLAG){
            $allGreenEnvelopes = ci()->envelope_m->get_many_by_many(array(
                "postbox_id" => $envelope->postbox_id,
                "to_customer_id" => $customer_id,
                "( (storage_flag =1 AND current_storage_charge_fee_day > 0  AND collect_shipping_flag <> '1'  
                        AND collect_shipping_flag <> '2' AND direct_shipping_flag <> '1' AND direct_shipping_flag <> '2') 
                    OR (direct_shipping_flag IS NULL AND collect_shipping_flag IS NULL))" => null,
                "trash_flag IS NULL" => null
            ));
            $list_envelope_id = array();
            foreach($allGreenEnvelopes as $envelope){
                $list_envelope_id[] = $envelope->id;
            }
            if(!empty($list_envelope_id)){
                $list_envelopes_str = implode(',', $list_envelope_id);
                ci()->envelope_m->update_by_many( array(
                    "id in (".implode(',', $list_envelope_id).")" => null,
                    'to_customer_id' => $customer_id
                ), array(
                    'shipping_address_id' => $shipping_address_id,
                    'shipping_address_date'=>now()
                ));
                
                shipping_api::saveShippingAddress($list_envelopes_str, $shipping_address_id);
            }
        }
        // fix collect shippment item
        else if($green_flag == APConstants::ON_FLAG){
            $allGreenEnvelopes = ci()->envelope_m->getAllReadyCollectItems($customer_id,$envelope->postbox_id);

            $list_envelope_id = array();
            foreach($allGreenEnvelopes as $envelope){
                $list_envelope_id[] = $envelope->id;
            }
            if(!empty($list_envelope_id)){
                $list_envelopes_str = implode(',', $list_envelope_id);
                ci()->envelope_m->update_by_many( array(
                    "id in (".$list_envelopes_str.")" => null,
                    'to_customer_id' => $customer_id
                ), array(
                    'shipping_address_id' => $shipping_address_id,
                    'shipping_address_date'=>now()
                ));
                
                shipping_api::saveShippingAddress($list_envelopes_str, $shipping_address_id);
            }
        }
        else{
            ci()->envelope_m->update_by_many( array(
                'id' => $envelope_id,
                'to_customer_id' => $customer_id
            ), array(
                'shipping_address_id' => $shipping_address_id,
                'shipping_address_date'=>now()
            ));
            
            shipping_api::saveShippingAddress($envelope_id, $shipping_address_id);
        }
        
        return true;
    }

    public static function getNumberEnvelopeScansOfCurrentMonth($customerID, $postboxID, $envelopeID='')
    {
        ci()->load->model('scans/envelope_summary_month_m');

        $currentEnvelopeScanNumber = ci()->envelope_summary_month_m->count_by_many(array(
            "customer_id" => $customerID,
            "postbox_id" => $postboxID,
            "year" => date('Y'),
            "month" => date('m'),
            "envelope_scan_number" => 1,
            "envelope_id <>'".$envelopeID."'" => null
        ));

        return ($currentEnvelopeScanNumber) ? $currentEnvelopeScanNumber : 0;
    }

    /**
     * @Des: Mark collect shipment request for all storage items
     */
    public static function makeCollectShipment($postbox_id, $customer_id='')
    {
        ci()->load->model('scans/envelope_m');
        ci()->load->model('mailbox/envelope_customs_m');
        ci()->load->library('shipping/shipping_api');
        
        $to_customer_id = $customer_id ? $customer_id : APContext::getCustomerCodeLoggedIn();
        
        // Gets all items in customs table with process = 0 (does not declare custom yet)
        $envelope_customs = ci()->envelope_customs_m->get_many_by_many(array(
            "customer_id" => $to_customer_id,
            "postbox_id" => $postbox_id,
            "process_flag" => APConstants::OFF_FLAG
        ));
        
        $list_envelope_id = array();
        $list_envelope_id[] = 0;
        foreach($envelope_customs as $e){
            $list_envelope_id[] = $e->envelope_id;
        }
        
        // Get all item for this collect, exclude item does not declare custom yet
        $list_envelopes = ci()->envelope_m->get_many_by_many( array(
            "postbox_id" => $postbox_id,
            "to_customer_id" => $to_customer_id,
            "( (storage_flag =1 AND current_storage_charge_fee_day > 0  AND collect_shipping_flag <> '1'  
                    AND collect_shipping_flag <> '2' AND direct_shipping_flag <> '1' AND direct_shipping_flag <> '2') 
                OR (direct_shipping_flag IS NULL AND collect_shipping_flag IS NULL))" => null,
            "trash_flag IS NULL" => null,
            "id NOT IN (".implode(',', $list_envelope_id).")" => null
        ));
        
        // Validate over 68 KG for Fedex Collect Shipment
        foreach ($list_envelopes as $envelope) {
            $validWeight = shipping_api::checkValidCollectItem($envelope->id);
            if (!$validWeight) {
                $list_envelope_id[] = $envelope->id;
            }
        }

        //Update collect shipping flag
        ci()->envelope_m->update_by_many( array(
            "postbox_id" => $postbox_id,
            "to_customer_id" => $to_customer_id,
            "( (storage_flag =1 AND current_storage_charge_fee_day > 0  AND collect_shipping_flag <> '1'  
                    AND collect_shipping_flag <> '2' AND direct_shipping_flag <> '1' AND direct_shipping_flag <> '2') 
                OR (direct_shipping_flag IS NULL AND collect_shipping_flag IS NULL))" => null,
            "trash_flag IS NULL" => null,
            "id NOT IN (".implode(',', $list_envelope_id).")" => null
        ),
        array(
            'collect_shipping_flag' => APConstants::OFF_FLAG,
            'direct_shipping_flag' => null,
            'direct_shipping_date' => null,
            'last_updated_date' => now(),
            'collect_shipping_date' => now()
        ));
    }

    /**
     * Get all items are make as collect shipping
     * @param type $customerID
     * @param type $postbox_id
     * @return type
     */
    public static function getListCollectiveShippingItems($customerID, $postbox_id)
    {
        ci()->load->model('scans/envelope_m');
        $list_collect_envelope = ci()->envelope_m->get_postbox_collect_by($customerID, $postbox_id);
        return $list_collect_envelope;
    }

    /**
     * Get all customer and postbox having request collect
     */
    public static function getAllPostboxesRequestForCollectiveShipping()
    {
        ci()->load->model('scans/envelope_m');

        $listCollectiveShippingRequests = ci()->envelope_m->get_postbox_collect();

        return $listCollectiveShippingRequests;
    }

    public static function getAllItemsForCollectiveShippingRequest($customerID, $locationID)
    {
        ci()->load->model('scans/envelope_m');

        $collectiveShippingItems = ci()->envelope_m->get_many_by_many(
            array(
                "to_customer_id" => $customerID,
                'location_id' => $locationID,
                "collect_shipping_flag" => '0'
            )
        );

        return $collectiveShippingItems;
    }

    /**
     * Get marked collect shipping item that already have package_id
     * @param type $customerID
     * @param type $locationID
     * @param type $postboxID
     * @return type
     */
    public static function getItemForCollectiveShippingRequestWithPackageID($customerID, $locationID, $postboxID)
    {
        ci()->load->model('scans/envelope_m');

        $pending_collect_shipping_request = ci()->envelope_m->get_by_many(
            array(
                "to_customer_id" => $customerID,
                'location_id' => $locationID,
                'postbox_id' => $postboxID,
                "(collect_shipping_flag = '0')" => null,
                "package_id > " => 0
            )
        );

        return $pending_collect_shipping_request;
    }

    /**
     * @Des  Package this postbox (insert new record in postbox
     * @param $customerID
     * @param $locationID
     * @return $package_id
     */
    public static function createCollectiveShippingPackage($customerID, $locationID)
    {
        ci()->load->model('scans/envelope_package_m');

        $package_id = ci()->envelope_package_m->insert(
            array(
                "location_available_id" => $locationID,
                "customer_id" => $customerID,
                "package_date" => APUtils::getCurrentYearMonthDate()
            ));

        return $package_id;
    }

    /**
     * @Des: Update package_id for all envelopes in request of collective shipping
     * @param $customerID
     * @param $locationID
     * @param $packageID
     */
    public static function updatePackageIDForAllCollectiveShippingItems($customerID, $locationID, $packageID, $postboxID)
    {
        ci()->load->model('scans/envelope_m');
        // Apply customs procedure
        $declare_customs_flag = EnvelopeUtils::apply_collect_customs_process($customerID, $postboxID, $packageID);
        if ($declare_customs_flag == APConstants::ON_FLAG) {
            return array(
                'declare_customs_flag' => $declare_customs_flag
            );
        }
        ci()->envelope_m->update_by_many(
            array(
                "to_customer_id" => $customerID,
                'location_id' => $locationID,
                'postbox_id' => $postboxID,
                "collect_shipping_flag" => APConstants::OFF_FLAG,
                "(package_id IS NULL OR package_id = 0)" => null
            ), array(
            "package_id" => $packageID
        ));
        return array(
            'declare_customs_flag' => $declare_customs_flag
        );
        
    }

    /**
     * Get all collective shipping items in request status with Package ID
     * @param $customerID
     * @param $locationID
     * @param $package_id
     * @return mixed
     */
    public static function getAllItemsForCollectiveShippingRequestWithPackageID($customerID, $locationID, $package_id,$postboxID)
    {
        ci()->load->model('scans/envelope_m');

        $list_envelope_update = ci()->envelope_m->get_many_by_many(array(
            "to_customer_id" => $customerID,
            "collect_shipping_flag" => '0',
            'location_id' => $locationID,
            "package_id" => $package_id,
            "postbox_id" => $postboxID
        ));

        return $list_envelope_update;
    }

    public static function getNumberDocumentScansOfCurrentMonth($customerID, $postboxID, $envelopeID='')
    {
        ci()->load->model('scans/envelope_summary_month_m');

        $currentDocumentScanNumber = ci()->envelope_summary_month_m->count_by_many(array(
            "customer_id" => $customerID,
            "postbox_id" => $postboxID,
            "year" => date('Y'),
            "month" => date('m'),
            "document_scan_number" => 1,
            "envelope_id <> '".$envelopeID."'" => null
        ));

        return ($currentDocumentScanNumber) ? $currentDocumentScanNumber : 0;
    }

    public static function getTotalPagesScannedOfCurrentMonth($customerID, $scanType = 0)
    {
        ci()->load->model('scan/envelope_file_m');

        $totalPagesScanned = ci()->envelope_file_m->getTotalPagesScannedOfCurrentMonth($customerID, $scanType);

        return ($totalPagesScanned) ? $totalPagesScanned : 0;
    }

    public static function getNumberPagesOfDocumentScan($customerID, $envelopeID)
    {
        ci()->load->model('scan/envelope_file_m');

        $numberPages = 0;
        $documentScan = ci()->envelope_file_m->get_by_many(array(
            "envelope_id" => $envelopeID,
            "customer_id" => $customerID,
            "type" => self::SCAN_TYPE_DOCUMENT
        ));
        if ($documentScan) {
            $numberPages = $documentScan->number_page;
        }

        return $numberPages;
    }

    public static function getEnvelopeSummaryOfCurrentMonth($customerID, $postboxID, $envelopeID)
    {
        ci()->load->model('scans/envelope_summary_month_m');

        $currentEnvelopeSummary = ci()->envelope_summary_month_m->get_by_many(array(
            "envelope_id" => $envelopeID,
            "customer_id" => $customerID,
            "postbox_id" => $postboxID,
            "year" => date('Y'),
            "month" => date('m')
        ));

        return $currentEnvelopeSummary;
    }

    public static function createEnvelopeSummaryOfCurrentMonth($customerID, $postboxID, $envelopeID, $envelopeScanNumber, $scanEnvelopePrice)
    {
        ci()->load->model('scans/envelope_summary_month_m');

        $id = ci()->envelope_summary_month_m->insert(array(
            "envelope_id" => $envelopeID,
            "customer_id" => $customerID,
            "postbox_id" => $postboxID,
            "year" => date('Y'),
            "month" => date('m'),
            "envelope_scan_number" => $envelopeScanNumber,
            "envelope_scan_price" => $scanEnvelopePrice
        ));

        return $id;
    }

    public static function updateEnvelopeSummaryOfCurrentMonth($customerID, $postboxID, $envelopeID, $envelopeScanNumberFlag, $scanEnvelopePrice)
    {
        ci()->load->model('scans/envelope_summary_month_m');

        ci()->envelope_summary_month_m->update_by_many(array(
            "customer_id" => $customerID,
            "postbox_id" => $postboxID,
            "year" => date('Y'),
            "month" => date('m'),
            "envelope_id" => $envelopeID,
        ), array(
            "envelope_scan_number" => $envelopeScanNumberFlag,
            "envelope_scan_price" => $scanEnvelopePrice
        ));

        return true;
    }
    
    public static function updateEnvelopeScanNumber($customer_id, $postbox_id, $envelope_id, $price, $envelope_scan_status = 0){
        ci()->load->model('scans/envelope_summary_month_m');
        
        $envelope = ci()->envelope_summary_month_m->get_by_many(array(
            "customer_id" => $customer_id,
            "postbox_id" => $postbox_id,
            "year" => date('Y'),
            "month" => date('m'),
            "envelope_id" => $envelope_id,
        ));
        
        if($envelope){
            ci()->envelope_summary_month_m->db->query("UPDATE envelope_summary_month "
                    . " SET envelope_scan_number = '".$envelope_scan_status."', envelope_scan_price='".$price."' "
                    . " WHERE envelope_id = '".$envelope_id."' AND postbox_id='".$postbox_id."' "
                    . " AND customer_id='".$customer_id."' AND year='".date("Y")."' AND month='".date('m')."'");
        }else{
            ci()->envelope_summary_month_m->insert(array(
                "envelope_id" => $envelope_id,
                "customer_id" => $customer_id,
                "postbox_id" => $postbox_id,
                "year" => date('Y'),
                "month" => date('m'),
                "envelope_scan_number" => $envelope_scan_status,
                "envelope_scan_price" => $price
            ));
        }
    }
    
    public static function updateItemScanNumber($customer_id, $postbox_id, $envelope_id, $price, $item_scan_status = 0){
        ci()->load->model('scans/envelope_summary_month_m');
        
        $envelope = ci()->envelope_summary_month_m->get_by_many(array(
            "customer_id" => $customer_id,
            "postbox_id" => $postbox_id,
            "year" => date('Y'),
            "month" => date('m'),
            "envelope_id" => $envelope_id,
        ));
        
        if($envelope){
            ci()->envelope_summary_month_m->db->query("UPDATE envelope_summary_month "
                    . " SET document_scan_number = '".$item_scan_status."', document_scan_price='".$price."' "
                    . " WHERE envelope_id = '".$envelope_id."' AND postbox_id='".$postbox_id."' "
                    . " AND customer_id='".$customer_id."' AND year='".date("Y")."' AND month='".date('m')."'");
        }else{
            ci()->envelope_summary_month_m->insert(array(
                "envelope_id" => $envelope_id,
                "customer_id" => $customer_id,
                "postbox_id" => $postbox_id,
                "year" => date('Y'),
                "month" => date('m'),
                "document_scan_number" => $item_scan_status,
                "document_scan_price" => $price
            ));
        }
    }
            

    public static function createDocumentSummaryOfCurrentMonth($customerID, $postboxID, $envelopeID, $documentScanNumberFlag, $documentScanPrice, $additionalPagesScanningNumber, $additionalPagesScanningPrice, $totalPagesScanningNumber)
    {
        ci()->load->model('scans/envelope_summary_month_m');

        $id = ci()->envelope_summary_month_m->insert(array(
            "envelope_id" => $envelopeID,
            "customer_id" => $customerID,
            "postbox_id" => $postboxID,
            "year" => date('Y'),
            "month" => date('m'),
            "document_scan_number" => $documentScanNumberFlag,
            "document_scan_price" => $documentScanPrice,
            "additional_pages_scanning_number" => $additionalPagesScanningNumber,
            "additional_pages_scanning_price" => $additionalPagesScanningPrice,
            "total_pages_scanning_number" => $totalPagesScanningNumber
        ));

        return $id;
    }

    public static function updateDocumentSummaryOfCurrentMonth($customerID, $postboxID, $envelopeID, $documentScanNumberFlag, $documentScanPrice, $additionalPagesScanningNumber, $additionalPagesScanningPrice, $totalPagesScanningNumber)
    {
        ci()->load->model('scans/envelope_summary_month_m');

        ci()->envelope_summary_month_m->update_by_many(array(
            "envelope_id" => $envelopeID,
            "customer_id" => $customerID,
            "postbox_id" => $postboxID,
            "year" => date('Y'),
            "month" => date('m')
        ), array(
            "document_scan_number" => $documentScanNumberFlag,
            "document_scan_price" => $documentScanPrice,
            "additional_pages_scanning_number" => $additionalPagesScanningNumber,
            "additional_pages_scanning_price" => $additionalPagesScanningPrice,
            "total_pages_scanning_number" => $totalPagesScanningNumber
        ));

        return true;
    }

    /**
     * Gets envelopes paging in todolist.
     *
     * @param unknown $arrayCondition
     * @param unknown $start
     * @param unknown $limit
     * @param unknown $sortCol
     * @param unknown $sortType
     * @return unknown
     */
    public static function getEnvelopePagingInTodoList($arrayCondition, $start, $limit, $sortCol, $sortType)
    {
        ci()->load->model('scans/envelope_m');

        $result = ci()->envelope_m->get_envelope_paging_todolist($arrayCondition, $start, $limit, $sortCol, $sortType);

        return $result;
    }

    /**
     * Get envelopes for paging in Prepare Shipping popup
     *
     * @param array $arrayCondition
     * @param integer $start
     * @param integer $limit
     * @param string $sortColumn
     * @param string $sortType
     *
     * @return The list of row objects
     */
    public static function getEnvelopePagingInPrepareShippingPopup(array $arrayCondition, $start, $limit, $sortColumn, $sortType)
    {
        ci()->load->model('scans/envelope_m');

        $results = ci()->envelope_m->get_envelope_paging($arrayCondition, $start, $limit, $sortColumn, $sortType);

        return $results;
    }

    /**
     * Update an envelope by its ID (auto-increment value)
     *
     * @param integer $envelopeID
     * @param array $values The array of key-value pairs with one key corresponds to field name
     * @return  boolean true
     */
    public static function updateEnvelopeByID($envelopeID, array $values)
    {
        ci()->load->model('scans/envelope_m');

        ci()->envelope_m->update($envelopeID, $values);

        return true;
    }

    /**
     * Update the status of prepare shipping for envelopes/items on the Prepare Shipping popup
     *
     * @param array $markedEnvelopeIDs The ids of envelopes marked for prepare-shipping
     * @param array $unmarkedEnvelopeIDs The ids of envelopes unmarked for prepare-shipping
     * @return boolean true
     */
    public static function markEnvelopesForPrepareShipping(array $markedEnvelopeIDs, array $unmarkedEnvelopeIDs)
    {
        ci()->load->model('scans/envelope_m');

        if ($markedEnvelopeIDs) {
            ci()->envelope_m->update_many($markedEnvelopeIDs, array('prepare_shipping_flag' => APConstants::ON_FLAG));
        }
        if ($unmarkedEnvelopeIDs) {
            ci()->envelope_m->update_many($unmarkedEnvelopeIDs, array('prepare_shipping_flag' => APConstants::OFF_FLAG));
        }

        return true;
    }

    /**
     * Count envelopes by month.
     * @param unknown $yearMonth
     * @param unknown $locationId
     */
    public static function countEnvelopesByMonth($yearMonth, $locationId, $charge_flag=true)
    {
        ci()->load->model('scans/envelope_m');
        ci()->load->model('scans/envelope_storage_month_m');
        ci()->load->model('invoices/invoice_summary_by_location_m');

        // count of active customers
        $result = ci()->envelope_m->countEnvelopesByMonth($yearMonth, $locationId, $charge_flag);
        $data = array(
            "received_num" => 0,
            "envelope_scanned_num" => 0,
            "item_scanned_num" => 0,
            "forwarded_num" => 0,
            "storage_num" => 0,
        );
        foreach ($result as $r) {
            if ($r->kind == 'received_number') {
                $data['received_num'] = (int)$r->total;
            } else if ($r->kind == 'envelope_scanned_number') {
                $data['envelope_scanned_num'] = (int)$r->total;
            } else if ($r->kind == 'item_scanned_number') {
                $data['item_scanned_num'] = (int)$r->total;
            } else if ($r->kind == 'forwarded_number') {
                $data['forwarded_num'] = (int)$r->total;
            } else if ($r->kind == 'storage_number') {
                $data['storage_num'] = (int)$r->total;
            }
        }

        // count of deleted customers.
        $result2 = ci()->envelope_m->countEnvelopesOfDeletedCustomer($yearMonth, $locationId, $charge_flag);
        foreach ($result2 as $r) {
            if ($r->kind == 'received_number') {
                $data['received_num'] += 0;
            } else if ($r->kind == 'envelope_scanned_number') {
                $data['envelope_scanned_num'] += (int)$r->total;
            } else if ($r->kind == 'item_scanned_number') {
                $data['item_scanned_num'] += (int)$r->total;
            } else if ($r->kind == 'forwarded_number') {
                $data['forwarded_num'] += (int)$r->total;
            }
        }
        
        // storage item.
        $data['storage_num'] = ci()->envelope_storage_month_m->count_storage_items_by(substr($yearMonth, 0, 4), substr($yearMonth, 4, 2), $locationId, $charge_flag);

        return $data;
    }

    public static function getEnvelopePDF($fullTextSearchValue, $fullTextSearchValue, $start, $limit)
    {
        ci()->load->model('scans/envelope_pdf_content_m');

        $list_envelopes = ci()->envelope_pdf_content_m->get_many_by_many(
            array(
                "pdf_content LIKE '%" . $fullTextSearchValue . "%' OR envelope_content LIKE '%" . $fullTextSearchValue . "%'" => null
            ), $start, $limit);

        return $list_envelopes;
    }

    public static function getEnvelopePagingMailbox($array_where, $start, $limit)
    {
        ci()->load->model('scans/envelope_m');

        $output = ci()->envelope_m->get_envelope_paging_mailbox($array_where, $start, $limit, 'incomming_date', 'DESC');

        return $output;
    }

    public static function getListCollectEnvelope($customer_id)
    {
        ci()->load->model('scans/envelope_m');

        $list_collect_envelope = ci()->envelope_m->get_postbox_collect_bycustomer($customer_id);

        return $list_collect_envelope;
    }

    /**
     * summary forwading envelope by location.
     * @param unknown $locationId
     * @param unknown $reportMonth
     * @return unknown
     */
    public static function summaryForwardingEnvelopesByLocation($locationId, $reportMonth, $share_rev_flag = false)
    {
        ci()->load->model('scans/envelope_shipping_m');

        $forwardingCharges = ci()->envelope_shipping_m->summary_by_location($locationId, $reportMonth, $share_rev_flag);

        return $forwardingCharges;
    }

    /**
     * count number item of customer list.
     * @param unknown $customerId
     */
    public static function getNumberItemsByCustomerList($listCustomerId)
    {
        ci()->load->model('scans/envelope_m');
        $numbers = ci()->envelope_m->get_number_item_of_customer($listCustomerId);
        $result = array();
        foreach ($listCustomerId as $id) {
            $result[$id] = 0;
            if (APContext::isPrimaryCustomerByID($id)) {
                $result[$id] = self::getNumberItemsByEnterpriseCustomerId($id);
            }
            else {
                foreach ($numbers as $row) {
                    if ($row->customer_id == $id) {
                        $result[$id] = $row->number_item;
                        break;
                    }
                }
            }
        }

        return $result;
    }
    
     /**
     * count number item of customer list.
     * @param unknown $parent_customer_id
     */
    public static function getNumberItemsByEnterpriseCustomerId($parent_customer_id)
    {
        // Get list of customer
        $list_customer_id = CustomerUtils::getListCustomerIdOfEnterpriseCustomer($parent_customer_id);
        $list_customer_id[] = $parent_customer_id;
        ci()->load->model('scans/envelope_m');
        $numbers = ci()->envelope_m->get_number_item_of_customer($list_customer_id);
        $total = 0;
        foreach ($numbers as $row) {
            $total += $row->number_item;
        }
        return $total;
    }

    /**
     * Register message to send push.
     *
     * @param unknown_type $customer_id
     * @param unknown_type $postbox_id
     * @param unknown_type $envelope_id
     * @param unknown_type $message
     * @param unknown_type $notify_type
     */
    public static function registerPushMessage($customer_id, $postbox_id, $envelope_id, $message, $notify_type)
    {
        ci()->load->model('api/customer_push_token_m');
        ci()->load->model('api/push_message_notification_m');

        // Get push_id by customer and platform
        $list_android_push_id = ci()->customer_push_token_m->get_many_by_many(array(
            'customer_id' => $customer_id,
            'active_flag' => APConstants::ON_FLAG
        ));
        foreach ($list_android_push_id as $push) {
            if (!empty($push->push_id)) {
                ci()->push_message_notification_m->insert(array(
                    'customer_id' => $customer_id,
                    'postbox_id' => $postbox_id,
                    'envelope_id' => $envelope_id,
                    'message' => $message,
                    'notify_type' => $notify_type,
                    'platform' => $push->platform,
                    'push_id' => $push->push_id,
                    'sent_flag' => APConstants::OFF_FLAG,
                    'created_date' => now()
                ));
            }
        }
    }

    /**
     * Insert data to completed table
     *
     * @param unknown_type $envelope_id
     * @param unknown_type $activity_id An action (1: New, 2, 3, 4, 5, 6)
     */
    public static function completeItem($envelope_id, $activity_id, $api_mobile = 0)
    {
        ci()->load->model('scans/envelope_completed_m');
        ci()->load->model('scans/envelope_m');

        if($api_mobile){

           $user = MobileContext::getAdminLoggedIn();
           $completed_by = (!empty($user)) ? $user->id:""; 
        }
        else{

            $completed_by = APContext::getAdminIdLoggedIn();
        }
       

        $envelope = ci()->envelope_m->get_by_many(array("id" => $envelope_id));
        if ($envelope) {
            $envelope_completed = ci()->envelope_completed_m->get_by_many(
                array(
                    "envelope_id" => $envelope_id,
                    "activity_id" => $activity_id
                )
            );
            if (empty($envelope_completed)) {
                $new_activity = array();

                $new_activity["completed_by"] = $completed_by;

                $new_activity["completed_date"] = now();

                // Unset id
                $new_activity['activity_id'] = $activity_id;
                $new_activity['envelope_id'] = $envelope_id;

                $new_activity['from_customer_name'] = $envelope->from_customer_name;
                $new_activity['to_customer_id'] = $envelope->to_customer_id;
                $new_activity['postbox_id'] = $envelope->postbox_id;
                $new_activity['envelope_type_id'] = $envelope->envelope_type_id;
                $new_activity['weight'] = $envelope->weight;
                $new_activity['weight_unit'] = $envelope->weight_unit;
                $new_activity['last_updated_date'] = $envelope->last_updated_date;
                $new_activity['incomming_date'] = $envelope->incomming_date;
                $new_activity['category_type'] = $envelope->category_type;
                $new_activity['invoice_flag'] = $envelope->invoice_flag;
                $new_activity['shipping_type'] = $envelope->shipping_type;
                $new_activity['include_estamp_flag'] = $envelope->include_estamp_flag;
                $new_activity['sync_cloud_flag'] = $envelope->sync_cloud_flag;
                $new_activity['envelope_scan_flag'] = $envelope->envelope_scan_flag;
                $new_activity['item_scan_flag'] = $envelope->item_scan_flag;
                $new_activity['direct_shipping_flag'] = $envelope->direct_shipping_flag;
                $new_activity['collect_shipping_flag'] = $envelope->collect_shipping_flag;
                $new_activity['trash_flag'] = $envelope->trash_flag;
                $new_activity['storage_flag'] = $envelope->storage_flag;
                $new_activity['completed_flag'] = APConstants::ON_FLAG;
                $new_activity['email_notification_flag'] = $envelope->email_notification_flag;
                $new_activity['location_id'] = $envelope->location_id;

                // Insert to completed table
                ci()->envelope_completed_m->insert($new_activity);
            } else {
                ci()->envelope_completed_m->update_by_many(
                    array(
                        'id' => $envelope_completed->id
                    ),
                    array(
                        "completed_by" => $completed_by,
                        "completed_date" => now(),
                        'last_updated_date' => now()
                    )
                );
            }
        }
    }
    
    public static function getTotalStorageDaysBy($location_id, $target_month){
        ci()->load->model('scans/envelope_m');
        
        $total = ci()->envelope_m->sum_by_many(array(
            
        ),"current_storage_charge_fee_day");
    }
    
    public static function updateStorageStatus($envelop_id, $customer_id, $postbox_id, $year, $month, $location_id, $status){
        ci()->load->model('scans/envelope_storage_month_m');
        
        $check_flag = ci()->envelope_storage_month_m->get_by_many(array(
            "envelope_id" => $envelop_id,
            "customer_id" => $customer_id,
            "postbox_id" => $postbox_id,
            "location_id" => $location_id,
            "year" => $year,
            "month" => $month,
        ));
        
        if($check_flag){
            ci()->envelope_storage_month_m->update_by_many(array(
                "envelope_id" => $envelop_id,
                "customer_id" => $customer_id,
                "postbox_id" => $postbox_id,
                "location_id" => $location_id,
                "year" => $year,
                "month" => $month,
            ), array(
                "storage_flag" => $status
            ));
        }else{
            ci()->envelope_storage_month_m->insert(array(
                "envelope_id" => $envelop_id,
                "customer_id" => $customer_id,
                "postbox_id" => $postbox_id,
                "location_id" => $location_id,
                "year" => $year,
                "month" => $month,
                "storage_flag" => $status,
                'created_date' =>now()
            ));
        }
    }
    
    /**
     * Get selected forwarding address of envelopes.
     */
    public static function getSelectedForwardingAddressOfEnvelopes($customer_id, $shipping_address_id=0){
        ci()->load->model('addresses/customers_address_m');
        ci()->load->model('addresses/customers_forward_address_m');
        
        if(!empty($shipping_address_id)){
            $result = ci()->customers_forward_address_m->get_by_many(array(
                "id" => $shipping_address_id,
                "customer_id" => $customer_id
            ));
            
            return  $result;
        }else{
            $result = ci()->customers_address_m->get_by_many(array(
                "customer_id" => $customer_id
            ));
            
            return  $result;
        }
    }
    
    /**
     * get incomming list
     */
    public static function getIncomingList($customer_id, $from_customer_name, $type, $weight, $term, $list_filter_location_id, $input_paging, $limit){
        ci()->load->library('scans/incoming_api');
        
        $response = ci()->incoming_api->getIncomingList($customer_id, $from_customer_name, $type, $weight, $term, $list_filter_location_id, $input_paging, $limit);
        return $response;
    }

    
    /**
     * Insert activity log to envelope_complete
     * @param type $envelope_ids
     * @param type $activity_id
     * @param type $completed_by_type default = system
     * @param type $completed_by default = system
     * @return type
     */
    public static function insertCompleteItem($envelope_ids, $activity_id, $completed_by_type = APConstants::TRIGGER_BY_SYSTEM, $completed_by = 0) {
        // Load 
        ci()->load->model('scans/envelope_completed_m');
        ci()->load->model('scans/envelope_m');
        
        if (empty($envelope_ids) || empty($activity_id)) {
            return;
        }
        
        //Insert activity log 
        ci()->envelope_completed_m->insert_activity_history($envelope_ids, $activity_id, $completed_by_type, $completed_by);
    }

}