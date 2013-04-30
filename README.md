Zaakpay
=======

Zaakpay payment processor for civicrm (Under construction)

Resources:
==========

https://www.zaakpay.com/developers/guide

Installation:
============

1. Go to Administer > System Settings > Manage Extensions.
2. Enable the Zaakpay extension.

Usage Pre-requisites:
=====================

		Create a Profile with the following fields:
			-> First Name
			-> Last Name
			-> Address Name (Primary)
			-> City (Primary)
			-> State (Primary)
			-> Country (Primary)
			-> Postal Code (Primary)
			-> Phone-Phone (Primary)
			

Usage:
=====

1. Go to Administer > System Settings > Payment Processors.
2. Select Zaakpay from the list of payment processors.
3. Fill in Merchant Id and secret key provided by Zaakpay.
4. Change the website currency to INR (Zaakpay only supports INR).

	For Contributions:
	=================
		1. Go to Manage Contribution Pages > {Your contribution page} > Cofigure > Include Profiles
		2. Choose the profile that you created and save.
		3. Go to Contribution Amounts.
		4. Choose zaakpay payment processor and save. (make sure that you currency is set to INR)
		5. Check the processor by going to the test-drive mode.
		
	For Events:
	===========
		1. Go to Manage Events > {Your Event} > Configure > Online Registration
		2. Include Profile (bottom of page)  > select profile that you created > save.
		3. Go to "Fees".
		4. Choose Zaakpay payment Processor and save.(make sure that you currency is set to INR)
		5. Test the processor by going to the test-drive mode.
		
		
		

