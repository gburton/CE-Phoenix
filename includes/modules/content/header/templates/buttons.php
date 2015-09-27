<div id="headerShortcuts" class="col-sm-<?php echo $content_width; ?> text-right">
  <div class="btn-group">
<?php
  echo tep_draw_button(HEADER_TITLE_CART_CONTENTS . ($cart->count_contents() > 0 ? ' (' . $cart->count_contents() . ')' : ''), 'fa fa-shopping-cart', tep_href_link('shopping_cart.php')) .
       tep_draw_button(HEADER_TITLE_CHECKOUT, 'fa fa-credit-card', tep_href_link('checkout_shipping.php', '', 'SSL')) .
       tep_draw_button(HEADER_TITLE_MY_ACCOUNT, 'fa fa-user', tep_href_link('account.php', '', 'SSL'));

  if ( tep_session_is_registered('customer_id') ) {
    echo tep_draw_button(HEADER_TITLE_LOGOFF, 'fa fa-sign-out', tep_href_link('logoff.php', '', 'SSL'));
  }
?>
  </div>
</div>

