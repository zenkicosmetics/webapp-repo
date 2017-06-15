<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author DungNT
 */
class APConstants
{
    /**
     * Version number - for media asset
     */
    const VER_NO = '1.1.1';
    const CACHED_SECONDS = 86400;
    /**
     * Log debug
     */
    const LOG_DEBUG = "DEBUG";

    /**
     * Log debug
     */
    const LOG_ERROR = "ERROR";

    /**
     * Log infor
     */
    const LOG_INFOR = "INFO";

    /**
     * Default number of page record
     */
    const DEFAULT_PAGE_ROW = 10;
    
    /**
     * Default Deutsche Post Brief ID
     */
    const DEUTSCHE_POST_BRIEF_SHIPPING_SERVICE_ID = '6';
    
    /**
     * Default Standard
     */
    const DEFAULT_SHIPPING_SERVICE_ID = '0';
    
    
    const DEFAULT_CLEVVERMAIL_DOMAIN = 'clevvermail.com';

    /**
     * Number page item display.
     *
     * @var unknown_type
     */
    const PANATION_URI_SEGMET = 5;
    const PAYPAL_STATUS_NEW = 'New';
    const PAYPAL_STATUS_APPROVAL = 'Approval';
    const PAYPAl_STATUS_ERROR = 'Error';
    const PAYPAl_STATUS_PENDING = 'Pending';

    /**
     * Declare key to store supplier information to session
     */
    const SESSION_MEMBERS_KEY = "SESSION_MEMBERS_KEY";

    /**
     * Declare key to store customer information to session
     */
    const SESSION_CUSTOMER_KEY = "SESSION_CUSTOMER_KEY";
    
    /**
     * Declare key to store PARENT customer information to session
     */
    const SESSION_PARENT_CUSTOMER_KEY = "SESSION_PARENT_CUSTOMER_KEY";

    /**
     * Declare key to store customer information to session
     */
    const SESSION_MOBILE_CUSTOMER_KEY = "SESSION_MOBILE_CUSTOMER_KEY";
    const SESSION_MOBILE_ADMIN_KEY    = "SESSION_MOBILE_ADMIN_KEY";

    /**
     * Declare key to store customer information to session
     */
    const SESSION_MOBILE_KEY = "SESSION_MOBILE_KEY";

    /**
     * Declare key to store customer information to session
     */
    const SESSION_CUSTOMER_ADDRESS_KEY = "SESSION_CUSTOMER_ADDRESS_KEY";

    /**
     * Declare key to store customer information to session
     */
    const SESSION_CLOUD_CUSTOMER_KEY = "SESSION_CLOUD_CUSTOMER_KEY";

    /**
     * Declare key to store customer information to session
     */
    const SESSION_USERADMIN_KEY = "SESSION_USERADMIN_KEY";

    /**
     * Declare key to store customer information to session
     */
    const SESSION_SUPPERUSERADMIN_KEY = "SESSION_SUPPERUSERADMIN_KEY";

    /**
     * Declare key to store customer information to session
     */
    const SESSION_INSTANCE_ID_KEY = "SESSION_INSTANCE_ID_KEY";

    /**
     * Declare key to store customer information to session
     */
    const SESSION_CUSTOMER_SETTING_KEY = "SESSION_CUSTOMER_SETTING_KEY";

    /**
     * Declare slug name of email template (active_user)
     */
    const SESSION_CUSTOMER_TOKEN_KEY = "SESSION_CUSTOMER_TOKEN_KEY";

    /**
     * Declare key to store customer information to session
     */
    const DIRECT_ACCESS_CUSTOMER_KEY = "DIRECT_ACCESS_CUSTOMER_KEY";
    
    const SESSION_MOBILE_GROUP_USERS_ROLE = "SESSION_MOBILE_GROUP_USERS_ROLE";
    const SESSION_GROUP_USERS_ROLE = "SESSION_GROUP_USERS_ROLE";
    
    const SESSION_SHOW_MOBILE_ADV_FIRST_LOGIN = "SESSION_SHOW_MOBILE_ADV_FIRST_LOGIN";

    /**
     * Declare slug name of email template (active_user)
     */
    const SESSION_GROUP_SEARCH_KEY = "SESSION_GROUP_SEARCH_KEY";
    const SESSION_MAP_PRODUCT_KEY = "SESSION_MAP_PRODUCT_KEY";
    const SESSION_SKIP_CUS_KEY = "SESSION_SKIP_CUS_KEY";
    const PAYMENT_CUSTOMER_LIMIT = "5";
    
    const SESSION_UPDATE_CALL_HISTORY_SONTEL = "SESSION_UPDATE_CALL_HISTORY_SONTEL";
    
    // Don't use
    const SESSION_CUSTOMER_USER = "SESSION_CUSTOMER_USER";
    
    /** contain session data of postbox id */
    const SESSION_POSTBOX_USER_DATA= "SESSION_POSTBOX_USER_DATA";
    
    /** contain session data of phone number id */
    const SESSION_PHONENUMBER_USER_DATA= "SESSION_PHONENUMBER_USER_DATA";
    
    /** contain session data of phones id */
    const SESSION_PHONES_USER_DATA= "SESSION_PHONES_USER_DATA";
    
    /** contain session data of phones id */
    const SESSION_PHONE_INCOMMING_SETTING_USER_DATA= "SESSION_PHONE_INCOMMING_SETTING_USER_DATA";
    
    /** contain session data of phones id */
    const SESSION_PHONE_OUTGOING_SETTING_USER_DATA= "SESSION_PHONE_OUTGOING_SETTING_USER_DATA";
    
    /** contain session data of phones id */
    const SESSION_PHONE_LOCATION_AREA_USER_DATA= "SESSION_PHONE_LOCATION_AREA_USER_DATA";

    /*
     * ---------------------------------------------------------------------------------------
     * Envelope status
     * ---------------------------------------------------------------------------------------
     */
    // New status (when worker add to system)
    const OFF_FLAG = '0';
    const ON_FLAG = '1';
    const DELETE_ACTIVITY_FLAG = "2";
    const CANCEL_ACTIVITY_FLAG = "3";
    const MARK_COMPLETED_DELETE_FLAG = "6";

    /*
     * ---------------------------------------------------------------------------------------
     * Envelope activity id
     * ---------------------------------------------------------------------------------------
     */
    const ENVELOPE_NEW_ACTIVITY = 'Incomming';
    const ENVELOPE_ENVELOPE_SCAN_ACTIVITY = 'Scan envelope';
    const ENVELOPE_DOCUMENT_SCAN_ACTIVITY = 'Scan item';
    const ENVELOPE_DIRECT_SHIP_ACTIVITY = 'Direct forwarding';
    const ENVELOPE_COLLECT_SHIP_ACTIVITY = 'Collect forwarding';
    const ENVELOPE_TRASH_ACTIVITY = 'Trash';

    /*
     *
     * ---------------------------------------------------------------------------------------
     * Cloud service code
     * ---------------------------------------------------------------------------------------
     */
    // Dropbox
    const CLOUD_DROPBOX_CODE = '001';

    // iCloud
    const CLOUD_ICLOUD_CODE = '002';

