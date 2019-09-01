<?php
/*
  2018 QTPro 6.0 Phoenix
  by @raiwa 
  info@oscaddons.com
  www.oscaddons.com

  Copyright (c) 2019 Rainer Schmied
  All rights reserved.

  Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

  1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

  2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

  3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

class hook_shop_siteWide_qtPro {

  function listen_injectRedirects() {

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

}
