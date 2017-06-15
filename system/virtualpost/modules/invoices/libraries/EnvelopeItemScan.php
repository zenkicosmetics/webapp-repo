<?php defined('BASEPATH') or exit('No direct script access allowed');

class EnvelopeItemScan
{
    public static function getCostForEnvelopeScan($customerID, $postboxID, $envelopeID)
    {
        ci()->load->library('invoices/invoices_api');
        ci()->load->library('customers/customers_api');
        ci()->load->library('addresses/addresses_api');
        ci()->load->library('mailbox/mailbox_api');
        ci()->load->library('price/price_api');
        ci()->load->library('scans/scans_api');

        $postbox = mailbox_api::getPostBoxByID($postboxID);
        $postboxType = $postbox->type;
        $locationID = $postbox->location_available_id;
        
        // Update #1438
        // $pricingMap = price_api::getPricingModelByLocationID($locationID);
        $pricingMap = price_api::getPricingModelByCusotomerAndLocationID($customerID, $locationID);

        $customer = customers_api::getCustomerByID($customerID);
        $customerAddress = addresses_api::getCustomerAddress($customerID);
        $customerVAT = customers_api::getVATCustomer($customer, $customerAddress);
        $vat = $customerVAT->rate;
        $vatCaseID = $customerVAT->vat_case_id;
        $invoiceSummaryID = invoices_api::checkInvoiceOfCurrentMonth($customerID, $vat, $vatCaseID);

        // The number of Envelope scans without charge
        $envelopeScanningFront = $pricingMap[$postboxType]['envelope_scanning_front'];

        // Charge fee for one additional Envelope scan
        $envelopScanning = $pricingMap[$postboxType]['envelop_scanning'];

        // Get the number of current envelope scans in this month
        $currentEnvelopeScanNumber = scans_api::getNumberEnvelopeScansOfCurrentMonth($customerID, $postboxID, $envelopeID);

        $scanEnvelopePrice = 0;
        $envelopeScanNumberFlag = 1;
        if ($currentEnvelopeScanNumber + 1 > $envelopeScanningFront) {
            $scanEnvelopePrice = $envelopScanning;
            $envelopeScanNumberFlag = 0;
        }
        return $scanEnvelopePrice;
    }
    /**
     * Get cost for pre-payment scan of envelope
     * @param type $customerID
     * @param type $postboxID
     * @param type $envelopeID
     * @return type
     */
    public static function getCostForPreEnvelopeScan($customerID, $postboxID, $envelopeID)
    {
        log_audit_message(APConstants::LOG_ERROR, 'getCostForPreEnvelopeScan: '.$customerID.'|'.$postboxID.'|'.$envelopeID);
        ci()->load->library('invoices/invoices_api');
        ci()->load->library('customers/customers_api');
        ci()->load->library('addresses/addresses_api');
        ci()->load->library('mailbox/mailbox_api');
        ci()->load->library('price/price_api');
        ci()->load->library('scans/scans_api');
        ci()->load->model('scans/envelope_prepayment_cost_m');
        
        $key_array = array(
                "envelope_id" => $envelopeID,
                "postbox_id" => $postboxID,
                "customer_id" => $customerID
            );
        $check_exist = ci()->envelope_prepayment_cost_m->get_by_many($key_array);
        if (!empty($check_exist) && $check_exist->envelope_scan_cost != NULL) {
            return $check_exist->envelope_scan_cost;
        }

        $postbox = mailbox_api::getPostBoxByID($postboxID);
        $postboxType = $postbox->type;
        $locationID = $postbox->location_available_id;
        
        // Update #1438
        // $pricingMap = price_api::getPricingModelByLocationID($locationID);
        $pricingMap = price_api::getPricingModelByCusotomerAndLocationID($customerID, $locationID);

        $customer = customers_api::getCustomerByID($customerID);
        $customerAddress = addresses_api::getCustomerAddress($customerID);
        $customerVAT = customers_api::getVATCustomer($customer, $customerAddress);
        $vat = $customerVAT->rate;
        $vatCaseID = $customerVAT->vat_case_id;
        invoices_api::checkInvoiceOfCurrentMonth($customerID, $vat, $vatCaseID);

        // The number of Envelope scans without charge
        $envelopeScanningFront = $pricingMap[$postboxType]['envelope_scanning_front'];

        // Charge fee for one additional Envelope scan
        $envelopScanning = $pricingMap[$postboxType]['envelop_scanning'];

        // Get the number of current envelope scans in this month
        $currentEnvelopeScanNumber = scans_api::getNumberEnvelopeScansOfCurrentMonth($customerID, $postboxID, $envelopeID);

        // Get current number of pending
        $numberEnvelopeScanPending = mailbox_api::countAllPendingPrepaymentEnvelopScanItem($customerID);
        
        $scanEnvelopePrice = 0;
        $envelopeScanNumberFlag = 1;
        log_audit_message(APConstants::LOG_ERROR, 'envelopeScanningFront: '.$envelopeScanningFront);
        log_audit_message(APConstants::LOG_ERROR, 'currentEnvelopeScanNumber: '.$currentEnvelopeScanNumber);
        log_audit_message(APConstants::LOG_ERROR, 'numberEnvelopeScanPending: '.$numberEnvelopeScanPending);
        log_audit_message(APConstants::LOG_ERROR, 'envelopScanning: '.$envelopScanning);
        if ($currentEnvelopeScanNumber + $numberEnvelopeScanPending + 1 > $envelopeScanningFront) {
            $scanEnvelopePrice = $envelopScanning;
            $envelopeScanNumberFlag = 0;
        }
        log_audit_message(APConstants::LOG_ERROR, 'scanEnvelopePrice: '.$scanEnvelopePrice);
        return $scanEnvelopePrice;
    }
    public static function calculateCostForEnvelopeScan($customerID, $postboxID, $envelopeID)
    {
        ci()->load->library('invoices/invoices_api');
        ci()->load->library('customers/customers_api');
        ci()->load->library('addresses/addresses_api');
        ci()->load->library('mailbox/mailbox_api');
        ci()->load->library('price/price_api');
        ci()->load->library('scans/scans_api');

        $postbox = mailbox_api::getPostBoxByID($postboxID);
        $postboxType = $postbox->type;
        $locationID = $postbox->location_available_id;
        
        // Update #1438
        // $pricingMap = price_api::getPricingModelByLocationID($locationID);
        $pricingMap = price_api::getPricingModelByCusotomerAndLocationID($customerID, $locationID);

        $customer = customers_api::getCustomerByID($customerID);
        $customerAddress = addresses_api::getCustomerAddress($customerID);
        $customerVAT = customers_api::getVATCustomer($customer, $customerAddress);
        $vat = $customerVAT->rate;
        $vatCaseID = $customerVAT->vat_case_id;
        $invoiceSummaryID = invoices_api::checkInvoiceOfCurrentMonth($customerID, $vat, $vatCaseID);

        // The number of Envelope scans without charge
        $envelopeScanningFront = $pricingMap[$postboxType]['envelope_scanning_front'];

        // Charge fee for one additional Envelope scan
        $envelopScanning = $pricingMap[$postboxType]['envelop_scanning'];

        // Get the number of current envelope scans in this month
        $currentEnvelopeScanNumber = scans_api::getNumberEnvelopeScansOfCurrentMonth($customerID, $postboxID, $envelopeID);
        
        $scanEnvelopePrice = 0;
        $envelopeScanNumberFlag = 1;
        if ($currentEnvelopeScanNumber + 1 > $envelopeScanningFront) {
            $scanEnvelopePrice = $envelopScanning;
            $envelopeScanNumberFlag = 0;
        }
        log_audit_message(APConstants::LOG_INFOR, 'Customer ID:'.$customerID.', postboxID:'.$postboxID.', currentEnvelopeScanNumber'.$currentEnvelopeScanNumber
                .', envelopeScanningFront'.$envelopeScanningFront.', envelopeScanNumberFlag'.$envelopeScanNumberFlag, false, 'calculateCostForEnvelopeScan');
        
        $currentEnvelopeSummary = scans_api::getEnvelopeSummaryOfCurrentMonth($customerID, $postboxID, $envelopeID);
        if ($currentEnvelopeSummary) {
            scans_api::updateEnvelopeSummaryOfCurrentMonth($customerID, $postboxID, $envelopeID, $envelopeScanNumberFlag, $scanEnvelopePrice);
        } else {
            scans_api::createEnvelopeSummaryOfCurrentMonth($customerID, $postboxID, $envelopeID, $envelopeScanNumberFlag, $scanEnvelopePrice);
        }

        invoices_api::createInvoiceDetailForEnvelopeScanned($customerID, $envelopeID, $postboxType, $invoiceSummaryID, $scanEnvelopePrice);

        // Summary for invoice
        invoices_api::calculateInvoiceSummary($customerID, date('Y'), date('m'));
        return $scanEnvelopePrice;
    }
    
