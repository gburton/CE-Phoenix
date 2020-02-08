<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  $action = ($_GET['action'] ?? '');

  $OSCOM_Hooks->call('customers', 'preAction');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'update':
        $customer_details = $this->process();

        if (!empty($customer_details)) {
          if (empty($customer_details['password'])) {
            unset($customer_details['password']);
          }
          $this->save($customer_details);
          tep_db_query("UPDATE customers_info SET customers_info_date_account_last_modified = NOW() WHERE customers_info_id = " . (int)$customer_details['customers_id']);

          $OSCOM_Hooks->call('customers', 'afterUpdate');

          tep_redirect(tep_href_link('customers.php', tep_get_all_get_params(['cID', 'action']) . 'cID=' . $customers_id));
        }

        // if we reach here, we did not redirect, so there was some kind of error
        $action = 'edit';
        break;
      case 'deleteconfirm':
        $customers_id = tep_db_prepare_input($_GET['cID']);

        if (isset($_POST['delete_reviews']) && ($_POST['delete_reviews'] == 'on')) {
          tep_db_query("DELETE r, rd FROM reviews r LEFT JOIN reviews_description rd ON r.reviews_id = rd.reviews_id WHERE r.customers_id = " . (int)$customers_id);
        } else {
          tep_db_query("UPDATE reviews SET customers_id = NULL WHERE customers_id = " . (int)$customers_id);
        }

        tep_db_query("DELETE FROM address_book WHERE customers_id = " . (int)$customers_id);
        tep_db_query("DELETE FROM customers WHERE customers_id = " . (int)$customers_id);
        tep_db_query("DELETE FROM customer_data WHERE customers_id = " . (int)$customers_id);
        tep_db_query("DELETE FROM customers_info WHERE customers_info_id = " . (int)$customers_id);
        tep_db_query("DELETE FROM customers_basket WHERE customers_id = " . (int)$customers_id);
        tep_db_query("DELETE FROM customers_basket_attributes WHERE customers_id = " . (int)$customers_id);
        tep_db_query("DELETE FROM whos_online WHERE customer_id = " . (int)$customers_id);

        $OSCOM_Hooks->call('customers', 'afterDelete');

        tep_redirect(tep_href_link('customers.php', tep_get_all_get_params(['cID', 'action'])));
        break;
      default:
        $customer_details_query = tep_db_query($customer_data->build_read($customer_data->list_all_capabilities(), 'both', [ 'id' => (int)$_GET['cID'] ]));
        $customer_details = tep_db_fetch_array($customer_details_query);
    }
  }

  $OSCOM_Hooks->call('customers', 'postAction');

  require 'includes/template_top.php';
