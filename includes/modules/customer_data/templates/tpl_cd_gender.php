<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  $attribute = '';
  if (tep_not_null(ENTRY_GENDER_TEXT)) {
    $attribute = ' aria-describedby="atGender"';
  }

  $attribute .= ' aria-labelledby="atGenderLabel" class="custom-control-input"';
?>
  <div class="form-group row align-items-center">
    <span id="atGenderLabel" class="col-form-label col-sm-3 text-left text-sm-right"><?= ENTRY_GENDER; ?></span>
    <div class="col-sm-9">
<?php
  $fieldset_close = null;
  if ($this->is_required()) {
    echo '    <fieldset aria-required="true">' . "\n";
    $attribute .= ' required';
    $fieldset_close = "    </fieldset>\n";
  }
?>
      <div class="custom-control custom-radio custom-control-inline">
        <?= tep_draw_selection_field('gender', 'radio', 'm', ('m' === $gender), 'id="genderM"' . $attribute); ?>
        <label class="custom-control-label" for="genderM"><?= MALE; ?></label>
      </div>
      <div class="custom-control custom-radio custom-control-inline">
        <?= tep_draw_selection_field('gender', 'radio', 'f', ('f' === $gender), 'id="genderF"' . $attribute); ?>
        <label class="custom-control-label" for="genderF"><?= FEMALE; ?></label>
      </div>
<?php
  if (isset($fieldset_close)) {
    echo $fieldset_close;
  }

  if (tep_not_null(ENTRY_GENDER_TEXT)) {
?>
      <span id="atGender" class="form-text"><small><?= ENTRY_GENDER_TEXT; ?></small></span>
<?php
  }

  if ($this->is_required() && tep_not_null(FORM_REQUIRED_INPUT)) {
?>
      <div class="float-right"><?= FORM_REQUIRED_INPUT; ?></div>
<?php
  }
?>
    </div>
  </div>
