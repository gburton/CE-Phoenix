<div class="col-sm-<?php echo $content_width; ?> cm-pi-also-purchased" itemscope itemtype="http://schema.org/ItemList">
  <meta itemprop="itemListOrder" content="http://schema.org/ItemListUnordered" />
  <meta itemprop="numberOfItems" content="<?php echo (int)$num_products_ordered; ?>" />

  <h4 itemprop="name"><?php echo MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_PUBLIC_TITLE; ?></h4>

  <div class="card-deck">
    <?php
    $item = 1;
    while ($orders = tep_db_fetch_array($orders_query)) {      
      ?>
      <div class="card text-center">        
        <div class="card-body" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
          <a href="<?php echo tep_href_link('product_info.php', 'products_id=' . (int)$card_products['products_id']); ?>"><?php echo tep_image('images/' . $orders['products_image'], htmlspecialchars($orders['products_name']), null, null, 'itemprop="image"'); ?></a>
          <br>
          <a class="card-link" itemprop="url" href="<?php echo tep_href_link('product_info.php', 'products_id=' . (int)$orders['products_id']); ?>"><span itemprop="name"><?php echo $orders['products_name']; ?></span></a>
          <meta itemprop="position" content="<?php echo (int)$item; ?>" />
        </div>
      </div>      
      <?php
      if ( $item%MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_DISPLAY_ROW_SM == 0 ) echo '<div class="w-100 d-none d-sm-block d-md-none"></div>' . PHP_EOL; 
      if ( $item%MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_DISPLAY_ROW_MD == 0 ) echo '<div class="w-100 d-none d-md-block d-lg-none"></div>' . PHP_EOL; 
      if ( $item%MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_DISPLAY_ROW_LG == 0 ) echo '<div class="w-100 d-none d-lg-block d-xl-none"></div>' . PHP_EOL;
      if ( $item%MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_DISPLAY_ROW_XL == 0 ) echo '<div class="w-100 d-none d-xl-block"></div>' . PHP_EOL;
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
