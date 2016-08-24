<?php
if ($cart->count_contents() > 0) {
  ?>
  <li class="dropdown">
    <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo sprintf(MODULE_NAVBAR_SHOPPING_CART_CONTENTS, $cart->count_contents()); ?></a>
    <ul class="dropdown-menu">
      <li><?php echo '<a href="' . tep_href_link('shopping_cart.php') . '">' . sprintf(MODULE_NAVBAR_SHOPPING_CART_HAS_CONTENTS, $cart->count_contents(), $currencies->format($cart->show_total())) . '</a>'; ?></li>
      <li role="separator" class="divider"></li>
      <?php      
      $products = $cart->get_products();
      foreach ($products as $k => $v) {
        echo '<li>' . sprintf(MODULE_NAVBAR_SHOPPING_CART_PRODUCT, $v['id'], $v['quantity'], $v['name']) . '</li>';
      }        
      ?>
      <li role="separator" class="divider"></li>
      <li><?php echo '<a href="' . tep_href_link('shopping_cart.php') . '">' . MODULE_NAVBAR_SHOPPING_CART_VIEW_CART . '</a>'; ?></li>
    </ul>
  </li>
  <?php
  echo '<li><a href="' . tep_href_link('checkout_shipping.php', '', 'SSL') . '">' . MODULE_NAVBAR_SHOPPING_CART_CHECKOUT . '</a></li>';
}
else {
  echo '<li><p class="navbar-text">' . MODULE_NAVBAR_SHOPPING_CART_NO_CONTENTS . '</p></li>';
}
?>