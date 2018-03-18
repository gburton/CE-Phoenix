<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

// the following cPath references come from application_top.php
  $category_depth = 'top';
  if (isset($cPath) && tep_not_null($cPath)) {
    $categories_products_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_TO_CATEGORIES . " where categories_id = '" . (int)$current_category_id . "'");
    $categories_products = tep_db_fetch_array($categories_products_query);
    if ($categories_products['total'] > 0) {
      $category_depth = 'products'; // display products
    } else {
      $category_parent_query = tep_db_query("select count(*) as total from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$current_category_id . "'");
      $category_parent = tep_db_fetch_array($category_parent_query);
      if ($category_parent['total'] > 0) {
        $category_depth = 'nested'; // navigate through the categories
      } else {
        $category_depth = 'products'; // category has no products, but display the 'no products' message
      }
    }
  }

  require('includes/languages/' . $language . '/index.php');

  require('includes/template_top.php');

  if ($category_depth == 'nested') {
    
    if ($messageStack->size('product_action') > 0) {
      echo $messageStack->output('product_action');
    }
?>

<div class="contentContainer">
  <div class="row">
    <?php echo $oscTemplate->getContent('index_nested'); ?>
  </div>
</div>

<?php
  } elseif ($category_depth == 'products' || (isset($_GET['manufacturers_id']) && !empty($_GET['manufacturers_id']))) {

?>

<div class="contentContainer">
  <div class="row">
    <?php echo $oscTemplate->getContent('index_products'); ?>
  </div>
</div>

<?php
  } else { // default page
  
    if ($messageStack->size('product_action') > 0) {
      echo $messageStack->output('product_action');
    }
?>

<div class="row">
  <?php echo $oscTemplate->getContent('index'); ?>
</div>

<?php
  }

  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
