<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  $xx_mins_ago = (time() - 900);

  require 'includes/application_top.php';

// remove entries that have expired
  tep_db_query("DELETE FROM whos_online WHERE time_last_click < " . (int)$xx_mins_ago);

  require 'includes/template_top.php';
?>

  <h1 class="display-4 mb-2"><?= HEADING_TITLE ?></h1>

  <div class="row no-gutters">
    <div class="col-12 col-sm-8">
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th><?= TABLE_HEADING_ONLINE ?></th>
              <th><?= TABLE_HEADING_CUSTOMER_ID ?></th>
              <th><?= TABLE_HEADING_FULL_NAME ?></th>
              <th><?= TABLE_HEADING_IP_ADDRESS ?></th>
              <th class="text-right"><?= TABLE_HEADING_ENTRY_TIME ?></th>
              <th class="text-right"><?= TABLE_HEADING_LAST_CLICK ?></th>
              <th class="text-right"><?= TABLE_HEADING_LAST_PAGE_URL ?></th>
            </tr>
          </thead>
          <tbody>
          <?php
          $whos_online_query = tep_db_query("SELECT * FROM whos_online");
          while ($whos_online = tep_db_fetch_array($whos_online_query)) {
            $time_online = (time() - $whos_online['time_entry']);
            if (!isset($info) && (!isset($_GET['info']) || ($_GET['info'] == $whos_online['session_id']))) {
              $info = new ObjectInfo($whos_online);
            }

            if (isset($info->session_id) && ($whos_online['session_id'] == $info->session_id)) {
              echo '<tr class="table-active">';
            } else {
              echo '<tr onclick="document.location.href=\'' . tep_href_link('whos_online.php', tep_get_all_get_params(['info', 'action']) . 'info=' . $whos_online['session_id']) . '\'">';
            }
?>
                <td><?= gmdate('H:i:s', $time_online) ?></td>
                <td><?= $whos_online['customer_id'] ?></td>
                <td><?= $whos_online['full_name'] ?></td>
                <td><?= $whos_online['ip_address'] ?></td>
                <td class="text-right"><?= date('H:i:s', $whos_online['time_entry']) ?></td>
                <td class="text-right"><?= date('H:i:s', $whos_online['time_last_click']) ?></td>
                <td class="text-right"><?= preg_replace('/ceid=[A-Z0-9,-]+[&]*/i', '', $whos_online['last_page_url']) ?></td>
              </tr>
<?php
  }
?>
          </tbody>
        </table>
      </div>

      <p><?php printf(TEXT_NUMBER_OF_CUSTOMERS, tep_db_num_rows($whos_online_query)); ?></p>

    </div>

<?php
  $heading = [];
  $contents = [];

  if (isset($info)) {
    $heading[] = ['text' => TABLE_HEADING_SHOPPING_CART];

    if ( $info->customer_id > 0 ) {
      function tep_has_product_attributes($products_id) {
        $attributes_query = tep_db_query("SELECT COUNT(*) AS count FROM products_attributes WHERE products_id = " . (int)$products_id);
        $attributes = tep_db_fetch_array($attributes_query);

        return $attributes['count'] > 0;
      }

      function tep_create_random_value($length, $type = 'mixed') {
        return 0;
      }

      $session_customer_id = $_SESSION['customer_id'] ?? null;
      $session_currency = $_SESSION['currency'] ?? null;
      $_SESSION['customer_id'] = $info->customer_id;
      $_SESSION['currency'] = DEFAULT_CURRENCY;

      $shoppingCart = new shoppingCart();
      $shoppingCart->restore_contents();

      foreach ($shoppingCart->get_products() as $product) {
        $contents[] = ['text' => sprintf(TEXT_SHOPPING_CART_ITEM, $product['quantity'], $product['name'])];
      }

      $currencies = new currencies();
      $contents[] = [
        'class' => 'table-dark text-right',
        'text' => sprintf(TEXT_SHOPPING_CART_SUBTOTAL, $currencies->format($shoppingCart->show_total())),
      ];

      $_SESSION['customer_id'] = $session_customer_id;
      $_SESSION['currency'] = $session_currency;
    } else {
      $contents[] = ['text' => TEXT_SHOPPING_CART_NA];
    }
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '<div class="col-12 col-sm-4">';
      $box = new box();
      echo $box->infoBox($heading, $contents);
    echo '</div>';
  }
?>

  </div>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
