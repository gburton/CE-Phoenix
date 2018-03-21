<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/
	$content = '<div class="col-sm-<?php echo $content_width; ?> category-listing">';
	$content .= '  <div itemscope itemtype="http://schema.org/ItemList">';
	$content .= '      <meta itemprop="itemListOrder" content="http://schema.org/ItemListUnordered" />';
	$content .= '      <meta itemprop="name" content="' . $category['title'] .'" />';

		foreach($data as $categories) {
			$content .= '<div class="col-sm-' . $category_width . '">';
			$content .= '  <div class="text-center">';
			$content .= '    <a href="' . tep_href_link('index.php', 'cPath=' . $categories['id']) . '">' . tep_image('images/' . $categories['image'], htmlspecialchars($categories['title']), SUBCATEGORY_IMAGE_WIDTH, SUBCATEGORY_IMAGE_HEIGHT) . '</a>';
			$content .= '    <div class="caption text-center">';
			$content .= '      <h5><a href="' . tep_href_link('index.php', 'cPath=' . $categories['id']) . '"><span itemprop="itemListElement">' . $categories['title'] . '</span></a></h5>';
			$content .= '    </div>';
			$content .= '  </div>';
			$content .= '</div>';
		}
	$content .= '  </div>';    
	$content .= '</div>';
	
	echo $content;
?>