    // Skydrive
    const CLOUD_SKYDRIVE_CODE = '003';

    // Google Drive
    const CLOUD_GOOGLEDRIVE_CODE = '004';

    // Amazon Drive
    const CLOUD_AMAZONDRIVE_CODE = '005';

    // Evernote
    const CLOUD_EVERNOTE_CODE = '006';

    // Box.net
    const CLOUD_BOXNET_CODE = '007';
    
    // Acouting interface
    const CLOUD_ACCOUNTING_EMAIL_CODE = '008';

    /*
     *
     * ---------------------------------------------------------------------------------------
     * Order status
     * ---------------------------------------------------------------------------------------
     */
    const ORDER_INCOMPLETE = 'Incomplete';
    const ORDER_TEMPORARY = 'Temporary';
    const ORDER_COMPLETE = 'Complete';
    const ORDER_PENDING = 'Pending';
    const ORDER_PROCESSED = 'Processed';
    const ORDER_PARTIALLY_SHIPPED = 'Partially Shipped';
    const ORDER_SHIPPING = 'Shipping';
    const ORDER_SHIPPED = 'Shipped';
    const ORDER_PARTIALLY_RETURNED = 'Partially Returned';
    const ORDER_RETURNED = 'Returned';
    const ORDER_CANCELED = 'Canceled';
    const ORDER_ORDERED = 'Ordered';

    /*
     *
     * ---------------------------------------------------------------------------------------
     * Declare all Setting key constant
     * ---------------------------------------------------------------------------------------
     */
    // Number record per page (10)
    const NUMBER_RECORD_PER_PAGE_CODE = '000001';
    // Drop downlist for paging (10,20,30)
    const DROPDOWN_LIST_CODE = '000002';
    // Drop downlist for active (1: Active,0: UnActive)
    const DROPDOWN_ACTIVE_CODE = '000003';
    // Administrator themes
    const ADMIN_THEMES_CODE = '000004';
    // Frontend themes
    const FRONTEND_THEMES_CODE = '000005';


    /**
     * EMAIL
     */
    // MAIL_PROTOCOL
    const MAIL_PROTOCOL_CODE = '000006';
    // MAIL_SENDMAIL_PATH
    const MAIL_SENDMAIL_PATH_CODE = '000007';
    // MAIL_SMTP_HOST
    const MAIL_SMTP_HOST_CODE = '000008';
    // MAIL_SMTP_USER
    const MAIL_SMTP_USER_CODE = '000009';
    // MAIL_SMTP_PASS
    const MAIL_SMTP_PASS_CODE = '000010';
    // MAIL_SMTP_PORT
    const MAIL_SMTP_PORT_CODE = '000011';
    // MAIL_ALIAS_NAME
    const MAIL_ALIAS_NAME_CODE = '000012';
    // CONTACT_EMAIL
    const MAIL_CONTACT_CODE = '000013';
    // MAIL_SERVER
    const MAIL_SERVER_CODE = '000014';

    /**
     * GENERAL
     */
    // SITE_NAME
    const SITE_NAME_CODE = '000015';
    // SITE_SLOGAN
    const SITE_SLOGAN_CODE = '000016';
    // SITE_LOGO
    const SITE_LOGO_CODE = '000031';
    // DATE_FORMAT
    const DATE_FORMAT_CODE = '000017';
    // CURRENTCY
    const CURRENTCY_CODE = '000018';
    // SITE_STATUS
    const SITE_STATUS_CODE = '000019';
    // SITE_MESSAGE
    const SITE_UNAVAILABLE_MESSAGE_CODE = '000020';

    /**
     * PAYMENT
     */
    // PAYMENT_PAYPAL_USERNAME
    const PAYMENT_PAYPAL_USERNAME_CODE = '000021';
    // PAYMENT_PAYPAL_PASSWORD
    const PAYMENT_PAYPAL_PASSWORD_CODE = '000022';
    // PAYMENT_PAYPAL_SIGNATURE
    const PAYMENT_PAYPAL_SIGNATURE_CODE = '000023';
    // PAYMENT_EWAY_CUSTOMERID
    const PAYMENT_EWAY_CUSTOMERID_CODE = '000024';

    /**
     * Envelope
     */
    // ENVELOPE_TYPE_CODE
    const ENVELOPE_TYPE_CODE = '000025';
    // CATEGORY_TYPE_CODE
    const CATEGORY_TYPE_CODE = '000026';
    // E-Mail notiï¬�cation for incomming
    const EMAIL_NOTIFICATION_CODE = '000027';
    // Invoicing cycle
    const INVOICING_CYCLE_CODE = '000028';
    // Collect items for shipping
    const COLLECT_ITEMS_SHIPPING_CODE = '000029';
    // Weekday for shipping
    const WEEKDAY_SHIPPING_CODE = '000030';

    /**
     * Account type
     */
    const ACCOUNT_TYPE = '000033';
    const SHIPPING_TYPE = '000034';
    const LINK_CHECK_VAT = '000035';
    const SHIPPING_TYPE_FEDEX_LABEL_SIZE = '000236';

    /**
     * E-Stamp infor
     */
    const ESTAMP_LINK = '000036';
    const ESTAMP_USER = '000037';
    const ESTAMP_PASSWORD = '000038';
    const ESTAMP_PARTNER_ID = '000039';
    const ESTAMP_KEY_PHASE = '000040';
    const ESTAMP_SCHLUESSEL_DPWN_MARKTPLATZ = '000041';
    const ESTAMP_NAMESPACE = '000042';

    /**
     * Dropbox Setting Infomartion
     */
    const DROPBOX_APP_KEY = '000043';
    const DROPBOX_APP_SECRET = '000044';
    const DROPBOX_FILE_TAG = 'file';
    const DROPBOX_FOLDER_TAG = 'folder';

    /**
     * Shipping & Handding fee key
     */
    const SHIPPING_HANDDING_KEY = '000045';
    const PRINTER_KEY = '000046';
    const FIRST_LETTER_KEY = '000047';
    const FIRST_ENVELOPE_KEY = '000048';
    const SITE_LOGO_WHITE_CODE = '000049';
    
    /**
     * color setting
     */
    const MAIN_COLOR_CODE = '000050';
    const SECOND_COLOR_CODE = '000051';
    const COLORS_LIST_KEY = '000256';
    
    /**
     * Payone
     */
    const MERCHANT_ID_CODE = '000052';
    const SUB_ACCOUNT_ID_CODE = '000053';
    const PORTAL_ID_CODE = '000054';
    const PORTAL_KEY_CODE = '000055';