    public static function getCostForItemScan($customerID, $postboxID, $envelopeID)
    {
        ci()->load->library('invoices/invoices_api');
        ci()->load->library('customers/customers_api');
        ci()->load->library('addresses/addresses_api');
        ci()->load->library('mailbox/mailbox_api');
        ci()->load->library('price/price_api');
        ci()->load->library('scans/scans_api');

        $postbox = mailbox_api::getPostBoxByID($postboxID);
        $postboxType = $postbox->type;
        $locationID = $postbox->location_available_id;
        
        // Update #1438
        // $pricingMap = price_api::getPricingModelByLocationID($locationID);
        $pricingMap = price_api::getPricingModelByCusotomerAndLocationID($customerID, $locationID);

        $customer = customers_api::getCustomerByID($customerID);
        $customerAddress = addresses_api::getCustomerAddress($customerID);
        $customerVAT = customers_api::getVATCustomer($customer, $customerAddress);
        $vat = $customerVAT->rate;
        $vatCaseID = $customerVAT->vat_case_id;
        $invoiceSummaryID = invoices_api::checkInvoiceOfCurrentMonth($customerID, $vat, $vatCaseID);

        // The number of Document scans without charge
        $includedOpeningScanning = $pricingMap[$postboxType]['included_opening_scanning'];

        // Charge fee for one additional Item (Document) scan
        $openingScanning = $pricingMap[$postboxType]['opening_scanning'];

        // The number of content pages that are included in ONE scan without additional charge.
        $additionalIncludedPageOpeningScanning = $pricingMap[$postboxType]['additional_included_page_opening_scanning'];

        // The cost of each additional content page above the included pages that need to be charged for
        $additionalPagesScanningPrice = $pricingMap[$postboxType]['additional_pages_scanning_price'];

        // Get the number of current Document scans in this month
        $currentDocumentScanNumber = scans_api::getNumberDocumentScansOfCurrentMonth($customerID, $postboxID, $envelopeID);

        $scanDocumentPrice = 0;
        $documentScanNumberFlag = 1;
        if ($currentDocumentScanNumber + 1 > $includedOpeningScanning) {
            $scanDocumentPrice = $openingScanning;
            $documentScanNumberFlag = 0;
        }
        return $scanDocumentPrice;
    }
    
