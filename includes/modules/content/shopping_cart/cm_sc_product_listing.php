<?php
/*
  $Id$

  Copyright (c) 2020:
    Dan Cole - @Dan Cole
    James Keebaugh - @kymation
    Lambros - @Tsimi
    Rainer Schmied - @raiwa

  All rights reserved.

  Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

  1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

  2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

  3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

  class cm_sc_product_listing extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_SC_PRODUCT_LISTING_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      global $cart, $currencies, $languages_id, $any_out_of_stock;

      $content_width = (int)MODULE_CONTENT_SC_PRODUCT_LISTING_CONTENT_WIDTH;

      if ($cart->count_contents() > 0) {
        $any_out_of_stock = false;
        $products = $cart->get_products();
        $products_name = '';
        $products_field = '';

        for ($i=0, $n=count($products); $i<$n; $i++) {
          // Push all attributes information in an array
          if (isset($products[$i]['attributes']) && is_array($products[$i]['attributes'])) {
            foreach($products[$i]['attributes'] as $option => $value) {
              $products_field .= tep_draw_hidden_field('id[' . $products[$i]['id'] . '][' . $option . ']', $value);
              $attributes = tep_db_query("select popt.*, poval.*, pa.*
                                          from products_options popt, products_options_values poval, products_attributes pa
                                          where pa.products_id = '" . (int)$products[$i]['id'] . "'
                                          and pa.options_id = '" . (int)$option . "'
                                          and pa.options_id = popt.products_options_id
                                          and pa.options_values_id = '" . (int)$value . "'
                                          and pa.options_values_id = poval.products_options_values_id
                                          and popt.language_id = '" . (int)$languages_id . "'
                                          and poval.language_id = '" . (int)$languages_id . "'");
              $attributes_values = tep_db_fetch_array($attributes);

              $products[$i][$option]['products_options_name'] = $attributes_values['products_options_name'];
              $products[$i][$option]['options_values_id'] = $value;
              $products[$i][$option]['products_options_values_name'] = $attributes_values['products_options_values_name'];
              $products[$i][$option]['options_values_price'] = $attributes_values['options_values_price'];
              $products[$i][$option]['price_prefix'] = $attributes_values['price_prefix'];
            }
          }
        }

        $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
        include 'includes/modules/content/cm_template.php';
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_SC_PRODUCT_LISTING_STATUS' => [
          'title' => 'Enable Shopping Cart Product Listing',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your shop?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_SC_PRODUCT_LISTING_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_SC_PRODUCT_LISTING_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '120',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
