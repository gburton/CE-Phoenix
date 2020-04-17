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
  
  $OSCOM_Hooks->call('manufacturers', 'manufacturersPreAction');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'insert':
      case 'save':
        if (isset($_GET['mID'])) $manufacturers_id = tep_db_prepare_input($_GET['mID']);
        $manufacturers_name = tep_db_prepare_input($_POST['manufacturers_name']);

        $sql_data_array = ['manufacturers_name' => $manufacturers_name];

        if ($action == 'insert') {
          $insert_sql_data = ['date_added' => 'now()'];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          tep_db_perform('manufacturers', $sql_data_array);
          $manufacturers_id = tep_db_insert_id();
        } elseif ($action == 'save') {
          $update_sql_data = ['last_modified' => 'now()'];

          $sql_data_array = array_merge($sql_data_array, $update_sql_data);

          tep_db_perform('manufacturers', $sql_data_array, 'update', "manufacturers_id = '" . (int)$manufacturers_id . "'");
        }

        $manufacturers_image = new upload('manufacturers_image');
        $manufacturers_image->set_destination(DIR_FS_CATALOG_IMAGES);

        if ($manufacturers_image->parse() && $manufacturers_image->save()) {
          tep_db_query("update manufacturers set manufacturers_image = '" . tep_db_input($manufacturers_image->filename) . "' where manufacturers_id = '" . (int)$manufacturers_id . "'");
        }

        $languages = tep_get_languages();
        for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
          $manufacturers_url_array = $_POST['manufacturers_url'];
          $manufacturers_description_array = $_POST['manufacturers_description'];
          $manufacturers_seo_description_array = $_POST['manufacturers_seo_description'];
          $manufacturers_seo_keywords_array = $_POST['manufacturers_seo_keywords'];
          $manufacturers_seo_title_array = $_POST['manufacturers_seo_title'];
          $language_id = $languages[$i]['id'];

          $sql_data_array = ['manufacturers_url' => tep_db_prepare_input($manufacturers_url_array[$language_id])];
          $sql_data_array['manufacturers_description'] = tep_db_prepare_input($manufacturers_description_array[$language_id]);
          $sql_data_array['manufacturers_seo_description'] = tep_db_prepare_input($manufacturers_seo_description_array[$language_id]);
          $sql_data_array['manufacturers_seo_keywords'] = tep_db_prepare_input($manufacturers_seo_keywords_array[$language_id]);
          $sql_data_array['manufacturers_seo_title'] = tep_db_prepare_input($manufacturers_seo_title_array[$language_id]);

          if ($action == 'insert') {
            $insert_sql_data = ['manufacturers_id' => $manufacturers_id, 'languages_id' => $language_id];

            $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

            tep_db_perform('manufacturers_info', $sql_data_array);
          } elseif ($action == 'save') {
            tep_db_perform('manufacturers_info', $sql_data_array, 'update', "manufacturers_id = '" . (int)$manufacturers_id . "' and languages_id = '" . (int)$language_id . "'");
          }
        }
        
        $OSCOM_Hooks->call('manufacturers', 'manufacturersActionSave');

        tep_redirect(tep_href_link('manufacturers.php', (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . 'mID=' . $manufacturers_id));
        break;
      case 'deleteconfirm':
        $manufacturers_id = tep_db_prepare_input($_GET['mID']);

        if (isset($_POST['delete_image']) && ($_POST['delete_image'] == 'on')) {
          $manufacturer_query = tep_db_query("select manufacturers_image from manufacturers where manufacturers_id = '" . (int)$manufacturers_id . "'");
          $manufacturer = tep_db_fetch_array($manufacturer_query);

          $image_location = DIR_FS_DOCUMENT_ROOT . DIR_WS_CATALOG_IMAGES . $manufacturer['manufacturers_image'];

          if (file_exists($image_location)) @unlink($image_location);
        }

        tep_db_query("delete from manufacturers where manufacturers_id = '" . (int)$manufacturers_id . "'");
        tep_db_query("delete from manufacturers_info where manufacturers_id = '" . (int)$manufacturers_id . "'");

        if (isset($_POST['delete_products']) && ($_POST['delete_products'] == 'on')) {
          $products_query = tep_db_query("select products_id from products where manufacturers_id = '" . (int)$manufacturers_id . "'");
          while ($products = tep_db_fetch_array($products_query)) {
            tep_remove_product($products['products_id']);
          }
        } else {
          tep_db_query("update products set manufacturers_id = '' where manufacturers_id = '" . (int)$manufacturers_id . "'");
        }
        
        $OSCOM_Hooks->call('manufacturers', 'manufacturersActionDelete');

        tep_redirect(tep_href_link('manufacturers.php', 'page=' . $_GET['page']));
        break;
    }
  }
  
  $OSCOM_Hooks->call('manufacturers', 'manufacturersPostAction');

  require('includes/template_top.php');
