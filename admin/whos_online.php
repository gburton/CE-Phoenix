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
      $products_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT cb.*, pd.*
 FROM customers_basket cb INNER JOIN products_description pd ON cb.products_id = pd.products_id
 WHERE cb.customers_id = %d AND pd.language_id = %d
EOSQL
        , (int)$info->customer_id, (int)$_SESSION['languages_id']));

      if ( tep_db_num_rows($products_query) ) {
        $shoppingCart = new shoppingCart();

        while ( $products = tep_db_fetch_array($products_query) ) {
          $contents[] = ['text' => sprintf(TEXT_SHOPPING_CART_ITEM, $products['customers_basket_quantity'], $products['products_name'])];

          $attributes = [];

          if ( strpos($products['products_id'], '{') !== false ) {
            $combos = [];
            preg_match_all('/(\{[0-9]+\}[0-9]+){1}/', $products['products_id'], $combos);

            foreach ( $combos[0] as $combo ) {
              $att = [];
              preg_match('/\{([0-9]+)\}([0-9]+)/', $combo, $att);

              $attributes[$att[1]] = $att[2];
            }
          }

          $shoppingCart->add_cart(tep_get_prid($products['products_id']), $products['customers_basket_quantity'], $attributes);
        }

        $currencies = new currencies();
        $contents[] = ['class' => 'table-dark text-right', 'text'  => sprintf(TEXT_SHOPPING_CART_SUBTOTAL, $currencies->format($shoppingCart->show_total()))];
      } else {
        $contents[] = ['text' => '&nbsp;'];
      }
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
