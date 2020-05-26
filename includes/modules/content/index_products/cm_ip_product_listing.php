<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_ip_product_listing extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_IP_PRODUCT_LISTING_';

    function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      global $category, $cPath_array, $cPath, $current_category_id, $messageStack, $currencies, $PHP_SELF, $OSCOM_Hooks;

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
        if ($value > 0) $column_list[] = $key;
      }

// show the products of a specified manufacturer
      if (!empty($_GET['manufacturers_id'])) {
        if (isset($_GET['filter_id']) && tep_not_null($_GET['filter_id'])) {
// We are asked to show only a specific category
          $listing_sql = "select p.*, pd.*, m.*, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price, p.products_quantity as in_stock, if(s.status, 1, 0) as is_special from products p left join specials s on p.products_id = s.products_id, products_description pd, manufacturers m, products_to_categories p2c where p.products_status = '1' and p.manufacturers_id = m.manufacturers_id and m.manufacturers_id = '" . (int)$_GET['manufacturers_id'] . "' and p.products_id = p2c.products_id and pd.products_id = p2c.products_id and pd.language_id = '" . (int)$_SESSION['languages_id'] . "' and p2c.categories_id = '" . (int)$_GET['filter_id'] . "'";
        } else {
// We show them all
          $listing_sql = "select p.*, pd.*, m.*, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price, p.products_quantity as in_stock, if(s.status, 1, 0) as is_special from products p left join specials s on p.products_id = s.products_id, products_description pd, manufacturers m where p.products_status = '1' and pd.products_id = p.products_id and pd.language_id = '" . (int)$_SESSION['languages_id'] . "' and p.manufacturers_id = m.manufacturers_id and m.manufacturers_id = '" . (int)$_GET['manufacturers_id'] . "'";
        }
      } else {
// show the products in a given categorie
        if (isset($_GET['filter_id']) && tep_not_null($_GET['filter_id'])) {
// We are asked to show only specific catgeory
          $listing_sql = "select p.*, pd.*, m.*, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price, p.products_quantity as in_stock, if(s.status, 1, 0) as is_special from products p left join specials s on p.products_id = s.products_id, products_description pd, manufacturers m, products_to_categories p2c where p.products_status = '1' and p.manufacturers_id = m.manufacturers_id and m.manufacturers_id = '" . (int)$_GET['filter_id'] . "' and p.products_id = p2c.products_id and pd.products_id = p2c.products_id and pd.language_id = '" . (int)$_SESSION['languages_id'] . "' and p2c.categories_id = '" . (int)$current_category_id . "'";
        } else {
// We show them all
          $listing_sql = "select p.*, pd.*, m.*, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price, p.products_quantity as in_stock, if(s.status, 1, 0) as is_special from products_description pd, products p left join manufacturers m on p.manufacturers_id = m.manufacturers_id left join specials s on p.products_id = s.products_id, products_to_categories p2c where p.products_status = '1' and p.products_id = p2c.products_id and pd.products_id = p2c.products_id and pd.language_id = '" . (int)$_SESSION['languages_id'] . "' and p2c.categories_id = '" . (int)$current_category_id . "'";
        }
      }
      
      $listing_sql .= $OSCOM_Hooks->call('filter', 'injectSQL');

      if ( (!isset($_GET['sort'])) || (!preg_match('/^[1-9][ad]$/', $_GET['sort'])) || (substr($_GET['sort'], 0, 1) > count($column_list)) ) {
        for ($i=0, $n=count($column_list); $i<$n; $i++) {
          if ($column_list[$i] == 'PRODUCT_LIST_NAME') {
            $_GET['sort'] = $i+1 . 'a';
            $listing_sql .= " order by pd.products_name";
            break;
          }
        }
      } else {
        $sort_col = substr($_GET['sort'], 0 , 1);
        $sort_order = substr($_GET['sort'], 1);

        switch ($column_list[$sort_col-1]) {
          case 'PRODUCT_LIST_MODEL':
            $listing_sql .= " order by p.products_model " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
            break;
          case 'PRODUCT_LIST_NAME':
            $listing_sql .= " order by pd.products_name " . ($sort_order == 'd' ? 'desc' : '');
            break;
          case 'PRODUCT_LIST_MANUFACTURER':
            $listing_sql .= " order by m.manufacturers_name " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
            break;
          case 'PRODUCT_LIST_QUANTITY':
            $listing_sql .= " order by p.products_quantity " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
            break;
          case 'PRODUCT_LIST_IMAGE':
            $listing_sql .= " order by pd.products_name";
            break;
          case 'PRODUCT_LIST_WEIGHT':
            $listing_sql .= " order by p.products_weight " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
            break;
          case 'PRODUCT_LIST_PRICE':
            $listing_sql .= " order by final_price " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
            break;
          case 'PRODUCT_LIST_ID':
            $listing_sql .= " order by p.products_id " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
            break;
          case 'PRODUCT_LIST_ORDERED':
            $listing_sql .= " order by p.products_ordered " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
            break;
        }
      }

// optional Product List Filter
      $output = null;
      if (PRODUCT_LIST_FILTER > 0) {
        if (empty($_GET['manufacturers_id'])) {
          $filterlist_sql = "select distinct m.manufacturers_id as id, m.manufacturers_name as name from products p, products_to_categories p2c, manufacturers m where p.products_status = '1' and p.manufacturers_id = m.manufacturers_id and p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$current_category_id . "' order by m.manufacturers_name";
        } else {
          $filterlist_sql = "select distinct c.categories_id as id, cd.categories_name as name from products p, products_to_categories p2c, categories c, categories_description cd where p.products_status = '1' and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and p2c.categories_id = cd.categories_id and cd.language_id = '" . (int)$_SESSION['languages_id'] . "' and p.manufacturers_id = '" . (int)$_GET['manufacturers_id'] . "' order by cd.categories_name";
        }

        $filterlist_query = tep_db_query($filterlist_sql);
        if (tep_db_num_rows($filterlist_query) > 1) {
          if (empty($_GET['manufacturers_id'])) {
            $output = tep_draw_hidden_field('cPath', $cPath);
            $options = [['id' => '', 'text' => TEXT_ALL_MANUFACTURERS]];
          } else {
            $output = tep_draw_hidden_field('manufacturers_id', $_GET['manufacturers_id']);
            $options = [['id' => '', 'text' => TEXT_ALL_CATEGORIES]];
          }
          $output .= tep_draw_hidden_field('sort', $_GET['sort']);
          while ($filterlist = tep_db_fetch_array($filterlist_query)) {
            $options[] = ['id' => $filterlist['id'], 'text' => $filterlist['name']];
          }
          $output .= tep_draw_pull_down_menu('filter_id', $options, ($_GET['filter_id'] ?? ''), 'onchange="this.form.submit()"');
          $output .= tep_hide_session_id();
        }
      }

      $content_width = MODULE_CONTENT_IP_PRODUCT_LISTING_CONTENT_WIDTH;

      $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
      include 'includes/modules/content/cm_template.php';
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_IP_PRODUCT_LISTING_STATUS' => [
          'title' => 'Enable Product Listing Module',
          'value' => 'True',
          'desc' => 'Should this module be enabled?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_IP_PRODUCT_LISTING_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_IP_PRODUCT_LISTING_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '200',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
