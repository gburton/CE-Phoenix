<?php

/*
  MailBeez Automatic Trigger Email Campaigns
  http://www.mailbeez.com

  Copyright (c) 2010, 2011 MailBeez

  inspired and in parts based on
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
 */

$promotion_url = 'http://www.mailbeez.com/trustpilot/' . MH_LINKID_1;

global $module_directory_current_ws;

if (!defined('MAILBEEZ_TRUSTPILOT_TRIGGER_EMAIL') || (defined('MAILBEEZ_TRUSTPILOT_TRIGGER_EMAIL') && MAILBEEZ_TRUSTPILOT_TRIGGER_EMAIL == 'yourid@trustpilotservice.com')) {
  $promotion = '<div class="pro">Save up to 20% with the <a href="' . $promotion_url . '" target="_blank">MailBeez discount</a> for Trustpilot</div><br /><link rel="stylesheet" type="text/css" href="' . $module_directory_current_ws . 'trustpilot/flags.css" ><div align="center" style="border: 1px solid #909090; "><div style="background-color: #232323; color: #c0c0c0";><br />
<b>What is your Reputation?</b><br />
Click on the flag of your country <br />
to visit your local Trustpilot-site:<br />
<div style="margin:auto; width: 260px; background-color: #232323; padding-top: 2px; margin-top: 5px;" >
<a class="ceebox" href="http://www.trustpilot.com"><span class="americanflag"></span></a>
<a class="ceebox" href="http://www.trustpilot.co.uk"><span class="britishflag"></span></a>
<a class="ceebox" href="http://www.trustpilot.de"><span class="germanflag"></span></a>
<a class="ceebox" href="http://www.trustpilot.at"><span class="austrianflag"></span></a>
<a class="ceebox" href="http://www.trustpilot.fr"><span class="frenchflag"></span></a>
<a class="ceebox" href="http://www.trustpilot.be"><span class="belgianflag"></span></a>
<a class="ceebox" href="http://www.trustpilot.nl"><span class="dutchflag"></span></a>
<a class="ceebox" href="http://www.trustpilot.es"><span class="spanishflag"></span></a>
<a class="ceebox" href="http://www.trustpilot.it"><span class="italianflag"></span></a>
<a class="ceebox" href="http://www.trustpilot.ru"><span class="russianflag"></span></a>
<a class="ceebox" href="http://www.trustpilot.ro"><span class="romanianflag"></span></a>
<a class="ceebox" href="http://www.trustpilot.se"><span class="swedishflag"></span></a>
<a class="ceebox" href="http://www.trustpilot.dk"><span class="danishflag"></span></a>
<a class="ceebox" href="http://www.trustpilot.no"><span class="norwegianflag"></span></a><br clear="left">
</div>
<a href="' . $promotion_url . '" target="_blank">' . mh_image($module_directory_current_ws . 'trustpilot/trustpilot.png', '', '', '', 'vspace="5"') . '</a>
<br />
All Trustpilot ratings are international visible across all local Trustpilot sites.
<br />
<br />
</div>
' . mh_image($module_directory_current_ws . 'trustpilot/line.png', '', '', '', 'width="100%" height="11"') . '
<div style="background-color: #ffffff";>
<div style="padding: 7px;">
	<b>MailBeez is a Trustpilot-Partner</b><br />
	<a href="' . $promotion_url . '" target="_blank"><u>Register here for<br>
	a free month and up to <b>20% discount</b></u><br />
	' . mh_image($module_directory_current_ws . 'trustpilot/promotion.png', '', '', '', 'vspace="10"') . '</a><br />
	with registration through this link<br>
	you support the development of MailBeez
		</div>
	</div>
</div>
	<div style="border: 1px solid red; padding:5px; text-align: left;	background-color: #F7F9FA;">
	<ol style="padding: 10px; padding-left:30px;padding-right:30px;margin: 0px;">
	 <li>Install this MailBeez Module<br>
	 <li><a href="' . $promotion_url . '" target="_blank"><u>Register for a Trustpilot Partnership<br>
	 with up to 20% discount</u></a><br />
	 <li>Enter your personal Trustpilot Email
	</ol>
	Now you are ready to collect buyers ratings from your customers and display them on your site - find the widget on your Trustpilot Site
</div>


<br />(this box will disappear after you entered your trustpilot email address)';
} else {
  $promotion = '';
}
?>