?>
  
  <div class="row">
    <div class="col">
      <h1 class="display-4 mb-2"><?php echo HEADING_TITLE; ?></h1>
    </div>
    <div class="col text-right align-self-center">
      <?php
      if (empty($action)) {
        echo tep_draw_bootstrap_button(BUTTON_INSERT_NEW_MANUFACTURER, 'fas fa-id-card', tep_href_link('manufacturers.php', 'action=new'), null, null, 'btn-danger xxx text-white');
      }
      else {
        echo tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link('manufacturers.php'), null, null, 'btn-light');
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
              <th><?php echo TABLE_HEADING_MANUFACTURERS; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_ACTION; ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $manufacturers_query_raw = "select * from manufacturers order by manufacturers_name";
            $manufacturers_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $manufacturers_query_raw, $manufacturers_query_numrows);
            $manufacturers_query = tep_db_query($manufacturers_query_raw);
            while ($manufacturers = tep_db_fetch_array($manufacturers_query)) {
              if ((!isset($_GET['mID']) || (isset($_GET['mID']) && ($_GET['mID'] == $manufacturers['manufacturers_id']))) && !isset($mInfo) && (substr($action, 0, 3) != 'new')) {
                $manufacturer_products_query = tep_db_query("select count(*) as products_count from products where manufacturers_id = '" . (int)$manufacturers['manufacturers_id'] . "'");
                $manufacturer_products = tep_db_fetch_array($manufacturer_products_query);

                $mInfo_array = array_merge($manufacturers, $manufacturer_products);
                $mInfo = new objectInfo($mInfo_array);
              }

              if (isset($mInfo) && is_object($mInfo) && ($manufacturers['manufacturers_id'] == $mInfo->manufacturers_id)) {
                echo '<tr class="table-active" onclick="document.location.href=\'' . tep_href_link('manufacturers.php', 'page=' . (int)$_GET['page'] . '&mID=' . (int)$manufacturers['manufacturers_id'] . '&action=edit') . '\'">';
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('manufacturers.php', 'page=' . (int)$_GET['page'] . '&mID=' . (int)$manufacturers['manufacturers_id']) . '\'">';
              }
              ?>
                <td><?php echo $manufacturers['manufacturers_name']; ?></td>
                <td class="text-right"><?php if (isset($mInfo) && is_object($mInfo) && ($manufacturers['manufacturers_id'] == $mInfo->manufacturers_id)) { echo '<i class="fas fa-chevron-circle-right text-info"></i>'; } else { echo '<a href="' . tep_href_link('manufacturers.php', 'page=' . $_GET['page'] . '&mID=' . $manufacturers['manufacturers_id']) . '"><i class="fas fa-info-circle text-muted"></i></a>'; } ?></td>
              </tr>
<?php
  }
