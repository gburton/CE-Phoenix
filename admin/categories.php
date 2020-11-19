<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  $currencies = new currencies();

  // calculate category path
  $cPath = $_GET['cPath'] ?? '';
  if (tep_not_null($cPath)) {
    $cPath_array = tep_parse_category_path($cPath);
    $cPath = implode('_', $cPath_array);
    $current_category_id = end($cPath_array);
  } else {
    $current_category_id = 0;
  }

  const DIR_FS_CATALOG_IMAGES = DIR_FS_CATALOG . 'images/';

  $action = $_GET['action'] ?? '';

  $OSCOM_Hooks->call('categories', 'preAction');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'setflag':
        tep_db_query("UPDATE products SET products_status = '" . (int)$_GET['flag'] . "', products_last_modified = NOW() WHERE products_id = " . (int)$_GET['pID']);

        $OSCOM_Hooks->call('categories', 'setFlagAction');

        tep_redirect(tep_href_link('categories.php', 'cPath=' . $_GET['cPath'] . '&pID=' . (int)$_GET['pID']));
        break;
      case 'insert_category':
      case 'update_category':
        if (isset($_POST['categories_id'])) {
          $categories_id = tep_db_prepare_input($_POST['categories_id']);
        }
        $sort_order = tep_db_prepare_input($_POST['sort_order']);

        $sql_data_array = ['sort_order' => (int)$sort_order];

        if ($action == 'insert_category') {
          $insert_sql_data = ['parent_id' => $current_category_id,
                                   'date_added' => 'now()'];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          tep_db_perform('categories', $sql_data_array);

          $categories_id = tep_db_insert_id();
        } elseif ($action == 'update_category') {
          $update_sql_data = ['last_modified' => 'now()'];

          $sql_data_array = array_merge($sql_data_array, $update_sql_data);

          tep_db_perform('categories', $sql_data_array, 'update', "categories_id = '" . (int)$categories_id . "'");
        }

        foreach (tep_get_languages() as $l) {
          $categories_name_array = $_POST['categories_name'];
          $categories_description_array = $_POST['categories_description'];
          $categories_seo_description_array = $_POST['categories_seo_description'];
          $categories_seo_title_array = $_POST['categories_seo_title'];

          $language_id = $l['id'];

          $sql_data_array = ['categories_name' => tep_db_prepare_input($categories_name_array[$language_id])];
          $sql_data_array['categories_description'] = tep_db_prepare_input($categories_description_array[$language_id]);
          $sql_data_array['categories_seo_description'] = tep_db_prepare_input($categories_seo_description_array[$language_id]);
          $sql_data_array['categories_seo_title'] = tep_db_prepare_input($categories_seo_title_array[$language_id]);

          if ($action == 'insert_category') {
            $insert_sql_data = ['categories_id' => $categories_id, 'language_id' => $l['id']];

            $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

            $OSCOM_Hooks->call('categories', 'insertCategoryAction');

            tep_db_perform('categories_description', $sql_data_array);
          } elseif ($action == 'update_category') {
            $OSCOM_Hooks->call('categories', 'updateCategoryAction');

            tep_db_perform('categories_description', $sql_data_array, 'update', "categories_id = '" . (int)$categories_id . "' and language_id = '" . (int)$l['id'] . "'");
          }
        }

        $categories_image = new upload('categories_image');
        $categories_image->set_destination(DIR_FS_CATALOG_IMAGES);

        if ($categories_image->parse() && $categories_image->save()) {
          tep_db_query("update categories set categories_image = '" . tep_db_input($categories_image->filename) . "' where categories_id = '" . (int)$categories_id . "'");
        }

        $OSCOM_Hooks->call('categories', 'insertCategoryUpdateCategoryAction');

        tep_redirect(tep_href_link('categories.php', 'cPath=' . $cPath . '&cID=' . $categories_id));
        break;
      case 'delete_category_confirm':
        if (isset($_POST['categories_id'])) {
          $categories_id = tep_db_prepare_input($_POST['categories_id']);

          $categories = tep_get_category_tree($categories_id, '', '0', '', true);
          $products = [];
          $products_delete = [];

          for ($i=0, $n=count($categories); $i<$n; $i++) {
            $product_ids_query = tep_db_query("select products_id from products_to_categories where categories_id = '" . (int)$categories[$i]['id'] . "'");

            while ($product_ids = tep_db_fetch_array($product_ids_query)) {
              $products[$product_ids['products_id']]['categories'][] = $categories[$i]['id'];
            }
          }

          foreach ($products as $key => $value) {
            $category_ids = '';

            for ($i=0, $n=count($value['categories']); $i<$n; $i++) {
              $category_ids .= "'" . (int)$value['categories'][$i] . "', ";
            }
            $category_ids = substr($category_ids, 0, -2);

            $check_query = tep_db_query("select count(*) as total from products_to_categories where products_id = '" . (int)$key . "' and categories_id not in (" . $category_ids . ")");
            $check = tep_db_fetch_array($check_query);
            if ($check['total'] < '1') {
              $products_delete[$key] = $key;
            }
          }

// removing categories can be a lengthy process
          tep_set_time_limit(0);
          for ($i=0, $n=count($categories); $i<$n; $i++) {
            tep_remove_category($categories[$i]['id']);
          }

          foreach ($products_delete as $key) {
            tep_remove_product($key);
          }
        }

        $OSCOM_Hooks->call('categories', 'deleteCategoryConfirmAction');

        tep_redirect(tep_href_link('categories.php', 'cPath=' . $cPath));
        break;
      case 'delete_product_confirm':
        if (isset($_POST['products_id']) && isset($_POST['product_categories']) && is_array($_POST['product_categories'])) {
          $product_id = tep_db_prepare_input($_POST['products_id']);
          $product_categories = $_POST['product_categories'];

          for ($i=0, $n=count($product_categories); $i<$n; $i++) {
            tep_db_query("delete from products_to_categories where products_id = '" . (int)$product_id . "' and categories_id = '" . (int)$product_categories[$i] . "'");
          }

          $product_categories_query = tep_db_query("select count(*) as total from products_to_categories where products_id = '" . (int)$product_id . "'");
          $product_categories = tep_db_fetch_array($product_categories_query);

          if ($product_categories['total'] == '0') {
            tep_remove_product($product_id);
          }
        }

        $OSCOM_Hooks->call('categories', 'deleteProductConfirmAction');

        tep_redirect(tep_href_link('categories.php', 'cPath=' . $cPath));
        break;
      case 'move_category_confirm':
        if (isset($_POST['categories_id']) && ($_POST['categories_id'] != $_POST['move_to_category_id'])) {
          $categories_id = tep_db_prepare_input($_POST['categories_id']);
          $new_parent_id = tep_db_prepare_input($_POST['move_to_category_id']);

          $path = explode('_', tep_get_generated_category_path_ids($new_parent_id));

          if (in_array($categories_id, $path)) {
            $messageStack->add_session(ERROR_CANNOT_MOVE_CATEGORY_TO_PARENT, 'error');

            tep_redirect(tep_href_link('categories.php', 'cPath=' . $cPath . '&cID=' . $categories_id));
          } else {
            tep_db_query("update categories set parent_id = '" . (int)$new_parent_id . "', last_modified = now() where categories_id = '" . (int)$categories_id . "'");

            $OSCOM_Hooks->call('categories', 'moveCategoryConfirmAction');

            tep_redirect(tep_href_link('categories.php', 'cPath=' . $new_parent_id . '&cID=' . $categories_id));
          }
        }

        break;
      case 'move_product_confirm':
        $products_id = tep_db_prepare_input($_POST['products_id']);
        $new_parent_id = tep_db_prepare_input($_POST['move_to_category_id']);

        $duplicate_check_query = tep_db_query("select count(*) as total from products_to_categories where products_id = '" . (int)$products_id . "' and categories_id = '" . (int)$new_parent_id . "'");
        $duplicate_check = tep_db_fetch_array($duplicate_check_query);
        if ($duplicate_check['total'] < 1) tep_db_query("update products_to_categories set categories_id = '" . (int)$new_parent_id . "' where products_id = '" . (int)$products_id . "' and categories_id = '" . (int)$current_category_id . "'");

        $OSCOM_Hooks->call('categories', 'moveProductConfirmAction');

        tep_redirect(tep_href_link('categories.php', 'cPath=' . $new_parent_id . '&pID=' . $products_id));
        break;
      case 'insert_product':
      case 'update_product':
        if (isset($_GET['pID'])) $products_id = tep_db_prepare_input($_GET['pID']);
        $products_date_available = tep_db_prepare_input($_POST['products_date_available']);

        $products_date_available = (date('Y-m-d') < $products_date_available) ? $products_date_available : 'null';

        $sql_data_array = ['products_quantity' => (int)tep_db_prepare_input($_POST['products_quantity']),
                           'products_model' => tep_db_prepare_input($_POST['products_model']),
                           'products_price' => tep_db_prepare_input($_POST['products_price']),
                           'products_date_available' => $products_date_available,
                           'products_weight' => (float)tep_db_prepare_input($_POST['products_weight']),
                           'products_status' => tep_db_prepare_input($_POST['products_status']),
                           'products_tax_class_id' => tep_db_prepare_input($_POST['products_tax_class_id']),
                           'manufacturers_id' => (int)tep_db_prepare_input($_POST['manufacturers_id'])];
        $sql_data_array['products_gtin'] = (tep_not_null($_POST['products_gtin'])) ? str_pad(tep_db_prepare_input($_POST['products_gtin']), 14, '0', STR_PAD_LEFT) : 'null';

        $products_image = new upload('products_image');
        $products_image->set_destination(DIR_FS_CATALOG_IMAGES);
        if ($products_image->parse() && $products_image->save()) {
          $sql_data_array['products_image'] = tep_db_prepare_input($products_image->filename);
        }

        if ($action == 'insert_product') {
          $insert_sql_data = ['products_date_added' => 'now()'];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          tep_db_perform('products', $sql_data_array);
          $products_id = tep_db_insert_id();

          tep_db_query("insert into products_to_categories (products_id, categories_id) values ('" . (int)$products_id . "', '" . (int)$current_category_id . "')");
        } elseif ($action == 'update_product') {
          $update_sql_data = ['products_last_modified' => 'now()'];

          $sql_data_array = array_merge($sql_data_array, $update_sql_data);

          tep_db_perform('products', $sql_data_array, 'update', "products_id = '" . (int)$products_id . "'");
        }

        foreach (tep_get_languages() as $l) {
          $language_id = $l['id'];

          $sql_data_array = ['products_name' => tep_db_prepare_input($_POST['products_name'][$language_id]),
                             'products_description' => tep_db_prepare_input($_POST['products_description'][$language_id]),
                             'products_url' => tep_db_prepare_input($_POST['products_url'][$language_id])];
          $sql_data_array['products_seo_description'] = tep_db_prepare_input($_POST['products_seo_description'][$language_id]);
          $sql_data_array['products_seo_keywords'] = tep_db_prepare_input($_POST['products_seo_keywords'][$language_id]);
          $sql_data_array['products_seo_title'] = tep_db_prepare_input($_POST['products_seo_title'][$language_id]);

          if ($action == 'insert_product') {
            $insert_sql_data = ['products_id' => $products_id, 'language_id' => $language_id];

            $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

            tep_db_perform('products_description', $sql_data_array);
          } elseif ($action == 'update_product') {
            tep_db_perform('products_description', $sql_data_array, 'update', "products_id = '" . (int)$products_id . "' and language_id = '" . (int)$language_id . "'");
          }
        }

        $pi_sort_order = 0;
        $piArray = [0];

        foreach ($_FILES as $key => $value) {
// Update existing large product images
          if (preg_match('/^products_image_large_([0-9]+)$/', $key, $matches)) {
            $pi_sort_order++;

            $sql_data_array = ['htmlcontent' => tep_db_prepare_input($_POST['products_image_htmlcontent_' . $matches[1]]), 'sort_order' => $pi_sort_order];

            $t = new upload($key);
            $t->set_destination(DIR_FS_CATALOG_IMAGES);
            if ($t->parse() && $t->save()) {
              $sql_data_array['image'] = tep_db_prepare_input($t->filename);
            }

            tep_db_perform('products_images', $sql_data_array, 'update', "products_id = '" . (int)$products_id . "' and id = '" . (int)$matches[1] . "'");

            $piArray[] = (int)$matches[1];
          } elseif (preg_match('/^products_image_large_new_([0-9]+)$/', $key, $matches)) {
// Insert new large product images
            $sql_data_array = ['products_id' => (int)$products_id, 'htmlcontent' => tep_db_prepare_input($_POST['products_image_htmlcontent_new_' . $matches[1]])];

            $t = new upload($key);
            $t->set_destination(DIR_FS_CATALOG_IMAGES);
            if ($t->parse() && $t->save()) {
              $pi_sort_order++;

              $sql_data_array['image'] = tep_db_prepare_input($t->filename);
              $sql_data_array['sort_order'] = $pi_sort_order;

              tep_db_perform('products_images', $sql_data_array);

              $piArray[] = tep_db_insert_id();
            }
          }
        }

        $product_images_query = tep_db_query("select image from products_images where products_id = '" . (int)$products_id . "' and id not in (" . implode(',', $piArray) . ")");
        if (tep_db_num_rows($product_images_query)) {
          while ($product_images = tep_db_fetch_array($product_images_query)) {
            $duplicate_image_query = tep_db_query("select count(*) as total from products_images where image = '" . tep_db_input($product_images['image']) . "'");
            $duplicate_image = tep_db_fetch_array($duplicate_image_query);

            if ($duplicate_image['total'] < 2) {
              if (file_exists(DIR_FS_CATALOG_IMAGES . $product_images['image'])) {
                @unlink(DIR_FS_CATALOG_IMAGES . $product_images['image']);
              }
            }
          }

          tep_db_query("delete from products_images where products_id = '" . (int)$products_id . "' and id not in (" . implode(',', $piArray) . ")");
        }

        $OSCOM_Hooks->call('categories', 'productActionSave');

        tep_redirect(tep_href_link('categories.php', 'cPath=' . $cPath . '&pID=' . $products_id));
        break;
      case 'copy_to_confirm':
        if (isset($_POST['products_id'], $_POST['categories_id'])) {
          $products_id = tep_db_prepare_input($_POST['products_id']);
          $categories_id = tep_db_prepare_input($_POST['categories_id']);

          if ($_POST['copy_as'] == 'link') {
            if ($categories_id != $current_category_id) {
              $check_query = tep_db_query("select count(*) as total from products_to_categories where products_id = '" . (int)$products_id . "' and categories_id = '" . (int)$categories_id . "'");
              $check = tep_db_fetch_array($check_query);
              if ($check['total'] < '1') {
                tep_db_query("insert into products_to_categories (products_id, categories_id) values ('" . (int)$products_id . "', '" . (int)$categories_id . "')");
              }
            } else {
              $messageStack->add_session(ERROR_CANNOT_LINK_TO_SAME_CATEGORY, 'error');
            }
          } elseif ($_POST['copy_as'] == 'duplicate') {
            $db = [
              'products' => [
                'products_quantity' => null,
                'products_model' => null,
                'products_image' => null,
                'products_price' => null,
                'products_date_added' => 'NOW()',
                'products_date_available' => null,
                'products_weight' => null,
                'products_status' => 0,
                'products_tax_class_id' => null,
                'manufacturers_id' => null,
                'products_gtin' => null,
              ],
              'products_description' => [
                'products_id' => null,
                'language_id' => null,
                'products_name' => null,
                'products_description' => null,
                'products_url' => null,
                'products_viewed' => 0,
                'products_seo_title' => null,
                'products_seo_description' => null,
                'products_seo_keywords' => null,
              ],
              'products_images' => [
                'products_id' => null,
                'image' => null,
                'htmlcontent' => null,
                'sort_order' => null,
              ],
            ];

            $parameters = ['db' => &$db];
            $OSCOM_Hooks->call('categories', 'preDuplicateCopyToConfirmAction', $parameters);
            $products_id = tep_db_copy($db, 'products_id', (int)$products_id);
            tep_db_query("INSERT INTO products_to_categories (products_id, categories_id) VALUES (" . (int)$products_id . ", " . (int)$categories_id . ")");
          }
        }

        $OSCOM_Hooks->call('categories', 'copyToConfirmAction');

        tep_redirect(tep_href_link('categories.php', 'cPath=' . $categories_id . '&pID=' . $products_id));
        break;
    }
  }

  $OSCOM_Hooks->call('categories', 'postAction');

