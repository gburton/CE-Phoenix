<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

// if the customer is not logged on, redirect them to the shopping cart page
  if (!isset($_SESSION['customer_id'])) {
    tep_redirect(tep_href_link('shopping_cart.php'));
  }

  $orders_query = tep_db_query("SELECT orders_id FROM orders WHERE customers_id = " . (int)$_SESSION['customer_id'] . " ORDER BY date_purchased DESC LIMIT 1");

// redirect to shopping cart page if no orders exist
  if ( !mysqli_num_rows($orders_query) ) {
    tep_redirect(tep_href_link('shopping_cart.php'));
  }

  $orders = $orders_query->fetch_assoc();

  $order_id = $orders['orders_id'];

  if ( isset($_GET['action']) && ($_GET['action'] === 'update') ) {
    tep_redirect(tep_href_link('index.php'));
  }

  require language::map_to_translation('checkout_success.php');

  require $oscTemplate->map_to_template(__FILE__, 'page');

  require 'includes/application_bottom.php';
