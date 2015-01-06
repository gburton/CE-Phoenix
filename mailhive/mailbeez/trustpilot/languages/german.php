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

	define('MAILBEEZ_TRUSTPILOT_TEXT_TITLE',  'Trustpilot - Automatischer Feedback Service&reg;');		

	define('MAILBEEZ_TRUSTPILOT_TEXT_DESCRIPTION', '<b>Mache Besucher zu Kunden</b><br />
	<br />Mit einem offen sichbaren Vertrauen in deinen Shop erh&auml;lst du einen starken Wettbewerbsvorteil. Untersuchungen haben gezeigt, dass f&uuml;r Kunden das <b>Vertrauen in den Shop bis zu 10x wichtiger als der Preis</b> ist.<br />
<br />
Daher sind unabh&auml;ngige und damit glaubw&uuml;rdige Kunden-Bewertungen der wohl wichtigste Faktor zur Neu-Kundengewinnung.
<br />

	Mit diesem MailBeez Modul erh&auml;lst du bis zu 100x mehr Bewertungen auf Trustpilot.de. Das Modul sendet automatische Steuer-Emails an den automatischen Trustpilot Feedback Service&reg;. Dieser Service fordert dann deine Kunden zur Bewertung auf - mit einem personalisierten Link geht das f&uuml;r den Kunden ohne Login. Die Feedback Service&reg; Email wird von Trustpilot in deinem Namen und mit deiner Gestaltung verschickt.<br /><br />

' . ( (defined('MAILBEEZ_TP_RSS_IMPORTER_STATUS')) ? '' : '<div class="pro">Zeige deine wertvollen Trustpilot Bewertungen im Shop und in MailBeez Emails! 
Downloade die <a href="http://www.mailbeez.com/documentation/configbeez/config_trustpilot_rss_importer/' . MH_LINKID_1 .'" target="_blank">Trustpilot Integration Suite</a></div>' ) . '

<div class="tipp"><b>Unsubscribe Link - so geht\'s:</b><br />
	folgenden code dem Template des Trustpilot Automatic Feedback Service hinzuf&uuml;gen<br>
	(Bearbeitungsm&ouml;glichkeit befindet sich im Trustpilot H&auml;ndlerbereich):
	<pre style="background-color: #fff">
  &lt;a href="[tp_custom1]"&gt;
  keine weiteren Emails von Trustpilot
  &lt;/a&gt;
</pre>
(darf in einer Zeile stehen)</div>
' . $promotion . '
<br />Die Einstellungen hierf&uuml;r sowie deine pers&ouml;nliche Trustpilot Email findes du im Trustpilot b2b Bereich (Partner Login).<br />
<br />
<blockquote><a href="http://download.trustpilot.dk/B2B/TP%20Automatic%20Feedback%20Service.The%20Guide_EN.pdf"><b>Download TP Feedback Service Guide (EN).PDF</b></a></blockquote>
		<br />');

	define('MAILBEEZ_TRUSTPILOT_STATUS_TITLE', 'Nutze den automatischen Trustpilot Feedback Service&reg;');
	define('MAILBEEZ_TRUSTPILOT_STATUS_DESC', 'M&ouml;chtest du deine Kunden von Trustpilot zur Bewertung auffordern lassen?');
	
	define('MAILBEEZ_TRUSTPILOT_ORDER_STATUS_ID_TITLE', 'Bestell-Status f&uuml;r Bewertungen');
	define('MAILBEEZ_TRUSTPILOT_ORDER_STATUS_ID_DESC', 'Bestellungen mit diesem Status werde bei der Aufforderung ber&uuml;cksichtigt');
	
	define('MAILBEEZ_TRUSTPILOT_TRIGGER_EMAIL_TITLE', 'Deine Trustpilot Email Adresse');
	define('MAILBEEZ_TRUSTPILOT_TRIGGER_EMAIL_DESC', 'Deine pers&ouml;nliche Trustpilot Email Adresse findest du im Trustpilot Partnerbereich');
	
	define('MAILBEEZ_TRUSTPILOT_PASSED_DAYS_TITLE', 'Mindest-Alter der Bestellung');
	define('MAILBEEZ_TRUSTPILOT_PASSED_DAYS_DESC', 'Anzahl der Tage, nach denen eine Aufforderung verschickt wird');	
	
	define('MAILBEEZ_TRUSTPILOT_PASSED_DAYS_SKIP_TITLE', 'H&ouml;chst-Alter der Bestellung');
	define('MAILBEEZ_TRUSTPILOT_PASSED_DAYS_SKIP_DESC', '&Auml;ltere Bestellungen werden nicht ber&uuml;cksichtigt.');
	
	define('MAILBEEZ_TRUSTPILOT_SENDER_TITLE', 'Absender Email Adresse');
	define('MAILBEEZ_TRUSTPILOT_SENDER_DESC', 'Absender Email Adresse');
	
	define('MAILBEEZ_TRUSTPILOT_SENDER_NAME_TITLE', 'Absender Name');
	define('MAILBEEZ_TRUSTPILOT_SENDER_NAME_DESC', 'Absender Name');
	
	define('MAILBEEZ_TRUSTPILOT_LANGUAGE_TITLE', 'Standard Kundensprache');
	define('MAILBEEZ_TRUSTPILOT_LANGUAGE_DESC', 'Bitte w&auml;hle, in welcher Sprache Trustpilot deine Kunden kontaktiert');
	
	define('MAILBEEZ_TRUSTPILOT_SENDER_NAME_TITLE', 'Anzeige-Reihenfolge');
	define('MAILBEEZ_TRUSTPILOT_SORT_ORDER_DESC', 'Anzeige-Reihenfolge der Module');	
	
 ?>