    /**
     * Instance owner
     */
    const INSTANCE_OWNER_COMPANY_CODE = '000056';
    const INSTANCE_OWNER_STREET_CODE = '000057';
    const INSTANCE_OWNER_PLZ_CODE = '000058';
    const INSTANCE_OWNER_CITY_CODE = '000059';
    const INSTANCE_OWNER_REGION_CODE = '000060';
    const INSTANCE_OWNER_COUNTRY_CODE = '000061';
    const INSTANCE_OWNER_TAX_NUMBER_CODE = '000062';
    const INSTANCE_OWNER_DIRECTOR_CODE = '000063';
    const INSTANCE_OWNER_IBAN_CODE = '000064';
    const INSTANCE_OWNER_SWIFT_CODE = '000065';
    const INSTANCE_OWNER_BANK_NAME_CODE = '000066';
    const INSTANCE_OWNER_TEL_INVOICE_CODE = '000067';
    const INSTANCE_OWNER_FAX_CODE = '000068';
    const INSTANCE_OWNER_TEL_SALES_CODE = '000069';
    const INSTANCE_OWNER_TEL_SUPPORT_CODE = '000070';
    const INSTANCE_OWNER_MAIL_INVOICE_CODE = '000071';
    const INSTANCE_OWNER_MAIL_SALES_CODE = '000072';
    const INSTANCE_OWNER_WEBSITE_CODE = '000073';
    const INSTANCE_OWNER_REGISTERED_NUM_CODE = '000074';
    const INSTANCE_OWNER_PLACE_REGISTRATION_CODE = '000075';
    const INSTANCE_OWNER_VAT_NUM_CODE = '000076';
    const INSTANCE_OWNER_MAIL_SUPPORT_CODE = '000077';
    const INSTANCE_OWNER_ACCOUNTNUMBER_CODE = '000078';
    const INSTANCE_OWNER_BANKCODE_CODE = '000079';
    const INSTANCE_OWNER_POSTCODE_CODE = '000080';
    const TEST_MERCHANT_ID_CODE = '000081';
    const TEST_SUB_ACCOUNT_ID_CODE = '000082';
    const TEST_PORTAL_ID_CODE = '000083';
    const TEST_PORTAL_KEY_CODE = '000084';
    const PAYMENT_PAYPAL_TEST_MODE = '000085';
    const PAYMENT_PAYPAL_CURRENCY_CODE = '000086';
    const PAYMENT_PAYPAL_CLIENT_ID = '000087';
    const PAYMENT_PAYPAL_CLIENT_SECRET = '000088';
    const PAYMENT_PAYPAL_MERCHANT_ID = '000089';
    const STORAGE_FEE_BASELINE_DATE = '000090';
    const APP_MODE = '000091';
    const MAILCHIMP_LIST_ID = '000092';
    const MAILCHIMP_API_KEY = '000093';
    const LIST_LANGUAGE_SUPPORT_KEY = '000094';
    const FLAG_UPLOAD_S3 = '000095';
    const INSTANCE_OWNER_CUSTOMS_NUMBER = '000096';

    const LINK_CHECK_VAT_02 = '000097';

    const LIST_DESC_MANUAL_INVOICES = '000098';
    const PDF_INFO_DIR_KEY = '000099';

    const GOOGLE_ADWORD_CLIENT_ID = '000100';
    const GOOGLE_ADWORD_CLIENT_SECRET = '000101';
    const GOOGLE_ADWORD_API_KEY = '000102';

    // widget themes
    const WIDGET_THEMES_CODE = '000103';

    const GOOGLE_ADWORD_ACCESS_TOKEN = '000111';
    const GOOGLE_ADWORD_REFRESH_TOKEN = '000112';

    const ENABLE_FEDEX_SHIPPING_KEY = '000113';

    /** Absolute path data files. */
    const ABSOLUTE_PATH_DATA_FILE = "000114";

    /** Cronjob mailing list (The list of emails to receive notifications from jobs' execution) */
    const CRON_MAILING_LIST = '000115';

    /** Absolute path logs file. */
    const ABSOLUTE_PATH_LOGS_FILE = '000116';

    /** Absolute path upload files */
    const ABSOLUTE_PATH_UPLOAD_FILE = '000117';

    /** The pem file path */
    const PUSH_IOS_PEM_FILE_PATH = '000118';

    /** The password of pem file */
    const PUSH_IOS_PEM_PASSWORD = '000119';
    
    /** The estimate shipping cost */
    const ESTIMATE_SHIPPING_COST_LETTER_NATIONAL = '000220';
    const ESTIMATE_SHIPPING_COST_LETTER_INNATIONAL = '000221';
    const ESTIMATE_SHIPPING_COST_PACKAGE_NATIONAL = '000222';
    const ESTIMATE_SHIPPING_COST_PACKAGE_INNATIONAL = '000223';
    
    const PUSH_ANDROID_KEY = '000224';
    
    /** The code of api yahoo path in change currency rate */
    const CODE_API_YAHOO_CHANGED_CURRENCY_RATE = '000225';
    
    /** Accounting setting value */
    const THIRST_COUNTRY_TAXABLE = "000226";
    const EU_COUNTRY_TAXABLE = "000227";
    const INLAND_TAXABLE_REVENUE = "000228";
    const GEGENKONTO_NUMBER = "000229";
    
    /** User Profiles */
    const DATE_FROMAT_01_CODE = "000230";
    const LENGTH_UNIT_CODE = "000231";
    const WEIGHT_UNIT_CODE = "000232";
    const DECIMAL_SEPARATOR_CODE = "000233";
    
    /** expire days setting when customer direct access to mailbox */
    const EXPIRE_DAYS_DIRECT_ACCESS_MAILBOX = 2;
    
    /** NUMBER UNIT */
    const POUND_NUMBER_PER_GRAM_CODE = "000234";
    const INCH_NUMBER_PER_CENTIMET_CODE  = "000235";
    
    /** CASE VERIFICATION */
    const CASE_VERIFICATION_USPS_OFFICER_OWNER  = "000237";
    
    /** Dropdown list for shipping service */
    const SHIPPING_SERVICE_TYPE  = "000238";
    const SHIPPING_PACKAGING_TYPE  = "000239";
    const SHIPPING_TYPE_CANADAPOST_LABEL_SIZE = '000240';
    const SHIPPING_TYPE_SHIPPO_LABEL_SIZE = '000241';
    
    /** PHONE NUMBER FEATEURE */
    const PHONE_THEMES_CODE = '000242';
    const PHONE_APP_TYPE_CODE = '000243';
    const SONETEL_API_ENDPOINT = '000244';
    const SONETEL_API_KEY = '000245';
    const SONETEL_API_TOKEN = '000246';
    
    /** Dropdown list for customer type (employee, individual, Company) */
    const CUSTOMER_TYPE_CODE = '000247';
    
    /** Dropdown list for pricing contract terms */
    const PRICING_CONTRACT_TERM_CODE = '000248';
    
    /** Dropdown list for billing period */
    const PRICING_BILLING_PERIOD_CODE = '000249';
    
    /** Server side OCR API Key */
    const SERVER_OCR_API_KEY = '000250';
    
    /** Server side OCR API Endpoint */
    const SERVER_OCR_API_ENDPOINT = '000251';
    
    /** Server side OCR Executeable file */
    const SERVER_OCR_EXE_FILE_PATH = '000252';
    
    /** Server side OCR TESS DATA file */
    const SERVER_OCR_TESSDATA_FILE_PATH = '000253';
    
    /** Server side OCR CONFIG file */
    const SERVER_OCR_CONFIG_FILE_PATH = '000254';
    
    /** List language support (eng+deu+jpn+chi) */
    const SERVER_OCR_LIST_LANGUAGE = '000255';
    
