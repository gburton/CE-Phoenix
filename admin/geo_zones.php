<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  $saction = $_GET['saction'] ?? '';

  $OSCOM_Hooks->call('geo_zones', 'preSaction');

  if (!Text::is_empty($saction)) {
    switch ($saction) {
      case 'insert_sub':
        $zID = Text::prepare($_GET['zID']);
        $zone_country_id = Text::prepare($_POST['zone_country_id']);
        $zone_id = Text::prepare($_POST['zone_id']);

        tep_db_query("INSERT INTO zones_to_geo_zones (zone_country_id, zone_id, geo_zone_id, date_added) VALUES (" . (int)$zone_country_id . ", " . (int)$zone_id . ", " . (int)$zID . ", NOW())");
        $new_subzone_id = tep_db_insert_id();

        $OSCOM_Hooks->call('geo_zones', 'insertSubSaction');

        tep_redirect(tep_href_link('geo_zones.php', 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $new_subzone_id));
        break;
      case 'save_sub':
        $sID = Text::prepare($_GET['sID']);
        $zID = Text::prepare($_GET['zID']);
        $zone_country_id = Text::prepare($_POST['zone_country_id']);
        $zone_id = Text::prepare($_POST['zone_id']);

        tep_db_query("UPDATE zones_to_geo_zones SET geo_zone_id = " . (int)$zID . ", zone_country_id = " . (int)$zone_country_id . ", zone_id = " . (Text::is_empty($zone_id) ? 'NULL' : (int)$zone_id) . ", last_modified = NOW() WHERE association_id = " . (int)$sID);

        $OSCOM_Hooks->call('geo_zones', 'saveSubSaction');

        tep_redirect(tep_href_link('geo_zones.php', 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $_GET['sID']));
        break;
      case 'deleteconfirm_sub':
        $sID = Text::prepare($_GET['sID']);

        tep_db_query("DELETE FROM zones_to_geo_zones WHERE association_id = '" . (int)$sID . "'");

        $OSCOM_Hooks->call('geo_zones', 'deleteConfirmSubSaction');

        tep_redirect(tep_href_link('geo_zones.php', 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage']));
        break;
    }
  }

  $OSCOM_Hooks->call('geo_zones', 'postSaction');

  $action = $_GET['action'] ?? '';

  $OSCOM_Hooks->call('geo_zones', 'preAction');

  if (!Text::is_empty($action)) {
    switch ($action) {
      case 'insert_zone':
        $geo_zone_name = Text::prepare($_POST['geo_zone_name']);
        $geo_zone_description = Text::prepare($_POST['geo_zone_description']);

        tep_db_query("INSERT INTO geo_zones (geo_zone_name, geo_zone_description, date_added) VALUES ('" . tep_db_input($geo_zone_name) . "', '" . tep_db_input($geo_zone_description) . "', NOW())");
        $new_zone_id = tep_db_insert_id();

        $OSCOM_Hooks->call('geo_zones', 'insertZoneAction');

        tep_redirect(tep_href_link('geo_zones.php', 'zpage=' . $_GET['zpage'] . '&zID=' . $new_zone_id));
        break;
      case 'save_zone':
        $zID = Text::prepare($_GET['zID']);
        $geo_zone_name = Text::prepare($_POST['geo_zone_name']);
        $geo_zone_description = Text::prepare($_POST['geo_zone_description']);

        tep_db_query("UPDATE geo_zones SET geo_zone_name = '" . tep_db_input($geo_zone_name) . "', geo_zone_description = '" . tep_db_input($geo_zone_description) . "', last_modified = NOW() WHERE geo_zone_id = " . (int)$zID);

        $OSCOM_Hooks->call('geo_zones', 'saveZoneAction');

        tep_redirect(tep_href_link('geo_zones.php', 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID']));
        break;
      case 'deleteconfirm_zone':
        $zID = Text::prepare($_GET['zID']);

        tep_db_query("DELETE FROM geo_zones WHERE geo_zone_id = " . (int)$zID);
        tep_db_query("DELETE FROM zones_to_geo_zones WHERE geo_zone_id = " . (int)$zID);

        $OSCOM_Hooks->call('geo_zones', 'deleteConfirmAction');

        tep_redirect(tep_href_link('geo_zones.php', 'zpage=' . $_GET['zpage']));
        break;
    }
  }

  $OSCOM_Hooks->call('geo_zones', 'postAction');

  require 'includes/template_top.php';

  if (isset($_GET['zID']) && (($saction == 'edit') || ($saction == 'new'))) {
?>
<script><!--
function update_zone(theForm) {
  var NumState = theForm.zone_id.options.length;
  var SelectedCountry = "";

  while(NumState > 0) {
    NumState--;
    theForm.zone_id.options[NumState] = null;
  }

  SelectedCountry = theForm.zone_country_id.options[theForm.zone_country_id.selectedIndex].value;

<?= tep_js_zone_list('SelectedCountry', 'theForm', 'zone_id') ?>

}
//--></script>
<?php
  }
?>

  <div class="row">
    <div class="col">
      <h1 class="display-4 mb-2"><?= HEADING_TITLE ?><?php if (isset($_GET['zID'])) echo ' <small>' . tep_get_geo_zone_name($_GET['zID']) . '</small>' ?></h1>
    </div>
    <div class="col text-right align-self-center">
      <?php
      if (empty($action)) {
        echo tep_draw_bootstrap_button(TEXT_INFO_HEADING_NEW_ZONE, 'fas fa-atlas', tep_href_link('geo_zones.php', 'action=new_zone' . (isset($_GET['zpage']) ? '&zpage=' . $_GET['zpage'] : '') . (isset($_GET['zID']) ? '&zID=' . $_GET['zID'] : '')), null, null, 'btn-danger');
      } else {
        echo tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link('geo_zones.php'), null, null, 'btn-light');
      }
      ?>
    </div>
  </div>

  <?php
  if ('list' === $action) {
    ?>
    <div class="row no-gutters">
      <div class="col-12 col-sm-8">
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead class="thead-dark">
              <tr>
                <th><?= TABLE_HEADING_COUNTRY ?></th>
                <th><?= TABLE_HEADING_COUNTRY_ZONE ?></th>
                <th class="text-right"><?= TABLE_HEADING_ACTION ?></th>
              </tr>
            </thead>
            <tbody>
              <?php
              $zones_query_raw = sprintf(<<<'EOSQL'
SELECT a.*, c.countries_name, z.zone_name
 FROM zones_to_geo_zones a
   LEFT JOIN countries c ON a.zone_country_id = c.countries_id
   LEFT JOIN zones z ON a.zone_id = z.zone_id
 WHERE a.geo_zone_id = %d
 ORDER BY association_id
EOSQL
                , (int)$_GET['zID']);
              $zones_split = new splitPageResults($_GET['spage'], MAX_DISPLAY_SEARCH_RESULTS, $zones_query_raw, $zones_query_numrows);
              $zones_query = tep_db_query($zones_query_raw);
              while ($zone = $zones_query->fetch_assoc()) {
                if (!isset($sInfo) && (!isset($_GET['sID']) || ($_GET['sID'] == $zone['association_id'])) && (substr($action, 0, 3) != 'new')) {
                  $sInfo = new objectInfo($zone);
                }

                $query_string = 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=';
                if (isset($sInfo->association_id) && ($zone['association_id'] == $sInfo->association_id)) {
                  echo '<tr class="table-active" onclick="document.location.href=\'' . tep_href_link('geo_zones.php', $query_string . $sInfo->association_id . '&saction=edit') . '\'">';
                  $icon = '<i class="fas fa-chevron-circle-right text-info"></i>';
                } else {
                  echo '<tr onclick="document.location.href=\'' . tep_href_link('geo_zones.php', $query_string . $zone['association_id']) . '\'">';
                  $icon = '<a href="' . tep_href_link('geo_zones.php', $query_string . $zone['association_id']) . '"><i class="fas fa-info-circle text-muted"></i></a>';
                }
                ?>
                  <td><?= $zone['countries_name'] ?? TEXT_ALL_COUNTRIES ?></td>
                  <td><?= $zone['zone_name'] ?? PLEASE_SELECT ?></td>
                  <td class="text-right"><?= $icon ?>&nbsp;</td>
                </tr>
                <?php
              }
              ?>
            </tbody>
          </table>
        </div>

        <div class="row my-1">
          <div class="col"><?= $zones_split->display_count($zones_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['spage'], TEXT_DISPLAY_NUMBER_OF_COUNTRIES) ?></div>
          <div class="col text-right mr-2"><?= $zones_split->display_links($zones_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['spage'], 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list', 'spage') ?></div>
        </div>

        <?php
        if (empty($saction)) {
          ?>
          <div class="row">
            <div class="col"><p class="pt-2 text-right"><?= tep_draw_bootstrap_button(IMAGE_INSERT, 'fas fa-plus', tep_href_link('geo_zones.php', 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&' . (isset($sInfo) ? 'sID=' . $sInfo->association_id . '&' : '') . 'saction=new'), null, null, 'btn-warning') ?></p></div>
          </div>
          <?php
        }
        ?>
      </div>
      <?php
    } else {
      ?>
      <div class="row no-gutters">
        <div class="col-12 col-sm-8">
          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead class="thead-dark">
                <tr>
                  <th><?= TABLE_HEADING_TAX_ZONES ?></th>
                  <th class="text-right"><?= TABLE_HEADING_ACTION ?></th>
                </tr>
              </thead>
              <tbody>
                <?php
                $zones_query_raw = "SELECT * FROM geo_zones ORDER BY geo_zone_name";
                $zones_split = new splitPageResults($_GET['zpage'], MAX_DISPLAY_SEARCH_RESULTS, $zones_query_raw, $zones_query_numrows);
                $zones_query = tep_db_query($zones_query_raw);
                while ($zone = $zones_query->fetch_assoc()) {
                  if ((!isset($_GET['zID']) || (isset($_GET['zID']) && ($_GET['zID'] == $zone['geo_zone_id']))) && !isset($zInfo) && (substr($action, 0, 3) != 'new')) {
                    $num_zones_query = tep_db_query("SELECT COUNT(*) AS num_zones FROM zones_to_geo_zones WHERE geo_zone_id = " . (int)$zone['geo_zone_id'] . " GROUP BY geo_zone_id");
                    $num_zones = $num_zones_query->fetch_assoc();

                    $zone['num_zones'] = $num_zones['num_zones'] ?? 0;

                    $zInfo = new objectInfo($zone);
                  }

                  if (isset($zInfo->geo_zone_id) && ($zone['geo_zone_id'] == $zInfo->geo_zone_id)) {
                    echo '<tr class="table-active" onclick="document.location.href=\'' . tep_href_link('geo_zones.php', 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=list') . '\'">';
                    $icon = '<i class="fas fa-chevron-circle-right text-info"></i>';
                  } else {
                    echo '<tr onclick="document.location.href=\'' . tep_href_link('geo_zones.php', 'zpage=' . $_GET['zpage'] . '&zID=' . $zone['geo_zone_id']) . '\'">';
                    $icon = '<a href="' . tep_href_link('geo_zones.php', 'zpage=' . $_GET['zpage'] . '&zID=' .  $zone['geo_zone_id']) . '"><i class="fas fa-info-circle text-muted"></i></a>';
                  }
                  ?>
                  <td><?= '<a href="' . tep_href_link('geo_zones.php', 'zpage=' . $_GET['zpage'] . '&zID=' . $zone['geo_zone_id'] . '&action=list') . '"><i class="fas fa-folder text-warning"></i></a>&nbsp;' . $zone['geo_zone_name'] ?></td>
                  <td class="text-right"><?= $icon ?>&nbsp;</td>
                </tr>
                <?php
              }
              ?>
              </tbody>
            </table>
          </div>

          <div class="row my-1">
            <div class="col"><?= $zones_split->display_count($zones_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['zpage'], TEXT_DISPLAY_NUMBER_OF_TAX_ZONES) ?></div>
            <div class="col text-right mr-2"><?= $zones_split->display_links($zones_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['zpage'], '', 'zpage') ?></div>
          </div>

        </div>
      <?php
    }

  $heading = [];
  $contents = [];

  if ($action == 'list') {
    switch ($saction) {
      case 'new':
        $heading[] = ['text' => TEXT_INFO_HEADING_NEW_SUB_ZONE];

        $contents = ['form' => tep_draw_form('zones', 'geo_zones.php', 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&' . (isset($_GET['sID']) ? 'sID=' . $_GET['sID'] . '&' : '') . 'saction=insert_sub')];
        $contents[] = ['text' => TEXT_INFO_NEW_SUB_ZONE_INTRO];
        $contents[] = ['text' => TEXT_INFO_COUNTRY . '<br>' . tep_draw_pull_down_menu('zone_country_id', tep_get_countries(TEXT_ALL_COUNTRIES), '', 'onchange="update_zone(this.form);"')];
        $contents[] = ['text' => TEXT_INFO_COUNTRY_ZONE . '<br>' . tep_draw_pull_down_menu('zone_id', tep_prepare_country_zones_pull_down())];
        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('geo_zones.php', 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . (isset($_GET['sID']) ? '&sID=' . $_GET['sID'] : '')), null, null, 'btn-light')];
        break;
      case 'edit':
        $heading[] = ['text' => TEXT_INFO_HEADING_EDIT_SUB_ZONE];

        $contents = ['form' => tep_draw_form('zones', 'geo_zones.php', 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id . '&saction=save_sub')];
        $contents[] = ['text' => TEXT_INFO_EDIT_SUB_ZONE_INTRO];
        $contents[] = ['text' => TEXT_INFO_COUNTRY . '<br>' . tep_draw_pull_down_menu('zone_country_id', tep_get_countries(TEXT_ALL_COUNTRIES), $sInfo->zone_country_id, 'onchange="update_zone(this.form);"')];
        $contents[] = ['text' => TEXT_INFO_COUNTRY_ZONE . '<br>' . tep_draw_pull_down_menu('zone_id', tep_prepare_country_zones_pull_down($sInfo->zone_country_id), $sInfo->zone_id)];
        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('geo_zones.php', 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id), null, null, 'btn-light')];
        break;
      case 'delete':
        $heading[] = ['text' => TEXT_INFO_HEADING_DELETE_SUB_ZONE];

        $contents = ['form' => tep_draw_form('zones', 'geo_zones.php', 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id . '&saction=deleteconfirm_sub')];
        $contents[] = ['text' => TEXT_INFO_DELETE_SUB_ZONE_INTRO];
        $contents[] = ['class' => 'text-center text-uppercase font-weight-bold', 'text' => $sInfo->countries_name];
        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', null, 'primary', null, 'btn-danger mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('geo_zones.php', 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id), null, null, 'btn-light')];
        break;
      default:
        if (isset($sInfo) && is_object($sInfo)) {
          $heading[] = ['text' => $sInfo->countries_name];

          $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_EDIT, 'fas fa-cogs', tep_href_link('geo_zones.php', 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id . '&saction=edit'), null, null, 'btn-warning mr-2') . tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', tep_href_link('geo_zones.php', 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id . '&saction=delete'), null, null, 'btn-danger')];
          $contents[] = ['text' => sprintf(TEXT_INFO_DATE_ADDED, null) . ' ' . tep_date_short($sInfo->date_added)];
          if (!Text::is_empty($sInfo->last_modified)) {
            $contents[] = ['text' => sprintf(TEXT_INFO_LAST_MODIFIED, null) . ' ' . tep_date_short($sInfo->last_modified)];
          }
        }
        break;
    }
  } else {
    switch ($action) {
      case 'new_zone':
        $heading[] = ['text' => TEXT_INFO_HEADING_NEW_ZONE];

        $contents = ['form' => tep_draw_form('zones', 'geo_zones.php', 'zpage=' . $_GET['zpage'] . (isset($_GET['zID']) ? '&zID=' . $_GET['zID'] : '') . '&action=insert_zone')];
        $contents[] = ['text' => TEXT_INFO_NEW_ZONE_INTRO];
        $contents[] = ['text' => TEXT_INFO_ZONE_NAME . '<br>' . tep_draw_input_field('geo_zone_name')];
        $contents[] = ['text' => sprintf(TEXT_INFO_ZONE_DESCRIPTION, null) . '<br>' . tep_draw_input_field('geo_zone_description')];
        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('geo_zones.php', 'zpage=' . $_GET['zpage'] . (isset($_GET['zID']) ? '&zID=' . $_GET['zID'] : '')), null, null, 'btn-light')];
        break;
      case 'edit_zone':
        $heading[] = ['text' => '<strong>' . TEXT_INFO_HEADING_EDIT_ZONE . '</strong>'];

        $contents = ['form' => tep_draw_form('zones', 'geo_zones.php', 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=save_zone')];
        $contents[] = ['text' => TEXT_INFO_EDIT_ZONE_INTRO];
        $contents[] = ['text' => TEXT_INFO_ZONE_NAME . '<br>' . tep_draw_input_field('geo_zone_name', $zInfo->geo_zone_name)];
        $contents[] = ['text' => sprintf(TEXT_INFO_ZONE_DESCRIPTION, null) . '<br>' . tep_draw_input_field('geo_zone_description', $zInfo->geo_zone_description)];
        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('geo_zones.php', 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id), null, null, 'btn-light')];
        break;
      case 'delete_zone':
        $heading[] = ['text' => TEXT_INFO_HEADING_DELETE_ZONE];

        $contents = ['form' => tep_draw_form('zones', 'geo_zones.php', 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=deleteconfirm_zone')];
        $contents[] = ['text' => TEXT_INFO_DELETE_ZONE_INTRO];
        $contents[] = ['class' => 'text-center text-uppercase font-weight-bold', 'text' => $zInfo->geo_zone_name];
        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', null, 'primary', null, 'btn-danger mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('geo_zones.php', 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id), null, null, 'btn-light')];
        break;
      default:
        if (isset($zInfo) && is_object($zInfo)) {
          $heading[] = ['text' => $zInfo->geo_zone_name];

          $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_EDIT, 'fas fa-cogs', tep_href_link('geo_zones.php', 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=edit_zone'), null, null, 'btn-warning mr-2') . tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', tep_href_link('geo_zones.php', 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=delete_zone'), null, null, 'btn-danger mr-2') . tep_draw_bootstrap_button(IMAGE_DETAILS, 'fas fa-eye', tep_href_link('geo_zones.php', 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=list'), null, null, 'btn-info')];
          $contents[] = ['text' => sprintf(TEXT_INFO_NUMBER_ZONES, $zInfo->num_zones)];
          $contents[] = ['text' => sprintf(TEXT_INFO_DATE_ADDED, tep_date_short($zInfo->date_added))];
          if (!Text::is_empty($zInfo->last_modified)) {
            $contents[] = ['text' => sprintf(TEXT_INFO_LAST_MODIFIED, tep_date_short($zInfo->last_modified))];
          }
          $contents[] = ['text' => sprintf(TEXT_INFO_ZONE_DESCRIPTION, $zInfo->geo_zone_description)];
        }
        break;
    }
  }

  if ( ([] !== $heading) && ([] !== $contents) ) {
    echo '<div class="col-12 col-sm-4">';
      $box = new box();
      echo $box->infoBox($heading, $contents);
    echo '</div>';
  }
?>

  </div>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
