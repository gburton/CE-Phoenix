<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com

  File information: contains default configuration options.
  Feel free to change but be careful because you may break AAS functionality.

*/
defined('AAS') or die;

$defaults=array(

  'entriesPerPage'=>'5', // enter a number to display max entries per page (make sure the number you entered exists in $perPageArray )
  'tableSorting'=>1, //0=false, make table columns sortable or not (Note that sortable value on $fieldsArray must be true) This is applicable when we have more than one product listed.
  'orderBy'=>'pd.products_name',
  'ascDesc'=>'ASC',
  'colorEachTableRowDifferently'=>true, // apply a different background color to each table row

  'displayBreadcrumb'=>true,
  'displayByStatus'=>2, // 0 displays products that are out of stock, 1 displays products that are in of stock, 2 display all products in stock and out of stock
  'displayByStatusSelection'=>true, // in stock, out of stock, both, selection
  'displaySuccessAlertMessages'=>1, // 0 does not display success alert dialogs.
  'displayErrorAlertMessages'=>1, // 0 does not display error alert dialogs. Better Leave 1.
  'displayCategoriesSelection'=>true, // displays a dropdown where you can select if you want the categories to visible.
  'displayCategories'=>1,
  'displayFieldsPanel'=>true, // displays or not the right panel (selection of fields)
  'displayLanguageSelection'=>true,
  'displayGoBackButton'=>true,
  'displaySettingsButton'=>true,
  'displayGoToDefaultAdministrationPanel'=>true,
  'displayLogoffButton'=>true,
  'displayOrderBySelection'=>true,
  'displayAscDescSelection'=>true,
  'displayBottomInformation'=>true, // at least buy me a beer => https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=54CLXRPJ9M9B6
  'displayDatetime-info'=>true,// displays date, time and how much time you have stayed in page
  'displayCountProducts'=>true, // displays or not the ability to choose to display how many products - subcategories are under the category. Applicable only when theres atleast one category listed.
  'countProducts'=>false,
  'countSubcategories'=>false,

  'enableAttributesManager'=>true,
  'enableTempProductsList'=>true,
  'enableToolBox'=>true,
  'enableClocks'=>true,// displays worldwide clocks
  'enableSpecials'=>true,// displays specials
  'enableProductsExpected'=>false,
  'enableCalendar'=>true,// if yes make sure you have installed aas_calendar table in your db, check installation pdf for instructions.
  'enableOnlineUsers'=>true,
  'onlineUsers_displayCountriesFrom'=>false,// if yes make sure you have installed aas_ip2c table in your db, check installation pdf for instructions.
  'enableModulesManagerDialog'=>true,// make it possible for admins to upload new modules, e.t.c. Better leave it true and disabe it for other admins via Admin Options
  'enableTableStickyHeaders'=>true,
  'enableContactMe'=>true,
  'enableDonations'=>true,

  'paginationPosition'=>'bottom', // top or bottom // anything else will not display it
  'productsDescriptionEditor'=>'ckeditor', // Default editor, use tinymce or ckeditor. Use anything else to display default textarea.
  'inTableSearchPosition'=>array('visible'=>true,'x'=>'right','y'=>'top'), // x can take only left or right and y top or bottom

  //'alterCategoriesStatus'=>true, TODO

  /* PRODUCT_DESCRIPTION_UNIQUE_ID_WRAPPER

  In order to be able to preview the product description before applying any changes you need to set up a unique id that is wrapped on the products description code which can be found in the catalog's product_info.php page.
  You can change it to whatever you like but make sure you have also edit catalog's product_info.php and change:

  <?php echo stripslashes($product_info['products_description']); ?>
  to <?php echo '<span id="XXX">'.stripslashes($product_info['products_description']).'</span>'; ?>
  or to <span id="XXX"><?php echo stripslashes($product_info['products_description']); ?></span>

  Where XXX is the value of the key productDescriptionUniqueIdWrapper which is by default 'aas'.
  */

  'productDescriptionUniqueIdWrapper'=>'aas'

);

/*

Clocks

  Note : enableClocks must be true
  Parameters: Title => Current time zone offset
  You can add more.

*/
$clocks=array(

  AAS_CLOCK_TITLE_LOCAL=>0,
  AAS_CLOCK_TITLE_ATHENS=>3,
  AAS_CLOCK_TITLE_PARIS=>1,
  AAS_CLOCK_TITLE_LOS_ANGELES=>-7,
  AAS_CLOCK_TITLE_SYDNEY=>11

);

