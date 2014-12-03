<?php
/*
  $Id: create_order.php,v 1 2003/08/17 23:21:34 frankl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
  
*/

// pull down default text
define('PULL_DOWN_DEFAULT', 'Bitte wählen');
define('TYPE_BELOW', 'Unten eingeben');

define('JS_ERROR', 'Es sind Fehler aufgetreten!\nBitte nehmen Sie folgende Änderungen vor :\n\n');

define('JS_GENDER', '* \'Geschlecht\' muss ausgewählt sein.\n');
define('JS_FIRST_NAME', '* Der \'Vorname\' muss mindestens ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' Zeichen lang sein.\n');
define('JS_LAST_NAME', '* Der \'Nachname\' muss mindestens ' . ENTRY_LAST_NAME_MIN_LENGTH . ' Zeichen lang sein.\n');
define('JS_DOB', '* Das \'Geburtsdatum\' muss folgendes Format haben: xx/xx/xxxx (Monat/Tag/Jahr).\n');
define('JS_EMAIL_ADDRESS', '* Die \'E-Mail Adresse\' muss mindestens ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' Zeichen lang sein.\n');
define('JS_ADDRESS', '* Der \'Straßenname\' muss mindestens ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' Zeichen lang sein.\n');
define('JS_POST_CODE', '* Die \'Postleitzahl\' muss mindestens ' . ENTRY_POSTCODE_MIN_LENGTH . ' Zeichen lang sein.\n');
define('JS_CITY', '* Der \'Stadtteil\' muss mindestens ' . ENTRY_CITY_MIN_LENGTH . ' Zeichen lang sein.\n');
define('JS_STATE', '* Das \'Bundesland\' muss ausgewählt werden.\n');
define('JS_STATE_SELECT', '-- oben auswählen --');
define('JS_ZONE', '* Das \'Bundesland\' muss aus der Liste ausgewählt werden.\n');
define('JS_COUNTRY', '* Das \'Land\' muss aus der Liste ausgewählt werden.\n');
define('JS_TELEPHONE', '* Die \'Telefonnummer\' muss mindestens ' . ENTRY_TELEPHONE_MIN_LENGTH . ' Zeichen lang sein.\n');
define('JS_PASSWORD', '* Das \'Passwort\' muss mindestens ' . ENTRY_PASSWORD_MIN_LENGTH . ' Zeichen lang sein.\n');

define('CATEGORY_COMPANY', 'Firmendetails');
define('CATEGORY_PERSONAL', 'persönliche Details');
define('CATEGORY_ADDRESS', 'Adresse');
define('CATEGORY_CONTACT', 'Kontaktinformationen');
define('CATEGORY_OPTIONS', 'Optionen');
define('CATEGORY_PASSWORD', 'Passwort');
define('CATEGORY_CORRECT', 'Wenn das der gewünschte Kunde ist klicken Sie bestätigen.');
define('ENTRY_CUSTOMERS_ID', 'Kunden-ID:');
define('ENTRY_CUSTOMERS_ID_TEXT', '&nbsp;');
define('ENTRY_COMPANY', 'Firmenname:');
define('ENTRY_COMPANY_ERROR', '');
define('ENTRY_COMPANY_TEXT', '');
define('ENTRY_GENDER', 'Geschlecht:');
define('ENTRY_GENDER_FEMALE', 'weiblich:');
define('ENTRY_GENDER_MALE', 'männlich:');
define('ENTRY_GENDER_ERROR', '&nbsp;');
define('ENTRY_GENDER_TEXT', '&nbsp;');
define('ENTRY_FIRST_NAME', 'Vorname:');
define('ENTRY_FIRST_NAME_ERROR', '&nbsp;<small><font color="#FF0000">min ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' Zeichen</font></small>');
define('ENTRY_FIRST_NAME_TEXT', '&nbsp;');
define('ENTRY_LAST_NAME', 'Nachname:');
define('ENTRY_LAST_NAME_ERROR', '&nbsp;<small><font color="#FF0000">min ' . ENTRY_LAST_NAME_MIN_LENGTH . ' Zeichen</font></small>');
define('ENTRY_LAST_NAME_TEXT', '&nbsp;');
define('ENTRY_DATE_OF_BIRTH', 'Geburtsdatum:');
define('ENTRY_DATE_OF_BIRTH_ERROR', '&nbsp;<small><font color="#FF0000">(z.B. 05/21/1970|Monat/Tag/Jahr)</font></small>');
define('ENTRY_DATE_OF_BIRTH_TEXT', '&nbsp;<small>(z.B. 05/21/1970) ');
define('ENTRY_EMAIL_ADDRESS', 'E-Mail Adresse:');
define('ENTRY_EMAIL_ADDRESS_ERROR', '&nbsp;<small><font color="#FF0000">min ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' Zeichen</font></small>');
define('ENTRY_EMAIL_ADDRESS_CHECK_ERROR', '&nbsp;<small><font color="#FF0000">Die E-Mail Adresse scheint nicht gültig zu sein!</font></small>');
define('ENTRY_EMAIL_ADDRESS_ERROR_EXISTS', '&nbsp;<small><font color="#FF0000">Die E-Mail Adresse ist bereits vorhanden!</font></small>');
define('ENTRY_EMAIL_ADDRESS_TEXT', '&nbsp;');
define('ENTRY_STREET_ADDRESS', 'Straße:');
define('ENTRY_STREET_ADDRESS_ERROR', '&nbsp;<small><font color="#FF0000">min ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' Zeichen</font></small>');
define('ENTRY_STREET_ADDRESS_TEXT', '&nbsp;');
define('ENTRY_SUBURB', 'Stadtteil:');
define('ENTRY_SUBURB_ERROR', '');
define('ENTRY_SUBURB_TEXT', '');
define('ENTRY_POST_CODE', 'Postleitzahl:');
define('ENTRY_POST_CODE_ERROR', '&nbsp;<small><font color="#FF0000">min ' . ENTRY_POSTCODE_MIN_LENGTH . ' Zeichen</font></small>');
define('ENTRY_POST_CODE_TEXT', '&nbsp;');
define('ENTRY_CITY', 'Stadt:');
define('ENTRY_CITY_ERROR', '&nbsp;<small><font color="#FF0000">min ' . ENTRY_CITY_MIN_LENGTH . ' Zeichen</font></small>');
define('ENTRY_CITY_TEXT', '&nbsp;');
define('ENTRY_STATE', 'Bundesland:');
define('ENTRY_STATE_ERROR', '&nbsp;');
define('ENTRY_STATE_TEXT', '&nbsp;');
define('ENTRY_COUNTRY', 'Land:');
define('ENTRY_COUNTRY_ERROR', '');
define('ENTRY_COUNTRY_TEXT', '&nbsp;');
define('ENTRY_TELEPHONE_NUMBER', 'Telefonnummer:');
define('ENTRY_TELEPHONE_NUMBER_ERROR', '&nbsp;<small><font color="#FF0000">min ' . ENTRY_TELEPHONE_MIN_LENGTH . ' Zeichen</font></small>');
define('ENTRY_TELEPHONE_NUMBER_TEXT', '&nbsp;');
define('ENTRY_FAX_NUMBER', 'Faxnummer:');
define('ENTRY_FAX_NUMBER_ERROR', '');
define('ENTRY_FAX_NUMBER_TEXT', '');
define('ENTRY_NEWSLETTER', 'Newsletter(Neuigkeiten per Email):');
define('ENTRY_NEWSLETTER_TEXT', '');
define('ENTRY_NEWSLETTER_YES', 'abonnieren');
define('ENTRY_NEWSLETTER_NO', 'Deabonnieren');
define('ENTRY_NEWSLETTER_ERROR', '');
define('ENTRY_PASSWORD', 'Passwort:');
define('ENTRY_PASSWORD_CONFIRMATION', 'Passwort bestätigung:');
define('ENTRY_PASSWORD_CONFIRMATION_TEXT', '&nbsp;');
define('ENTRY_PASSWORD_ERROR', '&nbsp;<small><font color="#FF0000">min ' . ENTRY_PASSWORD_MIN_LENGTH . ' Zeichen</font></small>');
define('ENTRY_PASSWORD_TEXT', '&nbsp;');
define('PASSWORD_HIDDEN', '--AUSGEBLENDET--');

define('CREATE_ORDER_TEXT_EXISTING_CUST', 'bestehendes Kundenkonto');
define('CREATE_ORDER_TEXT_NEW_CUST', 'neues Kundenkonto');
define('CREATE_ORDER_TEXT_NO_CUST', 'ohne Kundenkonto');

define('HEADING_TITLE', 'neue Bestellung');
define('TEXT_SELECT_CUST', '- wählen Sie einen Kunden -'); 
define('TEXT_SELECT_CURRENCY', '- wählen Sie eine Währung -');
define('TEXT_SELECT_CURRENCY_TITLE', 'wählen Sie eine Währung');
define('BUTTON_TEXT_SELECT_CUST', 'wählen Sie einen Kunden:'); 
define('TEXT_OR_BY', 'oder wählen Sie ihn über die Kunde-ID aus:'); 
define('TEXT_STEP_1', 'Schritt 1 - Wählen Sie einen bestehenden Kunden aus um die Felder automatisch auszufüllen.');
define('TEXT_STEP_2', 'Schritt 2 - Bestätigen Sie die Daten oder Änderungen.');
define('BUTTON_SUBMIT', 'auswählen');
define('ENTRY_CURRENCY','Währung: ');
define('ENTRY_ADMIN','Bestellung eingeben von:');
define('TEXT_CS','Kundenservice');

define('ACCOUNT_EXTRAS','Kontoextras');
define('ENTRY_ACCOUNT_PASSWORD','Passwort');
define('ENTRY_NEWSLETTER_SUBSCRIBE','Newsletter(Neuigkeiten per Email)');
define('ENTRY_ACCOUNT_PASSWORD_TEXT','');
define('ENTRY_NEWSLETTER_SUBSCRIBE_TEXT','1 = abonniert, oder 0 (NULL) = nicht abonniert.');


?>