<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

	$content = '<div class="col-sm-'.$content_width.' upcoming-products">';
	$content .= '  <table class="table table-striped table-condensed">';
	$content .= '    <tbody>';
	$content .= '      <tr>';
	$content .= '        <th>' . MODULE_CONTENT_UPCOMING_PRODUCTS_TABLE_HEADING_PRODUCTS . '</th>';
	$content .= '        <th class="text-right">' . MODULE_CONTENT_UPCOMING_PRODUCTS_TABLE_HEADING_DATE_EXPECTED . '</th>';
	$content .= '      </tr>';
		
		foreach ( $data as $expected ) {
			$content .= '<tr>';
			$content .= '  <td><a href="' . tep_href_link('product_info.php', 'products_id=' . $expected['master_id']) . '">' . $expected['name'] . '</a></td>';
			$content .= '  <td class="text-right">' . tep_date_short($expected['date_expected']) . '</td>';
			$content .= '</tr>';		
		}	
	
	$content .= '    </tbody>';
	$content .= '  </table>';  
	$content .= '</div>';
		
	echo  $content;
?> 