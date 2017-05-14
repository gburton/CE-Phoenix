<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2017 osCommerce

  Released under the GNU General Public License
*/
?>
<div class="col-sm-<?php echo $content_width; ?> also-purchased" itemscope itemtype="http://schema.org/ItemList">
  <meta itemprop="itemListOrder" content="http://schema.org/ItemListUnordered" />
  <meta itemprop="numberOfItems" content="<?php echo (int)$num_products_ordered; ?>" />

  <h3 itemprop="name"><?php echo MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_PUBLIC_TITLE; ?></h3>

  <div class="row list-group">
    <?php
    $position = 1;
    while ($orders = tep_db_fetch_array($orders_query)) {      
      ?>
      <div class="col-sm-<?php echo $product_width; ?>">
        <div class="thumbnail equal-height">
          <a href="<?php echo tep_href_link('product_info.php', 'products_id=' . $orders['products_id']); ?>"><?php echo tep_image('images/' . $orders['products_image'], $orders['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT); ?></a>
          <div class="caption">
            <h5 class="text-center" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="url" href="<?php echo tep_href_link('product_info.php', 'products_id=' . $orders['products_id']); ?>"><span itemprop="name"><?php echo $orders['products_name']; ?></span></a><meta itemprop="position" content="<?php echo (int)$position; ?>" /></h5>
          </div>
        </div>
      </div>
      <?php
      $position++;
    }
    ?>    
  </div>    
</div>