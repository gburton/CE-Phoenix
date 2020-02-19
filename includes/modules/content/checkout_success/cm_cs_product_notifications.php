<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class cm_cs_product_notifications {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_TITLE;
      $this->description = MODULE_CONTENT_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_DESCRIPTION;

      if ( defined('MODULE_CONTENT_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate, $customer_id, $order_id;
      
      $content_width = MODULE_CONTENT_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_CONTENT_WIDTH;

      if ( tep_session_is_registered('customer_id') ) {
        $global_query = tep_db_query("select global_product_notifications from customers_info where customers_info_id = '" . (int)$customer_id . "'");
        $global = tep_db_fetch_array($global_query);

        if ( $global['global_product_notifications'] != '1' ) {
          if ( isset($_GET['action']) && ($_GET['action'] == 'update') ) {
            if ( isset($_POST['notify']) && is_array($_POST['notify']) && !empty($_POST['notify']) ) {
              $notify = array_unique($_POST['notify']);

              foreach ( $notify as $n ) {
                if ( is_numeric($n) && ($n > 0) ) {
                  $check_query = tep_db_query("select products_id from products_notifications where products_id = '" . (int)$n . "' and customers_id = '" . (int)$customer_id . "' limit 1");

                  if ( !tep_db_num_rows($check_query) ) {
                    tep_db_query("insert into products_notifications (products_id, customers_id, date_added) values ('" . (int)$n . "', '" . (int)$customer_id . "', now())");
                  }
                }
              }
            }
          }

          $products_displayed = array();
          
          $products_query = tep_db_query("select DISTINCT products_id, products_name from orders_products where orders_id = '" . (int)$order_id . "' order by products_name");
          while ($products = tep_db_fetch_array($products_query)) {
            
            if ( !isset($products_displayed[$products['products_id']]) ) {
              $checkbox = '<li class="list-group-item">';
                $checkbox .= '<div class="custom-control custom-switch">';
                $checkbox .= tep_draw_checkbox_field('notify[]', $products['products_id'], false, 'class="custom-control-input" id="notify_' . $products['products_id'] . '"');
                $checkbox .= '<label class="custom-control-label" for="notify_' . $products['products_id'] . '">' . $products['products_name'] . '</label></div>';
              $checkbox .= '</li>';
              
              $products_displayed[$products['products_id']] = $checkbox;
            }
          }

          $products_notifications = implode('', $products_displayed);

          ob_start();
          include('includes/modules/content/' . $this->group . '/templates/tpl_' . basename(__FILE__));
          $template = ob_get_clean();

          $oscTemplate->addContent($template, $this->group);
        }
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Product Notifications Module', 'MODULE_CONTENT_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_STATUS', 'True', 'Should the product notifications block be shown on the checkout success page?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_CONTENT_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_CONTENT_WIDTH', '5', 'What width container should the content be shown in? (12 = full width, 6 = half width).', '6', '2', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_SORT_ORDER', '1000', 'Sort order of display. Lowest is displayed first.', '6', '3', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_CONTENT_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_STATUS', 'MODULE_CONTENT_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_CONTENT_WIDTH', 'MODULE_CONTENT_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_SORT_ORDER');
    }
  }
  