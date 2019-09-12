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
        <?php echo $products_name; ?>
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