    /** Dropbox setting */
    const DROPBOX_APP_ACCESS_TOKEN = '000256';
    const DROPBOX_CALLBACK_URL = '000257';
    
    /** 4: Standard customer, 5 : Enterprise customer */
    const CUSTOMER_TYPE = '000258';
    
    /**
     * ---------------------------------End Setting--------------------------------------
     */
    const FIRST_LETTER_CHARACTER = "_000";

    /**
     * Payment
     */
    const PAYMENT_DIRECT_DEBIT_ACCOUNT = "10";
    const PAYMENT_PAYPAL_ACCOUNT = "20";
    const PAYMENT_CREDIT_CARD_ACCOUNT = "30";

    /**
     * ---------------------------------Pricing--------------------------------------
     */
    const address_number = 'address_number';
    const included_incomming_items = 'included_incomming_items';
    const storage = 'storage';
    const hand_sorting_of_advertising = 'hand_sorting_of_advertising';
    const envelope_scanning_front = 'envelope_scanning_front';
    const included_opening_scanning = 'included_opening_scanning';
    const storing_items_letters = 'storing_items_letters';
    const storing_items_packages = 'storing_items_packages';
    const storing_items_digitally = 'storing_items_digitally';
    const trashing_items = 'trashing_items';
    const cloud_service_connection = 'cloud_service_connection';
    const additional_incomming_items = 'additional_incomming_items';
    const envelop_scanning = 'envelop_scanning';
    const opening_scanning = 'opening_scanning';
    const send_out_directly = 'send_out_directly';
    const send_out_collected = 'send_out_collected';
    const storing_items_over_free_letter = 'storing_items_over_free_letter';
    const storing_items_over_free_packages = 'storing_items_over_free_packages';
    const additional_private_mailbox = 'additional_private_mailbox';
    const additional_business_mailbox = 'additional_business_mailbox';

    /**
     * ---------------------------------Postbox and Account Type--------------------------------------
     */
    const FREE_TYPE = '1';
    const PRIVATE_TYPE = '2';
    const BUSINESS_TYPE = '3';
    const ENTERPRISE_TYPE = '5';

    /**
     * ---------------------------------Detail activity type--------------------------------------
     */
    const INCOMMING_ACTIVITY_TYPE = '1';
    /**
     * Scan envelope completed
     */
    const ENVELOPE_SCAN_ACTIVITY_TYPE = '2';
    /**
     * Scan item completed
     */
    const ITEM_SCAN_ACTIVITY_TYPE = '3';
    /**
     * Direct forwarding completed
     */
    const DIRECT_SHIPPING_ACTIVITY_TYPE = '4';
    /**
     * Collect forwarding completed
     */
    const COLLECT_SHIPPING_ACTIVITY_TYPE = '5';
    const ADDITIONAL_SCAN_ACTIVITY_TYPE = '6';

    const CUSTOMS_DECLARATION_01_ACTIVITY_TYPE = '7';
    const CUSTOMS_DECLARATION_02_ACTIVITY_TYPE = '8';
    
    // API Access cost for enterprise customer
    const INVOICE_ACTIVITY_TYPE_API_ACCESS = '9';
    
    // postbox fee
    const INVOICE_ACTIVITY_TYPE_POSTBOX_FEE = '10';
    // storage fee
    const INVOICE_ACTIVITY_TYPE_STORAGE_FEE_LETTER = '11';
    const INVOICE_ACTIVITY_TYPE_STORAGE_FEE_PACKAGE = '12';
    
    const INVOICE_ACTIVITY_TYPE_OWN_LOCATION = '13';
    const INVOICE_ACTIVITY_TYPE_TOUCH_PANEL_OWN_LOCATION = '14';
    const INVOICE_ACTIVITY_TYPE_OWN_MOBILE_APP = '15';
    const INVOICE_ACTIVITY_TYPE_CLEVVER_SUBDOMAIN = '16';
    const INVOICE_ACTIVITY_TYPE_OWN_SUBDOMAIN = '17';

    /**
     * ---------------------------------End Detail activity type-----------------------------------
     */
    /**
     * Completed activity (not use for payment and display in front end)
     */
    //Complete activity by admin
    const SCAN_ENVELOPE_COMPLETED_ACTIVITY_TYPE = '1';
    const SCAN_ITEM_COMPLETED_ACTIVITY_TYPE = '2';
    const DIRECT_FORWARDING_COMPLETED_ACTIVITY_TYPE = '3';
    const COLLECT_FORWARDING_COMPLETED_ACTIVITY_TYPE = '4';

    const TRASH_COMPLETED_ACTIVITY_TYPE = '5';
    const TRASH_AFTER_SCAN_ACTIVITY_TYPE = '6';
    const SCAN_BOTH_ACTIVITY_TYPE = '7';
    const TRASH_ORDER_BY_CUSTOMER_ACTIVITY_TYPE = '8';

    const CANCEL_SCAN_ACTIVITY_TYPE = '9';
    const REGISTERED_INCOMMING_ACTIVITY_TYPE = '10';
    
    //Request activity by customer
    const SCAN_ENVELOPE_ORDER_BY_CUSTOMER_ACTIVITY_TYPE = '11';
    const SCAN_ITEM_ORDER_BY_CUSTOMER_ACTIVITY_TYPE = '12';
    const DIRECT_FORWARDING_ORDER_BY_CUSTOMER_ACTIVITY_TYPE = '13';
    const MARK_COLLECT_FORWARDING_ORDER_BY_CUSTOMER_ACTIVITY_TYPE = '14';
    
    //Request activity by system
    const SCAN_ENVELOPE_ORDER_BY_SYSTEM_ACTIVITY_TYPE = '15';
    const SCAN_ITEM_ORDER_BY_SYSTEM_ACTIVITY_TYPE = '16';
    const DIRECT_FORWARDING_ORDER_BY_SYSTEM_ACTIVITY_TYPE = '17';
    const MARK_COLLECT_FORWARDING_ORDER_BY_SYSTEM_ACTIVITY_TYPE = '18';
    
    const TRASH_ORDER_BY_SYSTEM_ACTIVITY_TYPE = '19';
    const WAITING_FOR_PREPAYMENT_ACTIVITY_TYPE = '20';
    const WAITING_FOR_CUSTOMS_DECLARITON_ACTIVITY_TYPE = '21';
    
    const INCOMMING_DELETED_ACTIVITY_TYPE = '22';
    const TRIGGER_ITEM_COLLECT_FORWARDING_BY_CUSTOMER_ACTIVITY_TYPE = '23';
    const TRIGGER_ITEM_COLLECT_FORWARDING_BY_SYSTEM_ACTIVITY_TYPE = '24';
    
    const CANCEL_ENVELOPE_SCAN_REQUEST_BY_ADMIN_ACTIVITY_TYPE = "25";
    const CANCEL_ITEM_SCAN_REQUEST_BY_ADMIN_ACTIVITY_TYPE = "26";
    const CANCEL_DIRECT_FORWARDING_REQUEST_BY_ADMIN_ACTIVITY_TYPE = "27";
    const CANCEL_COLLECT_SHIPPING_REQUEST_BY_ADMIN_ACTIVITY_TYPE = "28";
    
