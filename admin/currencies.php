<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require('includes/classes/currencies.php');
  $currencies = new currencies();

  $action = $_GET['action'] ?? '';
  
  $OSCOM_Hooks->call('currencies', 'preAction');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'insert':
      case 'save':
        if (isset($_GET['cID'])) $currency_id = tep_db_prepare_input($_GET['cID']);
        $title = tep_db_prepare_input($_POST['title']);
        $code = tep_db_prepare_input($_POST['code']);
        $symbol_left = tep_db_prepare_input($_POST['symbol_left']);
        $symbol_right = tep_db_prepare_input($_POST['symbol_right']);
        $decimal_point = tep_db_prepare_input($_POST['decimal_point']);
        $thousands_point = tep_db_prepare_input($_POST['thousands_point']);
        $decimal_places = tep_db_prepare_input($_POST['decimal_places']);
        $value = tep_db_prepare_input($_POST['value']);

        $sql_data_array = ['title' => $title,
                           'code' => $code,
                           'symbol_left' => $symbol_left,
                           'symbol_right' => $symbol_right,
                           'decimal_point' => $decimal_point,
                           'thousands_point' => $thousands_point,
                           'decimal_places' => $decimal_places,
                           'value' => $value];

        if ($action == 'insert') {
          tep_db_perform('currencies', $sql_data_array);
          $currency_id = tep_db_insert_id();
          
          $OSCOM_Hooks->call('currencies', 'insert');
          
        } elseif ($action == 'save') {
          tep_db_perform('currencies', $sql_data_array, 'update', "currencies_id = '" . (int)$currency_id . "'");
          
          $OSCOM_Hooks->call('currencies', 'save');
        }

        if (isset($_POST['default']) && ($_POST['default'] == 'on')) {
          tep_db_query("update configuration set configuration_value = '" . tep_db_input($code) . "' where configuration_key = 'DEFAULT_CURRENCY'");
        }
        
        $OSCOM_Hooks->call('currencies', 'saveinsert');        

        tep_redirect(tep_href_link('currencies.php', 'page=' . (int)$_GET['page'] . '&cID=' . $currency_id));
        break;
      case 'deleteconfirm':
        $currencies_id = tep_db_prepare_input($_GET['cID']);

        $currency_query = tep_db_query("select currencies_id from currencies where code = '" . DEFAULT_CURRENCY . "'");
        $currency = tep_db_fetch_array($currency_query);

        if ($currency['currencies_id'] == $currencies_id) {
          tep_db_query("update configuration set configuration_value = '' where configuration_key = 'DEFAULT_CURRENCY'");
        }

        tep_db_query("delete from currencies where currencies_id = '" . (int)$currencies_id . "'");
        
        $OSCOM_Hooks->call('currencies', 'deleteconfirm');

        tep_redirect(tep_href_link('currencies.php', 'page=' . (int)$_GET['page']));
        break;
      case 'update':
        include_once('includes/languages/' . $language . '/modules/currencies/' . MODULE_ADMIN_CURRENCIES_INSTALLED);
        include_once('includes/modules/currencies/' . MODULE_ADMIN_CURRENCIES_INSTALLED);
        
        $converter = basename(MODULE_ADMIN_CURRENCIES_INSTALLED, '.php');
 
        call_user_func([$converter, 'execute']);
        
        $OSCOM_Hooks->call('currencies', 'update');

        tep_redirect(tep_href_link('currencies.php'));
        break;
      case 'delete':
        $currencies_id = tep_db_prepare_input($_GET['cID']);

        $currency_query = tep_db_query("select code from currencies where currencies_id = '" . (int)$currencies_id . "'");
        $currency = tep_db_fetch_array($currency_query);

        $remove_currency = true;
        if ($currency['code'] == DEFAULT_CURRENCY) {
          $remove_currency = false;
          $messageStack->add(ERROR_REMOVE_DEFAULT_CURRENCY, 'error');
        }
        
        $OSCOM_Hooks->call('currencies', 'delete');
        break;
    }
  }

  $currency_select = ['USD' => ['title' => 'U.S. Dollar', 'code' => 'USD', 'symbol_left' => '$', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'],
                      'EUR' =>['title' => 'Euro', 'code' => 'EUR', 'symbol_left' => '', 'symbol_right' => '€', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'],
                      'JPY' => ['title' => 'Japanese Yen', 'code' => 'JPY', 'symbol_left' => '¥', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'],
                      'GBP' => ['title' => 'Pounds Sterling', 'code' => 'GBP', 'symbol_left' => '£', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'],
                      'CHF' => ['title' => 'Swiss Franc', 'code' => 'CHF', 'symbol_left' => '', 'symbol_right' => 'CHF', 'decimal_point' => ',', 'thousands_point' => '.', 'decimal_places' => '2'],
                      'AUD' => ['title' => 'Australian Dollar', 'code' => 'AUD', 'symbol_left' => '$', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'],
                      'CAD' => ['title' => 'Canadian Dollar', 'code' => 'CAD', 'symbol_left' => '$', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'],
                      'SEK' => ['title' => 'Swedish Krona', 'code' => 'SEK', 'symbol_left' => '', 'symbol_right' => 'kr', 'decimal_point' => ',', 'thousands_point' => '.', 'decimal_places' => '2'],
                      'HKD' => ['title' => 'Hong Kong Dollar', 'code' => 'HKD', 'symbol_left' => '$', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'],
                      'NOK' => ['title' => 'Norwegian Krone', 'code' => 'NOK', 'symbol_left' => 'kr', 'symbol_right' => '', 'decimal_point' => ',', 'thousands_point' => '.', 'decimal_places' => '2'],
                      'NZD' => ['title' => 'New Zealand Dollar', 'code' => 'NZD', 'symbol_left' => '$', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'],
                      'MXN' => ['title' => 'Mexican Peso', 'code' => 'MXN', 'symbol_left' => '$', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'],
                      'SGD' => ['title' => 'Singapore Dollar', 'code' => 'SGD', 'symbol_left' => '$', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'],
                      'BRL' => ['title' => 'Brazilian Real', 'code' => 'BRL', 'symbol_left' => 'R$', 'symbol_right' => '', 'decimal_point' => ',', 'thousands_point' => '.', 'decimal_places' => '2'],
                      'CNY' => ['title' => 'Chinese RMB', 'code' => 'CNY', 'symbol_left' => '￥', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'],
                      'CZK' => ['title' => 'Czech Koruna', 'code' => 'CZK', 'symbol_left' => '', 'symbol_right' => 'Kč', 'decimal_point' => ',', 'thousands_point' => '.', 'decimal_places' => '2'],
                      'DKK' => ['title' => 'Danish Krone', 'code' => 'DKK', 'symbol_left' => '', 'symbol_right' => 'kr', 'decimal_point' => ',', 'thousands_point' => '.', 'decimal_places' => '2'],
                      'HUF' => ['title' => 'Hungarian Forint', 'code' => 'HUF', 'symbol_left' => '', 'symbol_right' => 'Ft', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'],
                      'ILS' => ['title' => 'Israeli New Shekel', 'code' => 'ILS', 'symbol_left' => '₪', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'],
                      'INR' => ['title' => 'Indian Rupee', 'code' => 'INR', 'symbol_left' => 'Rs.', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'],
                      'MYR' => ['title' => 'Malaysian Ringgit', 'code' => 'MYR', 'symbol_left' => 'RM', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'],
                      'PHP' => ['title' => 'Philippine Peso', 'code' => 'PHP', 'symbol_left' => 'Php', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'],
                      'PLN' => ['title' => 'Polish Zloty', 'code' => 'PLN', 'symbol_left' => '', 'symbol_right' => 'zł', 'decimal_point' => ',', 'thousands_point' => '.', 'decimal_places' => '2'],
                      'THB' => ['title' => 'Thai Baht', 'code' => 'THB', 'symbol_left' => '', 'symbol_right' => '฿', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'],
                      'TWD' => ['title' => 'Taiwan New Dollar', 'code' => 'TWD', 'symbol_left' => 'NT$', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2']];

  $currency_select_array = [['id' => '', 'text' => TEXT_INFO_COMMON_CURRENCIES]];
  foreach ($currency_select as $cs) {
    if (!isset($currencies->currencies[$cs['code']])) {
      $currency_select_array[] = ['id' => $cs['code'], 'text' => '[' . $cs['code'] . '] ' . $cs['title']];
    }
  }

  require('includes/template_top.php');
?>

<script>
var currency_select = new Array();
<?php
  foreach ($currency_select_array as $cs) {
    if (!empty($cs['id'])) {
      echo 'currency_select["' . $cs['id'] . '"] = new Array("' . $currency_select[$cs['id']]['title'] . '", "' . $currency_select[$cs['id']]['symbol_left'] . '", "' . $currency_select[$cs['id']]['symbol_right'] . '", "' . $currency_select[$cs['id']]['decimal_point'] . '", "' . $currency_select[$cs['id']]['thousands_point'] . '", "' . $currency_select[$cs['id']]['decimal_places'] . '");' . "\n";
    }
  }
?>

function updateForm() {
  var cs = document.forms["currencies"].cs[document.forms["currencies"].cs.selectedIndex].value;

  document.forms["currencies"].title.value = currency_select[cs][0];
  document.forms["currencies"].code.value = cs;
  document.forms["currencies"].symbol_left.value = currency_select[cs][1];
  document.forms["currencies"].symbol_right.value = currency_select[cs][2];
  document.forms["currencies"].decimal_point.value = currency_select[cs][3];
  document.forms["currencies"].thousands_point.value = currency_select[cs][4];
  document.forms["currencies"].decimal_places.value = currency_select[cs][5];
  document.forms["currencies"].value.value = 1;
}
</script>

  <div class="row">
    <div class="col">
      <h1 class="display-4 mb-2"><?php echo HEADING_TITLE; ?></h1>
    </div>
    <div class="col-sm-4 text-right align-self-center">
      <?php
      if (empty($action)) {
        echo tep_draw_bootstrap_button(IMAGE_NEW_CURRENCY, 'fas fa-cogs', tep_href_link('currencies.php', 'action=new'), null, null, 'btn-danger xxx text-white');
      } else {
        echo tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link('currencies.php'), null, null, 'btn-light');
      }
      ?>
    </div>
  </div>
  
  <div class="row no-gutters">
    <div class="col">
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th><?php echo TABLE_HEADING_CURRENCY_NAME; ?></th>
              <th><?php echo TABLE_HEADING_CURRENCY_CODES; ?></th>
              <th><?php echo TABLE_HEADING_CURRENCY_VALUE; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_ACTION; ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $currency_query_raw = "SELECT * FROM currencies ORDER BY title";
            $currency_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $currency_query_raw, $currency_query_numrows);
            $currency_query = tep_db_query($currency_query_raw);
            while ($currency = tep_db_fetch_array($currency_query)) {
              if ((!isset($_GET['cID']) || (isset($_GET['cID']) && ($_GET['cID'] == $currency['currencies_id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
                $cInfo = new objectInfo($currency);
              }

              if (isset($cInfo) && is_object($cInfo) && ($currency['currencies_id'] == $cInfo->currencies_id) ) {
                echo '<tr class="table-active" onclick="document.location.href=\'' . tep_href_link('currencies.php', 'page=' . (int)$_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=edit') . '\'">';
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('currencies.php', 'page=' . (int)$_GET['page'] . '&cID=' . $currency['currencies_id']) . '\'">';
              }

              if (DEFAULT_CURRENCY == $currency['code']) {
                echo '<th>' . $currency['title'] . ' (' . TEXT_DEFAULT . ')</th>';
              } else {
                echo '<td>' . $currency['title'] . '</td>';
              }
              ?>
                <td><?php echo $currency['code']; ?></td>
                <td><?php echo number_format($currency['value'], 8); ?></td>
                <td class="text-right"><?php if (isset($cInfo) && is_object($cInfo) && ($currency['currencies_id'] == $cInfo->currencies_id) ) { echo '<i class="fas fa-chevron-circle-right text-info"></i>'; } else { echo '<a href="' . tep_href_link('currencies.php', 'page=' . (int)$_GET['page'] . '&cID=' . $currency['currencies_id']) . '"><i class="fas fa-info-circle text-muted"></i></a>'; } ?></td>
              </tr>
              <?php
            }
            ?>
          </tbody>
        </table>
      </div>
      
      <div class="row my-1">
        <div class="col"><?php echo $currency_split->display_count($currency_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CURRENCIES); ?></div>
        <div class="col text-right mr-2"><?php echo $currency_split->display_links($currency_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></div>
      </div>
      
      <?php
      if ( defined('MODULE_ADMIN_CURRENCIES_INSTALLED') && tep_not_null(MODULE_ADMIN_CURRENCIES_INSTALLED) ) {
        echo '<p class="mr-2">';
          echo tep_draw_bootstrap_button(IMAGE_UPDATE_CURRENCIES, 'fas fa-money-bill-alt', tep_href_link('currencies.php', 'action=update'), null, null, 'btn-success btn-block xxx text-white');
        echo '</p>';
      }
      else {
        echo '<div class="alert alert-warning mr-2">';
          echo ERROR_INSTALL_CURRENCY_CONVERTER;
        echo '</div>';
      }
      ?>

    </div>

<?php
  $heading = [];
  $contents = [];

  switch ($action) {
    case 'new':
      $heading[] = ['text' => TEXT_INFO_HEADING_NEW_CURRENCY];

      $contents = ['form' => tep_draw_form('currencies', 'currencies.php', 'page=' . (int)$_GET['page'] . (isset($cInfo) ? '&cID=' . $cInfo->currencies_id : '') . '&action=insert')];
      $contents[] = ['text' => TEXT_INFO_INSERT_INTRO];
      $contents[] = ['text' => tep_draw_pull_down_menu('cs', $currency_select_array, '', 'onchange="updateForm();"')];
      $contents[] = ['text' => TEXT_INFO_CURRENCY_TITLE . '<br>' . tep_draw_input_field('title')];
      $contents[] = ['text' => TEXT_INFO_CURRENCY_CODE . '<br>' . tep_draw_input_field('code')];
      $contents[] = ['text' => TEXT_INFO_CURRENCY_SYMBOL_LEFT . '<br>' . tep_draw_input_field('symbol_left')];
      $contents[] = ['text' => TEXT_INFO_CURRENCY_SYMBOL_RIGHT . '<br>' . tep_draw_input_field('symbol_right')];
      $contents[] = ['text' => TEXT_INFO_CURRENCY_DECIMAL_POINT . '<br>' . tep_draw_input_field('decimal_point')];
      $contents[] = ['text' => TEXT_INFO_CURRENCY_THOUSANDS_POINT . '<br>' . tep_draw_input_field('thousands_point')];
      $contents[] = ['text' => TEXT_INFO_CURRENCY_DECIMAL_PLACES . '<br>' . tep_draw_input_field('decimal_places')];
      $contents[] = ['text' => TEXT_INFO_CURRENCY_VALUE . '<br>' . tep_draw_input_field('value')];
      $contents[] = ['text' => tep_draw_checkbox_field('default') . ' ' . TEXT_INFO_SET_AS_DEFAULT];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success xxx text-white mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('currencies.php'), null, null, 'btn-light')];
      break;
    case 'edit':
      $heading[] = ['text' => TEXT_INFO_HEADING_EDIT_CURRENCY];

      $contents = ['form' => tep_draw_form('currencies', 'currencies.php', 'page=' . (int)$_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=save')];
      $contents[] = ['text' => TEXT_INFO_EDIT_INTRO];
      $contents[] = ['text' => TEXT_INFO_CURRENCY_TITLE . '<br>' . tep_draw_input_field('title', $cInfo->title)];
      $contents[] = ['text' => TEXT_INFO_CURRENCY_CODE . '<br>' . tep_draw_input_field('code', $cInfo->code)];
      $contents[] = ['text' => TEXT_INFO_CURRENCY_SYMBOL_LEFT . '<br>' . tep_draw_input_field('symbol_left', $cInfo->symbol_left)];
      $contents[] = ['text' => TEXT_INFO_CURRENCY_SYMBOL_RIGHT . '<br>' . tep_draw_input_field('symbol_right', $cInfo->symbol_right)];
      $contents[] = ['text' => TEXT_INFO_CURRENCY_DECIMAL_POINT . '<br>' . tep_draw_input_field('decimal_point', $cInfo->decimal_point)];
      $contents[] = ['text' => TEXT_INFO_CURRENCY_THOUSANDS_POINT . '<br>' . tep_draw_input_field('thousands_point', $cInfo->thousands_point)];
      $contents[] = ['text' => TEXT_INFO_CURRENCY_DECIMAL_PLACES . '<br>' . tep_draw_input_field('decimal_places', $cInfo->decimal_places)];
      $contents[] = ['text' => TEXT_INFO_CURRENCY_VALUE . '<br>' . tep_draw_input_field('value', $cInfo->value)];
      if (DEFAULT_CURRENCY != $cInfo->code) $contents[] = ['text' => tep_draw_checkbox_field('default') . ' ' . TEXT_INFO_SET_AS_DEFAULT];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success xxx text-white mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('currencies.php', 'page=' . (int)$_GET['page'] . '&cID=' . $cInfo->currencies_id), null, null, 'btn-light')];
      break;
    case 'delete':
      $heading[] = ['text' => TEXT_INFO_HEADING_DELETE_CURRENCY];

      $contents[] = ['text' => TEXT_INFO_DELETE_INTRO];
      $contents[] = ['class' => 'text-center text-uppercase font-weight-bold', 'text' => $cInfo->title];
      $contents[] = ['class' => 'text-center', 'text' => (($remove_currency) ? tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', tep_href_link('currencies.php', 'page=' . (int)$_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=deleteconfirm'), null, null, 'btn-danger xxx text-white mr-2') : '') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('currencies.php', 'page=' . (int)$_GET['page'] . '&cID=' . $cInfo->currencies_id), null, null, 'btn-light')];
      break;
    default:
      if (is_object($cInfo)) {
        $heading[] = ['text' => $cInfo->title];

        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_EDIT, 'fas fa-cogs', tep_href_link('currencies.php', 'page=' . (int)$_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=edit'), null, null, 'btn-warning mr-2') . tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', tep_href_link('currencies.php', 'page=' . (int)$_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=delete'), null, null, 'btn-danger xxx text-white')];
        $contents[] = ['text' => sprintf(TEXT_INFO_CURRENCY_TITLE, $cInfo->title)];
        $contents[] = ['text' => sprintf(TEXT_INFO_CURRENCY_CODE, $cInfo->code)];
        $contents[] = ['text' => sprintf(TEXT_INFO_CURRENCY_SYMBOL_LEFT, $cInfo->symbol_left)];
        $contents[] = ['text' => sprintf(TEXT_INFO_CURRENCY_SYMBOL_RIGHT, $cInfo->symbol_right)];
        $contents[] = ['text' => sprintf(TEXT_INFO_CURRENCY_DECIMAL_POINT, $cInfo->decimal_point)];
        $contents[] = ['text' => sprintf(TEXT_INFO_CURRENCY_THOUSANDS_POINT, $cInfo->thousands_point)];
        $contents[] = ['text' => sprintf(TEXT_INFO_CURRENCY_DECIMAL_PLACES, $cInfo->decimal_places)];
        $contents[] = ['text' => sprintf(TEXT_INFO_CURRENCY_LAST_UPDATED, tep_date_short($cInfo->last_updated))];
        $contents[] = ['text' => sprintf(TEXT_INFO_CURRENCY_VALUE, number_format($cInfo->value, 8))];
        $contents[] = ['text' => sprintf(TEXT_INFO_CURRENCY_EXAMPLE, $currencies->format('30', false, DEFAULT_CURRENCY), $currencies->format('30', true, $cInfo->code))];
      }
      break;
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '<div class="col-12 col-sm-3">';
      $box = new box;
      echo $box->infoBox($heading, $contents);
    echo '</div>';
  }
?>

  </div>
  
<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
