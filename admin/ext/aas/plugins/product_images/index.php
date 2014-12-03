<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com

*/

defined('AAS') or die;
?>
<div class="overlay" id="product_images">
  <script>
  translate.AAS_PRODUCT_IMAGES_LARGE_IMAGE_PREVIEW="<?php echo AAS_PRODUCT_IMAGES_LARGE_IMAGE_PREVIEW; ?>";
  translate.AAS_PRODUCT_IMAGES_HTML_CONTENT_NOT_UPDATED="<?php echo AAS_PRODUCT_IMAGES_HTML_CONTENT_NOT_UPDATED; ?>";
  translate.AAS_PRODUCT_IMAGES_HTML_CONTENT_UPDATED="<?php echo AAS_PRODUCT_IMAGES_HTML_CONTENT_UPDATED; ?>";
  translate.AAS_PRODUCT_IMAGES_COULD_NOT_DELETE_LARGE_IMAGE="<?php echo AAS_PRODUCT_IMAGES_COULD_NOT_DELETE_LARGE_IMAGE; ?>";
  translate.AAS_PRODUCT_IMAGES_SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN="<?php echo AAS_PRODUCT_IMAGES_SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN; ?>";
  </script>
  <script src="ext/aas/plugins/product_images/js/product_images.js" type="text/javascript"></script>
  <div class="container">
    <div class="top-loriza">
      <span style="font-size:20px;"><?php echo AAS_PRODUCT_IMAGES_TITLE; ?></span>
      <span class="close-button button applyButton"><?php echo AAS_PRODUCT_IMAGES_CLOSE; ?></span>
    </div>
    <div class="product_images-if_image_exists_wrapper"><?php echo AAS_PRODUCT_IMAGES_IF_IMAGE_EXISTS_THEN; ?>
      <select class="duplicate_image_filename_action">
        <option value="0"><?php echo AAS_PRODUCT_IMAGES_OVERWRITE_IT; ?></option>
        <option value="1"><?php echo AAS_PRODUCT_IMAGES_AUTO_RENAME_THE_NEW_IMAGE; ?></option>
        <!--<option value="2">Ask what to do (only for small image)</option>-->
        <option value="3"><?php echo AAS_PRODUCT_IMAGES_CANCEL_IMAGE_UPLOAD; ?></option>
      </select>
    </div>
    <div class="module_content">
        <div class="product_images_container">
        <?php echo AAS_PRODUCT_IMAGES_PRODUCT_NAME; ?><span id="product_images-product_name"></span>
          <div class="margin-20-auto">
            <div class="clear"></div>
              <fieldset class="small_image_fieldset">
                <legend><?php echo AAS_PRODUCT_IMAGES_CURRENT_SMALL_IMAGE; ?><span id="product_images-current_image_path"></span></legend>
                  <div class="small_image_wrapper">
                    <img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D" id="product_images-current-img" alt="product current image" width="<?php echo SMALL_IMAGE_WIDTH; ?>" height="<?php echo SMALL_IMAGE_HEIGHT ?>">
                  </div>
              </fieldset>
