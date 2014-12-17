<?php
/*
  mm_mail_manager.php 
  mail manager, css-oscommerce.com, 2014
  oscommerce v2.3.4
  $Id$
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Copyright (c) 2010 osCommerce
  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  

  require(DIR_WS_INCLUDES . 'template_top.php');
?>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table>
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_MODULES; ?>
                
                </td>
                
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_NOTE; ?>&nbsp;</td>
              
              </tr>
			 <?php  echo '<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_MM_BULKMAIL) . '\'">' . "\n"; ?>
                <td class="dataTableContent"><?php echo TEXT_BULKMAIL;?></td>
                <td class="dataTableContent"><?php echo TEXT_NOTE_BULKMAIL;?></td>
                </tr>
                 <?php  echo '<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_MM_RESPONSEMAIL) . '\'">' . "\n"; ?>
                <td class="dataTableContent"><?php echo TEXT_RESPONSEMAIL;?></td>
                <td class="dataTableContent"><?php echo TEXT_NOTE_RESPONSEMAIL;?></td>
                </tr>
                 <?php  echo '<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_MM_TEMPLATES) . '\'">' . "\n"; ?>
                <td class="dataTableContent"><?php echo TEXT_TEMPLATES;?></td>
                <td class="dataTableContent"><?php echo TEXT_NOTE_TEMPLATES;?></td>
                </tr>
                 <?php  echo '<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_MM_EMAIL) . '\'">' . "\n"; ?>
                <td class="dataTableContent"><?php echo TEXT_EMAIL;?></td>
                <td class="dataTableContent"><?php echo TEXT_NOTE_EMAIL;?></td>
                </tr>
               
                <tr class="dataTableRow">
                <td class="dataTableContent" colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', 1,1); ?></td>
                </tr>
                
              
                </table>
<?php
  require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