?>
<h1 class="pageHeading"><?php echo HEADING_TITLE; ?></h1>
<div class="contentContainer">
<?php
  if ($action == 'edit' || $action == 'update') {
    $oscTemplate = new oscTemplate();
    echo tep_draw_form('customers', 'customers.php', tep_get_all_get_params(['action']) . 'action=update', 'post', 'onsubmit="return check_form();"')
       . tep_draw_hidden_field('default_address_id', $customer_data->get('address_id', $customer_details));

    $cwd = getcwd();
    chdir(DIR_FS_CATALOG);
    $grouped_modules = $customer_data->get_grouped_modules();
    $customer_data_group_query = tep_db_query(<<<'EOSQL'
SELECT customer_data_groups_id, customer_data_groups_name
 FROM customer_data_groups
 WHERE language_id =
EOSQL
      . (int)$languages_id . ' ORDER BY cdg_vertical_sort_order, cdg_horizontal_sort_order');

    while ($customer_data_group = tep_db_fetch_array($customer_data_group_query)) {
      if (empty($grouped_modules[$customer_data_group['customer_data_groups_id']])) {
        continue;
      }
?>
  <h2 class="h4"><?php echo $customer_data_group['customer_data_groups_name']; ?></h2>
<?php
      foreach ((array)$grouped_modules[$customer_data_group['customer_data_groups_id']] as $module) {
        $module->display_input($customer_details);
      }
    }
    chdir($cwd);
?>
    <div class="buttonSet">
      <div class="text-right"><?php echo tep_draw_button(IMAGE_SAVE, 'disk', null, 'primary') . tep_draw_button(IMAGE_CANCEL, 'close', tep_href_link('customers.php', tep_get_all_get_params(array('action')))); ?></div>
    </div>
  </form>
</div>
<?php
  } else {
?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            <td class="smallText" align="right"><?php

    echo tep_draw_form('search', 'customers.php', '', 'get');
    echo HEADING_TITLE_SEARCH . ' ' . tep_draw_input_field('search');
    echo tep_hide_session_id();

   ?></form></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_NAME; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACCOUNT_CREATED; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $customers_query_raw = $customer_data->build_read([ 'id', 'sortable_name', 'email_address', 'country_id' ], 'customers');
    $keywords = null;
    if (tep_not_null($_GET['search'] ?? '')) {
      $keywords = tep_db_prepare_input($_GET['search']);
      $customer_data->add_search_criteria($customers_query_raw, $keywords);
    }
    $customers_query_raw = $customer_data->add_order_by($customers_query_raw, ['sortable_name']);

    $customers_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $customers_query_raw, $customers_query_numrows);
    $customers_query = tep_db_query($customers_query_raw);
    while ($customers = tep_db_fetch_array($customers_query)) {
      $info_query = tep_db_query(<<<'EOSQL'
SELECT customers_info_date_account_created AS date_account_created,
       customers_info_date_account_last_modified AS date_account_last_modified,
       customers_info_date_of_last_logon AS date_last_logon,
       customers_info_number_of_logons AS number_of_logons
 FROM customers_info
 WHERE customers_info_id = 
EOSQL
        . (int)$customer_data->get('id', $customers));
      $info = tep_db_fetch_array($info_query);

      if (!isset($cInfo) && (!isset($_GET['cID']) || ($_GET['cID'] === $customer_data->get('id', $customers)))) {
        $reviews_query = tep_db_query("SELECT COUNT(*) AS number_of_reviews FROM reviews WHERE customers_id = " . (int)$customer_data->get('id', $customers));
        $reviews = tep_db_fetch_array($reviews_query);

        $icon = tep_image('images/icon_arrow_right.gif', '');

        $cInfo_array = array_merge($customers, (array)$info, $reviews);

        // preload necessary fields not already used
        $customer_data->get([ 'sortable_name', 'name', 'email_address', 'country_id', 'id' ], $cInfo_array);
        $cInfo = new objectInfo($cInfo_array);
?>
              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href='<?php echo tep_href_link('customers.php', tep_get_all_get_params(['cID', 'action']) . 'cID=' . $cInfo->customers_id . '&action=edit'); ?>'">
<?php
      } else {
        $href = tep_href_link('customers.php', tep_get_all_get_params(['cID']) . 'cID=' . $customer_data->get('id', $customers));
        $icon = '<a href="' . $href . '">' . tep_image('images/icon_info.gif', IMAGE_ICON_INFO) . '</a>';
?>
              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href='<?php echo $href; ?>'">
<?php
      }
?>
                <td class="dataTableContent"><?php echo $customer_data->get('sortable_name', $customers); ?></td>
                <td class="dataTableContent" align="right"><?php echo tep_date_short($info['date_account_created']); ?></td>
                <td class="dataTableContent" align="right"><?php echo $icon; ?>&nbsp;</td>
              </tr>
<?php
    }