?>
          </tbody>
        </table>
      </div>
      
      <div class="row my-1">
        <div class="col"><?php echo $manufacturers_split->display_count($manufacturers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_MANUFACTURERS); ?></div>
        <div class="col text-right mr-2"><?php echo $manufacturers_split->display_links($manufacturers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></div>
      </div>
    </div>

<?php
  $heading = [];
  $contents = [];
  
  $col = 6;

  switch ($action) {
    case 'new':
      $heading[] = ['text' => TEXT_HEADING_NEW_MANUFACTURER];

      $contents = ['form' => tep_draw_form('manufacturers', 'manufacturers.php', 'action=insert', 'post', 'enctype="multipart/form-data"')];
      $contents[] = ['text' => TEXT_NEW_INTRO];
      $contents[] = ['text' => TEXT_MANUFACTURERS_NAME . '<br>' . tep_draw_input_field('manufacturers_name')];
      $contents[] = ['text' => TEXT_MANUFACTURERS_IMAGE . '<br><div class="custom-file mb-2">' . tep_draw_input_field('manufacturers_image', '', 'id="inputManufacturersImage"', 'file', null, 'class="form-control-input"') . '<label class="custom-file-label" for="inputManufacturersImage">' . TEXT_MANUFACTURERS_IMAGE_LABEL . '</label></div>'];

      $manufacturer_inputs_string = $manufacturer_description_string = $manufacturer_seo_description_string = $manufacturer_seo_keywords_string = $manufacturer_seo_title_string = '';

      $languages = tep_get_languages();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $manufacturer_inputs_string .= '<div class="input-group"><div class="input-group-prepend"><span class="input-group-text">' . tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']) . '</span></div>' . tep_draw_input_field('manufacturers_url[' . $languages[$i]['id'] . ']') . '</div>';
        $manufacturer_description_string .= '<div class="input-group"><div class="input-group-prepend"><span class="input-group-text">' . tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name'], '', '', 'style="vertical-align: top;"') . '</span></div>' . tep_draw_textarea_field('manufacturers_description[' . $languages[$i]['id'] . ']', 'soft', '80', '10') . '</div>';
        $manufacturer_seo_description_string .= '<div class="input-group"><div class="input-group-prepend"><span class="input-group-text">' . tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name'], '', '', 'style="vertical-align: top;"') . '</span></div>' . tep_draw_textarea_field('manufacturers_seo_description[' . $languages[$i]['id'] . ']', 'soft', '80', '10') . '</div>';
        $manufacturer_seo_keywords_string .= '<div class="input-group"><div class="input-group-prepend"><span class="input-group-text">' . tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']) . '</span></div>' . tep_draw_input_field('manufacturers_seo_keywords[' . $languages[$i]['id'] . ']', NULL, 'placeholder="' . PLACEHOLDER_COMMA_SEPARATION . '"') . '</div>';
        $manufacturer_seo_title_string .= '<div class="input-group"><div class="input-group-prepend"><span class="input-group-text">' . tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']) . '</span></div>' . tep_draw_input_field('manufacturers_seo_title[' . $languages[$i]['id'] . ']') . '</div>';
      }

      $contents[] = ['text' => TEXT_MANUFACTURERS_URL . $manufacturer_inputs_string];
      $contents[] = ['text' => TEXT_MANUFACTURERS_SEO_TITLE . $manufacturer_seo_title_string];
      $contents[] = ['text' => TEXT_MANUFACTURERS_DESCRIPTION . $manufacturer_description_string];
      $contents[] = ['text' => TEXT_MANUFACTURERS_SEO_DESCRIPTION . $manufacturer_seo_description_string];
      $contents[] = ['text' => TEXT_MANUFACTURERS_SEO_KEYWORDS . $manufacturer_seo_keywords_string];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success xxx text-white mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('manufacturers.php'), null, null, 'btn-light')];
      break;
    case 'edit':
      $heading[] = ['text' => TEXT_HEADING_EDIT_MANUFACTURER . ' <small>' . TEXT_EDIT_INTRO . '</small>'];

      $contents = ['form' => tep_draw_form('manufacturers', 'manufacturers.php', 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=save', 'post', 'enctype="multipart/form-data"')];
      $contents[] = ['text' => TEXT_EDIT_INTRO];
      $contents[] = ['text' => TEXT_MANUFACTURERS_NAME . '<br>' . tep_draw_input_field('manufacturers_name', $mInfo->manufacturers_name)];
      $contents[] = ['text' => TEXT_MANUFACTURERS_IMAGE . '<br><div class="custom-file mb-2">' . tep_draw_input_field('manufacturers_image', '', 'id="inputManufacturersImage"', 'file', null, 'class="form-control-input"') . '<label class="custom-file-label" for="inputManufacturersImage">' . $mInfo->manufacturers_image . '</label></div>'];
      
      $manufacturer_inputs_string = $manufacturer_description_string = $manufacturer_seo_description_string = $manufacturer_seo_keywords_string = $manufacturer_seo_title_string = '';
      $languages = tep_get_languages();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        
        $manufacturer_inputs_string .= '<div class="input-group"><div class="input-group-prepend"><span class="input-group-text">' . tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']) . '</span></div>' . tep_draw_input_field('manufacturers_url[' . $languages[$i]['id'] . ']', tep_get_manufacturer_url($mInfo->manufacturers_id, $languages[$i]['id'])) . '</div>';
        $manufacturer_seo_title_string .= '<div class="input-group"><div class="input-group-prepend"><span class="input-group-text">' . tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']) . '</span></div>' . tep_draw_input_field('manufacturers_seo_title[' . $languages[$i]['id'] . ']', tep_get_manufacturer_seo_title($mInfo->manufacturers_id, $languages[$i]['id'])) . '</div>';
        $manufacturer_description_string .= '<div class="input-group"><div class="input-group-prepend"><span class="input-group-text">' . tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name'], '', '', 'style="vertical-align: top;"') . '</span></div>' . tep_draw_textarea_field('manufacturers_description[' . $languages[$i]['id'] . ']', 'soft', '80', '10', tep_get_manufacturer_description($mInfo->manufacturers_id, $languages[$i]['id'])) . '</div>';
        $manufacturer_seo_description_string .= '<div class="input-group"><div class="input-group-prepend"><span class="input-group-text">' . tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name'], '', '', 'style="vertical-align: top;"') . '</span></div>' . tep_draw_textarea_field('manufacturers_seo_description[' . $languages[$i]['id'] . ']', 'soft', '80', '10', tep_get_manufacturer_seo_description($mInfo->manufacturers_id, $languages[$i]['id'])) . '</div>';
        $manufacturer_seo_keywords_string .= '<div class="input-group"><div class="input-group-prepend"><span class="input-group-text">' . tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']) . '</span></div>' . tep_draw_input_field('manufacturers_seo_keywords[' . $languages[$i]['id'] . ']', tep_get_manufacturer_seo_keywords($mInfo->manufacturers_id, $languages[$i]['id']), 'placeholder="' . PLACEHOLDER_COMMA_SEPARATION . '"') . '</div>';
      }

      $contents[] = ['text' => TEXT_MANUFACTURERS_URL . $manufacturer_inputs_string];
      $contents[] = ['text' => TEXT_EDIT_MANUFACTURERS_SEO_TITLE . $manufacturer_seo_title_string];
      $contents[] = ['text' => TEXT_EDIT_MANUFACTURERS_DESCRIPTION . $manufacturer_description_string];
      $contents[] = ['text' => TEXT_EDIT_MANUFACTURERS_SEO_DESCRIPTION . $manufacturer_seo_description_string];
      $contents[] = ['text' => TEXT_EDIT_MANUFACTURERS_SEO_KEYWORDS . $manufacturer_seo_keywords_string];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success xxx text-white mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('manufacturers.php', 'page=' . $_GET['page'] . '&mID=' . (int)$mInfo->manufacturers_id), null, null, 'btn-light')];
      break;
    case 'delete':
      $col = 3;
      $heading[] = ['text' =>  TEXT_HEADING_DELETE_MANUFACTURER];

      $contents = ['form' => tep_draw_form('manufacturers', 'manufacturers.php', 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=deleteconfirm')];
      $contents[] = ['text' => TEXT_DELETE_INTRO];
      $contents[] = ['text' => '<strong>' . $mInfo->manufacturers_name . '</strong>'];
      $contents[] = ['text' => tep_draw_checkbox_field('delete_image', '', true) . ' ' . TEXT_DELETE_IMAGE];

      if ($mInfo->products_count > 0) {
        $contents[] = ['text' => tep_draw_checkbox_field('delete_products') . ' ' . TEXT_DELETE_PRODUCTS];
        $contents[] = ['text' => sprintf(TEXT_DELETE_WARNING_PRODUCTS, $mInfo->products_count)];
      }

      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', null, 'primary', null, 'btn-danger xxx text-white mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('manufacturers.php', 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id), null, null, 'btn-light')];
      break;
    default:
      $col = 3;
      if (isset($mInfo) && is_object($mInfo)) {
        $heading[] = ['text' => $mInfo->manufacturers_name];

        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_EDIT, 'fas fa-cogs', tep_href_link('manufacturers.php', 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=edit'), null, null, 'btn-warning mr-2') . tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', tep_href_link('manufacturers.php', 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=delete'), null, null, 'btn-danger xxx text-white')];
        $contents[] = ['text' => sprintf(TEXT_DATE_ADDED, tep_date_short($mInfo->date_added))];
        if (tep_not_null($mInfo->last_modified)) $contents[] = ['text' => sprintf(TEXT_LAST_MODIFIED, tep_date_short($mInfo->last_modified))];
        $contents[] = ['text' => tep_info_image($mInfo->manufacturers_image, $mInfo->manufacturers_name)];
        $contents[] = ['text' => sprintf(TEXT_PRODUCTS, $mInfo->products_count)];
      }
      break;
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '<div class="col-12 col-sm-' . $col . '">';
      $box = new box;
      echo $box->infoBox($heading, $contents);
    echo '</div>';
  }
?>

  </div>
  
  <script>$(document).on('change', '#inputManufacturersImage', function (event) { $(this).next('.custom-file-label').html(event.target.files[0].name); });</script>  

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
