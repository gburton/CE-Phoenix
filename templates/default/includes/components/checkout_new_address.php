<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/
?>

  <div class="contentText">

<?php
  if (!isset($customer_details)) {
    $customer_details = null;
  }
  $customer_data->display_input($customer_data->get_fields_for_page('checkout_new_address'), $customer_details);
?>

</div>
