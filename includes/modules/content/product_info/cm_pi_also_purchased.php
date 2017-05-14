<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2017 osCommerce

  Released under the GNU General Public License
*/

  class cm_pi_also_purchased {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_TITLE;
      $this->description = MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_DESCRIPTION;
      $this->description .= '<div class="secWarning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';

      if ( defined('MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate, $languages_id;
      
      $content_width = (int)MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_CONTENT_WIDTH;
      $product_width = (int)MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_DISPLAY_EACH;
      
      $orders_query = tep_db_query("select p.products_id, p.products_image, pd.products_name from orders_products opa, orders_products opb, orders o, products p LEFT JOIN products_description pd on p.products_id = pd.products_id where opa.products_id = '" . (int)$_GET['products_id'] . "' and opa.orders_id = opb.orders_id and opb.products_id != '" . (int)$_GET['products_id'] . "' and opb.products_id = p.products_id and opb.orders_id = o.orders_id and p.products_status = '1' and pd.language_id = '" . (int)$languages_id . "' group by p.products_id order by o.date_purchased desc limit " . (int)MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_CONTENT_LIMIT);
      $num_products_ordered = tep_db_num_rows($orders_query);
      
      if ($num_products_ordered > 0) {
        ob_start();
        include('includes/modules/content/' . $this->group . '/templates/also_purchased.php');
        $template = ob_get_clean();

        $oscTemplate->addContent($template, $this->group);
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Reviews Module', 'MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_STATUS', 'True', 'Should the Also Purchased block be shown on the product info page?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_CONTENT_WIDTH', '12', 'What width container should the content be shown in?', '6', '1', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Number of Products', 'MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_CONTENT_LIMIT', '4', 'How many products (maximum) should be shown?', '6', '1', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Product Width', 'MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_DISPLAY_EACH', '3', 'What width container should each product be shown in? (12 = full width, 6 = half width).', '6', '4', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_STATUS', 'MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_CONTENT_WIDTH', 'MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_CONTENT_LIMIT', 'MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_DISPLAY_EACH', 'MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_SORT_ORDER');
    }
  }
  