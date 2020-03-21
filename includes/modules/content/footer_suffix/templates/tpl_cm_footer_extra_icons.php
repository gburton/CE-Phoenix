<div class="col-sm-<?php echo $content_width; ?> text-center text-sm-right cm-footer-extra-icons">
  <p><?php

  if ( is_string($brand_icons)) {
    echo $brand_icons;
  } else {
    foreach ($brand_icons as $icon ) {
      echo '<i class="fab fa-' . $icon . ' fa-lg"></i> ';
    }
  }

?></p>
</div>

<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/
?>