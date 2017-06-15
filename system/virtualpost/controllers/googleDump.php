<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

/**
 * The admin class is basically the main controller for the backend.
 */
class GoogleDump extends MX_Controller {
	public function __construct() {
		parent::__construct ();
		$this->load->library ( 'GoogleAdwards' );
	}
	public function send() {
		GoogleAdwards::sendOfflineConversionFeed ( 'CPTJ2MikxscCFUOZGwod_DMGsQ', 1 );
	}
	public function oAuth2Callback() {
		try {
			$code = $this->input->get ( 'code' );
			if (isset ( $code )) {
				GoogleAdwards::get_oauth2_token ( $code );
				// GoogleAdwards::getOauth2Token ();
				// redirect ( $_SERVER ['PHP_SELF'], 'send' );
			} else {
				header ( "Location: " . GoogleAdwards::createAuthorizationUrl () );
			}
			echo "Done!";
		} catch ( Exception $e ) {
			log_message ( 'error', $e->getMessage (), FALSE );
			echo $e->getMessage ();
		}
	}
	public function index() {
		header ( "Location: " . GoogleAdwards::createAuthorizationUrl () );
		// $exceptionType = isset ( $e->detail->ApiExceptionFault->errors->enc_value->errorString ) ? $e->detail->ApiExceptionFault->errors->enc_value->errorString : '';
	}
}
?>