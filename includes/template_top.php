<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  $oscTemplate->buildBlocks();

  if (!$oscTemplate->hasBlocks('boxes_column_left')) {
    $oscTemplate->setGridContentWidth($oscTemplate->getGridContentWidth() + $oscTemplate->getGridColumnWidth());
  }

  if (!$oscTemplate->hasBlocks('boxes_column_right')) {
    $oscTemplate->setGridContentWidth($oscTemplate->getGridContentWidth() + $oscTemplate->getGridColumnWidth());
  }
?>
<!DOCTYPE html>
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta charset="<?php echo CHARSET; ?>">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
 <meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo tep_output_string_protected($oscTemplate->getTitle()); ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">

<link href="ext/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="custom.css" rel="stylesheet">
<link href="user.css" rel="stylesheet">

<!--[if lt IE 9]>
   <script src="ext/js/html5shiv.js"></script>
   <script src="ext/js/respond.min.js"></script>
   <script src="ext/js/excanvas.min.js"></script>
<![endif]-->
 
<script src="ext/jquery/jquery-1.11.1.min.js"></script>

<!-- font awesome -->
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">

<?php echo $oscTemplate->getBlocks('header_tags'); ?>
</head>
<body>

  <?php echo $oscTemplate->getContent('navigation'); ?>

  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
   
  <section id="bodyWrapper" class="<?php echo BOOTSTRAP_CONTAINER; ?>">
    <div class="row">

      <div id="bodyContent" class="col-md-<?php echo $oscTemplate->getGridContentWidth(); ?> <?php echo ($oscTemplate->hasBlocks('boxes_column_left') ? 'col-md-push-' . $oscTemplate->getGridColumnWidth() : ''); ?>">
      
<?php
  if (isset($HTTP_GET_VARS['error_message']) && tep_not_null($HTTP_GET_VARS['error_message'])) {
?>
        <div class="row">
          <div class="col-xs-12">
            <div class="alert alert-danger">
              <a href="#" class="close glyphicon glyphicon-remove" data-dismiss="alert"></a>
              <?php echo htmlspecialchars(stripslashes(urldecode($HTTP_GET_VARS['error_message']))); ?>
            </div>
          </div>
        </div>
<?php
  }

  if (isset($HTTP_GET_VARS['info_message']) && tep_not_null($HTTP_GET_VARS['info_message'])) {
?>
        <div class="row">
          <div class="col-xs-12">
            <div class="alert alert-info">
              <a href="#" class="close glyphicon glyphicon-remove" data-dismiss="alert"></a>
              <?php echo htmlspecialchars(stripslashes(urldecode($HTTP_GET_VARS['info_message']))); ?>
            </div>
          </div>
        </div>
<?php
  }
?>
