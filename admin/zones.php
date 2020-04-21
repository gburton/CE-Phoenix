<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $action = $_GET['action'] ?? '';

  if (tep_not_null($action)) {
    switch ($action) {
      case 'insert':
        $zone_country_id = tep_db_prepare_input($_POST['zone_country_id']);
        $zone_code = tep_db_prepare_input($_POST['zone_code']);
        $zone_name = tep_db_prepare_input($_POST['zone_name']);

        tep_db_query("insert into " . TABLE_ZONES . " (zone_country_id, zone_code, zone_name) values ('" . (int)$zone_country_id . "', '" . tep_db_input($zone_code) . "', '" . tep_db_input($zone_name) . "')");

        tep_redirect(tep_href_link('zones.php'));
        break;
      case 'save':
        $zone_id = tep_db_prepare_input($_GET['cID']);
        $zone_country_id = tep_db_prepare_input($_POST['zone_country_id']);
        $zone_code = tep_db_prepare_input($_POST['zone_code']);
        $zone_name = tep_db_prepare_input($_POST['zone_name']);

        tep_db_query("update " . TABLE_ZONES . " set zone_country_id = '" . (int)$zone_country_id . "', zone_code = '" . tep_db_input($zone_code) . "', zone_name = '" . tep_db_input($zone_name) . "' where zone_id = '" . (int)$zone_id . "'");

        tep_redirect(tep_href_link('zones.php', 'page=' . (int)$_GET['page'] . '&cID=' . $zone_id));
        break;
      case 'deleteconfirm':
        $zone_id = tep_db_prepare_input($_GET['cID']);

        tep_db_query("delete from " . TABLE_ZONES . " where zone_id = '" . (int)$zone_id . "'");

        tep_redirect(tep_href_link('zones.php', 'page=' . (int)$_GET['page']));
        break;
    }
  }

  require('includes/template_top.php');