// check if the catalog image directory exists
  if (is_dir(DIR_FS_CATALOG_IMAGES)) {
    if (!tep_is_writable(DIR_FS_CATALOG_IMAGES)) {
      $messageStack->add(sprintf(ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE, DIR_FS_CATALOG_IMAGES), 'error');
    }
  } else {
    $messageStack->add(sprintf(ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST, DIR_FS_CATALOG_IMAGES), 'error');
  }

  require 'includes/template_top.php';

  $base_url = HTTP_SERVER . DIR_WS_ADMIN;

  if ($action == 'new_product') {
    $parameters = ['products_name' => '',
                   'products_description' => '',
                   'products_url' => '',
                   'products_id' => '',
                   'products_quantity' => '',
                   'products_model' => '',
                   'products_image' => '',
                   'products_larger_images' => [],
                   'products_price' => '',
                   'products_weight' => '',
                   'products_date_added' => '',
                   'products_last_modified' => '',
                   'products_date_available' => '',
                   'products_status' => '',
                   'products_tax_class_id' => '',
                   'manufacturers_id' => ''];
    $parameters['products_gtin'] = '';
    $parameters['products_seo_description'] = '';
    $parameters['products_seo_keywords'] = '';
    $parameters['products_seo_title'] = '';

    $pInfo = new objectInfo($parameters);

    if (isset($_GET['pID']) && empty($_POST)) {
      $product_query = tep_db_query("select pd.*, p.*, date_format(p.products_date_available, '%Y-%m-%d') as products_date_available from products p, products_description pd where p.products_id = '" . (int)$_GET['pID'] . "' and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "'");
      $product = tep_db_fetch_array($product_query);

      $pInfo->objectInfo($product);

      $product_images_query = tep_db_query("select id, image, htmlcontent, sort_order from products_images where products_id = '" . (int)$product['products_id'] . "' order by sort_order");
      while ($product_images = tep_db_fetch_array($product_images_query)) {
        $pInfo->products_larger_images[] = ['id' => $product_images['id'],
                                            'image' => $product_images['image'],
                                            'htmlcontent' => $product_images['htmlcontent'],
                                            'sort_order' => $product_images['sort_order']];
      }
    }

    $manufacturers_array = [['id' => '', 'text' => TEXT_NONE]];
    $manufacturers_query = tep_db_query("select manufacturers_id, manufacturers_name from manufacturers order by manufacturers_name");
    while ($manufacturers = tep_db_fetch_array($manufacturers_query)) {
      $manufacturers_array[] = ['id' => $manufacturers['manufacturers_id'], 'text' => $manufacturers['manufacturers_name']];
    }

    $tax_class_array = [['id' => '0', 'text' => TEXT_NONE]];
    $tax_class_query = tep_db_query("select tax_class_id, tax_class_title from tax_class order by tax_class_title");
    while ($tax_class = tep_db_fetch_array($tax_class_query)) {
      $tax_class_array[] = ['id' => $tax_class['tax_class_id'], 'text' => $tax_class['tax_class_title']];
    }

    $languages = tep_get_languages();

    if (!isset($pInfo->products_status)) {
      $pInfo->products_status = '1';
    }
    $out_status = ('0' === $pInfo->products_status);
    $in_status = !$out_status;

    $form_action = (isset($_GET['pID'])) ? 'update_product' : 'insert_product';
?>
<script>
var tax_rates = new Array();
<?php
    for ($i=0, $n=count($tax_class_array); $i<$n; $i++) {
      if ($tax_class_array[$i]['id'] > 0) {
        echo 'tax_rates["' . $tax_class_array[$i]['id'] . '"] = ' . tep_get_tax_rate_value($tax_class_array[$i]['id']) . ';' . "\n";
      }
    }
?>

function doRound(x, places) {
  return Math.round(x * Math.pow(10, places)) / Math.pow(10, places);
}

function getTaxRate() {
  var selected_value = document.forms["new_product"].products_tax_class_id.selectedIndex;
  var parameterVal = document.forms["new_product"].products_tax_class_id[selected_value].value;

  if ( (parameterVal > 0) && (tax_rates[parameterVal] > 0) ) {
    return tax_rates[parameterVal];
  } else {
    return 0;
  }
}

function updateGross() {
  var taxRate = getTaxRate();
  var grossValue = document.forms["new_product"].products_price.value;

  if (taxRate > 0) {
    grossValue = grossValue * ((taxRate / 100) + 1);
  }

  document.forms["new_product"].products_price_gross.value = doRound(grossValue, 4);
}

function updateNet() {
  var taxRate = getTaxRate();
  var netValue = document.forms["new_product"].products_price_gross.value;

  if (taxRate > 0) {
    netValue = netValue / ((taxRate / 100) + 1);
  }

  document.forms["new_product"].products_price.value = doRound(netValue, 4);
}
</script>

<?= tep_draw_form('new_product', 'categories.php', 'cPath=' . $cPath . (isset($_GET['pID']) ? '&pID=' . (int)$_GET['pID'] : '') . '&action=' . $form_action, 'post', 'enctype="multipart/form-data"') ?>

  <div class="row">
    <div class="col">
      <h1 class="display-4 mb-2"><?= (isset($_GET['pID'])) ? sprintf(TEXT_EXISTING_PRODUCT, $pInfo->products_name, tep_output_generated_category_path($current_category_id)) : sprintf(TEXT_NEW_PRODUCT, tep_output_generated_category_path($current_category_id)) ?></h1>
    </div>
    <div class="col-1 text-right align-self-center">
      <?= tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link('categories.php', tep_get_all_get_params(['action'])), null, null, 'btn-light') ?>
    </div>
  </div>

  <div id="productTabs">
    <ul class="nav nav-tabs">
      <li class="nav-item"><?= '<a class="nav-link active" data-toggle="tab" href="#section_data_content" role="tab">' . SECTION_HEADING_DATA . '</a>' ?></li>
      <li class="nav-item"><?= '<a class="nav-link" data-toggle="tab" href="#section_general_content" role="tab">' . SECTION_HEADING_GENERAL . '</a>' ?></li>
      <li class="nav-item"><?= '<a class="nav-link" data-toggle="tab" href="#section_images_content" role="tab">' . SECTION_HEADING_IMAGES . '</a>' ?></li>
    </ul>

    <div class="tab-content pt-3">
      <div class="tab-pane fade show active" id="section_data_content" role="tabpanel">
        <div class="form-group row align-items-center" id="zStatus">
          <label class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_STATUS ?></label>
          <div class="col-sm-9">
            <div class="custom-control custom-radio custom-control-inline">
              <?= tep_draw_selection_field('products_status', 'radio', '1', $in_status, 'id="pIn" class="custom-control-input"') . '<label class="custom-control-label" for="pIn">' . TEXT_PRODUCT_AVAILABLE . '</label>' ?>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
              <?= tep_draw_selection_field('products_status', 'radio', '0', $out_status, 'id="pOut" class="custom-control-input"') . '<label class="custom-control-label" for="pOut">' . TEXT_PRODUCT_NOT_AVAILABLE . '</label>' ?>
            </div>
          </div>
        </div>

        <div class="form-group row" id="zQty">
          <label for="pQty" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_QUANTITY ?></label>
          <div class="col-sm-9">
            <?= tep_draw_input_field('products_quantity', $pInfo->products_quantity, 'required aria-required="true" id="pQty" class="form-control w-25"'); ?>
          </div>
        </div>

        <div class="form-group row" id="zDate">
          <label for="products_date_available" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_DATE_AVAILABLE ?></label>
          <div class="col-sm-9">
            <?= tep_draw_input_field('products_date_available', $pInfo->products_date_available, 'class="form-control w-25" id="products_date_available" aria-describedby="pDateHelp"');?>
            <small id="pDateHelp" class="form-text text-muted">
              <?= TEXT_PRODUCTS_DATE_AVAILABLE_HELP ?>
            </small>
          </div>
        </div>

        <hr>

        <div class="form-group row" id="zBrand">
          <label for="pBrand" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_MANUFACTURER ?></label>
          <div class="col-sm-9">
            <?= tep_draw_pull_down_menu('manufacturers_id', $manufacturers_array, $pInfo->manufacturers_id, 'id="pBrand"'); ?>
          </div>
        </div>

        <div class="form-group row" id="zModel">
          <label for="pModel" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_MODEL ?></label>
          <div class="col-sm-9">
            <?= tep_draw_input_field('products_model', $pInfo->products_model, 'id="pModel"'); ?>
          </div>
        </div>

        <hr>

        <div class="form-group row" id="zTax">
          <label for="pTax" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_TAX_CLASS ?></label>
          <div class="col-sm-9">
            <?= tep_draw_pull_down_menu('products_tax_class_id', $tax_class_array, $pInfo->products_tax_class_id, 'id="pTax" onchange="updateGross()"'); ?>
          </div>
        </div>

        <div class="form-group row" id="zNet">
          <label for="pNet" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_PRICE_NET ?></label>
          <div class="col-sm-9">
            <?= tep_draw_input_field('products_price', $pInfo->products_price, 'required aria-required="true" id="pNet" class="form-control w-25" onchange="updateGross()"'); ?>
          </div>
        </div>
        <div class="form-group row" id="zGross">
          <label for="pGross" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_PRICE_GROSS ?></label>
          <div class="col-sm-9">
            <?= tep_draw_input_field('products_price_gross', $pInfo->products_price, 'id="pGross" class="form-control w-25" onchange="updateNet()"'); ?>
          </div>
        </div>

        <hr>

        <div class="form-group row" id="zWeight">
          <label for="pWeight" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_WEIGHT ?></label>
          <div class="col-sm-9">
            <?= tep_draw_input_field('products_weight', $pInfo->products_weight, 'id="pWeight" class="form-control w-25"'); ?>
          </div>
        </div>

        <div class="form-group row" id="zGtin">
          <label for="pGtin" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_GTIN ?></label>
          <div class="col-sm-9">
            <?= tep_draw_input_field('products_gtin', $pInfo->products_gtin, 'id="pGtin" class="form-control w-25" aria-describedby="pGtinHelp"'); ?>
            <small id="pGtinHelp" class="form-text text-muted">
            <?= TEXT_PRODUCTS_GTIN_HELP ?>
            </small>
          </div>
        </div>

        <?= $OSCOM_Hooks->call('categories', 'injectDataForm'); ?>

      </div>

      <div class="tab-pane fade" id="section_general_content" role="tabpanel">
        <div class="accordion" id="productLanguageAccordion">
          <?php
          foreach ($languages as $l) {
            $show = ($i == 0) ? ' show' : null;
            ?>
            <div class="card">
              <div class="card-header" id="heading<?= $l['directory'] ?>">
                <button class="btn btn-info" type="button" data-toggle="collapse" data-target="#<?= $l['directory'] ?>" aria-expanded="true" aria-controls="<?= $l['directory'] ?>"><?= tep_image(tep_catalog_href_link('includes/languages/' . $l['directory'] . '/images/' . $l['image']), $l['name'], null, null, null, false, 'lng mr-2') . $l['name'] ?></button>
              </div>
              <div id="<?= $l['directory'] ?>" class="collapse<?= $show ?>" aria-labelledby="heading<?= $l['directory'] ?>" data-parent="#productLanguageAccordion">
                <div class="card-body">
                  <div class="form-group row" id="zName<?= $l['directory']; ?>">
                    <label for="pName" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_NAME ?></label>
                    <div class="col-sm-9">
                      <?= tep_draw_input_field('products_name[' . $l['id'] . ']', (empty($pInfo->products_id) ? '' : tep_get_products_name($pInfo->products_id, $l['id'])), 'required aria-required="true" class="form-control" id="pName"'); ?>
                    </div>
                  </div>

                  <div class="form-group row" id="zDesc<?= $l['directory']; ?>">
                    <label for="pDesc" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_DESCRIPTION ?></label>
                    <div class="col-sm-9">
                      <?= tep_draw_textarea_field('products_description[' . $l['id'] . ']', 'soft', '70', '15', (empty($pInfo->products_id) ? '' : tep_get_products_description($pInfo->products_id, $l['id'])), 'required aria-required="true" class="form-control" id="pDesc"'); ?>
                    </div>
                  </div>

                  <div class="form-group row" id="zUrl<?= $l['directory']; ?>">
                    <label for="pUrl" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_URL ?></label>
                    <div class="col-sm-9">
                      <?= tep_draw_input_field('products_url[' . $l['id'] . ']', (isset($products_url[$l['id']]) ? stripslashes($products_url[$l['id']]) : (empty($pInfo->products_id) ? '' : tep_get_products_url($pInfo->products_id, $l['id']))), 'class="form-control" id="pUrl" aria-describedby="pUrlHelp"'); ?>
                      <small id="pUrlHelp" class="form-text text-muted">
                        <?= TEXT_PRODUCTS_URL_WITHOUT_HTTP ?>
                      </small>
                    </div>
                  </div>

                  <div class="form-group row" id="zSeoTitle<?= $l['directory']; ?>">
                    <label for="pSeoTitle" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_SEO_TITLE ?></label>
                    <div class="col-sm-9">
                      <?= tep_draw_input_field('products_seo_title[' . $l['id'] . ']', (empty($pInfo->products_id) ? '' : tep_get_products_seo_title($pInfo->products_id, $l['id'])), 'class="form-control" id="pSeoTitle" aria-describedby="pSeoTitleHelp"'); ?>
                      <small id="pSeoTitleHelp" class="form-text text-muted">
                        <?= TEXT_PRODUCTS_SEO_TITLE_HELP ?>
                      </small>
                    </div>
                  </div>

                  <div class="form-group row" id="zSeoDesc<?= $l['directory']; ?>">
                    <label for="pSeoDesc" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_SEO_DESCRIPTION ?></label>
                    <div class="col-sm-9">
                      <?= tep_draw_textarea_field('products_seo_description[' . $l['id'] . ']', 'soft', '70', '15', (empty($pInfo->products_id) ? '' : tep_get_products_seo_description($pInfo->products_id, $l['id'])), 'class="form-control" id="pSeoDesc"  aria-describedby="pSeoDescHelp"'); ?>
                      <small id="pSeoDescHelp" class="form-text text-muted">
                        <?= TEXT_PRODUCTS_SEO_DESCRIPTION_HELP ?>
                      </small>
                    </div>
                  </div>

                  <div class="form-group row" id="zSeoKeywords<?= $l['directory']; ?>">
                    <label for="pSeoKeywords" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_SEO_KEYWORDS ?></label>
                    <div class="col-sm-9">
                      <?= tep_draw_input_field('products_seo_keywords[' . $l['id'] . ']', (empty($pInfo->products_id) ? '' : tep_get_products_seo_keywords($pInfo->products_id, $l['id'])), 'class="form-control" id="pSeoKeywords" placeholder="' . PLACEHOLDER_COMMA_SEPARATION . '" aria-describedby="pSeoKeywordsHelp"'); ?>
                      <small id="pSeoKeywordsHelp" class="form-text text-muted">
                        <?= TEXT_PRODUCTS_SEO_KEYWORDS_HELP ?>
                      </small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php
            echo $OSCOM_Hooks->call('categories', 'injectLanguageForm');
          }
          ?>
        </div>
      </div>

      <div class="tab-pane fade" id="section_images_content" role="tabpanel">
        <div class="mb-3">
          <div class="form-group row" id="zImg">
            <label for="pImg" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_MAIN_IMAGE ?></label>
            <div class="col-sm-9">
              <div class="custom-file mb-2">
                <?php
                echo tep_draw_input_field('products_image', '', 'id="pImg"', 'file', null, (tep_not_null($pInfo->products_image) ? null : 'required aria-required="true" ') . 'class="custom-file-input"');
                echo '<label class="custom-file-label" for="pImg">' . $pInfo->products_image . '</label>';
                ?>
              </div>
            </div>
          </div>

          <hr>

          <div class="form-group row" id="zPiList">
            <div class="col-sm-3 text-left text-sm-right">
              <?= TEXT_PRODUCTS_OTHER_IMAGES ?>
              <br><a class="btn btn-info btn-sm text-white mt-2" role="button" href="#" id="add_image" onclick="addNewPiForm();return false;"><?= TEXT_PRODUCTS_ADD_LARGE_IMAGE ?></a>
            </div>
            <div class="col-sm-9" id="piList">
              <?php
              $pi_counter = 0;

              foreach ($pInfo->products_larger_images as $pi) {
                $pi_counter++;
                echo '<div class="row mb-2" id="piId' . $pi_counter . '">';
                  echo '<div class="col">';
                    echo '<div class="custom-file mb-2">';
                      echo tep_draw_input_field('products_image_large_' . $pi['id'], '', 'id="pImg' . $pi_counter . '"', 'file', null, 'class="custom-file-input"');
                      echo '<label class="custom-file-label" for="pImg' . $pi_counter . '">' . $pi['image'] . '</label>';
                    echo '</div>';
                  echo '</div>';
                  echo '<div class="col">';
                    echo tep_draw_textarea_field('products_image_htmlcontent_' . $pi['id'], 'soft', '70', '3', $pi['htmlcontent']);
                    echo '<small class="form-text text-muted">' . TEXT_PRODUCTS_LARGE_IMAGE_HTML_CONTENT . '</small>';
                  echo '</div>';
                   echo '<div class="col-1">';
                     echo '<i class="fas fa-arrows-alt-v mr-2"></i>';
                     echo '<a href="#" class="piDel" data-pi-id="' . $pi_counter . '"><i class="fas fa-trash text-danger"></i></a>';
                  echo '</div>';
                echo '</div>';
              }
              ?>
            </div>
          </div>

          <?= $OSCOM_Hooks->call('categories', 'injectImageForm'); ?>

          <script>
          $('#piList').sortable({ containment: 'parent' });

          var piSize = <?= $pi_counter ?>;

          function addNewPiForm() {
            piSize++;

            $('#piList').append('<div class="row mb-2" id="piId' + piSize + '"><div class="col"><div class="custom-file mb-2"><input type="file" class="form-control-input" id="pImg' + piSize + '" name="products_image_large_new_' + piSize + '"><label class="custom-file-input" for="pImg' + piSize + '">&nbsp;</label></div></div><div class="col"><textarea name="products_image_htmlcontent_new_' + piSize + '" wrap="soft" class="form-control" cols="70" rows="3"></textarea><small class="form-text text-muted"><?= TEXT_PRODUCTS_LARGE_IMAGE_HTML_CONTENT ?></small></div><div class="col-1"><i class="fas fa-arrows-alt-v mr-2"></i><a class="piDel" data-pi-id="' + piSize + '"><i class="fas fa-trash text-danger"></i></a></div></div>');
          }

          $('a.piDel').click(function(e){
            var p = $(this).data('pi-id');
            $('#piId' + p).effect('blind').remove();

            e.preventDefault();
          });
          </script>
        </div>
      </div>

      <?= $OSCOM_Hooks->call('categories', 'productTab'); ?>
    </div>
  </div>

  <script>
  updateGross();
  $('#products_date_available').datepicker({ dateFormat: 'yy-mm-dd' });
  </script>

  <?php
  echo tep_draw_hidden_field('products_date_added', (tep_not_null($pInfo->products_date_added) ? $pInfo->products_date_added : date('Y-m-d')));
  echo tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success btn-block btn-lg mt-3 mb-1');
  echo tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('categories.php', 'cPath=' . $cPath . (isset($_GET['pID']) ? '&pID=' . (int)$_GET['pID'] : '')), null, null, 'btn-light');
  ?>