    const REQUEST_TRACKING_NUMBER_ACTIVITY_TYPE = "29";
    const COMPLETED_TRACKING_NUMBER_ACTIVITY_TYPE = "30";
    const NO_TRACKING_NUMBER_ACTIVITY_TYPE = "31";
    const UPDATE_TRACKING_NUMBER_ACTIVITY_TYPE = "32";
    
    const SUBCRIBE_PHONE_ACCOUNT_ACTIVITY_TYPE = '33';
    const SUBCRIBE_PHONE_NUMBER_ACTIVITY_TYPE = '34';
    const RECURRING_PHONE_NUMBER_ACTIVITY_TYPE = '35';
    
    const UN_MARK_COLLECT_FORWARDING_BY_CUSTOMER_ACTIVITY_TYPE = '36';
    
    const REQUEST_PREPAYMENT_FOR_SCAN_ENVELOPE_BY_SYSTEM_ACTIVITY_TYPE = '37';
    const REQUEST_PREPAYMENT_FOR_SCAN_ITEM_BY_SYSTEM_ACTIVITY_TYPE = '38';
    const REQUEST_PREPAYMENT_FOR_DIRECT_FORWARDING_BY_SYSTEM_ACTIVITY_TYPE = '39';
    const REQUEST_PREPAYMENT_FOR_COLLECT_FORWARDING_BY_SYSTEM_ACTIVITY_TYPE = '40';
    
    const CANCEL_ENVELOPE_SCAN_BY_SYSTEM_ACTIVITY_TYPE = "41";
    const CANCEL_ITEM_SCAN_BY_SYSTEM_ACTIVITY_TYPE = "42";
    const CANCEL_DIRECT_FORWARDING_BY_SYSTEM_ACTIVITY_TYPE = "43";
    const CANCEL_COLLECT_SHIPPING_BY_SYSTEM_ACTIVITY_TYPE = "44";
    
    const MARK_SEND_SCAN_ITEM_TO_EMAIL_INTERFACE_BY_CUSTOMER_ACTIVITY_TYPE = '45';
    const MARK_SEND_SCAN_ITEM_TO_EMAIL_INTERFACE_BY_SYSTEM_ACTIVITY_TYPE = '46';
    
    const DISABLE_PREPAYMENT_REQUEST_BY_ADMIN_ACTIVITY_TYPE = '47';
    
    const CANCEL_ENVELOPE_SCAN_REQUEST_PREPAYMENT_BY_SYSTEM = "48";
    const CANCEL_ITEM_SCAN_REQUEST_BY_PREPAYMENT_SYSTEM = "49";
    const CANCEL_DIRECT_FORWARDING_REQUEST_PREPAYMENT_BY_SYSTEM = "50";
    const CANCEL_COLLECT_SHIPPING_REQUEST_PREPAYMENT_BY_SYSTEM = "51";
    
    /**
     * -------------------Declare Envelope trash status -----------------------
     */
    const ENVELOPE_TRASH_BY_CUSTOMER_IN_TRASH_FOLDER            = "5";
    const ENVELOPE_COMPLETED_TRASH_BY_ADMIN                     = "6";

    /**
     * -------------------Declare activity name (using in invoice detail table -----------------------
     */
    const SHIPPING_HANDING_INVOICE_DETAIL_AT = 'Shipping&Handling';
    const CUSTOMS_DECLARATION_01_INVOICE_DETAIL_AT = 'custom declaration >1000 EUR';
    const CUSTOMS_DECLARATION_02_INVOICE_DETAIL_AT = 'custom declaration <1000 EUR';
    
    const SUBCRIBE_PHONE_ACCOUNT_AT = 'Subscription phone account';
    const SUBCRIBE_PHONE_NUMBER_AT = 'Subscription phone number';
    const RECURRING_PHONE_NUMBER_AT = 'Recurring phone number 1 year';
    const USAGE_PHONE_NUMBER_AT = 'Phone number usage';

    /**
     * ---------------------------------Email template slug name--------------------------------------
     */
    const new_customer_register = 'new_customer_register';
    const customer_reset_password = 'customer_reset_password';
    const customer_change_password = 'customer_change_password';
    const new_incomming_notification = 'new_incomming_notification';
    const new_incomming_notification_daily = 'new_incomming_notification_daily';
    const new_incomming_notification_weekly = 'new_incomming_notification_weekly';
    const new_incomming_notification_monthly = 'new_incomming_notification_monthly';
    const declare_customs_notification = 'declare_customs_notification';
    const new_incomming_notification_for_notactivated = 'new_incomming_notification_for_notactivated';
    const scan_item_completed_notification = 'scan_item_completed_notification';
    const first_letter_notification = 'first_letter_notification';
    const new_business_account_notification = 'new_business_account_notification';
    const deactived_customer_notification = 'deactived_customer_notification';
    const new_business_account_notification_for_customer = 'new_business_account_notification_for_customer';
    const downgraded_business_account = 'downgraded_business_account';
    const email_is_not_confirmed_after_one_day = "email_is_not_confirmed_after_one_day";
    const email_is_not_confirmed_after_three_days = "email_is_not_confirmed_after_three_days";
    const email_is_not_confirmed_after_seven_days = "email_is_not_confirmed_after_seven_days";
    const account_is_not_activated_after_eight_days = "account_is_not_activated_after_eight_days";
    const account_is_not_activated_after_thirty_days = "account_is_not_activated_after_thirty_days";
    const account_is_not_activated_after_sixty_days = "account_is_not_activated_after_sixty_days";
    const account_is_auto_deactivated_after_eight_days = "account_is_auto_deactivated_after_eight_days";
    const account_is_auto_deactivated_after_thirty_days = "account_is_auto_deactivated_after_thirty_days";
    const account_is_auto_deactivated_after_sixty_days = "account_is_auto_deactivated_after_sixty_days";
    const account_has_been_deleted = "account_has_been_deleted";
    const email_invoices_report_by_monthly = "email_invoices_report_by_monthly";
    const email_invoices_report_for_creditnote = "email_invoices_report_for_creditnote";
    const admin_make_payment_invoices_success = 'admin_make_payment_invoices_success';
    const email_change_new_payment_method_standard = "email_change_new_payment_method_standard";
    const email_is_confirmed_card_expired_date_remain_seven_days = "email_is_confirmed_card_expired_date_remain_seven_days";
    const email_is_confirmed_card_expired_date_remain_thirty_days = "email_is_confirmed_card_expired_date_remain_thirty_days";
    const email_is_confirmed_card_expired_date_remain_sixty_days = "email_is_confirmed_card_expired_date_remain_sixty_days";
    const account_has_envelope_must_verify_case = "account_has_envelope_must_verify_case";
    const user_reset_password = "user_reset_password";
    const prepayment_notification_email = "prepayment_notification_email";
    const envelope_shipping_tracking_number = "envelope_shipping_tracking_number";
    const update_shipping_tracking_number = "update_shipping_tracking_number";
    const new_email_customer_confirmation = "new_email_customer_confirmation";
    const email_notified_postbox_successfully_verification_status = "email_notified_postbox_successfully_verification_status";
    const notify_email_to_location_admin = "notify_email_to_location_admin";
    const notify_email_new_and_delete_customers = "notify_email_new_and_delete_customers";
    const notify_email_report_deactive_and_delete_accounts = "notify_email_report_deactive_and_delete_accounts";
    const accounting_invoice_email = "accounting_invoice_email";
    const notify_payment_fails_email = "notify_payment_fails_email";
    const send_notify_new_terms_condition = "send_notify_new_terms_condition";
    const warning_accept_new_terms_condition = "warning_accept_new_terms_condition";
    const auto_forward_not_work_open_balance_prohibits = "auto_forward_not_work_open_balance_prohibits";
    
