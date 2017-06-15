<?php
define ( 'ADWORDS_VERSION', 'v201506' );

// Include the AdWordsUser.
ini_set ( 'include_path', implode ( array (
                ini_get ( 'include_path' ),
                PATH_SEPARATOR,
                dirname ( __FILE__ ) 
) ) );
// Get the services, which loads the required classes.
require_once dirname ( __FILE__ ) . '/Google/Api/Ads/AdWords/Lib/AdWordsUser.php';

/**
 * GoogleAdwards class.
 */
class GoogleAdwards {
    var $version = "1.0";
    
    /**
     * Declare offline verserion name
     *
     * @var String
     */
    const OfflineConversionName = "OfflineConversion75";
    
    /**
     * the client customer ID to make the request against
     *
     * @var String
     */
    // const CutomerClientID = '797-039-3067';
    const CutomerClientID = '907-636-6167';
    
    /**
     * RedirectUri
     *
     * @var unknown
     */
    // const RedirectUri = 'http://localhost/virtualpost/google/oAuth2Callback';
    const RedirectUri = 'https://www.clevvermail.com';
    
    /**
     * UserAgent
     *
     * @var unknown
     */
    const UserAgent = 'ClevverMail';
    
    /**
     * User login.
     *
     * @var AdWordsUser
     */
    private static $user;
    
    /**
     * Connect to the Google API for a given list.
     *
     * @param string $apikey
     *            Your MailChimp apikey
     * @param string $secure
     *            Whether or not this should use a secure connection
     */
    function __construct() {
    }
    
    /**
     * Send offline versersion feed.
     * https://accounts.google.com/o/oauth2/auth?client_id=393652284525-9jg9rkiab3afb4k4js9id7l0f76o514k.apps.googleusercontent.com&response_type=code&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fadwords&redirect_uri=https://www.clevvermail.com&access_type=offline&approval_prompt=auto&state=profile
     *
     * @param
     *            Multidimensional arrays(gclid, value, date) $conversions
     */
    public static function sendOfflineConversionFeed($conversions) {
        $oauth2Info = array (
                        'client_id' => Settings::get ( APConstants::GOOGLE_ADWORD_CLIENT_ID ),
                        'client_secret' => Settings::get ( APConstants::GOOGLE_ADWORD_CLIENT_SECRET ),
                        'access_token' => Settings::get ( APConstants::GOOGLE_ADWORD_ACCESS_TOKEN ),
                        'refresh_token' => Settings::get ( APConstants::GOOGLE_ADWORD_REFRESH_TOKEN ) 
        );
        
        // $oauth2Info['expires_in'] = $oauth->expires_in;
        $user = new AdWordsUser ( null, Settings::get ( APConstants::GOOGLE_ADWORD_API_KEY ), GoogleAdwards::UserAgent, GoogleAdwards::CutomerClientID, null, $oauth2Info );
        
        // Check Access token key valid
        $OAuth2Handler = $user->GetOAuth2Handler ();
        if ($OAuth2Handler->IsAccessTokenValid ( $oauth2Info )) {
            $user->SetOAuth2Info ( $OAuth2Handler->RefreshAccessToken ( $oauth2Info ) );
            // return;
        }
        
        // Check Access token key expired
        if ($OAuth2Handler->IsAccessTokenExpiring ( $oauth2Info )) {
            $user->SetOAuth2Info ( $OAuth2Handler->RefreshAccessToken ( $oauth2Info ) );
        }
        
        GoogleAdwards::UploadOfflineConversions ( $user, GoogleAdwards::OfflineConversionName, $conversions );
    }
    
