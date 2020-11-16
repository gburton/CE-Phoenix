<div class="col-sm-<?= $content_width; ?> cm-header-search align-self-center">
  <?= tep_draw_form('quick_find', tep_href_link('advanced_search_result.php', '', $GLOBALS['request_type'], false), 'get', '') . tep_hide_session_id(); ?>
    <div class="input-group">
      <?= tep_draw_input_field('keywords', '', 'required aria-required="true" autocomplete="off" aria-label="' . TEXT_SEARCH_PLACEHOLDER . '" placeholder="' . TEXT_SEARCH_PLACEHOLDER . '"', 'search'); ?>
      <div class="input-group-append">
        <button type="submit" class="btn btn-info"><i class="fas fa-search"></i></button>
      </div>
    </div>
  </form>
</div>

<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/
?>
