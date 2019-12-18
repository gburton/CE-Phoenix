<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  tep_db_query("update products set products_date_available = '' where to_days(now()) > to_days(products_date_available)");

  require('includes/template_top.php');
?>

  <h1 class="display-4 mb-2"><?php echo HEADING_TITLE; ?></h1>
  
  <div class="row no-gutters">
    <div class="col">
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
  $products_query_raw = "select pd.products_id, pd.products_name, p.products_date_available from products_description pd, products p where p.products_id = pd.products_id and p.products_date_available != '' and pd.language_id = '" . (int)$languages_id . "' order by p.products_date_available";
  $products_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $products_query_raw, $products_query_numrows);
  $products_query = tep_db_query($products_query_raw);
  while ($products = tep_db_fetch_array($products_query)) {
    if ((!isset($_GET['pID']) || (isset($_GET['pID']) && ($_GET['pID'] == $products['products_id']))) && !isset($pInfo)) {
      $pInfo = new objectInfo($products);
    }

    if (isset($pInfo) && is_object($pInfo) && ($products['products_id'] == $pInfo->products_id)) {
      echo '<tr onclick="document.location.href=\'' . tep_href_link('categories.php', 'pID=' . (int)$products['products_id'] . '&action=new_product') . '\'">';
    } else {
      echo '<tr onclick="document.location.href=\'' . tep_href_link('products_expected.php', 'page=' . (int)$_GET['page'] . '&pID=' . $products['products_id']) . '\'">';
    }
?>
              <td><?php echo $products['products_name']; ?></td>
              <td class="text-center"><?php echo tep_date_short($products['products_date_available']); ?></td>
              <td class="text-right"><?php if (isset($pInfo) && is_object($pInfo) && ($products['products_id'] == $pInfo->products_id)) { echo '<i class="fas fa-chevron-circle-right text-info"></i>'; } else { echo '<a href="' . tep_href_link('products_expected.php', 'page=' . (int)$_GET['page'] . '&pID=' . (int)$products['products_id']) . '"><i class="fas fa-info-circle text-muted"></i></a>'; } ?></td>
            </tr>
<?php
  }
?>

          </tbody>
        </table>
      </div>
      
      <div class="row my-1">
        <div class="col"><?php echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS_EXPECTED); ?></div>
        <div class="col text-right"><?php echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></div>
      </div>
                
    </div>
<?php
  $heading = array();
  $contents = array();

  if (isset($pInfo) && is_object($pInfo)) {
    $heading[] = array('text' => $pInfo->products_name);

    $contents[] = array('class' => 'text-center', 'text' => tep_draw_button(IMAGE_EDIT, 'document', tep_href_link('categories.php', 'pID=' . (int)$pInfo->products_id . '&action=new_product')));
    $contents[] = array('text' => sprintf(TEXT_INFO_DATE_EXPECTED, tep_date_short($pInfo->products_date_available)));
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '<div class="col-12 col-sm-3">';
      $box = new box;
      echo $box->infoBox($heading, $contents);
    echo '</div>';
  }
?>

  </div>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
