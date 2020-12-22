<li class="nav-item dropdown nb-languages">
  <a class="nav-link dropdown-toggle" href="#" id="navDropdownLanguages" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <?= MODULE_NAVBAR_LANGUAGES_SELECTED_LANGUAGE ?>
  </a>
  <div class="dropdown-menu<?= (('Right' === MODULE_NAVBAR_LANGUAGES_CONTENT_PLACEMENT) ? ' dropdown-menu-right' : '') ?>" aria-labelledby="navDropdownLanguages">
    <?php
    foreach ($lng->catalog_languages as $key => $value) {
      echo '<a class="dropdown-item" href="'
           . tep_href_link($GLOBALS['PHP_SELF'], tep_get_all_get_params(['language', 'currency']) . 'language=' . $key, $GLOBALS['request_type'])
           . '">'
           . tep_image('includes/languages/' .  $value['directory'] . '/images/' . $value['image'], htmlspecialchars($value['name']), null, null, null, false)
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
