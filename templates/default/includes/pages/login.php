<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('login.php', '', 'SSL'));

  require $oscTemplate->map_to_template('template_top.php', 'component');

  if ($messageStack->size('login') > 0) {
    echo $messageStack->output('login');
  }
?>

  <div class="row">
    <?php echo $page_content; ?>
  </div>

<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
