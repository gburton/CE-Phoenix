<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2016 osCommerce

  Released under the GNU General Public License
*/

  class cm_in_new_products {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_IN_NEW_PRODUCTS_TITLE;
      $this->description = MODULE_CONTENT_IN_NEW_PRODUCTS_DESCRIPTION;
      $this->description .= '<div class="secWarning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';

      if ( defined('MODULE_CONTENT_IN_NEW_PRODUCTS_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_IN_NEW_PRODUCTS_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_IN_NEW_PRODUCTS_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate, $current_category_id, $languages_id, $currencies, $PHP_SELF;
      
      $content_width = MODULE_CONTENT_IN_NEW_PRODUCTS_CONTENT_WIDTH;
      $product_width = MODULE_CONTENT_IN_NEW_PRODUCTS_DISPLAY_EACH;
     
      $new_products_query = tep_db_query("select distinct p.products_id, p.products_image, p.products_tax_class_id, pd.products_name, if(s.status, s.specials_new_products_price, p.products_price) as products_price from products p left join specials s on p.products_id = s.products_id, products_description pd, products_to_categories p2c, categories c where p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and c.parent_id = '" . (int)$current_category_id . "' and p.products_status = '1' and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' order by p.products_date_added desc limit " . (int)MODULE_CONTENT_IN_NEW_PRODUCTS_MAX_DISPLAY);
      $num_new_products = tep_db_num_rows($new_products_query);

      if ($num_new_products > 0) {
        ob_start();
        include(DIR_WS_MODULES . 'content/' . $this->group . '/templates/category_new_products.php');
        $template = ob_get_clean(); 
        
        $oscTemplate->addContent($template, $this->group);
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_IN_NEW_PRODUCTS_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable New Products Module', 'MODULE_CONTENT_IN_NEW_PRODUCTS_STATUS', 'True', 'Do you want to enable this module?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_CONTENT_IN_NEW_PRODUCTS_CONTENT_WIDTH', '12', 'What width container should the content be shown in? (12 = full width, 6 = half width).', '6', '2', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Maximum Display', 'MODULE_CONTENT_IN_NEW_PRODUCTS_MAX_DISPLAY', '6', 'Maximum Number of products that should show in this module?', '6', '3', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Product Width', 'MODULE_CONTENT_IN_NEW_PRODUCTS_DISPLAY_EACH', '3', 'What width container should each product be shown in? (12 = full width, 6 = half width).', '6', '4', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_IN_NEW_PRODUCTS_SORT_ORDER', '300', 'Sort order of display. Lowest is displayed first.', '6', '5', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_CONTENT_IN_NEW_PRODUCTS_STATUS', 'MODULE_CONTENT_IN_NEW_PRODUCTS_CONTENT_WIDTH', 'MODULE_CONTENT_IN_NEW_PRODUCTS_MAX_DISPLAY', 'MODULE_CONTENT_IN_NEW_PRODUCTS_DISPLAY_EACH', 'MODULE_CONTENT_IN_NEW_PRODUCTS_SORT_ORDER');
    }
  }
  