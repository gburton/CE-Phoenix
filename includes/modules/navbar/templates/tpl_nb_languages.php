<li class="nav-item dropdown nb-languages">
  <a class="nav-link dropdown-toggle" href="#" id="navDropdownLanguages" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <?= MODULE_NAVBAR_LANGUAGES_SELECTED_LANGUAGE ?>
  </a>
  <div class="dropdown-menu<?= (('Right' === MODULE_NAVBAR_LANGUAGES_CONTENT_PLACEMENT) ? ' dropdown-menu-right' : '') ?>" aria-labelledby="navDropdownLanguages">
    <?php
    foreach ($lng->catalog_languages as $key => $value) {
      $image = Text::ltrim_once(language::map_to_translation("images/{$value['image']}", $value['directory']), DIR_FS_CATALOG);
      echo '<a class="dropdown-item" href="'
           . tep_href_link($GLOBALS['PHP_SELF'], tep_get_all_get_params(['language', 'currency']) . 'language=' . $key)
           . '">'
           . tep_image($image, htmlspecialchars($value['name']), '', '', '', false)
           . ' ' . $value['name'] . '</a>' . PHP_EOL;
    }
    ?>
  </div>
</li>

<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/
?>
