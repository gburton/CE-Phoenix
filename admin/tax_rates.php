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

  if (tep_not_null($action)) {
    switch ($action) {
      case 'insert':
        $tax_zone_id = tep_db_prepare_input($_POST['tax_zone_id']);
        $tax_class_id = tep_db_prepare_input($_POST['tax_class_id']);
        $tax_rate = tep_db_prepare_input($_POST['tax_rate']);
        $tax_description = tep_db_prepare_input($_POST['tax_description']);
        $tax_priority = tep_db_prepare_input($_POST['tax_priority']);

        tep_db_query("insert into tax_rates (tax_zone_id, tax_class_id, tax_rate, tax_description, tax_priority, date_added) values ('" . (int)$tax_zone_id . "', '" . (int)$tax_class_id . "', '" . tep_db_input($tax_rate) . "', '" . tep_db_input($tax_description) . "', '" . tep_db_input($tax_priority) . "', now())");

        tep_redirect(tep_href_link('tax_rates.php'));
        break;
      case 'save':
        $tax_rates_id = tep_db_prepare_input($_GET['tID']);
        $tax_zone_id = tep_db_prepare_input($_POST['tax_zone_id']);
        $tax_class_id = tep_db_prepare_input($_POST['tax_class_id']);
        $tax_rate = tep_db_prepare_input($_POST['tax_rate']);
        $tax_description = tep_db_prepare_input($_POST['tax_description']);
        $tax_priority = tep_db_prepare_input($_POST['tax_priority']);

        tep_db_query("update tax_rates set tax_rates_id = '" . (int)$tax_rates_id . "', tax_zone_id = '" . (int)$tax_zone_id . "', tax_class_id = '" . (int)$tax_class_id . "', tax_rate = '" . tep_db_input($tax_rate) . "', tax_description = '" . tep_db_input($tax_description) . "', tax_priority = '" . tep_db_input($tax_priority) . "', last_modified = now() where tax_rates_id = '" . (int)$tax_rates_id . "'");

        tep_redirect(tep_href_link('tax_rates.php', 'page=' . (int)$_GET['page'] . '&tID=' . $tax_rates_id));
        break;
      case 'deleteconfirm':
        $tax_rates_id = tep_db_prepare_input($_GET['tID']);

        tep_db_query("delete from tax_rates where tax_rates_id = '" . (int)$tax_rates_id . "'");

        tep_redirect(tep_href_link('tax_rates.php', 'page=' . (int)$_GET['page']));
        break;
    }
  }

  require('includes/template_top.php');
