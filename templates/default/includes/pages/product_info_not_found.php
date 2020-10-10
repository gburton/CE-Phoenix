<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require $oscTemplate->map_to_template('template_top.php', 'component');
?>

  <div class="row">
    <?php echo $oscTemplate->getContent('product_info_not_found'); ?>
  </div>
  
<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
