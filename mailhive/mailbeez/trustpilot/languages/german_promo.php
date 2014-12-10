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
  $promotion = '<div class="pro">Trustpilot bis zu 20% billiger mit dem  <a href="' . $promotion_url . '" target="_blank">MailBeez discount</a></div><br />
<link rel="stylesheet" type="text/css" href="' . $module_directory_current_ws . 'trustpilot/flags.css" ><div align="center" style="border: 1px solid #909090; "><div style="background-color: #232323; color: #c0c0c0";><br />
<b>Vertrauen Kunden deinem Shop?</b><br />
Klicke auf die Flagge deines Landes, <br />
um Trustpilot.de oder Trustpilot.at zu &ouml;ffnen<br>
<div style="margin:auto; width: 40px; background-color: #232323; padding-top: 2px; margin-top: 5px;" >
<a class="ceebox" href="http://www.trustpilot.de"><span class="germanflag"></span></a>
<a class="ceebox" href="http://www.trustpilot.at"><span class="austrianflag"></span></a><br clear="left">
</div>
<a href="' . $promotion_url . '" target="_blank">' . mh_image($module_directory_current_ws . 'trustpilot/trustpilot.png', '', '', '', 'vspace="5"') . '</a><br />
Trustpilot ist international in vielen Sprachen verf&uuml;gbar.
Alle Bewertungen<br>
sind international sichtbar.<br />
<br />
</div>
' . mh_image($module_directory_current_ws . 'trustpilot/line.png', '', '', '', 'width="100%" height="11"') . '
<div style="background-color: #ffffff";>
<div style="padding: 7px;">
	<b>MailBeez ist Trustpilot-Partner</b><br />
	<a href="' . $promotion_url . '" target="_blank"><u>Jetzt mit Probe-Monat und bis zu<br>
	<b>20% Ersparnis</b> registrieren.</u><br />
	' . mh_image($module_directory_current_ws . 'trustpilot/promotion.png', '', '', '', 'vspace="10"') . '</a><br />
	mit Registrierung &uuml;ber diesen Link<br>
	unterst&uuml;tzt du die Entwicklung von MailBeez
		</div>
	</div>
</div>
	<div style="border: 1px solid red; padding:5px; text-align: left; background-color: #F7F9FA;">
	<ol style="padding: 10px; padding-left:30px;padding-right:30px;margin: 0px;">
	 <li>Installiere dieses MailBeez Modul<br>
	 <li><a href="' . $promotion_url . '" target="_blank"><u>Registere dich als Trustpilot Partner<br>
	 mit bis zu 20% Ersparnis</u></a><br />
	 <li>Gib deine pers&ouml;nliche Trustpilot Email hier im Modul ein.
	</ol>
	Jetzt bis du bereit, um Service Bewertungen deiner Kunden einzusammeln und auf deinem Shop anzuzeigen - verschiedene Trustbox Widgets (JS, RSS) findest du auf der Trustpilot Partner Seite.
</div>


<br />(Dieser Hinweis verschwindet, sobald du deine Trustpilot Email eingegeben hast.)';
} else {
  $promotion = '';
}
?>