<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com

*/

defined('AAS') or die;

$query_aas_settings="CREATE TABLE IF NOT EXISTS `aas_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sgroup` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'aac',
  `skey` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'default',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`skey`,`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=22 ;";

$query_aas_settings_fill="INSERT INTO `aas_settings` (`id`, `sgroup`, `skey`, `type`, `description`, `value`) VALUES
(1, 'aac', 'displayFieldsPanel', 'default', 'AAS_AAC_DISPLAY_FIELDS_PANEL', ''),
(2, 'aac', 'displayLanguageSelection', 'default', 'AAS_AAC_DISPLAY_LANGUAGE_SELECTION', ''),
(3, 'aac', 'displayBottomInformation', 'default', 'AAS_AAC_DISPLAY_BOTTOM_INFORMATION', ''),
(4, 'aac', 'displayCountProducts', 'default', 'AAS_AAC_DISPLAYCOUNTPRODUCTS', ''),
(5, 'aac', 'enableAttributesManager', 'default', 'AAS_AAC_DISABLE_ATTRIBUTES_MANAGER', ''),
(6, 'aac', 'enableTempProductsList', 'default', 'AAS_AAC_DISABLE_TEMP_PRODUCTS_LIST', ''),
(7, 'aac', 'enableToolBox', 'default', 'AAS_AAC_DISABLE_TOOLBOX', ''),
(8, 'aac', 'enableClocks', 'default', 'AAS_AAC_DISABLE_CLOCKS', ''),
(9, 'aac', 'enableSpecials', 'default', 'AAS_AAC_DISABLE_SPECIALS', ''),
(10, 'aac', 'enableModulesManagerDialog', 'default', 'AAS_AAC_DISABLE_MODULES_MANAGER_DIALOG', ''),
(11, 'aac', 'enableCalendar', 'default', 'AAS_AAC_DISABLE_CALENDAR', ''),
(12, 'aac', 'enableOnlineUsers', 'default', 'AAS_AAC_DISABLE_ONLINE_USERS', ''),
(13, 'aac', 'enableContactMe', 'default', 'AAS_AAC_DISABLE_CONTACT_ME', ''),
(14, 'aac', 'enableDonations', 'default', 'AAS_AAC_DISABLE_DONATIONS', ''),
(15, 'aac', 'delete_products', 'default', 'AAS_AAC_DISABLE_DELETE_PRODUCTS', ''),
(16, 'aac', 'import', 'default', 'AAS_AAC_DISABLE_IMPORT', ''),
(17, 'aac', 'export', 'default', 'AAS_AAC_DISABLE_EXPORT', ''),
(18, 'aac', 'search', 'default', 'AAS_AAC_DISABLE_SEARCH', ''),
(19, 'aac', 'print', 'default', 'AAS_AAC_DISABLE_PRINT', ''),
(20, 'aac', 'all_edit', 'default', 'AAS_AAC_DISABLE_ALL_EDIT', ''),
(21, 'aac', 'mass_columns_edit', 'default', 'AAS_AAC_DISABLE_MASS_COLUMNS_EDIT', '');";
$error='';
if(isset($_POST['execQueryAASSettings'])){
 
  if(tep_db_query($query_aas_settings)){
     
   tep_db_query($query_aas_settings_fill);
   tep_redirect(tep_href_link(FILENAME_AAS, '', 'SSL', false));
    
  }else $error='Cannot create [ aas_settings ] table. Please try again or do it manually.';
  
}

?>
<!DOCTYPE html>
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo AAS_TITLE; ?></title>
<link rel="shortcut icon" href="ext/aas/images/favicon.ico">
<link rel="stylesheet" href="ext/aas/css/style.css">
</head>
<body>
<div id="sadAdmin">
<div class="margin-10-auto"></div>
<div class="faceWrapper">
<p class="face"><img src="ext/aas/images/sadAdmin.png" alt="Sad Admin"></p>
<p class="face"><img src="ext/aas/images/logo.png" alt="AAS logo"></p>
</div>
<div class="margin-30-auto"></div>
<p><b>AAS</b> requires a new table [ aas_settings ] in osCommerce db to be installed. This table is used to store some configuration values such as admin access control, columns disable actions, columns sorting, modules e.t.c.<br><br>If you have any questions contact <a target="_blank" href="http://www.alternative-administration-system.com/support">support</a>.</p>
<div class="margin-10-auto"></div>
Copy & paste bellow code into phpmyadmin's sql tab textarea and press the [ Go ] button or click
<?php echo tep_draw_form('execQueryAASSettings', FILENAME_AAS, '', 'post','style="display:inline-block;"'); ?>
<input type="submit" name="execQueryAASSettings" class="applyButton" value="here">
</form> and let AAS execute those queries for you.
<?php if($error!='') echo '<p style="margin:10px auto;color:#f00">'.$error.'</p>'; ?>
<div class="code_box">
<pre>
<?php echo $query_aas_settings; ?>


<?php echo $query_aas_settings_fill; ?>
</pre>
</div>
</div>
</body>
</html>
