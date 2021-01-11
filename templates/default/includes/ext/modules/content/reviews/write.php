<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('ext/modules/content/reviews/write.php',  tep_get_all_get_params()));

  require $oscTemplate->map_to_template('template_top.php', 'component');
?>

<div class="row">
  <h1 class="display-4 col-sm-8"><?= $product_info['products_name'] ?></h1>
  <h2 class="display-4 col-sm-4 text-left text-sm-right"><?= $products_price ?></h2>
</div>

<?= tep_draw_form('review', tep_href_link('ext/modules/content/reviews/write.php', 'action=process&products_id=' . (int)$_GET['products_id']), 'post', 'enctype="multipart/form-data"', true) ?>

  <div class="alert alert-warning" role="alert">
    <?= sprintf(TEXT_REVIEW_WRITING, htmlspecialchars($customer->get_short_name()), $product_info['products_name']) ?>
  </div>

  <div class="form-group row">
    <label for="inputNickName" class="col-form-label col-sm-3 text-left text-sm-right"><?= SUB_TITLE_FROM ?></label>
    <div class="col-sm-9">
      <?=
       tep_draw_input_field('nickname', htmlspecialchars($customer->get_short_name()), 'required aria-required="true" id="inputNickName" placeholder="' . SUB_TITLE_REVIEW_NICKNAME . '"'),
       FORM_REQUIRED_INPUT
?>
    </div>
  </div>

  <div class="form-group row">
    <label for="inputReview" class="col-form-label col-sm-3 text-left text-sm-right"><?= SUB_TITLE_REVIEW ?></label>
    <div class="col-sm-9">
<?=   tep_draw_textarea_field('review', 'soft', 60, 15, null, 'required aria-required="true" id="inputReview" placeholder="' . SUB_TITLE_REVIEW_TEXT . '"'),
      FORM_REQUIRED_INPUT
?>
    </div>
  </div>

  <div class="form-group row align-items-center">
    <label class="col-form-label col-sm-3 text-left text-sm-right"><?= SUB_TITLE_RATING ?></label>
    <div class="col-sm-9">
      <div class="rating d-flex justify-content-end flex-row-reverse align-items-baseline">
        <?= sprintf(TEXT_GOOD, 5) ?>
        <input type="radio" id="r5" name="rating" required aria-required="true" value="5"><label title="<?= sprintf(TEXT_RATED, sprintf(TEXT_GOOD, 5)) ?>" for="r5">&nbsp;</label>
        <input type="radio" id="r4" name="rating" value="4"><label title="<?= sprintf(TEXT_RATED, 4) ?>" for="r4">&nbsp;</label>
        <input type="radio" id="r3" name="rating" value="3"><label title="<?= sprintf(TEXT_RATED, 3) ?>" for="r3">&nbsp;</label>
        <input type="radio" id="r2" name="rating" value="2"><label title="<?= sprintf(TEXT_RATED, 2) ?>" for="r2">&nbsp;</label>
        <input type="radio" id="r1" name="rating" checked value="1"><label title="<?= sprintf(TEXT_RATED, sprintf(TEXT_BAD, 1)) ?>" for="r1">&nbsp;</label>
      </div>
    </div>
  </div>

  <?= $OSCOM_Hooks->call('write', 'injectFormDisplay') ?>

  <div class="buttonSet">
    <div class="text-right"><?= tep_draw_button(IMAGE_BUTTON_ADD_REVIEW, 'fas fa-pen', null, 'primary', null, 'btn-success btn-lg btn-block') ?></div>
    <p><?= tep_draw_button(IMAGE_BUTTON_BACK, 'fas fa-angle-left', tep_href_link('product_info.php', 'products_id=' . (int)$_GET['products_id']), null, null, 'btn-light mt-2') ?></p>
  </div>

  <hr>

  <div class="row">
    <div class="col-sm-8"><?= $product_info['products_description'] ?>...</div>
    <div class="col-sm-4"><?= tep_image('images/' . $product_info['products_image'], htmlspecialchars($product_info['products_name'])) ?></div>
  </div>

</form>

<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
