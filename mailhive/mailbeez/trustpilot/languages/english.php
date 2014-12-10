<?php 
/*
  MailBeez Automatic Trigger Email Campaigns
  http://www.mailbeez.com

  Copyright (c) 2010, 2011 MailBeez
	
	inspired and in parts based on
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

	if (file_exists(MH_DIR_FS_CATALOG . 'mailhive/mailbeez/trustpilot/languages/' . $_SESSION['language'] . '_promo' . $file_extension)) {
		include(MH_DIR_FS_CATALOG . 'mailhive/mailbeez/trustpilot/languages/' . $_SESSION['language'] . '_promo' . $file_extension);
	}	elseif (file_exists(MH_DIR_FS_CATALOG . 'mailhive/mailbeez/trustpilot/languages/english_promo' . $file_extension)) {
		include(MH_DIR_FS_CATALOG . 'mailhive/mailbeez/trustpilot/languages/english_promo' . $file_extension);
	}

	define('MAILBEEZ_TRUSTPILOT_TEXT_TITLE',  'Trustpilot - automatic feedback service&reg;');		

	define('MAILBEEZ_TRUSTPILOT_TEXT_DESCRIPTION', '<b>Turn Visitors into Customers.</b><br />
	<br />If you do establish trust, you gain a powerful competitive advantage. According to some estimates, <b>trust is 10 times more important to consumers than cost</b>.<br />
<br />

That\'s why Buyers Rating are important to increase your conversion rate. <br />
<br />
<blockquote><a href="http://download.trustpilot.dk/B2B/TP%20Automatic%20Feedback%20Service.The%20Guide_EN.pdf"><b>Download TP Feedback Service Guide (EN).PDF</b></a></blockquote><br />

' . ( (defined('MAILBEEZ_TP_RSS_IMPORTER_STATUS')) ? '' : '<div class="pro">Integrate your valuable Trustpilot Ratings into your Storefront and MailBeez Email campaigns! 
Download the <a href="http://www.mailbeez.com/documentation/configbeez/config_trustpilot_rss_importer/' . MH_LINKID_1 . '" target="_blank">Trustpilot Integration Suite</a></div>' ) . '

<div class="tipp"><b>Add unsubscribe Link:</b><br />
	add following code to the template of your Trustpilot Automatic Feedback Service<br>
	(you will find that in the Trustpilot b2b area):
	<pre style="background-color: #fff">
  &lt;a href="[tp_custom1]"&gt;
  No more Emails from Trustpilot
  &lt;/a&gt;
</pre>
(can be in one line)</div>
' . $promotion . '<br />

	This MailBeez Module generates up to 100x more ratings on Trustpilot. It sends a trigger email to Trustpilot.<br />
	Trustpilot will then ask your customer by email to rate your service with a personalized no-sign-on rating link. <br />
	Find your personal Trustpilot email address in your Trustpilot b2b site.<br />
		<br />');

	define('MAILBEEZ_TRUSTPILOT_STATUS_TITLE', 'Send trustpilot trigger email');
	define('MAILBEEZ_TRUSTPILOT_STATUS_DESC', 'Do you want to send trustpilot trigger email to ask your customer for a review?');
	
	define('MAILBEEZ_TRUSTPILOT_ORDER_STATUS_ID_TITLE', 'Set Order Status');
	define('MAILBEEZ_TRUSTPILOT_ORDER_STATUS_ID_DESC', 'Set the status of orders to send trigger');
	
	define('MAILBEEZ_TRUSTPILOT_TRIGGER_EMAIL_TITLE', 'your trustpilot email address');
	define('MAILBEEZ_TRUSTPILOT_TRIGGER_EMAIL_DESC', 'your unique trustpilot email address');
	
	define('MAILBEEZ_TRUSTPILOT_PASSED_DAYS_TITLE', 'Minimum age of order');
	define('MAILBEEZ_TRUSTPILOT_PASSED_DAYS_DESC', 'number of days to wait befor sending the emails');	
	
	define('MAILBEEZ_TRUSTPILOT_PASSED_DAYS_SKIP_TITLE', 'Maxium age of order');
	define('MAILBEEZ_TRUSTPILOT_PASSED_DAYS_SKIP_DESC', 'number of days after which do skip the reminder');		
	
	define('MAILBEEZ_TRUSTPILOT_SENDER_TITLE', 'sender email');
	define('MAILBEEZ_TRUSTPILOT_SENDER_DESC', 'sender email');
	
	define('MAILBEEZ_TRUSTPILOT_SENDER_NAME_TITLE', 'sender name');
	define('MAILBEEZ_TRUSTPILOT_SENDER_NAME_DESC', 'sender name');
	
	define('MAILBEEZ_TRUSTPILOT_LANGUAGE_TITLE', 'Default Customer Language');
	define('MAILBEEZ_TRUSTPILOT_LANGUAGE_DESC', 'Please choose the language Trustpilot will ask your Customers.');	
	
	define('MAILBEEZ_TRUSTPILOT_SORT_ORDER_TITLE', 'Sort order of display.');
	define('MAILBEEZ_TRUSTPILOT_SORT_ORDER_DESC', 'Sort order of display. Lowest is displayed first.');	
	
 ?>