    const email_notify_warning_slow_account = "email_notify_warning_slow_account";
    const email_notify_assign_digital_panel_location_enterprise = "email_notify_assign_digital_panel_location_enterprise";
    /**
     * delete envelopes
     */
    const email_is_notified_envelope_is_direct_deleted = "email_is_notified_envelope_is_direct_deleted";
    const email_is_notified_envelope_is_trashed = "email_is_notified_envelope_is_trashed";

    /**
     * Email send for the first day of month
     */
    const email_notify_open_balance_due = "email_notify_open_balance_due";

    /**
     * Email send at 10 of month to deactivate customer
     * @var unknown_type
     */
    const send_email_notify_deactivate_open_balance_due = "send_email_notify_deactivate_open_balance_due";

    /**
     * Email send when customer have 1 auto envelope scan
     * @var unknown_type
     */
    const send_notify_auto_envelope_scan = "send_notify_auto_envelope_scan";

    /**
     * Email send when customer have 1 auto item scan
     * @var unknown_type
     */
    const send_notify_auto_item_scan = "send_notify_auto_item_scan";
    
    /**
     * Email send when customer send book/contact request
     * @var unknown_type
     */
    const send_booking_request = "send_booking_request";

    /**
     * ---------------------------------User Paging Key--------------------------------------
     */
    const USER_PAGING_SETTING = 'paging_setting';
    const SESSION_PAGING_SETTING = "session_paging_setting";
    const USER_HIDE_PANES_LAYOUT = 'hide_panes_layout';
    const LOCATION_USER_SELECTION_SETTING_TYPE = "location_selection";
    const MONEY_UNIT = "€";
    /**
     * --------------------------------- Invoice PDF output --------------------------------------
     */
    const DATEFORMAT_OUTPUT_PDF = "d.m.Y";
    const SESSION_TREE_NODE_ID = "SESSION_TREE_NODE_ID";
    const EXTERNAL_PAYMENT = 'external';
    const AUTO_PAYMENT = 'auto';
    const MANUAL_PAYMENT = 'manual';
    const AUTO_INACTIVE_TYPE = 'auto';
    const MANUAL_INACTIVE_TYPE = 'manual';
    
    /**
     * --------------------------------- USER TYPE --------------------------------------
     */
    const INSTANCE_ADMIN = "1";
    const WORKER_ADMIN = "2";
    const PARTNER_ADMIN = "3";
    const LOCATION_ADMIN = "4";

    /**
     * --------------------------------- PRICING --------------------------------------
     */
    // default template for invoice.
    const DEfAULT_PRICING_MODEL_INVOICE = "1";

    // clone data value of pricing model for new template
    const DEfAULT_PRICING_MODEL_TEMPLATE = "0";

    /**
     * --------------------------------- VAT --------------------------------------
     */
    const VAT_PRODUCT_DIGITAL_GOOD = 'digital goods';
    const VAT_PRODUCT_LOCAL_SERVICE = 'local service';
    const VAT_PRODUCT_SHIPPING = 'shipping';
    const CUSTOMER_TYPE_PRIVATE = 'private';
    const CUSTOMER_TYPE_ENTERPRISE = 'enterprise';
    const GERMANY_COUNTRY_ID = 282;
    const VAT_LOCAL_SERVICE_LABEL = "Local service";
    const VAT_SHIPPING_LABEL = "Shipping";
    const VAT_DIGITAL_GOOD_LABEL = "Digital good";

    const PARTNER_LOCATION_TYPE = "0";
    const PARTNER_MARKETING_TYPE = "1";
    const PARTNER_SERVICE_TYPE = "2";
    
    /**
     * --------------------------------- GROUP NORMAL USER----------------------------
     */
    const GROUP_CUSTOMER_ROLE_KEY = "GROUP_CUSTOMER_ROLE_KEY";
    const GROUP_CUSTOMER_LISTUSER_KEY = "GROUP_CUSTOMER_LISTUSER_KEY";
    const GROUP_CUSTOMER_PRIMARY = "1";
    const GROUP_CUSTOMER_ADMIN = "1";
    const GROUP_CUSTOMER_USER = "0";
    

    /**
     * --------------------------------- GROUP ADMIN USER----------------------------
     */
    const GROUP_SUPER_ADMIN = "0";
    const GROUP_ADMIN = "1";
    const GROUP_WORKER = "2";
    const GROUP_LOCATION_ADMIN = "4";
    const GROUP_SERVICE_PARTNER_ADMIN = "5";

    /**
     * --------------------------------- COUNTRY RISK CLASS--------------------------
     */
    const COUNTRY_LOW_RISK_CLASS = "1";
    const COUNTRY_MEDIUM_RISK_CLASS = "2";
    const COUNTRY_HIGH_RISK_CLASS = "3";
    const COUNTRY_NO_SERVICE_CLASS = "4";

    /*
     * ---------------------------------------------------------------------------------------
     * Credit Card charge status
     *
     * Every charge to the credit card (no matter if automatic or through customer or admin manually)
     * (+) if charge is approved, set value to "OK"
     * (+) if charge does not go through for any reason, set value to "FAIL"
     * (+) if card has not yet been tried, set value to "N.A."
     * ---------------------------------------------------------------------------------------
     */
    const CARD_CHARGE_NA = '0';
    const CARD_CHARGE_OK = '1';
    const CARD_CHARGE_FAIL = '2';

    /**
     * Decimal Separators
     *
     * Allows the customer to select the character of decimal point to show price values in his expected way: ',' or '.'
     */
    const DECIMAL_SEPARATOR_COMMA = ','; // example: 1.854,23
    const DECIMAL_SEPARATOR_DOT = '.'; // example: 1,854.23

    /**
     * --------------------------------- API Status-----------------------------------------
     */
    const API_RETURN_SUCCESS = '0000';
    const API_RETURN_ERROR = '9999';

    /**
     * --------------------------------- Created & Last Modified by Type -----------------------------------------
     */
    const CREATED_BY_ADMIN = 0;
    const CREATED_BY_CUSTOMER = 1;
    const CREATED_BY_CRON_JOB = 2;
    const CREATED_BY_WEB_SERVICE = 3;
    const LAST_MODIFIED_BY_ADMIN = 0;
    const LAST_MODIFIED_BY_CUSTOMER = 1;
    const LAST_MODIFIED_BY_CRON_JOB = 2;
    const LAST_MODIFIED_BY_WEB_SERVICE = 3;

    /**
     * --------------------------------- Mobile platform -----------------------------------------
     */
    const MOBILE_PLATFORM_ANDROID = 'android';
    const MOBILE_PLATFORM_IOS = 'ios';

