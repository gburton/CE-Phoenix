<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce
    
  Edited by 2014 Newburns Design and Technology
  *************************************************
  ************ New addon definitions **************
  ************        Below          **************
  *************************************************
  Credit Class, Gift Vouchers & Discount Coupons osC2.3.3.4 (CCGV) added -- http://addons.oscommerce.com/info/9020

  Released under the GNU General Public License
*/

  class bm_shopping_cart {
    var $code = 'bm_shopping_cart';
    var $group = 'boxes';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function bm_shopping_cart() {
      $this->title = MODULE_BOXES_SHOPPING_CART_TITLE;
      $this->description = MODULE_BOXES_SHOPPING_CART_DESCRIPTION;

      if ( defined('MODULE_BOXES_SHOPPING_CART_STATUS') ) {
        $this->sort_order = MODULE_BOXES_SHOPPING_CART_SORT_ORDER;
        $this->enabled = (MODULE_BOXES_SHOPPING_CART_STATUS == 'True');

        $this->group = ((MODULE_BOXES_SHOPPING_CART_CONTENT_PLACEMENT == 'Left Column') ? 'boxes_column_left' : 'boxes_column_right');
      }
    }

    function execute() {
/* ** Altered for CCGV **
      global $cart, $new_products_id_in_cart, $currencies, $oscTemplate;
      $cart_contents_string = '';
*/
	if (tep_session_is_registered('customer_id'))
		{
		$gv_query = tep_db_query("select amount from " . TABLE_COUPON_GV_CUSTOMER . " where customer_id = '" . (int)$customer_id . "'");
		$gv_result = tep_db_fetch_array($gv_query);
		if ($gv_result['amount'] > 0 )
			{
			$gv_contents_string = '<div class="ui-widget-content infoBoxContents"><div style="float:left;">' . VOUCHER_BALANCE . '</div><div style="float:right;">' . $currencies->format($gv_result['amount']) . '</div><div style="clear:both;"></div>';
			$gv_contents_string .= '<div style="text-align:center; width:100%; margin:auto;"><a href="'. tep_href_link(FILENAME_GV_SEND) . '">' . BOX_SEND_TO_FRIEND . '</a></div></div>';
			}
		}
	if (tep_session_is_registered('gv_id'))
		{
		$gv_query = tep_db_query("select coupon_amount from " . TABLE_COUPONS . " where coupon_id = '" . $gv_id . "'");
		$coupon = tep_db_fetch_array($gv_query);
		$gv_contents_string = '<div style="text-align:center; width:100%; margin:auto;">' . VOUCHER_REDEEMED . '</td><td class="smalltext" align="right" valign="bottom">' . $currencies->format($coupon['coupon_amount']) . '</div>';
		}
	if (tep_session_is_registered('cc_id') && $cc_id)
		{
		$coupon_query = tep_db_query("select * from " . TABLE_COUPONS . " where coupon_id = '" . $cc_id . "'");
		$coupon = tep_db_fetch_array($coupon_query);
		$coupon_desc_query = tep_db_query("select * from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . $cc_id . "' and language_id = '" . $languages_id . "'");
		$coupon_desc = tep_db_fetch_array($coupon_desc_query);
		$text_coupon_help = sprintf("%s",$coupon_desc['coupon_name']);
		$gv_contents_string = '<div style="text-align:center; width:100%; margin:auto;">' . CART_COUPON . $text_coupon_help . '<br>' . '</div>';
		}
/* ** EOF alterations for CCGV ** */
      if ($cart->count_contents() > 0) {
        $cart_contents_string = NULL;
        $products = $cart->get_products();
        for ($i=0, $n=sizeof($products); $i<$n; $i++) {

          $cart_contents_string .= '<li';
          if ((tep_session_is_registered('new_products_id_in_cart')) && ($new_products_id_in_cart == $products[$i]['id'])) {
            $cart_contents_string .= ' class="newItemInCart"';
          }
          $cart_contents_string .= '>';

          $cart_contents_string .= $products[$i]['quantity'] . '&nbsp;x&nbsp;';

          $cart_contents_string .= '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products[$i]['id']) . '">';

          $cart_contents_string .= $products[$i]['name'];

          $cart_contents_string .= '</a></li>';

          if ((tep_session_is_registered('new_products_id_in_cart')) && ($new_products_id_in_cart == $products[$i]['id'])) {
            tep_session_unregister('new_products_id_in_cart');
          }
        }

        $cart_contents_string .= '<li class="text-right"><hr>' . $currencies->format($cart->show_total()) . '</li>';

      } else {
        $cart_contents_string .= '<p>' . MODULE_BOXES_SHOPPING_CART_BOX_CART_EMPTY . '</p>';
      }
              
      ob_start();
      include(DIR_WS_MODULES . 'boxes/templates/shopping_cart.php');
      $data = ob_get_clean();

      $oscTemplate->addBlock($data, $this->group);
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_BOXES_SHOPPING_CART_STATUS');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Shopping Cart Module', 'MODULE_BOXES_SHOPPING_CART_STATUS', 'True', 'Do you want to add the module to your shop?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Placement', 'MODULE_BOXES_SHOPPING_CART_CONTENT_PLACEMENT', 'Right Column', 'Should the module be loaded in the left or right column?', '6', '1', 'tep_cfg_select_option(array(\'Left Column\', \'Right Column\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_BOXES_SHOPPING_CART_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_BOXES_SHOPPING_CART_STATUS', 'MODULE_BOXES_SHOPPING_CART_CONTENT_PLACEMENT', 'MODULE_BOXES_SHOPPING_CART_SORT_ORDER');
    }
  }
?>
