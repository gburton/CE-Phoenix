<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $directory = DIR_FS_CATALOG . 'includes/actions/';

  require('includes/template_top.php');
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
    <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
  </tr>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr class="dataTableHeadingRow">
    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_FILE; ?></td>
    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ACTION; ?></td>
    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CLASS; ?></td>
    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_METHOD; ?></td>
  </tr>

<?php
  $files = array_diff(scandir($directory), array('.', '..'));
  
  foreach ($files as $file) {
    $code = substr($file, 0, strrpos($file, '.'));
	  $class = 'osC_Actions_' . $code;
    
    if ( !class_exists($class) ) {
      include($directory . '/' . $file);
    }
    
    $obj = new $class();
    
    foreach (get_class_methods($obj) as $method) {
      ?>
      <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">
        <td class="dataTableContent"><?php echo $file; ?></td>
        <td class="dataTableContent"><?php echo $code; ?></td>
        <td class="dataTableContent"><?php echo $class; ?></td>
        <td class="dataTableContent"><?php echo $method; ?></td>
      </tr>
    <?php
    }
  }
?>

</table>

<p class="smallText"><?php echo TEXT_ACTIONS_DIRECTORY . ' ' . DIR_FS_CATALOG . 'includes/actions/'; ?></p>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