?>

  <div class="row">
    <div class="col">
      <h1 class="display-4 mb-2"><?php echo HEADING_TITLE; ?></h1>
    </div>
    <div class="col text-right align-self-center">
      <?php
      if (empty($action)) {
        echo tep_draw_bootstrap_button(IMAGE_NEW_TAX_RATE, 'fas fa-percent', tep_href_link('tax_rates.php', 'action=new'), null, null, 'btn-danger xxx text-white');
      }
      else {
        echo tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link('tax_rates.php'), null, null, 'btn-light mt-2');
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
              <th><?php echo TABLE_HEADING_TAX_RATE_PRIORITY; ?></th>
              <th><?php echo TABLE_HEADING_TAX_CLASS_TITLE; ?></th>
              <th><?php echo TABLE_HEADING_ZONE; ?></th>
              <th><?php echo TABLE_HEADING_TAX_RATE; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_ACTION; ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $rates_query_raw = "select r.*, z.*, tc.* from tax_class tc, tax_rates r left join geo_zones z on r.tax_zone_id = z.geo_zone_id where r.tax_class_id = tc.tax_class_id";
            $rates_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $rates_query_raw, $rates_query_numrows);
            $rates_query = tep_db_query($rates_query_raw);
            while ($rates = tep_db_fetch_array($rates_query)) {
              if ((!isset($_GET['tID']) || (isset($_GET['tID']) && ($_GET['tID'] == $rates['tax_rates_id']))) && !isset($trInfo) && (substr($action, 0, 3) != 'new')) {
                $trInfo = new objectInfo($rates);
              }

              if (isset($trInfo) && is_object($trInfo) && ($rates['tax_rates_id'] == $trInfo->tax_rates_id)) {
                echo '<tr class="table-active" onclick="document.location.href=\'' . tep_href_link('tax_rates.php', 'page=' . (int)$_GET['page'] . '&tID=' . $trInfo->tax_rates_id . '&action=edit') . '\'">';
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('tax_rates.php', 'page=' . (int)$_GET['page'] . '&tID=' . $rates['tax_rates_id']) . '\'">';
              }
              ?>
                <td><?php echo $rates['tax_priority']; ?></td>
                <td><?php echo $rates['tax_class_title']; ?></td>
                <td><?php echo $rates['geo_zone_name']; ?></td>
                <td><?php echo tep_display_tax_value($rates['tax_rate']); ?>%</td>
                <td class="text-right"><?php if (isset($trInfo) && is_object($trInfo) && ($rates['tax_rates_id'] == $trInfo->tax_rates_id)) { echo '<i class="fas fa-chevron-circle-right text-info"></i>'; } else { echo '<a href="' . tep_href_link('tax_rates.php', 'page=' . (int)$_GET['page'] . '&tID=' . $rates['tax_rates_id']) . '"><i class="fas fa-info-circle text-muted"></i></a>'; } ?></td>
              </tr>
              <?php
              }
            ?>
          </tbody>
        </table>
      </div>
      
      <div class="row my-1">
        <div class="col"><?php echo $rates_split->display_count($rates_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_TAX_RATES); ?></div>
        <div class="col text-right mr-2"><?php echo $rates_split->display_links($rates_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></div>
      </div>
    </div>

<?php
  $heading = [];
  $contents = [];

  switch ($action) {
    case 'new':
      $heading[] = ['text' => TEXT_INFO_HEADING_NEW_TAX_RATE];

      $contents = ['form' => tep_draw_form('rates', 'tax_rates.php', 'page=' . (int)$_GET['page'] . '&action=insert')];
      $contents[] = ['text' => TEXT_INFO_INSERT_INTRO];
      $contents[] = ['text' => TEXT_INFO_CLASS_TITLE . '<br>' . tep_tax_classes_pull_down('name="tax_class_id" class="form-control"')];
      $contents[] = ['text' => TEXT_INFO_ZONE_NAME . '<br>' . tep_geo_zones_pull_down('name="tax_zone_id" class="form-control"')];
      $contents[] = ['text' => TEXT_INFO_TAX_RATE . '<br>' . tep_draw_input_field('tax_rate')];
      $contents[] = ['text' => sprintf(TEXT_INFO_RATE_DESCRIPTION, null) . '<br>' . tep_draw_input_field('tax_description')];
      $contents[] = ['text' => TEXT_INFO_TAX_RATE_PRIORITY . '<br>' . tep_draw_input_field('tax_priority')];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success xxx text-white mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('tax_rates.php', 'page=' . (int)$_GET['page']), null, null, 'btn-light')];
      break;
    case 'edit':
      $heading[] = ['text' => TEXT_INFO_HEADING_EDIT_TAX_RATE];

      $contents = ['form' => tep_draw_form('rates', 'tax_rates.php', 'page=' . (int)$_GET['page'] . '&tID=' . $trInfo->tax_rates_id  . '&action=save')];
      $contents[] = ['text' => TEXT_INFO_EDIT_INTRO];
      $contents[] = ['text' => TEXT_INFO_CLASS_TITLE . '<br>' . tep_tax_classes_pull_down('name="tax_class_id" class="form-control"', $trInfo->tax_class_id)];
      $contents[] = ['text' => TEXT_INFO_ZONE_NAME . '<br>' . tep_geo_zones_pull_down('name="tax_zone_id" class="form-control"', $trInfo->geo_zone_id)];
      $contents[] = ['text' => TEXT_INFO_TAX_RATE . '<br>' . tep_draw_input_field('tax_rate', $trInfo->tax_rate)];
      $contents[] = ['text' => sprintf(TEXT_INFO_RATE_DESCRIPTION, null) . '<br>' . tep_draw_input_field('tax_description', $trInfo->tax_description)];
      $contents[] = ['text' => TEXT_INFO_TAX_RATE_PRIORITY . '<br>' . tep_draw_input_field('tax_priority', $trInfo->tax_priority)];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success xxx text-white mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('tax_rates.php', 'page=' . (int)$_GET['page'] . '&tID=' . $trInfo->tax_rates_id), null, null, 'btn-light')];
      break;
    case 'delete':
      $heading[] = ['text' => TEXT_INFO_HEADING_DELETE_TAX_RATE];

      $contents = ['form' => tep_draw_form('rates', 'tax_rates.php', 'page=' . (int)$_GET['page'] . '&tID=' . $trInfo->tax_rates_id  . '&action=deleteconfirm')];
      $contents[] = ['text' => TEXT_INFO_DELETE_INTRO];
      $contents[] = ['class' => 'text-center text-uppercase font-weight-bold', 'text' => $trInfo->tax_class_title . ' ' . number_format($trInfo->tax_rate, TAX_DECIMAL_PLACES) . '%'];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', null, 'primary', null, 'btn-danger xxx text-white mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('tax_rates.php', 'page=' . (int)$_GET['page'] . '&tID=' . $trInfo->tax_rates_id), null, null, 'btn-light')];
      break;
    default:
      if (is_object($trInfo)) {
        $heading[] = ['text' => $trInfo->tax_class_title];
        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_EDIT, 'fas fa-cogs', tep_href_link('tax_rates.php', 'page=' . (int)$_GET['page'] . '&tID=' . $trInfo->tax_rates_id . '&action=edit'), null, null, 'btn-warning mr-2') . tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', tep_href_link('tax_rates.php', 'page=' . (int)$_GET['page'] . '&tID=' . $trInfo->tax_rates_id . '&action=delete'), null, null, 'btn-danger xxx text-white mr-2')];
        $contents[] = ['text' => sprintf(TEXT_INFO_DATE_ADDED, tep_date_short($trInfo->date_added))];
        $contents[] = ['text' => sprintf(TEXT_INFO_LAST_MODIFIED, tep_date_short($trInfo->last_modified))];
        $contents[] = ['text' => sprintf(TEXT_INFO_RATE_DESCRIPTION, $trInfo->tax_description)];
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