    /**
     * Upload Offline Conversions.
     *
     * @param AdWordsUser $user            
     * @param string $conversionName            
     * @param
     *            Multidimensional arrays[gclid, value, date(Ymd His)]
     *            $conversions
     */
    public static function UploadOfflineConversions(AdWordsUser $user, $conversionName, $conversions) {
        log_message ( APConstants::LOG_INFOR, '---------------------- UploadOfflineConversionsExample START ----------------------' );
        /**
         * 1.Creating a new conversions (only run once)
         */
        log_message ( APConstants::LOG_INFOR, '1. Creating a new conversions (only run once)' );
        $conversionTrackerService = $user->GetService ( 'ConversionTrackerService', ADWORDS_VERSION );
        
        // Create an upload conversion. Once created, this entry will be visible
        // under Tools and Analysis->Conversion and will have Source = "Import".
        $uploadConversion = new UploadConversion ();
        // chosen from DEFAULT, PAGE_VIEW, PURCHASE, SIGNUP, LEAD, or
        // REMARKETING
        $uploadConversion->category = 'LEAD';
        $uploadConversion->name = $conversionName;
        // integer between 1 and 30
        $uploadConversion->viewthroughLookbackWindow = 30;
        // integer between 7 and 90
        $uploadConversion->ctcLookbackWindow = 90;
        
        $uploadConversionOperation = new ConversionTrackerOperation ();
        $uploadConversionOperation->operator = 'ADD';
        $uploadConversionOperation->operand = $uploadConversion;
        
        $uploadConversionOperations = array (
                        $uploadConversionOperation 
        );
        
        // check conversionName is duplicate
        if ($conversionTrackerService->query ( "SELECT Id, Name
                                        WHERE Name = '" . $conversionName . "'
                                        ORDER BY Name
                                        DESC LIMIT 0,50" )->totalNumEntries < 1) {
            
            $result = $conversionTrackerService->mutate ( $uploadConversionOperations );
            
            $uploadConversion = $result->value [0];
            log_message ( 'info', sprintf ( "New upload conversion type with name = '%s' and ID = %d was" . "created.\n", $uploadConversion->name, $uploadConversion->id ), FALSE );
        }
        
        /**
         * 2.Your list of clicks and conversions from your database
         */
        log_message ( APConstants::LOG_INFOR, '2. Your list of clicks and conversions from your database' );
        // Get the services, which loads the required classes.
        $customerGoogle = $user->GetService ( 'CustomerService', ADWORDS_VERSION )->get ();
        $currencyCode = $customerGoogle->currencyCode;
        $dateTimeZone = $customerGoogle->dateTimeZone;
        
        // Get the services, which loads the required classes.
        $offlineConversionService = $user->GetService ( 'OfflineConversionFeedService', ADWORDS_VERSION );
        
        $operations = array ();
        foreach ( $conversions as $conversion ) {
            $offlineConversion = new OfflineConversionFeed ();
            $offlineConversion->conversionName = $conversionName;
            $offlineConversion->conversionTime = date ( "Ymd His", $conversion ["date"] ) . ' ' . $dateTimeZone;
            $offlineConversion->conversionValue = isset ( $conversion ["value"] ) ? $conversion ["value"] : 0;
            $offlineConversion->googleClickId = $conversion ["gclid"];
            $operations [] = new OfflineConversionFeedOperation ( $offlineConversion, "ADD" );
        }
        
        // send google adwords
        // try {
        $result = $offlineConversionService->mutate ( $operations );
        
        $feed = $result->value [0];
        log_message ( 'info', sprintf ( 'Uploaded offline conversion value of %d for Google Click ID = ' . "'%s' to '%s'.", $feed->conversionValue, $feed->googleClickId, $feed->conversionName ), FALSE );
        // } catch (Exception $e) {
        // log_message('error', $e->getMessage(), FALSE);
        // }
        log_message ( APConstants::LOG_INFOR, '---------------------- UploadOfflineConversionsExample END ----------------------' );
    }
    
    /**
     * Create Authorization Url.
     *
     * @return string
     */
    function createAuthorizationUrl() {
        $apiKey = Settings::get ( APConstants::GOOGLE_ADWORD_API_KEY );
        $clientId = Settings::get ( APConstants::GOOGLE_ADWORD_CLIENT_ID );
        $clientSecret = Settings::get ( APConstants::GOOGLE_ADWORD_CLIENT_SECRET );
        
        $oauth2Info = array (
                        "client_id" => $clientId,
                        "client_secret" => $clientSecret 
        );
        
        $user = new AdWordsUser ( null, $apiKey, GoogleAdwards::UserAgent, null, null, $oauth2Info );
        $offline = true;
        
        // Get the authorization URL for the OAuth2 token.
        $OAuth2Handler = $user->GetOAuth2Handler ();
        
        return $OAuth2Handler->GetAuthorizationUrl ( $user->GetOAuth2Info (), GoogleAdwards::RedirectUri, $offline );
    }
    
    /**
     * Only user one time
     *
     * @param unknown_type $grantCode            
     * @return unknown
     */
    function get_oauth2_token($authorizationCode) {
        log_message ( APConstants::LOG_INFOR, '---------------------- get_oauth2_token START ----------------------' );
        $oauth2Info = array (
                        'client_id' => Settings::get ( APConstants::GOOGLE_ADWORD_CLIENT_ID ),
                        'client_secret' => Settings::get ( APConstants::GOOGLE_ADWORD_CLIENT_SECRET ) 
        );
        
        $user = new AdWordsUser ( null, Settings::get ( APConstants::GOOGLE_ADWORD_API_KEY ), GoogleAdwards::UserAgent, null, null, $oauth2Info );
        $OAuth2Handler = $user->GetOAuth2Handler ();
        
        // try {
        // Get the access token using the authorization code. Ensure you use the
        // same
        // redirect URL used when requesting authorization.
        $user->SetOAuth2Info ( $OAuth2Handler->GetAccessToken ( $user->GetOAuth2Info (), $authorizationCode, GoogleAdwards::RedirectUri ) );
        
        // The access token expires but the refresh token obtained for offline
        // use
        // doesn't, and should be stored for later use.
        $oAuth2 = $user->GetOAuth2Info ();
        log_message ( APConstants::LOG_INFOR, print_r ( $oAuth2, true ) );
        
        // Store DB refresh token
        if (isset ( $oAuth2 ['refresh_token'] )) {
            Settings::Set ( APConstants::GOOGLE_ADWORD_REFRESH_TOKEN, $oAuth2 ['refresh_token'] );
        }
        
        Settings::Set ( APConstants::GOOGLE_ADWORD_ACCESS_TOKEN, $oAuth2 ['access_token'] );
        // } catch ( Exception $e ) {
        // log_message ( 'error', $e->getMessage(), FALSE );
        // }
        log_message ( APConstants::LOG_INFOR, '---------------------- get_oauth2_token END ----------------------' );
    }
    
    /**
     * Only user one time
     *
     * @param unknown_type $grantCode            
     * @param unknown_type $grantType            
     * @return unknown
     */
    function getOauth2Token() {
        $client_id = Settings::get ( APConstants::GOOGLE_ADWORD_CLIENT_ID );
        $client_secret = Settings::get ( APConstants::GOOGLE_ADWORD_CLIENT_SECRET );
        $grantCode = '1/pqDH_FIXvOmFUTZF7dKGSuw8y8BW_qcvp0oPxA-5btM';
        $grantType = 'offline';
        
        $oauth2token_url = "https://accounts.google.com/o/oauth2/token";
        $clienttoken_post = array (
                        "client_id" => $client_id,
                        "client_secret" => $client_secret 
        );
        
        // Offline mode
        if ($grantType === "online") {
            $clienttoken_post ["code"] = $grantCode;
            $clienttoken_post ["redirect_uri"] = 'https://www.clevvermail.com';
            $clienttoken_post ["grant_type"] = "authorization_code";
        }
        
        if ($grantType === "offline") {
            $clienttoken_post ["refresh_token"] = $grantCode;
            $clienttoken_post ["grant_type"] = "refresh_token";
        }
        
        $curl = curl_init ( $oauth2token_url );
        
        curl_setopt ( $curl, CURLOPT_POST, true );
        curl_setopt ( $curl, CURLOPT_POSTFIELDS, $clienttoken_post );
        curl_setopt ( $curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY );
        curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
        
        $json_response = curl_exec ( $curl );
        curl_close ( $curl );
        
        $authObj = json_decode ( $json_response );
        // if offline access requested and granted, get refresh token
        if (isset ( $authObj->refresh_token )) {
            $refreshToken = $authObj->refresh_token;
        }
        
        $accessToken = $authObj->access_token;
        ettings::Set ( APConstants::GOOGLE_ADWORD_ACCESS_TOKEN, $accessToken );
        return $authObj;
    }
}
?>