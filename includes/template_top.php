<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  $oscTemplate->buildBlocks();
  
  $OSCOM_Hooks->call('siteWide', 'injectRedirects');
  
  if (!$oscTemplate->hasBlocks('boxes_column_left')) {
    $oscTemplate->setGridContentWidth($oscTemplate->getGridContentWidth() + $oscTemplate->getGridColumnWidth());
  }

  if (!$oscTemplate->hasBlocks('boxes_column_right')) {
    $oscTemplate->setGridContentWidth($oscTemplate->getGridContentWidth() + $oscTemplate->getGridColumnWidth());
  }
?>
<!DOCTYPE html>
<html<?php echo HTML_PARAMS; ?>>
<head>
<meta charset="<?php echo CHARSET; ?>">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title><?php echo tep_output_string_protected($oscTemplate->getTitle()); ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">

<?php 
echo $OSCOM_Hooks->call('siteWide', 'injectSiteStart');

echo $oscTemplate->getBlocks('header_tags'); 
?>
</head>
<body>

  <?php 
  echo $OSCOM_Hooks->call('siteWide', 'injectBodyStart');
  
  echo $oscTemplate->getContent('navigation'); 
  ?>
  
  <div id="bodyWrapper" class="<?php echo BOOTSTRAP_CONTAINER; ?> pt-2">

    <?php
    echo $OSCOM_Hooks->call('siteWide', 'injectBodyWrapperStart');
    
    echo $OSCOM_Hooks->call('siteWide', 'injectBeforeHeader');
    
    require('includes/header.php');

    echo $OSCOM_Hooks->call('siteWide', 'injectAfterHeader');    
    ?>

    <div class="row">
      <div id="bodyContent" class="col order-1 order-md-6">
      
        <?php
        echo $OSCOM_Hooks->call('siteWide', 'injectBodyContentStart');
        ?>
