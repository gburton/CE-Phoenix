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
  
  $OSCOM_Hooks->call('languages', 'languagesPreAction');

  if (tep_not_null($action)) {
    if ('insert' == $action || 'save' == $action) {
      $sql_data = [
        'name' => tep_db_prepare_input($_POST['name']),
        'code' => tep_db_prepare_input(substr($_POST['code'], 0, 2)),
        'image' => tep_db_prepare_input($_POST['image']),
        'directory' => tep_db_prepare_input($_POST['directory']),
        'sort_order' => (int)tep_db_prepare_input($_POST['sort_order']),
      ];
    }

    switch ($action) {
      case 'insert':
        tep_db_perform('languages', $sql_data);
        $lID = tep_db_insert_id();

// create additional language-specific records
        tep_db_query("INSERT INTO categories_description (categories_id, language_id, categories_name) SELECT categories_id, " . (int)$lID . ", categories_name FROM categories_description WHERE language_id = " . (int)$languages_id);
        tep_db_query("INSERT INTO products_description (products_id, language_id, products_name, products_description, products_url) SELECT products_id, " . (int)$lID . ", products_name, products_description, products_url FROM products_description WHERE language_id = " . (int)$languages_id);
        tep_db_query("INSERT INTO products_options (products_options_id, language_id, products_options_name) SELECT products_options_id, " . (int)$lID . ", products_options_name FROM products_options WHERE language_id = " . (int)$languages_id);
        tep_db_query("INSERT INTO products_options_values (products_options_values_id, language_id, products_options_values_name) SELECT products_options_values_id, " . (int)$lID . ", products_options_values_name FROM products_options_values WHERE language_id = " . (int)$languages_id);
        tep_db_query("INSERT INTO manufacturers_info (manufacturers_id, languages_id, manufacturers_url) SELECT manufacturers_id, " . (int)$lID . ", manufacturers_url FROM manufacturers_info WHERE languages_id = " . (int)$languages_id);
        tep_db_query("INSERT INTO orders_status (orders_status_id, language_id, orders_status_name) SELECT orders_status_id, " . (int)$lID . ", orders_status_name FROM orders_status WHERE language_id = " . (int)$languages_id);
        tep_db_query("INSERT INTO customer_data_groups (customer_data_groups_id, language_id, customer_data_groups_name, cdg_vertical_sort_order, cdg_horizontal_sort_order, customer_data_groups_width) SELECT customer_data_groups_id, " . (int)$lID . ", customer_data_groups_name, cdg_vertical_sort_order, cdg_horizontal_sort_order, customer_data_groups_width FROM customer_data_groups WHERE language_id = " . (int)$languages_id);

        if (isset($_POST['default']) && ($_POST['default'] == 'on')) {
          tep_db_query("UPDATE configuration SET configuration_value = '" . tep_db_input($code) . "' WHERE configuration_key = 'DEFAULT_LANGUAGE'");
        }
        
        $OSCOM_Hooks->call('languages', 'languagesActionInsert');

        tep_redirect(tep_href_link('languages.php', (isset($_GET['page']) ? 'page=' . (int)$_GET['page'] . '&' : '') . 'lID=' . $lID));
        break;
      case 'save':
        $lID = tep_db_prepare_input($_GET['lID']);
        tep_db_perform('languages', $sql_data, 'update', "languages_id = " . (int)$lID);

        if (isset($_POST['default']) && $_POST['default'] == 'on') {
          tep_db_query("UPDATE configuration SET configuration_value = '" . tep_db_input($sql_data['code']) . "' WHERE configuration_key = 'DEFAULT_LANGUAGE'");
        }
        
        $OSCOM_Hooks->call('languages', 'languagesActionSave');

        tep_redirect(tep_href_link('languages.php', (isset($_GET['page']) ? 'page=' . (int)$_GET['page'] . '&' : '') . 'lID=' . $lID));
        break;
      case 'deleteconfirm':
        $lID = tep_db_prepare_input($_GET['lID']);

        $lng_query = tep_db_query("SELECT languages_id FROM languages WHERE code = '" . DEFAULT_CURRENCY . "'");
        $lng = tep_db_fetch_array($lng_query);
        if ($lng['languages_id'] == $lID) {
          $remove_language = false;
          $messageStack->add(ERROR_REMOVE_DEFAULT_LANGUAGE, 'error');
          $action = 'delete';
          break;
        }

        tep_db_query("DELETE FROM categories_description WHERE language_id = '" . (int)$lID . "'");
        tep_db_query("DELETE FROM products_description WHERE language_id = '" . (int)$lID . "'");
        tep_db_query("DELETE FROM products_options WHERE language_id = '" . (int)$lID . "'");
        tep_db_query("DELETE FROM products_options_values WHERE language_id = '" . (int)$lID . "'");
        tep_db_query("DELETE FROM manufacturers_info WHERE languages_id = '" . (int)$lID . "'");
        tep_db_query("DELETE FROM orders_status WHERE language_id = '" . (int)$lID . "'");
        tep_db_query("DELETE FROM customer_data_groups WHERE language_id = '" . (int)$lID . "'");
        tep_db_query("DELETE FROM languages WHERE languages_id = '" . (int)$lID . "'");
        
        $OSCOM_Hooks->call('languages', 'languagesActionDeleteConfirm');

        tep_redirect(tep_href_link('languages.php', (isset($_GET['page']) ? 'page=' . (int)$_GET['page'] : '')));
        break;
      case 'delete':
        $lID = tep_db_prepare_input($_GET['lID']);

        $lng_query = tep_db_query("SELECT code FROM languages WHERE languages_id = '" . (int)$lID . "'");
        $lng = tep_db_fetch_array($lng_query);

        $remove_language = true;
        if ($lng['code'] == DEFAULT_LANGUAGE) {
          $remove_language = false;
          $messageStack->add(ERROR_REMOVE_DEFAULT_LANGUAGE, 'error');
        }
        
        $OSCOM_Hooks->call('languages', 'languagesActionDelete');
        break;
    }
  }
  
  $OSCOM_Hooks->call('languages', 'languagesPostAction');

  require 'includes/template_top.php';
