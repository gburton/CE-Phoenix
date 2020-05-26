<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  tep_db_query("UPDATE products SET products_date_available = NULL WHERE NOW() > products_date_available");

  $products_query_raw = sprintf(<<<'EOSQL'
SELECT pd.products_id, pd.products_name, p.products_date_available
 FROM products_description pd, products p
 WHERE p.products_id = pd.products_id AND p.products_date_available IS NOT NULL AND pd.language_id = %d
 ORDER BY p.products_date_available
EOSQL
    , (int)$_SESSION['languages_id']);
  $products_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $products_query_raw, $products_query_numrows);
  $products_query = tep_db_query($products_query_raw);

  require 'includes/template_top.php';
?>

  <h1 class="display-4 mb-2"><?php echo HEADING_TITLE; ?></h1>
  
  <div class="row no-gutters">
    <div class="col-12 col-sm-8">
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th><?php echo TABLE_HEADING_PRODUCTS; ?></th>
              <th class="text-center"><?php echo TABLE_HEADING_DATE_EXPECTED; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_ACTION; ?></th>
            </tr>
          </thead>
          <tbody>
          <?php
            while ($products = tep_db_fetch_array($products_query)) {
              if (!isset($pInfo) && (!isset($_GET['pID']) || ($_GET['pID'] == $products['products_id']))) {
                $pInfo = new objectInfo($products);
              }

              if (isset($pInfo->products_id) && ($products['products_id'] == $pInfo->products_id)) {
                echo '<tr class="table-active" onclick="document.location.href=\'' . tep_href_link('categories.php', 'pID=' . (int)$products['products_id'] . '&action=new_product') . '\'">';
                $action_icon = '<i class="fas fa-chevron-circle-right text-info"></i>';
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('products_expected.php', 'page=' . (int)$_GET['page'] . '&pID=' . $products['products_id']) . '\'">';
                $action_icon = '<a href="'
                             . tep_href_link('products_expected.php', 'page=' . (int)$_GET['page'] . '&pID=' . (int)$products['products_id'])
                             . '"><i class="fas fa-info-circle text-muted"></i></a>';
              }
              ?>
              <td><?php echo $products['products_name']; ?></td>
              <td class="text-center"><?php echo tep_date_short($products['products_date_available']); ?></td>
              <td class="text-right"><?php echo $action_icon; ?></td>
            </tr>
            <?php
            }
            ?>
          </tbody>
        </table>
      </div>
      
      <div class="row my-1">
        <div class="col"><?php echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS_EXPECTED); ?></div>
        <div class="col text-right mr-2"><?php echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></div>
      </div>
    </div>
<?php
  $heading = [];
  $contents = [];

  if (isset($pInfo) && is_object($pInfo)) {
    $heading[] = ['text' => $pInfo->products_name];

    $contents[] = [
      'class' => 'text-center',
      'text' => tep_draw_bootstrap_button(IMAGE_EDIT, 'fas fa-cogs', tep_href_link('categories.php', 'pID=' . (int)$pInfo->products_id . '&action=new_product'), null, null, 'btn-warning'),
    ];
    $contents[] = ['text' => sprintf(TEXT_INFO_DATE_EXPECTED, tep_date_short($pInfo->products_date_available))];
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '<div class="col-12 col-sm-4">';
      $box = new box;
      echo $box->infoBox($heading, $contents);
    echo '</div>';
  }
?>

  </div>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