    public static function getCostForPreItemScan($customerID, $postboxID, $envelopeID)
    {
        ci()->load->library('invoices/invoices_api');
        ci()->load->library('customers/customers_api');
        ci()->load->library('addresses/addresses_api');
        ci()->load->library('mailbox/mailbox_api');
        ci()->load->library('price/price_api');
        ci()->load->library('scans/scans_api');
        ci()->load->model('scans/envelope_prepayment_cost_m');
        
        $key_array = array(
                "envelope_id" => $envelopeID,
                "postbox_id" => $postboxID,
                "customer_id" => $customerID
            );
        $check_exist = ci()->envelope_prepayment_cost_m->get_by_many($key_array);
        if (!empty($check_exist) && $check_exist->item_scan_cost != NULL) {
            return $check_exist->item_scan_cost;
        }

        $postbox = mailbox_api::getPostBoxByID($postboxID);
        $postboxType = $postbox->type;
        $locationID = $postbox->location_available_id;
        
        // Update #1438
        // $pricingMap = price_api::getPricingModelByLocationID($locationID);
        $pricingMap = price_api::getPricingModelByCusotomerAndLocationID($customerID, $locationID);

        $customer = customers_api::getCustomerByID($customerID);
        $customerAddress = addresses_api::getCustomerAddress($customerID);
        $customerVAT = customers_api::getVATCustomer($customer, $customerAddress);
        $vat = $customerVAT->rate;
        $vatCaseID = $customerVAT->vat_case_id;
        invoices_api::checkInvoiceOfCurrentMonth($customerID, $vat, $vatCaseID);

        // The number of Document scans without charge
        $includedOpeningScanning = $pricingMap[$postboxType]['included_opening_scanning'];

        // Charge fee for one additional Item (Document) scan
        $openingScanning = $pricingMap[$postboxType]['opening_scanning'];

        // The number of content pages that are included in ONE scan without additional charge.
        $additionalIncludedPageOpeningScanning = $pricingMap[$postboxType]['additional_included_page_opening_scanning'];

        // The cost of each additional content page above the included pages that need to be charged for
        $additionalPagesScanningPrice = $pricingMap[$postboxType]['additional_pages_scanning_price'];

        // Get the number of current Document scans in this month
        $currentDocumentScanNumber = scans_api::getNumberDocumentScansOfCurrentMonth($customerID, $postboxID, $envelopeID);

        // Get current number of pending
        $numberItemScanPending = mailbox_api::countAllPendingPrepaymentItemScanItem($customerID);
        
        $scanDocumentPrice = 0;
        $documentScanNumberFlag = 1;
        if ($currentDocumentScanNumber + $numberItemScanPending + 1 > $includedOpeningScanning) {
            $scanDocumentPrice = $openingScanning;
            $documentScanNumberFlag = 0;
        }
        return $scanDocumentPrice;
    }

