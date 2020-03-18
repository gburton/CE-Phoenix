<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $action = $_GET['action'] ?? '';
  
  $OSCOM_Hooks->call('countries', 'countriesPreAction');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'insert':
        $countries_name = tep_db_prepare_input($_POST['countries_name']);
        $countries_iso_code_2 = tep_db_prepare_input($_POST['countries_iso_code_2']);
        $countries_iso_code_3 = tep_db_prepare_input($_POST['countries_iso_code_3']);
        $address_format_id = tep_db_prepare_input($_POST['address_format_id']);

        tep_db_query("insert into countries (countries_name, countries_iso_code_2, countries_iso_code_3, address_format_id) values ('" . tep_db_input($countries_name) . "', '" . tep_db_input($countries_iso_code_2) . "', '" . tep_db_input($countries_iso_code_3) . "', '" . (int)$address_format_id . "')");
        
        $OSCOM_Hooks->call('countries', 'countriesActionInsert');

        tep_redirect(tep_href_link('countries.php'));
        break;
      case 'save':
        $countries_id = tep_db_prepare_input($_GET['cID']);
        $countries_name = tep_db_prepare_input($_POST['countries_name']);
        $countries_iso_code_2 = tep_db_prepare_input($_POST['countries_iso_code_2']);
        $countries_iso_code_3 = tep_db_prepare_input($_POST['countries_iso_code_3']);
        $address_format_id = tep_db_prepare_input($_POST['address_format_id']);

        tep_db_query("update countries set countries_name = '" . tep_db_input($countries_name) . "', countries_iso_code_2 = '" . tep_db_input($countries_iso_code_2) . "', countries_iso_code_3 = '" . tep_db_input($countries_iso_code_3) . "', address_format_id = '" . (int)$address_format_id . "' where countries_id = '" . (int)$countries_id . "'");
        
        $OSCOM_Hooks->call('countries', 'countriesActionSave');

        tep_redirect(tep_href_link('countries.php', 'page=' . $_GET['page'] . '&cID=' . $countries_id));
        break;
      case 'deleteconfirm':
        $countries_id = tep_db_prepare_input($_GET['cID']);

        tep_db_query("delete from countries where countries_id = '" . (int)$countries_id . "'");
        
        $OSCOM_Hooks->call('countries', 'countriesActionDelete');

        tep_redirect(tep_href_link('countries.php', 'page=' . $_GET['page']));
        break;
    }
  }
  
  $OSCOM_Hooks->call('countries', 'countriesPostAction');

  require('includes/template_top.php');
