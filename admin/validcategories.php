<?php
/*
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
<h4><?php echo TEXT_VALID_CATEGORIES_LIST; ?></h4>
</td>
</tr>
<?php
    echo "<tr><th>" . TEXT_VALID_CATEGORIES_ID . "</th><th>" . TEXT_VALID_CATEGORIES_NAME . "</th></tr><tr>";
    $result = tep_db_query("SELECT * FROM " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd WHERE c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' ORDER BY c.categories_id");
    $row = tep_db_fetch_array($result);
        do {
            echo "<td>".$row["categories_id"]."</td>\n";
            echo "<td>".$row["categories_name"]."</td>\n";
            echo "</tr>\n";
        }
        while($row = mysqli_fetch_array($result));
    echo "</table>\n";
?>
<br>
<table width="550" border="0" cellspacing="1">
<tr>
<td align=middle><input type="button" value="Close Window" onClick="window.close()"></td>
</tr></table>
</body>
</html>
