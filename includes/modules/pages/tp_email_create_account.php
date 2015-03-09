<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2015 osCommerce

  Released under the GNU General Public License
*/

  class tp_email_create_account {
    var $group = 'email_create_account';
    var $template = '';
    var $title = 'Create Account';
    var $description = 'osCommerce Basic Email Template<br />preview demo generated from <strong>last account</strong> data';
    var $section = 'shop';
    var $version = '1.01';

    function prepare() {
      global $oscTemplate;
      $oscTemplate->_email_data['create_account']['enable_osc_mail'] = 'True';
      $GLOBALS['mimemessage'] = new email(array('X-Mailer: osCommerce'));
      $mimemessage = $GLOBALS['mimemessage'];
    }

    function build() {
      global $oscTemplate, $mimemessage, $lastname, $firstname, $gender, $email_address;

      if ($oscTemplate->_email_data['create_account']['enable_osc_mail'] == 'True') {
        $name = $firstname . ' ' . $lastname;

        if (ACCOUNT_GENDER == 'true') {
           if ($gender == 'm') {
             $welcome_text = sprintf(EMAIL_GREET_MR, $lastname);
           } else {
             $welcome_text = sprintf(EMAIL_GREET_MS, $lastname);
           }
        } else {
          $welcome_text = sprintf(EMAIL_GREET_NONE, $firstname);
        }

        $mimemessage = new email(array('X-Mailer: osCommerce'));
        $mimemessage->build_params['text_encoding'] = 'quoted-printable';

        ob_start();
        include(DIR_WS_MODULES . 'pages/templates/email_create_account_text.php');
        $text_content = ob_get_clean();

        // Build the text version
        $text_content = tep_convert_linefeeds(array("<br />"), "\n", $text_content);
        if (EMAIL_USE_HTML == 'true') {
          ob_start();
          include(DIR_WS_MODULES . 'pages/templates/email_create_account_html.php');
          $html_content = tep_convert_linefeeds(array("\r\n", "\n", "\r"), '', ob_get_clean());

          $mimemessage->add_html($html_content, $text_content);
        } else {
          $mimemessage->add_text($text_content);
        }

        $mimemessage->build_message();
        $mimemessage->send($name, $email_address, STORE_OWNER, EMAIL_FROM, EMAIL_SUBJECT);
      }
    }

    function preview() {
      global $mode, $language;

      require(DIR_FS_CATALOG . DIR_WS_LANGUAGES . $language . '/create_account.php');

      $last_account_query = tep_db_query("select max(customers_id), customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . "");

      $last_account = tep_db_fetch_array($last_account_query);

      $email_text = sprintf(EMAIL_GREET_NONE, $last_account['customers_firstname']);

      ob_start();
      if ($mode == 'text') {
        include(DIR_FS_CATALOG . DIR_WS_MODULES . 'pages/templates/email_create_account_text.php');
      } else {
        include(DIR_FS_CATALOG . DIR_WS_MODULES . 'pages/templates/email_create_account_html.php');
      }

      $this->template = ob_get_clean();
    }

    function info() {
      return array('group' => $this->group,
                   'title' => $this->title,
                   'description' => $this->description,
                   'section' => $this->section,
                   'version' => $this->version);
    }
  }
?>
