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
        $tax_class_title = tep_db_prepare_input($_POST['tax_class_title']);
        $tax_class_description = tep_db_prepare_input($_POST['tax_class_description']);

        tep_db_query("insert into tax_class (tax_class_title, tax_class_description, date_added) values ('" . tep_db_input($tax_class_title) . "', '" . tep_db_input($tax_class_description) . "', now())");

        tep_redirect(tep_href_link('tax_classes.php'));
        break;
      case 'save':
        $tax_class_id = tep_db_prepare_input($_GET['tID']);
        $tax_class_title = tep_db_prepare_input($_POST['tax_class_title']);
        $tax_class_description = tep_db_prepare_input($_POST['tax_class_description']);

        tep_db_query("update tax_class set tax_class_id = '" . (int)$tax_class_id . "', tax_class_title = '" . tep_db_input($tax_class_title) . "', tax_class_description = '" . tep_db_input($tax_class_description) . "', last_modified = now() where tax_class_id = '" . (int)$tax_class_id . "'");

        tep_redirect(tep_href_link('tax_classes.php', 'page=' . $_GET['page'] . '&tID=' . $tax_class_id));
        break;
      case 'deleteconfirm':
        $tax_class_id = tep_db_prepare_input($_GET['tID']);

        tep_db_query("delete from tax_class where tax_class_id = '" . (int)$tax_class_id . "'");

        tep_redirect(tep_href_link('tax_classes.php', 'page=' . $_GET['page']));
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
        echo tep_draw_bootstrap_button(IMAGE_NEW_TAX_CLASS, 'fas fa-funnel-dollar', tep_href_link('tax_classes.php', 'action=new'), null, null, 'btn-danger xxx text-white');
      }
      else {
        echo tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link('tax_classes.php'), null, null, 'btn-light mt-2');
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
              <th><?php echo TABLE_HEADING_TAX_CLASSES; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_ACTION; ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $classes_query_raw = "select * from tax_class order by tax_class_title";
            $classes_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $classes_query_raw, $classes_query_numrows);
            $classes_query = tep_db_query($classes_query_raw);
            while ($classes = tep_db_fetch_array($classes_query)) {
              if ((!isset($_GET['tID']) || (isset($_GET['tID']) && ($_GET['tID'] == $classes['tax_class_id']))) && !isset($tcInfo) && (substr($action, 0, 3) != 'new')) {
                $tcInfo = new objectInfo($classes);
              }

              if (isset($tcInfo) && is_object($tcInfo) && ($classes['tax_class_id'] == $tcInfo->tax_class_id)) {
                echo '<tr class="table-active" onclick="document.location.href=\'' . tep_href_link('tax_classes.php', 'page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id . '&action=edit') . '\'">';
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('tax_classes.php', 'page=' . $_GET['page'] . '&tID=' . $classes['tax_class_id']) . '\'">';
              }
              ?>
                <td><?php echo $classes['tax_class_title']; ?></td>
                <td class="text-right"><?php if (isset($tcInfo) && is_object($tcInfo) && ($classes['tax_class_id'] == $tcInfo->tax_class_id)) { echo '<i class="fas fa-chevron-circle-right text-info"></i>'; } else { echo '<a href="' . tep_href_link('tax_classes.php', 'page=' . $_GET['page'] . '&tID=' . $classes['tax_class_id']) . '"><i class="fas fa-info-circle text-muted"></i></a>'; } ?></td>
              </tr>
              <?php
            }
            ?>
          </tbody>
        </table>
      </div>
      
      <div class="row my-1">
        <div class="col"><?php echo $classes_split->display_count($classes_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_TAX_CLASSES); ?></div>
        <div class="col text-right mr-2"><?php echo $classes_split->display_links($classes_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></div>
      </div>
    </div>

<?php
  $heading = [];
  $contents = [];

  switch ($action) {
    case 'new':
      $heading[] = ['text' => TEXT_INFO_HEADING_NEW_TAX_CLASS];

      $contents = ['form' => tep_draw_form('classes', 'tax_classes.php', 'page=' . $_GET['page'] . '&action=insert')];
      $contents[] = ['text' => TEXT_INFO_INSERT_INTRO];
      $contents[] = ['text' => TEXT_INFO_CLASS_TITLE . '<br>' . tep_draw_input_field('tax_class_title')];
      $contents[] = ['text' => sprintf(TEXT_INFO_CLASS_DESCRIPTION, null) . '<br>' . tep_draw_input_field('tax_class_description')];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, null, null, 'btn-success xxx text-white mr-2') .  tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('tax_classes.php', 'page=' . $_GET['page']), null, null, 'btn-light')];
      break;
    case 'edit':
      $heading[] = ['text' => TEXT_INFO_HEADING_EDIT_TAX_CLASS];

      $contents = ['form' => tep_draw_form('classes', 'tax_classes.php', 'page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id . '&action=save')];
      $contents[] = ['text' => TEXT_INFO_EDIT_INTRO];
      $contents[] = ['text' => TEXT_INFO_CLASS_TITLE . '<br>' . tep_draw_input_field('tax_class_title', $tcInfo->tax_class_title)];
      $contents[] = ['text' => sprintf(TEXT_INFO_CLASS_DESCRIPTION, null) . '<br>' . tep_draw_input_field('tax_class_description', $tcInfo->tax_class_description)];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success xxx text-white mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('tax_classes.php', 'page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id), null, null, 'btn-light')];
      break;
    case 'delete':
      $heading[] = ['text' => TEXT_INFO_HEADING_DELETE_TAX_CLASS];

      $contents = ['form' => tep_draw_form('classes', 'tax_classes.php', 'page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id . '&action=deleteconfirm')];
      $contents[] = ['text' => TEXT_INFO_DELETE_INTRO];
      $contents[] = ['class' => 'text-center text-uppercase font-weight-bold', 'text' => $tcInfo->tax_class_title];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', null, 'primary', null, 'btn-danger xxx text-white mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('tax_classes.php', 'page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id), null, null, 'btn-light')];
      break;
    default:
      if (isset($tcInfo) && is_object($tcInfo)) {
        $heading[] = ['text' => $tcInfo->tax_class_title];

        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_EDIT, 'fas fa-cogs', tep_href_link('tax_classes.php', 'page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id . '&action=edit'), null, null, 'btn-warning mr-2') . tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', tep_href_link('tax_classes.php', 'page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id . '&action=delete'), null, null, 'btn-danger xxx text-white mr-2')];
        $contents[] = ['text' => sprintf(TEXT_INFO_DATE_ADDED, tep_date_short($tcInfo->date_added))];
        $contents[] = ['text' => sprintf(TEXT_INFO_LAST_MODIFIED, tep_date_short($tcInfo->last_modified))];
        $contents[] = ['text' => sprintf(TEXT_INFO_CLASS_DESCRIPTION, $tcInfo->tax_class_description)];
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