/*

Per page entries

  $defaults['entriesPerPage'] must be a number that is included bellow

*/
$perPageArray=array('5','10','15','20','30','50','70','90',AAS_TEXT_ALL);

/* Search on specific field

  {id}: the name of the field
  {text}: the text to display on the select menu

*/
$searchOnFieldArray=array(

array('id'=>'pd.products_name','text'=>AAS_TEXT_SEARCH_IN_PRODUCTS_NAME),
array('id'=>'p.products_model','text'=>AAS_TEXT_SEARCH_IN_PRODUCTS_MODEL)

);

/* Order by specific field

  key: the name of the field
  value: the text to display on the select menu

*/
$orderByArray=array(
'pd.products_name'=>AAS_TEXT_SORT_BY_PRODUCTS_NAME,
'p.products_last_modified'=>AAS_TEXT_SORT_BY_LAST_MODIFIED,
'p.products_date_added'=>AAS_TEXT_SORT_BY_DATE_ADDED,
'p.products_model'=>AAS_TEXT_SORT_BY_PRODUCTS_MODEL,
'p.products_price'=>AAS_TEXT_SORT_BY_PRODUCTS_PRICE,
'p.products_ordered'=>AAS_TEXT_SORT_BY_PRODUCTS_ORDERED,
'pd.products_viewed'=>AAS_TEXT_SORT_BY_PRODUCTS_VIEWED
);
/* Order by asc desc

  key: the name of the field
  value: the text to display on the select menu

*/
$ascDescArray=array('ASC'=>AAS_TEXT_ASC,'DESC'=>AAS_TEXT_DESC);

/*

WHERE THE MAGIC BEGINS

You can add more rows if are present on products table

'products_image'=>array('visible'=>false,'sortable'=>false,'cellEditable'=>false,'theadText'=>AAS_HEADING_PRODUCTS_IMAGE),

  {visible}: set true to display always
  {lockVisibility}: when true user cannot change status visibility set by {visible} (Visibility status can be changed on the fly by the right hidden Panel)
  {sortable}: makes the table column sortable {apply only to alphanumeric values such as Price , weight. On image does not work }
  {cellEditable}: means that when you click on a table cell it adds an input so you can change the value, just like the price and weight.
  {exportable}: whether the field will be exported to csv, json e.t.c.
  {theadText}: its the table field name (translations are located inside languages folder in tbl.php)

Tips:

  Set {visible}:true and {lockVisibility}:true to always display field (user cannot hide the field)
  Set {visible}:false and {lockVisibility}:true to always not display field (user cannot display the field)
  Set {visible}:false and {lockVisibility}:false to let user decide if want to display the field, which is not visible by default
  Set {visible}:true and {lockVisibility}:false to let user decide if want to display the field, which is visible by default

Extra Tip:

  You can reorder the displayed columns by rearranging the records in $fieldsArray. For example:

  $fieldsArray=array(

  'products_model'=>array('visible'=>false,'lockVisibility'=>false,'sortable'=>true,'cellEditable'=>true,'exportable'=>true,'theadText'=>AAS_HEADING_PRODUCTS_MODEL),
  'products_image'=>array('visible'=>false,'lockVisibility'=>false,'sortable'=>false,'cellEditable'=>false,'exportable'=>false,'theadText'=>AAS_HEADING_PRODUCTS_IMAGE),
  .
  .
  .
  );

  Now model column will appear first and then image.

*/

