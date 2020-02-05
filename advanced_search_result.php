<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  require "includes/languages/$language/advanced_search.php";

  $error = false;

  if ( (isset($_GET['keywords']) && empty($_GET['keywords'])) &&
       (isset($_GET['dfrom']) && (empty($_GET['dfrom']) || ($_GET['dfrom'] == DOB_FORMAT_STRING))) &&
       (isset($_GET['dto']) && (empty($_GET['dto']) || ($_GET['dto'] == DOB_FORMAT_STRING))) &&
       (isset($_GET['pfrom']) && !is_numeric($_GET['pfrom'])) &&
       (isset($_GET['pto']) && !is_numeric($_GET['pto'])) ) {
    $error = true;

    $messageStack->add_session('search', ERROR_AT_LEAST_ONE_INPUT);
  } else {
    $dfrom = '';
    $dto = '';
    $pfrom = '';
    $pto = '';
    $keywords = '';

    if (isset($_GET['dfrom'])) {
      $dfrom = (($_GET['dfrom'] == DOB_FORMAT_STRING) ? '' : $_GET['dfrom']);
    }

    if (isset($_GET['dto'])) {
      $dto = (($_GET['dto'] == DOB_FORMAT_STRING) ? '' : $_GET['dto']);
    }

    if (isset($_GET['pfrom'])) {
      $pfrom = $_GET['pfrom'];
    }

    if (isset($_GET['pto'])) {
      $pto = $_GET['pto'];
    }

    if (isset($_GET['keywords'])) {
      $keywords = tep_db_prepare_input($_GET['keywords']);
    }

    $price_check_error = false;
    if (tep_not_null($pfrom)) {
      if (!settype($pfrom, 'double')) {
        $error = true;
        $price_check_error = true;

        $messageStack->add_session('search', ERROR_PRICE_FROM_MUST_BE_NUM);
      }
    }

    if (tep_not_null($pto)) {
      if (!settype($pto, 'double')) {
        $error = true;
        $price_check_error = true;

        $messageStack->add_session('search', ERROR_PRICE_TO_MUST_BE_NUM);
      }
    }

    if (($price_check_error == false) && is_float($pfrom) && is_float($pto)) {
      if ($pfrom >= $pto) {
        $error = true;

        $messageStack->add_session('search', ERROR_PRICE_TO_LESS_THAN_PRICE_FROM);
      }
    }

    if (tep_not_null($keywords)) {
      if (!tep_parse_search_string($keywords, $search_keywords)) {
        $error = true;

        $messageStack->add_session('search', ERROR_INVALID_KEYWORDS);
      }
    }
  }

  if (empty($dfrom) && empty($dto) && empty($pfrom) && empty($pto) && empty($keywords)) {
    $error = true;

    $messageStack->add_session('search', ERROR_AT_LEAST_ONE_INPUT);
  }

  if ($error == true) {
    tep_redirect(tep_href_link('advanced_search.php', tep_get_all_get_params(), 'NONSSL', true, false));
  }
  // create column list
  $define_list = [
    'PRODUCT_LIST_MODEL' => PRODUCT_LIST_MODEL,
    'PRODUCT_LIST_NAME' => PRODUCT_LIST_NAME,
    'PRODUCT_LIST_MANUFACTURER' => PRODUCT_LIST_MANUFACTURER,
    'PRODUCT_LIST_PRICE' => PRODUCT_LIST_PRICE,
    'PRODUCT_LIST_QUANTITY' => PRODUCT_LIST_QUANTITY,
    'PRODUCT_LIST_WEIGHT' => PRODUCT_LIST_WEIGHT,
    'PRODUCT_LIST_IMAGE' => PRODUCT_LIST_IMAGE,
    'PRODUCT_LIST_BUY_NOW' => PRODUCT_LIST_BUY_NOW,
    'PRODUCT_LIST_ID' => PRODUCT_LIST_ID,
    'PRODUCT_LIST_ORDERED' => PRODUCT_LIST_ORDERED,
  ];

  asort($define_list);

  $column_list = [];
  foreach ($define_list as $key => $value) {
    if ($value > 0) {
      $column_list[] = $key;
    }
  }

  $select_str = "SELECT DISTINCT p.products_id, m.*, p.*, pd.*, p.products_quantity AS in_stock, IF(s.status, s.specials_new_products_price, NULL) AS specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) AS final_price, IF(s.status, 1, 0) AS is_special ";

  if ( (DISPLAY_PRICE_WITH_TAX == 'true') && (tep_not_null($pfrom) || tep_not_null($pto)) ) {
    $select_str .= ", SUM(tr.tax_rate) AS tax_rate ";
  }

  $from_str = "FROM products p LEFT JOIN manufacturers m using(manufacturers_id) LEFT JOIN specials s ON p.products_id = s.products_id";

  if ( (DISPLAY_PRICE_WITH_TAX == 'true') && (tep_not_null($pfrom) || tep_not_null($pto)) ) {
    if (isset($_SESSION['customer_id'])) {
      $country_id = $customer->get_country_id();
      $zone_id = $customer->get_zone_id();
    } else {
      $country_id = STORE_COUNTRY;
      $zone_id = STORE_ZONE;
    }
    $from_str .= " LEFT JOIN tax_rates tr ON p.products_tax_class_id = tr.tax_class_id LEFT JOIN zones_to_geo_zones gz ON tr.tax_zone_id = gz.geo_zone_id AND (gz.zone_country_id IS NULL OR gz.zone_country_id = '0' OR gz.zone_country_id = " . (int)$country_id . ") AND (gz.zone_id IS NULL OR gz.zone_id = '0' OR gz.zone_id = " . (int)$zone_id . ")";
  }

  $from_str .= ", products_description pd, categories c, products_to_categories p2c";

  $where_str = " WHERE p.products_status = 1 AND p.products_id = pd.products_id AND pd.language_id = " . (int)$languages_id . " AND p.products_id = p2c.products_id AND p2c.categories_id = c.categories_id ";

  if (isset($_GET['categories_id']) && tep_not_null($_GET['categories_id'])) {
    if (isset($_GET['inc_subcat']) && ($_GET['inc_subcat'] == '1')) {
      $subcategories_array = [];
      tep_get_subcategories($subcategories_array, $_GET['categories_id']);

      $where_str .= " AND p2c.products_id = p.products_id AND p2c.products_id = pd.products_id AND (p2c.categories_id = " . (int)$_GET['categories_id'];

      foreach ($subcategories_array as $subcategory_id) {
        $where_str .= " OR p2c.categories_id = " . (int)$subcategory_id;
      }

      $where_str .= ")";
    } else {
      $where_str .= " AND p2c.products_id = p.products_id AND p2c.products_id = pd.products_id AND pd.language_id = " . (int)$languages_id . " AND p2c.categories_id = " . (int)$_GET['categories_id'];
    }
  }

  if (isset($_GET['manufacturers_id']) && tep_not_null($_GET['manufacturers_id'])) {
    $where_str .= " AND m.manufacturers_id = " . (int)$_GET['manufacturers_id'];
  }

  if (isset($search_keywords) && (count($search_keywords) > 0)) {
    $where_str .= " AND (";
    foreach ($search_keywords as $search_keyword) {
      switch ($search_keyword) {
        case '(':
        case ')':
        case 'and':
        case 'or':
          $where_str .= " " . $search_keyword . " ";
          break;
        default:
          $keyword = tep_db_prepare_input($search_keyword);
          $where_str .= "(";
          if ( (defined('MODULE_HEADER_TAGS_PRODUCT_META_KEYWORDS_STATUS')) && (MODULE_HEADER_TAGS_PRODUCT_META_KEYWORDS_STATUS != 'Meta') ) {
            $where_str .= "pd.products_seo_keywords LIKE '%" . tep_db_input($keyword) . "%' OR ";
          }
          $where_str .= "pd.products_name LIKE '%" . tep_db_input($keyword) . "%' OR p.products_model LIKE '%" . tep_db_input($keyword) . "%' OR m.manufacturers_name LIKE '%" . tep_db_input($keyword) . "%'";
          if (isset($_GET['search_in_description']) && ($_GET['search_in_description'] == '1')) $where_str .= " OR pd.products_description LIKE '%" . tep_db_input($keyword) . "%'";
          $where_str .= ')';
          break;
      }
    }
    $where_str .= " )";
  }

  if (tep_not_null($pfrom)) {
    if ($currencies->is_set($currency)) {
      $rate = $currencies->get_value($currency);

      $pfrom = $pfrom / $rate;
    }
  }

  if (tep_not_null($pto)) {
    if (isset($rate)) {
      $pto = $pto / $rate;
    }
  }

  if (DISPLAY_PRICE_WITH_TAX == 'true') {
    if ($pfrom > 0) $where_str .= " AND (IF(s.status, s.specials_new_products_price, p.products_price) * IF(gz.geo_zone_id IS NULL, 1, 1 + (tr.tax_rate / 100) ) >= " . (double)$pfrom . ")";
    if ($pto > 0) $where_str .= " AND (IF(s.status, s.specials_new_products_price, p.products_price) * IF(gz.geo_zone_id IS NULL, 1, 1 + (tr.tax_rate / 100) ) <= " . (double)$pto . ")";
  } else {
    if ($pfrom > 0) $where_str .= " AND (IF(s.status, s.specials_new_products_price, p.products_price) >= " . (double)$pfrom . ")";
    if ($pto > 0) $where_str .= " AND (IF(s.status, s.specials_new_products_price, p.products_price) <= " . (double)$pto . ")";
  }

  if ( (DISPLAY_PRICE_WITH_TAX == 'true') && (tep_not_null($pfrom) || tep_not_null($pto)) ) {
    $where_str .= " GROUP BY p.products_id, tr.tax_priority";
  }

  if ( (!isset($_GET['sort'])) || (!preg_match('/^[1-8][ad]$/', $_GET['sort'])) || (substr($_GET['sort'], 0, 1) > count($column_list)) ) {
    foreach ($column_list as $i => $column) {
      if ($column == 'PRODUCT_LIST_NAME') {
        $_GET['sort'] = $i+1 . 'a';
        $order_str = " ORDER BY pd.products_name";
        break;
      }
    }
  } else {
    $sort_col = substr($_GET['sort'], 0 , 1);
    $sort_order = substr($_GET['sort'], 1);

    switch ($column_list[$sort_col-1]) {
      case 'PRODUCT_LIST_MODEL':
        $order_str = " ORDER BY p.products_model " . ($sort_order == 'd' ? 'DESC' : '') . ", pd.products_name";
        break;
      case 'PRODUCT_LIST_NAME':
        $order_str = " ORDER BY pd.products_name " . ($sort_order == 'd' ? 'DESC' : '');
        break;
      case 'PRODUCT_LIST_MANUFACTURER':
        $order_str = " ORDER BY m.manufacturers_name " . ($sort_order == 'd' ? 'DESC' : '') . ", pd.products_name";
        break;
      case 'PRODUCT_LIST_QUANTITY':
        $order_str = " ORDER BY p.products_quantity " . ($sort_order == 'd' ? 'DESC' : '') . ", pd.products_name";
        break;
      case 'PRODUCT_LIST_IMAGE':
        $order_str = " ORDER BY pd.products_name";
        break;
      case 'PRODUCT_LIST_WEIGHT':
        $order_str = " ORDER BY p.products_weight " . ($sort_order == 'd' ? 'DESC' : '') . ", pd.products_name";
        break;
      case 'PRODUCT_LIST_PRICE':
        $order_str = " ORDER BY final_price " . ($sort_order == 'd' ? 'DESC' : '') . ", pd.products_name";
        break;
      case 'PRODUCT_LIST_ID':
        $order_str = " ORDER BY p.products_id " . ($sort_order == 'd' ? 'DESC' : '') . ", pd.products_name";
        break;
      case 'PRODUCT_LIST_ORDERED':
        $order_str = " ORDER BY p.products_ordered " . ($sort_order == 'd' ? 'DESC' : '') . ", pd.products_name";
        break;
    }
  }

  $listing_sql = $select_str . $from_str . $where_str . $order_str;

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('advanced_search.php'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('advanced_search_result.php', tep_get_all_get_params(), 'NONSSL', true, false));

  require 'includes/template_top.php';
?>

<h1 class="display-4"><?php echo HEADING_TITLE_2; ?></h1>

<div class="contentContainer">

<?php
  require 'includes/modules/product_listing.php';
?>

  <br>

  <div class="buttonSet">
    <?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fas fa-angle-left', tep_href_link('advanced_search.php', tep_get_all_get_params(['sort', 'page']), 'NONSSL', true, false)); ?>
  </div>
</div>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
