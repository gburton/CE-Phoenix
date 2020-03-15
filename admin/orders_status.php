<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2013 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $action = $_GET['action'] ?? '';
  
  $OSCOM_Hooks->call('orders_status', 'PreAction');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'insert':
      case 'save':
        if (isset($_GET['oID'])) $orders_status_id = tep_db_prepare_input($_GET['oID']);

        $languages = tep_get_languages();
        for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
          $orders_status_name_array = $_POST['orders_status_name'];
          $language_id = $languages[$i]['id'];

          $sql_data_array = ['orders_status_name' => tep_db_prepare_input($orders_status_name_array[$language_id]),
                             'public_flag' => ((isset($_POST['public_flag']) && ($_POST['public_flag'] == '1')) ? '1' : '0'),
                             'downloads_flag' => ((isset($_POST['downloads_flag']) && ($_POST['downloads_flag'] == '1')) ? '1' : '0')];

          if ($action == 'insert') {
            if (empty($orders_status_id)) {
              $next_id_query = tep_db_query("select max(orders_status_id) as orders_status_id from orders_status");
              $next_id = tep_db_fetch_array($next_id_query);
              $orders_status_id = $next_id['orders_status_id'] + 1;
            }

            $insert_sql_data = ['orders_status_id' => $orders_status_id, 'language_id' => $language_id];

            $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

            tep_db_perform('orders_status', $sql_data_array);
          } elseif ($action == 'save') {
            tep_db_perform('orders_status', $sql_data_array, 'update', "orders_status_id = '" . (int)$orders_status_id . "' and language_id = '" . (int)$language_id . "'");
          }
        }

        if (isset($_POST['default']) && ($_POST['default'] == 'on')) {
          tep_db_query("update configuration set configuration_value = '" . tep_db_input($orders_status_id) . "' where configuration_key = 'DEFAULT_ORDERS_STATUS_ID'");
        }
        
        $OSCOM_Hooks->call('orders_status', 'InsertSave');

        tep_redirect(tep_href_link('orders_status.php', 'page=' . $_GET['page'] . '&oID=' . $orders_status_id));
        break;
      case 'deleteconfirm':
        $oID = tep_db_prepare_input($_GET['oID']);

        $orders_status_query = tep_db_query("select configuration_value from configuration where configuration_key = 'DEFAULT_ORDERS_STATUS_ID'");
        $orders_status = tep_db_fetch_array($orders_status_query);

        if ($orders_status['configuration_value'] == $oID) {
          tep_db_query("update configuration set configuration_value = '' where configuration_key = 'DEFAULT_ORDERS_STATUS_ID'");
        }

        tep_db_query("delete from orders_status where orders_status_id = '" . tep_db_input($oID) . "'");
        
        $OSCOM_Hooks->call('orders_status', 'DeleteConfirm');

        tep_redirect(tep_href_link('orders_status.php', 'page=' . $_GET['page']));
        break;
      case 'delete':
        $oID = tep_db_prepare_input($_GET['oID']);

        $status_query = tep_db_query("select count(*) as count from orders where orders_status = '" . (int)$oID . "'");
        $status = tep_db_fetch_array($status_query);

        $remove_status = true;
        if ($oID == DEFAULT_ORDERS_STATUS_ID) {
          $remove_status = false;
          $messageStack->add(ERROR_REMOVE_DEFAULT_ORDER_STATUS, 'error');
        } elseif ($status['count'] > 0) {
          $remove_status = false;
          $messageStack->add(ERROR_STATUS_USED_IN_ORDERS, 'error');
        } else {
          $history_query = tep_db_query("select count(*) as count from orders_status_history where orders_status_id = '" . (int)$oID . "'");
          $history = tep_db_fetch_array($history_query);
          if ($history['count'] > 0) {
            $remove_status = false;
            $messageStack->add(ERROR_STATUS_USED_IN_HISTORY, 'error');
          }
        }
        
        $OSCOM_Hooks->call('orders_status', 'Delete');
        
        break;
    }
  }
  
  $OSCOM_Hooks->call('orders_status', 'PostAction');

  require('includes/template_top.php');
