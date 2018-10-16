<div class="col-sm-<?php echo $content_width; ?> cm-i-card-products">
  <h4><?php echo sprintf(MODULE_CONTENT_CARD_PRODUCTS_HEADING, strftime('%B')); ?></h4>

  <div class="card-deck cm-i-card-products" itemscope itemtype="http://schema.org/ItemList">
    <meta itemprop="numberOfItems" content="<?php echo (int)$num_card_products; ?>" />
    <?php
    $item = 1;
    while ($card_products = tep_db_fetch_array($card_products_query)) {
      ?>
      <div class="card text-center is-product" data-is-special="<?php echo (int)$card_products['is_special']; ?>" data-product-price="<?php echo $currencies->display_raw($card_products['products_price'], tep_get_tax_rate($card_products['products_tax_class_id'])); ?>" data-product-manufacturer="<?php echo max(0, (int)$card_products['manufacturers_id']); ?>" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/Product">
        <div class="card-header caption">
          <h6 class="group inner list-group-item-heading">
            <a itemprop="url" href="<?php echo tep_href_link('product_info.php', 'products_id=' . (int)$card_products['products_id']); ?>"><span itemprop="name"><?php echo $card_products['products_name']; ?></span></a>
          </h6>  
        </div>
        <div class="card-body">
          <a href="<?php echo tep_href_link('product_info.php', 'products_id=' . (int)$card_products['products_id']); ?>"><?php echo tep_image('images/' . $card_products['products_image'], htmlspecialchars($card_products['products_name']), null, null, 'itemprop="image"'); ?></a>
        </div>
        <div class="card-footer">          
          <div class="row">
            <div class="col text-left" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
              <meta itemprop="priceCurrency" content="<?php echo tep_output_string($currency); ?>" />
              <span class="align-middle" itemprop="price" content="<?php echo $currencies->display_raw($card_products['products_price'], tep_get_tax_rate($card_products['products_tax_class_id'])); ?>"><?php echo $currencies->display_price($card_products['products_price'], tep_get_tax_rate($card_products['products_tax_class_id'])); ?></span>
            </div>
            <div class="col text-right">
              <?php
              switch (MODULE_CONTENT_CARD_PRODUCTS_BUTTON) {
                case 'View':
                echo '<a role="button" href="' . tep_href_link('product_info.php', 'products_id=' . (int)$card_products['products_id']) . '" class="btn btn-light btn-sm btn-index btn-view">' . MODULE_CONTENT_CARD_PRODUCTS_BUTTON_VIEW . '</a>';
                break;
                case 'Buy':
                echo '<a role="button" href="' . tep_href_link($PHP_SELF, tep_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . (int)$card_products['products_id']) . '" data-has-attributes="' . ((tep_has_product_attributes((int)$card_products['products_id']) === true) ? '1' : '0') . '" data-in-stock="' . (int)$card_products['in_stock'] . '" data-product-id="' . (int)$card_products['products_id'] . '" class="btn btn-success btn-sm btn-index btn-buy">' . MODULE_CONTENT_CARD_PRODUCTS_BUTTON_BUY . '</a>';
                break;
              }               
              ?>
            </div>
          </div>          
        </div>
      </div>      
      <?php
      if ( $item%MODULE_CONTENT_CARD_PRODUCTS_DISPLAY_ROW_SM == 0 ) echo '<div class="w-100 d-none d-sm-block d-md-none"></div>' . PHP_EOL; 
      if ( $item%MODULE_CONTENT_CARD_PRODUCTS_DISPLAY_ROW_MD == 0 ) echo '<div class="w-100 d-none d-md-block d-lg-none"></div>' . PHP_EOL; 
      if ( $item%MODULE_CONTENT_CARD_PRODUCTS_DISPLAY_ROW_LG == 0 ) echo '<div class="w-100 d-none d-lg-block d-xl-none"></div>' . PHP_EOL;
      if ( $item%MODULE_CONTENT_CARD_PRODUCTS_DISPLAY_ROW_XL == 0 ) echo '<div class="w-100 d-none d-xl-block"></div>' . PHP_EOL;
      $item++;
    }
    ?>
  </div> 
</div>

<?php
/*
  Copyright (c) 2018, G Burton
  All rights reserved.

  Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

  1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

  2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

  3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
?>