<?php if(atleastOneFolderWritable(realpath(DIR_FS_CATALOG_IMAGES))){ ?>
              <fieldset id="dropbox-image">
                <legend><?php echo AAS_PRODUCT_IMAGES_NEW_SMALL_IMAGE; ?></legend>
                <h2><?php echo AAS_PRODUCT_IMAGES_DRAG_N_DROP_NEW_IMAGE_HERE; ?></h2><?php echo AAS_PRODUCT_IMAGES_DRAG_N_DROP_NEW_IMAGE_HERE_OR; ?>&nbsp;&nbsp;<input type="file" accept="image/*" name="products_image" id="products_image_change_default">
                  <fieldset class="dropbox-image-fieldset"><legend><?php echo AAS_PRODUCT_IMAGES_SELECT_FOLDER_TO_SAVE_NEW_IMAGE; ?>&nbsp;<img class="product_images-image_change_save_path_toggle" src="ext/aas/images/circle_plus-15x15.png" alt="toggle folders list"></legend>
                    <ol id="product_images-images_folders">
                    <?php
                      if(!tep_is_writable(DIR_FS_CATALOG_IMAGES)) echo '<li><label style="color:lightGray"><input disabled="disabled" type="radio" name="image_folder" value="'.DIR_FS_CATALOG_IMAGES.'">'.AAS_PRODUCT_IMAGES_IMAGES.'<span class="no-writable">'.AAS_PRODUCT_IMAGES_NO_WRITABLE.'</span></label>';
                      else echo '<li><label><input type="radio" name="product_images-image_folder" value="" class="product_images-image_folder" checked="checked" >'.substr(DIR_FS_CATALOG_IMAGES, strlen(DIR_FS_CATALOG)).'</label>';

                      listFolders(realpath(DIR_FS_CATALOG_IMAGES),'product_images-image_folder','product_images-image_folder');
                    ?>
                    </li>
                    </ol>
                  </fieldset>
              </fieldset>
<?php }else{ ?>
              <p class="no-writable-image-folders-wrapper"><strong><?php echo DIR_FS_CATALOG_IMAGES; ?></strong>&nbsp;<?php echo AAS_PRODUCT_IMAGES_NO_WRITABLE_FOLDERS_WARNING; ?></p>
<?php } ?>
            <div class="clear margin-20-auto"></div>
            <div style="border-top:1px dotted lightGray">
              <div class="clear margin-20-auto"></div>
              <?php echo AAS_PRODUCT_IMAGES_PRODUCT_LARGE_IMAGES; ?>
              <input type="button" value="<?php echo AAS_PRODUCT_IMAGES_ADD_LARGE_IMAGE; ?>" id="add_large_image-button" class="applyButton">
              <!--<input type="button" value="Get Large Images" id="getLargeImages-button" class="applyButton">-->
              <div id="large_images_container"></div>
              <div id="product_images-large_image-list"></div>
                <div id="product_images-large_image-cloner">
                  <fieldset class="product_images-large_image-list product_images-redIt">
                    <legend class="product_images-close_large_image"><img class="product_images-large-image-wrapper-remove-btn" src="ext/aas/images/remove-15x15.png" alt="Remove Large Image"></legend>
                    <div class="product_images_handle"></div>
                    <fieldset class="product_images-dragndrop-noinline product_images-large_image-drag-n-drop-wrapper">
                      <h2><?php echo AAS_PRODUCT_IMAGES_DRAG_N_DROP_NEW_IMAGE_HERE; ?></h2><?php echo AAS_PRODUCT_IMAGES_DRAG_N_DROP_NEW_IMAGE_HERE_OR; ?> <input type="file" accept="image/*" name="products_large_image" class="product_images-large_image-file_select_input">
                    </fieldset>
                    <fieldset class="product_images-large_image-htmlcontent-wrapper">
                    <legend><input type="button" class="applyButton updateHTMLContent" value="<?php echo AAS_PRODUCT_IMAGES_UPDATE_HTML_CONTENT; ?>" /></legend>
                      <textarea class="lfor product_images-large_image-htmlcontent" placeholder="<?php echo AAS_PRODUCT_IMAGES_HTML_CONTENT; ?>"></textarea>
                    </fieldset>
                    <fieldset class="product_images-image_preview">
                      <legend><?php echo AAS_PRODUCT_IMAGES_LARGE_IMAGE_PREVIEW; ?></legend>
                      <div class="product_images-large_image-display"></div>
                    </fieldset>
                    <fieldset class="product_images-image_change_save_path">
                      <legend><?php echo AAS_PRODUCT_IMAGES_CHANGE_NEW_IMAGE_LOCATION; ?>&nbsp;<img class="product_images-image_change_save_path_toggle" src="ext/aas/images/circle_minus-15x15.png" alt="toggle folders list"></legend>
                        <ol class="product_images-large_images_folders"></ol>
                    </fieldset>
                  </fieldset>
                </div>
            </div>
          </div>
      </div>
      <div class="clear"></div>
      </div>
  </div>
<!--
  <div class="dialog" id="dialog-on_duplicate_image_what_to_do" title="What to do?">
    <div class="message">An image with same filename already exists in selected location.</div>
    <div class="clear margin-10-auto">Please select an action:</div>
      <div>
      <ul class="ulawtdodifa">
      <li><label><input type="radio" name="awtdodifa" value="0" checked="checked"> Overwrite old Image</label></li>
      <li><label><input type="radio" name="awtdodifa" value="1"> Auto rename new Image</label></li>
      <li><label><input type="radio" name="awtdodifa" value="2"> User rename new Image: </label> <input type="text" id="awtdodifa-newimagefield" class="lfor" value=""></li>
      </ul>

     </div>

  </div>
-->
</div>