</form>

<?php
  } elseif ($action == 'new_product_preview') {
    $product_query = tep_db_query("select p.*, pd.* from products p, products_description pd where p.products_id = pd.products_id and p.products_id = '" . (int)$_GET['pID'] . "'");
    $product = tep_db_fetch_array($product_query);

    $pInfo = new objectInfo($product);
    $products_image_name = $pInfo->products_image;

    foreach (tep_get_languages() as $l) {
      $pInfo->products_name = tep_get_products_name($pInfo->products_id, $l['id']);
      $pInfo->products_description = tep_get_products_description($pInfo->products_id, $l['id']);
      $pInfo->products_url = tep_get_products_url($pInfo->products_id, $l['id']);
      $pInfo->products_seo_description = tep_get_products_seo_description($pInfo->products_id, $l['id']);
      $pInfo->products_seo_keywords = tep_get_products_seo_keywords($pInfo->products_id, $l['id']);
      $pInfo->products_seo_title = tep_get_products_seo_title($pInfo->products_id, $l['id']);
      ?>

      <div class="row">
        <div class="col">
          <h1 class="display-4 mb-2"><?= tep_image(tep_catalog_href_link('includes/languages/' . $l['directory'] . '/images/' . $l['image']), $l['name']) . '&nbsp;' . $pInfo->products_name ?></h1>
        </div>
        <div class="col text-right align-self-center">
          <h1 class="display-4 mb-2"><?= $currencies->format($pInfo->products_price) ?></h1>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-3 text-left text-sm-right font-weight-bold"><?= TEXT_PRODUCTS_DESCRIPTION ?></div>
        <div class="col-sm-9"><?= $pInfo->products_description ?></div>
      </div>

      <div class="row">
        <div class="col-sm-3 text-left text-sm-right font-weight-bold"><?= TEXT_PRODUCTS_IMAGE ?></div>
        <div class="col-sm-9"><?= tep_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'images/' . $products_image_name) ?></div>
      </div>

      <div class="row">
        <div class="col-sm-3 text-left text-sm-right font-weight-bold"><?= TEXT_PRODUCTS_URL ?></div>
        <div class="col-sm-9"><?= $pInfo->products_url ?>&nbsp;</div>
      </div>

      <div class="row">
        <div class="col-sm-3 text-left text-sm-right font-weight-bold"><?= TEXT_PRODUCT_DATE_ADDED ?></div>
        <div class="col-sm-9"><?= $pInfo->products_date_added ?></div>
      </div>

      <div class="row">
        <div class="col-sm-3 text-left text-sm-right font-weight-bold"><?= TEXT_PRODUCT_DATE_AVAILABLE ?></div>
        <div class="col-sm-9"><?= $pInfo->products_date_available ?>&nbsp;</div>
      </div>
      <?php
    }

    if (isset($_GET['origin'])) {
      $pos_params = strpos($_GET['origin'], '?', 0);
      if ($pos_params != false) {
        $back_url = substr($_GET['origin'], 0, $pos_params);
        $back_url_params = substr($_GET['origin'], $pos_params + 1);
      } else {
        $back_url = $_GET['origin'];
        $back_url_params = '';
      }
    } else {
      $back_url = 'categories.php';
      $back_url_params = 'cPath=' . $cPath . '&pID=' . $pInfo->products_id;
    }

    echo tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link($back_url, $back_url_params), null, null, 'btn-light');

  } else {
?>

  <div class="row">
    <div class="col-md-6">
      <h1 class="display-4 mb-2"><?= HEADING_TITLE ?></h1>
    </div>
    <div class="col-sm-6 col-md-4 text-right align-self-center">
      <?php
      echo tep_draw_form('search', 'categories.php', '', 'get');
        echo '<div class="input-group mb-1">';
          echo '<div class="input-group-prepend">';
            echo '<span class="input-group-text">' . HEADING_TITLE_SEARCH . '</span>';
          echo '</div>';
          echo tep_draw_input_field('search');
        echo '</div>';
        echo tep_hide_session_id();
      echo '</form>';
      echo tep_draw_form('goto', 'categories.php', '', 'get');
        echo '<div class="input-group mb-1">';
          echo '<div class="input-group-prepend">';
            echo '<span class="input-group-text">' . HEADING_TITLE_GOTO . '</span>';
          echo '</div>';
          echo tep_draw_pull_down_menu('cPath', tep_get_category_tree(), $current_category_id, 'onchange="this.form.submit();"');
        echo '</div>';
        echo tep_hide_session_id();
      echo '</form>';
      ?>
    </div>
    <div class="col-sm-6 col-md-2 text-right align-self-center">
      <?php
      if (!isset($_GET['search'])) {
        echo tep_draw_bootstrap_button(IMAGE_NEW_CATEGORY, 'fas fa-sitemap', tep_href_link('categories.php', 'cPath=' . $cPath . '&action=new_category'), null, null, 'btn-danger btn-block mb-1') . tep_draw_bootstrap_button(IMAGE_NEW_PRODUCT, 'fas fa-boxes', tep_href_link('categories.php', 'cPath=' . $cPath . '&action=new_product'), null, null, 'btn-danger btn-block mb-1');
      }
      else {
        echo tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link('categories.php'), null, null, 'btn-light');
      }
      ?>
    </div>
  </div>

  <div class="row no-gutters">
    <div class="col-12 col-sm-8">
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th><?= TABLE_HEADING_CATEGORIES_PRODUCTS ?></th>
              <th class="text-center"><?= TABLE_HEADING_STATUS ?></th>
              <th class="text-right"><?= TABLE_HEADING_ACTION ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $categories_count = 0;
            $rows = 0;
            if (isset($_GET['search'])) {
              $search = tep_db_prepare_input($_GET['search']);

              $categories_query = tep_db_query("select c.*, cd.* from categories c, categories_description cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' and cd.categories_name like '%" . tep_db_input($search) . "%' order by c.sort_order, cd.categories_name");
            } else {
              $categories_query = tep_db_query("select c.*, cd.*  from categories c, categories_description cd where c.parent_id = '" . (int)$current_category_id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' order by c.sort_order, cd.categories_name");
            }
            while ($categories = tep_db_fetch_array($categories_query)) {
              $categories_count++;
              $rows++;

              // Get parent_id for subcategories if search
              if (isset($_GET['search'])) $cPath= $categories['parent_id'];

              if ((!isset($_GET['cID']) && !isset($_GET['pID']) || (isset($_GET['cID']) && ($_GET['cID'] == $categories['categories_id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
                $cInfo = new objectInfo($categories);
              }

              if (isset($cInfo) && is_object($cInfo) && ($categories['categories_id'] == $cInfo->categories_id) ) {
                echo '<tr class="table-active" onclick="document.location.href=\'' . tep_href_link('categories.php', tep_get_path($categories['categories_id'])) . '\'">' . "\n";
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('categories.php', 'cPath=' . $cPath . '&cID=' . $categories['categories_id']) . '\'">' . "\n";
              }
              ?>
                <th><?= $categories['categories_name'] ?></th>
                <td>&nbsp;</td>
                <td class="text-right">
                  <?php
                  echo '<a href="' . tep_href_link('categories.php', tep_get_path($categories['categories_id'])) . '"><i class="fas fa-folder-open mr-2 text-dark"></i></a>';
                  if (isset($cInfo->categories_id) && ($categories['categories_id'] == $cInfo->categories_id) ) { echo '<i class="fas fa-chevron-circle-right text-info"></i>'; } else { echo '<a href="' . tep_href_link('categories.php', 'cPath=' . $cPath . '&cID=' . $categories['categories_id']) . '"><i class="fas fa-info-circle text-muted"></i></a>'; }
                  ?>
                </td>
              </tr>
              <?php
            }

            $products_count = 0;
            if (isset($_GET['search'])) {
              $products_query = tep_db_query("select p.*, pd.*, p2c.categories_id from products p, products_description pd, products_to_categories p2c where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and p.products_id = p2c.products_id and((pd.products_name like '%" . tep_db_input($search) . "%') || (p.products_model like '%" . tep_db_input($search) . "%') ||  (p.products_gtin like '%" . tep_db_input($search) . "%')) order by pd.products_name");
            } else {
              $products_query = tep_db_query("select p.*, pd.* from products p, products_description pd, products_to_categories p2c where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$current_category_id . "' order by pd.products_name");
            }
            while ($products = tep_db_fetch_array($products_query)) {
              $products_count++;
              $rows++;

        // Get categories_id for product if search
              if (isset($_GET['search'])) $cPath = $products['categories_id'];

              if ( !isset($pInfo) && !isset($cInfo) && (!isset($_GET['pID']) && !isset($_GET['cID']) || (isset($_GET['pID']) && ($_GET['pID'] == $products['products_id']))) && (substr($action, 0, 3) != 'new')) {
        // find out the rating average from customer reviews
                $reviews_query = tep_db_query("select (avg(reviews_rating) / 5 * 100) as average_rating from reviews where products_id = '" . (int)$products['products_id'] . "'");
                $reviews = tep_db_fetch_array($reviews_query);
                $pInfo_array = array_merge($products, $reviews);
                $pInfo = new objectInfo($pInfo_array);
              }

              $icons = '<a href="' . tep_href_link('categories.php', 'cPath=' . $cPath . '&pID=' . $products['products_id'] . '&action=new_product_preview&read=only') . '"><i class="fas fa-eye mr-2 text-dark"></i></a>'
                     . '<a href="' . tep_href_link('categories.php', 'cPath=' . $cPath . '&pID=' . $products['products_id'] . '&action=new_product') . '"><i class="fas fa-cogs mr-2 text-dark"></i></a>';
              if (isset($pInfo->products_id) && ($products['products_id'] == $pInfo->products_id) ) {
                echo '<tr class="table-active" onclick="document.location.href=\'' . tep_href_link('categories.php', 'cPath=' . $cPath . '&pID=' . $products['products_id'] . '&action=new_product_preview&read=only') . '\'">';
                $icons .= '<i class="fas fa-chevron-circle-right text-info"></i>';
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('categories.php', 'cPath=' . $cPath . '&pID=' . $products['products_id']) . '\'">';
                $icons .= '<a href="' . tep_href_link('categories.php', 'cPath=' . $cPath . '&pID=' . $products['products_id']) . '"><i class="fas fa-info-circle text-muted"></i></a>';
              }
              ?>
                <th><?= $products['products_name'] ?></th>
                <td class="text-center">
                  <?php
                  if ($products['products_status'] == '1') {
                    echo '<i class="fas fa-check-circle text-success"></i> <a href="' . tep_href_link('categories.php', 'action=setflag&flag=0&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '"><i class="fas fa-times-circle text-muted"></i></a>';
                  } else {
                    echo '<a href="' . tep_href_link('categories.php', 'action=setflag&flag=1&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '"><i class="fas fa-check-circle text-muted"></i></a>  <i class="fas fa-times-circle text-danger"></i>';
                  }
                  ?>
                </td>
                <td class="text-right"><?= $icons ?></td>
              </tr>
              <?php
            }

            $cPath_back = '';
            if (isset($cPath_array) && count($cPath_array) > 0) {
              $cPath_back .= $cPath_array[0];
              for ($i=1, $n=count($cPath_array)-1; $i<$n; $i++) {
                $cPath_back .= '_' . $cPath_array[$i];
              }
            }

            $cPath_back = (tep_not_null($cPath_back)) ? 'cPath=' . $cPath_back . '&' : '';
            ?>
          </tbody>
        </table>
      </div>

      <div class="row my-1">
        <div class="col"><?= TEXT_CATEGORIES . '&nbsp;' . $categories_count . '<br>' . TEXT_PRODUCTS . '&nbsp;' . $products_count ?></div>
        <div class="col text-right mr-2"><?php if (isset($cPath_array) && (count($cPath_array) > 0)) echo tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link('categories.php', $cPath_back), null, null, 'btn-light mr-2'); if (!isset($_GET['search'])) echo tep_draw_bootstrap_button(IMAGE_NEW_CATEGORY, 'fas fa-sitemap', tep_href_link('categories.php', 'cPath=' . $cPath . '&action=new_category'), null, null, 'btn-danger mr-2') . tep_draw_bootstrap_button(IMAGE_NEW_PRODUCT, 'fas fa-boxes', tep_href_link('categories.php', 'cPath=' . $cPath . '&action=new_product'), null, null, 'btn-danger') ?></div>
      </div>

    </div>

<?php
    $heading = [];
    $contents = [];

    switch ($action) {
      case 'new_category':
        $heading[] = ['text' => TEXT_INFO_HEADING_NEW_CATEGORY];

        $contents = ['form' => tep_draw_form('newcategory', 'categories.php', 'action=insert_category&cPath=' . $cPath, 'post', 'enctype="multipart/form-data"')];
        $contents[] = ['text' => TEXT_NEW_CATEGORY_INTRO];

        $category_inputs_string = $category_description_string = $category_seo_description_string = $category_seo_title_string = '';
        foreach (tep_get_languages() as $l) {

          $category_inputs_string .= '<div class="input-group mb-1">';
            $category_inputs_string .= '<div class="input-group-prepend">';
              $category_inputs_string .= '<span class="input-group-text">'. tep_image(tep_catalog_href_link('includes/languages/' . $l['directory'] . '/images/' . $l['image']), $l['name']) . '</span>';
            $category_inputs_string .= '</div>';
            $category_inputs_string .= tep_draw_input_field('categories_name[' . $l['id'] . ']', null, 'required aria-required="true"');
          $category_inputs_string .= '</div>';
          $category_seo_title_string .= '<div class="input-group mb-1">';
            $category_seo_title_string .= '<div class="input-group-prepend">';
              $category_seo_title_string .= '<span class="input-group-text">'. tep_image(tep_catalog_href_link('includes/languages/' . $l['directory'] . '/images/' . $l['image']), $l['name']) . '</span>';
            $category_seo_title_string .= '</div>';
            $category_seo_title_string .= tep_draw_input_field('categories_seo_title[' . $l['id'] . ']');
          $category_seo_title_string .= '</div>';
         $category_description_string .= '<div class="input-group mb-1">';
            $category_description_string .= '<div class="input-group-prepend">';
              $category_description_string .= '<span class="input-group-text">'. tep_image(tep_catalog_href_link('includes/languages/' . $l['directory'] . '/images/' . $l['image']), $l['name']) . '</span>';
            $category_description_string .= '</div>';
            $category_description_string .= tep_draw_textarea_field('categories_description[' . $l['id'] . ']', 'soft', '80', '10');
          $category_description_string .= '</div>';
          $category_seo_description_string .= '<div class="input-group mb-1">';
            $category_seo_description_string .= '<div class="input-group-prepend">';
              $category_seo_description_string .= '<span class="input-group-text">'. tep_image(tep_catalog_href_link('includes/languages/' . $l['directory'] . '/images/' . $l['image']), $l['name']) . '</span>';
            $category_seo_description_string .= '</div>';
            $category_seo_description_string .= tep_draw_textarea_field('categories_seo_description[' . $l['id'] . ']', 'soft', '80', '10');
          $category_seo_description_string .= '</div>';
        }

        $contents[] = ['text' => TEXT_CATEGORIES_NAME . $category_inputs_string];
        $contents[] = ['text' => TEXT_CATEGORIES_SEO_TITLE . $category_seo_title_string];
        $contents[] = ['text' => TEXT_CATEGORIES_DESCRIPTION . $category_description_string];
        $contents[] = ['text' => TEXT_CATEGORIES_SEO_DESCRIPTION . $category_seo_description_string];
        $contents[] = ['text' => TEXT_EDIT_CATEGORIES_IMAGE . '<div class="custom-file mb-2">' . tep_draw_input_field('categories_image', '', 'id="cImg"', 'file', null, 'class="custom-file-input"') . '<label class="custom-file-label" for="cImg">&nbsp;</label></div>'];
        $contents[] = ['text' => TEXT_SORT_ORDER . '<br>' . tep_draw_input_field('sort_order', '', 'size="2"')];
        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success btn-block btn-lg mb-1') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times',  tep_href_link('categories.php', 'cPath=' . $cPath), null, null, 'btn-light')];
        break;
      case 'edit_category':
        $heading[] = ['text' => TEXT_INFO_HEADING_EDIT_CATEGORY];

        $contents = ['form' => tep_draw_form('categories', 'categories.php', 'action=update_category&cPath=' . $cPath, 'post', 'enctype="multipart/form-data"') . tep_draw_hidden_field('categories_id', $cInfo->categories_id)];
        $contents[] = ['text' => TEXT_EDIT_INTRO];

        $category_inputs_string = $category_description_string = $category_seo_description_string = $category_seo_title_string = '';
        foreach (tep_get_languages() as $l) {
          $category_inputs_string .= '<div class="input-group mb-1">';
            $category_inputs_string .= '<div class="input-group-prepend">';
              $category_inputs_string .= '<span class="input-group-text">'. tep_image(tep_catalog_href_link('includes/languages/' . $l['directory'] . '/images/' . $l['image']), $l['name']) . '</span>';
            $category_inputs_string .= '</div>';
            $category_inputs_string .= tep_draw_input_field('categories_name[' . $l['id'] . ']', tep_get_category_name($cInfo->categories_id, $l['id']), 'required aria-required="true"');
          $category_inputs_string .= '</div>';
          $category_seo_title_string .= '<div class="input-group mb-1">';
            $category_seo_title_string .= '<div class="input-group-prepend">';
              $category_seo_title_string .= '<span class="input-group-text">'. tep_image(tep_catalog_href_link('includes/languages/' . $l['directory'] . '/images/' . $l['image']), $l['name']) . '</span>';
            $category_seo_title_string .= '</div>';
            $category_seo_title_string .= tep_draw_input_field('categories_seo_title[' . $l['id'] . ']', tep_get_category_seo_title($cInfo->categories_id, $l['id']));
          $category_seo_title_string .= '</div>';
         $category_description_string .= '<div class="input-group mb-1">';
            $category_description_string .= '<div class="input-group-prepend">';
              $category_description_string .= '<span class="input-group-text">'. tep_image(tep_catalog_href_link('includes/languages/' . $l['directory'] . '/images/' . $l['image']), $l['name']) . '</span>';
            $category_description_string .= '</div>';
            $category_description_string .= tep_draw_textarea_field('categories_description[' . $l['id'] . ']', 'soft', '80', '10', tep_get_category_description($cInfo->categories_id, $l['id']));
          $category_description_string .= '</div>';
          $category_seo_description_string .= '<div class="input-group mb-1">';
            $category_seo_description_string .= '<div class="input-group-prepend">';
              $category_seo_description_string .= '<span class="input-group-text">'. tep_image(tep_catalog_href_link('includes/languages/' . $l['directory'] . '/images/' . $l['image']), $l['name']) . '</span>';
            $category_seo_description_string .= '</div>';
            $category_seo_description_string .= tep_draw_textarea_field('categories_seo_description[' . $l['id'] . ']', 'soft', '80', '10', tep_get_category_seo_description($cInfo->categories_id, $l['id']));
          $category_seo_description_string .= '</div>';
        }

        $contents[] = ['text' => TEXT_EDIT_CATEGORIES_NAME . $category_inputs_string];
        $contents[] = ['text' => TEXT_EDIT_CATEGORIES_SEO_TITLE . $category_seo_title_string];
        $contents[] = ['text' => TEXT_EDIT_CATEGORIES_DESCRIPTION . $category_description_string];
        $contents[] = ['text' => TEXT_EDIT_CATEGORIES_SEO_DESCRIPTION . $category_seo_description_string];
        $contents[] = ['text' => TEXT_EDIT_CATEGORIES_IMAGE . tep_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'images/' . $cInfo->categories_image, $cInfo->categories_name)];
        $contents[] = ['text' => '<div class="custom-file mb-2">' . tep_draw_input_field('categories_image', '', 'id="cImg"', 'file', null, 'class="custom-file-input"') . '<label class="custom-file-label" for="cImg">' .  $cInfo->categories_image . '</label></div>'];
        $contents[] = ['text' => TEXT_EDIT_SORT_ORDER . '<br>' . tep_draw_input_field('sort_order', $cInfo->sort_order, 'size="2"')];
        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success btn-block btn-lg mb-1') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times',  tep_href_link('categories.php', 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id), null, null, 'btn-light')];
        break;
      case 'delete_category':
        $subcategory_products_check = tep_db_fetch_array(tep_db_query("SELECT COUNT(*) AS total FROM (SELECT categories_id AS id FROM categories WHERE parent_id = '" . (int)$_GET['cID'] . "' UNION SELECT p2c.products_id AS id FROM products_to_categories p2c LEFT JOIN products_to_categories self ON p2c.products_id = self.products_id AND p2c.categories_id != self.categories_id WHERE p2c.categories_id = '" . (int)$_GET['cID'] . "' AND self.categories_id IS NULL ) combined"));

        $heading[] = ['text' => TEXT_INFO_HEADING_DELETE_CATEGORY];

        $contents = ['form' => tep_draw_form('categories', 'categories.php', 'action=delete_category_confirm&cPath=' . $cPath) . tep_draw_hidden_field('categories_id', $cInfo->categories_id)];
        $contents[] = ['text' => TEXT_DELETE_CATEGORY_INTRO];
        $contents[] = ['text' => '<strong>' . $cInfo->categories_name . '</strong>'];
        if ($subcategory_products_check['total'] > 0) $contents[] = ['text' => TEXT_DELETE_WARNING];
        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', null, 'primary', null, 'btn-danger btn-block btn-lg mb-1') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times',  tep_href_link('categories.php', 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id), null, null, 'btn-light')];
        break;
      case 'move_category':
        $heading[] = ['text' => TEXT_INFO_HEADING_MOVE_CATEGORY];

        $contents = ['form' => tep_draw_form('categories', 'categories.php', 'action=move_category_confirm&cPath=' . $cPath) . tep_draw_hidden_field('categories_id', $cInfo->categories_id)];
        $contents[] = ['text' => sprintf(TEXT_MOVE_CATEGORIES_INTRO, $cInfo->categories_name)];
        $contents[] = ['text' => sprintf(TEXT_MOVE, $cInfo->categories_name) . '<br>' . tep_draw_pull_down_menu('move_to_category_id', tep_get_category_tree(), $current_category_id)];
        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_MOVE, 'fas fa-arrows-alt', null, null, null, 'btn-success btn-block btn-lg mb-1') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times',  tep_href_link('categories.php', 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id), null, null, 'btn-light')];
        break;
      case 'delete_product':
        $heading[] = ['text' => TEXT_INFO_HEADING_DELETE_PRODUCT];

        $contents = ['form' => tep_draw_form('products', 'categories.php', 'action=delete_product_confirm&cPath=' . $cPath) . tep_draw_hidden_field('products_id', $pInfo->products_id)];
        $contents[] = ['text' => TEXT_DELETE_PRODUCT_INTRO];
        $contents[] = ['class' => 'text-center text-uppercase font-weight-bold', 'text' => $pInfo->products_name];

        $product_categories_string = '';
        $product_categories = tep_generate_category_path($pInfo->products_id, 'product');
        for ($i = 0, $n = count($product_categories); $i < $n; $i++) {
          $category_path = '';
          for ($j = 0, $k = count($product_categories[$i]); $j < $k; $j++) {
            $category_path .= $product_categories[$i][$j]['text'] . '&nbsp;&gt;&nbsp;';
          }
          $category_path = substr($category_path, 0, -16);

          $product_categories_string .= '<div class="custom-control custom-switch">';
            $product_categories_string .= tep_draw_selection_field('product_categories[]', 'checkbox', $product_categories[$i][count($product_categories[$i])-1]['id'], true, 'class="custom-control-input" id="dProduct_' . $i . '"');
            $product_categories_string .= '<label for="dProduct_' . $i . '" class="custom-control-label text-muted"><small>' . $category_path . '</small></label>';
          $product_categories_string .= '</div>';
        }

        $contents[] = ['text' => $product_categories_string];
        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', null, 'primary', null, 'btn-danger btn-block btn-lg mb-1') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times',  tep_href_link('categories.php', 'cPath=' . $cPath . '&pID=' . $pInfo->products_id), null, null, 'btn-light')];
        break;
      case 'move_product':
        $heading[] = ['text' => TEXT_INFO_HEADING_MOVE_PRODUCT];

        $contents = ['form' => tep_draw_form('products', 'categories.php', 'action=move_product_confirm&cPath=' . $cPath) . tep_draw_hidden_field('products_id', $pInfo->products_id)];
        $contents[] = ['text' => sprintf(TEXT_MOVE_PRODUCTS_INTRO, $pInfo->products_name)];
        $contents[] = ['text' => TEXT_INFO_CURRENT_CATEGORIES . '<br><i>' . tep_output_generated_category_path($pInfo->products_id, 'product') . '</i>'];
        $contents[] = ['text' => sprintf(TEXT_MOVE, $pInfo->products_name) . '<br>' . tep_draw_pull_down_menu('move_to_category_id', tep_get_category_tree(), $current_category_id)];
        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_MOVE, 'fas fa-arrows-alt', null, null, null, 'btn-success btn-block btn-lg mb-1') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('categories.php', 'cPath=' . $cPath . '&pID=' . $pInfo->products_id), null, null, 'btn-light')];
        break;
      case 'copy_to':
        $heading[] = ['text' => TEXT_INFO_HEADING_COPY_TO];

        $contents = ['form' => tep_draw_form('copy_to', 'categories.php', 'action=copy_to_confirm&cPath=' . $cPath) . tep_draw_hidden_field('products_id', $pInfo->products_id)];
        $contents[] = ['text' => TEXT_INFO_COPY_TO_INTRO];
        $contents[] = ['text' => TEXT_INFO_CURRENT_CATEGORIES . '<br><i>' . tep_output_generated_category_path($pInfo->products_id, 'product') . '</i>'];
        $contents[] = ['text' => TEXT_CATEGORIES . '<br>' . tep_draw_pull_down_menu('categories_id', tep_get_category_tree(), $current_category_id)];
        $contents[] = ['text' => TEXT_HOW_TO_COPY . '<br><div class="custom-control custom-radio custom-control-inline">' . tep_draw_selection_field('copy_as', 'radio', 'link', true, 'id="cLink" class="custom-control-input"') . '<label class="custom-control-label" for="cLink">' . TEXT_COPY_AS_LINK . '</label></div><br><div class="custom-control custom-radio custom-control-inline">' . tep_draw_selection_field('copy_as', 'radio', 'duplicate', null, 'id="dLink" class="custom-control-input"') . '<label class="custom-control-label" for="dLink">' . TEXT_COPY_AS_DUPLICATE . '</label></div>'];
        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_COPY, 'fas fa-copy', null, null, null, 'btn-success btn-block btn-lg mb-1') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('categories.php', 'cPath=' . $cPath . '&pID=' . $pInfo->products_id), null, null, 'btn-light')];
        break;
      default:
        if ($rows > 0) {
          if (isset($cInfo) && is_object($cInfo)) { // category info box contents
            $category_path_string = '';
            $category_path = tep_generate_category_path($cInfo->categories_id);
            for ($i=(count($category_path[0])-1); $i>0; $i--) {
              $category_path_string .= $category_path[0][$i]['id'] . '_';
            }
            $category_path_string = substr($category_path_string, 0, -1);

            $heading[] = ['text' => $cInfo->categories_name];

            $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_EDIT, 'fas fa-cogs', tep_href_link('categories.php', 'cPath=' . $category_path_string . '&cID=' . $cInfo->categories_id . '&action=edit_category'), null, null, 'btn-warning mr-2') . tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', tep_href_link('categories.php', 'cPath=' . $category_path_string . '&cID=' . $cInfo->categories_id . '&action=delete_category'), null, null, 'btn-danger mr-2')];
            $contents[] = ['text' => TEXT_DATE_ADDED . ' ' . tep_date_short($cInfo->date_added)];
            if (tep_not_null($cInfo->last_modified)) $contents[] = ['text' => TEXT_LAST_MODIFIED . ' ' . tep_date_short($cInfo->last_modified)];
            $contents[] = ['text' => tep_info_image($cInfo->categories_image, $cInfo->categories_name, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT) . '<br>' . $cInfo->categories_image];

            $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_MOVE, 'fas fa-arrows-alt', tep_href_link('categories.php', 'cPath=' . $category_path_string . '&cID=' . $cInfo->categories_id . '&action=move_category'), null, null, 'btn-light')];

          } elseif (isset($pInfo) && is_object($pInfo)) { // product info box contents
            $heading[] = ['text' => tep_get_products_name($pInfo->products_id, $languages_id)];

            $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_EDIT, 'fas fa-cogs', tep_href_link('categories.php', 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=new_product'), null, null, 'btn-warning mr-2') . tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', tep_href_link('categories.php', 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=delete_product'), null, null, 'btn-danger mr-2')];
            $contents[] = ['text' => TEXT_DATE_ADDED . ' ' . tep_date_short($pInfo->products_date_added)];
            if (tep_not_null($pInfo->products_last_modified)) $contents[] = ['text' => TEXT_LAST_MODIFIED . ' ' . tep_date_short($pInfo->products_last_modified)];
            if (date('Y-m-d') < $pInfo->products_date_available) $contents[] = ['text' => TEXT_DATE_AVAILABLE . ' ' . tep_date_short($pInfo->products_date_available)];
            $contents[] = ['text' => tep_info_image($pInfo->products_image, $pInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '<br>' . $pInfo->products_image];
            $contents[] = ['text' => TEXT_PRODUCTS_PRICE_INFO . ' ' . $currencies->format($pInfo->products_price) . '<br>' . TEXT_PRODUCTS_QUANTITY_INFO . ' ' . $pInfo->products_quantity];
            $contents[] = ['text' => TEXT_PRODUCTS_AVERAGE_RATING . ' ' . number_format($pInfo->average_rating, 2) . '%'];
            $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_MOVE, 'fas fa-arrows-alt', tep_href_link('categories.php', 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=move_product'), null, null, 'btn-light mr-2') . tep_draw_bootstrap_button(IMAGE_COPY_TO, 'fas fa-copy', tep_href_link('categories.php', 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=copy_to'), null, null, 'btn-light')];
          }
        } else { // create category/product info
          $heading[] = ['text' => EMPTY_CATEGORY];

          $contents[] = ['text' => TEXT_NO_CHILD_CATEGORIES_OR_PRODUCTS];
        }
        break;
    }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '<div class="col-12 col-sm-4">';
      $box = new box();
      echo $box->infoBox($heading, $contents);
    echo '</div>';
  }
?>

</div>

<?php
  }
?>

<script>
$(document).on('change', '#cImg, [id^=pImg]', function (event) { $(this).next('.custom-file-label').html(event.target.files[0].name); });
</script>




<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