?>

  <div class="row">
    <div class="col"><h1 class="display-4 mb-2"><?php echo HEADING_TITLE; ?></h1></div>
    <div class="col text-right align-self-center">
      <?php
      if (empty($action)) {
        echo tep_draw_bootstrap_button(IMAGE_NEW_ZONE, 'fas fa-map-marker-alt', tep_href_link('zones.php', 'action=new'), null, null, 'btn-danger xxx text-white');
      }
      else {
        echo tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-angle-left', tep_href_link('zones.php'), null, null, 'btn-light mt-2');
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
              <th><?php echo TABLE_HEADING_ZONE_NAME; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_ZONE_CODE; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_ACTION; ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $zones_query_raw = "select z.zone_id, c.countries_id, c.countries_name, z.zone_name, z.zone_code, z.zone_country_id from " . TABLE_ZONES . " z, " . TABLE_COUNTRIES . " c where z.zone_country_id = c.countries_id order by c.countries_name, z.zone_name";
            $zones_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $zones_query_raw, $zones_query_numrows);
            $zones_query = tep_db_query($zones_query_raw);
            while ($zones = tep_db_fetch_array($zones_query)) {
              if ((!isset($_GET['cID']) || (isset($_GET['cID']) && ($_GET['cID'] == $zones['zone_id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
                $cInfo = new objectInfo($zones);
              }

              if (isset($cInfo) && is_object($cInfo) && ($zones['zone_id'] == $cInfo->zone_id)) {
                echo '<tr class="table-active" onclick="document.location.href=\'' . tep_href_link('zones.php', 'page=' . (int)$_GET['page'] . '&cID=' . $cInfo->zone_id . '&action=edit') . '\'">';
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('zones.php', 'page=' . (int)$_GET['page'] . '&cID=' . $zones['zone_id']) . '\'">';
              }
              ?>
                <td><?php echo $zones['countries_name']; ?></td>
                <td><?php echo $zones['zone_name']; ?></td>
                <td class="text-right"><?php echo $zones['zone_code']; ?></td>
                <td class="text-right"><?php if (isset($cInfo) && is_object($cInfo) && ($zones['zone_id'] == $cInfo->zone_id) ) { echo '<i class="fas fa-chevron-circle-right text-info"></i>'; } else { echo '<a href="' . tep_href_link('zones.php', 'page=' . (int)$_GET['page'] . '&cID=' . $zones['zone_id']) . '"><i class="fas fa-info-circle text-muted"></i></a>'; } ?></td>
              </tr>
              <?php
              }
            ?>
          </tbody>
        </table>
      </div>
      
      <div class="row my-1">
        <div class="col"><?php echo $zones_split->display_count($zones_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ZONES); ?></div>
        <div class="col text-right mr-2"><?php echo $zones_split->display_links($zones_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></div>
      </div>
    </div>

<?php
  $heading = [];
  $contents = [];

  switch ($action) {
    case 'new':
      $heading[] = ['text' => TEXT_INFO_HEADING_NEW_ZONE];

      $contents = ['form' => tep_draw_form('zones', 'zones.php', 'page=' . (int)$_GET['page'] . '&action=insert')];
      $contents[] = ['text' => TEXT_INFO_INSERT_INTRO];
      $contents[] = ['text' => TEXT_INFO_ZONES_NAME . '<br>' . tep_draw_input_field('zone_name')];
      $contents[] = ['text' => TEXT_INFO_ZONES_CODE . '<br>' . tep_draw_input_field('zone_code')];
      $contents[] = ['text' => TEXT_INFO_COUNTRY_NAME . '<br>' . tep_draw_pull_down_menu('zone_country_id', tep_get_countries())];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success xxx text-white mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('zones.php', 'page=' . (int)$_GET['page']), null, null, 'btn-light')];
      break;
    case 'edit':
      $heading[] = ['text' => TEXT_INFO_HEADING_EDIT_ZONE];

      $contents = ['form' => tep_draw_form('zones', 'zones.php', 'page=' . (int)$_GET['page'] . '&cID=' . $cInfo->zone_id . '&action=save')];
      $contents[] = ['text' => TEXT_INFO_EDIT_INTRO];
      $contents[] = ['text' => TEXT_INFO_ZONES_NAME . '<br>' . tep_draw_input_field('zone_name', $cInfo->zone_name)];
      $contents[] = ['text' => TEXT_INFO_ZONES_CODE . '<br>' . tep_draw_input_field('zone_code', $cInfo->zone_code)];
      $contents[] = ['text' => TEXT_INFO_COUNTRY_NAME . '<br>' . tep_draw_pull_down_menu('zone_country_id', tep_get_countries(), $cInfo->countries_id)];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success xxx text-white mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('zones.php', 'page=' . (int)$_GET['page'] . '&cID=' . $cInfo->zone_id), null, null, 'btn-light')];
      break;
    case 'delete':
      $heading[] = ['text' => TEXT_INFO_HEADING_DELETE_ZONE];

      $contents = ['form' => tep_draw_form('zones', 'zones.php', 'page=' . (int)$_GET['page'] . '&cID=' . $cInfo->zone_id . '&action=deleteconfirm')];
      $contents[] = ['text' => TEXT_INFO_DELETE_INTRO];
      $contents[] = ['text' => '<strong>' . $cInfo->zone_name . '</strong>'];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', null, 'primary', null, 'btn-danger xxx text-white mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('zones.php', 'page=' . (int)$_GET['page'] . '&cID=' . $cInfo->zone_id), null, null, 'btn-light')];
      break;
    default:
      if (isset($cInfo) && is_object($cInfo)) {
        $heading[] = ['text' => $cInfo->zone_name];

        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_EDIT, 'fas fa-cogs', tep_href_link('zones.php', 'page=' . (int)$_GET['page'] . '&cID=' . $cInfo->zone_id . '&action=edit'), null, null, 'btn-warning mr-2') . tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', tep_href_link('zones.php', 'page=' . (int)$_GET['page'] . '&cID=' . $cInfo->zone_id . '&action=delete'), null, null, 'btn-danger xxx text-white')];
        $contents[] = ['text' => TEXT_INFO_ZONES_NAME . '<br>' . $cInfo->zone_name . ' (' . $cInfo->zone_code . ')'];
        $contents[] = ['text' => TEXT_INFO_COUNTRY_NAME . ' ' . $cInfo->countries_name];
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
