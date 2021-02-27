<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('gdpr.php'));

  $page_content = $oscTemplate->getContent('gdpr');

  $OSCOM_Hooks->call('gdpr', 'portData');

  require $oscTemplate->map_to_template('template_top.php', 'component');
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

  <div class="row">
    <?php echo $page_content; ?>
  </div>

<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
