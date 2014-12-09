<?php
/*
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
<title>Valid Products List</title>
<style type="text/css">
h3 {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: medium; text-align: center}
p {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: xx-small}
th {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: xx-small}
td {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: xx-small}
table {border-collapse: collapse}
</style>
<head>
<body>
<table width="100%" border="1" cellspacing="1" bordercolor="#CCC">
<tr>
<td colspan="3">
<h3><?php echo TEXT_VALID_PRODUCTS_LIST; ?></h3>
</td>
</tr>
<?php
    echo "<tr><th>". TEXT_VALID_PRODUCTS_ID . "</th><th>" . TEXT_VALID_PRODUCTS_NAME . "</th><th>" . TEXT_VALID_PRODUCTS_MODEL . "</th></tr><tr>";
    $result = tep_db_query("SELECT * FROM " . TABLE_PRODUCTS . " p,  " . TABLE_PRODUCTS_DESCRIPTION . " pd WHERE p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' ORDER BY pd.products_name");
     $row = tep_db_fetch_array($result);
             do {
            echo "<td>".$row["products_id"]."</td>\n";
            echo "<td>".$row["products_name"]."</td>\n";
            echo "<td>".$row["products_model"]."</td>\n";
            echo "</tr>\n";
        }
        while($row = tep_db_fetch_array($result));
    echo "</table>\n";
?>
<br>
<table width="550" border="0" cellspacing="1">
<tr>
<td align=middle><input type="button" value="Close Window" onClick="window.close()"></td>
</tr></table>
</body>
</html>