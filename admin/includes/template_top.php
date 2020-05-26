<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/
?>
<!DOCTYPE html>
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta charset="<?php echo CHARSET; ?>">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title><?php echo TITLE; ?></title>
<base href="<?php echo ($request_type == 'SSL') ? HTTPS_SERVER . DIR_WS_HTTPS_ADMIN : HTTP_SERVER . DIR_WS_ADMIN; ?>" />
<link rel="stylesheet" href="<?php echo tep_catalog_href_link('ext/jquery/ui/redmond/jquery-ui-1.10.4.min.css', '', 'SSL'); ?>">

<?php
  echo $OSCOM_Hooks->call('siteWide', 'injectSiteStart');
?>

<script src="<?php echo tep_catalog_href_link('ext/jquery/ui/jquery-ui-1.10.4.min.js', '', 'SSL'); ?>"></script>

<?php  
  if (tep_not_null(JQUERY_DATEPICKER_I18N_CODE)) {
?>
<script src="<?php echo tep_catalog_href_link('ext/jquery/ui/i18n/jquery.ui.datepicker-' . JQUERY_DATEPICKER_I18N_CODE . '.js', '', 'SSL'); ?>"></script>
<script>
$.datepicker.setDefaults($.datepicker.regional['<?php echo JQUERY_DATEPICKER_I18N_CODE; ?>']);
</script>
<?php
  }
?>

</head>
<body>

<?php
echo $OSCOM_Hooks->call('siteWide', 'injectBodyStart');
?>

<div class="<?php echo BOOTSTRAP_CONTAINER; ?>">
  <div class="row">

<?php
  if (tep_session_is_registered('admin')) {
    require('includes/header.php');
  } 
?>

  <div id="contentText" class="col">

    <?php
    if ($messageStack->size > 0) {
      echo $messageStack->output();
    }
    ?>