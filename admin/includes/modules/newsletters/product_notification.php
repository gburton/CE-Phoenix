<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class product_notification {
    var $show_choose_audience, $title, $content;

    function __construct($title, $content) {
      $this->show_choose_audience = true;
      $this->title = $title;
      $this->content = $content;
    }

    function choose_audience() {
      global $languages_id;

      $products_array = array();
      $products_query = tep_db_query("select pd.products_id, pd.products_name from products p, products_description pd where pd.language_id = '" . $languages_id . "' and pd.products_id = p.products_id and p.products_status = '1' order by pd.products_name");
      while ($products = tep_db_fetch_array($products_query)) {
        $products_array[] = array('id' => $products['products_id'],
                                  'text' => $products['products_name']);
      }

$choose_audience_string = '<script><!--
function mover(move) {
  if (move == \'remove\') {
    for (x=0; x<(document.notifications.products.length); x++) {
      if (document.notifications.products.options[x].selected) {
        with(document.notifications.elements[\'chosen[]\']) {
          options[options.length] = new Option(document.notifications.products.options[x].text,document.notifications.products.options[x].value);
        }
        document.notifications.products.options[x] = null;
        x = -1;
      }
    }
  }
  if (move == \'add\') {
    for (x=0; x<(document.notifications.elements[\'chosen[]\'].length); x++) {
      if (document.notifications.elements[\'chosen[]\'].options[x].selected) {
        with(document.notifications.products) {
          options[options.length] = new Option(document.notifications.elements[\'chosen[]\'].options[x].text,document.notifications.elements[\'chosen[]\'].options[x].value);
        }
        document.notifications.elements[\'chosen[]\'].options[x] = null;
        x = -1;
      }
    }
  }
  return true;
}

function selectAll(FormName, SelectBox) {
  temp = "document." + FormName + ".elements[\'" + SelectBox + "\']";
  Source = eval(temp);

  for (x=0; x<(Source.length); x++) {
    Source.options[x].selected = "true";
  }

  if (x<1) {
    alert(\'' . JS_PLEASE_SELECT_PRODUCTS . '\');
    return false;
  } else {
    return true;
  }
}
//--></script>';

      $choose_audience_string .= '<form name="notifications" action="' . tep_href_link('newsletters.php', 'page=' . $_GET['page'] . '&nID=' . $_GET['nID'] . '&action=confirm') . '" method="post" onsubmit="return selectAll(\'notifications\', \'chosen[]\')">';
        $choose_audience_string .= '<div class="row mb-3">';
          $choose_audience_string .= '<div class="col-5">';
            $choose_audience_string .= '<h6>' . TEXT_PRODUCTS . '</h6>';
            $choose_audience_string .= tep_draw_pull_down_menu('products', $products_array, '', 'class="custom-select" size="20" multiple');
          $choose_audience_string .= '</div>';
          $choose_audience_string .= '<div class="col-2 align-self-center text-center">';
            $choose_audience_string .= tep_draw_bootstrap_button(BUTTON_GLOBAL, 'fas fa-globe', tep_href_link('newsletters.php', 'page=' . $_GET['page'] . '&nID=' . $_GET['nID'] . '&action=confirm&global=true'), 'primary', null, 'btn-info xxx text-white');
            $choose_audience_string .= '<br><br>';
            $choose_audience_string .= '<input type="button" class="btn btn-secondary" value="' . BUTTON_SELECT . '" onClick="mover(\'remove\');">';
            $choose_audience_string .= '<br><br>';
            $choose_audience_string .= '<input type="button" class="btn btn-secondary" value="' . BUTTON_UNSELECT . '" onClick="mover(\'add\');">';
          $choose_audience_string .= '</div>';
          $choose_audience_string .= '<div class="col-5">';
            $choose_audience_string .= '<h6>' . TEXT_SELECTED_PRODUCTS . '</h6>';
            $choose_audience_string .= tep_draw_pull_down_menu('chosen[]', array(), '', 'class="custom-select" size="20" multiple');
          $choose_audience_string .= '</div>';
        $choose_audience_string .= '</div>';

        $choose_audience_string .= tep_draw_bootstrap_button(IMAGE_SEND, 'fas fa-paper-plane', null, 'primary', null, 'btn-success btn-lg btn-block xxx text-white');
        $choose_audience_string .= tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-angle-left', tep_href_link('newsletters.php', 'page=' . $_GET['page'] . '&nID=' . $_GET['nID']), null, null, 'btn-light mt-2');
       $choose_audience_string .= '</form>';

      return $choose_audience_string;
    }

    function confirm() {
      $audience = array();

      if (isset($_GET['global']) && ($_GET['global'] == 'true')) {
        $products_query = tep_db_query("select distinct customers_id from products_notifications");
        while ($products = tep_db_fetch_array($products_query)) {
          $audience[$products['customers_id']] = '1';
        }

        $customers_query = tep_db_query("select customers_info_id from customers_info where global_product_notifications = '1'");
        while ($customers = tep_db_fetch_array($customers_query)) {
          $audience[$customers['customers_info_id']] = '1';
        }
      } else {
        $chosen = $_POST['chosen'];

        $ids = implode(',', $chosen);

        $products_query = tep_db_query("select distinct customers_id from products_notifications where products_id in (" . $ids . ")");
        while ($products = tep_db_fetch_array($products_query)) {
          $audience[$products['customers_id']] = '1';
        }

        $customers_query = tep_db_query("select customers_info_id from customers_info where global_product_notifications = '1'");
        while ($customers = tep_db_fetch_array($customers_query)) {
          $audience[$customers['customers_info_id']] = '1';
        }
      }

      $confirm_string = '<div class="alert alert-danger">' . sprintf(TEXT_COUNT_CUSTOMERS, sizeof($audience)) . '</div>';

      $confirm_string .= '<table class="table table-striped">';
        $confirm_string .= '<tr>';
          $confirm_string .= '<th>' . TEXT_TITLE . '</th>';
          $confirm_string .= '<td>' . $this->title . '</td>';
        $confirm_string .= '</tr>';
        $confirm_string .= '<tr>';
          $confirm_string .= '<th>' . TEXT_CONTENT . '</th>';
          $confirm_string .= '<td>' . $this->content . '</td>';
        $confirm_string .= '</tr>';
      $confirm_string .= '</table>';

      if (sizeof($audience) > 0) {
        if (isset($_GET['global']) && ($_GET['global'] == 'true')) {
          $confirm_string .= tep_draw_hidden_field('global', 'true');
        } else {
          for ($i = 0, $n = sizeof($chosen); $i < $n; $i++) {
            $confirm_string .= tep_draw_hidden_field('chosen[]', $chosen[$i]);
          }
        }
        $confirm_string .= tep_draw_bootstrap_button(IMAGE_SEND, 'fas fa-paper-plane', null, 'primary', null, 'btn-success btn-block btn-lg');
      }

      $confirm_string .= tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-angle-left', tep_href_link('newsletters.php', 'page=' . $_GET['page'] . '&nID=' . $_GET['nID'] . '&action=send'), 'primary', null, 'btn-light mt-2');

      return $confirm_string;
    }

    function send($newsletter_id) {
      $audience = array();

      if (isset($_POST['global']) && ($_POST['global'] == 'true')) {
        $products_query = tep_db_query("select distinct pn.customers_id, c.customers_firstname, c.customers_lastname, c.customers_email_address from customers c, products_notifications pn where c.customers_id = pn.customers_id");
        while ($products = tep_db_fetch_array($products_query)) {
          $audience[$products['customers_id']] = array('firstname' => $products['customers_firstname'],
                                                       'lastname' => $products['customers_lastname'],
                                                       'email_address' => $products['customers_email_address']);
        }

        $customers_query = tep_db_query("select c.customers_id, c.customers_firstname, c.customers_lastname, c.customers_email_address from customers c, customers_info ci where c.customers_id = ci.customers_info_id and ci.global_product_notifications = '1'");
        while ($customers = tep_db_fetch_array($customers_query)) {
          $audience[$customers['customers_id']] = array('firstname' => $customers['customers_firstname'],
                                                        'lastname' => $customers['customers_lastname'],
                                                        'email_address' => $customers['customers_email_address']);
        }
      } else {
        $chosen = $_POST['chosen'];

        $ids = implode(',', $chosen);

        $products_query = tep_db_query("select distinct pn.customers_id, c.customers_firstname, c.customers_lastname, c.customers_email_address from customers c, products_notifications pn where c.customers_id = pn.customers_id and pn.products_id in (" . $ids . ")");
        while ($products = tep_db_fetch_array($products_query)) {
          $audience[$products['customers_id']] = array('firstname' => $products['customers_firstname'],
                                                       'lastname' => $products['customers_lastname'],
                                                       'email_address' => $products['customers_email_address']);
        }

        $customers_query = tep_db_query("select c.customers_id, c.customers_firstname, c.customers_lastname, c.customers_email_address from customers c, customers_info ci where c.customers_id = ci.customers_info_id and ci.global_product_notifications = '1'");
        while ($customers = tep_db_fetch_array($customers_query)) {
          $audience[$customers['customers_id']] = array('firstname' => $customers['customers_firstname'],
                                                        'lastname' => $customers['customers_lastname'],
                                                        'email_address' => $customers['customers_email_address']);
        }
      }

      $mimemessage = new email();
      $mimemessage->add_message($this->content);
      $mimemessage->build_message();

      foreach ($audience as $key => $value) {
        $mimemessage->send($value['firstname'] . ' ' . $value['lastname'], $value['email_address'], '', EMAIL_FROM, $this->title);
      }

      $newsletter_id = tep_db_prepare_input($newsletter_id);
      tep_db_query("update newsletters set date_sent = now(), status = '1' where newsletters_id = '" . tep_db_input($newsletter_id) . "'");
    }
  }
  