    /**
     * --------------------------------- PUSH Notification Event --------------------------------
     */
    const PUSH_MESSAGE_INCOMMING_TYPE = 'incomming';
    const PUSH_MESSAGE_SCANS_TYPE = 'scans';

    /**
     * --------------------------------- Verified Status ----------------------------------------
     */
    const VERIFIED_STATUS_COMPLETED = 'Completed';
    const VERIFIED_STATUS_INCOMPLETE = 'Incomplete';
    const VERIFIED_STATUS_NOT_REQUIRED = 'None';
    
    /**
     * --------------------------------- Shipping Service Type ----------------------------------------
     */
    const SHIPPING_SERVICE_NORMAL = 'normal';
    const SHIPPING_SERVICE_FEDEX = 'fedex';
    const FEDEX_DEFAULT_LOGO_PATH = 'system/virtualpost/themes/new_user2/images/fedex.png';
    
    /**
     * --------------------------------- Shipping Type ----------------------------------------
     */
    const SHIPPING_TYPE_DIRECT = 'direct';
    const SHIPPING_TYPE_COLLECT = 'collect';
    
    /**
     * --------------------------------- Trigger Type ----------------------------------------
     */
    const TRIGGER_ACTION_TYPE_SYSTEM = 'system';
    const TRIGGER_ACTION_TYPE_CUSTOMER = 'customer';
    
    /**
     * --------------------------------- DATE TIME ----------------------------------------
     */
    const DATE_TIME = 'd.m.Y - H:i';
    const DATEFORMAT_DEFAULT = 'd/m/Y';
    const DATEFORMAT_01 = 'm/Y';
    const DATEFORMAT_02 = 'Y';
    const DATEFORMAT_03 = 'Ym';
    const DATEFORMAT_04 = 'mm/dd/yy';
    const DATEFORMAT_05 = 'dd/mm/yy';
    const DATE_TIME_06 = 'd.m.Y';
    const DATE_TIME_YYYYMMDD = 'Ymd';
    const TIMEFORMAT_OUTPUT01 = ' - H:i';
    const TIMEFORMAT_OUTPUT02 = ' H:i:s';
    /**
     * --------------------------------- Profile user ----------------------------------------
     */
    const MONEY_SHORT = 'EUR';
    const WEIGH_UNIT = 'g';
    const LENGTH_UNIT = 'cm';
    const CURRENCY_RATE = 1;
    const WEIGH_POUND_NUMBER = 0.035;
    const LENGTH_INCH_NUMBER = 0.3937;
    
    const MONEY_DOT_SEPARATOR_FORMAT = "000.000.000.000,00";
    const MONEY_COMMAN_SEPARATOR_FORMAT = "000,000,000,000.00";
    
    const SUGGESTION_SUITE_POSTBOX_NAME_PREFIX = "_suite";
    
    
    /**
     * ---------------------------------------- CASE VERIFICCATION------------------------------------
     */
    const CASE_COMPLETED_STATUS = "2";
    
     /**
     * ----------------------------- DEFAULT SHIPPING SERVICE TEMPLATE -------------------------------
     */
    const SHIPPING_SERVICE_TEMPLATE_DEFAULT = "1";
    const SHIPPING_SERVICE_TEMPLATE_FEDEX = "2";
    const SHIPPING_SERVICE_TEMPLATE_DPB = "3";
    
    /**
     * ----------------------------- DEFAULT SHIPPING SERVICE -------------------------------
     */
    const SHIPPING_SERVICE_TYPE_BOTH = "0";
    const SHIPPING_SERVICE_TYPE_NATIONAL = "1";
    const SHIPPING_SERVICE_TYPE_INTERNATIONAL = "2";
    
    /**
     * ----------------------------- DEFAULT CARRIER CODE -------------------------------
     */
    const FEDEX_CARRIER = "FDX";
    const CANADAPOST_CARRIER = "CP";
    const SHIPPO_CARRIER = "SP";
    
    /**
     * ----------------------------- DEFAULT SHIPPING PACKAGING TYPE--------------------------
     */
    // 1:normal letters and packages
    const SHIPPING_PACKAGING_TYPE_1 = "1";
    // 2:only parcels and express envelopes
    const SHIPPING_PACKAGING_TYPE_2 = "2";
    // 3:only letters
    const SHIPPING_PACKAGING_TYPE_3 = "3";
    
    /**
     * ----------------------------- DEFAULT ENVELOPE TYPE--------------------------
     */
    // Letter
    const ENVELOPE_TYPE_LETTER = "Letter";
    // Package
    const ENVELOPE_TYPE_PACKAGE = "Package";
    
    /**
     * ----------------------------- Cases verification activity history--------------------------
     */
    
    const CASE_ACTIVITY_CREATED = 0;
    const CASE_ACTIVITY_REJECT = 1;
    const CASE_ACTIVITY_COMPLETED = 2;
    
    /**
     * ----------------------------- Postbox activity history--------------------------
     */
    // Created == 1
    const POSTBOX_CREATE = '1';
    // Downgrade ordered == 2
    const POSTBOX_DOWNGRADE_ORDER = '2';
    // Upgrade ordered == 3
    const POSTBOX_UPGRADE_ORDER = '3';
    // Downgraded == 4
    const POSTBOX_DOWNGRADE = '4';
    // Upgraded == 5
    const POSTBOX_UPGRADE = '5';
    // Delete ordered by customer == 6
    const POSTBOX_DELETE_ORDER_BY_CUSTOMER = '6';
    // Delete ordered by system == 7
    const POSTBOX_DELETE_ORDER_BY_SYSTEM = '7';
    // Deleted == 8
    const POSTBOX_DELETE = '8';
    // Deactivated == 9
    const POSTBOX_DEACTIVATED = '9';
    // Reactivated == 10
    const POSTBOX_REACTIVATED = '10';
    // Action 
    const INSERT_POSTBOX = 'Insert';
    const UPDATE_POSTBOX = 'Update';

	/**
     * --------------------------------- Customer Type ----------------------------------------
     */
    const NORMAL_CUSTOMER = 4;
    const ENTERPRISE_CUSTOMER = 5;
    
    
    /**
     * --------------------------------- ENTERPRISE CUSTOMER PROPERTIES ----------------------------------------
     */
    const CUSTOMER_NEW_VAT_KEY = "CUSTOMER_NEW_VAT_KEY";
    
    const CUSTOMER_SUPPORT_EMAIL_KEY = "CUSTOMER_SUPPORT_EMAIL_KEY";
    const CUSTOMER_SUPPORT_PHONE_KEY = "CUSTOMER_SUPPORT_PHONE_KEY";
    
    const CUSTOMER_OWN_DOMAIN_KEY = "CUSTOMER_OWN_DOMAIN_KEY";
    const CUSTOMER_OWN_DOMAIN_LOGIN_KEY = "CUSTOMER_OWN_DOMAIN_LOGIN_KEY";
    
    const CLEVVERMAIL_PRODUCT = 1;
    const CLEVVERPHONE_PRODUCT = 2;
    const CLEVVERCOMPANY_PRODUCT = 3;
    const CLEVVERBANK_PRODUCT = 4;
    
