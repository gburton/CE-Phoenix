<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2015 osCommerce

  Released under the GNU General Public License
*/

  class tp_email_checkout_process {
    var $group = 'email_checkout_process';
    var $template = '';
    var $title = 'Checkout Process';
    var $description = 'osCommerce Basic Email Template<br />preview demo generated from <strong>last order</strong> data';
    var $section = 'shop';
    var $version = '1.01';

    function prepare() {
      global $oscTemplate;
      $oscTemplate->_email_data['orders']['enable_osc_mail'] = 'True';
      $GLOBALS['mimemessage'] = new email(array('X-Mailer: osCommerce'));
      $mimemessage = $GLOBALS['mimemessage'];
    }

    function build() {
      global $oscTemplate, $mimemessage, $order, $payment, $products_ordered, $insert_id, $order_totals, $customer_id, $sendto, $billto;

      if ($oscTemplate->_email_data['orders']['enable_osc_mail'] == 'True') {
        if (is_object($$payment)) {
          $payment_class = $$payment;
        }

        $mimemessage = new email(array('X-Mailer: osCommerce'));
        $mimemessage->build_params['text_encoding'] = 'quoted-printable';

        ob_start();
        include(DIR_WS_MODULES . 'pages/templates/email_checkout_process_text.php');
        $text_content = ob_get_clean();

        // Build the text version
        $text_content = tep_convert_linefeeds(array("<br />"), "\n", $text_content);
        if (EMAIL_USE_HTML == 'true') {
          ob_start();
          include(DIR_WS_MODULES . 'pages/templates/email_checkout_process_html.php');
          $html_content = tep_convert_linefeeds(array("\r\n", "\n", "\r"), '', ob_get_clean());

          $mimemessage->add_html($html_content, $text_content);
        } else {
          $mimemessage->add_text($text_content);
        }

        $mimemessage->build_message();
        $mimemessage->send($order->customer['firstname'] . ' ' . $order->customer['lastname'], $order->customer['email_address'], STORE_OWNER, EMAIL_FROM, EMAIL_TEXT_SUBJECT);

        // send emails to other people
        if (SEND_EXTRA_ORDER_EMAILS_TO != '') {
          $mimemessage->send('', SEND_EXTRA_ORDER_EMAILS_TO, STORE_OWNER, EMAIL_FROM, EMAIL_TEXT_SUBJECT);
        }
      }
    }

    function preview() {
      global $oID, $currencies, $payment, $mode, $language;

      if (tep_not_null($oID)) {
        require(DIR_FS_CATALOG . DIR_WS_LANGUAGES . $language . '/checkout_process.php');

        include(DIR_FS_CATALOG . DIR_WS_CLASSES . 'order.php');
        $order = new order($oID);

        $insert_id = $oID;

        $$payment = new payment_demo;

        $comment_query = tep_db_query("select comments from orders_status_history where orders_id = '" . $oID . "' limit 1");
        $comment = tep_db_fetch_array($comment_query);

        $order->info['comments'] = $comment['comments'];

        $order_totals = $order->totals;

        $customer_id = $order->customer['id'];

        $customer_info_query = tep_db_query("select c.customers_firstname, c.customers_default_address_id, ab.entry_country_id, ab.entry_zone_id from " . TABLE_CUSTOMERS . " c left join " . TABLE_ADDRESS_BOOK . " ab on (c.customers_id = ab.customers_id and c.customers_default_address_id = ab.address_book_id) where c.customers_id = '" . (int)$customer_id . "'");
        $customer_info = tep_db_fetch_array($customer_info_query);

        $sendto = $customer_info['customers_default_address_id'];
        $billto = $customer_info['customers_default_address_id'];

        $products_ordered = '';
        for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
          $products_ordered_attributes = '';
          $products_ordered .= $order->products[$i]['qty'] . ' x ' . $order->products[$i]['name'] . ' (' . $order->products[$i]['model'] . ')';

          if (isset($order->products[$i]['attributes']) && (sizeof($order->products[$i]['attributes']) > 0)) {
            for ($j = 0, $k = sizeof($order->products[$i]['attributes']); $j < $k; $j++) {
              $products_ordered_attributes .= "\n\t" . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'];
            }
          }

          $products_ordered .= ' = ' . $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax'], true) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . $products_ordered_attributes . "\n";
        }

        ob_start();
        if ($mode == 'text') {
          include(DIR_FS_CATALOG . DIR_WS_MODULES . 'pages/templates/email_checkout_process_text.php');
        } else {
          include(DIR_FS_CATALOG . DIR_WS_MODULES . 'pages/templates/email_checkout_process_html.php');
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
