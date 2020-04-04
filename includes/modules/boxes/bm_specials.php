<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class bm_specials {
    var $code = 'bm_specials';
    var $group = 'boxes';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->title = MODULE_BOXES_SPECIALS_TITLE;
      $this->description = MODULE_BOXES_SPECIALS_DESCRIPTION;

      if ( defined('MODULE_BOXES_SPECIALS_STATUS') ) {
        $this->sort_order = MODULE_BOXES_SPECIALS_SORT_ORDER;
        $this->enabled = (MODULE_BOXES_SPECIALS_STATUS == 'True');

        $this->group = ((MODULE_BOXES_SPECIALS_CONTENT_PLACEMENT == 'Left Column') ? 'boxes_column_left' : 'boxes_column_right');
      }
    }

    function execute() {
      global $currencies, $oscTemplate;

      if ($random_product = tep_random_select("select p.*, pd.*, s.*, p.products_quantity as in_stock, s.specials_new_products_price as final_price from products p, products_description pd, specials s where p.products_status = '1' and p.products_id = s.products_id and pd.products_id = s.products_id and pd.language_id = '" . (int)$_SESSION['languages_id'] . "' and s.status = '1' order by s.specials_date_added desc limit " . MODULE_BOXES_SPECIALS_MAX_RANDOM_SELECT_SPECIALS)) {
        $data['data-is-special'] = 1;
        $data['data-product-price'] = $currencies->display_raw($random_product['final_price'], tep_get_tax_rate($random_product['products_tax_class_id']));
        $data['data-product-manufacturer'] = max(0, (int)$random_product['manufacturers_id']);

        // data attributes
        $box_attr = '';
        foreach ( $data as $key => $value ) {
          $box_attr .= ' ' . tep_output_string_protected($key) . '="' . tep_output_string_protected($value) . '"';
        }
        // product title
        $box_title = '<a href="' . tep_href_link('product_info.php', 'products_id=' . (int)$random_product['products_id']) . '">' . $random_product['products_name'] . '</a>';
        // product image
        $box_image = '<a href="' . tep_href_link('product_info.php', 'products_id=' . $random_product['products_id']) . '">' . tep_image('images/' . $random_product['products_image'], htmlspecialchars($random_product['products_name']), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, '', true, 'card-img-top') . '</a>';
        // product price
        $box_price = sprintf(IS_PRODUCT_SHOW_PRICE_SPECIAL, $currencies->display_price($random_product['products_price'], tep_get_tax_rate($random_product['products_tax_class_id'])), $currencies->display_price($random_product['specials_new_products_price'], tep_get_tax_rate($random_product['products_tax_class_id'])));

        $tpl_data = ['group' => $this->group, 'file' => __FILE__];
        include 'includes/modules/block_template.php';
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_BOXES_SPECIALS_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Specials Module', 'MODULE_BOXES_SPECIALS_STATUS', 'True', 'Do you want to add the module to your shop?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Selection of Products on Special', 'MODULE_BOXES_SPECIALS_MAX_RANDOM_SELECT_SPECIALS', '10', 'How many records to select from to choose one random product special to display', '6', '2', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Placement', 'MODULE_BOXES_SPECIALS_CONTENT_PLACEMENT', 'Right Column', 'Should the module be loaded in the left or right column?', '6', '3', 'tep_cfg_select_option(array(\'Left Column\', \'Right Column\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_BOXES_SPECIALS_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '4', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_BOXES_SPECIALS_STATUS', 'MODULE_BOXES_SPECIALS_MAX_RANDOM_SELECT_SPECIALS', 'MODULE_BOXES_SPECIALS_CONTENT_PLACEMENT', 'MODULE_BOXES_SPECIALS_SORT_ORDER');
    }
  }
