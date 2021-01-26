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
  if (empty($_GET['cPath'])) {
    $current_category_id = 0;
    $cPath = '';
  } else {
    $cPath_array = tep_parse_category_path($_GET['cPath']);
    $cPath = implode('_', $cPath_array);
    $current_category_id = end($cPath_array);
  }

  const DIR_FS_CATALOG_IMAGES = DIR_FS_CATALOG . 'images/';

  $action = $_GET['action'] ?? '';

  $OSCOM_Hooks->call('categories', 'preAction');

  if (!Text::is_empty($action)) {
    switch ($action) {
      case 'setflag':
        tep_db_query("UPDATE products SET products_status = " . (int)$_GET['flag'] . ", products_last_modified = NOW() WHERE products_id = " . (int)$_GET['pID']);

        $OSCOM_Hooks->call('categories', 'setFlagAction');

        tep_redirect(tep_href_link('categories.php', 'cPath=' . $_GET['cPath'] . '&pID=' . (int)$_GET['pID']));
        break;
      case 'insert_category':
      case 'update_category':
        if (isset($_POST['categories_id'])) {
          $categories_id = Text::input($_POST['categories_id']);
        }
        $sort_order = Text::input($_POST['sort_order']);

        $sql_data = ['sort_order' => (int)$sort_order];

        if ($action == 'insert_category') {
          $insert_sql_data = [
            'parent_id' => $current_category_id,
            'date_added' => 'NOW()',
          ];

          $sql_data = array_merge($sql_data, $insert_sql_data);

          tep_db_perform('categories', $sql_data);

          $categories_id = tep_db_insert_id();
        } elseif ($action == 'update_category') {
          $update_sql_data = ['last_modified' => 'NOW()'];

          $sql_data = array_merge($sql_data, $update_sql_data);

          tep_db_perform('categories', $sql_data, 'update', "categories_id = " . (int)$categories_id);
        }

        foreach (tep_get_languages() as $l) {
          $sql_data = [
            'categories_name' => Text::prepare($_POST['categories_name'][$l['id']]),
            'categories_description' => Text::prepare($_POST['categories_description'][$l['id']]),
            'categories_seo_description' => Text::prepare($_POST['categories_seo_description'][$l['id']]),
            'categories_seo_title' => Text::prepare($_POST['categories_seo_title'][$l['id']]),
          ];

          if ($action == 'insert_category') {
            $insert_sql_data = ['categories_id' => $categories_id, 'language_id' => $l['id']];

            $sql_data = array_merge($sql_data, $insert_sql_data);

            $OSCOM_Hooks->call('categories', 'insertCategoryAction');

            tep_db_perform('categories_description', $sql_data);
          } elseif ($action == 'update_category') {
            $OSCOM_Hooks->call('categories', 'updateCategoryAction');

            tep_db_perform('categories_description', $sql_data, 'update', "categories_id = " . (int)$categories_id . " AND language_id = " . (int)$l['id']);
          }
        }

        $categories_image = new upload('categories_image');
        $categories_image->set_destination(DIR_FS_CATALOG_IMAGES);

        if ($categories_image->parse() && $categories_image->save()) {
          tep_db_query("UPDATE categories SET categories_image = '" . tep_db_input($categories_image->filename) . "' WHERE categories_id = " . (int)$categories_id);
        }

        $OSCOM_Hooks->call('categories', 'insertCategoryUpdateCategoryAction');

        tep_redirect(tep_href_link('categories.php', 'cPath=' . $cPath . '&cID=' . $categories_id));
        break;
      case 'delete_category_confirm':
        if (isset($_POST['categories_id'])) {
          $categories_id = Text::input($_POST['categories_id']);

          $category_tree = new category_tree();
          $descendants = array_reverse($category_tree->get_descendants($categories_id));
          $descendants[] = $categories_id;

          $product_ids_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT c1.products_id
 FROM products_to_categories c1 LEFT JOIN products_to_categories c2
   ON c1.products_id = c2.products_id AND c1.categories_id != c2.categories_id
 WHERE c1.categories_id IN (%s) AND c2.categories_id IS NULL
EOSQL
            , implode(', ', array_map('intval', $descendants))));

          $products_delete = [];
          while ($product_ids = $product_ids_query->fetch_assoc()) {
            $products_delete[] = $product_ids['products_id'];
          }

// removing categories can be a lengthy process
          tep_set_time_limit(0);
          array_filter($products_delete, 'tep_remove_product');
          array_filter($descendants, 'tep_remove_category');
        }

        $OSCOM_Hooks->call('categories', 'deleteCategoryConfirmAction');

        tep_redirect(tep_href_link('categories.php', 'cPath=' . $cPath));
        break;
      case 'delete_product_confirm':
        if (isset($_POST['products_id'], $_POST['product_categories']) && is_array($_POST['product_categories'])) {
          $product_id = Text::input($_POST['products_id']);
          $product_categories = implode(', ', array_map('intval', $_POST['product_categories']));

          tep_db_query("DELETE FROM products_to_categories WHERE products_id = " . (int)$product_id . " AND categories_id IN (" . $product_categories . ")");

          $product_categories_query = tep_db_query("SELECT COUNT(*) AS total FROM products_to_categories WHERE products_id = " . (int)$product_id);
          $product_categories = $product_categories_query->fetch_assoc();

          if ($product_categories['total'] == '0') {
            tep_remove_product($product_id);
          }
        }

        $OSCOM_Hooks->call('categories', 'deleteProductConfirmAction');

        tep_redirect(tep_href_link('categories.php', 'cPath=' . $cPath));
        break;
      case 'move_category_confirm':
        if (isset($_POST['categories_id']) && ($_POST['categories_id'] != $_POST['move_to_category_id'])) {
          $categories_id = Text::input($_POST['categories_id']);
          $new_parent_id = Text::input($_POST['move_to_category_id']);

          $path = explode('_', tep_get_generated_category_path_ids($new_parent_id));

          if (in_array($categories_id, $path)) {
            $messageStack->add_session(ERROR_CANNOT_MOVE_CATEGORY_TO_PARENT, 'error');

            tep_redirect(tep_href_link('categories.php', 'cPath=' . $cPath . '&cID=' . $categories_id));
          } else {
            tep_db_query("UPDATE categories SET parent_id = " . (int)$new_parent_id . ", last_modified = NOW() WHERE categories_id = " . (int)$categories_id);

            $OSCOM_Hooks->call('categories', 'moveCategoryConfirmAction');

            tep_redirect(tep_href_link('categories.php', 'cPath=' . $new_parent_id . '&cID=' . $categories_id));
          }
        }

        break;
      case 'move_product_confirm':
        $products_id = Text::input($_POST['products_id']);
        $new_parent_id = Text::input($_POST['move_to_category_id']);

        $duplicate_check_query = tep_db_query("SELECT COUNT(*) AS total FROM products_to_categories WHERE products_id = " . (int)$products_id . " AND categories_id = " . (int)$new_parent_id);
        $duplicate_check = $duplicate_check_query->fetch_assoc();
        if ($duplicate_check['total'] < 1) {
          tep_db_query("UPDATE products_to_categories SET categories_id = " . (int)$new_parent_id . " WHERE products_id = " . (int)$products_id . " AND categories_id = " . (int)$current_category_id);
        }

        $OSCOM_Hooks->call('categories', 'moveProductConfirmAction');

        tep_redirect(tep_href_link('categories.php', 'cPath=' . $new_parent_id . '&pID=' . $products_id));
        break;
      case 'insert_product':
      case 'update_product':
        if (isset($_GET['pID'])) {
          $products_id = Text::input($_GET['pID']);
        }
        $products_date_available = Text::input($_POST['products_date_available']);

        $sql_data = [
          'products_quantity' => (int)Text::input($_POST['products_quantity']),
          'products_model' => Text::prepare($_POST['products_model']),
          'products_price' => Text::input($_POST['products_price']),
          'products_date_available' => (date('Y-m-d') < $products_date_available) ? $products_date_available : 'NULL',
          'products_weight' => (float)Text::input($_POST['products_weight']),
          'products_status' => Text::input($_POST['products_status']),
          'products_tax_class_id' => Text::input($_POST['products_tax_class_id']),
          'manufacturers_id' => (int)Text::input($_POST['manufacturers_id']),
          'products_gtin' => (Text::is_empty($_POST['products_gtin'])) ? 'NULL' : str_pad(Text::prepare($_POST['products_gtin']), 14, '0', STR_PAD_LEFT),
        ];

        $products_image = new upload('products_image');
        $products_image->set_destination(DIR_FS_CATALOG_IMAGES);
        if ($products_image->parse() && $products_image->save()) {
          $sql_data['products_image'] = Text::prepare($products_image->filename);
        }

        if ($action == 'insert_product') {
          $insert_sql_data = ['products_date_added' => 'NOW()'];

          $sql_data = array_merge($sql_data, $insert_sql_data);

          tep_db_perform('products', $sql_data);
          $products_id = tep_db_insert_id();

          tep_db_query("INSERT INTO products_to_categories (products_id, categories_id) VALUES (" . (int)$products_id . ", " . (int)$current_category_id . ")");
        } elseif ($action == 'update_product') {
          $update_sql_data = ['products_last_modified' => 'NOW()'];

          $sql_data = array_merge($sql_data, $update_sql_data);

          tep_db_perform('products', $sql_data, 'update', "products_id = " . (int)$products_id);
        }

        foreach (tep_get_languages() as $l) {
          $language_id = $l['id'];

          $sql_data = [
            'products_name' => Text::prepare($_POST['products_name'][$language_id]),
            'products_description' => Text::prepare($_POST['products_description'][$language_id]),
            'products_url' => Text::prepare($_POST['products_url'][$language_id]),
            'products_seo_description' => Text::prepare($_POST['products_seo_description'][$language_id]),
            'products_seo_keywords' => Text::prepare($_POST['products_seo_keywords'][$language_id]),
            'products_seo_title' => Text::prepare($_POST['products_seo_title'][$language_id]),
          ];

          if ($action == 'insert_product') {
            $insert_sql_data = ['products_id' => $products_id, 'language_id' => $language_id];

            $sql_data = array_merge($sql_data, $insert_sql_data);

            tep_db_perform('products_description', $sql_data);
          } elseif ($action == 'update_product') {
            tep_db_perform('products_description', $sql_data, 'update', "products_id = " . (int)$products_id . " AND language_id = " . (int)$language_id);
          }
        }

        $pi_sort_order = 0;
        $piArray = [0];

        foreach ($_FILES as $key => $value) {
// Update existing large product images
          if (preg_match('{\Aproducts_image_large_([0-9]+)\z}', $key, $matches)) {
            $pi_sort_order++;

            $sql_data = ['htmlcontent' => Text::prepare($_POST['products_image_htmlcontent_' . $matches[1]]), 'sort_order' => $pi_sort_order];

            $t = new upload($key);
            $t->set_destination(DIR_FS_CATALOG_IMAGES);
            if ($t->parse() && $t->save()) {
              $sql_data['image'] = Text::prepare($t->filename);
            }

            tep_db_perform('products_images', $sql_data, 'update', "products_id = " . (int)$products_id . " AND id = " . (int)$matches[1]);

            $piArray[] = (int)$matches[1];
          } elseif (preg_match('{\Aproducts_image_large_new_([0-9]+)\z}', $key, $matches)) {
// Insert new large product images
            $sql_data = ['products_id' => (int)$products_id, 'htmlcontent' => Text::prepare($_POST['products_image_htmlcontent_new_' . $matches[1]])];

            $t = new upload($key);
            $t->set_destination(DIR_FS_CATALOG_IMAGES);
            if ($t->parse() && $t->save()) {
              $pi_sort_order++;

              $sql_data['image'] = Text::prepare($t->filename);
              $sql_data['sort_order'] = $pi_sort_order;

              tep_db_perform('products_images', $sql_data);

              $piArray[] = tep_db_insert_id();
            }
          }
        }

        $product_images_query = tep_db_query("SELECT image FROM products_images WHERE products_id = " . (int)$products_id . " AND id NOT IN (" . implode(', ', $piArray) . ")");
        if (mysqli_num_rows($product_images_query)) {
          while ($product_images = $product_images_query->fetch_assoc()) {
            $duplicate_image_query = tep_db_query("SELECT COUNT(*) AS total FROM products_images WHERE image = '" . tep_db_input($product_images['image']) . "'");
            $duplicate_image = $duplicate_image_query->fetch_assoc();

            if ($duplicate_image['total'] < 2) {
              if (file_exists(DIR_FS_CATALOG_IMAGES . $product_images['image'])) {
                @unlink(DIR_FS_CATALOG_IMAGES . $product_images['image']);
              }
            }
          }

          tep_db_query("DELETE FROM products_images WHERE products_id = " . (int)$products_id . " AND id NOT IN (" . implode(', ', $piArray) . ")");
        }

        $OSCOM_Hooks->call('categories', 'productActionSave');

        tep_redirect(tep_href_link('categories.php', 'cPath=' . $cPath . '&pID=' . $products_id));
        break;
      case 'copy_to_confirm':
        if (isset($_POST['products_id'], $_POST['categories_id'])) {
          $products_id = Text::input($_POST['products_id']);
          $categories_id = Text::input($_POST['categories_id']);

          if ($_POST['copy_as'] == 'link') {
            if ($categories_id == $current_category_id) {
              $messageStack->add_session(ERROR_CANNOT_LINK_TO_SAME_CATEGORY, 'error');
            } else {
              $check_query = tep_db_query("SELECT COUNT(*) AS total FROM products_to_categories WHERE products_id = " . (int)$products_id . " AND categories_id = " . (int)$categories_id);
              $check = $check_query->fetch_assoc();
              if ($check['total'] < 1) {
                tep_db_query("INSERT INTO products_to_categories (products_id, categories_id) VALUES (" . (int)$products_id . ", " . (int)$categories_id . ")");
              }
            }
          } elseif ($_POST['copy_as'] == 'duplicate') {
            $db_columns = [
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

            $parameters = ['db' => &$db_columns];
            $OSCOM_Hooks->call('categories', 'preDuplicateCopyToConfirmAction', $parameters);
            $products_id = tep_db_copy($db_columns, 'products_id', (int)$products_id);
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
    if (isset($_GET['pID']) && empty($_POST)) {
      $product = product_by_id::administer($_GET['pID']);
      $translations = $product->get('translations');
    } else {
      $product = new Product([
        'products_name' => '',
        'products_description' => '',
        'products_url' => '',
        'products_id' => '',
        'products_quantity' => '',
        'products_model' => '',
        'products_image' => '',
        'products_price' => '',
        'products_weight' => '',
        'products_date_added' => '',
        'products_last_modified' => '',
        'products_date_available' => '',
        'products_status' => '',
        'products_tax_class_id' => '',
        'manufacturers_id' => '',
        'products_gtin' => '',
        'products_seo_description' => '',
        'products_seo_keywords' => '',
        'products_seo_title' => '',
      ]);
    }

    $manufacturers_array = [['id' => '', 'text' => TEXT_NONE]];
    $manufacturers_query = tep_db_query("SELECT manufacturers_id, manufacturers_name FROM manufacturers ORDER BY manufacturers_name");
    while ($manufacturers = $manufacturers_query->fetch_assoc()) {
      $manufacturers_array[] = ['id' => $manufacturers['manufacturers_id'], 'text' => $manufacturers['manufacturers_name']];
    }

    $tax_class_array = [['id' => '0', 'text' => TEXT_NONE]];
    $tax_class_query = tep_db_query("SELECT tax_class_id, tax_class_title FROM tax_class ORDER BY tax_class_title");
    while ($tax_class = $tax_class_query->fetch_assoc()) {
      $tax_class_array[] = ['id' => $tax_class['tax_class_id'], 'text' => $tax_class['tax_class_title']];
    }

    $out_status = ('0' === $product->get('status'));
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
      <h1 class="display-4 mb-2"><?= (isset($_GET['pID'])) ? sprintf(TEXT_EXISTING_PRODUCT, $product->get('name'), tep_output_generated_category_path($current_category_id)) : sprintf(TEXT_NEW_PRODUCT, tep_output_generated_category_path($current_category_id)) ?></h1>
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
            <?= tep_draw_input_field('products_quantity', $product->get('in_stock'), 'required aria-required="true" id="pQty" class="form-control w-25"') ?>
          </div>
        </div>

        <div class="form-group row" id="zDate">
          <label for="products_date_available" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_DATE_AVAILABLE ?></label>
          <div class="col-sm-9">
            <?= tep_draw_input_field('products_date_available', $product->get('date_available'), 'class="form-control w-25" id="products_date_available" aria-describedby="pDateHelp"');?>
            <small id="pDateHelp" class="form-text text-muted">
              <?= TEXT_PRODUCTS_DATE_AVAILABLE_HELP ?>
            </small>
          </div>
        </div>

        <hr>

        <div class="form-group row" id="zBrand">
          <label for="pBrand" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_MANUFACTURER ?></label>
          <div class="col-sm-9">
            <?= tep_draw_pull_down_menu('manufacturers_id', $manufacturers_array, $product->get('manufacturers_id'), 'id="pBrand"') ?>
          </div>
        </div>

        <div class="form-group row" id="zModel">
          <label for="pModel" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_MODEL ?></label>
          <div class="col-sm-9">
            <?= tep_draw_input_field('products_model', $product->get('model'), 'id="pModel"') ?>
          </div>
        </div>

        <hr>

        <div class="form-group row" id="zTax">
          <label for="pTax" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_TAX_CLASS ?></label>
          <div class="col-sm-9">
            <?= tep_draw_pull_down_menu('products_tax_class_id', $tax_class_array, $product->get('tax_class_id'), 'id="pTax" onchange="updateGross()"') ?>
          </div>
        </div>

        <div class="form-group row" id="zNet">
          <label for="pNet" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_PRICE_NET ?></label>
          <div class="col-sm-9">
            <?= tep_draw_input_field('products_price', $product->get('price'), 'required aria-required="true" id="pNet" class="form-control w-25" onchange="updateGross()"') ?>
          </div>
        </div>
        <div class="form-group row" id="zGross">
          <label for="pGross" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_PRICE_GROSS ?></label>
          <div class="col-sm-9">
            <?= tep_draw_input_field('products_price_gross', $product->get('price'), 'id="pGross" class="form-control w-25" onchange="updateNet()"') ?>
          </div>
        </div>

        <hr>

        <div class="form-group row" id="zWeight">
          <label for="pWeight" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_WEIGHT ?></label>
          <div class="col-sm-9">
            <?= tep_draw_input_field('products_weight', $product->get('weight'), 'id="pWeight" class="form-control w-25"') ?>
          </div>
        </div>

        <div class="form-group row" id="zGtin">
          <label for="pGtin" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_GTIN ?></label>
          <div class="col-sm-9">
            <?= tep_draw_input_field('products_gtin', $product->get('gtin'), 'id="pGtin" class="form-control w-25" aria-describedby="pGtinHelp"') ?>
            <small id="pGtinHelp" class="form-text text-muted">
            <?= TEXT_PRODUCTS_GTIN_HELP ?>
            </small>
          </div>
        </div>

        <?= $OSCOM_Hooks->call('categories', 'injectDataForm') ?>

      </div>

      <div class="tab-pane fade" id="section_general_content" role="tabpanel">
        <div class="accordion" id="productLanguageAccordion">
          <?php
          $show = ' show';
          foreach (tep_get_languages() as $l) {
            ?>
            <div class="card">
              <div class="card-header" id="heading<?= $l['directory'] ?>">
                <button class="btn btn-info" type="button" data-toggle="collapse" data-target="#<?= $l['directory'] ?>" aria-expanded="true" aria-controls="<?= $l['directory'] ?>"><?= tep_image(tep_catalog_href_link('includes/languages/' . $l['directory'] . '/images/' . $l['image']), $l['name'], null, null, null, false, 'lng mr-2') . $l['name'] ?></button>
              </div>
              <div id="<?= $l['directory'] ?>" class="collapse<?= $show ?>" aria-labelledby="heading<?= $l['directory'] ?>" data-parent="#productLanguageAccordion">
                <div class="card-body">
                  <div class="form-group row" id="zName<?= $l['directory'] ?>">
                    <label for="pName" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_NAME ?></label>
                    <div class="col-sm-9">
                      <?= tep_draw_input_field('products_name[' . $l['id'] . ']', $translations[$l['id']]['name'] ?? '', 'required aria-required="true" class="form-control" id="pName"') ?>
                    </div>
                  </div>

                  <div class="form-group row" id="zDesc<?= $l['directory'] ?>">
                    <label for="pDesc" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_DESCRIPTION ?></label>
                    <div class="col-sm-9">
                      <?= tep_draw_textarea_field('products_description[' . $l['id'] . ']', 'soft', '70', '15', $translations[$l['id']]['description'] ?? '', 'required aria-required="true" class="form-control" id="pDesc"') ?>
                    </div>
                  </div>

                  <div class="form-group row" id="zUrl<?= $l['directory'] ?>">
                    <label for="pUrl" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_URL ?></label>
                    <div class="col-sm-9">
                      <?= tep_draw_input_field('products_url[' . $l['id'] . ']', $translations[$l['id']]['url'] ?? '', 'class="form-control" id="pUrl" aria-describedby="pUrlHelp"') ?>
                      <small id="pUrlHelp" class="form-text text-muted">
                        <?= TEXT_PRODUCTS_URL_WITHOUT_HTTP ?>
                      </small>
                    </div>
                  </div>

                  <div class="form-group row" id="zSeoTitle<?= $l['directory'] ?>">
                    <label for="pSeoTitle" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_SEO_TITLE ?></label>
                    <div class="col-sm-9">
                      <?= tep_draw_input_field('products_seo_title[' . $l['id'] . ']', $translations[$l['id']]['seo_title'] ?? '', 'class="form-control" id="pSeoTitle" aria-describedby="pSeoTitleHelp"') ?>
                      <small id="pSeoTitleHelp" class="form-text text-muted">
                        <?= TEXT_PRODUCTS_SEO_TITLE_HELP ?>
                      </small>
                    </div>
                  </div>

                  <div class="form-group row" id="zSeoDesc<?= $l['directory'] ?>">
                    <label for="pSeoDesc" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_SEO_DESCRIPTION ?></label>
                    <div class="col-sm-9">
                      <?= tep_draw_textarea_field('products_seo_description[' . $l['id'] . ']', 'soft', '70', '15', $translations[$l['id']]['seo_description'] ?? '', 'class="form-control" id="pSeoDesc"  aria-describedby="pSeoDescHelp"') ?>
                      <small id="pSeoDescHelp" class="form-text text-muted">
                        <?= TEXT_PRODUCTS_SEO_DESCRIPTION_HELP ?>
                      </small>
                    </div>
                  </div>

                  <div class="form-group row" id="zSeoKeywords<?= $l['directory'] ?>">
                    <label for="pSeoKeywords" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PRODUCTS_SEO_KEYWORDS ?></label>
                    <div class="col-sm-9">
                      <?= tep_draw_input_field('products_seo_keywords[' . $l['id'] . ']', $translations[$l['id']]['seo_keywords'] ?? '', 'class="form-control" id="pSeoKeywords" placeholder="' . PLACEHOLDER_COMMA_SEPARATION . '" aria-describedby="pSeoKeywordsHelp"') ?>
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

            if ('' !== $show) {
              $show = '';
            }
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
                <?=
                tep_draw_input_field('products_image', '', 'id="pImg"', 'file', null, (Text::is_empty($product->get('image')) ? 'required aria-required="true" ' : null) . 'class="custom-file-input"'),
                '<label class="custom-file-label" for="pImg">', $product->get('image'), '</label>'
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
              foreach ($product->get('images') as $pi) {
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

          <?= $OSCOM_Hooks->call('categories', 'injectImageForm') ?>

          <script>
          $('#piList').sortable({ containment: 'parent' });

          var piSize = <?= $pi_counter ?>;

          function addNewPiForm() {
            piSize++;

            $('#piList').append('<div class="row mb-2" id="piId' + piSize + '"><div class="col"><div class="custom-file mb-2"><input type="file" class="custom-file-input" id="pImg' + piSize + '" name="products_image_large_new_' + piSize + '"><label class="custom-file-label" for="pImg' + piSize + '">&nbsp;</label></div></div><div class="col"><textarea name="products_image_htmlcontent_new_' + piSize + '" wrap="soft" class="form-control" cols="70" rows="3"></textarea><small class="form-text text-muted"><?= TEXT_PRODUCTS_LARGE_IMAGE_HTML_CONTENT ?></small></div><div class="col-1"><i class="fas fa-arrows-alt-v mr-2"></i><a class="piDel" data-pi-id="' + piSize + '"><i class="fas fa-trash text-danger"></i></a></div></div>');
          }

          $('a.piDel').click(function(e){
            var p = $(this).data('pi-id');
            $('#piId' + p).effect('blind').remove();

            e.preventDefault();
          });
          </script>
        </div>
      </div>

      <?= $OSCOM_Hooks->call('categories', 'productTab') ?>
    </div>
  </div>

  <script>
  updateGross();
  $('#products_date_available').datepicker({ dateFormat: 'yy-mm-dd' });
  </script>

  <?=
  tep_draw_hidden_field('products_date_added', ($product->get('date_added') ?: date('Y-m-d'))),
  tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success btn-block btn-lg mt-3 mb-1'),
  tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('categories.php', 'cPath=' . $cPath . (isset($_GET['pID']) ? '&pID=' . (int)$_GET['pID'] : '')), null, null, 'btn-light')
  ?>

</form>

<?php
  } elseif ('new_product_preview' === $action) {
    $product = product_by_id::administer($_GET['pID']);
    $translations = $product->get('translations');

    foreach (tep_get_languages() as $l) {
      ?>

      <div class="row">
        <div class="col">
          <h1 class="display-4 mb-2"><?= tep_image(tep_catalog_href_link('includes/languages/' . $l['directory'] . '/images/' . $l['image']), $l['name']) . '&nbsp;' . $translations[$l['id']]['name'] ?></h1>
        </div>
        <div class="col text-right align-self-center">
          <h1 class="display-4 mb-2"><?= $product->format('price') ?></h1>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-3 text-left text-sm-right font-weight-bold"><?= TEXT_PRODUCTS_DESCRIPTION ?></div>
        <div class="col-sm-9"><?= $translations[$l['id']]['description'] ?></div>
      </div>

      <div class="row">
        <div class="col-sm-3 text-left text-sm-right font-weight-bold"><?= TEXT_PRODUCTS_IMAGE ?></div>
        <div class="col-sm-9"><?= tep_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'images/' . $product->get('image')) ?></div>
      </div>

      <div class="row">
        <div class="col-sm-3 text-left text-sm-right font-weight-bold"><?= TEXT_PRODUCTS_URL ?></div>
        <div class="col-sm-9"><?= $translations[$l['id']]['url'] ?>&nbsp;</div>
      </div>

      <div class="row">
        <div class="col-sm-3 text-left text-sm-right font-weight-bold"><?= TEXT_PRODUCT_DATE_ADDED ?></div>
        <div class="col-sm-9"><?= $product->get('date_added') ?></div>
      </div>

      <div class="row">
        <div class="col-sm-3 text-left text-sm-right font-weight-bold"><?= TEXT_PRODUCT_DATE_AVAILABLE ?></div>
        <div class="col-sm-9"><?= $product->get('date_available') ?>&nbsp;</div>
      </div>
      <?php
    }

    if (isset($_GET['origin'])) {
      $pos_params = strpos($_GET['origin'], '?', 0);
      if ($pos_params) {
        $back_url = substr($_GET['origin'], 0, $pos_params);
        $back_url_params = substr($_GET['origin'], $pos_params + 1);
      } else {
        $back_url = $_GET['origin'];
        $back_url_params = '';
      }
    } else {
      $back_url = 'categories.php';
      $back_url_params = 'cPath=' . $cPath . '&pID=' . (int)$product->get('id');
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
      <?=
      isset($_GET['search'])
      ? tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link('categories.php'), null, null, 'btn-light')
      : tep_draw_bootstrap_button(IMAGE_NEW_CATEGORY, 'fas fa-sitemap', tep_href_link('categories.php', 'cPath=' . $cPath . '&action=new_category'), null, null, 'btn-danger btn-block mb-1')
        . tep_draw_bootstrap_button(IMAGE_NEW_PRODUCT, 'fas fa-boxes', tep_href_link('categories.php', 'cPath=' . $cPath . '&action=new_product'), null, null, 'btn-danger btn-block mb-1')

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
            if (isset($_GET['search'])) {
              $search = Text::prepare($_GET['search']);

              $categories_query = tep_db_query("SELECT c.*, cd.* FROM categories c, categories_description cd WHERE c.categories_id = cd.categories_id AND cd.language_id = " . (int)$_SESSION['languages_id'] . " AND cd.categories_name LIKE '%" . tep_db_input($search) . "%' ORDER BY c.sort_order, cd.categories_name");
            } else {
              $categories_query = tep_db_query("SELECT c.*, cd.* FROM categories c, categories_description cd WHERE c.parent_id = " . (int)$current_category_id . " AND c.categories_id = cd.categories_id AND cd.language_id = " . (int)$_SESSION['languages_id'] . " ORDER BY c.sort_order, cd.categories_name");
            }
            $categories_count = mysqli_num_rows($categories_query);

            while ($categories = $categories_query->fetch_assoc()) {

              // Get parent_id for subcategories if search
              if (isset($_GET['search'])) {
                $cPath= $categories['parent_id'];
              }

              if (!isset($cInfo) && (!isset($_GET['pID']) && !isset($_GET['cID']) || (isset($_GET['cID']) && ($_GET['cID'] == $categories['categories_id']))) && !Text::is_prefixed_by($action, 'new')) {
                $cInfo = new objectInfo($categories);
              }

              if (isset($cInfo->categories_id) && ($categories['categories_id'] == $cInfo->categories_id) ) {
                echo '<tr class="table-active" onclick="document.location.href=\'' . tep_href_link('categories.php', tep_get_path($categories['categories_id'])) . '\'">' . "\n";
                $icon = '<i class="fas fa-chevron-circle-right text-info"></i>';
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('categories.php', 'cPath=' . $cPath . '&cID=' . $categories['categories_id']) . '\'">' . "\n";
                $icon = '<a href="' . tep_href_link('categories.php', 'cPath=' . $cPath . '&cID=' . $categories['categories_id']) . '"><i class="fas fa-info-circle text-muted"></i></a>';
              }
              ?>
                <th><?= $categories['categories_name'] ?></th>
                <td>&nbsp;</td>
                <td class="text-right">
                  <?=
                  '<a href="' . tep_href_link('categories.php', tep_get_path($categories['categories_id'])) . '"><i class="fas fa-folder-open mr-2 text-dark"></i></a>',
                  $icon
                  ?>
                </td>
              </tr>
              <?php
            }

            if (isset($_GET['search'])) {
              $products_query = tep_db_query("SELECT p.*, pd.*, p2c.categories_id FROM products p, products_description pd, products_to_categories p2c WHERE p.products_id = pd.products_id AND pd.language_id = " . (int)$_SESSION['languages_id'] . " AND p.products_id = p2c.products_id AND ((pd.products_name LIKE '%" . tep_db_input($search) . "%') || (p.products_model LIKE '%" . tep_db_input($search) . "%') ||  (p.products_gtin LIKE '%" . tep_db_input($search) . "%')) ORDER BY pd.products_name");
            } else {
              $products_query = tep_db_query("SELECT p.*, pd.* FROM products p, products_description pd, products_to_categories p2c WHERE p.products_id = pd.products_id AND pd.language_id = " . (int)$_SESSION['languages_id'] . " AND p.products_id = p2c.products_id AND p2c.categories_id = " . (int)$current_category_id . " ORDER BY pd.products_name");
            }
            $products_count = mysqli_num_rows($products_query);

            while ($products = $products_query->fetch_assoc()) {
        // Get categories_id for product if search
              if (isset($_GET['search'])) {
                $cPath = $products['categories_id'];
              }
              $p = new Product($products);

              if ( !isset($product) && !isset($cInfo) && (!isset($_GET['cID']) && !isset($_GET['pID']) || (isset($_GET['pID']) && ($_GET['pID'] == $p->get('id')))) && !Text::is_prefixed_by($action, 'new')) {
                $product = $p;
              }

              $icons = '<a href="' . tep_href_link('categories.php', 'cPath=' . $cPath . '&pID=' . (int)$p->get('id') . '&action=new_product_preview&read=only') . '"><i class="fas fa-eye mr-2 text-dark"></i></a>'
                     . '<a href="' . tep_href_link('categories.php', 'cPath=' . $cPath . '&pID=' . (int)$p->get('id') . '&action=new_product') . '"><i class="fas fa-cogs mr-2 text-dark"></i></a>';
              if (isset($product) && ($p->get('id') == $product->get('id')) ) {
                echo '<tr class="table-active" onclick="document.location.href=\'' . tep_href_link('categories.php', 'cPath=' . $cPath . '&pID=' . $p->get('id') . '&action=new_product_preview&read=only') . '\'">';
                $icons .= '<i class="fas fa-chevron-circle-right text-info"></i>';
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('categories.php', 'cPath=' . $cPath . '&pID=' . $p->get('id')) . '\'">';
                $icons .= '<a href="' . tep_href_link('categories.php', 'cPath=' . $cPath . '&pID=' . $p->get('id')) . '"><i class="fas fa-info-circle text-muted"></i></a>';
              }
              ?>
                <th><?= $p->get('name') ?></th>
                <td class="text-center">
                  <?=
                  ($p->get('status') == '1')
                  ? '<i class="fas fa-check-circle text-success"></i> <a href="' . tep_href_link('categories.php', 'action=setflag&flag=0&pID=' . $p->get('id') . '&cPath=' . $cPath) . '"><i class="fas fa-times-circle text-muted"></i></a>'
                  : '<a href="' . tep_href_link('categories.php', 'action=setflag&flag=1&pID=' . $p->get('id') . '&cPath=' . $cPath) . '"><i class="fas fa-check-circle text-muted"></i></a>  <i class="fas fa-times-circle text-danger"></i>'
                  ?>
                </td>
                <td class="text-right"><?= $icons ?></td>
              </tr>
              <?php
            }

            if (isset($cPath_array) && count($cPath_array) > 1) {
              $cPath_back = 'cPath=' . implode('_', array_slice($cPath_array, 0, -1));
            } else {
              $cPath_back = '';
            }
            ?>
          </tbody>
        </table>
      </div>

      <div class="row my-1">
        <div class="col"><?= TEXT_CATEGORIES . '&nbsp;' . $categories_count . '<br>' . TEXT_PRODUCTS . '&nbsp;' . $products_count ?></div>
        <div class="col text-right mr-2"><?php if (isset($cPath_array) && (count($cPath_array) > 0)) echo tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link('categories.php', $cPath_back), null, null, 'btn-light mr-2'); if (!isset($_GET['search'])) echo tep_draw_bootstrap_button(IMAGE_NEW_CATEGORY, 'fas fa-sitemap', tep_href_link('categories.php', 'cPath=' . $cPath . '&action=new_category'), null, null, 'btn-danger mr-2') . tep_draw_bootstrap_button(IMAGE_NEW_PRODUCT, 'fas fa-boxes', tep_href_link('categories.php', 'cPath=' . $cPath . '&action=new_product'), null, null, 'btn-danger'); ?></div>
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
        $translations_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT l.*, cd.*
 FROM languages l LEFT JOIN categories_description cd
   ON l.languages_id = cd.language_id AND cd.categories_id = %d
 ORDER BY l.sort_order
EOSQL
          , $cInfo->categories_id));
        while ($l = $translations_query->fetch_assoc()) {
          $language_icon = tep_image(tep_catalog_href_link("includes/languages/{$l['directory']}/images/{$l['image']}"), $l['name']);
          $category_inputs_string .= '<div class="input-group mb-1">';
            $category_inputs_string .= '<div class="input-group-prepend">';
              $category_inputs_string .= '<span class="input-group-text">'. $language_icon . '</span>';
            $category_inputs_string .= '</div>';
            $category_inputs_string .= tep_draw_input_field('categories_name[' . $l['languages_id'] . ']', $l['categories_name'] ?? '', 'required aria-required="true"');
          $category_inputs_string .= '</div>';
          $category_seo_title_string .= '<div class="input-group mb-1">';
            $category_seo_title_string .= '<div class="input-group-prepend">';
              $category_seo_title_string .= '<span class="input-group-text">'. $language_icon . '</span>';
            $category_seo_title_string .= '</div>';
            $category_seo_title_string .= tep_draw_input_field('categories_seo_title[' . $l['languages_id'] . ']', $l['categories_seo_title'] ?? '');
          $category_seo_title_string .= '</div>';
         $category_description_string .= '<div class="input-group mb-1">';
            $category_description_string .= '<div class="input-group-prepend">';
              $category_description_string .= '<span class="input-group-text">'. $language_icon . '</span>';
            $category_description_string .= '</div>';
            $category_description_string .= tep_draw_textarea_field('categories_description[' . $l['languages_id'] . ']', 'soft', '80', '10', $l['categories_description'] ?? '');
          $category_description_string .= '</div>';
          $category_seo_description_string .= '<div class="input-group mb-1">';
            $category_seo_description_string .= '<div class="input-group-prepend">';
              $category_seo_description_string .= '<span class="input-group-text">'. $language_icon . '</span>';
            $category_seo_description_string .= '</div>';
            $category_seo_description_string .= tep_draw_textarea_field('categories_seo_description[' . $l['languages_id'] . ']', 'soft', '80', '10', $l['categories_seo_description'] ?? '');
          $category_seo_description_string .= '</div>';
        }

        $contents[] = ['text' => TEXT_EDIT_CATEGORIES_NAME . $category_inputs_string];
        $contents[] = ['text' => TEXT_EDIT_CATEGORIES_SEO_TITLE . $category_seo_title_string];
        $contents[] = ['text' => TEXT_EDIT_CATEGORIES_DESCRIPTION . $category_description_string];
        $contents[] = ['text' => TEXT_EDIT_CATEGORIES_SEO_DESCRIPTION . $category_seo_description_string];
        $contents[] = ['text' => TEXT_EDIT_CATEGORIES_IMAGE . tep_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'images/' . $cInfo->categories_image, $cInfo->categories_name)];
        $contents[] = ['text' => '<div class="custom-file mb-2">' . tep_draw_input_field('categories_image', '', 'id="cImg"', 'file', null, 'class="custom-file-input"') . '<label class="custom-file-label" for="cImg">' .  $cInfo->categories_image . '</label></div>'];
        $contents[] = ['text' => TEXT_EDIT_SORT_ORDER . '<br>' . tep_draw_input_field('sort_order', $cInfo->sort_order, 'size="2"')];
        $contents[] = [
          'class' => 'text-center',
          'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success btn-block btn-lg mb-1')
                  . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times',  tep_href_link('categories.php', 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id), null, null, 'btn-light'),
        ];
        break;
      case 'delete_category':
        $subcategory_products_check = tep_db_query("SELECT COUNT(*) AS total FROM (SELECT categories_id AS id FROM categories WHERE parent_id = " . (int)$_GET['cID'] . " UNION SELECT p2c.products_id AS id FROM products_to_categories p2c LEFT JOIN products_to_categories self ON p2c.products_id = self.products_id AND p2c.categories_id != self.categories_id WHERE p2c.categories_id = " . (int)$_GET['cID'] . " AND self.categories_id IS NULL ) combined")->fetch_assoc();

        $heading[] = ['text' => TEXT_INFO_HEADING_DELETE_CATEGORY];

        $contents = ['form' => tep_draw_form('categories', 'categories.php', 'action=delete_category_confirm&cPath=' . $cPath) . tep_draw_hidden_field('categories_id', $cInfo->categories_id)];
        $contents[] = ['text' => TEXT_DELETE_CATEGORY_INTRO];
        $contents[] = ['text' => '<strong>' . $cInfo->categories_name . '</strong>'];
        if ($subcategory_products_check['total'] > 0) {
          $contents[] = ['text' => TEXT_DELETE_WARNING];
        }
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

        $contents = ['form' => tep_draw_form('products', 'categories.php', 'action=delete_product_confirm&cPath=' . $cPath) . tep_draw_hidden_field('products_id', $product->get('id'))];
        $contents[] = ['text' => TEXT_DELETE_PRODUCT_INTRO];
        $contents[] = ['class' => 'text-center text-uppercase font-weight-bold', 'text' => $product->get('name')];

        $product_categories_string = '';
        foreach (tep_generate_category_path($product->get('id'), 'product') as $i => $product_categories) {
          $category_path = implode('&nbsp;&gt;&nbsp;', array_column($product_categories, 'text'));

          $product_categories_string .= '<div class="custom-control custom-switch">';
            $product_categories_string .= tep_draw_selection_field('product_categories[]', 'checkbox', $product_categories[count($product_categories)-1]['id'], true, 'class="custom-control-input" id="dProduct_' . $i . '"');
            $product_categories_string .= '<label for="dProduct_' . $i . '" class="custom-control-label text-muted"><small>' . $category_path . '</small></label>';
          $product_categories_string .= '</div>';
        }

        $contents[] = ['text' => $product_categories_string];
        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', null, 'primary', null, 'btn-danger btn-block btn-lg mb-1') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times',  tep_href_link('categories.php', 'cPath=' . $cPath . '&pID=' . $product->get('id')), null, null, 'btn-light')];
        break;
      case 'move_product':
        $heading[] = ['text' => TEXT_INFO_HEADING_MOVE_PRODUCT];

        $contents = ['form' => tep_draw_form('products', 'categories.php', 'action=move_product_confirm&cPath=' . $cPath) . tep_draw_hidden_field('products_id', $product->get('id'))];
        $contents[] = ['text' => sprintf(TEXT_MOVE_PRODUCTS_INTRO, $product->get('name'))];
        $contents[] = ['text' => TEXT_INFO_CURRENT_CATEGORIES . '<br><i>' . tep_output_generated_category_path($product->get('id'), 'product') . '</i>'];
        $contents[] = ['text' => sprintf(TEXT_MOVE, $product->get('name')) . '<br>' . tep_draw_pull_down_menu('move_to_category_id', tep_get_category_tree(), $current_category_id)];
        $contents[] = [
          'class' => 'text-center',
          'text' => tep_draw_bootstrap_button(IMAGE_MOVE, 'fas fa-arrows-alt', null, null, null, 'btn-success btn-block btn-lg mb-1')
                  . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('categories.php', 'cPath=' . $cPath . '&pID=' . $product->get('id')), null, null, 'btn-light')];
        break;
      case 'copy_to':
        $heading[] = ['text' => TEXT_INFO_HEADING_COPY_TO];

        $contents = ['form' => tep_draw_form('copy_to', 'categories.php', 'action=copy_to_confirm&cPath=' . $cPath) . tep_draw_hidden_field('products_id', $product->get('id'))];
        $contents[] = ['text' => TEXT_INFO_COPY_TO_INTRO];
        $contents[] = ['text' => TEXT_INFO_CURRENT_CATEGORIES . '<br><i>' . tep_output_generated_category_path($product->get('id'), 'product') . '</i>'];
        $contents[] = ['text' => TEXT_CATEGORIES . '<br>' . tep_draw_pull_down_menu('categories_id', tep_get_category_tree(), $current_category_id)];
        $contents[] = ['text' => TEXT_HOW_TO_COPY . '<br><div class="custom-control custom-radio custom-control-inline">' . tep_draw_selection_field('copy_as', 'radio', 'link', true, 'id="cLink" class="custom-control-input"') . '<label class="custom-control-label" for="cLink">' . TEXT_COPY_AS_LINK . '</label></div><br><div class="custom-control custom-radio custom-control-inline">' . tep_draw_selection_field('copy_as', 'radio', 'duplicate', null, 'id="dLink" class="custom-control-input"') . '<label class="custom-control-label" for="dLink">' . TEXT_COPY_AS_DUPLICATE . '</label></div>'];
        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_COPY, 'fas fa-copy', null, null, null, 'btn-success btn-block btn-lg mb-1') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('categories.php', 'cPath=' . $cPath . '&pID=' . $product->get('id')), null, null, 'btn-light')];
        break;
      default:
        if ($categories_count + $products_count > 0) {
          if (isset($cInfo) && is_object($cInfo)) { // category info box contents
            Guarantor::ensure_global('category_tree');
            $category_path_string = $category_tree->find_path($category_tree->get_parent_id($cInfo->categories_id));

            $heading[] = ['text' => $cInfo->categories_name];

            $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_EDIT, 'fas fa-cogs', tep_href_link('categories.php', 'cPath=' . $category_path_string . '&cID=' . $cInfo->categories_id . '&action=edit_category'), null, null, 'btn-warning mr-2') . tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', tep_href_link('categories.php', 'cPath=' . $category_path_string . '&cID=' . $cInfo->categories_id . '&action=delete_category'), null, null, 'btn-danger mr-2')];
            $contents[] = ['text' => TEXT_DATE_ADDED . ' ' . tep_date_short($cInfo->date_added)];
            if (!Text::is_empty($cInfo->last_modified)) {
              $contents[] = ['text' => TEXT_LAST_MODIFIED . ' ' . tep_date_short($cInfo->last_modified)];
            }
            $contents[] = ['text' => tep_info_image($cInfo->categories_image, $cInfo->categories_name, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT) . '<br>' . $cInfo->categories_image];

            $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_MOVE, 'fas fa-arrows-alt', tep_href_link('categories.php', 'cPath=' . $category_path_string . '&cID=' . $cInfo->categories_id . '&action=move_category'), null, null, 'btn-light')];

          } elseif (isset($product) && ($product instanceof Product)) { // product info box contents
            $heading[] = ['text' => $product->get('name')];

            $contents[] = [
              'class' => 'text-center',
              'text' => tep_draw_bootstrap_button(IMAGE_EDIT, 'fas fa-cogs', tep_href_link('categories.php', 'action=new_product&cPath=' . $cPath . '&pID=' . $product->get('id')), null, null, 'btn-warning mr-2')
                      . tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', tep_href_link('categories.php', 'action=delete_product&cPath=' . $cPath . '&pID=' . $product->get('id')), null, null, 'btn-danger mr-2'),
            ];
            $contents[] = ['text' => TEXT_DATE_ADDED . ' ' . tep_date_short($product->get('date_added'))];
            if (!Text::is_empty($product->get('last_modified'))) {
              $contents[] = ['text' => TEXT_LAST_MODIFIED . ' ' . tep_date_short($product->get('last_modified'))];
            }
            if (date('Y-m-d') < $product->get('date_available')) {
              $contents[] = ['text' => TEXT_DATE_AVAILABLE . ' ' . tep_date_short($product->get('date_available'))];
            }
            $contents[] = ['text' => tep_info_image($product->get('image'), $product->get('name'), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '<br>' . $product->get('image')];
            $contents[] = ['text' => TEXT_PRODUCTS_PRICE_INFO . ' ' . $product->format('price') . '<br>' . TEXT_PRODUCTS_QUANTITY_INFO . ' ' . $product->get('quantity')];
            $contents[] = ['text' => TEXT_PRODUCTS_AVERAGE_RATING . ' ' . number_format($product->get('review_percentile'), 2) . '%'];
            $contents[] = [
              'class' => 'text-center',
              'text' => tep_draw_bootstrap_button(IMAGE_MOVE, 'fas fa-arrows-alt', tep_href_link('categories.php', 'cPath=' . $cPath . '&pID=' . $product->get('id') . '&action=move_product'), null, null, 'btn-light mr-2')
                      . tep_draw_bootstrap_button(IMAGE_COPY_TO, 'fas fa-copy', tep_href_link('categories.php', 'cPath=' . $cPath . '&pID=' . $product->get('id') . '&action=copy_to'), null, null, 'btn-light'),
            ];
          }
        } else { // create category/product info
          $heading[] = ['text' => EMPTY_CATEGORY];

          $contents[] = ['text' => TEXT_NO_CHILD_CATEGORIES_OR_PRODUCTS];
        }
        break;
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
  }
?>

<script>
$(document).on('change', '#cImg, [id^=pImg]', function (event) { $(this).next('.custom-file-label').html(event.target.files[0].name); });
</script>


<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
