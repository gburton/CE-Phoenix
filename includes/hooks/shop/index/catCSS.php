<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

class hook_shop_index_catCSS {

  function listen_injectSiteStart() {
    if (defined('MODULE_CONTENT_IN_CATEGORY_LISTING_STATUS') && (MODULE_CONTENT_IN_CATEGORY_LISTING_STATUS == 'True')) {
      $sm_count = MODULE_CONTENT_IN_CATEGORY_LISTING_DISPLAY_ROW_SM;
      $md_count = MODULE_CONTENT_IN_CATEGORY_LISTING_DISPLAY_ROW_MD;
      $lg_count = MODULE_CONTENT_IN_CATEGORY_LISTING_DISPLAY_ROW_LG;
      $xl_count = MODULE_CONTENT_IN_CATEGORY_LISTING_DISPLAY_ROW_XL;
      
      $sm = (100/$sm_count);
      $md = (100/$md_count);
      $lg = (100/$lg_count);
      $xl = (100/$xl_count);

      $catCSS = <<<eod
<style>@media (min-width: 576px) {.cm-in-category-listing > .card-group > .card {max-width: {$sm}%;}.cm-in-category-listing > .card-deck > .card {max-width: calc({$sm}% - 30px);}.cm-in-category-listing > .card-columns {column-count: {$sm_count};}} @media (min-width: 768px) {.cm-in-category-listing > .card-group > .card {max-width: {$md}%;}.cm-in-category-listing > .card-deck > .card {max-width: calc({$md}% - 30px);}.cm-in-category-listing > .card-columns {column-count: {$md_count};}} @media (min-width: 992px) {.cm-in-category-listing > .card-group > .card {max-width: {$lg}%;}.cm-in-category-listing > .card-deck > .card {max-width: calc({$lg}% - 30px);}.cm-in-category-listing > .card-columns {column-count: {$lg_count};}} @media (min-width: 1200px) {.cm-in-category-listing > .card-group > .card {max-width: {$xl}%;}.cm-in-category-listing > .card-deck > .card {max-width: calc({$xl}% - 30px);}.cm-in-category-listing > .card-columns {column-count: {$xl_count};}}</style>
eod;

      return $catCSS;
    }
  }

}
