<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com

*/

defined('AAS') or die;
?>
<div id="overlayWindow">
  <div class="container">
    <div class="top-lorida">

      <button id="desc_previous" class="toolbox-descriptionbutton" data-previous="" data-title="<?php echo AAS_BUTTON_TOOLTIP_EDIT_PREVIOUS_PRODUCT_DESCRIPTION; ?>"><?php echo AAS_BUTTON_TEXT_PREVIOUS; ?></button>
      &nbsp;&nbsp;
      <button id="desc_apply_changes" class="toolbox-descriptionbutton" data-title="<?php echo AAS_BUTTON_TOOLTIP_SAVE_ALL_DESC_CHANGES; ?>" style="color:green"><?php echo AAS_BUTTON_TEXT_SUBMIT_CHANGES; ?></button>
      <button id="desc_toggle_editors" class="toolbox-descriptionbutton" data-title="<?php echo AAS_BUTTON_TOOLTIP_TOGGLE_EDITORS; ?>"><?php echo AAS_BUTTON_TEXT_TOGGLE_EDITORS; ?></button>
      <button id="desc_preview_changes" class="toolbox-descriptionbutton" data-title="<?php echo AAS_BUTTON_TOOLTIP_PREVIEW_DESC_CHANGES; ?>"><?php echo AAS_BUTTON_TEXT_PREVIEW_CHANGES; ?></button>
      <button id="desc_reload_preview" class="toolbox-descriptionbutton" data-title="<?php echo AAS_BUTTON_TOOLTIP_RELOAD_ALL_PREVIEWS; ?>"><?php echo AAS_BUTTON_TEXT_RELOAD_PREVIEW; ?></button>
      <button id="desc_close" class="toolbox-descriptionbutton" data-title="<?php echo AAS_BUTTON_TOOLTIP_CLOSE_DESC_EDITING; ?>" style="color:#800"><?php echo AAS_BUTTON_TEXT_CLOSE; ?></button>
      
      &nbsp;&nbsp;
      <button id="desc_next" class="toolbox-descriptionbutton" data-next="" data-title="<?php echo AAS_BUTTON_TOOLTIP_EDIT_NEXT_PORDUCTS_DESC; ?>"><?php echo AAS_BUTTON_TEXT_NEXT; ?></button>
<?php $count_clanguages=sizeof($languages);
      if($count_clanguages > 1){ ?>
      <div id="also_edilanguages"><?php echo AAS_TEXT_EDIT_DESCRIPTION_IN; ?>

        <?php foreach($languages as $key => $clanguage){

            echo '<span class="overlay_language_img" data-alias="'.$clanguage['code'].'" id="lid_'.$clanguage['id'].'">'.tep_image(DIR_WS_CATALOG_LANGUAGES . $clanguage['directory'] . '/images/' . $clanguage['image'], $clanguage['name']).'&nbsp;'.$clanguage['name'].'</span>';
            if($key<$count_clanguages-1) echo ',&nbsp;&nbsp;';

          } ?>
        <?php echo AAS_TEXT_EDIT_DESCRIPTION_IN_LANGUAGE; ?>
      </div>
<?php } ?>
    </div>
    <div style="margin-top:60px;"></div>
    <fieldset id="overlay-fieldset">
    <legend><?php echo AAS_TEXT_LANGUAGE; ?><img src="<?php echo DIR_WS_CATALOG_LANGUAGES . $languages_selected['directory'] . '/images/' . $languages_selected['image']; ?>" alt="<?php echo $languages_selected['name']; ?>" title="<?php echo $languages_selected['name']; ?>" >&nbsp;<?php echo $languages_selected['name']; ?>
    
      &nbsp;&nbsp;<button class="applyButton descriptionbuttonaki" onclick="changeEditor('description_editor')"><?php echo AAS_TEXT_TOGGLE_EDITOR; ?></button>
      &nbsp;<button class="applyButton descriptionbuttonaki" onclick="submitMainDescriptionChanges()"><?php echo AAS_BUTTON_TEXT_SUBMIT_CHANGES; ?></button>
      &nbsp;<button class="applyButton descriptionbuttonaki" onclick="reloadDescriptionPreview('iframias')"><?php echo AAS_BUTTON_TEXT_RELOAD_PREVIEW; ?></button>
    
      <a href="#" id="view_in_new_window" class="view_products_page product_description_action" data-title="<?php echo AAS_TEXT_VIEW_PRODUCTS_PAGE; ?>"><img src="<?php echo DIR_WS_ADMIN.'ext/aas/images/glyphicons_152_new_window.png'; ?>" alt=""></a>
      <?php if(isset($aasAac['fields_disable_action']['attributes'][$_SESSION['admin']['id']]) && $aasAac['fields_disable_action']['attributes'][$_SESSION['admin']['id']] ){ }else{ ?><a href="#" id="edit_products_attributes" data-pid="" data-productname="" class="edit_products_attributes product_description_action" data-title="<?php echo AAS_VIEW_EDIT_ATTRIBUTES; ?>"><img src="<?php echo DIR_WS_ADMIN.'ext/aas/images/glyphicons_048_dislikes.png'; ?>" alt=""></a> <?php } ?>
    </legend>

      <div class="leftPanel">
        <textarea id="description_editor" data-editor="<?php echo $defaults['productsDescriptionEditor']; ?>" class="description_editor"></textarea>
      </div>
      <div class="rightPanel">
        <iframe id="iframias" class="iframia"></iframe>
        <input type="hidden" id="previewid" value="<?php echo isset($defaults['productDescriptionUniqueIdWrapper']) && $defaults['productDescriptionUniqueIdWrapper']!='' ? $defaults['productDescriptionUniqueIdWrapper'] : 'tbl';  ?>" />
        <input type="hidden" id="overlay_pid" name="pid" value="" />
        <input type="hidden" id="overlay_lid" name="lid" value="<?php echo $languages_selected['id']; ?>" />
      </div>
    </fieldset>
    <div style="margin-bottom:20px;"></div>
    <div id="overlay-other-languages" style="margin-bottom:200px;"></div>
    <div class="buttons-lorida">
    <?php echo AAS_TEXT_EDITING; ?> <span id="productName" style="color:green"></span> <?php echo AAS_TEXT_PRODUCTS_DESCRIPTION; ?>
    </div>
  </div>
</div>