?>

  <div class="row">
    <div class="col">
      <h1 class="display-4 mb-2"><?php echo HEADING_TITLE; ?></h1>
    </div>
    <div class="col text-right align-self-center">
      <?php
      if (empty($action)) {
        echo tep_draw_bootstrap_button(IMAGE_INSERT, 'fas fa-cogs', tep_href_link('orders_status.php', 'action=new'), null, null, 'btn-danger xxx text-white');
      }
      else {
        echo tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link('orders_status.php'), null, null, 'btn-light');
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
              <th><?php echo TABLE_HEADING_ORDERS_STATUS; ?></th>
              <th class="text-center"><?php echo TABLE_HEADING_PUBLIC_STATUS; ?></th>
              <th class="text-center"><?php echo TABLE_HEADING_DOWNLOADS_STATUS; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_ACTION; ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $orders_status_query_raw = "select * from orders_status where language_id = '" . (int)$languages_id . "' order by orders_status_id";
            $orders_status_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $orders_status_query_raw, $orders_status_query_numrows);
            $orders_status_query = tep_db_query($orders_status_query_raw);
            while ($orders_status = tep_db_fetch_array($orders_status_query)) {
              if ((!isset($_GET['oID']) || (isset($_GET['oID']) && ($_GET['oID'] == $orders_status['orders_status_id']))) && !isset($oInfo) && (substr($action, 0, 3) != 'new')) {
                $oInfo = new objectInfo($orders_status);
              }

              if (isset($oInfo) && is_object($oInfo) && ($orders_status['orders_status_id'] == $oInfo->orders_status_id)) {
                echo '<tr class="table-active" onclick="document.location.href=\'' . tep_href_link('orders_status.php', 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id . '&action=edit') . '\'">';
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('orders_status.php', 'page=' . $_GET['page'] . '&oID=' . $orders_status['orders_status_id']) . '\'">';
              }

              if (DEFAULT_ORDERS_STATUS_ID == $orders_status['orders_status_id']) {
                echo '<th>' . $orders_status['orders_status_name'] . ' (' . TEXT_DEFAULT . ')</th>';
              } else {
                echo '<td>' . $orders_status['orders_status_name'] . '</td>';
              }
              ?>
                <td class="text-center"><?php echo ($orders_status['public_flag'] == '1') ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>'; ?></td>
                <td class="text-center"><?php echo ($orders_status['downloads_flag'] == '1') ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>'; ?></td>
                <td class="text-right"><?php if (isset($oInfo) && is_object($oInfo) && ($orders_status['orders_status_id'] == $oInfo->orders_status_id)) { echo '<i class="fas fa-chevron-circle-right text-info"></i>'; } else { echo '<a href="' . tep_href_link('orders_status.php', 'page=' . $_GET['page'] . '&oID=' . $orders_status['orders_status_id']) . '"><i class="fas fa-info-circle text-muted"></i></a>'; } ?></td>
              </tr>
              <?php
            }
            ?>
          </tbody>
        </table>
      </div>
      
      <div class="row my-1">
        <div class="col"><?php echo $orders_status_split->display_count($orders_status_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ORDERS_STATUS); ?></div>
        <div class="col text-right mr-2"><?php echo $orders_status_split->display_links($orders_status_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></div>
      </div>
      
    </div>

<?php
  $heading = [];
  $contents = [];

  switch ($action) {
    case 'new':
      $heading[] = ['text' => TEXT_INFO_HEADING_NEW_ORDERS_STATUS];

      $contents = ['form' => tep_draw_form('status', 'orders_status.php', 'page=' . $_GET['page'] . '&action=insert')];
      $contents[] = ['text' => TEXT_INFO_INSERT_INTRO];

      $orders_status_inputs_string = '';
      $languages = tep_get_languages();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $orders_status_inputs_string .= '<div class="input-group"><div class="input-group-prepend"><span class="input-group-text">' . tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']) . '</span></div>' . tep_draw_input_field('orders_status_name[' . $languages[$i]['id'] . ']') . '</div>';
      }

      $contents[] = ['text' => TEXT_INFO_ORDERS_STATUS_NAME . $orders_status_inputs_string];
      $contents[] = ['text' => tep_draw_checkbox_field('public_flag', '1') . ' ' . TEXT_SET_PUBLIC_STATUS];
      $contents[] = ['text' => tep_draw_checkbox_field('downloads_flag', '1') . ' ' . TEXT_SET_DOWNLOADS_STATUS];
      $contents[] = ['text' => tep_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success xxx text-white mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('orders_status.php', 'page=' . $_GET['page']), null, null, 'btn-light')];
      break;
    case 'edit':
      $heading[] = ['text' => TEXT_INFO_HEADING_EDIT_ORDERS_STATUS];

      $contents = ['form' => tep_draw_form('status', 'orders_status.php', 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id  . '&action=save')];
      $contents[] = ['text' => TEXT_INFO_EDIT_INTRO];

      $orders_status_inputs_string = '';
      $languages = tep_get_languages();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $orders_status_inputs_string .= '<div class="input-group"><div class="input-group-prepend"><span class="input-group-text">' . tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']) . '</span></div>' . tep_draw_input_field('orders_status_name[' . $languages[$i]['id'] . ']', tep_get_orders_status_name($oInfo->orders_status_id, $languages[$i]['id'])) . '</div>';
      }

      $contents[] = ['text' => TEXT_INFO_ORDERS_STATUS_NAME . $orders_status_inputs_string];
      $contents[] = ['text' => tep_draw_checkbox_field('public_flag', '1', $oInfo->public_flag) . ' ' . TEXT_SET_PUBLIC_STATUS];
      $contents[] = ['text' => tep_draw_checkbox_field('downloads_flag', '1', $oInfo->downloads_flag) . ' ' . TEXT_SET_DOWNLOADS_STATUS];
      if (DEFAULT_ORDERS_STATUS_ID != $oInfo->orders_status_id) $contents[] = ['text' => '<br>' . tep_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success xxx text-white mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('orders_status.php', 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id), null, null, 'btn-light')];
      break;
    case 'delete':
      $heading[] = ['text' => TEXT_INFO_HEADING_DELETE_ORDERS_STATUS];

      $contents = ['form' => tep_draw_form('status', 'orders_status.php', 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id  . '&action=deleteconfirm')];
      $contents[] = ['text' => TEXT_INFO_DELETE_INTRO];
      $contents[] = ['class' => 'text-center text-uppercase font-weight-bold', 'text' => $oInfo->orders_status_name];
      if ($remove_status) $contents[] = ['class' => 'text-center', 'text' => '<br>' . tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', null, null, null, 'btn-danger xxx text-white mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('orders_status.php', 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id), null, null, 'btn-light')];
      break;
    default:
      if (isset($oInfo) && is_object($oInfo)) {
        $heading[] = ['text' => $oInfo->orders_status_name];

        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_EDIT, 'fas fa-cogs',  tep_href_link('orders_status.php', 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id . '&action=edit'), null, null, 'btn-warning mr-2') . tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', tep_href_link('orders_status.php', 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id . '&action=delete'), null, null, 'btn-danger xxx text-white')];

        $orders_status_inputs_string = '';
        $languages = tep_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $orders_status_inputs_string .= '<br>' . tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']) . '&nbsp;' . tep_get_orders_status_name($oInfo->orders_status_id, $languages[$i]['id']);
        }

        $contents[] = ['text' => $orders_status_inputs_string];
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