?>

  <div class="row">
    <div class="col">
      <h1 class="display-4 mb-2"><?php echo HEADING_TITLE; ?></h1>
    </div>
    <div class="col text-right align-self-center">
      <?php
      if (empty($action)) {
        echo tep_draw_bootstrap_button(IMAGE_NEW_LANGUAGE, 'fas fa-comment-dots', tep_href_link('languages.php', 'action=new'), null, null, 'btn-danger xxx text-white');
      }
      else {
        echo tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link('languages.php'), null, null, 'btn-light');
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
              <th><?php echo TABLE_HEADING_LANGUAGE_NAME; ?></th>
              <th><?php echo TABLE_HEADING_LANGUAGE_CODE; ?></th>
              <th><?php echo TABLE_HEADING_LANGUAGE_IMAGE; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_ACTION; ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $languages_query_raw = "select * from languages order by sort_order";
            $languages_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $languages_query_raw, $languages_query_numrows);
            $languages_query = tep_db_query($languages_query_raw);

            while ($languages = tep_db_fetch_array($languages_query)) {
              if ((!isset($_GET['lID']) || (isset($_GET['lID']) && ($_GET['lID'] == $languages['languages_id']))) && !isset($lInfo) && (substr($action, 0, 3) != 'new')) {
                $lInfo = new objectInfo($languages);
              }

              if (isset($lInfo) && is_object($lInfo) && ($languages['languages_id'] == $lInfo->languages_id) ) {
                echo '<tr class="table-active" onclick="document.location.href=\'' . tep_href_link('languages.php', 'page=' . (int)$_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=edit') . '\'">';
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('languages.php', 'page=' . (int)$_GET['page'] . '&lID=' . $languages['languages_id']) . '\'">';
              }

              if (DEFAULT_LANGUAGE == $languages['code']) {
                echo '<th>' . $languages['name'] . ' (' . TEXT_DEFAULT . ')</th>';
              } else {
                echo '<td>' . $languages['name'] . '</td>';
              }
              ?>
                <td><?php echo $languages['code']; ?></td>
                <td><?php echo tep_image(tep_catalog_href_link('includes/languages/' . $languages['directory'] . '/images/' . $languages['image'], '', 'SSL')); ?></td>
                <td class="text-right"><?php if (isset($lInfo) && is_object($lInfo) && ($languages['languages_id'] == $lInfo->languages_id)) { echo '<i class="fas fa-chevron-circle-right text-info"></i>'; } else { echo '<a href="' . tep_href_link('languages.php', 'page=' . (int)$_GET['page'] . '&lID=' . $languages['languages_id']) . '"><i class="fas fa-info-circle text-muted"></i></a>'; } ?></td>
              </tr>
              <?php
            }
            ?>
          </tbody>
        </table>
      </div>
      
      <div class="row my-1">
        <div class="col"><?php echo $languages_split->display_count($languages_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_LANGUAGES); ?></div>
        <div class="col text-right mr-2"><?php echo $languages_split->display_links($languages_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></div>
      </div>
      
    </div>

<?php
  $heading = [];
  $contents = [];

  switch ($action) {
    case 'new':
      $heading[] = ['text' => TEXT_INFO_HEADING_NEW_LANGUAGE];

      $contents = ['form' => tep_draw_form('languages', 'languages.php', 'action=insert')];
      $contents[] = ['text' => TEXT_INFO_INSERT_INTRO];
      $contents[] = ['text' => sprintf(TEXT_INFO_LANGUAGE_NAME, null) . '<br>' . tep_draw_input_field('name')];
      $contents[] = ['text' => sprintf(TEXT_INFO_LANGUAGE_CODE, null) . '<br>' . tep_draw_input_field('code')];
      $contents[] = ['text' => sprintf(TEXT_INFO_LANGUAGE_IMAGE, null) . '<br>' . tep_draw_input_field('image', 'icon.gif')];
      $contents[] = ['text' => sprintf(TEXT_INFO_LANGUAGE_DIRECTORY, null, null) . '<br>' . tep_draw_input_field('directory')];
      $contents[] = ['text' => sprintf(TEXT_INFO_LANGUAGE_SORT_ORDER, null) . '<br>' . tep_draw_input_field('sort_order')];
      $contents[] = ['text' => tep_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success xxx text-white mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('languages.php'), null, null, 'btn-light')];
      break;
    case 'edit':
      $heading[] = ['text' => TEXT_INFO_HEADING_EDIT_LANGUAGE];

      $contents = ['form' => tep_draw_form('languages', 'languages.php', 'page=' . (int)$_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=save')];
      $contents[] = ['text' => TEXT_INFO_EDIT_INTRO];
      $contents[] = ['text' => sprintf(TEXT_INFO_LANGUAGE_NAME, null) . '<br>' . tep_draw_input_field('name', $lInfo->name)];
      $contents[] = ['text' => sprintf(TEXT_INFO_LANGUAGE_CODE, null) . '<br>' . tep_draw_input_field('code', $lInfo->code)];
      $contents[] = ['text' => sprintf(TEXT_INFO_LANGUAGE_IMAGE, null) . '<br>' . tep_draw_input_field('image', $lInfo->image)];
      $contents[] = ['text' => sprintf(TEXT_INFO_LANGUAGE_DIRECTORY, null, null) . '<br>' . tep_draw_input_field('directory', $lInfo->directory)];
      $contents[] = ['text' => sprintf(TEXT_INFO_LANGUAGE_SORT_ORDER, null) . '<br>' . tep_draw_input_field('sort_order', $lInfo->sort_order)];
      if (DEFAULT_LANGUAGE != $lInfo->code) $contents[] = ['text' => tep_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT];
      $contents[] = ['class' => 'text-center', 'text' => '<br>' . tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success xxx text-white mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('languages.php', 'page=' . (int)$_GET['page'] . '&lID=' . $lInfo->languages_id), null, null, 'btn-light')];
      break;
    case 'delete':
      $heading[] = ['text' => TEXT_INFO_HEADING_DELETE_LANGUAGE];

      $contents[] = ['text' => TEXT_INFO_DELETE_INTRO];
      $contents[] = ['class' => 'text-center text-uppercase font-weight-bold', 'text' => $lInfo->name];
      $contents[] = ['class' => 'text-center', 'text' => (($remove_language) ? tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', tep_href_link('languages.php', 'page=' . (int)$_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=deleteconfirm'), null, null, 'btn-danger xxx text-white mr-2') : '') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('languages.php', 'page=' . (int)$_GET['page'] . '&lID=' . $lInfo->languages_id), null, null, 'btn-light')];
      break;
    default:
      if (is_object($lInfo)) {
        $heading[] = ['text' => $lInfo->name];

        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_EDIT, 'fas fa-cogs', tep_href_link('languages.php', 'page=' . (int)$_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=edit'), null, null, 'btn-warning mr-2') . tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', tep_href_link('languages.php', 'page=' . (int)$_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=delete'), null, null, 'btn-danger xxx text-white mr-2')]; 
        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_DETAILS, 'fas fa-eye', tep_href_link('define_language.php', 'lngdir=' . $lInfo->directory), null, null, 'btn-info xxx text-white')];
        $contents[] = ['text' => sprintf(TEXT_INFO_LANGUAGE_DIRECTORY, DIR_WS_CATALOG_LANGUAGES, $lInfo->directory)];
        $contents[] = ['text' => sprintf(TEXT_INFO_LANGUAGE_SORT_ORDER, $lInfo->sort_order)];
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
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
