<?php

require_once 'CiviTest/CiviSeleniumTestCase.php';

class WebTest_Zaakpay_CiviZaakpayTest extends CiviSeleniumTestCase {

  private $merchantIdentifier = '165583e81c2f4bf1be24240f44da742c';
  private $secretKey = '63911f0898004595b079508a5f1da113';

  protected function setUp() {
    parent::setUp();
  }

  function testZaakpayInstall() { // success
    $this->open($this->sboxPath);

    $this->webtestLogin(TRUE);

    //enable extension
    $this->open($this->sboxPath . "civicrm/admin/extensions?action=add&id=org.civicrm.payment.zaakpay&key=org.civicrm.payment.zaakpay");
    $this->waitForElementPresent("_qf_Extensions_next-bottom");
    $this->click("_qf_Extensions_next-bottom");
    //$this->waitForElementPresent('abc');
    $this->waitForPageToLoad("300000");
  }

  function testAddPaymentProcessor() { //success
    $this->open($this->sboxPath);
    $this->webtestLogin(TRUE);
    $this->open($this->sboxPath . "civicrm/admin/paymentProcessor");
    $this->waitForPageToLoad("300000");
    $this->waitForElementPresent('newPaymentProcessor');
    $this->click('newPaymentProcessor');
    $this->waitForElementPresent("payment_processor_type_id");
    $this->select('payment_processor_type_id', 'label=Zaakpay (org.civicrm.payment.zaakpay)');
    $this->waitForPageToLoad('300000');
    $this->type('name', 'Zaakpay');
    $this->select('financial_account_id', 'value=12');
    $this->type('user_name', $this->merchantIdentifier);
    $this->type('password', $this->secretKey);
    $this->type('signature', '127.0.0.1');
    $this->type('test_user_name', $this->merchantIdentifier);
    $this->type('test_password', $this->secretKey);
    $this->type('test_signature', '127.0.0.1');
    $this->click('_qf_PaymentProcessor_next-bottom');
    $this->waitForPageToLoad("300000");
  }

  function testAddProfile() { //success
    $this->open($this->sboxPath);
    $this->webtestLogin(TRUE);
    $this->open($this->sboxPath . 'civicrm/admin/uf/group/add?action=add&reset=1');
    $this->waitForPageToLoad("300000");
    $this->type('title', 'zaakpay');
    $this->click('_qf_Group_next-bottom');
    $this->waitForPageToLoad('300000');

    $fields = array(
      '1' => array('Individual', 'First Name'),
      '2' => array('Individual', 'Last Name'),
      '3' => array('Contacts', 'Additional Address 1'),
      '4' => array('Contacts', 'City'),
      '5' => array('Contacts', 'State'),
      '6' => array('Contacts', 'Country'),
      '7' => array('Contacts', 'Postal Code'),
      '8' => array('Contacts', 'Phone'),
    );

    $count = 0;
    foreach ($fields as $field) {
      $count++;

      $this->select('field_name_0', 'label=' . $field[0]);
      $this->waitForElementPresent('field_name_1');
      $this->select('field_name_1', 'label=' . $field[1]);
      $this->click('is_required');
      $this->click('_qf_Field_next_new-bottom');
      if ($count != 8) {
        $this->click('_qf_Field_next_new-bottom');
      }
      else {
        $this->click('_qf_Field_next-bottom');
      }
      $this->waitForPageToLoad('300000');
    }
  }

  function testLocalizationSettings() {
    //defaultCurrency
    //currencyLimit-f
  }

  function testAddContributionPage() {
    $this->open($this->sboxPath);
    $this->webtestLogin(TRUE);
    $this->open($this->sboxPath . "civicrm/admin/contribute/add?reset=1&action=add");
    $this->waitForPageToLoad("300000");
    $this->type('title', 'Zaakpay test contribution page');
    $this->click('_qf_Settings_next-bottom');
    $this->waitForPageToLoad("300000");
    $this->waitForElementPresent('currency');
    $this->select('currency', 'value=INR');
    $this->click("xpath=//label[text()='Zaakpay']/");
    $this->type('label_1', 'option 1');
    $this->type('value_1', '100');
    $this->click("_qf_Amount_next-top");
    $this->waitForPageToLoad("300000");
    $this->waitForElementPresent("xpath=//a[@id='ui-id-6']");
    $this->click("xpath=//a[@id = 'ui-id-6']");
    $this->waitForElementPresent('custom_post_id');
    $this->select('custom_post_id', 'label=zaakpay');
    $this->click('_qf_Custom_upload_done-top');
    $this->waitForPageToLoad('300000');
  }

