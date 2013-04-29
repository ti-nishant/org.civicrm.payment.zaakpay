<?php

include('checksum.php');
require_once 'CRM/Core/Payment.php';

class testdrupal_payment_zaakpay extends CRM_Core_Payment {

	protected $templateDir;
	private $data;
  /**
   * We only need one instance of this object. So we use the singleton
   * pattern and cache the instance in this variable
   *
   * @var object
   * @static
   */
  static private $_singleton = null;
 
  /**
   * mode of operation: live or test
   *
   * @var object
   * @static
   */
  static protected $_mode = null;
 
  /**
   * Constructor
   *
   * @param string $mode the mode of operation: live or test
   *
   * @return void
   */
  function __construct( $mode, &$paymentProcessor ) {
  	
  	$config = CRM_Core_Config::singleton();
	$this->templateDir = $config->extensionsDir.'/testdrupal.payment.zaakpay/templates/';
    $this->_mode             = $mode;
    $this->_paymentProcessor = $paymentProcessor;
    $this->_processorName    = ts('Zaakpay Payment Processor');
  }
 
  /**
   * singleton function used to manage this object
   *
   * @param string $mode the mode of operation: live or test
   *
   * @return object
   * @static
   *
   */
  static function &singleton( $mode, &$paymentProcessor ) {
      $processorName = $paymentProcessor['name'];
      if (self::$_singleton[$processorName] === null ) {
          self::$_singleton[$processorName] = new testdrupal_payment_zaakpay( $mode, $paymentProcessor );
      }
      return self::$_singleton[$processorName];
  }
 
  /**
   * This function checks to see if we have the right config values
   *
   * @return string the error message if any
   * @public
   */
  function checkConfig( ) {
    $config = CRM_Core_Config::singleton();
 
    $error = array();
 
    if (empty($this->_paymentProcessor['user_name'])) {
      $error[] = ts('Merchant Identifier must not be empty.');
    }
    
    if(empty($this->_paymentProcessor['password'])){
    	$error[] = ts('Secret Key must not be empty.');
    }
 
 	
    if (!empty($error)) {
      return implode('<p>', $error);
    }
    else {
      return NULL;
    }
  }
 
  function doDirectPayment(&$params) {
	CRM_Core_Error::fatal(ts('This function is not implemented'));	
  }
   
  function doTransferCheckout(&$params, $component) {
    $component = strtolower($component);
    $config = CRM_Core_Config::singleton();
    
    if ($component != 'contribute' && $component != 'event') {
      CRM_Core_Error::fatal(ts('Component is invalid'));
    }
    dpm($params);
   // die;
    $email = isset($params['email-5'])?$params['email-5']:$params['email-Primary'];
    
    /* Sanitization of every data is important to calculate checksum. */
    /* Refer to zaakpay transact api documentation to see which array key means what */
    
    $this->data = array(
      'merchantIdentifier'	=>	Checksum::sanitizedParam($this->_paymentProcessor['user_name']),
      'orderId' => Checksum::sanitizedParam(substr($params['invoiceID'], 0, 15)),
      'buyerEmail'	=> Checksum::sanitizedParam($email),
      'buyerFirstName' => Checksum::sanitizedParam($params['first_name']),
      'buyerLastName'=> Checksum::sanitizedParam($params['last_name']),
      'buyerAddress' => Checksum::sanitizedParam($params['address_name-Primary']),
      'buyerCity' => Checksum::sanitizedParam($params['city-Primary']),
      'buyerState' => Checksum::sanitizedParam($params['state_province-Primary']),
      'buyerCountry' => Checksum::sanitizedParam($params['country-Primary']),
      'buyerPincode' => Checksum::sanitizedParam($params['postal_code-Primary']),
      'buyerPhoneNumber'	=> Checksum::sanitizedParam($params['phone-Primary-1']),
      'txnType'	=> Checksum::sanitizedParam(1),
      'zpPayOption'	=>	Checksum::sanitizedParam(1),
      'mode'	=>	Checksum::sanitizedParam($this->_mode == 'test' ? 0 : 1),
      'currency'	=>	Checksum::sanitizedParam('INR'), // zaakpay only supports INR
      'amount'	=>	Checksum::sanitizedParam($params['amount']*100),
      'merchantIpAddress'	=>	Checksum::sanitizedParam($this->_paymentProcessor['signature']),
      'purpose'	=> Checksum::sanitizedParam(1),
      'productDescription'	=>	Checksum::sanitizedParam($params['description']),
      'txnDate'	=>	Checksum::sanitizedParam(date('Y-n-d')),
    );
    

    /* set return url based on the component */
    
    if($component == 'contribute'){
    	$this->data['returnUrl'] = CRM_Utils_System::baseCMSURL()."civicrm/payment/ipn?processor_name=Zaakpay&md=contribute&qfKey=".$params['qfKey'].'&inId='.$params['invoiceID'];
    }else if($component == 'event'){
    	$this->data['returnUrl'] = CRM_Utils_System::baseCMSURL()."civicrm/payment/ipn?processor_name=Zaakpay&md=event&qfKey=".$params['qfKey'].'&inId='.$params['invoiceID'];
    }

	/* important because without storing session objects, 
	*  civicrm wouldnt know if the confirm page ever submitted as we are using exit at the end
	*  and it will never redirect to the thank you page, rather keeps redirecting to the confirmation page.
	*/
	
	require_once 'CRM/Core/Session.php';
    CRM_Core_Session::storeSessionObjects( );
	
    $secret = $this->_paymentProcessor['password'];

	/* calculate checksum by using the functions given in checksum.php which is provide by Zaakpay */
	
	$all = Checksum::getAllParams($this->data);

	$checksum = Checksum::calculateChecksum($secret, $all);
    $this->data['checksum'] = $checksum;
    
    
    /* includes zaakpay.tpl which posts the data to zaakpay */
    
    $template = CRM_Core_Smarty::singleton();
    $tpl = $this->templateDir.'zaakpay.tpl';
  	
  	$template->assign('data', $this->data);
  	$tpl = $template->fetch($tpl);
  	print $tpl;
  	exit;    
    
  }
  
  
  function newOrderNotify( $success, $privateData, $component, $amount, $transactionReference ) {
        $ids = $input = $params = array( );
 	
        $input['component'] = strtolower($component);
 
        $ids['contact']          = $privateData['contactID'];
        $ids['contribution']     = $privateData['contributionID'];//, 'Integer', $privateData, true );
 
        if ( $input['component'] == "event" ) {
            $ids['event']       = $privateData['eventID'];
            $ids['participant'] = $privateData['participantID'];
            $ids['membership']  = null;
        } else {
            $ids['membership'] = $privateData['membershipID'];
        }
        $ids['contributionRecur'] = $ids['contributionPage'] = null;
 
 		$baseIpn = new CRM_Core_Payment_BaseIPN();
 		
        if ( ! $baseIpn->validateData( $input, $ids, $objects ) ) {
            return false;
        }
 
        // make sure the invoice is valid and matches what we have in the contribution record
        $input['invoice']    =  $privateData['invoiceID'];
        $input['newInvoice'] =  $transactionReference;
        $contribution        =& $objects['contribution'];
        $input['trxn_id']  =    $transactionReference;
 
        if ( $contribution->invoice_id != $input['invoice'] ) {
            CRM_Core_Error::debug_log_message( "Invoice values dont match between database and IPN request" );
            echo "Failure: Invoice values dont match between database and IPN request<p>";
            return;
        }
 
        // lets replace invoice-id with Payment Processor -number because thats what is common and unique
        // in subsequent calls or notifications sent by google.
        $contribution->invoice_id = $input['newInvoice'];
 
        $input['amount'] = $amount;
 
        if ( $contribution->total_amount != $input['amount'] ) {
            CRM_Core_Error::debug_log_message( "Amount values dont match between database and IPN request" );
            echo "Failure: Amount values dont match between database and IPN request."
                        .$contribution->total_amount."/".$input['amount']."<p>";
            return;
        }
 
        require_once 'CRM/Core/Transaction.php';
        $transaction = new CRM_Core_Transaction( );
 
        // check if contribution is already completed, if so we ignore this ipn
 
        if ( $contribution->contribution_status_id == 1 ) {
            CRM_Core_Error::debug_log_message( "returning since contribution has already been handled" );
            echo "Success: Contribution has already been handled<p>";
            return true;
        } else {
            /* Since trxn_id hasn't got any use here,
             * lets make use of it by passing the eventID/membershipTypeID to next level.
             * And change trxn_id to the payment processor reference before finishing db update */
            if ( $ids['event'] ) {
                $contribution->trxn_id =
                    $ids['event']       . CRM_Core_DAO::VALUE_SEPARATOR .
                    $ids['participant'] ;
            } else {
                $contribution->trxn_id = $ids['membership'];
            }
        }
        $this->completeTransaction ( $input, $ids, $objects, $transaction);
        return true;
    }
  
