<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2015 osCommerce

  Released under the GNU General Public License
*/

  class tp_email_tell_a_friend {
    var $group = 'email_tell_a_friend';
    var $template = '';
    var $title = 'Tell a Friend';
    var $description = 'osCommerce Basic Email Template<br />preview demo with <strong>hard coded</strong> variables';
    var $section = 'shop';
    var $version = '1.01';

    function prepare() {
      global $oscTemplate;
      $oscTemplate->_email_data['tell_a_friend']['enable_osc_mail'] = 'True';
      $GLOBALS['mimemessage'] = new email(array('X-Mailer: osCommerce'));
      $mimemessage = $GLOBALS['mimemessage'];
    }

    function build() {
      global $oscTemplate, $mimemessage, $to_name, $to_email_address, $from_name, $from_email_address, $product_info, $message;

      if ($oscTemplate->_email_data['tell_a_friend']['enable_osc_mail'] == 'True') {
        $email_subject = sprintf(TEXT_EMAIL_SUBJECT, $from_name, STORE_NAME);
        $email_body = sprintf(TEXT_EMAIL_INTRO, $to_name, $from_name, $product_info['products_name'], STORE_NAME) . "\n\n";

        $mimemessage = new email(array('X-Mailer: osCommerce'));
        $mimemessage->build_params['text_encoding'] = 'quoted-printable';

        ob_start();
        require(DIR_WS_MODULES . 'pages/templates/email_tell_a_friend_text.php');
        $text_content = ob_get_clean();

        // Build the text version
        $text_content = tep_convert_linefeeds(array("<br />"), "\n", $text_content);
        if (EMAIL_USE_HTML == 'true') {
          ob_start();
          require(DIR_WS_MODULES . 'pages/templates/email_tell_a_friend_html.php');
          $html_content = tep_convert_linefeeds(array("\r\n", "\n", "\r"), '', ob_get_clean());

          $mimemessage->add_html($html_content, $text_content);
        } else {
          $mimemessage->add_text($text_content);
        }

        $mimemessage->build_message();
        $mimemessage->send($to_name, $to_email_address, $from_name, $from_email_address, $email_subject);
      }
    }

    function preview() {
      global $mode, $language;

      require(DIR_FS_CATALOG . DIR_WS_LANGUAGES . $language . '/tell_a_friend.php');
      $_GET['products_id'] = '100';
      $to_name = 'Bill';
      $from_name = 'Andrea';
      $message = 'Bill,' . "\n" . 'this is very cool product!';
      $product_info['products_name'] = 'Matrox G200 MMS';

      $email_body = "\n" . sprintf(TEXT_EMAIL_INTRO, $to_name, $from_name, $product_info['products_name'], STORE_NAME) . "\n";

      ob_start();
      if ($mode == 'text') {
        include(DIR_FS_CATALOG . DIR_WS_MODULES . 'pages/templates/email_tell_a_friend_text.php');
      } else {
        include(DIR_FS_CATALOG . DIR_WS_MODULES . 'pages/templates/email_tell_a_friend_html.php');
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
