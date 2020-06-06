<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('account.php', '', 'SSL'));

  require $oscTemplate->map_to_template('template_top.php', 'component');

  if ($messageStack->size('account') > 0) {
    echo $messageStack->output('account');
  }
?>

<div class="contentContainer">
  <div class="row"><?php echo $oscTemplate->getContent('account'); ?></div>
</div>


<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
