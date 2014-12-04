<?php
/*
  $Id: /admin/includes/functions/dbcheck.php,v 1.0 2008/05/21
  Database checking tool for admin v1.0 MS 2.2

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

//  Begin duplicate names function (Case 1).
function duplicate_names() {
?>

<table cellpadding="0" border="1" valign="top">
<tr>
<td width="100%"><table border="1" width="100%" cellspacing="0" cellpadding="0">
<tr class="dataTableHeadingRow">

<?php
//Query database for all duplicate product titles.  Determined by matching products_name fields of COUNT(*) > than 1.
$query = "SELECT products_name, COUNT(*) AS NumOccurrences FROM " . TABLE_PRODUCTS_DESCRIPTION . " GROUP BY products_name HAVING ( COUNT(products_name) > 1 )";
$query_info = tep_db_query($query);

//  Count the number of rows that are returned. (if any)
$num = tep_db_num_rows($query_info);
?>
<td class="pageHeading" width="100%" align="center"><?php echo $num . " " . HEADING_ACTION_DUPLICATE_NAME;?></td>
</tr>
</table>
<table width="100%" cellpadding="0" border="1" valign="top">
<tr>
<?php
//Let's find out if we have matches.  If not, we say so.
if ($num < 1) {
echo "<td width='100%' align='center' class='dataTableRow'>"; echo NO_PRODUCTS_NAMES; "</td>";
}else{
//  We have matches!  We list them all.
?>

<!-- Begin Column Headings //-->
<tr class="dataTableContent">
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_ID;?> *</td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_MODEL;?> *</td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_PRODUCT_NAME;?></td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_LANGUAGE_ID;?></td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_DATE_ADDED;?></td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_MANUFACTURER;?></td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_DELETE;?></td>
</tr>
<!-- End Column Headings //-->

<?php
// Begin first loop for results
for ($i=0; $i < $num; $i++) {
$row = tep_db_fetch_array($query_info);
$products_name = $row['products_name'];

//  Let's get products_id numbers (all instances) for products that match the product name(s) we found in the last query.
$second_query = "SELECT products_id, language_id FROM " . TABLE_PRODUCTS_DESCRIPTION . " WHERE products_name ='" .addslashes($products_name) . "'";
$second_query_info = tep_db_query($second_query);
$second_num = tep_db_num_rows($second_query_info);

//  Begin second loop to be sure we get all of the resulting product id numbers.
for ($j=0; $j < $second_num; $j++) {
$row2 = tep_db_fetch_array($second_query_info);
$products_id = $row2['products_id'];

//  Now let's get some more user friendly information to help identify these products more easily.
$final_query = "SELECT p.products_id, p.products_model, p.products_date_added, p.manufacturers_id, m.manufacturers_id, m.manufacturers_name FROM (" . TABLE_PRODUCTS . " p) LEFT JOIN " . TABLE_MANUFACTURERS . " m ON p.manufacturers_id = m.manufacturers_id WHERE p.products_id = '" . (int)$products_id . "'";
$final_query_info = tep_db_query($final_query);
$final_num = tep_db_num_rows($final_query_info);
$row3 = tep_db_fetch_array($final_query_info);
$products_date_added = $row3['products_date_added'];
$products_model = $row3['products_model'];
$manufacturers_name = $row3['manufacturers_name'];
$products_language = $row2['language_id'];
//  Show results.
echo '<tr>';
echo '<td align="center" class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"><a href="categories.php?action=new_product&pID=' . $products_id . ' " target=_blank">' . $products_id . '</a></td>';
echo '<td align="center" class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"><a href="categories.php?action=new_product&pID=' . $products_id . ' " target=_blank">' . $products_model . '</a></td>';
echo '<td>' . $products_name . '</td>';
echo '<td>' . $products_language . '</td>';
echo '<td>' . $products_date_added . '</td>';
echo '<td>' . $manufacturers_name . '</td>';
echo '<td><a href="database_check.php?action=delete_products&ref=1&products_id=' . $products_id . ' " onclick="return confirmDelete();"><img src="includes/languages/english/images/buttons/button_delete.gif" border="0" alt="Click to delete record"></a></td>';
echo '</tr>';
}
//  End second loop
}
//  End first loop for results
}
?>

<!-- Begin Next Menu //-->
<table width="100%" align="center">
<tr>
<td><a href="database_check.php"><img src="includes/languages/english/images/buttons/button_main.gif" border="0" alt="Back to start"></a></td>
<td><a href="database_check.php?action=2"><img src="includes/languages/english/images/buttons/button_next.gif" border="0" alt="Skip to the next step"></a></td>
</tr>
</table>
<!-- End Next Menu //-->

<?php
}

function products_no_titles() { // 2
?>

<table cellpadding="0" border="1" valign="top">
<tr>
<td width="100%"><table border="1" width="100%" cellspacing="0" cellpadding="0">
<tr class="dataTableHeadingRow">

<?php
//  Query database for related info on all products without titles.  Determined by checking products_name field for '' (empty).
$query = "SELECT p.products_id, p.products_model, p.products_price, p.products_date_added, p.manufacturers_id, pd.products_id AS PD_ID, pd.products_name, m.manufacturers_id, m.manufacturers_name FROM ((" . TABLE_PRODUCTS . " p) LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON p.products_id = pd.products_id) LEFT JOIN " . TABLE_MANUFACTURERS . " m ON p.manufacturers_id = m.manufacturers_id WHERE pd.products_name = '' OR pd.products_name IS NULL GROUP BY p.products_id";
$query_info = tep_db_query($query);

//  Count the number of rows that are returned. (if any)
$num = tep_db_num_rows($query_info);
?>
<td class="pageHeading" width="100%" align="center"><?php echo $num . " " . HEADING_ACTION_PRODUCTS_WITHOUT_NAME;?></td>
</tr>
</table>
<table width="100%" cellpadding="0" border="1" valign="top">
<tr>
<?php

//  Let's find out if we have matches.  If not, we say so.
if ($num < 1) {
echo "<td width='100%' class='dataTableRow' align='center'>"; echo NO_PRODUCTS_TITLES; "</td>";
}else{
//  We have matches!  We list them all.
?>

<!-- Begin Column Headings //-->
<tr class="dataTableContent">
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_ID;?> *</td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_MODEL;?> *</td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_PRICE;?></td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_DATE_ADDED;?></td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_MANUFACTURER;?></td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_DELETE;?></td>
</tr>
<?php
//  End column headings.

//  Begin loop for results
for ($i=0; $i < $num; $i++) {
$row = tep_db_fetch_array($query_info);
$products_id = $row['products_id'];
$products_model = $row['products_model'];
$products_price = $row['products_price'];
$products_date_added = $row['products_date_added'];
$manufacturers_name = $row['manufacturers_name'];

//  Show results.
echo '<tr>';
echo '<td align="center" class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"><a href="categories.php?action=new_product&pID='.$products_id.' "target=blank">'.$products_id.'</a></td>';
echo '<td align="center" class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"><a href="categories.php?action=new_product&pID=' . $products_id . ' " target=_blank">' . $products_model . '</a></td>';
echo '<td>'.$products_price.'</td>';
echo '<td>'.$products_date_added.'</td>';
echo '<td>'.$manufacturers_name.'</td>';
echo '<td><a href="database_check.php?action=delete_products&ref=2&products_id='.$products_id.'" onclick="return confirmDelete();"><img src="includes/languages/english/images/buttons/button_delete.gif" border="0" alt="Click to delete record"></a></td>';
echo '</tr>';
}
//  End loop for results
}
?>

<!-- Begin Next Menu //-->
<table width="100%" align="center">
<tr>
<td><a href="database_check.php"><img src="includes/languages/english/images/buttons/button_main.gif" border="0" alt="Back to start"></a></td>
<td><a href="database_check.php?action=3"><img src="includes/languages/english/images/buttons/button_next.gif" border="0" alt="Skip to the next step"></a></td>
</tr>
</table>
<!-- End Next Menu //-->

<?php
}
//  End products no titles function (Case 2)

//  Begin products price $0 function (Case 3)
function products_no_price() {
?>

<table cellpadding="0" border="1" valign="top">
<tr>
<td width="100%"><table border="1" width="100%" cellspacing="0" cellpadding="0">
<tr class="dataTableHeadingRow">
<?php

//  Query database for related info on all products a price of $0.  Determined by checking products_price field for value less than $0.01.
$query = "SELECT p.products_id, p.products_model, p.products_price, p.products_date_added, p.manufacturers_id, pd.products_id AS PD_ID, pd.products_name, m.manufacturers_id, m.manufacturers_name FROM ((" . TABLE_PRODUCTS . " p) LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON p.products_id = pd.products_id) LEFT JOIN " . TABLE_MANUFACTURERS . " m ON p.manufacturers_id = m.manufacturers_id WHERE p.products_price < .01 OR p.products_price IS NULL GROUP BY p.products_id";
$query_info = tep_db_query($query);

//  Count the number of rows that are returned. (if any)
$num = tep_db_num_rows($query_info);
?>
<td class="pageHeading" width="100%" align="center"><?php echo $num . " " . HEADING_ACTION_PRODUCTS_PRICE_0;?></td>
</tr>
</table>
<table width="100%" cellpadding="0" border="1" valign="top">
<tr>
<?php

//  Let's find out if we have matches.  If not, we say so.
if ($num < 1) {
echo "<td width='100%' class='dataTableRow' align='center'>"; echo PRODUCTS_PRICE_0; "</td>";
}else{
//  We have matches!  We list them all.
?>

<!-- Begin Column Headings //-->
<tr class="dataTableContent">
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_ID;?> *</td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_MODEL;?> *</td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_PRODUCT_NAME;?></td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_DATE_ADDED;?></td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_MANUFACTURER;?></td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_DELETE;?></td>
</tr>
<!-- End Column Headings //-->
<?php

//  Loop for results.
for ($i=0; $i < $num; $i++) {
$row = tep_db_fetch_array($query_info);
$products_id = $row['products_id'];
$products_model = $row['products_model'];
$products_name = $row['products_name'];
$products_date_added = $row['products_date_added'];
$manufacturers_name = $row['manufacturers_name'];

//  Show results.
echo '<tr>';
echo '<td align="center" class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"><a href="categories.php?action=new_product&pID='.$products_id.' "target=blank">'.$products_id.'</a></td>';
echo '<td align="center" class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"><a href="categories.php?action=new_product&pID=' . $products_id . ' " target=_blank">' . $products_model . '</a></td>';
echo '<td>'.$products_name.'</td>';
echo '<td>'.$products_date_added.'</td>';
echo '<td>'.$manufacturers_name.'</td>';
echo '<td><a href="database_check.php?action=delete_products&ref=3&products_id='.$products_id.'" onclick="return confirmDelete();"><img src="includes/languages/english/images/buttons/button_delete.gif" border="0" alt="Click to delete record"></a></td>';
echo '</tr>';
}
//End Loop for results
}
?>

<!-- Begin Next Menu //-->
<table width="100%" align="center">
<tr>
<td><a href="database_check.php"><img src="includes/languages/english/images/buttons/button_main.gif" border="0" alt="Back to start"></a></td>
<td><a href="database_check.php?action=4"><img src="includes/languages/english/images/buttons/button_next.gif" border="0" alt="Skip to the next step"></a></td>
</tr>
</table>
<!-- End Next Menu //-->

<?php
}
//  End products price $0 function (Case 3)

//  Begin products 0 weight function (Case 4)
function products_no_weight() {
?>

<table cellpadding="0" border="1" valign="top">
<tr>
<td width="100%"><table border="1" width="100%" cellspacing="0" cellpadding="0">
<tr class="dataTableHeadingRow">

<?php
//  Query database for related info on all products without weights.  Determined by checking products_weight field for value < 0.01.
$query = "SELECT p.products_id, p.products_model, p.products_price, p.products_date_added, p.manufacturers_id, p.products_weight, pd.products_id AS PD_ID, pd.products_name, m.manufacturers_id, m.manufacturers_name FROM ((" . TABLE_PRODUCTS . " p) LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON p.products_id = pd.products_id) LEFT JOIN " . TABLE_MANUFACTURERS . " m ON p.manufacturers_id = m.manufacturers_id WHERE p.products_weight < .01 OR p.products_weight IS NULL GROUP BY p.products_id";
$query_info = tep_db_query($query);

//  Count the number of rows that are returned. (if any)
$num = tep_db_num_rows($query_info);
?>
<td class="pageHeading" width="100%" align="center"><?php echo $num . " " . HEADING_ACTION_PRODUCTS_WEIGHT_0;?></td>
</tr>
</table>
<table width="100%" cellpadding="0" border="1" valign="top">
<tr>
<?php
//  Let's find out if we have matches.  If not, we say so.
if ($num < 1) {
echo "<td width='100%' class='dataTableRow' align='center'>"; 
echo PRODUCTS_WEIGHT_0 . "</td>";
}else{
//  We have matches!  We list them all.
?>

<!-- Begin Column Headings //-->
<tr class="dataTableContent" align="center">
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_ID;?> *</td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_MODEL;?> *</td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_PRODUCT_NAME;?></td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_DATE_ADDED;?></td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_MANUFACTURER;?></td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_STANDARD;?></td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_DELETE;?></td>
</tr>
<!-- End Column Headings //-->

<?php
// Begin loop for results.
for ($i=0; $i < $num; $i++) {
$row = tep_db_fetch_array($query_info);
$products_id = $row['products_id'];
$products_model = $row['products_model'];
$products_name = $row['products_name'];
$products_date_added = $row['products_date_added'];
$manufacturers_name = $row['manufacturers_name'];

//  Show results.
echo '<tr>';
echo '<td align="center" class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"><a href="categories.php?action=new_product&pID='.$products_id.' "target=blank">'.$products_id.'</a></td>';
echo '<td align="center" class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"><a href="categories.php?action=new_product&pID=' . $products_id . ' " target=_blank">' . $products_model . '</a></td>';
echo '<td>'.$products_name.'</td>';
echo '<td>'.$products_date_added.'</td>';
echo '<td>'.$manufacturers_name.'</td>';
echo '<td><a href="database_check.php?action=set_default_wt&ref=4&products_id='.$products_id.'" onclick="return confirmUpdate();"><img src="includes/languages/english/images/buttons/button_update.gif" border="0" alt="Click to set std wt"></a></td>';
echo '<td><a href="database_check.php?action=delete_products&ref=4&products_id='.$products_id.'" onclick="return confirmDelete();"><img src="includes/languages/english/images/buttons/button_delete.gif" border="0" alt="Click to delete record"></a></td>';
echo '</tr>';
}
//  End loop for results
}
?>

<!-- Begin Next Menu //-->
<table width="100%" align="center">
<tr>
<td><a href="database_check.php"><img src="includes/languages/english/images/buttons/button_main.gif" border="0" alt="Back to start"></a></td>
<td><a href="database_check.php?action=5"><img src="includes/languages/english/images/buttons/button_next.gif" border="0" alt="Skip to the next step"></a></td>
</tr>
</table>
<!-- End Next Menu //-->

<?php
}
//  End products 0 weight function (Case 4).

//  Begin products no category function (Case 5)
function products_no_category() {
?>

<table cellpadding="0" border="1" valign="top">
<tr>
<td width="100%"><table border="1" width="100%" cellspacing="0" cellpadding="0">
<tr class="dataTableHeadingRow">

<?php
//  Query database for related info on all products without categories.  Determined by finding p.products_id not in pc.products_id.

$query = "SELECT p.products_id, p.products_model, p.products_price, p.products_date_added, p.manufacturers_id, p.products_id, pd.products_id, pd.products_name, m.manufacturers_id, m.manufacturers_name FROM " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_MANUFACTURERS . " m WHERE p.products_id = pd.products_id AND p.manufacturers_id = m.manufacturers_id AND p.products_id NOT IN(SELECT products_id FROM " . TABLE_PRODUCTS_TO_CATEGORIES . ") GROUP BY p.products_id";

$query = "SELECT p.products_id, p.products_model, p.products_price, p.products_date_added, p.manufacturers_id, pc.products_id AS PC_ID, pd.products_id AS PD_ID, pd.products_name, m.manufacturers_id, m.manufacturers_name FROM (((" . TABLE_PRODUCTS . " p) LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON p.products_id = pd.products_id) LEFT JOIN " . TABLE_MANUFACTURERS . " m ON p.manufacturers_id = m.manufacturers_id) LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " pc on p.products_id = pc.products_id WHERE  pc.products_id IS NULL GROUP BY p.products_id ORDER BY p.products_id";

$query_info = tep_db_query($query);

//  Count the number of rows that are returned. (if any)
$num = tep_db_num_rows($query_info);
?>
<td class="pageHeading" width="100%" align="center"><?php echo $num . " " . HEADING_ACTION_PRODUCTS_NO_CATEGORY;?></td>
</tr>
</table>
<table width="100%" cellpadding="0" border="1" valign="top">
<tr>
<?php
//  Let's find out if we have matches.  If not, we say so.
if ($num < 1) {
	echo "<td width='100%' class='dataTableRow' align='center'>"; 
	echo PRODUCTS_NO_CATEGORY . "</td>";
}else{
//  We have matches!  We list them all.
?>

<!-- Begin Column Headings //-->
<tr class="dataTableContent" align="center">
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_ID;?> *</td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_MODEL;?> *</td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_PRODUCT_NAME;?></td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_DATE_ADDED;?></td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_MANUFACTURER;?></td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_DEFAULT_CATEGORY;?></td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_DELETE;?></td>
</tr>
<!-- End Column Headings //-->

<?php
//  Begin loop for results.
for ($i=0; $i < $num; $i++) {
$row = tep_db_fetch_array($query_info);
$products_id = $row['products_id'];
$products_model = $row['products_model'];
$products_name = $row['products_name'];
$products_date_added = $row['products_date_added'];
$manufacturers_name = $row['manufacturers_name'];

//  Show results.
echo '<tr>';
echo '<td class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"><a href="categories.php?action=new_product&pID='.$products_id.' "target=blank">'.$products_id.'</a></td>';
echo '<td class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"><a href="categories.php?action=new_product&pID=' . $products_id . ' " target=_blank">' . $products_model . '</a></td>';
echo '<td>'.$products_name.'</td>';
echo '<td>'.$products_date_added.'</td>';
echo '<td>'.$manufacturers_name.'</td>';
echo '<td><a href="database_check.php?action=set_default_cat&ref=5&products_id='.$products_id.'" onclick="return confirmUpdate();">','<img src="includes/languages/english/images/buttons/button_update.gif" border="0" alt="Click to delete record"></a></td>';
echo '<td><a href="database_check.php?action=delete_products&ref=5&products_id='.$products_id.'" onclick="return confirmDelete();">','<img src="includes/languages/english/images/buttons/button_delete.gif" border="0" alt="Click to delete record"></a></td>';
echo '</tr>';
}
//  End loop for results.
}
?>

<!-- Begin Next Menu //-->
<table width="100%" align="center">
<tr>
<td><a href="database_check.php"><img src="includes/languages/english/images/buttons/button_main.gif" border="0" alt="Back to start"></a></td>
<td><a href="database_check.php?action=6"><img src="includes/languages/english/images/buttons/button_next.gif" border="0" alt="Skip to the next step"></a></td>
</tr>
</table>
<!-- End Next Menu //-->

<?php
}
//  End products no category function (Case 5).

//  Begin categories no products function (Case 6)
function categories_no_products() {
?>

<table cellpadding="0" border="1" valign="top">
<tr>
<td width="100%"><table border="1" width="100%" cellspacing="0" cellpadding="0">
<tr class="dataTableHeadingRow">

<?php

//  Query database for related info on all categories with no products.  Determined by finding categories (categories_id) not in products_to_categories (categories_id).

$query = "SELECT c.categories_id, c.parent_id, c.date_added, cd.categories_id, cd.categories_name FROM ((" . TABLE_CATEGORIES . " c) LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd ON c.categories_id = cd.categories_id) LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " pc ON c.categories_id =  pc.categories_id WHERE pc.categories_id IS NULL GROUP BY c.categories_id ORDER BY c.categories_id";

$query_info = tep_db_query($query);

//  Count the number of rows that are returned. (if any)
$num = tep_db_num_rows($query_info);
?>
<td class="pageHeading" width="100%" align="center"><?php echo $num . " " . HEADING_ACTION_CATEGORIES_NO_PRODUCTS;?></td>
</tr>
</table>
<table width="100%" cellpadding="0" border="1" valign="top">
<tr>
<?php

//  Let's find out if we have matches.  If not, we say so.
if ($num < 1) {
echo "<td width='100%' class='dataTableRow' align='center'>"; 
echo CATEGORIES_NO_PRODUCTS . "</td>";
}else{
//  We have matches!  We list them all.
?>

<!-- Begin Column Headings //-->
<tr class="dataTableContent" align="center">
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_ID;?> *</td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_CATEGORY_NAME;?></td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_PARENT_CATEGORY;?> *</td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_SUB_CATEGORY;?></td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_DATE_ADDED;?></td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_DELETE;?></td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_ADD_PRODUCTS;?></td>
</tr>
<!-- End Column Headings //-->

<?php
//  Begin loop for results.
for ($i=0; $i < $num; $i++) {
$row = tep_db_fetch_array($query_info);
$categories_id = $row['categories_id'];
$categories_name = $row['categories_name'];
$cPath = $row['parent_id'];

$querya = "SELECT c.categories_id, c.parent_id FROM " . TABLE_CATEGORIES . " c WHERE c.parent_id = '".$categories_id."' ORDER BY c.categories_id";

$query_infoa = tep_db_query($querya);

//  Count the number of rows that are returned. (if any)
$numa = tep_db_num_rows($query_infoa);

$date_added = $row['date_added'];

//  Show results.
echo '<tr>';
echo '<td align="center" class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"><a href="categories.php?cPath='.$cPath.'&cID='.$categories_id.' "target=blank">'.$categories_id.'</a></td>';
echo '<td>'.$categories_name.'</td>';
echo '<td align="center" class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"><a href="categories.php?cPath='.$cPath.' "target=blank">'.$cPath.'</a></td>';
echo '<td align="center">'.$numa.'</td>';
echo '<td>'.$date_added.'</td>';
echo '<td align="center"><a href="database_check.php?action=delete_categories&ref=6&categories_id='.$categories_id.'" onclick="return confirmDelete();"><img src="includes/languages/english/images/buttons/button_delete.gif" border="0" alt="Click to delete category"></a></td>';
echo '<td><a href="categories.php?cPath='.$cPath.'_'.$categories_id.'&action=new_product "target=blank"><img src="includes/languages/english/images/buttons/button_new_product.gif" border="0" alt="Click to add products"></a></td>';
echo '</tr>';
}
//  End loop for results.
}
?>

<!-- Begin Next Menu //-->
<table width="100%" align="center">
<tr>
<td><a href="database_check.php"><img src="includes/languages/english/images/buttons/button_main.gif" border="0" alt="Back to start"></a></td>
<td><a href="database_check.php?action=7"><img src="includes/languages/english/images/buttons/button_next.gif" border="0" alt="Skip to the next step"></a></td>
</tr>
</table>
<!-- End Next Menu //-->

<?php
}
//  End products no category function (Case 6).

//  Begin duplicate products to categories function (Case 7)
function dup_prodtocat() {
?>

<table cellpadding="0" border="1" valign="top">
<tr>
<td width="100%"><table border="1" width="100%" cellspacing="0" cellpadding="0">
<tr class="dataTableHeadingRow">
<?php
//  Query database for products to categories duplicates.  Determined by finding duplicates instances of the same (products_id) AND (categories_id) together from products_to_categories > 1.
$query = "SELECT products_id, categories_id, COUNT(products_id + categories_id) AS NumberOccurances FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " GROUP BY products_id, categories_id HAVING ( COUNT(*) > 1)";
$query_info = tep_db_query($query);

//Count the number of rows returned.
$num = tep_db_num_rows($query_info);
?>
<td class="pageHeading" width="100%" align="center"><?php echo $num . " " . HEADING_ACTION_PRODUCTS_TO_CATEGORIES;?></td>
</tr>
</table>
<table width="100%" cellpadding="0" border="1" valign="top">
<tr>
<?php

//  Let's find out if we have matches.  If not, we say so.
if ($num < 1) {
	echo "<td width='100%' class='dataTableRow' align='center'>"; 
	echo PRODUCTS_TO_CATEGORIES . "</td>";
}else{
//  We have matches!  We list them all.
?>

<!-- Begin Column Headings //-->
<tr class="dataTableContent" align="center">
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_ID;?></td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_CATEGORY_ID;?></td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_PRODUCT_NAME;?></td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_CATEGORY_NAME;?></td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_DELETE;?></td>
</tr>
<!-- End Column Headings //-->

<?php
//  Begin loop for results.
for ($i=0; $i < $num; $i++) {
$row = tep_db_fetch_array($query_info);
$products_id = $row['products_id'];
$products_model = $row['products_model'];
$categories_id = $row['categories_id'];

//  Let's get more user friendly information based on the results of the first query.
$second_query = "SELECT p.products_id, p.products_model, pd.products_id, pd.products_name, c.categories_id, c.parent_id, cd.categories_name FROM " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd WHERE p.products_id = '" . (int)$products_id . "' AND pd.products_id = '" . (int)$products_id . "' AND c.categories_id = '" . (int)$categories_id . "' AND cd.categories_id = '" . (int)$categories_id . "'";
$second_query_info = mysqli_query($second_query);
$row2 = tep_db_fetch_array($second_query_info);
$cPath = $row2['parent_id'];
$products_name = $row2['products_name'];
$categories_name = $row2['categories_name'];

//  Show results.
echo '<tr>';
echo '<td class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"><a href="categories.php?action=new_product&pID= '.$products_id.' " target=blank">'.$products_id.'</a></td>';
echo '<td class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"><a href="categories.php?cPath='.$cPath.'_'.$categories_id.' " target=blank">'.$categories_id.'</a></td>';
echo '<td>'.$products_name.'</td>';
echo '<td>'.$categories_name.'</td>';
echo '<td><a href="database_check.php?action=delete_prodtocat&ref=7&products_id='.$products_id.'&categories_id='.$categories_id.'" onclick="return confirmDelete();"><img src="includes/languages/english/images/buttons/button_delete.gif" border="0" alt="Click to delete category"></a></td>';
echo '</tr>';
}
//  End loop for results
}
?>

<!-- Begin Next Menu //-->
<table width="100%" align="center">
<tr>
<td><a href="database_check.php"><img src="includes/languages/english/images/buttons/button_main.gif" border="0" alt="Back to start"></a></td>
<td><a href="database_check.php?action=8"><img src="includes/languages/english/images/buttons/button_next.gif" border="0" alt="Skip to the next step"></a></td>
</tr>
</table>
<!-- End Next Menu //-->

<?php
}
//  End products no category function (Case 7).


function categories_no_descriptions() {
?>

<table cellpadding="0" border="1" valign="top">
<tr>
<td width="100%"><table border="1" width="100%" cellspacing="0" cellpadding="0">
<tr class="dataTableHeadingRow">

<?php
$last_part='';
$catCount = 0;
$prodCount = 0;
$ptc_query = tep_db_query("select c.categories_id as categories_id, cd.categories_name as categories_name, c.parent_id as parent_id from (" . TABLE_CATEGORIES . " c) left join " . TABLE_CATEGORIES_DESCRIPTION . " cd on  c.categories_id = cd.categories_id where isnull(cd.categories_name) or categories_name = '' order by  c.categories_id");

//  Count the number of rows that are returned. (if any)
$num = tep_db_num_rows($ptc_query);
?>
<td class="pageHeading" width="100%" align="center"><?php echo $num . " " . HEADING_ACTION_CATEGORIES_NO_DESCRIPTION;?></td>
</tr>
</table>
<table width="100%" cellpadding="0" border="1" valign="top">
<tr>
<?php

//  Let's find out if we have matches.  If not, we say so.
if ($num < 1) {
	echo "<td width='100%' class='dataTableRow' align='center'>"; 
	echo CATEGORIES_NO_DESCRIPTION . "</td>";
}else{
//  We have matches!  We list them all.
?>

<!-- Begin Column Headings //-->
				<tr>
					<th class="smallText" align="center" width="20%">Parent</th>
					<th class="smallText" align="center" width="20%">Category No *</th>
					<th class="smallText" align="center" width="50%">Category Name</th>
				</tr>
<!-- End Column Headings //-->

<?php
//  Begin loop for results.
	while ($row = tep_db_fetch_array($ptc_query)) { 
?> 
				<tr>
					<td align="center"><?php echo $row['parent_id']; ?></td>
					<td align="center" class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"><a href="categories.php?cPath=<?php echo $row['parent_id']; ?>&cID=<?php echo $row['categories_id']; ?>&action=edit_category "target=blank"><?php echo $row['categories_id']; ?></a></td>
					<td><?php echo $row['categories_name']; ?></td>
				</tr>
<?php
//			delete_categories_only
    } 

//  End loop for results.
}
?>

<!-- Begin Next Menu //-->
<table width="100%" align="center">
<tr>
<td align="center"><a href="database_check.php"><img src="includes/languages/english/images/buttons/button_main.gif" border="0" alt="Back to start"></a></td>
<td align="center"><a href="database_check.php?action=9"><img src="includes/languages/english/images/buttons/button_next.gif" border="0" alt="Skip to the next step"></a></td>
<td align="center"><a href="database_check.php?action=delete_all_categories_only&ref=8" onclick="return confirmDelete();"><img src="includes/languages/english/images/buttons/button_delete.gif" border="0" alt="Click to delete category"></a></td>
</tr>
</table>
<!-- End Next Menu //-->

<?php
}

function category_descriptions_no_category() {
?>

<table cellpadding="0" border="1" valign="top">
<tr>
<td width="100%"><table border="1" width="100%" cellspacing="0" cellpadding="0">
<tr class="dataTableHeadingRow">

<?php
$last_part='';
$catCount = 0;
$prodCount = 0;
$ptc_query = tep_db_query("select cd.categories_id as categories_id, c.categories_id as the_id, cd.categories_name as categories_name from (" . TABLE_CATEGORIES_DESCRIPTION . " cd) left join " . TABLE_CATEGORIES . " c on  c.categories_id = cd.categories_id where isnull(c.categories_id) order by  cd.categories_id");

//  Count the number of rows that are returned. (if any)
$num = tep_db_num_rows($ptc_query);
?>
<td class="pageHeading" width="100%" align="center"><?php echo $num . " " . HEADING_ACTION_CATEGORIES_DESCRIPTION_NO_CATEGORY;?></td>
</tr>
</table>
<table width="100%" cellpadding="0" border="1" valign="top">
<tr>
<?php

//  Let's find out if we have matches.  If not, we say so.
if ($num < 1) {
	echo "<td width='100%' class='dataTableRow' align='center'>"; 
	echo CATEGORIES_DESCRIPTION_NO_CATEGORY . "</td>";
}else{
//  We have matches!  We list them all.
?>

<!-- Begin Column Headings //-->
				<tr>
					<th class="smallText" align="center">Category No</th>
					<th class="smallText" align="center">Category Name</th>
				</tr>
<!-- End Column Headings //-->

<?php
//  Begin loop for results.
	while ($row = tep_db_fetch_array($ptc_query)) { 
?> 
				<tr>
					<td align="center" ><?php echo $row['categories_id']; ?></td>
					<td><?php echo $row['categories_name']; ?></td>
				</tr>
<?php
//			tep_db_query("delete from " . TABLE_CATEGORIES . " where categories_id = '" . $row['categories_id'] . "'");
        
    } 

//  End loop for results.
}
?>

<!-- Begin Next Menu //-->
<table width="100%" align="center">
<tr>
<td align="center"><a href="database_check.php"><img src="includes/languages/english/images/buttons/button_main.gif" border="0" alt="Back to start"></a></td>
<td align="center"><a href="database_check.php?action=10"><img src="includes/languages/english/images/buttons/button_next.gif" border="0" alt="Skip to the next step"></a></td>
<td align="center"><a href="database_check.php?action=delete_all_categories_desc&ref=9" onclick="return confirmDelete();"><img src="includes/languages/english/images/buttons/button_delete.gif" border="0" alt="Click to delete category"></a></td>
</tr>
</table>
<!-- End Next Menu //-->

<?php
}

function category_no_parent() {
?>

<table cellpadding="0" border="1" valign="top">
<tr>
<td width="100%">
<table border="1" width="100%" cellspacing="0" cellpadding="0">
<tr class="dataTableHeadingRow">

<?php
$last_part='';
$catCount = 0;
$prodCount = 0;
$ptc_query = tep_db_query("select c.categories_id as categories_id, c.parent_id, cp.categories_id as parent_categories_id FROM " . TABLE_CATEGORIES . " c left join " . TABLE_CATEGORIES . " cp ON cp.categories_id = c.parent_id WHERE c.parent_id <> 0 AND isnull( cp.categories_id )");

//  Count the number of rows that are returned. (if any)
$num = tep_db_num_rows($ptc_query);
?>
<td class="pageHeading" width="100%" align="center"><?php echo $num . " " . HEADING_ACTION_CATEGORIES_NO_PARENT;?></td>
</tr>
</table>
<table width="100%" cellpadding="0" border="1" valign="top">
<tr>
<?php

//  Let's find out if we have matches.  If not, we say so.
if ($num < 1) {
	echo "<td width='100%' class='dataTableRow' align='center'>"; 
	echo CATEGORIES_NO_PARENT . "</td>";
}else{
//  We have matches!  We list them all.
?>

<!-- Begin Column Headings //-->
					<th class="smallText" align="center" width="50%">Parent</th>
					<th class="smallText" align="center" width="50%">Category No</th>
				</tr>
<!-- End Column Headings //-->

<?php
//  Begin loop for results.
	while ($row = tep_db_fetch_array($ptc_query)) { 
?> 
				<tr>
					<td align="center"><?php echo $row['parent_id']; ?></td>
					<td align="center"><?php echo $row['categories_id']; ?></td>
				</tr>
<?php
//			tep_db_query("delete from " . TABLE_CATEGORIES . " where categories_id = '" . $row['categories_id'] . "'");
        
    } 

//  End loop for results.
}
?>

<!-- Begin Next Menu //-->
<table width="100%" align="center">
<tr>
<td align="center"><a href="database_check.php"><img src="includes/languages/english/images/buttons/button_main.gif" border="0" alt="Back to start"></a></td>
<td align="center"><a href="database_check.php?action=11"><img src="includes/languages/english/images/buttons/button_next.gif" border="0" alt="Skip to the next step"></a></td>
<td align="center"><a href="database_check.php?action=delete_all_categories&ref=10" onclick="return confirmDelete();"><img src="includes/languages/english/images/buttons/button_delete.gif" border="0" alt="Click to delete category"></a></td>
</tr>
</table>
<!-- End Next Menu //-->

<?php
}

function category_no_image() {
?>

<table cellpadding="0" border="1" valign="top">
<tr>
<td width="100%"><table border="1" width="100%" cellspacing="0" cellpadding="0">
<tr class="dataTableHeadingRow">

<?php

$query = tep_db_query("select c.categories_id, c.parent_id, categories_image from " . TABLE_CATEGORIES . " c where c.categories_image = '' or isnull(c.categories_image)");

//  Count the number of rows that are returned. (if any)
$num = tep_db_num_rows($query);
?>
<td class="pageHeading" width="100%" align="center"><?php echo $num . " " . HEADING_ACTION_CATEGORIES_NO_IMAGE;?></td>
</tr>
</table>
<table width="100%" cellpadding="0" border="1" valign="top">
<tr>
<?php
//  Let's find out if we have matches.  If not, we say so.
if ($num < 1) {
echo "<td width='100%' class='dataTableRow' align='center'>"; 
echo CATEGORIES_NO_IMAGE . "</td>";
}else{
//  We have matches!  We list them all.
?>
				<tr>
					<th class="smallText" align="center" >Parent</th>
					<th class="smallText" align="center" >Category No *</th>
					<th class="smallText" align="center" >Category Image</th>
					<th class="smallText" align="center" >Action</th>
				</tr>
<?php
//  Begin loop for results.
	while ($row = tep_db_fetch_array($query)) { 
?> 
				<tr>
					<td align="center"><?php echo $row['parent_id']; ?></td>
					<td align="center" class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"><a href="categories.php?cPath=<?php echo $row['parent_id']; ?>&cID=<?php echo $row['categories_id']; ?>&action=edit_category "target=blank"><?php echo $row['categories_id']; ?></a></td>
					<td><?php echo $row['categories_image']; ?></td>
					<td align="center"><a href="database_check.php?action=set_default_image&ref=11&categories_id=<?php echo $row['categories_id'];?>" onclick="return confirmUpdate();"><img src="includes/languages/english/images/buttons/button_update.gif" border="0" alt="Click to update image to default"></a></td>
				</tr>
<?php
    }
}
?>

<!-- Begin Next Menu //-->
<table width="100%" align="center">
<tr>
<td align="center"><a href="database_check.php"><img src="includes/languages/english/images/buttons/button_main.gif" border="0" alt="Back to start"></a></td>
<td align="center"><a href="database_check.php?action=12"><img src="includes/languages/english/images/buttons/button_next.gif" border="0" alt="Skip to the next step"></a></td>
<td align="center"><a href="database_check.php?action=set_all_default_image&ref=11" onclick="return confirmUpdate();"><img src="includes/languages/english/images/buttons/button_update.gif" border="0" alt="Click to update all missing images to default"></a></td>
</tr>
</table>
<!-- End Next Menu //-->

<?php
}
function non_existant_cat_on_ptc() { //  12
?>

<table cellpadding="0" border="1" valign="top">
<tr>
<td width="100%"><table border="1" width="100%" cellspacing="0" cellpadding="0">
<tr class="dataTableHeadingRow">

<?php

$query = tep_db_query("select ptc.categories_id as categories_id, ptc.products_id as products_id, c.parent_id as parent_id from (" . TABLE_PRODUCTS_TO_CATEGORIES . " ptc) left join " . TABLE_CATEGORIES . " c on  ptc.categories_id = c.categories_id where isnull(c.parent_id) order by  ptc.categories_id");

//  Count the number of rows that are returned. (if any)
$num = tep_db_num_rows($query);
?>
<td class="pageHeading" width="100%" align="center"><?php echo $num . " " . HEADING_ACTION_NON_EXISTANT_CAT_ON_PTC;?></td>
</tr>
</table>
<table width="100%" cellpadding="0" border="1" valign="top">
<tr>
<?php
//  Let's find out if we have matches.  If not, we say so.
if ($num < 1) {
echo "<td width='100%' class='dataTableRow' align='center'>"; 
echo NON_EXISTANT_CAT_ON_PTC . "</td>";
}else{
//  We have matches!  We list them all.
?>
				<tr>
					<th class="smallText" align="center">Category No</th>
					<th class="smallText" align="center" >Product ID</th>
					<th class="smallText" align="center" >Action</th>
				</tr>
<?php
//  Begin loop for results.
	while ($row = tep_db_fetch_array($query)) { 
?> 
				<tr>
					<td align="center"><?php echo $row['categories_id']; ?></td>
					<td align="center"><?php echo $row['products_id']; ?></td>
					<td align="center"><a href="database_check.php?action=delete_prodtocat&ref=12&categories_id=<?php echo $row['categories_id'];?>&products_id=<?php echo $row['products_id'];?>" onclick="return confirmDelete();"><img src="includes/languages/english/images/buttons/button_delete.gif" border="0" alt="Click to delete image to default"></a></td>
				</tr>
<?php
	}
//			tep_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where categories_id = '" . $row['categories_id'] . "'");
}
?>

<!-- Begin Next Menu //-->
<table width="100%" align="center">
<tr>
<td align="center"><a href="database_check.php"><img src="includes/languages/english/images/buttons/button_main.gif" border="0" alt="Back to start"></a></td>
<td align="center"><a href="database_check.php?action=13"><img src="includes/languages/english/images/buttons/button_next.gif" border="0" alt="Skip to the next step"></a></td>
<td align="center"><a href="database_check.php?action=delete_prodtocat_cat&ref=12" onclick="return confirmDelete();"><img src="includes/languages/english/images/buttons/button_delete.gif" border="0" alt="Click to delete ptc records"></a></td>
</tr>
</table>
<!-- End Next Menu //-->

<?php
}

function non_existant_prod_on_ptc() { //13
?>

<table cellpadding="0" border="1" valign="top">
<tr>
<td width="100%"><table border="1" width="100%" cellspacing="0" cellpadding="0">
<tr class="dataTableHeadingRow">

<?php

$query = tep_db_query("select ptc.products_id as products_id, ptc.categories_id as categories_id, p.products_id as the_id from (" . TABLE_PRODUCTS_TO_CATEGORIES . " ptc) left join " . TABLE_PRODUCTS . " p on  ptc.products_id = p.products_id where isnull(p.products_id) order by  ptc.products_id");

//  Count the number of rows that are returned. (if any)
$num = tep_db_num_rows($query);
?>
<td class="pageHeading" width="100%" align="center"><?php echo $num . " " . HEADING_ACTION_NON_EXISTANT_PROD_ON_PTC;?></td>
</tr>
</table>
<table width="100%" cellpadding="0" border="1" valign="top">
<tr>
<?php
//  Let's find out if we have matches.  If not, we say so.
if ($num < 1) {
echo "<td width='100%' class='dataTableRow' align='center'>"; 
echo NON_EXISTANT_PROD_ON_PTC . "</td>";
}else{
//  We have matches!  We list them all.
?>
				<tr>
					<th class="smallText" align="center" >Category No</th>
					<th class="smallText" align="center" >Product ID</th>
					<th class="smallText" align="center" >Action</th>
				</tr>
<?php
//  Begin loop for results.
	while ($row = tep_db_fetch_array($query)) { 
?> 
				<tr>
					<td align="center"><?php echo $row['categories_id']; ?></td>
					<td align="center"><?php echo $row['products_id']; ?></td>
					<td align="center"><a href="database_check.php?action=delete_prodtocat&ref=13&categories_id=<?php echo $row['categories_id'];?>&products_id=<?php echo $row['products_id'];?>" onclick="return confirmDelete();"><img src="includes/languages/english/images/buttons/button_delete.gif" border="0" alt="Click to delete image to default"></a></td>
				</tr>
<?php
    }
//			tep_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . $row['products_id']."'");
}
?>

<!-- Begin Next Menu //-->
<table width="100%" align="center">
<tr>
<td align="center"><a href="database_check.php"><img src="includes/languages/english/images/buttons/button_main.gif" border="0" alt="Back to start"></a></td>
<td align="center"><a href="database_check.php?action=14"><img src="includes/languages/english/images/buttons/button_next.gif" border="0" alt="Skip to the next step"></a></td>
<td align="center"><a href="database_check.php?action=delete_prodtocat_prod&ref=13" onclick="return confirmDelete();"><img src="includes/languages/english/images/buttons/button_delete.gif" border="0" alt="Click to delete ptc records"></a></td>
</tr>
</table>
<!-- End Next Menu //-->

<?php
}

function category_desc_gt_32() { // 14
?>

<table cellpadding="0" border="1" valign="top">
<tr>
<td width="100%"><table border="1" width="100%" cellspacing="0" cellpadding="0">
<tr class="dataTableHeadingRow">

<?php

$query = tep_db_query("select cd.categories_id, cd.categories_name as categories_name, length(cd.categories_name) as name_length from " . TABLE_CATEGORIES_DESCRIPTION . " cd where length(cd.categories_name) > 31 order by categories_name");

//  Count the number of rows that are returned. (if any)
$num = tep_db_num_rows($query);
?>
<td class="pageHeading" width="100%" align="center"><?php echo $num . " " . HEADING_ACTION_CATEGORY_DESC_SIZE;?></td>
</tr>
</table>
<table width="100%" cellpadding="0" border="1" valign="top">
<tr>
<?php
//  Let's find out if we have matches.  If not, we say so.
if ($num < 1) {
echo "<td width='100%' class='dataTableRow' align='center'>"; 
echo CATEGORY_DESC_SIZE . "</td>";
}else{
//  We have matches!  We list them all.
?>
				<tr>
					<th class="smallText" align="center" >Category No *</th>
					<th class="smallText" align="center" >Name</th>
					<th class="smallText" align="center" >Size</th>
					<th class="smallText" align="center" >Action</th>
				</tr>
<?php
//  Begin loop for results.
	while ($row = tep_db_fetch_array($query)) { 
?> 
				<tr>
					<td align="center"><?php echo $row['categories_id']; ?></td>
					<td><?php echo $row['categories_name']; ?></td>
					<td><?php echo $row['name_length']; ?></td>
					<td align="center"><a href="database_check.php?action=delete_categories&ref=14&categories_id=<?php echo $row['categories_id'];?>" onclick="return confirmDelete();"><img src="includes/languages/english/images/buttons/button_delete.gif" border="0" alt="Click to delete category"></a></td>
				</tr>
<?php
//			tep_db_query("delete from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $row['categories_id'] . "'");
//			tep_db_query("delete from " . TABLE_CATEGORIES . " where categories_id = '" . $row['categories_id'] . "'");
    }
}
?>

<!-- Begin Next Menu //-->
<table width="100%" align="center">
<tr>
<td align="center"><a href="database_check.php"><img src="includes/languages/english/images/buttons/button_main.gif" border="0" alt="Back to start"></a></td>
<td align="center"><a href="database_check.php?action=15"><img src="includes/languages/english/images/buttons/button_next.gif" border="0" alt="Skip to the next step"></a></td>
<td align="center"><a href="database_check.php?action=delete_all_categories_size&ref=14" onclick="return confirmDelete();"><img src="includes/languages/english/images/buttons/button_delete.gif" border="0" alt="Click to delete category records"></a></td>
</tr>
</table>
<!-- End Next Menu //-->

<?php
}

function products_no_ptc() { // 15
?>

<table cellpadding="0" border="1" valign="top">
<tr>
<td width="100%"><table border="1" width="100%" cellspacing="0" cellpadding="0">
<tr class="dataTableHeadingRow">

<?php

$query = tep_db_query("select ptc.products_id as products_id, p.products_id as the_id from (" . TABLE_PRODUCTS . " p) left join " . TABLE_PRODUCTS_TO_CATEGORIES . " ptc on  ptc.products_id = p.products_id where ptc.products_id is null order by  p.products_id");

//  Count the number of rows that are returned. (if any)
$num = tep_db_num_rows($query);
?>
<td class="pageHeading" width="100%" align="center"><?php echo $num . " " . HEADING_ACTION_PROD_NO_PTC;?></td>
</tr>
</table>
<table width="100%" cellpadding="0" border="1" valign="top">
<tr>
<?php
//  Let's find out if we have matches.  If not, we say so.
if ($num < 1) {
echo "<td width='100%' class='dataTableRow' align='center'>"; 
echo PROD_NO_PTC . "</td>";
}else{
//  We have matches!  We list them all.
?>
				<tr>
					<th class="smallText" align="center">Product No</th>
					<th class="smallText" align="center">Action</th>
				</tr>
<?php
//  Begin loop for results.
	while ($row = tep_db_fetch_array($query)) { 
?> 
				<tr>
					<td align="center"><?php echo $row['the_id']; ?></td>
					<td align="center"><a href="database_check.php?action=delete_products&ref=15&products_id=<?php echo $row['the_id']; ?>" onclick="return confirmDelete();"><img src="includes/languages/english/images/buttons/button_delete.gif" border="0" alt="Click to delete products"></a></td>
				</tr>
<?php
    }
}
?>

<!-- Begin Next Menu //-->
<table width="100%" align="center">
<tr>
<td align="center"><a href="database_check.php"><img src="includes/languages/english/images/buttons/button_main.gif" border="0" alt="Back to start"></a></td>
<td align="center"><a href="database_check.php?action=16"><img src="includes/languages/english/images/buttons/button_next.gif" border="0" alt="Skip to the next step"></a></td>
<td align="center"><a href="database_check.php?action=delete_all_prod&ref=15" onclick="return confirmDelete();"><img src="includes/languages/english/images/buttons/button_delete.gif" border="0" alt="Click to delete category records"></a></td>
</tr>
</table>
<!-- End Next Menu //-->

<?php
}

function products_no_descriptions() {
?>

<table cellpadding="0" border="1" valign="top">
<tr>
<td width="100%"><table border="1" width="100%" cellspacing="0" cellpadding="0">
<tr class="dataTableHeadingRow">

<?php
$last_part='';
$catCount = 0;
$prodCount = 0;
$ptc_query = tep_db_query("select p.products_id as products_id, pd.products_name as products_name from (" . TABLE_PRODUCTS . " p) left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on  p.products_id = pd.products_id where isnull(pd.products_name) or products_name = '' order by  p.products_id");

//  Count the number of rows that are returned. (if any)
$num = tep_db_num_rows($ptc_query);
?>
<td class="pageHeading" width="100%" align="center"><?php echo $num . " " . HEADING_ACTION_PRODUCTS_NO_DESCRIPTION;?></td>
</tr>
</table>
<table width="100%" cellpadding="0" border="1" valign="top">
<tr>
<?php

//  Let's find out if we have matches.  If not, we say so.
if ($num < 1) {
	echo "<td width='100%' class='dataTableRow' align='center'>"; 
	echo PRODUCTS_NO_DESCRIPTION . "</td>";
}else{
//  We have matches!  We list them all.
?>

<!-- Begin Column Headings //-->
				<tr>
					<th class="smallText" align="center">Product No *</th>
					<th class="smallText" align="center">Product Name</th>
				</tr>
<!-- End Column Headings //-->

<?php
//  Begin loop for results.
	while ($row = tep_db_fetch_array($ptc_query)) { 
?> 
				<tr>
					<td align="center" class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"><a href="categories.php?action=new_product&pID=<?php echo $row['products_id']; ?> "target=blank"><?php echo $row['products_id']; ?></a></td>
					<td><?php echo $row['products_name']; ?></td>
				</tr>
<?php
    } 

//  End loop for results.
}
?>

<!-- Begin Next Menu //-->
<table width="100%" align="center">
<tr>
<td align="center"><a href="database_check.php"><img src="includes/languages/english/images/buttons/button_main.gif" border="0" alt="Back to start"></a></td>
<td align="center"><a href="database_check.php?action=17"><img src="includes/languages/english/images/buttons/button_next.gif" border="0" alt="Skip to the next step"></a></td>
<td align="center"><a href="database_check.php?action=delete_all_products_only&ref=8" onclick="return confirmDelete();"><img src="includes/languages/english/images/buttons/button_delete.gif" border="0" alt="Click to delete product"></a></td>
</tr>
</table>
<!-- End Next Menu //-->

<?php
}

function product_descriptions_no_product() {
?>

<table cellpadding="0" border="1" valign="top">
<tr>
<td width="100%"><table border="1" width="100%" cellspacing="0" cellpadding="0">
<tr class="dataTableHeadingRow">

<?php
$last_part='';
$catCount = 0;
$prodCount = 0;
$ptc_query = tep_db_query("select pd.products_id as products_id, p.products_id as the_id, pd.products_name as products_name from (" . TABLE_PRODUCTS_DESCRIPTION . " pd) left join " . TABLE_PRODUCTS . " p on  p.products_id = pd.products_id where isnull(p.products_id) order by  pd.products_id");

//  Count the number of rows that are returned. (if any)
$num = tep_db_num_rows($ptc_query);
?>
<td class="pageHeading" width="100%" align="center"><?php echo $num . " " . HEADING_ACTION_PRODUCTS_DESCRIPTION_NO_CATEGORY;?></td>
</tr>
</table>
<table width="100%" cellpadding="0" border="1" valign="top">
<tr>
<?php

//  Let's find out if we have matches.  If not, we say so.
if ($num < 1) {
	echo "<td width='100%' class='dataTableRow' align='center'>"; 
	echo PRODUCTS_DESCRIPTION_NO_CATEGORY . "</td>";
}else{
//  We have matches!  We list them all.
?>

<!-- Begin Column Headings //-->
				<tr>
					<th class="smallText" align="center">Product No</th>
					<th class="smallText" align="center">Product Name</th>
				</tr>
<!-- End Column Headings //-->

<?php
//  Begin loop for results.
	while ($row = tep_db_fetch_array($ptc_query)) { 
?> 
				<tr>
					<td align="center"><?php echo $row['products_id']; ?></td>
					<td><?php echo $row['products_name']; ?></td>
				</tr>
<?php        
    } 

//  End loop for results.
}
?>

<!-- Begin Next Menu //-->
<table width="100%" align="center">
<tr>
<td align="center"><a href="database_check.php"><img src="includes/languages/english/images/buttons/button_main.gif" border="0" alt="Back to start"></a></td>
<td align="center"><a href="database_check.php?action=18"><img src="includes/languages/english/images/buttons/button_next.gif" border="0" alt="Skip to the next step"></a></td>
<td align="center"><a href="database_check.php?action=delete_all_products_desc&ref=9" onclick="return confirmDelete();"><img src="includes/languages/english/images/buttons/button_delete.gif" border="0" alt="Click to delete product"></a></td>
</tr>
</table>
<!-- End Next Menu //-->

<?php
}

function manufacturer_no_product() {
?>

<table cellpadding="0" border="1" valign="top">
<tr>
<td width="100%"><table border="1" width="100%" cellspacing="0" cellpadding="0">
<tr class="dataTableHeadingRow">

<?php

//  Query database for related info on all manufacturers with no products.  Determined by finding manufacturers (categories_id) not in products (categories_id).

$query = "SELECT m.manufacturers_id, p.products_id, m.manufacturers_name FROM (" . TABLE_MANUFACTURERS . " m) LEFT JOIN " . TABLE_PRODUCTS . " p ON m.manufacturers_id = p.manufacturers_id WHERE p.products_id IS NULL GROUP BY m.manufacturers_id ORDER BY m.manufacturers_id";

$query_info = tep_db_query($query);

//  Count the number of rows that are returned. (if any)
$num = tep_db_num_rows($query_info);
?>
<td class="pageHeading" width="100%" align="center"><?php echo $num . " " . HEADING_ACTION_MANUFACTURERS_NO_PRODUCTS;?></td>
</tr>
</table>
<table width="100%" cellpadding="0" border="1" valign="top">
<tr>
<?php

//  Let's find out if we have matches.  If not, we say so.
if ($num < 1) {
echo "<td width='100%' class='dataTableRow' align='center'>"; 
echo MANUFACTURERS_NO_PRODUCTS . "</td>";
}else{
//  We have matches!  We list them all.
?>

<!-- Begin Column Headings //-->
<tr class="dataTableContent" align="center">
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_ID;?> *</td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_MANUFACTURERS_NAME;?></td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_DELETE;?></td>
<td align="center" class="dataTableHeadingRow"><?php echo HEADING_ACTION_ADD_PRODUCTS;?></td>
</tr>
<!-- End Column Headings //-->

<?php
//  Begin loop for results.
for ($i=0; $i < $num; $i++) {
$row = tep_db_fetch_array($query_info);
$manufacturers_id = $row['manufacturers_id'];
$manufacturers_name = $row['manufacturers_name'];

//  Show results.
echo '<tr>';
echo '<td align="center" class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"><a href="manufacturers.php?cPath='.$cPath.'&cID='.$manufacturers_id.' "target=blank">'.$manufacturers_id.'</a></td>';
echo '<td align="center" class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"><a href="manufacturers.php?cPath='.$cPath.'&cID='.$manufacturers_id.' "target=blank">'.$manufacturers_name.'</a></td>';
echo '<td align="center"><a href="database_check.php?action=delete_manufacturers&ref=18&manufacturers_id='.$manufacturers_id.'" onclick="return confirmDelete();"><img src="includes/languages/english/images/buttons/button_delete.gif" border="0" alt="Click to delete category"></a></td>';
echo '<td><a href="categories.php "target=blank"><img src="includes/languages/english/images/buttons/button_new_product.gif" border="0" alt="Click to add products"></a></td>';
echo '</tr>';
}
//  End loop for results.
}
?>

<!-- Begin Next Menu //-->
<table width="100%" align="center">
<tr>
<td><a href="database_check.php"><img src="includes/languages/english/images/buttons/button_main.gif" border="0" alt="Back to start"></a></td>
<td><a href="database_check.php?action=19"><img src="includes/languages/english/images/buttons/button_next.gif" border="0" alt="Skip to the next step"></a></td>
</tr>
</table>
<!-- End Next Menu //-->

<?php
}


function finish() {
?>

<table cellpadding="0" border="1" valign="top">
<tr>
<td width="100%"><table border="1" width="100%" cellspacing="0" cellpadding="0">
<tr class="dataTableHeadingRow">
<td class="pageHeading" width="100%" align="center"><?php echo HEADING_ACTION_FINISH;?></td>
</tr>
</tr>
</table>

<?php
echo '<table width="100%" cellpadding="0" border="1" valign="top">';
echo '<tr>';
echo "<td width='100%' class='dataTableRow'>"; echo FINISH; "</td>";
echo '</table>';
?>
<!-- Begin Finish Menu //-->
<table width="100%" align="center">
<tr>
<td><a href="database_check.php"><img src="includes/languages/english/images/buttons/button_main.gif" border="0" alt="Back to start"></a></td>
</tr>
</table>
<!-- End Finish Menu //-->

<?php
}
?>

<!--  Begin function confirm delete //-->
<script type="text/javascript">
function confirmDelete(){
return confirm("Do you really want to Delete??");
}
function confirmUpdate(){
return confirm("Do you really want to Update??");
}
</script type"text/javascript">
