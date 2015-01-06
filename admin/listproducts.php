<?php
/*
  $Id: validproducts.php,v 0.01 2014/03/10 17:56:34 Melanie Shepherd
  $Id: validproducts.php,v 0.01 2002/08/17 15:38:34 Richard Fielder
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Copyright (c) 2002 Richard Fielder
  Released under the GNU General Public License
*/
require('includes/application_top.php');
?>
<html>
<head>
<title>Valid Categories/Products List</title>
<style type="text/css">
<!--
h4 {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: x-small; text-align: center}
p {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: xx-small}
th {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: xx-small}
td {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: xx-small}
table {border-collapse: collapse}
-->
</style>
<head>
<body>
<table width="550" border="1" cellspacing="1" bordercolor="gray">
<tr>
<td colspan="3">
<h4>Valid Products List</h4>
</td>
</tr>
<?php
   $coupon_get=tep_db_query("select restrict_to_products,restrict_to_categories from " . TABLE_COUPONS . "  where coupon_id='".$_GET['cid']."'");
   $get_result=tep_db_fetch_array($coupon_get);
    echo "<tr><th>Product ID</th><th>Product Name</th><th>Product Size</th></tr><tr>";
    $pr_ids = preg_split("[,]", $get_result['restrict_to_products']);
    for ($i = 0; $i < count($pr_ids); $i++) {
      $result = tep_db_query("SELECT * FROM " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESRIPTIONS . " pd, WHERE p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "'and p.products_id = '" . $pr_ids[$i] . "'");
      if ($row = tep_db_fetch_array($result)) {
            echo "<td>".$row["products_id"]."</td>\n";
            echo "<td>".$row["products_name"]."</td>\n";
            echo "<td>".$row["products_model"]."</td>\n";
            echo "</tr>\n";
      }
    }
      echo "</table>\n";
?>
<br>
<table width="550" border="0" cellspacing="1">
<tr>
<td align=middle><input type="button" value="Close Window" onClick="window.close()"></td>
</tr></table>
</body>
</html>
