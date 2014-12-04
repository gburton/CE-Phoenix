<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/
  require('includes/application_top.php');
	ini_set('max_execution_time', 300); //300 seconds = 5 minutes
	ini_set('memory_limit','128M');
  include('includes/functions/dbcheck.php');

  //  Get $action and $ref to direct to proper case
  $action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');
  $ref = (isset($HTTP_GET_VARS['ref']) ? $HTTP_GET_VARS['ref'] : '');

  //  If action has value, go to that action
  if (tep_not_null($action)){
    switch ($action) {

// Begin duplicate products delete.
case 'delete_products':
        tep_db_query("delete from " . TABLE_PRODUCTS . " where products_id = '" . $HTTP_GET_VARS['products_id'] . "'");
        tep_db_query("delete from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . $HTTP_GET_VARS['products_id'] . "'");
        tep_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . $HTTP_GET_VARS['products_id'] . "'");
        $action = $ref;
        break;
//  End duplicate products delete

// Begin categories delete.
case 'delete_categories':
        tep_db_query("delete from " . TABLE_CATEGORIES . " where categories_id = '" . $HTTP_GET_VARS['categories_id'] . "'");
        tep_db_query("delete from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $HTTP_GET_VARS['categories_id'] . "'");
        $action = $ref;
        break;
//  End categories delete

// Begin manufacturers delete.
case 'delete_manufacturers':
        tep_db_query("delete from " . TABLE_MANUFACTURERS . " where manufacturers_id = '" . $HTTP_GET_VARS['manufacturers_id'] . "'");
        tep_db_query("delete from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . $HTTP_GET_VARS['manufacturers_id'] . "'");
        $action = $ref;
        break;
//  End categories delete

case 'delete_all_categories': // Invalid parent category
		tep_db_query("DELETE from " . TABLE_CATEGORIES_DESCRIPTION . " WHERE categories_id  IN (SELECT cid FROM (select c.categories_id as cid, c.parent_id, cp.categories_id as parent_categories_id FROM " . TABLE_CATEGORIES . " c left join " . TABLE_CATEGORIES . " cp ON cp.categories_id = c.parent_id WHERE c.parent_id <> 0 AND isnull( cp.categories_id ) ) as c )");
		tep_db_query("DELETE from " . TABLE_CATEGORIES . " WHERE categories_id  IN (SELECT cid FROM (select c.categories_id as cid, c.parent_id, cp.categories_id as parent_categories_id FROM " . TABLE_CATEGORIES . " c left join " . TABLE_CATEGORIES . " cp ON cp.categories_id = c.parent_id WHERE c.parent_id <> 0 AND isnull( cp.categories_id ) ) as c )");
		$action = $ref;
        break;

case 'delete_all_categories_size': // Category size
		tep_db_query("DELETE from " . TABLE_CATEGORIES_DESCRIPTION . " WHERE categories_id  IN (SELECT cid FROM (select cd.categories_id as cid, cd.categories_name as categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " cd where length(cd.categories_name) > 31 ) as c )");
		tep_db_query("DELETE from " . TABLE_CATEGORIES . " WHERE categories_id  IN (SELECT cid FROM (select cd.categories_id as cid, cd.categories_name as categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " cd where length(cd.categories_name) > 31 ) as c )");
		$action = $ref;
        break;

case 'delete_categories_desc': // specific category descriptions and no categories
        tep_db_query("delete from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $HTTP_GET_VARS['categories_id'] . "'");
        $action = $ref;
        break;

case 'delete_products_desc': // specific product description and no product
        tep_db_query("delete from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . $HTTP_GET_VARS['products_id'] . "'");
        $action = $ref;
        break;

case 'delete_all_categories_desc': // Category descriptions and no categories
		tep_db_query("DELETE from " . TABLE_CATEGORIES_DESCRIPTION . " WHERE categories_id  IN (SELECT cid FROM (select cd.categories_id as cid from (" . TABLE_CATEGORIES_DESCRIPTION . " cd) left join " . TABLE_CATEGORIES . " c on  c.categories_id = cd.categories_id where isnull(c.categories_id) ) as c )");
        $action = $ref;
        break;

case 'delete_all_products_desc': // Product descriptions and no products
		tep_db_query("DELETE from " . TABLE_PRODUCTS_DESCRIPTION . " WHERE products_id  IN (SELECT pid FROM (select pd.products_id as pid from (" . TABLE_PRODUCTS_DESCRIPTION . " pd) left join " . TABLE_PRODUCTS . " p on  p.products_id = pd.products_id where isnull(p.products_id) ) as c )");
        $action = $ref;
        break;

case 'delete_all_categories_only': // Categories with no description
		tep_db_query("DELETE from " . TABLE_CATEGORIES . " WHERE categories_id  IN (SELECT cid FROM (select c.categories_id as cid from (" . TABLE_CATEGORIES . " c) left join " . TABLE_CATEGORIES_DESCRIPTION . " cd on  c.categories_id = cd.categories_id where isnull(cd.categories_name) or cd.categories_name = '' order by  c.categories_id ) as c )");
        $action = $ref;
        break;

case 'delete_all_products_only': // Products with no description
		tep_db_query("DELETE from " . TABLE_PRODUCTS . " WHERE products_id  IN (SELECT pid FROM (select p.products_id as pid from (" . TABLE_PRODUCTS . " p) left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on  c.products_id = pd.products_id where isnull(pd.products_name) or pd.products_name = '' order by  c.products_id ) as c )");
        $action = $ref;
        break;

// Begin products to categories delete.
case 'delete_prodtocat':
        tep_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE products_id = '" . $HTTP_GET_VARS['products_id'] . "' AND categories_id = '" . $HTTP_GET_VARS['categories_id'] . "' LIMIT 1");
        $action = $ref;
        break;

// Begin products to categories delete invalid cat.
case 'delete_prodtocat_cat':
		tep_db_query("DELETE from " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE categories_id  IN (SELECT cid FROM (select ptc.categories_id as cid from (" . TABLE_PRODUCTS_TO_CATEGORIES . " ptc) left join " . TABLE_CATEGORIES . " c on ptc.categories_id = c.categories_id where isnull(c.categories_id)) as c )");
		$action = $ref;
        break;

// Begin products to categories delete invalid prod.
case 'delete_prodtocat_prod':
		tep_db_query("DELETE from " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE products_id  IN (SELECT pid FROM (select ptc.products_id as pid from (" . TABLE_PRODUCTS_TO_CATEGORIES . " ptc) left join " . TABLE_PRODUCTS . " p on ptc.products_id = p.products_id where isnull(p.products_id)) as c )");
		$action = $ref;
        break;

// Begin products delete prod not in cat.
case 'delete_all_prod':
		tep_db_query("DELETE from " . TABLE_PRODUCTS_DESCRIPTION . " WHERE products_id  IN (SELECT pid FROM (select p.products_id as pid from (" . TABLE_PRODUCTS . " p) left join " . TABLE_PRODUCTS_TO_CATEGORIES . " ptc on  ptc.products_id = p.products_id where ptc.products_id is null order by  p.products_id) as c )");
		tep_db_query("DELETE from " . TABLE_PRODUCTS . " WHERE products_id  IN (SELECT pid FROM (select p.products_id as pid from (" . TABLE_PRODUCTS . " p) left join " . TABLE_PRODUCTS_TO_CATEGORIES . " ptc on  ptc.products_id = p.products_id where ptc.products_id is null order by  p.products_id) as c )");
		$action = $ref;
        break;

// Begin setting default wt on products.
case 'set_default_wt':
        tep_db_query("update " . TABLE_PRODUCTS . " SET products_weight = 1 WHERE products_id = '" . $HTTP_GET_VARS['products_id'] . "'");
        $action = $ref;
        break;

// Begin setting default image on category.
case 'set_default_image':
        tep_db_query("update " . TABLE_CATEGORIES . " SET categories_image = 'no_image.jpg' WHERE categories_id = '" . $HTTP_GET_VARS['categories_id'] . "'");
        $action = $ref;
        break;

// Begin setting all default image on category.
case 'set_all_default_image':
        tep_db_query("update " . TABLE_CATEGORIES . " SET categories_image = 'no_image.jpg' WHERE categories_image = '' or isnull(categories_image)");
        $action = $ref;
        break;

// Begin setting products cat to top level.
case 'set_default_cat':
        tep_db_query("insert into " . TABLE_PRODUCTS_TO_CATEGORIES . " (products_id, categories_id) values ('" . $HTTP_GET_VARS['products_id'] . "', '0')");
        $action = $ref;
        break;
//  End setting products cat to top level.
}
}

require(DIR_WS_INCLUDES . 'template_top.php');
?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%">
		  <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
              <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
            </tr>
          </table>
		</td>
      </tr>
    </table>

<?php 

switch ($action) {

//  Begin case for duplicate names function.
case '1':
duplicate_names();
break;

//  Begin case for products no titles function.
case '2':
products_no_titles();
break;

//  Begin case for products price $0.
case '3':
products_no_price();
break;

//  Begin case for products 0 weight.
case '4':
products_no_weight();
break;

//  Begin case for products no category.
case '5':
products_no_category();
break;

//  Begin case for categories no products.
case '6':
categories_no_products();
break;

case '7':
dup_prodtocat(); //duplicate products to categories.
break;

case '8':
categories_no_descriptions(); //Cat no desc
break;

case '9':
category_descriptions_no_category(); //desc no cat
break;

case '10':
category_no_parent(); // cat no parent
break;

case '11':
category_no_image(); //cat no image
break;

case '12':
non_existant_cat_on_ptc(); //non exist cat on ptc
break;

case '13':
non_existant_prod_on_ptc(); //non exist prod on ptc
break;

case '14':
category_desc_gt_32();
break;

case '15':
products_no_ptc();
break;

case '16':
products_no_descriptions();
break;

case '17':
product_descriptions_no_product();
break;

case '18':
manufacturer_no_product();
break;

default:
finish();
break;

// Begin default case. (Main page).
default:
?>
<table cellpadding="0" border="0" valign="top" align="center">
<tr>
<td align="left" border="0"><?php echo HEADING_ACTION_DESCRIPTION;?></td>
</tr>
</table>
<br>

<!-- Begin step-by-step //-->
<table cellpadding="0" border="0" valign="top" align="center">
<tr>
<td class="main"><a href="database_check.php?action=1"><img src="includes/languages/english/images/buttons/button_start.gif" border="0" alt="Click here to start"></a></td>
</tr>
</table>
<!-- End step-by-step //-->

<br><br><br>

<!-- Begin default menu //-->
<table cellpadding="0" border="0" valign="top" align="center" width="100%">
<td width="100%"><table border="1" width="100%" cellspacing="0" cellpadding="0">
         <tr class="dataTableHeadingRow">
           <td class="pageHeading" align="center"><?php echo MAIN_MENU_TITLE;?></td>
           </tr>
           </table>
<table cellpadding="0" border="2" valign="top" align="center" width="100%">
<tr class="dataTableHeadingRow">
<td align="center" class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" width="33%"><a href="database_check.php?action=1"><?php echo HEADING_ACTION_DUPLICATE_NAME; ?></a></td>
<td align="center" class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" width="33%"><a href="database_check.php?action=2"><?php echo HEADING_ACTION_PRODUCTS_WITHOUT_NAME; ?></a></td>
<td align="center" class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" width="33%"><a href="database_check.php?action=3"><?php echo HEADING_ACTION_PRODUCTS_PRICE_0; ?></a></td>
</tr>
<tr class="dataTableHeadingRow">
<td align=center class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" width="33%"><a href="database_check.php?action=4"><?php echo HEADING_ACTION_PRODUCTS_WEIGHT_0; ?></a></td>
<td align=center class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" width="33%"><a href="database_check.php?action=5"><?php echo HEADING_ACTION_PRODUCTS_NO_CATEGORY; ?></a></td>
<td align=center class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" width="33%"><a href="database_check.php?action=6"><?php echo HEADING_ACTION_CATEGORIES_NO_PRODUCTS; ?></a></td>
</tr>
<tr class="dataTableHeadingRow">
<td align=center class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" width="33%"><a href="database_check.php?action=7"><?php echo HEADING_ACTION_PRODUCTS_TO_CATEGORIES; ?></a></td>
<td align=center class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" width="33%"><a href="database_check.php?action=8"><?php echo HEADING_ACTION_CATEGORIES_NO_DESCRIPTION; ?></a></td>
<td align=center class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" width="33%"><a href="database_check.php?action=9"><?php echo HEADING_ACTION_CATEGORIES_DESCRIPTION_NO_CATEGORY; ?></a></td>
</tr>
<tr class="dataTableHeadingRow">
<td align=center class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" width="33%"><a href="database_check.php?action=10"><?php echo HEADING_ACTION_CATEGORIES_NO_PARENT; ?></td>
<td align=center class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" width="33%"><a href="database_check.php?action=11"><?php echo HEADING_ACTION_CATEGORIES_NO_IMAGE; ?></td>
<td align=center class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" width="33%"><a href="database_check.php?action=12"><?php echo HEADING_ACTION_NON_EXISTANT_CAT_ON_PTC; ?></td>
</tr>
<tr class="dataTableHeadingRow">
<td align=center class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" width="33%"><a href="database_check.php?action=13"><?php echo HEADING_ACTION_NON_EXISTANT_PROD_ON_PTC; ?></td>
<td align=center class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" width="33%"><a href="database_check.php?action=14"><?php echo HEADING_ACTION_CATEGORY_DESC_SIZE; ?></td>
<td align=center class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" width="33%"><a href="database_check.php?action=15"><?php echo HEADING_ACTION_PROD_NO_PTC; ?></td>
</tr>
<tr class="dataTableHeadingRow">
<td align=center class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" width="33%"><a href="database_check.php?action=16"><?php echo HEADING_ACTION_PRODUCTS_NO_DESCRIPTION; ?></td>
<td align=center class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" width="33%"><a href="database_check.php?action=17"><?php echo HEADING_ACTION_PRODUCTS_DESCRIPTION_NO_CATEGORY; ?></td>
<td align=center class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" width="33%"><a href="database_check.php?action=18"><?php echo HEADING_ACTION_MANUFACTURERS_NO_PRODUCTS; ?></td>
</tr>
</table>
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
		    <tr>
                <td align="left" class="smallText" colspan="3"><br><strong>The Database checking tool was enhanced and improved by Geoffrey Walton of <a href="http://www.theukwaltons.co.uk">The UK Waltons</a>.<br><br>All sorts of development and support services are available from <a href="http://www.theukwaltons.co.uk">www.theukwaltons.co.uk</a></strong></td>
			</tr>
		  </table>
<!-- End default menu //-->
<?php 
break;
// End default case

}

  require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>