<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2015 osCommerce

  Released under the GNU General Public License
*/

  class tp_email_orders {
    var $group = 'email_orders';
    var $template = '';
    var $title = 'Orders Status';
    var $description = 'osCommerce Basic Email Template<br />preview demo generated from <strong>last order status</strong> shop database data';
    var $section = 'admin';
    var $version = '1.01';

    function prepare() {
      global $oscTemplate;
      $oscTemplate->_email_data['orders']['enable_osc_mail'] = 'True';
      $GLOBALS['mimemessage'] = new email(array('X-Mailer: osCommerce'));
      $mimemessage = $GLOBALS['mimemessage'];
    }

    function build() {
      global $oscTemplate, $mimemessage, $notify_comments, $oID, $check_status, $orders_status_array, $status, $comments;

      if ($oscTemplate->_email_data['orders']['enable_osc_mail'] == 'True') {

        ob_start();
        include(DIR_WS_MODULES . 'pages/templates/email_orders_text.php');
        $text_content = ob_get_clean();

        // Build the text version
        $text_content = tep_convert_linefeeds(array("<br />"), "\n", $text_content);
        if (EMAIL_USE_HTML == 'true') {
          ob_start();
          include(DIR_WS_MODULES . 'pages/templates/email_orders_html.php');
          $html_content = tep_convert_linefeeds(array("\r\n", "\n", "\r"), '', ob_get_clean());

          $mimemessage->add_html($html_content, $text_content);
        } else {
          $mimemessage->add_text($text_content);
        }

        $mimemessage->build_message();
        $mimemessage->send($check_status['customers_name'], $check_status['customers_email_address'], STORE_OWNER, EMAIL_FROM, EMAIL_TEXT_SUBJECT);
      }
    }

    function preview() {
      global $oID, $language, $languages_id, $mode;

      if (tep_not_null($oID)) {
        $orders_statuses = array();
        $orders_status_array = array();
        $orders_status_query = tep_db_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . (int)$languages_id . "'");

        while ($orders_status = tep_db_fetch_array($orders_status_query)) {
          $orders_statuses[] = array('id' => $orders_status['orders_status_id'],
                                     'text' => $orders_status['orders_status_name']);
          $orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
        }

        include(DIR_WS_LANGUAGES . $language . '/orders.php');

        $orders_history_query = tep_db_query("select orders_status_id, date_added, customer_notified, comments from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id = '" . tep_db_input($oID) . "' order by orders_status_history_id desc limit 1");

        $orders_history = tep_db_fetch_array($orders_history_query);
        $status = $orders_history['orders_status_id'];
        $comments = $orders_history['comments'];

        $check_status_query = tep_db_query("select customers_name, customers_email_address, orders_status, date_purchased from " . TABLE_ORDERS . " where orders_id = '" . (int)$oID . "'");
        $check_status = tep_db_fetch_array($check_status_query);

        if ($orders_history['customer_notified'] == '1') {
          $_POST['notify_comments'] = 'on';
          $notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, $comments) . "\n\n";
        } else {
          $notify_comments = '';
        }

        ob_start();
        if ($mode == 'text') {
          include(DIR_FS_CATALOG . DIR_WS_MODULES . 'pages/templates/email_orders_text.php');
        } else {
          include(DIR_FS_CATALOG . DIR_WS_MODULES . 'pages/templates/email_orders_html.php');
        }

        $this->template = ob_get_clean();

      } else {
        $this->template = ERROR_NO_PREVIEW_CONTENT;
        $mode = 'text';
      }
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
