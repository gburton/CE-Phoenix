<?php
/*
  $Id: validproducts.php,v 0.01 2014/03/10 17:56:34 Melanie Shepherd
  $Id: validcategories.php,v 0.01 2002/08/17 15:38:34 Richard Fielder
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
<td colspan="4">
<h4>Valid Categories List</h4>
</td>
</tr>
<?php
   $coupon_get = tep_db_query("select restrict_to_categories from " . TABLE_COUPONS . " where coupon_id='".$_GET['cid']."'");
   $get_result = tep_db_fetch_array($coupon_get);
   echo "<tr><th>Category ID</th><th>Category Name</th></tr><tr>";
   $cat_ids = preg_split("[,]", $get_result['restrict_to_categories']);
   for ($i = 0; $i < count($cat_ids); $i++) {
     $result = tep_db_query("SELECT * FROM " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd WHERE c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' and c.categories_id='" . $cat_ids[$i] . "'");
     if ($row = tep_db_fetch_array($result)) {
       echo "<td>".$row["categories_id"]."</td>\n";
       echo "<td>".$row["categories_name"]."</td>\n";
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
