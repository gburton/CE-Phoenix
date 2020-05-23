<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require('includes/languages/' . $language . '/testimonials.php');

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('testimonials.php'));
  
  $page_content = $oscTemplate->getContent('testimonials');

  require('includes/template_top.php');
?>

  <div class="row">
    <?php echo $page_content; ?>
  </div>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
