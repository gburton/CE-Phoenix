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
<html <?= HTML_PARAMS ?>>
<head>
<meta charset="<?= CHARSET ?>">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title><?= TITLE ?></title>
<base href="<?= HTTP_SERVER . DIR_WS_ADMIN ?>" />
<link rel="stylesheet" href="<?= tep_catalog_href_link('ext/jquery/ui/redmond/jquery-ui-1.10.4.min.css') ?>">

<?= $OSCOM_Hooks->call('siteWide', 'injectSiteStart') ?>

<script src="<?= tep_catalog_href_link('ext/jquery/ui/jquery-ui-1.10.4.min.js') ?>"></script>

<?php
  if (tep_not_null(JQUERY_DATEPICKER_I18N_CODE)) {
?>
<script src="<?= tep_catalog_href_link('ext/jquery/ui/i18n/jquery.ui.datepicker-' . JQUERY_DATEPICKER_I18N_CODE . '.js') ?>"></script>
<script>
$.datepicker.setDefaults($.datepicker.regional['<?= JQUERY_DATEPICKER_I18N_CODE ?>']);
</script>
<?php
  }
?>

</head>
<body>

<?= $OSCOM_Hooks->call('siteWide', 'injectBodyStart') ?>

<div class="container-fluid">
  <div class="row">

<?php
  if (isset($_SESSION['admin'])) {
    require 'includes/header.php';
  }
?>

  <div id="contentText" class="col">

    <?php
    if ($messageStack->size > 0) {
      echo $messageStack->output();
    }
    ?>