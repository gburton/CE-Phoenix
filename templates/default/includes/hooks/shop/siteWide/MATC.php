<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

class hook_shop_siteWide_MATC {

  function listen_injectFormDisplay() {
    if ($this->show_pages() === true) {
      $this->load_lang();

      $title = ENTRY_MATC;
      $checkbox = tep_draw_selection_field('matc', 'checkbox', 1, false, 'required aria-required="true" class="custom-control-input" id="inputMATC"');
      $label = ENTRY_MATC_TEXT;

      $matc = <<<eod
<div class="form-group row align-items-center">
  <div class="col-form-label col-sm-3 text-left text-sm-right">{$title}</div>
  <div class="col-sm-9 pl-5 custom-control custom-switch">
    {$checkbox}
    <label for="inputMATC" class="custom-control-label text-muted"><small>{$label}</small></label>
  </div>
</div>
eod;

      return $matc;
    }
  }

  function listen_injectSiteEnd() {
    if ($this->show_pages() === true) {
      $this->load_lang();

      $close_button   = MATC_BUTTON_CLOSE;

      $p_modal = info_pages::get_page(['p.slug' => 'privacy',
                                       'pd.languages_id' => $_SESSION['languages_id']]);
      $c_modal = info_pages::get_page(['p.slug' => 'conditions',
                                       'pd.languages_id' => $_SESSION['languages_id']]);

      $modal = <<<eod
<div class="modal fade" id="PModal" tabindex="-1" role="dialog" aria-labelledby="PModalLabel" aria-hidden="true"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="PModalLabel">{$p_modal['pages_title']}</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><div class="modal-body">{$p_modal['pages_text']}</div><div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{$close_button}</button></div></div></div></div><div class="modal fade" id="TCModal" tabindex="-1" role="dialog" aria-labelledby="TCModalLabel" aria-hidden="true"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="TCModalLabel">{$c_modal['pages_title']}</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><div class="modal-body">{$c_modal['pages_text']}</div><div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{$close_button}</button></div></div></div></div>
eod;

      return $modal;
    }
  }

  function load_lang() {
    if (!defined('ENTRY_MATC')) {
      require language::map_to_translation('hooks/shop/siteWide/MATC.php');
    }
  }

  function show_pages() {
    $good_pages = ['create_account.php', 'checkout_confirmation.php'];

    return in_array(basename($GLOBALS['PHP_SELF']), $good_pages);
  }

}
