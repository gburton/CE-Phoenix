<div class="col-sm-<?php echo $content_width ?> cm-sc-product-listing">
  <?php
  echo tep_draw_form('cart_quantity', tep_href_link('shopping_cart.php', 'action=update_product')) . PHP_EOL;
  echo $products_field . PHP_EOL;
  ?>
  <div class="table-responsive">
    <table class="table mb-0">
      <thead class="thead-light">
        <tr>
          <th class="d-none d-md-table-cell">&nbsp;</th>
          <th><?php echo MODULE_CONTENT_SC_PRODUCT_LISTING_HEADING_PRODUCT; ?></th>
          <th><?php echo MODULE_CONTENT_SC_PRODUCT_LISTING_HEADING_AVAILABILITY; ?></th>
          <th><?php echo MODULE_CONTENT_SC_PRODUCT_LISTING_HEADING_QUANTITY; ?></th>
          <th class="text-right"><?php echo MODULE_CONTENT_SC_PRODUCT_LISTING_HEADING_PRICE; ?></th>
        </tr>
      </thead>
      <tbody>
        <?php
  foreach ($products as $product) {
    echo '<tr>';
    echo '<td class="d-none d-md-table-cell"><a href="' . tep_href_link('product_info.php', 'products_id=' . $product['id']) . '">' . tep_image('images/' . $product['image'], htmlspecialchars($product['name']), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a></td>';
    echo   '<th><a href="' . tep_href_link('product_info.php', 'products_id=' . $product['id']) . '">' . $product['name'] . '</a>';
    foreach (($product['attributes'] ?? []) as $option => $value) {
      echo '<small><br><i> - ' . $product[$option]['products_options_name'] . ' ' . $product[$option]['products_options_values_name'] . '</i></small>';
    }
    echo   '</th>';

    if (STOCK_CHECK == 'true' && tep_check_stock($product['id'], $product['quantity'])) {
      $GLOBALS['any_out_of_stock'] = true;

      echo '<td><span class="text-danger"><b>' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</b></span></td>';
    } else {
      echo '<td>' . MODULE_CONTENT_SC_PRODUCT_LISTING_TEXT_IN_STOCK . '</td>';
    }

    echo '<td>';
    echo '<div class="input-group">';
    echo tep_draw_input_field('cart_quantity[]', $product['quantity'], 'style="width: 65px;" min="0"', 'number');
    echo tep_draw_hidden_field('products_id[]', $product['id']);
    echo '<div class="input-group-append">' . tep_draw_button(MODULE_CONTENT_SC_PRODUCT_LISTING_TEXT_BUTTON_UPDATE, null, NULL, NULL, NULL, 'btn-info') . '</div>';
    echo '<div class="input-group-append">' . tep_draw_button(MODULE_CONTENT_SC_PRODUCT_LISTING_TEXT_BUTTON_REMOVE, null, tep_href_link('shopping_cart.php', 'products_id=' . $product['id'] . '&action=remove_product'), NULL, NULL, 'btn-danger') . '</div>';
    echo '</div>';
    echo '</td>';
    echo '<td class="text-right">' . $GLOBALS['currencies']->display_price($product['final_price'], tep_get_tax_rate($product['tax_class_id']), $product['quantity']) . '</td>';
    echo '</tr>';
  }
?>
      </tbody>
    </table>
  </div>
  </form>
  <hr class="mt-0">
</div>	

<?php
/*
  $Id$

  Copyright (c) 2016:
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
?>
