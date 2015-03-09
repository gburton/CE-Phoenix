<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2015 osCommerce

  Released under the GNU General Public License
*/

  class tp_email_password_forgotten {
    var $group = 'email_password_forgotten';
    var $template = '';
    var $title = 'Password forgotten';
    var $description = 'osCommerce Basic Email Template<br />preview mode with <strong>hard coded</strong> variables';
    var $section = 'shop';
    var $version = '1.01';

    function prepare() {
      global $oscTemplate;
      $oscTemplate->_email_data['password_forgotten']['enable_osc_mail'] = 'True';
      $GLOBALS['mimemessage'] = new email(array('X-Mailer: osCommerce'));
      $mimemessage = $GLOBALS['mimemessage'];
    }

    function build() {
      global $oscTemplate, $mimemessage, $reset_key_url, $check_customer, $email_address;

      if ($oscTemplate->_email_data['password_forgotten']['enable_osc_mail'] == 'True') {
        $email_text = sprintf(EMAIL_PASSWORD_RESET_BODY, $reset_key_url);

        $mimemessage = new email(array('X-Mailer: osCommerce'));
        $mimemessage->build_params['text_encoding'] = 'quoted-printable';

        $name = $mail['customers_firstname'] . ' ' . $mail['customers_lastname'];

        ob_start();
        require(DIR_WS_MODULES . 'pages/templates/email_password_forgotten_text.php');
        $text_content = ob_get_clean();

        // Build the text version
        $text_content = tep_convert_linefeeds(array("<br />"), "\n", $text_content);
        if (EMAIL_USE_HTML == 'true') {
          ob_start();
          require(DIR_WS_MODULES . 'pages/templates/email_password_forgotten_html.php');
          $html_content = tep_convert_linefeeds(array("\r\n", "\n", "\r"), '', ob_get_clean());

          $mimemessage->add_html($html_content, $text_content);
        } else {
          $mimemessage->add_text($text_content);
        }

        $mimemessage->build_message();
        $mimemessage->send($check_customer['customers_firstname'] . ' ' . $check_customer['customers_lastname'], $email_address, STORE_OWNER, EMAIL_FROM, EMAIL_PASSWORD_RESET_SUBJECT);
      }
    }

    function preview() {
      global $mode, $language;

      require(DIR_FS_CATALOG . DIR_WS_LANGUAGES . $language . '/password_forgotten.php');

      $email_address = 'who@is.com';
      $reset_key = 'thisisasecretcodestring';

      $reset_key_url = tep_catalog_href_link('password_reset.php', 'account=' . urlencode($email_address) . '&key=' . $reset_key, 'SSL', false);

      $email_text = sprintf(EMAIL_PASSWORD_RESET_BODY, $reset_key_url);

      ob_start();
      if ($mode == 'text') {
        include(DIR_FS_CATALOG . DIR_WS_MODULES . 'pages/templates/email_password_forgotten_text.php');
      } else {
        include(DIR_FS_CATALOG . DIR_WS_MODULES . 'pages/templates/email_password_forgotten_html.php');
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
