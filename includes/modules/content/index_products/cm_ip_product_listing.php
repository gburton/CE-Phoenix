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
      global $cPath, $current_category_id, $languages_id, $messageStack, $currencies, $PHP_SELF;

      $listing_sql = <<<'EOSQL'
SELECT p.*, pd.*, m.*,
  IF(s.status, s.specials_new_products_price, NULL) AS specials_new_products_price,
  IF(s.status, s.specials_new_products_price, p.products_price) AS final_price,
  p.products_quantity AS in_stock,
  IF(s.status, 1, 0) AS is_special
 FROM
  products p
    LEFT JOIN specials s ON p.products_id = s.products_id
    INNER JOIN products_description pd ON p.products_id = pd.products_id
EOSQL;

// show the products of a specified manufacturer
      if (empty($_GET['manufacturers_id'])) {
// show the products in a given category
        if (isset($_GET['filter_id']) && tep_not_null($_GET['filter_id'])) {
// We are asked to show only a specific manufacturer
          $listing_sql .= <<<'EOSQL'
    INNER JOIN manufacturers m ON p.manufacturers_id = m.manufacturers_id
    INNER JOIN products_to_categories p2c ON p.products_id = p2c.products_id
 WHERE p.products_status = 1 AND m.manufacturers_id = 
EOSQL
          . (int)$_GET['filter_id'] . " AND pd.language_id = " . (int)$languages_id . " AND p2c.categories_id = " . (int)$current_category_id;
        } else {
// We show them all
          $listing_sql .= <<<'EOSQL'
    LEFT JOIN manufacturers m ON p.manufacturers_id = m.manufacturers_id
    INNER JOIN products_to_categories p2c
  WHERE p.products_status = 1 AND p.products_id = p2c.products_id AND pd.products_id = p2c.products_id AND pd.language_id = 
EOSQL
          . (int)$languages_id . " AND p2c.categories_id = " . (int)$current_category_id;
        }
      } else {
        if (isset($_GET['filter_id']) && tep_not_null($_GET['filter_id'])) {
// We are asked to show only a specific category
          $listing_sql .= <<<'EOSQL'
    INNER JOIN manufacturers m ON p.manufacturers_id = m.manufacturers_id
    INNER JOIN products_to_categories p2c ON p.products_id = p2c.products_id
  WHERE p.products_status = 1 AND m.manufacturers_id = 
EOSQL
          . (int)$_GET['manufacturers_id'] . " AND pd.language_id = " . (int)$languages_id . " AND p2c.categories_id = " . (int)$_GET['filter_id'];
        } else {
// We show them all
          $listing_sql .= <<<'EOSQL'
    INNER JOIN manufacturers m ON p.manufacturers_id = m.manufacturers_id
  WHERE p.products_status = 1 AND pd.language_id = 
EOSQL
          . (int)$languages_id . " AND m.manufacturers_id = " . (int)$_GET['manufacturers_id'];
        }
      }

      $listing_sql .= $GLOBALS['OSCOM_Hooks']->call('filter', 'injectSQL');
      require 'includes/system/segments/sortable_product_columns.php';

// optional Product List Filter
      $output = null;
      if (PRODUCT_LIST_FILTER > 0) {
        if (empty($_GET['manufacturers_id'])) {
          $filterlist_sql = <<<'EOSQL'
SELECT DISTINCT m.manufacturers_id AS id, m.manufacturers_name AS name
 FROM products p, products_to_categories p2c, manufacturers m
 WHERE p.products_status = 1
   AND p.manufacturers_id = m.manufacturers_id
   AND p.products_id = p2c.products_id
   AND p2c.categories_id = 
EOSQL
          . (int)$current_category_id . " ORDER BY m.manufacturers_name";
        } else {
          $filterlist_sql = <<<'EOSQL'
SELECT DISTINCT c.categories_id AS id, cd.categories_name AS name
 FROM products p, products_to_categories p2c, categories c, categories_description cd
 WHERE p.products_status = 1
   AND p.products_id = p2c.products_id
   AND p2c.categories_id = c.categories_id
   AND p2c.categories_id = cd.categories_id
   AND cd.language_id = 
EOSQL
          . (int)$languages_id . " AND p.manufacturers_id = " . (int)$_GET['manufacturers_id']
          . " ORDER BY cd.categories_name";
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
