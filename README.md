osCommerce-234-bootstrap
=========================
ALL CREDIT GOES TO THE ORIGINAL AUTHORS OF THE BASE AND ADDONS
==============================================================
All of the addons are commented in every file change with the proper support URL for each!
I take no credit for any addons or developments. 


osCommerce 2.3.4 with Bootstrap, what more needs to be said.  

The ORIGINAL point was to try to keep changes to a minimum as this will allow easy porting of Addons.  
However, the idea of this fork is to create an up-to-date bootstrap osCommerce with specific addons.  
If there are enough votes in the issues section, I will try to get those installed as well.  
Addons have been specially commented so to easily find changes. All original code will remain, but they will be commented out for any changes.  
All changes cann be found by doing a search for the special commenting system of asterisks.  
__:::The current addon listing:::__  

SEO Header Tags Reloaded added -- http://addons.oscommerce.com/info/8864  
Free Product Checkout added - http://addons.oscommerce.com/info/8080  
Order Editor added - http://addons.oscommerce.com/info/7844  
Mail Manager added - http://forums.oscommerce.com/topic/397966-mail-manager-for-osc-v23/  
Free Product Checkout added - http://addons.oscommerce.com/info/8080  
Custom Default Sort Order and Type - http://forums.oscommerce.com/topic/308798-product-listing-sort-order/  
KISS Image Thumbnailer added - http://addons.oscommerce.com/info/8492  
Custom change for product attribute sort ordering added - http://forums.oscommerce.com/topic/123629-sorting-attributes/  
Manual Order Maker added - http://addons.oscommerce.com/info/8334/v,23  
Database Check Tool 1.4 added - http://addons.oscommerce.com/info/9087  
Alternative Administration System added - http://addons.oscommerce.com/info/9135  
Gergely SMTP Email Addition - http://forums.oscommerce.com/topic/94340-smtp-authentication-and-oscommerce/page-2#entry1697522  
Security Pro R11 -- http://addons.oscommerce.com/info/7708
  
This is an attempt to get a strong working osc with some addons. This is NOT an independent project. Without the help of MANY coders that have contributed to osCommerce, this would not be possible. I did not code any of the modules, addons, or base software that you see here. Much of the effort has been completed by Gary Burton from osCommerce.  
  
IF YOU HAVE CREATED A PRIVATE ADDON THAT YOU SEE LISTED HERE AND IT IS NOT AVAILABLE TO THE GENERAL PUBLIC, please list it within the issues, and I will have it removed.
  
Links and descriptions will be used for all addon changes. Please contribute if you can.

**In the words of Gary Burton:**
This will be an ongoing COMMUNITY effort.  

If you cannot code, you can still help;

Check out the demo site at http://template.me.uk/2334bs3/ - have a look around and note any areas that you feel need attention, then post your feedback at http://forums.oscommerce.com/topic/396152-bootstrap-3-in-2334-responsive-from-the-get-go/

Are you a Coder ?
Please fork this project and start coding.  Let me know your Github Project URL by posting at http://forums.oscommerce.com/topic/396152-bootstrap-3-in-2334-responsive-from-the-get-go/   

Not a Coder ...
Please support this project by giving as much feedback as you possibly can.  Or by donating beer to the coders.

How to keep a clean Master copy using Github
============================================

I have put together a couple of videos.
1.  shows how to create a new Github account and Fork this project.
2.  shows how to check for new commits to this project and pull them into your own Fork.

You can find these videos at http://forums.oscommerce.com/topic/396152-bootstrap-3-in-2334-responsive-from-the-get-go/?p=1709648


Installation
============

Install as if this is a new osCommerce installation.  Then enter the admin area and turn on 3 new Header Tag modules;

1.  colorbox
2.  datepicker
3.  grid/list view

The functionality of these have been moved to header tag modules so that the site will only load them on the pages needed rather than on all pages.  Admin > Modules > Header Tags > {install}

You also need to install other components such as the logo, breadcrumb, footer boxes, side column boxes and so on.  Admin > Modules > Boxes > {install} AND Admin > Modules > Content > {install}.  Boxes and Modules can be sorted using the sort order, lowest is displayed first.

SEO Header Tags RELOADED
========================
1. After osCommerce installation, install the custom SQL changes using phpMyAdmin from the ../catalog/SQL_changes folder
2. Go through the following language files to change any customizable text pertaining to your installation
	a. ../catalog/includes/languages/english/index.php
	b. ../catalog/includes/languages/english/specials.php
	c. ../catalog/includes/languages/english/testimonials.php
	
