<?php
/*
  $Id: qtprodoctor.php
  $Loc: catalog/admin/
      
  2017 QTPro 5.0 BS
  by @raiwa 
  info@oscaddons.com
  www.oscaddons.com
  
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  require('includes/template_top.php');
  
  if (!defined('MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_STATUS')) {
    echo '<div class="secWarning">' . QTPRO_OPTIONS_WARNING . '<br>
          <a href="modules_content.php?module=cm_pi_qtpro_options&action=install">' . QTPRO_OPTIONS_INSTALL_NOW . '</a></div>';
  }
  if ( !defined('MODULE_HEADER_TAGS_QTPRO_STOCK_CHECK_STATUS') || (defined('MODULE_HEADER_TAGS_QTPRO_STOCK_CHECK_STATUS') && MODULE_HEADER_TAGS_QTPRO_STOCK_CHECK_STATUS != 'True') ) {
    echo '<div class="secWarning">' . QTPRO_HT_WARNING . '<br>
                                <a href="modules.php?set=header_tags&module=ht_qtpro_stock_check&action=install">' . QTPRO_HT_INSTALL_NOW . '</a></div>';
  }

  $doctor_action = null;

  if(isset($HTTP_GET_VARS['action'])){
  	$doctor_action = $HTTP_GET_VARS['action'];
  }
  
  if(isset($HTTP_GET_VARS['pID'])){
  	$products_id = $HTTP_GET_VARS['pID'];
  }
  
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading">
            <?php echo HEADING_TITLE; ?>
            </td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table>
		</td>
      </tr>
      <tr>
        <td>
		<?php 
			switch($doctor_action){
				case 'examine':
					if (qtpro_doctor_product_healthy($products_id)) {
						print TEXT_EXAMINE_HEALTHY;
					} else {
						print TEXT_EXAMINE_MESSED;
					}
				break;
				case 'amputate':
					print sprintf(TEXT_AMPUTATE, qtpro_doctor_amputate_bad_from_product($products_id));
					qtpro_update_summary_stock($products_id);
				break;
				case 'chuck_trash':
					print sprintf(TEXT_CHUCK_TRASH, qtpro_chuck_trash());
				break;
				case 'update_summary':
					qtpro_update_summary_stock($products_id);
					print TEXT_UPDATE_SUMMARY;
				break;
				
				
				
				default:
					print '<h1 class="pageHeading">' . PAGE_HEADING . '</h1>';
					print sprintf(TEXT_PRODUCT_COUNT, qtpro_normal_product_count());
					print sprintf(TEXT_PRODUCT_TRACKED_STOCK, qtpro_tracked_product_count());
					print sprintf(TEXT_PRODUCT_TRASH_ROWS, qtpro_number_of_trash_stock_rows());
					print sprintf(TEXT_PRODUCT_SICK, qtpro_sick_product_count());
					qtpro_doctor_formulate_database_investigation();

					
				break;
			
			}
		?>

		</td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
    </table>
        <?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>