?>
              <tr>
                <td colspan="4"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $customers_split->display_count($customers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
                    <td class="smallText" align="right"><?php echo $customers_split->display_links($customers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(['page', 'info', 'x', 'y', 'cID'])); ?></td>
                  </tr>
<?php
    if (isset($keywords)) {
?>
                  <tr>
                    <td class="smallText" align="right" colspan="2"><?php echo tep_draw_button(IMAGE_RESET, 'arrowrefresh-1-w', tep_href_link('customers.php')); ?></td>
                  </tr>
<?php
    }
?>
                </table></td>
              </tr>
            </table></td>
<?php
    $heading = [];
    $contents = [];

    switch ($action) {
      case 'confirm':
        $heading[] = ['text' => '<strong>' . TEXT_INFO_HEADING_DELETE_CUSTOMER . '</strong>'];

        $contents = ['form' => tep_draw_form('customers', 'customers.php', tep_get_all_get_params(['cID', 'action']) . 'cID=' . $cInfo->id . '&action=deleteconfirm')];
        $contents[] = ['text' => TEXT_DELETE_INTRO . '<br /><br /><strong>' . $cInfo->name . '</strong>'];
        if (isset($cInfo->number_of_reviews) && ($cInfo->number_of_reviews) > 0) $contents[] = ['text' => '<br />' . tep_draw_checkbox_field('delete_reviews', 'on', true) . ' ' . sprintf(TEXT_DELETE_REVIEWS, $cInfo->number_of_reviews)];
        $contents[] = [
          'align' => 'center',
          'text' => '<br />'
                  . tep_draw_button(IMAGE_DELETE, 'trash', null, 'primary')
                  . tep_draw_button(IMAGE_CANCEL, 'close', tep_href_link('customers.php', tep_get_all_get_params(['cID', 'action']) . 'cID=' . $cInfo->id)),
        ];
        break;
      default:
        if (($cInfo ?? null) instanceof objectInfo) {
          $heading[] = ['text' => '<strong>' . $cInfo->name . '</strong>'];

          $contents[] = [
            'align' => 'center',
            'text' => tep_draw_button(IMAGE_EDIT, 'document', tep_href_link('customers.php', tep_get_all_get_params(['cID', 'action']) . 'cID=' . $cInfo->id . '&action=edit'))
                    . tep_draw_button(IMAGE_DELETE, 'trash', tep_href_link('customers.php', tep_get_all_get_params(['cID', 'action']) . 'cID=' . $cInfo->id . '&action=confirm'))
                    . tep_draw_button(IMAGE_ORDERS, 'cart', tep_href_link('orders.php', 'cID=' . $cInfo->id))
                    . tep_draw_button(IMAGE_EMAIL, 'mail-closed', tep_href_link('mail.php', 'customer=' . urlencode($cInfo->email_address))),
          ];
          $contents[] = ['text' => '<br />' . TEXT_DATE_ACCOUNT_CREATED . ' ' . tep_date_short($cInfo->date_account_created)];
          $contents[] = ['text' => '<br />' . TEXT_DATE_ACCOUNT_LAST_MODIFIED . ' ' . tep_date_short($cInfo->date_account_last_modified)];
          $contents[] = ['text' => '<br />' . TEXT_INFO_DATE_LAST_LOGON . ' '  . tep_date_short($cInfo->date_last_logon)];
          $contents[] = ['text' => '<br />' . TEXT_INFO_NUMBER_OF_LOGONS . ' ' . $cInfo->number_of_logons];

          if ($customer_data->has('country_name') && isset($cInfo->country_id)) {
            $country_query = tep_db_query("SELECT * FROM countries WHERE countries_id = " . (int)$cInfo->country_id);
            $country = (array)tep_db_fetch_array($country_query);

            $contents[] = ['text' => '<br />' . TEXT_INFO_COUNTRY . ' ' . $country['countries_name']];
          }

          $contents[] = ['text' => '<br />' . TEXT_INFO_NUMBER_OF_REVIEWS . ' ' . $cInfo->number_of_reviews];
        }
        break;
    }

    if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
      echo '            <td width="25%" valign="top">' . "\n";

      $box = new box();
      echo $box->infoBox($heading, $contents);

      echo '            </td>' . "\n";
    }
?>
          </tr>
        </table></td>
      </tr>
    </table>
<?php
  }

  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
