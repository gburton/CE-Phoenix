<div class="col-sm-<?php echo $content_width; ?> text-right cm-header-buttons">
  <div class="btn-group" role="group" aria-label="...">
    <?php
    echo tep_draw_button(MODULE_CONTENT_HEADER_BUTTONS_TITLE_CART_CONTENTS . ($_SESSION['cart']->count_contents() > 0 ? ' (' . $_SESSION['cart']->count_contents() . ')' : ''), 'fas fa-shopping-cart', tep_href_link('shopping_cart.php'))
       . tep_draw_button(MODULE_CONTENT_HEADER_BUTTONS_TITLE_CHECKOUT, 'fas fa-credit-card', tep_href_link('checkout_shipping.php', '', 'SSL'))
       . tep_draw_button(MODULE_CONTENT_HEADER_BUTTONS_TITLE_MY_ACCOUNT, 'fas fa-user', tep_href_link('account.php', '', 'SSL'));

    if ( isset($_SESSION['customer_id']) ) {
      echo tep_draw_button(MODULE_CONTENT_HEADER_BUTTONS_TITLE_LOGOFF, 'fas fa-sign-out-alt', tep_href_link('logoff.php', '', 'SSL'));
    }
    ?>
  </div>
</div>

<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/
?>