  /*
  *	This is the function which handles the response 
  * when zaakpay redirects the user back to our website
  * after transaction.
  * Refer to the $this->data['returnURL'] in above function to see how the Url should be created
  */
  
  public function handlePaymentNotification() {
  
		require_once 'CRM/Utils/Array.php';

		$module = CRM_Utils_Array::value('md', $_GET);
		$qfKey = CRM_Utils_Array::value('qfKey', $_GET);
		$invoiceId = CRM_Utils_Array::value('inId', $_GET);

		switch ($module) {
		    case 'contribute':
					if($_POST['responseCode'] == 100){
						$query = "UPDATE civicrm_contribution SET trxn_id='".$_POST['orderId']."', contribution_status_id=1 where invoice_id='".$invoiceId."'";
						 CRM_Core_DAO::executeQuery($query);
						$url = CRM_Utils_System::url('civicrm/contribute/transact',
													 "_qf_ThankYou_display=1&qfKey={$qfKey}",
													  FALSE, 
													  NULL, 
													  FALSE
													);
					}else{
						CRM_Core_Session::setStatus(ts($_POST['responseDescription']), ts('Zaakpay Error:'), 'error');
						$url = CRM_Utils_System::url('civicrm/contribute/transact',
		   											 "_qf_Confirm_display=true&qfKey={$qfKey}",
		   											 FALSE, 
		   											 NULL, 
		   											 FALSE
		  											);
					}
					
		        break;
		        
		    case 'event':
		    	
					if($_POST['responseCode'] == 100){ // success code
					  	$url = CRM_Utils_System::url('civicrm/event/register',
													 "_qf_ThankYou_display=1&qfKey={$qfKey}",
						 							 FALSE, 
						 							 NULL, 
						 							 FALSE
					  								);
					  
					 }else{ // error code
					 	CRM_Core_Session::setStatus(ts($_POST['responseDescription']), ts('Zaakpay Error:'), 'error');
						$url = CRM_Utils_System::url('civicrm/event/register',
		    										 "_qf_Confirm_display=true&qfKey={$qfKey}",
		    										 FALSE,
		    										 NULL, 
		    										 FALSE
		  											);				 	
					 }
					 
		        break;
		        
		    default:
		        require_once 'CRM/Core/Error.php';
		        CRM_Core_Error::debug_log_message("Could not get module name from request url");
		        echo "Could not get module name from request url\r\n";
		}
		CRM_Utils_System::redirect($url);
		
  }	


}

