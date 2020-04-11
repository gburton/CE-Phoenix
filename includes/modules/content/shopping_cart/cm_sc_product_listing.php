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
      if ($_SESSION['cart']->count_contents() > 0) {
        $content_width = (int)MODULE_CONTENT_SC_PRODUCT_LISTING_CONTENT_WIDTH;
        
        $GLOBALS['any_out_of_stock'] = false;
        $products = $_SESSION['cart']->get_products();
        $products_field = '';

        for ($i=0, $n=count($products); $i<$n; $i++) {
          // Push all attributes information in an array
          foreach (($products[$i]['attributes'] ?? []) as $option => $value) {
            $products_field .= tep_draw_hidden_field('id[' . $products[$i]['id'] . '][' . $option . ']', $value);
            $attributes = tep_db_query(sprintf(<<<'EOSQL'
SELECT popt.*, poval.*, pa.*
 FROM products_options popt
   INNER JOIN products_attributes pa ON pa.options_id = popt.products_options_id
   INNER JOIN products_options_values poval
     ON pa.options_values_id = poval.products_options_values_id
    AND popt.language_id = poval.language_id
 WHERE pa.products_id = %d
   AND pa.options_id = %d
   AND pa.options_values_id = %d
   AND popt.language_id = %d
EOSQL
              , (int)$products[$i]['id'], (int)$option, (int)$value, (int)$_SESSION['languages_id']));
            $attributes_values = tep_db_fetch_array($attributes);

            $products[$i][$option]['products_options_name'] = $attributes_values['products_options_name'];
            $products[$i][$option]['options_values_id'] = $value;
            $products[$i][$option]['products_options_values_name'] = $attributes_values['products_options_values_name'];
            $products[$i][$option]['options_values_price'] = $attributes_values['options_values_price'];
            $products[$i][$option]['price_prefix'] = $attributes_values['price_prefix'];
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