$fieldsArray=array(

  'attributes'=>array('visible'=>false,'lockVisibility'=>false,'sortable'=>true,'cellEditable'=>false,'exportable'=>false,'massEdit'=>false,'theadText'=>AAS_HEADING_ATTRIBUTES),
  'date_added'=>array('visible'=>false,'lockVisibility'=>false,'sortable'=>true,'cellEditable'=>false,'exportable'=>true,'massEdit'=>true,'theadText'=>AAS_HEADING_DATE_ADDED),
  'products_date_available'=>array('visible'=>false,'lockVisibility'=>false,'sortable'=>false,'cellEditable'=>false,'exportable'=>true,'massEdit'=>true,'theadText'=>AAS_HEADING_PRODUCTS_DATE_AVAILABLE),
  'products_description'=>array('visible'=>false,'lockVisibility'=>false,'sortable'=>false,'cellEditable'=>false,'exportable'=>true,'massEdit'=>false,'theadText'=>AAS_HEADING_PRODUCTS_DESCRIPTION),
  'id'=>array('visible'=>false,'lockVisibility'=>false,'sortable'=>true,'cellEditable'=>false,'exportable'=>false,'massEdit'=>false,'theadText'=>AAS_HEADING_ID),
  'products_image'=>array('visible'=>false,'lockVisibility'=>false,'sortable'=>false,'cellEditable'=>false,'exportable'=>false,'massEdit'=>false,'theadText'=>AAS_HEADING_PRODUCTS_IMAGE),
  //products_linked is a custom implementation. There is no such field in products table
  'products_linked'=>array('visible'=>false,'lockVisibility'=>false,'sortable'=>true,'cellEditable'=>false,'exportable'=>false,'massEdit'=>false,'theadText'=>AAS_HEADING_PRODUCTS_LINKED),
  'last_modified'=>array('visible'=>false,'lockVisibility'=>false,'sortable'=>true,'cellEditable'=>false,'exportable'=>true,'massEdit'=>false,'theadText'=>AAS_HEADING_LAST_MODIFIED),
  'manufacturers_name'=>array('visible'=>false,'lockVisibility'=>false,'sortable'=>false,'cellEditable'=>false,'exportable'=>true,'massEdit'=>true,'theadText'=>AAS_HEADING_MANUFACTURERS_NAME),
  'products_model'=>array('visible'=>false,'lockVisibility'=>false,'sortable'=>true,'cellEditable'=>true,'exportable'=>true,'massEdit'=>false,'theadText'=>AAS_HEADING_PRODUCTS_MODEL),
  'products_ordered'=>array('visible'=>false,'lockVisibility'=>false,'sortable'=>true,'cellEditable'=>false,'exportable'=>true,'massEdit'=>false,'theadText'=>AAS_HEADING_PRODUCTS_ORDERED),
  'products_price'=>array('visible'=>false,'lockVisibility'=>false,'sortable'=>true,'cellEditable'=>true,'exportable'=>true,'massEdit'=>true,'theadText'=>AAS_HEADING_PRODUCTS_PRICE),
  //products_price_gross is a custom implementation. There is no products_price_gross field in products table
  'products_price_gross'=>array('visible'=>false,'lockVisibility'=>false,'sortable'=>true,'cellEditable'=>true,'exportable'=>true,'massEdit'=>true,'theadText'=>AAS_HEADING_PRODUCTS_PRICE_GROSS),
  //products_order_status is a custom implementation. There is no such field in products table
  'products_order_status'=>array('visible'=>false,'lockVisibility'=>false,'sortable'=>true,'cellEditable'=>false,'exportable'=>true,'massEdit'=>false,'theadText'=>AAS_HEADING_PRODUCTS_ORDER_STATUS),
  'products_quantity'=>array('visible'=>false,'lockVisibility'=>false,'sortable'=>true,'cellEditable'=>true,'exportable'=>true,'massEdit'=>'int','theadText'=>AAS_HEADING_PRODUCTS_QUANTITY),
  //Sort Order Is used by categories and products. But in order to use it in products you must have a field called products_sort_order in products table. Check out this addon: http://addons.oscommerce.com/info/8311
  'sort_order'=>array('visible'=>false,'lockVisibility'=>false,'sortable'=>true,'cellEditable'=>true,'exportable'=>false,'massEdit'=>false,'theadText'=>AAS_HEADING_SORT_ORDER),
  'special'=>array('visible'=>false,'lockVisibility'=>false,'sortable'=>false,'cellEditable'=>false,'exportable'=>false,'massEdit'=>false,'theadText'=>AAS_HEADING_SPECIAL),
  'products_status'=>array('visible'=>false,'lockVisibility'=>false,'sortable'=>false,'cellEditable'=>false,'exportable'=>true,'massEdit'=>true,'theadText'=>AAS_HEADING_PRODUCTS_STATUS),
  'tax_class_title'=>array('visible'=>false,'lockVisibility'=>false,'sortable'=>false,'cellEditable'=>false,'exportable'=>true,'massEdit'=>true,'theadText'=>AAS_HEADING_TAX_CLASS_TITLE),
  'products_url'=>array('visible'=>false,'lockVisibility'=>false,'sortable'=>true,'cellEditable'=>true,'exportable'=>true,'massEdit'=>false,'theadText'=>AAS_HEADING_PRODUCTS_URL),
  'products_viewed'=>array('visible'=>false,'lockVisibility'=>false,'sortable'=>true,'cellEditable'=>false,'exportable'=>true,'massEdit'=>false,'theadText'=>AAS_HEADING_PRODUCTS_VIEWED),
  'products_weight'=>array('visible'=>false,'lockVisibility'=>false,'sortable'=>true,'cellEditable'=>true,'exportable'=>true,'massEdit'=>'decimal','theadText'=>AAS_HEADING_PRODUCTS_WEIGHT),

  //'products_cost'=>array('visible'=>false,'lockVisibility'=>false,'sortable'=>true,'cellEditable'=>true,'exportable'=>true,'massEdit'=>false,'theadText'=>'Cost'),
  
);

