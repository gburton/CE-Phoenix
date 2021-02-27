<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

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
