<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  $expected_query = tep_db_query("select p.products_id, pd.products_name, products_date_available as date_expected from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where to_days(products_date_available) >= to_days(now()) and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' order by " . EXPECTED_PRODUCTS_FIELD . " " . EXPECTED_PRODUCTS_SORT . " limit " . MAX_DISPLAY_UPCOMING_PRODUCTS);
  if (tep_db_num_rows($expected_query) > 0) {
?>

  <div class="panel panel-info">
    <div class="panel-heading">
      <div class="pull-left">
        <?php echo TABLE_HEADING_UPCOMING_PRODUCTS; ?>
      </div>
      <div class="pull-right">
        <?php echo TABLE_HEADING_DATE_EXPECTED; ?>
      </div>
      <div class="clearfix"></div>
    </div>

    <div class="panel-body">
<?php
    while ($expected = tep_db_fetch_array($expected_query)) {
      echo '<div class="pull-left"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $expected['products_id']) . '">' . $expected['products_name'] . '</a></div>' . "\n" .
           '<div class="pull-right">' . tep_date_short($expected['date_expected']) . '</div>' .
           '<div class="clearfix"></div>' . "\n";
    }
?>

    </div>
  </div>

<?php
  }
?>