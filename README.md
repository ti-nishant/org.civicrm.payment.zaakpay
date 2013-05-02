Zaakpay
=======
This extension doesnot support recurring payments.

IMPORTANT NOTE: Please read completed README.md to avoid functionality errors.

Zaakpay payment processor for civicrm

Resources:
==========

	.https://www.zaakpay.com/developers/guide
	.http://civicrm.org/blogs/andyw/civicrm-42-payment-extensions-framework-overview-new-features
	.http://wiki.civicrm.org/confluence/display/CRMDOC42/Create+a+Payment-Processor+Extension
	.http://wiki.civicrm.org/confluence/display/CRMDOC42/Example+of+creating+a+payment+processor+extension
	.http://forum.civicrm.org/index.php?topic=15556.5;wap2

Installation:
============

1. Go to Administer > System Settings > Manage Extensions.
2. Enable the Zaakpay extension.

Usage Pre-requisites:
=====================

		1. Create a Profile with the following fields:
			-> First Name
			-> Last Name
			-> Address Name (Primary)
			-> City (Primary)
			-> State (Primary)
			-> Country (Primary)
			-> Postal Code (Primary)
			-> Phone-Phone (Primary)
			
		2. Localization settings:
			-> Go to Administer > Localization > Languages, currencies, locations
			-> Default Currency: INR
			-> Save.
			

Usage:
=====

1. Go to Administer > System Settings > Payment Processors.
2. Select Zaakpay from the list of payment processors.
3. Fill in Merchant Id and secret key provided by Zaakpay.
4. Change the website currency to INR (Zaakpay only supports INR).

	For Contributions:
	=================
		1. Go to Manage Contribution Pages > {Your contribution page} > Configure > Include Profiles
		2. Choose the profile that you created and save.
		3. Go to Contribution Amounts.
		4. Choose zaakpay payment processor and save. (make sure that your currency is set to INR)
		5. Check the processor by going to the test-drive mode.
		
	For Events:
	===========
		1. Go to Manage Events > {Your Event} > Configure > Online Registration
		2. Include Profile (bottom of page)  > select profile that you created > save.
		3. Go to "Fees".
		4. Choose Zaakpay payment Processor and save.(make sure that your currency is set to INR)
		5. Test the processor by going to the test-drive mode.
