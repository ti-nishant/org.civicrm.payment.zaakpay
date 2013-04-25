Zaakpay
=======

Zaakpay payment processor for civicrm (Under construction)

Installation:
============

1. Go to Administer > System Settings > Manage Extensions.
2. Enable the Zaakpay extension.

Usage:
=====

1. Go to Administer > System Settings > Payment Processors.
2. Select Zaakpay from the list of payment processors.
3. Fill in Merchant Id and secret key provided by Zaakpay.
4. Change the website currency to INR (Zaakpay only supports INR).

	For Contributions:
	=================
		1. Create a Profile with the following fields:
			-> First Name
			-> Last Name
			-> Address Name (Primary)
			-> City (Primary)
			-> State (Primary)
			-> Country (Primary)
			-> Postal Code (Primary)
			-> Phone-Phone (Primary)
			
		2. Go to Manage Contribution Pages > {Your contribution page} > Cofigure > Include Profiles
		3. Choose the profile that you created and save.
		5. Go to Contribution Amounts.
		6. Choose zaakpay payment processor and save.
		7. Check the processor by going to the test-drive mode.
		
		

