  <a href="<?= $product->get('link') ?>"><?= tep_image('images/' . $product->get('image'), htmlspecialchars($product->get('name')), '', '', '', true, 'card-img-top') ?></a>
  <div class="card-body">
    <h5 class="card-title"><a href="<?= $product->get('link') ?>"><?= $product->get('name') ?></a></h5>
    <h6 class="card-subtitle mb-2 text-muted"><?= $product->hype_price() ?></h6>
    <?= $card['extra'] ?? '' ?>
  </div>

<?php
  if ($card['show_buttons'] ?? false) {
?>

  <div class="card-footer bg-white pt-0 border-0">
    <div class="btn-group" role="group">
      <?php
    echo tep_draw_button(IS_PRODUCT_BUTTON_VIEW, '', $product->get('link'), null, null, 'btn-info btn-product-listing btn-view');

    if (!$product->get('has_attributes')) {
      echo PHP_EOL, tep_draw_button(
        IS_PRODUCT_BUTTON_BUY,
        '',
        tep_href_link(basename($GLOBALS['PHP_SELF']), tep_get_all_get_params(['action', 'products_id']) . 'action=buy_now&products_id=' . (int)$product->get('id')),
        null,
        ['params' => 'data-has-attributes="0" data-in-stock="' . (int)$product->get('in_stock') . '" data-product-id="' . (int)$product->get('id') . '"'],
        'btn-light btn-product-listing btn-buy');
    }
?>
    </div>
  </div>

<?php
  }

/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2020 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
?>