    const activate_shipping_address_completed = "shipping_address_completed";
    const activate_invoicing_address_completed = "invoicing_address_completed";
    const activate_postbox_name_flag = "postbox_name_flag";
    const activate_name_comp_address_flag = "name_comp_address_flag";
    const activate_city_address_flag = "city_address_flag";
    const activate_payment_detail_flag = "payment_detail_flag";
    const activate_email_confirm_flag = "email_confirm_flag";
    const activate_accept_terms_condition_flag = "accept_terms_condition_flag";
    const SELECTION_CLEVVER_PRODUCT = "SELECTION_CLEVVER_PRODUCT";
    const activate_add_phone_number = "activate_add_phone_number";
    const activate_10_postbox_enterprise_customer = "activate_10_postbox_enterprise_customer";
    
    const CUSTOMER_AUTOMATIC_CHARGE_SETTING = "CUSTOMER_AUTOMATIC_CHARGE_SETTING";
    const CUSTOMER_TERM_CONDITION_SETTING = "CUSTOMER_TERM_CONDITION_SETTING";
    const CUSTOMER_API_ACCESS_SETTING = "CUSTOMER_API_ACCESS_SETTING";
    const NEW_REGISTRATION_TOKEN_ENTERPRISE_ACCOUNT = "NEW_REGISTRATION_TOKEN_ENTERPRISE_ACCOUNT";
    const CUSTOMER_PRICING_TYPE = "CUSTOMER_PRICING_TYPE";
    
    const CUSTOMER_PRICING_TYPE_NORMAL = "Normal";
    const CUSTOMER_PRICING_TYPE_SPECIAL = "Special";
    
    const NEW_USER_POSTBOX_ENTERPRISE = "NEW_USER_POSTBOX_ENTERPRISE";
    
    /**
     * --------------------------------- verification product type ----------------------------------------
     */
    const CASE_PRODUCT_TYPE_POSTBOX = 'postbox';
    const CASE_PRODUCT_TYPE_ADDRESS = 'invoice_address';
    const CASE_PRODUCT_TYPE_PHONE = 'phone';
    
    const CUSTOMER_TYPE_COMPANY = 'Company';
    const CUSTOMER_TYPE_EMPLOYEE = 'Employee';
    const CUSTOMER_TYPE_INDIVIDUAL = 'Individual';
    
    
    /**
     * ----------------------------- SCAN TYPE--------------------------
     */
    // ENVELOPE'S SCAN TYPE
    const ENVELOPE_SCAN_TYPE = "envelope";
    // ITEM'S SCAN TYPE
    const ITEM_SCAN_TYPE = "item";
    
    /**
     * ----------------------------- ACTIVITY --------------------------
     */
    // envelope_scan_activity
    const ENVELOPE_SCAN_ACTIVITY = "envelope_scan_activity";
    
    // item_scan_activity
    const ITEM_SCAN_ACTIVITY = "item_scan_activity";
    
    // direct_shipping_activity
    const DIRECT_SHIPPING_ACTIVITY = "direct_shipping_activity";
    
    // collect_shipping_activity
    const COLLECT_SHIPPING_ACTIVITY = "collect_shipping_activity";
    
    /**
     * ----------------------------- ACCOUNT HISTORY LIST --------------------------
     */
    const ACCOUNT_STATUS_ACTIVATED = "Activated";
    const ACCOUNT_STATUS_NEVER_ACTIVATED = "Never Activated";
    const ACCOUNT_STATUS_DEACTIVATED = "Deactivated";
    const ACCOUNT_STATUS_AUTO_DEACTIVATED = "Auto Deactivated";
    const ACCOUNT_STATUS_MANUAL_DEACTIVATED = "Manual Deactivated";
    
     /**
     * ----------------------------- ACTIVITY TRIGGER BY TYPE --------------------------
     */
    const TRIGGER_BY_SYSTEM = 0;
    const TRIGGER_BY_ADMIN = 1;
    const TRIGGER_BY_CUSTOMER = 2;
    
     /**
     * ----------------------------- SHIPPING TYPE --------------------------
     */
    const DIRECT_FORWARDING = 1;
    const COLLECT_FORWARDING = 2;
    
    /**
     * ----------------------------- ITEM ACTIVITY FLAG --------------------------
     */
    const ACTIVITY_REQUEST_FLAG = 0;
    const ACTIVITY_COMPLETED_FLAG = 1;
    const ACTIVITY_NEED_PREPAYMENT_FLAG = 2;


    /**
     * ----------------------------- CUSTOMER HISTORY --------------------------
     */
    const CUSTOMER_HISTORY_ACTIVITY_CHANGE_PASSWORD = 'lib_APConstant_Activity_ChangePassword';
    const CUSTOMER_HISTORY_ACTIVITY_CHANGE_EMAIL = 'lib_APConstant_Activity_ChangeEmail';
    const CUSTOMER_HISTORY_ACTIVITY_CHANGE_STATUS = 'lib_APConstant_Activity_ChangeStatus';
    const CUSTOMER_HISTORY_ACTIVITY_CHANGE_PAYMENT_METHOD = 'lib_APConstant_Activity_ChangePaymentMethod';
    const CUSTOMER_HISTORY_ACTIVITY_CHANGE_PRIMARY_CREDITCARD = 'lib_APConstant_Activity_ChangePrimaryCreditCard';
    const CUSTOMER_HISTORY_ACTIVITY_ADD_CREDITCARD = 'lib_APConstant_Activity_AddCreditCard';
    const CUSTOMER_HISTORY_ACTIVITY_REMOVE_CREDITCARD = 'lib_APConstant_Activity_RemoveCreditCard';
    const CUSTOMER_HISTORY_ACTIVITY_UPDATE_VAT_NUMBER = 'lib_APConstant_Activity_UpdateVATNumber';
    const CUSTOMER_HISTORY_ACTIVITY_REMOVE_VAT_NUMBER = 'lib_APConstant_Activity_RemoveVATNumber';
    const CUSTOMER_HISTORY_ACTIVITY_DELETE = 'lib_APConstant_Activity_Delete';
    const CUSTOMER_HISTORY_ACTIVITY_CREATE = 'lib_APConstant_Activity_Create';
    const CUSTOMER_HISTORY_ACTIVITY_UNDEFINED = 'lib_APConstant_Activity_Undefined';

    const CUSTOMER_HISTORY_STATUS_DELETED = "Deleted";
    const CUSTOMER_HISTORY_STATUS_ACTIVATED = "Activated";
    const CUSTOMER_HISTORY_STATUS_NEVER_ACTIVATED = "Never Activated";
    const CUSTOMER_HISTORY_STATUS_AUTO_DEACTIVATED = "Auto Deactivated";
    const CUSTOMER_HISTORY_STATUS_MANUAL_DEACTIVATED = "Manual Deactivated";

    const CUSTOMER_HISTORY_CREATED_BY_SYSTEM = -1;
    const CUSTOMER_HISTORY_CREATED_BY_CUSTOMER = 0;

    const CUSTOMER_HISTORY_PAYMENT_METHOD_CREDIT_CARD = "Credit Card";
    const CUSTOMER_HISTORY_PAYMENT_METHOD_INVOICE = "Invoice";
}