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

        tep_redirect(tep_href_link('tax_rates.php', 'page=' . $_GET['page'] . '&tID=' . $tax_rates_id));
        break;
      case 'deleteconfirm':
        $tax_rates_id = tep_db_prepare_input($_GET['tID']);

        tep_db_query("delete from tax_rates where tax_rates_id = '" . (int)$tax_rates_id . "'");

        tep_redirect(tep_href_link('tax_rates.php', 'page=' . $_GET['page']));
        break;
    }
  }

  require('includes/template_top.php');
?>

  <h1 class="display-4 mb-2"><?php echo HEADING_TITLE; ?></h1>
  
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
                echo '<tr onclick="document.location.href=\'' . tep_href_link('tax_rates.php', 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id . '&action=edit') . '\'">';
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('tax_rates.php', 'page=' . $_GET['page'] . '&tID=' . $rates['tax_rates_id']) . '\'">';
              }
              ?>
                <td><?php echo $rates['tax_priority']; ?></td>
                <td><?php echo $rates['tax_class_title']; ?></td>
                <td><?php echo $rates['geo_zone_name']; ?></td>
                <td><?php echo tep_display_tax_value($rates['tax_rate']); ?>%</td>
                <td class="text-right"><?php if (isset($trInfo) && is_object($trInfo) && ($rates['tax_rates_id'] == $trInfo->tax_rates_id)) { echo '<i class="fas fa-chevron-circle-right text-info"></i>'; } else { echo '<a href="' . tep_href_link('tax_rates.php', 'page=' . $_GET['page'] . '&tID=' . $rates['tax_rates_id']) . '"><i class="fas fa-info-circle text-muted"></i></a>'; } ?></td>
              </tr>
              <?php
              }
            ?>
          </tbody>
        </table>
      </div>
      
      <div class="row">
        <div class="col"><?php echo $rates_split->display_count($rates_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_TAX_RATES); ?></div>
        <div class="col text-right"><?php echo $rates_split->display_links($rates_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></div>
      </div>
      
      <?php
      if (empty($action)) {
        ?>
        <p class="pt-2 text-right"><?php echo tep_draw_bootstrap_button(IMAGE_NEW_TAX_RATE, 'fas fa-hand-holding-usd', tep_href_link('tax_rates.php', 'page=' . $_GET['page'] . '&action=new'), null, null, 'btn-success btn-sm xxx text-white'); ?></p>
        <?php
        }
      ?>
    </div>

<?php
  $heading = array();
  $contents = array();

  switch ($action) {
    case 'new':
      $heading[] = array('text' => '<strong>' . TEXT_INFO_HEADING_NEW_TAX_RATE . '</strong>');

      $contents = array('form' => tep_draw_form('rates', 'tax_rates.php', 'page=' . $_GET['page'] . '&action=insert'));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
      $contents[] = array('text' => '<br />' . TEXT_INFO_CLASS_TITLE . '<br />' . tep_tax_classes_pull_down('name="tax_class_id" style="font-size:10px"'));
      $contents[] = array('text' => '<br />' . TEXT_INFO_ZONE_NAME . '<br />' . tep_geo_zones_pull_down('name="tax_zone_id" style="font-size:10px"'));
      $contents[] = array('text' => '<br />' . TEXT_INFO_TAX_RATE . '<br />' . tep_draw_input_field('tax_rate'));
      $contents[] = array('text' => '<br />' . TEXT_INFO_RATE_DESCRIPTION . '<br />' . tep_draw_input_field('tax_description'));
      $contents[] = array('text' => '<br />' . TEXT_INFO_TAX_RATE_PRIORITY . '<br />' . tep_draw_input_field('tax_priority'));
      $contents[] = array('align' => 'center', 'text' => '<br />' . tep_draw_button(IMAGE_SAVE, 'disk', null, 'primary') . tep_draw_button(IMAGE_CANCEL, 'close', tep_href_link('tax_rates.php', 'page=' . $_GET['page'])));
      break;
    case 'edit':
      $heading[] = array('text' => '<strong>' . TEXT_INFO_HEADING_EDIT_TAX_RATE . '</strong>');

      $contents = array('form' => tep_draw_form('rates', 'tax_rates.php', 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id  . '&action=save'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
      $contents[] = array('text' => '<br />' . TEXT_INFO_CLASS_TITLE . '<br />' . tep_tax_classes_pull_down('name="tax_class_id" style="font-size:10px"', $trInfo->tax_class_id));
      $contents[] = array('text' => '<br />' . TEXT_INFO_ZONE_NAME . '<br />' . tep_geo_zones_pull_down('name="tax_zone_id" style="font-size:10px"', $trInfo->geo_zone_id));
      $contents[] = array('text' => '<br />' . TEXT_INFO_TAX_RATE . '<br />' . tep_draw_input_field('tax_rate', $trInfo->tax_rate));
      $contents[] = array('text' => '<br />' . TEXT_INFO_RATE_DESCRIPTION . '<br />' . tep_draw_input_field('tax_description', $trInfo->tax_description));
      $contents[] = array('text' => '<br />' . TEXT_INFO_TAX_RATE_PRIORITY . '<br />' . tep_draw_input_field('tax_priority', $trInfo->tax_priority));
      $contents[] = array('align' => 'center', 'text' => '<br />' . tep_draw_button(IMAGE_SAVE, 'disk', null, 'primary') . tep_draw_button(IMAGE_CANCEL, 'close', tep_href_link('tax_rates.php', 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id)));
      break;
    case 'delete':
      $heading[] = array('text' => '<strong>' . TEXT_INFO_HEADING_DELETE_TAX_RATE . '</strong>');

      $contents = array('form' => tep_draw_form('rates', 'tax_rates.php', 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id  . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br /><strong>' . $trInfo->tax_class_title . ' ' . number_format($trInfo->tax_rate, TAX_DECIMAL_PLACES) . '%</strong>');
      $contents[] = array('align' => 'center', 'text' => '<br />' . tep_draw_button(IMAGE_DELETE, 'trash', null, 'primary') . tep_draw_button(IMAGE_CANCEL, 'close', tep_href_link('tax_rates.php', 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id)));
      break;
    default:
      if (is_object($trInfo)) {
        $heading[] = array('text' => '<strong>' . $trInfo->tax_class_title . '</strong>');
        $contents[] = array('align' => 'center', 'text' => tep_draw_button(IMAGE_EDIT, 'document', tep_href_link('tax_rates.php', 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id . '&action=edit')) . tep_draw_button(IMAGE_DELETE, 'trash', tep_href_link('tax_rates.php', 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id . '&action=delete')));
        $contents[] = array('text' => '<br />' . TEXT_INFO_DATE_ADDED . ' ' . tep_date_short($trInfo->date_added));
        $contents[] = array('text' => '' . TEXT_INFO_LAST_MODIFIED . ' ' . tep_date_short($trInfo->last_modified));
        $contents[] = array('text' => '<br />' . TEXT_INFO_RATE_DESCRIPTION . '<br />' . $trInfo->tax_description);
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
