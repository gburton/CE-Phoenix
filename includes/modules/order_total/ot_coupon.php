<?php
/*
osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com/
Copyright - 2014 osCommerce
Released under the GNU General Public License
*/
class ot_coupon {
var $title, $output;
function ot_coupon() {
  $this->code = 'ot_coupon';
/* ** Altered by Raymond Burns because of improper render on front page in Bootstrap. Needs more adjustment **
  $this->header = MODULE_ORDER_TOTAL_COUPON_HEADER;
*/  
  $this->title = MODULE_ORDER_TOTAL_COUPON_TITLE;
  $this->description = MODULE_ORDER_TOTAL_COUPON_DESCRIPTION;
  $this->user_prompt = '';
  $this->enabled = MODULE_ORDER_TOTAL_COUPON_STATUS;
  $this->sort_order = MODULE_ORDER_TOTAL_COUPON_SORT_ORDER;
  $this->include_shipping = MODULE_ORDER_TOTAL_COUPON_INC_SHIPPING;
  $this->include_tax = MODULE_ORDER_TOTAL_COUPON_INC_TAX;
  $this->calculate_tax = MODULE_ORDER_TOTAL_COUPON_CALC_TAX;
  $this->tax_class = MODULE_ORDER_TOTAL_COUPON_TAX_CLASS;
  $this->credit_class = true;
  $this->output = array();
}
function process() {
global $PHP_SELF, $order, $currencies;
  $order_total=$this->get_order_total();
  $od_amount = $this->calculate_credit($order_total);
  $tod_amount = 0.0;
  $this->deduction = $od_amount;
  if ($this->calculate_tax != 'None') {
    $tod_amount = $this->calculate_tax_deduction($order_total, $this->deduction, $this->calculate_tax);
  }
  if ($od_amount > 0) {
    // fix tax not correctly calculated bug (tax deducted twice if display tax is true)
               if (DISPLAY_PRICE_WITH_TAX == 'true') {
                   $order->info['total'] = $order->info['total'] - $od_amount;
                   } else {
                   $order->info['total'] = $order->info['total'] - $od_amount - $tod_amount;
                   }
                   $this->output[] = array('title' => $this->title . ':' . $this->coupon_code .':',
                                           'text' => '-' . $currencies->format($od_amount) . '',
                                           'value' => $od_amount);
  }
}
function selection_test() {
  return false;
}
function pre_confirmation_check($order_total) {
global $customer_id;
  return $this->calculate_credit($order_total);
}
function use_credit_amount() {
  return $output_string;
}
function credit_selection() {
global $customer_id, $currencies, $language;
  $selection_string = '';
  $selection_string .= '<tr>' . "\n";
  //$selection_string .= ' <td width="10"></td>';
  $selection_string .= ' <td class="main">' . "\n";
  $selection_string .= TEXT_ENTER_COUPON_CODE . '<br>' . tep_draw_input_field('gv_redeem_code') . '</td>';
  $selection_string .= ' <td align="right"> </td>';
  //$selection_string .= ' <td width="10"></td>';
  $selection_string .= '</tr>' . "\n";
  return $selection_string;
}
function collect_posts() {
global $_POST, $customer_id, $currencies, $cc_id;
  if ($_POST['gv_redeem_code']) {
// get some info from the coupon table
  $coupon_query=tep_db_query("select coupon_id, coupon_amount, coupon_type, coupon_minimum_order,uses_per_coupon, uses_per_user, restrict_to_products,restrict_to_categories from " . TABLE_COUPONS . " where coupon_code='".$_POST['gv_redeem_code']."' and coupon_active='Y' and coupon_status = '1'");
  $coupon_result=tep_db_fetch_array($coupon_query);
  if ($coupon_result['coupon_type'] != 'G') {
    if (tep_db_num_rows($coupon_query)==0) {
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(ERROR_NO_INVALID_REDEEM_COUPON), 'SSL'));
    }
    $date_query=tep_db_query("select coupon_start_date from " . TABLE_COUPONS . " where coupon_start_date <= now() and coupon_code='".$_POST['gv_redeem_code']."'");
    if (tep_db_num_rows($date_query)==0) {
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(ERROR_INVALID_STARTDATE_COUPON), 'SSL'));
    }
    $date_query=tep_db_query("select coupon_expire_date from " . TABLE_COUPONS . " where coupon_expire_date >= now() and coupon_code='".$_POST['gv_redeem_code']."'");
    if (tep_db_num_rows($date_query)==0) {
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(ERROR_INVALID_FINISDATE_COUPON), 'SSL'));
    }
    $coupon_count = tep_db_query("select coupon_id from " . TABLE_COUPON_REDEEM_TRACK . " where coupon_id = '" . $coupon_result['coupon_id']."'");
    $coupon_count_customer = tep_db_query("select coupon_id from " . TABLE_COUPON_REDEEM_TRACK . " where coupon_id = '" . $coupon_result['coupon_id']."' and customer_id = '" . $customer_id . "'");
    if (tep_db_num_rows($coupon_count)>=$coupon_result['uses_per_coupon'] && $coupon_result['uses_per_coupon'] > 0) {
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(ERROR_INVALID_USES_COUPON . $coupon_result['uses_per_coupon'] . TIMES ), 'SSL'));
    }
    if (tep_db_num_rows($coupon_count_customer)>=$coupon_result['uses_per_user'] && $coupon_result['uses_per_user'] > 0) {
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(ERROR_INVALID_USES_USER_COUPON . $coupon_result['uses_per_user'] . TIMES ), 'SSL'));
    }
    if ($coupon_result['coupon_type']=='S') {
      $coupon_amount = $order->info['shipping_cost'];
    } else {
      $coupon_amount = $currencies->format($coupon_result['coupon_amount']) . ' ';
    }
    if ($coupon_result['coupon_type']=='P') $coupon_amount = $coupon_result['coupon_amount'] . '% ';
    if ($coupon_result['coupon_minimum_order']>0) $coupon_amount .= 'on orders greater than ' . $coupon_result['coupon_minimum_order'];
    if (!tep_session_is_registered('cc_id')) tep_session_register('cc_id');
    $cc_id = $coupon_result['coupon_id'];
  }
  if ($_POST['submit_redeem_coupon_x'] && !$_POST['gv_redeem_code']) tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(ERROR_NO_REDEEM_CODE), 'SSL'));
  }
}
function calculate_credit($amount) {
global $customer_id, $order, $cc_id;
  $od_amount = 0;
  if (isset($cc_id) ) {
    $coupon_query = tep_db_query("select coupon_code from " . TABLE_COUPONS . " where coupon_id = '" . $cc_id . "'");
    if (tep_db_num_rows($coupon_query) !=0 ) {
      $coupon_result = tep_db_fetch_array($coupon_query);
      $this->coupon_code = $coupon_result['coupon_code'];
      $coupon_get = tep_db_query("select coupon_amount, coupon_minimum_order, restrict_to_products, restrict_to_categories, coupon_type from " . TABLE_COUPONS ." where coupon_code = '". $coupon_result['coupon_code'] . "'");
      $get_result = tep_db_fetch_array($coupon_get);
      $c_deduct = $get_result['coupon_amount'];
      if ($get_result['coupon_type']=='S') $c_deduct = $order->info['shipping_cost'];
      if ($get_result['coupon_minimum_order'] <= $this->get_order_total()) {
        if ($get_result['restrict_to_products'] || $get_result['restrict_to_categories']) {
          for ($i=0; $i<sizeof($order->products); $i++) {
            if ($get_result['restrict_to_products']) {
              $pr_ids = preg_split("[,]", $get_result['restrict_to_products']);
              for ($ii = 0; $ii < count($pr_ids); $ii++) {
                if ($pr_ids[$ii] == tep_get_prid($order->products[$i]['id'])) {
                  if ($get_result['coupon_type'] == 'P') {
                      $pr_c = $this->product_price($order->products[$i]['id']);
                      $pod_amount = round($pr_c*10)/10*$c_deduct/100;
                      $od_amount = $od_amount + $pod_amount;
                    } else {
                      $od_amount = $c_deduct;
                    }
                  }
                }
              } else {
                $cat_ids = preg_split("[,]", $get_result['restrict_to_categories']);
                for ($i=0; $i<sizeof($order->products); $i++) {
                  $my_path = tep_get_product_path(tep_get_prid($order->products[$i]['id']));
                  $sub_cat_ids = preg_split("[_]", $my_path);
                  for ($iii = 0; $iii < count($sub_cat_ids); $iii++) {
                    for ($ii = 0; $ii < count($cat_ids); $ii++) {
                      if ($sub_cat_ids[$iii] == $cat_ids[$ii]) {
                        if ($get_result['coupon_type'] == 'P') {
                          $pr_c = $this->product_price($order->products[$i]['id']);
                          $pod_amount = round($pr_c*10)/10*$c_deduct/100;
                          $od_amount = $od_amount + $pod_amount;
                        } else {
                          $od_amount = $c_deduct;
                        }
                      }
                    }
                  }
                }
              }
            }
          } else {
            if ($get_result['coupon_type'] !='P') {
              $od_amount = $c_deduct;
            } else {
              $od_amount = $amount * $get_result['coupon_amount'] / 100;
            }
          }
        }
      }
    if ($od_amount>$amount) $od_amount = $amount;
    }
  return $od_amount;
}
function calculate_tax_deduction($amount, $od_amount, $method) {
global $customer_id, $order, $cc_id, $cart;
  $coupon_query = tep_db_query("select coupon_code from " . TABLE_COUPONS . " where coupon_id = '" . $cc_id . "'");
  if (tep_db_num_rows($coupon_query) !=0 ) {
    $coupon_result = tep_db_fetch_array($coupon_query);
    $coupon_get = tep_db_query("select coupon_amount, coupon_minimum_order, restrict_to_products, restrict_to_categories, coupon_type from " . TABLE_COUPONS . " where coupon_code = '". $coupon_result['coupon_code'] . "'");
    $get_result = tep_db_fetch_array($coupon_get);
    if ($get_result['coupon_type'] != 'S') {
      //PRODUCT / CATEGORY RESTRICTION
      if ($get_result['restrict_to_products'] || $get_result['restrict_to_categories']) {
        // What to do here.
        // Loop through all products and build a list of all product_ids, price, tax class
        // at the same time create total net amount.
        // then for percentage discounts. simply reduce tax group per product by discount percentage
        // or for fixed payment amount calculate ratio based on total net for each product reduce tax group
        // per product by ratio amount.
        $products = $cart->get_products();
        $valid_product = false;
        for ($i=0; $i<sizeof($products); $i++) {
        $valid_product = false;
          $t_prid = tep_get_prid($products[$i]['id']);
          $cc_query = tep_db_query("select products_tax_class_id from " . TABLE_PRODUCTS . " where products_id = '" . $t_prid . "'");
          $cc_result = tep_db_fetch_array($cc_query);
          if ($get_result['restrict_to_products']) {
            $pr_ids = preg_split("[,]", $get_result['restrict_to_products']);
            for ($p = 0; $p < sizeof($pr_ids); $p++) {
              if ($pr_ids[$p] == $t_prid) $valid_product = true;
            }
          }
          if ($get_result['restrict_to_categories']) {
            $cat_ids = preg_split("[,]", $get_result['restrict_to_categories']);
            for ($c = 0; $c < sizeof($cat_ids); $c++) {
              $cat_query = tep_db_query("select products_id from products_to_categories where products_id = '" . $products_id . "' and categories_id = '" . $cat_ids[$i] . "'");
              if (tep_db_num_rows($cat_query) !=0 ) $valid_product = true;
            }
          }
          if ($valid_product) {
            $price_excl_vat = $products[$i]['final_price'] * $products[$i]['quantity'];
            $price_incl_vat = $this->product_price($t_prid);
            $valid_array[] = array('product_id' => $t_prid, 'products_price' => $price_excl_vat, 'products_tax_class' => $cc_result['products_tax_class_id']);
            $total_price += $price_excl_vat;
          }
        }
        if (sizeof($valid_array) > 0) {
          if ($get_result['coupon_type'] == 'P') {
            $ratio = $get_result['coupon_amount']/100;
          } else {
            $ratio = $od_amount / $total_price;
          }
          if ($get_result['coupon_type'] == 'S') $ratio = 1;
          if ($method=='Credit Note') {
            $tax_rate = tep_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
            $tax_desc = tep_get_tax_description($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
            if ($get_result['coupon_type'] == 'P') {
              $tod_amount = $od_amount / (100 + $tax_rate)* $tax_rate;
            } else {
              $tod_amount = $order->info['tax_groups'][$tax_desc] * $od_amount/100;
            }
            $order->info['tax_groups'][$tax_desc] -= $tod_amount;
            $order->info['tax'] -= $tod_amount;
          } else {
            for ($p=0; $p<sizeof($valid_array); $p++) {
              $tax_rate = tep_get_tax_rate($valid_array[$p]['products_tax_class'], $order->delivery['country']['id'], $order->delivery['zone_id']);
              $tax_desc = tep_get_tax_description($valid_array[$p]['products_tax_class'], $order->delivery['country']['id'], $order->delivery['zone_id']);
              if ($tax_rate > 0) {
                $tod_amount = ($valid_array[$p]['products_price'] * $tax_rate)/100 * $ratio;
                $order->info['tax_groups'][$tax_desc] -= ($valid_array[$p]['products_price'] * $tax_rate)/100 * $ratio;
                $order->info['tax'] -= ($valid_array[$p]['products_price'] * $tax_rate)/100 * $ratio;
              }
            }
          }
        }
        //NO RESTRICTION--------------------------------
      } else {
        if ($get_result['coupon_type'] =='F') {
          $tod_amount = 0;
          if ($method=='Credit Note') {
            $tax_rate = tep_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
            $tax_desc = tep_get_tax_description($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
            $tod_amount = $od_amount / (100 + $tax_rate)* $tax_rate;
            $order->info['tax_groups'][$tax_desc] -= $tod_amount;
          } else {
            reset($order->info['tax_groups']);
            while (list($key, $value) = each($order->info['tax_groups'])) {
              $ratio1 = $od_amount/($amount-$order->info['tax_groups'][$key]);
              $tax_rate = tep_get_tax_rate_from_desc($key);
              $net = $tax_rate * $order->info['tax_groups'][$key];
              if ($net>0) {
                $god_amount = $order->info['tax_groups'][$key] * $ratio1;
                $tod_amount += $god_amount;
                $order->info['tax_groups'][$key] = $order->info['tax_groups'][$key] - $god_amount;
              }
            }
          }
          $order->info['tax'] -= $tod_amount;
      }
      if ($get_result['coupon_type'] =='P') {
        $tod_amount=0;
        if ($method=='Credit Note') {
          $tax_desc = tep_get_tax_description($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
          $tod_amount = $order->info['tax_groups'][$tax_desc] * $od_amount/100;
          $order->info['tax_groups'][$tax_desc] -= $tod_amount;
        } else {
          reset($order->info['tax_groups']);
          while (list($key, $value) = each($order->info['tax_groups'])) {
            $god_amount = 0;
            $tax_rate = tep_get_tax_rate_from_desc($key);
            $net = $tax_rate * $order->info['tax_groups'][$key];
            if ($net>0) {
              $god_amount = $order->info['tax_groups'][$key] * $get_result['coupon_amount']/100;
              $tod_amount += $god_amount;
              $order->info['tax_groups'][$key] = $order->info['tax_groups'][$key] - $god_amount;
            }
          }
        }
        $order->info['tax'] -= $tod_amount;
      }
    }
  }
}
return $tod_amount;
}
function update_credit_account($i) {
  return false;
}
function apply_credit() {
global $insert_id, $customer_id, $REMOTE_ADDR, $cc_id;
  if ($this->deduction !=0) {
    tep_db_query("insert into " . TABLE_COUPON_REDEEM_TRACK . " (coupon_id, redeem_date, redeem_ip, customer_id, order_id) values ('" . $cc_id . "', now(), '" . $REMOTE_ADDR . "', '" . $customer_id . "', '" . $insert_id . "')");
  }
  tep_session_unregister('cc_id');
}
function get_order_total() {
global $order, $cart, $customer_id, $cc_id;
  $order_total = $order->info['total'];
  $products = $cart->get_products();
  for ($i=0; $i<sizeof($products); $i++) {
    $t_prid = tep_get_prid($products[$i]['id']);
    $gv_query = tep_db_query("select products_price, products_tax_class_id, products_model from " . TABLE_PRODUCTS . " where products_id = '" . $t_prid . "'");
    $gv_result = tep_db_fetch_array($gv_query);
    if (preg_match('/^GIFT/', addslashes($gv_result['products_model']))) {
      $qty = $cart->get_quantity($t_prid);
      $products_tax = tep_get_tax_rate($gv_result['products_tax_class_id']);
      if ($this->include_tax =='false') {
        $gv_amount = $gv_result['products_price'] * $qty;
      } else {
        $gv_amount = ($gv_result['products_price'] + tep_calculate_tax($gv_result['products_price'],$products_tax)) * $qty;
      }
      $order_total=$order_total - $gv_amount;
    }
  }
  if ($this->include_tax == 'false') $order_total=$order_total-$order->info['tax'];
  if ($this->include_shipping == 'false') $order_total=$order_total-$order->info['shipping_cost'];
  $coupon_query=tep_db_query("select coupon_code from " . TABLE_COUPONS . " where coupon_id='".$cc_id."'");
  if (tep_db_num_rows($coupon_query) !=0) {
    $coupon_result=tep_db_fetch_array($coupon_query);
    $coupon_get=tep_db_query("select coupon_amount, coupon_minimum_order,restrict_to_products,restrict_to_categories, coupon_type from " . TABLE_COUPONS . " where coupon_code='".$coupon_result['coupon_code']."'");
    $get_result=tep_db_fetch_array($coupon_get);
    $in_cat = true;
    if ($get_result['restrict_to_categories']) {
      $cat_ids = preg_split("[,]", $get_result['restrict_to_categories']);
      $in_cat=false;
      for ($i = 0; $i < count($cat_ids); $i++) {
        if (is_array($this->contents)) {
          reset($this->contents);
          while (list($products_id, ) = each($this->contents)) {
            $cat_query = tep_db_query("select products_id from products_to_categories where products_id = '" . $products_id . "' and categories_id = '" . $cat_ids[$i] . "'");
            if (tep_db_num_rows($cat_query) !=0 ) {
              $in_cat = true;
              $total_price += $this->get_product_price($products_id);
            }
          }
        }
      }
    }
    $in_cart = true;
    if ($get_result['restrict_to_products']) {
      $pr_ids = preg_split("[,]", $get_result['restrict_to_products']);
      $in_cart=false;
      $products_array = $cart->get_products();
      for ($i = 0; $i < sizeof($pr_ids); $i++) {
        for ($ii = 1; $ii<=sizeof($products_array); $ii++) {
          if (tep_get_prid($products_array[$ii-1]['id']) == $pr_ids[$i]) {
            $in_cart=true;
            $total_price += $this->get_product_price($products_array[$ii-1]['id']);
          }
        }
      }
      $order_total = $total_price;
    }
  }
return $order_total;
}
function get_product_price($product_id) {
global $cart, $order;
  $products_id = tep_get_prid($product_id);
  // products price
  $qty = $cart->contents[$product_id]['qty'];
  $product_query = tep_db_query("select products_id, products_price, products_tax_class_id, products_weight from " . TABLE_PRODUCTS . " where products_id='" . $product_id . "'");
  if ($product = tep_db_fetch_array($product_query)) {
    $prid = $product['products_id'];
    $products_tax = tep_get_tax_rate($product['products_tax_class_id']);
    $products_price = $product['products_price'];
    $specials_query = tep_db_query("select specials_new_products_price from " . TABLE_SPECIALS . " where products_id = '" . $prid . "' and status = '1'");
    if (tep_db_num_rows ($specials_query)) {
      $specials = tep_db_fetch_array($specials_query);
      $products_price = $specials['specials_new_products_price'];
    }
    if ($this->include_tax == 'true') {
      $total_price += ($products_price + tep_calculate_tax($products_price, $products_tax)) * $qty;
    } else {
      $total_price += $products_price * $qty;
    }
    // attributes price
    if (isset($cart->contents[$product_id]['attributes'])) {
      reset($cart->contents[$product_id]['attributes']);
      while (list($option, $value) = each($cart->contents[$product_id]['attributes'])) {
        $attribute_price_query = tep_db_query("select options_values_price, price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . $prid . "' and options_id = '" . $option . "' and options_values_id = '" . $value . "'");
        $attribute_price = tep_db_fetch_array($attribute_price_query);
        if ($attribute_price['price_prefix'] == '+') {
          if ($this->include_tax == 'true') {
            $total_price += $qty * ($attribute_price['options_values_price'] + tep_calculate_tax($attribute_price['options_values_price'], $products_tax));
          } else {
            $total_price += $qty * ($attribute_price['options_values_price']);
          }
        } else {
          if ($this->include_tax == 'true') {
            $total_price -= $qty * ($attribute_price['options_values_price'] + tep_calculate_tax($attribute_price['options_values_price'], $products_tax));
          } else {
            $total_price -= $qty * ($attribute_price['options_values_price']);
          }
        }
      }
    }
  }
  if ($this->include_shipping == 'true') {
    $total_price += $order->info['shipping_cost'];
  }
  return $total_price;
}
function product_price($product_id) {
  $total_price = $this->get_product_price($product_id);
  if ($this->include_shipping == 'true') $total_price -= $order->info['shipping_cost'];
  return $total_price;
}
function check() {
  if (!isset($this->check)) {
    $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_COUPON_STATUS'");
    $this->check = tep_db_num_rows($check_query);
  }
  return $this->check;
}
function keys() {
  return array('MODULE_ORDER_TOTAL_COUPON_STATUS', 'MODULE_ORDER_TOTAL_COUPON_SORT_ORDER', 'MODULE_ORDER_TOTAL_COUPON_INC_SHIPPING', 'MODULE_ORDER_TOTAL_COUPON_INC_TAX', 'MODULE_ORDER_TOTAL_COUPON_CALC_TAX', 'MODULE_ORDER_TOTAL_COUPON_TAX_CLASS');
}
function install() {
  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display Total', 'MODULE_ORDER_TOTAL_COUPON_STATUS', 'true', 'Do you want to display the Discount Coupon value?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ORDER_TOTAL_COUPON_SORT_ORDER', '3', 'Sort order of display.', '6', '2', now())");
  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function ,date_added) values ('Include Shipping', 'MODULE_ORDER_TOTAL_COUPON_INC_SHIPPING', 'true', 'Include Shipping in calculation', '6', '5', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function ,date_added) values ('Include Tax', 'MODULE_ORDER_TOTAL_COUPON_INC_TAX', 'true', 'Include Tax in calculation.', '6', '6','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function ,date_added) values ('Re-calculate Tax', 'MODULE_ORDER_TOTAL_COUPON_CALC_TAX', 'Standard', 'Re-Calculate Tax', '6', '7','tep_cfg_select_option(array(\'None\', \'Standard\', \'Credit Note\'), ', now())");
  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Tax Class', 'MODULE_ORDER_TOTAL_COUPON_TAX_CLASS', '0', 'Use the following tax class when treating Discount Coupon as Credit Note.', '6', '0', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now())");
}
    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }
}
?>