    public static function calculateCostForItemScan($customerID, $postboxID, $envelopeID)
    {
        ci()->load->library('invoices/invoices_api');
        ci()->load->library('customers/customers_api');
        ci()->load->library('addresses/addresses_api');
        ci()->load->library('mailbox/mailbox_api');
        ci()->load->library('price/price_api');
        ci()->load->library('scans/scans_api');

        $postbox = mailbox_api::getPostBoxByID($postboxID);
        $postboxType = $postbox->type;
        $locationID = $postbox->location_available_id;
        
        // Update #1438
        // $pricingMap = price_api::getPricingModelByLocationID($locationID);
        $pricingMap = price_api::getPricingModelByCusotomerAndLocationID($customerID, $locationID);

        $customer = customers_api::getCustomerByID($customerID);
        $customerAddress = addresses_api::getCustomerAddress($customerID);
        $customerVAT = customers_api::getVATCustomer($customer, $customerAddress);
        $vat = $customerVAT->rate;
        $vatCaseID = $customerVAT->vat_case_id;
        $invoiceSummaryID = invoices_api::checkInvoiceOfCurrentMonth($customerID, $vat, $vatCaseID);

        // The number of Document scans without charge
        $includedOpeningScanning = $pricingMap[$postboxType]['included_opening_scanning'];

        // Charge fee for one additional Item (Document) scan
        $openingScanning = $pricingMap[$postboxType]['opening_scanning'];

        // The number of content pages that are included in ONE scan without additional charge.
        $additionalIncludedPageOpeningScanning = $pricingMap[$postboxType]['additional_included_page_opening_scanning'];

        // The cost of each additional content page above the included pages that need to be charged for
        $additionalPagesScanningPrice = $pricingMap[$postboxType]['additional_pages_scanning_price'];

        // Get the number of current Document scans in this month
        $currentDocumentScanNumber = scans_api::getNumberDocumentScansOfCurrentMonth($customerID, $postboxID, $envelopeID);

        $scanDocumentPrice = 0;
        $documentScanNumberFlag = 1;
        if ($currentDocumentScanNumber + 1 > $includedOpeningScanning) {
            $scanDocumentPrice = $openingScanning;
            $documentScanNumberFlag = 0;
        }

        // Get the number pages of current document scan
        $numberPages = scans_api::getNumberPagesOfDocumentScan($customerID, $envelopeID);

        // Invoice detail for additional pages scanned
        $additionalPagesScanningNumber = 0;
        if ($numberPages > $additionalIncludedPageOpeningScanning) {
            $additionalPagesScanningNumber = $numberPages - $additionalIncludedPageOpeningScanning;
        }
        if ($additionalPagesScanningNumber > 0) {
            $additionalInvoiceDetail = invoices_api::checkExistInvoiceDetailForAdditionalDocumentScan($customerID, $envelopeID);
            if ($additionalInvoiceDetail) {
                invoices_api::updateInvoiceDetailForAdditionalPagesScanned($customerID, $envelopeID, $postboxType, $invoiceSummaryID, $additionalPagesScanningNumber, $additionalPagesScanningPrice);
            } else {
                invoices_api::createInvoiceDetailForAdditionalPagesScanned($customerID, $envelopeID, $postboxType, $invoiceSummaryID, $additionalPagesScanningNumber, $additionalPagesScanningPrice);
            }
        }

        // Update information of [envelope_summary_month] for Document scan
        $currentEnvelopeSummary = scans_api::getEnvelopeSummaryOfCurrentMonth($customerID, $postboxID, $envelopeID);
        if ($currentEnvelopeSummary) {
            scans_api::updateDocumentSummaryOfCurrentMonth($customerID, $postboxID, $envelopeID, $documentScanNumberFlag, $scanDocumentPrice, $additionalPagesScanningNumber, $additionalPagesScanningPrice, $numberPages);
        } else {
            scans_api::createDocumentSummaryOfCurrentMonth($customerID, $postboxID, $envelopeID, $documentScanNumberFlag, $scanDocumentPrice, $additionalPagesScanningNumber, $additionalPagesScanningPrice, $numberPages);
        }

        invoices_api::createInvoiceDetailForDocumentScanned($customerID, $envelopeID, $postboxType, $invoiceSummaryID, $scanDocumentPrice);

        // Summary for invoice
        invoices_api::calculateInvoiceSummary($customerID, date('Y'), date('m'));
        return $scanDocumentPrice;
    }
}