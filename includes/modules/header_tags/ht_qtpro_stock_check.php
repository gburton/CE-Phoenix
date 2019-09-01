<?php
/*
  $Id: qtpro_stock_check.php
  $Loc: catalog/includes/header_tags/

  2017 QTPro 5.6 BS
  by @raiwa 
  info@oscaddons.com
  www.oscaddons.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  class ht_qtpro_stock_check {
    var $code = 'ht_qtpro_stock_check';
    var $group = 'header_tags';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->title = MODULE_HEADER_TAGS_QTPRO_STOCK_CHECK_TITLE;
      $this->description = MODULE_HEADER_TAGS_QTPRO_STOCK_CHECK_DESCRIPTION;
      if (!defined('MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_STATUS')) {
        $this->description .=   '<div class="secWarning">' . MODULE_HEADER_TAGS_QTPRO_STOCK_CHECK_OPTIONS_WARNING . '<br>
                                <a href="modules_content.php?module=cm_pi_qtpro_options&action=install">' . MODULE_HEADER_TAGS_QTPRO_STOCK_CHECK_OPTIONS_INSTALL_NOW . '</a></div>';
      }

      if ( defined('MODULE_HEADER_TAGS_QTPRO_STOCK_CHECK_STATUS') ) {
        $this->sort_order = MODULE_HEADER_TAGS_QTPRO_STOCK_CHECK_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_QTPRO_STOCK_CHECK_STATUS == 'True');
      }
    }

    function execute() {
      global $PHP_SELF, $cart, $order;

      if (basename($PHP_SELF) == 'checkout_payment.php') {

        if ( (STOCK_CHECK == 'true') && (STOCK_ALLOW_CHECKOUT != 'true') ) {
          $products = $cart->get_products();
          $any_out_of_stock = 0;
          for ($i=0, $n=sizeof($products); $i<$n; $i++) {
            if (isset($products[$i]['attributes']) && is_array($products[$i]['attributes'])) {
              $stock_check = $this->check_stock_qtpro($products[$i]['id'], $products[$i]['quantity'], $products[$i]['attributes']);
            } else {
              $stock_check = $this->check_stock_qtpro($products[$i]['id'], $products[$i]['quantity']);
            }
            if ($stock_check) $any_out_of_stock = 1;
          }
          if ($any_out_of_stock == 1) {
            tep_redirect(tep_href_link('shopping_cart.php'));
          }
        }
      } elseif (basename($PHP_SELF) == 'checkout_confirmation.php') {
        
        $any_out_of_stock = false;
        if (STOCK_CHECK == 'true') {
          $check_stock = array();
          for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
            if (isset($order->products[$i]['attributes']) && is_array($order->products[$i]['attributes'])) {
              $attributes = array();
              foreach ($order->products[$i]['attributes'] as $attribute) {
                $attributes[$attribute['option_id']]=$attribute['value_id'];
              }
              $check_stock[$i] = $this->check_stock_qtpro($order->products[$i]['id'], $order->products[$i]['qty'], $attributes);
            } else {
              $check_stock[$i] = $this->check_stock_qtpro($order->products[$i]['id'], $order->products[$i]['qty']);
            }
            if ($check_stock[$i]) {
              $any_out_of_stock = true;
            }
          }    // Out of Stock
          if ( (STOCK_ALLOW_CHECKOUT != 'true') && ($any_out_of_stock == true) ) {
            tep_redirect(tep_href_link('shopping_cart.php'));
          }
        }
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_HEADER_TAGS_QTPRO_STOCK_CHECK_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable qtpro stock check Module', 'MODULE_HEADER_TAGS_QTPRO_STOCK_CHECK_STATUS', 'True', 'Do you want to add qtpro stock check to checkout pages?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_HEADER_TAGS_QTPRO_STOCK_CHECK_SORT_ORDER', '2000', 'Sort order of display. Lowest is displayed first.', '6', '2', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_HEADER_TAGS_QTPRO_STOCK_CHECK_STATUS', 'MODULE_HEADER_TAGS_QTPRO_STOCK_CHECK_SORT_ORDER');
    }

    ////
    // Check if the required stock is available
    // If insufficent stock is available return $out_of_stock = true
    private function check_stock_qtpro($products_id, $products_quantity, $attributes=array()) {
      $stock_left = $this->get_products_stock_qtpro($products_id, $attributes) - $products_quantity;
      $out_of_stock = '';

      if ($stock_left < 0) {
        $out_of_stock = '<span class="text-danger"><b>' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</b></span>';
      }

      return $out_of_stock;
    }
  
    ////
    // Return a product's stock
    // TABLES: products. products_stock
    private function get_products_stock_qtpro($products_id, $attributes=array()) {
      global $languages_id;
      $products_id = tep_get_prid($products_id);
      $all_nonstocked = true;
      if (sizeof($attributes) > 0) {
        $attr_list='';
        $options_list=implode(",",array_keys($attributes));
        $track_stock_query=tep_db_query("select products_options_id, products_options_track_stock from products_options where products_options_id in ($options_list) and language_id= '" . (int)$languages_id . "order by products_options_id'");
        while ($track_stock_array = tep_db_fetch_array($track_stock_query)) {
          if ($track_stock_array['products_options_track_stock']) {
            $attr_list .= $track_stock_array['products_options_id'] . '-' . $attributes[$track_stock_array['products_options_id']] . ',';
            $all_nonstocked=false;
          }
        }
        $attr_list=substr($attr_list,0,strlen($attr_list)-1);
      }
    
      if ( (sizeof($attributes) == 0) || ($all_nonstocked)) {
        $stock_query = tep_db_query("select products_quantity as quantity from products where products_id = '" . (int)$products_id . "'");
      } else {
        $stock_query=tep_db_query("select products_stock_quantity as quantity from products_stock where products_id='". (int)$products_id . "' and products_stock_attributes='$attr_list'");
      }
      if (tep_db_num_rows($stock_query)>0) {
        $stock=tep_db_fetch_array($stock_query);
        $quantity=$stock['quantity'];
      } else {
        $quantity = 0;
      }
      return $quantity;
    }

  } // end class
