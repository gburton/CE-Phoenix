<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require('includes/template_top.php');
?>

  <h1 class="display-4 mb-2"><?php echo HEADING_TITLE; ?></h1>

  <div class="table-responsive">
    <table class="table table-striped table-hover">
      <thead class="thead-dark">
        <tr>
          <th><?php echo TABLE_HEADING_NUMBER; ?></th>
          <th><?php echo TABLE_HEADING_PRODUCTS; ?></th>
          <th class="text-right"><?php echo TABLE_HEADING_PURCHASED; ?></th>
        </tr>
      </thead>
      <tbody>
        <?php
        if (isset($_GET['page']) && ($_GET['page'] > 1)) $rows = $_GET['page'] * MAX_DISPLAY_SEARCH_RESULTS - MAX_DISPLAY_SEARCH_RESULTS;
        $products_query_raw = "select p.products_id, p.products_ordered, pd.products_name from products p, products_description pd where pd.products_id = p.products_id and pd.language_id = '" . (int)$languages_id. "' and p.products_ordered > 0 group by pd.products_id order by p.products_ordered DESC, pd.products_name";
        $products_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $products_query_raw, $products_query_numrows);

        $rows = 0;
        $products_query = tep_db_query($products_query_raw);
        while ($products = tep_db_fetch_array($products_query)) {
          $rows++;
          ?>
          <tr onclick="document.location.href='<?php echo tep_href_link('categories.php', 'action=new_product_preview&read=only&pID=' . $products['products_id'] . '&origin=stats_products_purchased.php?page=' . (int)$_GET['page']); ?>'">
            <td><?php echo str_pad($rows, 2, '0', STR_PAD_LEFT); ?>.</td>
            <td><?php echo '<a href="' . tep_href_link('categories.php', 'action=new_product_preview&read=only&pID=' . (int)$products['products_id'] . '&origin=stats_products_purchased.php?page=' . (int)$_GET['page']) . '">' . $products['products_name'] . '</a>'; ?></td>
            <td class="text-right"><?php echo $products['products_ordered']; ?></td>
          </tr>
          <?php
        }
        ?>
      </tbody>
    </table>
  </div>
  
  <div class="row">
    <div class="col-sm-6"><?php echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, (int)$_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></div>
    <div class="col-sm-6 text-sm-right"><?php echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, (int)$_GET['page']); ?></div>
  </div>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