Free Product Checkout
=====================
1. Go into your shops admin modules >> payment , choose and install the "Free Product"  module and fill out the required info.
		-MUST: Go into your shops admin module >> paymet, set "Free Product" payment module as the first payment method for "Sort order of display". Make it 0 and the other payment method(s) higher.
2. If you want to skip the payment page then you must change the count based on how many payments you have. 
  **Edit ../catalog/checkout_payment.php around line 88:**
		  
		  // BOF skip if only 1 payment method available. IF YOU HAVE 2 PAYMENT METHODS then "set tep_count_payment_modules() == 2"
			if (tep_count_payment_modules() == 1 ) {

Change the number based on how many payments you have available (Including the Free Product Checkout payment module itself).

Manual Order Maker
==================
1. Install the appropriate SQL files within the ../catalog/SQL_changes/
				*NOTE: You can remove any SQL changes by using the appropriate uninstall sql file
2. Go to the Administration Backend and change the options for "Configuration >> Order Editor". Be sure to read **each** description accurately before changing options.

SMTP Email Configuration
========================
1. If osCommerce was already installed, and the default oscommerce installation is not ran, then import the appropriate sql file located in SQL_changes
2. Configure your SMTP via the Administration backend configuration panel

Alternative Administration System
=================================
1. If osCommerce was already installed, and the default oscommerce installation is not ran, then import the appropriate sql file located in SQL_changes
2. Configure the AAS installation via the AAS left column boxes
3. **BE SURE** that the following files are writeable by your WWW user
	../catalog/admin/ext/aas/plugins/product_images/
	../catalog/admin/ext/aas_modules/

Database Check Tool 1.8.1
=========================
If you get an "Internal Server Error" or as it is otherwise known a 500 error you can try editing the values in ../catalog/admin/database_check.php line 13.

	ini_set('max_execution_time', 300); //300 seconds = 5 minutes
	ini_set('memory_limit','128M');
	
Remember:  There ARE clickable links within the results you find from your store.  Before you
delete or change anything, you should either be 100% sure that you would like to make a change
or check each entry out before making an action.  There is plenty of information to help you
with this... so just take the extra moment(s) necessary to avoid costly errors.

KISS Image Thumbnailer
======================
By default, KISS Image Thumbnailer does not alter standard bootstrap view of products.
The function is available for use, but not implemented.
Be sure to change the KISSit Image Heigh and Width in ../catalog/includes/configure.php
The following directory(ies) needs to be writeable by the WWW user:
	../catalog/includes/modules/kiss_image_thumbnailer/thumbs/
	
Security Pro R11
================
If you would like a file to be excluded from the security cleansing, be sure to **MANUALLY** add that file in ../catalog/includes/application_top.php while uncommenting around line 65:
  // If you need to exclude a file from cleansing then you can add it like below
  //$security_pro->addExclusion( 'some_file.php' );

__Try your hardest__ not to exclude files in this manner. If they are payment or shipping files then fine .. but not for badly written contributions, in these cases the contribution should be modified so that it no longer passes bad characters.
Also never be tempted to weaken Security Pro by adding characters to the whitelist, you will restrict the scripts ability to do its job.

Custom Default Sort Order
=========================
This changes the defaulting product sorting order. It is currently sorting by ##Model Number - Descending##. To change to a different sorting option, please see the 1st response in thread http://forums.oscommerce.com/topic/308798-product-listing-sort-order/

Custom Default Attribute Sorting Option
=======================================
This changes the default attribute sorting order. It is currently sorted by ##Option Price or Value - Ascending##. To change to a different sorting option, please see the 1st response in thread http://forums.oscommerce.com/topic/123629-sorting-attributes/

Credit Class Gift Voucher
=========================
1. If osCommerce was already installed, and the default oscommerce installation is not ran, then import the appropriate sql file located in SQL_changes
2. To change the text that customers receive, alter the appropriate lines in these files:
	catalog/includes/languages/english/modules/order_total/ot_gv.php
	catalog/includes/languages/english/modules/order_total/ot_coupon.php
	catalog/includes/languages/english/gv_send.php
	catalog/includes/languages/english/gv_faq.php
	catalog/includes/languages/english/gv_redeem.php
3. For usage information, download the usage guide here -- http://addons.oscommerce.com/info/9020