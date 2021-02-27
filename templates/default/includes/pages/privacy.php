<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  $page = info_pages::get_page(['p.slug' => 'privacy',
                                'pd.languages_id' => (int)$_SESSION['languages_id']]);

  $breadcrumb->add($page['pages_title'], tep_href_link('privacy.php'));

  require $oscTemplate->map_to_template('template_top.php', 'component');
?>

<h1 class="display-4"><?php echo $page['pages_title']; ?></h1>

  <?php echo $page['pages_text']; ?>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fas fa-angle-right', tep_href_link('index.php'), null, null, 'btn-light btn-block btn-lg'); ?></div>
  </div>

<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
