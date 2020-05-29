<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('testimonials.php'));
  
  $page_content = $oscTemplate->getContent('testimonials');

  require $oscTemplate->map_to_template('template_top.php', 'component');
?>

<div class="contentContainer">
  <div class="row">
    <?php echo $page_content; ?>
  </div>
</div>

<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
