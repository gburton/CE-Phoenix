<li class="nav-item dropdown nb-shopping-cart">
  <a class="nav-link dropdown-toggle" href="#" id="navDropdownCart" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <?php echo sprintf(MODULE_NAVBAR_SHOPPING_CART_CONTENTS, $_SESSION['cart']->count_contents()); ?>
  </a>

  <div class="dropdown-menu<?php echo (('Right' === MODULE_NAVBAR_SHOPPING_CART_CONTENT_PLACEMENT) ? ' dropdown-menu-right' : ''); ?>" aria-labelledby="navDropdownCart">
    <?php
    echo '<a class="dropdown-item" href="' . tep_href_link('shopping_cart.php') . '">'
       . sprintf(
           MODULE_NAVBAR_SHOPPING_CART_HAS_CONTENTS,
           $_SESSION['cart']->count_contents(),
           $GLOBALS['currencies']->format($_SESSION['cart']->show_total())
         )
       . '</a>';
    if ($_SESSION['cart']->count_contents() > 0) {
      echo '<div class="dropdown-divider"></div>' . PHP_EOL;
      echo '<div class="dropdown-cart-list">';
      foreach ($_SESSION['cart']->get_products() as $p) {
        echo sprintf(MODULE_NAVBAR_SHOPPING_CART_PRODUCT, $p['id'], $p['quantity'], $p['name']);
      }
      echo '</div>' . PHP_EOL;
      echo '<div class="dropdown-divider"></div>' . PHP_EOL;
      echo '<a class="dropdown-item" href="' . tep_href_link('checkout_shipping.php', '', 'SSL') . '">' . MODULE_NAVBAR_SHOPPING_CART_CHECKOUT . '</a>' . PHP_EOL;
    }
    ?>
  </div>
</li>

<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/
?>
