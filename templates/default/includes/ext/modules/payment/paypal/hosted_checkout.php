<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/
?>
<!DOCTYPE html>
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>" />
<title><?php echo htmlspecialchars(TITLE); ?></title>
<base href="<?php echo HTTP_SERVER . DIR_WS_CATALOG; ?>" />
</head>
<body>

<div style="text-align: center;">
  <?php echo tep_image('ext/modules/payment/paypal/images/hss_load.gif');?>
</div>

<form name="pphs" action="<?php echo $form_url; ?>" method="post" <?php echo ($error ? 'target="_top"' : ''); ?>>
  <input type="hidden" name="hosted_button_id" value="<?php echo (isset($_SESSION['pphs_result']['HOSTEDBUTTONID']) ? htmlspecialchars($_SESSION['pphs_result']['HOSTEDBUTTONID']) : ''); ?>" />
</form>

<script>
  document.pphs.submit();
</script>

</body>
</html>
