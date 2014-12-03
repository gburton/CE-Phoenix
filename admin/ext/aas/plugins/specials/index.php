<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com

  Information: Manage specials like a boss.

*/

defined('AAS') or die;
?>
<div class="overlay" id="specials" style="display:none;">
  <script>
  translate.AAS_SPECIALS_TEXT_ADD="<?php echo AAS_SPECIALS_TEXT_ADD; ?>";
  translate.AAS_SPECIALS_DIALOG_TITLE_CONFIRM_SPECIAL_DELETION="<?php echo AAS_SPECIALS_DIALOG_TITLE_CONFIRM_SPECIAL_DELETION; ?>";
  translate.AAS_SPECIALS_DIALOG_TEXT_STATUS_SUCCESSFULLY_CHANGED="<?php echo AAS_SPECIALS_DIALOG_TEXT_STATUS_SUCCESSFULLY_CHANGED; ?>";
  translate.AAS_SPECIALS_DIALOG_TEXT_SUCCESSFULLY_SET_EXPIRE_DATE="<?php echo AAS_SPECIALS_DIALOG_TEXT_SUCCESSFULLY_SET_EXPIRE_DATE; ?>";
  translate.AAS_SPECIALS_DIALOG_TEXT_DELETE_SPECIAL="<?php echo AAS_SPECIALS_DIALOG_TEXT_DELETE_SPECIAL; ?>";
  translate.AAS_SPECIALS_DIALOG_TEXT_CANNOT_UPDATE_STATUS="<?php echo AAS_SPECIALS_DIALOG_TEXT_CANNOT_UPDATE_STATUS; ?>";
  translate.AAS_SPECIALS_DIALOG_TEXT_COULD_NOT_SET_AVAILABLE_DATE_TO_NULL="<?php echo AAS_SPECIALS_DIALOG_TEXT_COULD_NOT_SET_AVAILABLE_DATE_TO_NULL; ?>";
  translate.AAS_SPECIALS_DIALOG_TEXT_CANNOT_UPDATE_SPECIALS_PRICE="<?php echo AAS_SPECIALS_DIALOG_TEXT_CANNOT_UPDATE_SPECIALS_PRICE; ?>";
  translate.AAS_SPECIALS_DIALOG_TEXT_SUCCESSFULLY_UPDATED_VALUES="<?php echo AAS_SPECIALS_DIALOG_TEXT_SUCCESSFULLY_UPDATED_VALUES; ?>";
  translate.AAS_SPECIALS_DIALOG_TEXT_SUCCESSFULLY_DELETED="<?php echo AAS_SPECIALS_DIALOG_TEXT_SUCCESSFULLY_DELETED; ?>";
  translate.AAS_SPECIALS_DIALOG_TEXT_CANNOT_DELETE_SPECIAL="<?php echo AAS_SPECIALS_DIALOG_TEXT_CANNOT_DELETE_SPECIAL; ?>";
  translate.AAS_SPECIALS_DIALOG_TEXT_EMPTY_SPECIAL_PRICE_FOUND="<?php echo AAS_SPECIALS_DIALOG_TEXT_EMPTY_SPECIAL_PRICE_FOUND; ?>";
  translate.AAS_SPECIALS_DIALOG_TEXT_ADDED_NEW_SPECIAL_LOADING_SPECIALS_TABLE="<?php echo AAS_SPECIALS_DIALOG_TEXT_ADDED_NEW_SPECIAL_LOADING_SPECIALS_TABLE; ?>";
  translate.AAS_SPECIALS_DIALOG_TEXT_ERROR_FETCHING_CELL_DATA="<?php echo AAS_SPECIALS_DIALOG_TEXT_ERROR_FETCHING_CELL_DATA; ?>";
  translate.AAS_SPECIALS_DIALOG_TEXT_CANNOT_ADD_SPECIAL_TRY_AGAIN="<?php echo AAS_SPECIALS_DIALOG_TEXT_CANNOT_ADD_SPECIAL_TRY_AGAIN; ?>";
  translate.AAS_SPECIALS_DIALOG_TEXT_CANNOT_ADD_SPECIAL_NO_VALID_PRODUCT_SELECTION="<?php echo AAS_SPECIALS_DIALOG_TEXT_CANNOT_ADD_SPECIAL_NO_VALID_PRODUCT_SELECTION; ?>";
  translate.AAS_SPECIALS_DIALOG_TEXT_SOMETHING_WENT_WRONG_TRY_AGAIN="<?php echo AAS_SPECIALS_DIALOG_TEXT_SOMETHING_WENT_WRONG_TRY_AGAIN; ?>";
  translate.AAS_SPECIALS_TEXT_NOT_A_SPECIAL_YET="<?php echo AAS_SPECIALS_TEXT_NOT_A_SPECIAL_YET; ?>";
  </script>
  <script src="ext/aas/plugins/specials/js/specials.js" type="text/javascript"></script>
  <div class="container">
    <div class="top-loriza">
      <span style="font-size:20px;"><?php echo AAS_SPECIALS_TITLE; ?></span>
      <span id="add-special-button" class="add-special-button button applyButton"><?php echo AAS_SPECIALS_ADD_PRODUCT; ?></span>
      <span id="reload-special-button" class="add-special-button button applyButton"><?php echo AAS_SPECIALS_RELOAD; ?></span>
      <span class="close-button button applyButton"><?php echo AAS_SPECIALS_CLOSE; ?></span>
    </div>
    <div class="module_content">
      <div style="margin: 20px auto;background:#fff">
        <table id="tbl_specials" class="tbl-general tablesorter">
        <thead><tr><th><?php echo AAS_SPECIALS_HEADING_PRODUCTS; ?></th><th><?php echo AAS_SPECIALS_HEADING_PRODUCTS_PRICE; ?></th><th><?php echo AAS_SPECIALS_HEADING_PERCENTAGE; ?></th><th><?php echo AAS_SPECIALS_HEADING_STATUS; ?></th><th><?php echo AAS_SPECIALS_HEADING_DATE_ADDED; ?></th><th><?php echo AAS_SPECIALS_HEADING_LAST_MODIFIED; ?></th><th><?php echo AAS_SPECIALS_HEADING_EXPIRES_AT; ?></th><th><?php echo AAS_SPECIALS_HEADING_ACTIONS; ?></th></tr></thead>
        <tbody></tbody>
        </table>
      </div>
      <div class="clear"></div>
    </div>
  </div>
</div>
