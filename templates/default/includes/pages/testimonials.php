<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('testimonials.php'));
  
  $page_content = $oscTemplate->getContent('testimonials');

  require $oscTemplate->map_to_template('template_top.php', 'component');
?>

  <div class="row">
    <?php echo $page_content; ?>
  </div>

<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