?>

  <div class="row">
    <div class="col">
      <h1 class="display-4 mb-2"><?php echo HEADING_TITLE; ?></h1>
    </div>
    <div class="col text-right align-self-center">
      <?php
      if (empty($action)) {
        echo tep_draw_bootstrap_button(IMAGE_NEW_COUNTRY, 'fas fa-map-marker-alt', tep_href_link('countries.php', 'action=new'), null, null, 'btn-danger xxx text-white');
      }
      else {
        echo tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link('countries.php'), null, null, 'btn-light');
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
              <th><?php echo TABLE_HEADING_COUNTRY_NAME; ?></th>
              <th colspan="2"><?php echo TABLE_HEADING_COUNTRY_CODES; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_ACTION; ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $countries_query_raw = "select countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, address_format_id from countries order by countries_name";
            $countries_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $countries_query_raw, $countries_query_numrows);
            $countries_query = tep_db_query($countries_query_raw);
            while ($countries = tep_db_fetch_array($countries_query)) {
              if ((!isset($_GET['cID']) || (isset($_GET['cID']) && ($_GET['cID'] == $countries['countries_id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
                $cInfo = new objectInfo($countries);
              }

              if (isset($cInfo) && is_object($cInfo) && ($countries['countries_id'] == $cInfo->countries_id)) {
                echo '<tr class="table-active" onclick="document.location.href=\'' . tep_href_link('countries.php', 'page=' . $_GET['page'] . '&cID=' . (int)$cInfo->countries_id . '&action=edit') . '\'">';
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('countries.php', 'page=' . $_GET['page'] . '&cID=' . (int)$countries['countries_id']) . '\'">';
              }
              ?>
                <td><?php echo $countries['countries_name']; ?></td>
                <td><?php echo $countries['countries_iso_code_2']; ?></td>
                <td><?php echo $countries['countries_iso_code_3']; ?></td>
                <td class="text-right"><?php if (isset($cInfo) && is_object($cInfo) && ($countries['countries_id'] == $cInfo->countries_id) ) { echo '<i class="fas fa-chevron-circle-right text-info"></i>'; } else { echo '<a href="' . tep_href_link('countries.php', 'page=' . $_GET['page'] . '&cID=' . $countries['countries_id']) . '"><i class="fas fa-info-circle text-muted"></i></a>'; } ?></td>
              </tr>
              <?php
            }
            ?>
          </tbody>
        </table>
      </div>

      <div class="row my-1">
        <div class="col"><?php echo $countries_split->display_count($countries_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_COUNTRIES); ?></div>
        <div class="col text-right mr-2"><?php echo $countries_split->display_links($countries_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></div>
      </div>
    </div>

<?php
  $heading = [];
  $contents = [];

  switch ($action) {
    case 'new':
      $heading[] = ['text' => TEXT_INFO_HEADING_NEW_COUNTRY];

      $contents = ['form' => tep_draw_form('countries', 'countries.php', 'page=' . $_GET['page'] . '&action=insert')];
      $contents[] = ['text' => TEXT_INFO_INSERT_INTRO];
      $contents[] = ['text' => TEXT_INFO_COUNTRY_NAME . '<br>' . tep_draw_input_field('countries_name')];
      $contents[] = ['text' => TEXT_INFO_COUNTRY_CODE_2 . '<br>' . tep_draw_input_field('countries_iso_code_2')];
      $contents[] = ['text' => TEXT_INFO_COUNTRY_CODE_3 . '<br>' . tep_draw_input_field('countries_iso_code_3')];
      $contents[] = ['text' => TEXT_INFO_ADDRESS_FORMAT . '<br>' . tep_draw_pull_down_menu('address_format_id', tep_get_address_formats())];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success xxx text-white mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('countries.php', 'page=' . $_GET['page']), null, null, 'btn-light')];
      break;
    case 'edit':
      $heading[] = ['text' => TEXT_INFO_HEADING_EDIT_COUNTRY];

      $contents = ['form' => tep_draw_form('countries', 'countries.php', 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id . '&action=save')];
      $contents[] = ['text' => TEXT_INFO_EDIT_INTRO];
      $contents[] = ['text' => TEXT_INFO_COUNTRY_NAME . '<br>' . tep_draw_input_field('countries_name', $cInfo->countries_name)];
      $contents[] = ['text' => TEXT_INFO_COUNTRY_CODE_2 . '<br>' . tep_draw_input_field('countries_iso_code_2', $cInfo->countries_iso_code_2)];
      $contents[] = ['text' => TEXT_INFO_COUNTRY_CODE_3 . '<br>' . tep_draw_input_field('countries_iso_code_3', $cInfo->countries_iso_code_3)];
      $contents[] = ['text' => TEXT_INFO_ADDRESS_FORMAT . '<br>' . tep_draw_pull_down_menu('address_format_id', tep_get_address_formats(), $cInfo->address_format_id)];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success xxx text-white mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('countries.php', 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id), null, null, 'btn-light')];
      break;
    case 'delete':
      $heading[] = ['text' => TEXT_INFO_HEADING_DELETE_COUNTRY];

      $contents = ['form' => tep_draw_form('countries', 'countries.php', 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id . '&action=deleteconfirm')];
      $contents[] = ['text' => TEXT_INFO_DELETE_INTRO];
      $contents[] = ['class' => 'text-center text-uppercase font-weight-bold', 'text' => $cInfo->countries_name];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', null, 'primary', null, 'btn-danger xxx text-white mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('countries.php', 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id), null, null, 'btn-light')];
      break;
    default:
      if (is_object($cInfo)) {
        $heading[] = ['text' => $cInfo->countries_name];

        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_EDIT, 'fas fa-cogs', tep_href_link('countries.php', 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id . '&action=edit'), null, null, 'btn-warning mr-2') . tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', tep_href_link('countries.php', 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id . '&action=delete'), null, null, 'btn-danger xxx text-white')];
        $contents[] = ['text' => sprintf(TEXT_INFO_COUNTRY_NAME, $cInfo->countries_name)];
        $contents[] = ['text' => sprintf(TEXT_INFO_COUNTRY_CODE_2, $cInfo->countries_iso_code_2)];
        $contents[] = ['text' => sprintf(TEXT_INFO_COUNTRY_CODE_3, $cInfo->countries_iso_code_3)];
        $contents[] = ['text' => sprintf(TEXT_INFO_ADDRESS_FORMAT, $cInfo->address_format_id)];
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
