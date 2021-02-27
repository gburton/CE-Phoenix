<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('account.php', '', 'SSL'));

  require $oscTemplate->map_to_template('template_top.php', 'component');

  if ($messageStack->size('account') > 0) {
    echo $messageStack->output('account');
  }
?>

<div class="row"><?php echo $oscTemplate->getContent('account'); ?></div>

<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