/*

When we have fields from other tables than products we must add them as extra fields
which means we have to write the query SELECT {the field we want to display} FROM {table} WHERE {where case}

//DEPRECATED WILL BE REMOVED IN FUTURE RELEASES
*/
$_EXTRA_FIELDS=array(

/*
  'manufacturers_name'=>array(
    'SELECT'=>array(false=>'',true=>', m.manufacturers_name, m.manufacturers_id'),
    'FROM'=>array(false=>'',true=>TABLE_MANUFACTURERS.' m, '),
    'WHERE'=>array(false=>'',true=>'m.manufacturers_id=p.manufacturers_id AND ')
  ),

  'specials'=>array(
    'SELECT'=>array(false=>'',true=>', s.specials_id, s.specials_new_products_price, s.status AS specials_status'),
    'FROM'=>array(false=>'',true=>TABLE_SPECIALS.' s, '),
    'WHERE'=>array(false=>'',true=>'s.specials_id=p.products_id AND ')
  ),

  'tax_class_title'=>array(
    'SELECT'=>array(false=>'',true=>', tc.tax_class_title, p.products_tax_class_id'),
    'FROM'=>array(false=>'',true=>TABLE_TAX_CLASS.' tc, '),
    'WHERE'=>array(false=>'',true=>'p.products_tax_class_id=tc.tax_class_id AND ')
  )
*/
);

/*

Apply a css style or a class to the table cell (td)

For example on products_image the image has a standart width and height so its better to set it directly on the cell

*/
$_table_td_rules=array(

  'products_name'=>array('style'=>'cursor:pointer;','class'=>'centerAlign'),
  'products_image'=>array('style'=>'width:'.SMALL_IMAGE_WIDTH.'px;height:'.SMALL_IMAGE_HEIGHT.'px;','class'=>''),
  'products_model'=>array('style'=>'cursor:pointer;','class'=>''),
  'products_quantity'=>array('style'=>'cursor:pointer;','class'=>''),
  'products_price'=>array('style'=>'cursor:pointer;','class'=>''),
  'products_price_gross'=>array('style'=>'cursor:pointer;','class'=>''),
  'products_weight'=>array('style'=>'cursor:pointer;','class'=>''),
  'products_status'=>array('style'=>'','class'=>''),
  'products_date_available'=>array('style'=>'','class'=>''),
  'products_description'=>array('style'=>'','class'=>''),
  'products_ordered'=>array('style'=>'','class'=>''),
  'products_viewed'=>array('style'=>'','class'=>''),
  'products_url'=>array('style'=>'cursor:pointer;','class'=>''),
  'products_linked'=>array('style'=>'','class'=>''),
  'special'=>array('style'=>'','class'=>''),
  'manufacturers_name'=>array('style'=>'','class'=>''),
  'tax_class_title'=>array('style'=>'','class'=>''),
  'attributes'=>array('style'=>'','class'=>''),
  'sort_order'=>array('style'=>'cursor:move;','class'=>''),
  'date_added'=>array('style'=>'','class'=>''),
  'last_modified'=>array('style'=>'','class'=>'')
);

?>