  function testContributionPayment() {
    $this->open($this->sboxPath);
    $this->webtestLogin(TRUE);

    $this->open($this->sboxPath . 'civicrm/admin/contribute?reset=1');
    $this->click("xpath=//table[@id='option11']/tbody/tr/td[contains(., 'Zaakpay test contribution page')]/following-sibling::td[3]/div[3]/span/ul/li[2]/a");
    $this->waitForPageToLoad('300000');
    $this->waitForElementPresent("email-5");
    //$this->click('CIVICRM_QFID_23_4');
    //$this->waitForCondition()
    $this->click("xpath=//span[@class='price-set-option-content']/label");
    $this->type('email-5', 'admin@demo.com');
    $this->type('first_name', 'Nishant');
    $this->type('last_name', 'Nishant');
    $this->type('supplemental_address_1-Primary', 'address');
    $this->type('city-Primary', 'Jaipur');
    $this->select('country-Primary', 'label=India');
    //$this->waitForElementPresent("currency");1228
    $this->select('state_province-Primary', "value=1228");
    $this->type('postal_code-Primary', '000000');
    $this->type('phone-Primary-1', '0000000000');
    $this->click('_qf_Main_upload-bottom');
    $this->waitForPageToLoad("300000");
    $this->waitForElementPresent('_qf_Confirm_next-top');
    $this->click("_qf_Confirm_next-top");
    $this->waitForPageToLoad("300000");
    
    $this->waitForElementPresent("pan");
  	
  	$this->type('pan', '4012888888881881');
  	$this->select('expiry_month', 'value=12');
  	$this->select('expiry_year', 'value=20');
  	$this->type('cvv', '123');
  	$this->click('paynow0');
  	$this->waitForPageToLoad(30000);
  }

  //create payment processor
  /* $this->open($this->sboxPath . "civicrm/admin/paymentProcessor?action=add&reset=1&pp=Zaakpay");


    $this->type("name", "Zaakpay");
    $this->type("description", "Zaakpay payment processor");

    $this->type('user_name', $this->merchantIdentifier);
    $this->type('password', $this->secretKey);

    $this->type('test_user_name', $this->merchantIdentifier);
    $this->type('test_password', $this->secretKey);

    $this->click('_qf_PaymentProcessor_next-bottom');
    $this->waitForPageToLoad("300000"); */

  //create cotribution page
  /*   * ;
    $this->waitForPageToLoad("300000");

   * */

  //assign payment processor to
  /* $this->open($this->sboxPath . "civicrm/admin/contribute/amount?action=update&reset=1&id=4");
    $this->waitForElementPresent("currency");
    $this->select("currency", "value=INR");


    $this->open($this->sboxPath . "civicrm/contribute/transact?reset=1&action=preview&id=4");
    $this->waitForElementPresent("price_6");
    //$this->click('CIVICRM_QFID_23_4');

    $this->type('price_6', "100");
    $this->type('email-5', 'admin@demo.com');
    $this->type('first_name', 'Nishant');
    $this->type('last_name', 'Nishant');
    $this->type('address_name-Primary', 'address');
    $this->type('city-Primary', 'Jaipur');
    $this->select('country-Primary', 'label=India');
    //$this->waitForElementPresent("currency");1228
    $this->select('state_province-Primary', 'value=1228');
    $this->type('postal_code-Primary', '000000');
    $this->type('phone-Primary-1', '0000000000');
    $this->click('_qf_Main_upload-bottom');
    $this->waitForPageToLoad("300000");
    $this->click("_qf_Confirm_next-top");
    $this->waitForPageToLoad("300000");

    $this->open($this->sboxPath . "civicrm/admin/paymentProcessor?reset=1");
    $this->click("xpath=//table[@class='selector']/tbody/tr/td[contains(., 'Zaakpay')]/following-sibling::td[5]/span/a[3]");
    $this->waitForPageToLoad("300000");
    $this->click('_qf_PaymentProcessor_next-top');
    $this->waitForPageToLoad("300000");

    $this->open($this->sboxPath . "civicrm/admin/extensions?action=disable&id=18&key=testdrupal.payment.zaakpay");
    $this->waitForElementPresent("_qf_Extensions_next-bottom");
    $this->click("_qf_Extensions_next-bottom");
    $this->waitForPageToLoad("300000");

    $this->open($this->sboxPath . "civicrm/admin/extensions?action=delete&id=18&key=testdrupal.payment.zaakpay");
    $this->waitForElementPresent("_qf_Extensions_next-bottom");
    $this->click("_qf_Extensions_next-bottom");
    $this->waitForPageToLoad("300000"); */


  /* $this->type("first_name", "Nishant123");
    $this->type("last_name", "Nishant456");


    $this->waitForPageToLoad("30000");

    $this->click("edit-page");

    $this->waitForPageToLoad("30000"); */
  //}
}
