<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class product_notification {

    public $show_choose_audience = true;
    public $title, $content;

    function __construct($title, $content) {
      $this->show_choose_audience = true;
      $this->title = $title;
      $this->content = $content;
    }

    function choose_audience() {
      $products = [];
      $products_query = tep_db_query(<<<'EOSQL'
SELECT pd.products_id, pd.products_name
  FROM products p INNER JOIN products_description pd ON pd.products_id = p.products_id
  WHERE p.products_status = 1 AND pd.language_id = 
EOSQL
        . (int)$GLOBALS['languages_id'] . ' ORDER BY pd.products_name');
      while ($product = tep_db_fetch_array($products_query)) {
        $products[] = [
          'id' => $product['products_id'],
          'text' => $product['products_name'],
        ];
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

      $choose_audience_string .= '<form name="notifications" action="' . tep_href_link('newsletters.php', 'page=' . (int)$_GET['page'] . '&nID=' . $_GET['nID'] . '&action=confirm') . '" method="post" onsubmit="return selectAll(\'notifications\', \'chosen[]\')">';
        $choose_audience_string .= '<div class="row mb-3">';
          $choose_audience_string .= '<div class="col-5">';
            $choose_audience_string .= '<h6>' . TEXT_PRODUCTS . '</h6>';
            $choose_audience_string .= tep_draw_pull_down_menu('products', $products, '', 'class="custom-select" size="20" multiple');
          $choose_audience_string .= '</div>';
          $choose_audience_string .= '<div class="col-2 align-self-center text-center">';
            $choose_audience_string .= tep_draw_bootstrap_button(BUTTON_GLOBAL, 'fas fa-globe', tep_href_link('newsletters.php', 'page=' . (int)$_GET['page'] . '&nID=' . $_GET['nID'] . '&action=confirm&global=true'), 'primary', null, 'btn-info xxx text-white');
            $choose_audience_string .= '<br><br>';
            $choose_audience_string .= '<input type="button" class="btn btn-secondary" value="' . BUTTON_SELECT . '" onClick="mover(\'remove\');">';
            $choose_audience_string .= '<br><br>';
            $choose_audience_string .= '<input type="button" class="btn btn-secondary" value="' . BUTTON_UNSELECT . '" onClick="mover(\'add\');">';
          $choose_audience_string .= '</div>';
          $choose_audience_string .= '<div class="col-5">';
            $choose_audience_string .= '<h6>' . TEXT_SELECTED_PRODUCTS . '</h6>';
            $choose_audience_string .= tep_draw_pull_down_menu('chosen[]', [], '', 'class="custom-select" size="20" multiple');
          $choose_audience_string .= '</div>';
        $choose_audience_string .= '</div>';

        $choose_audience_string .= tep_draw_bootstrap_button(IMAGE_SEND, 'fas fa-paper-plane', null, 'primary', null, 'btn-success btn-lg btn-block xxx text-white');
        $choose_audience_string .= tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-angle-left', tep_href_link('newsletters.php', 'page=' . (int)$_GET['page'] . '&nID=' . $_GET['nID']), null, null, 'btn-light mt-2');
      $choose_audience_string .= '</form>';

      return $choose_audience_string;
    }

    function confirm() {
      $audience = [];

      $sql = "SELECT distinct customers_id FROM products_notifications";
      if ('true' !== ($_GET['global'] ?? null)) {
        $sql .= " WHERE products_id in (" . implode(', ', $_POST['chosen']) . ")";
      }

      $products_query = tep_db_query($sql);
      while ($products = tep_db_fetch_array($products_query)) {
        $audience[$products['customers_id']] = '1';
      }

      $customers_query = tep_db_query("SELECT customers_info_id FROM customers_info WHERE global_product_notifications = 1");
      while ($customers = tep_db_fetch_array($customers_query)) {
        $audience[$customers['customers_info_id']] = '1';
      }

      $confirm_string = '<div class="alert alert-danger">' . sprintf(TEXT_COUNT_CUSTOMERS, count($audience)) . '</div>';

        $confirm_string .= '<table class="table table-striped">';
          $confirm_string .= '<tr>';
            $confirm_string .= '<th scope="row">' . TEXT_TITLE . '</th>';
            $confirm_string .= '<td>' . $this->title . '</td>';
          $confirm_string .= '</tr>';
          $confirm_string .= '<tr>';
            $confirm_string .= '<th scope="row">' . TEXT_CONTENT . '</th>';
            $confirm_string .= '<td>' . $this->content . '</td>';
          $confirm_string .= '</tr>';
        $confirm_string .= '</table>';

      if (count($audience) > 0) {
        $confirm_string .= tep_draw_form('confirm', 'newsletters.php', 'page=' . (int)$_GET['page'] . '&nID=' . $_GET['nID'] . '&action=confirm_send');
        if (is_array($_POST['chosen'] ?? null)) {
          foreach ($_POST['chosen'] as $customer_id) {
            $confirm_string .= tep_draw_hidden_field('chosen[]', $customer_id);
          }
        } else {
          $confirm_string .= tep_draw_hidden_field('global', 'true');
        }

          $confirm_string .= tep_draw_bootstrap_button(IMAGE_SEND, 'fas fa-paper-plane', null, 'primary', null, 'btn-success btn-block btn-lg');
        $confirm_string .= '</form>';
      }

      $confirm_string .= tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-angle-left', tep_href_link('newsletters.php', 'page=' . (int)$_GET['page'] . '&nID=' . $_GET['nID'] . '&action=send'), 'primary', null, 'btn-light mt-2');

      return $confirm_string;
    }

    function send($newsletter_id) {
      global $customer_data;
      $audience = [];

      $db_tables = $customer_data->build_db_tables(['id', 'name', 'email_address'], 'customers');
      tep_guarantee_subarray($db_tables, 'customers');
      $db_tables['customers']['customers_id'] = null;
      $built = query::rtrim_string_once(customer_query::build_specified_columns($db_tables), query::COLUMN_SEPARATOR)
             . ' FROM' . customer_query::build_joins($db_tables, []);

      $sql = 'SELECT DISTINCT ' . $built;
      $sql .= ' INNER JOIN products_notifications pn ON c.customers_id = pn.customers_id';
      if ('true' !== ($_POST['global'] ?? null)) {
        $sql .= ' WHERE pn.products_id in (' . implode(',', $_POST['chosen']) . ')';
      }

      $products_query = tep_db_query($sql);
      while ($products = tep_db_fetch_array($products_query)) {
        $audience[$customer_data->get('id', $products)] = [
          'name' => $customer_data->get('name', $products),
          'email_address' => $customer_data->get('email_address', $products),
        ];
      }

      $customers_query = tep_db_query('SELECT ' . $built . ' INNER JOIN customers_info ci ON c.customers_id = ci.customers_info_id WHERE ci.global_product_notifications = 1');
      while ($customers = tep_db_fetch_array($customers_query)) {
        $audience[$customer_data->get('id', $customers)] = [
          'name' => $customer_data->get('name', $customers),
          'email_address' => $customer_data->get('email_address', $customers),
        ];
      }

      $mimemessage = new email();
      $mimemessage->add_message($this->content);
      $mimemessage->build_message();

      foreach ($audience as $value) {
        $mimemessage->send($value['name'], $value['email_address'], STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, $this->title);
      }

      $newsletter_id = tep_db_prepare_input($newsletter_id);
      tep_db_query("UPDATE newsletters SET date_sent = NOW(), status = 1 WHERE newsletters_id = '" . tep_db_input($newsletter_id) . "'");
    }
  }

