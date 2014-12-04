<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com

*/

defined('AAS') or die;
?>
<div class="overlay" id="categories_images">
  <script src="ext/aas/plugins/categories_images/js/categories_images.js" type="text/javascript"></script>
  <div class="container">
    <div class="top-loriza">
        <span style="font-size:20px;"><?php echo AAS_CATEGORY_IMAGES_TITLE; ?></span>
        <span class="close-button button applyButton"><?php echo AAS_CATEGORY_IMAGES_CLOSE; ?></span>
    </div>
    <div class="module_content">
      <div class="categories_images_container">
      <?php echo AAS_CATEGORY_IMAGES_CATEGORY_NAME; ?><span id="categories_images-category_name"></span>
        <div class="margin-20-auto">
          <div class="clear"></div>
            <fieldset class="small_image_fieldset">
              <legend><?php echo AAS_CATEGORY_IMAGES_CURRENT_SMALL_IMAGE; ?><span id="categories_images-current_image_path"></span></legend>
                <div class="small_image_wrapper">
                  <img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D" id="categories_images-current-img" alt="categories current image" width="<?php echo SMALL_IMAGE_WIDTH; ?>" height="<?php echo SMALL_IMAGE_HEIGHT ?>">
                </div>
            </fieldset>
<?php if(atleastOneFolderWritable(realpath(DIR_FS_CATALOG_IMAGES))){ ?>
            <fieldset id="dropbox-image-categories">
              <legend><?php echo AAS_CATEGORY_IMAGES_NEW_SMALL_IMAGE; ?></legend>
              <h2><?php echo AAS_CATEGORY_IMAGES_DRAG_N_DROP_NEW_IMAGE_HERE; ?></h2><?php echo AAS_CATEGORY_IMAGES_DRAG_N_DROP_NEW_IMAGE_HERE_OR; ?>&nbsp;&nbsp;<input type="file" accept="image/*" name="categories_image" id="categories_image_change_default">
                <fieldset class="dropbox-image-fieldset"><legend><?php echo AAS_CATEGORY_IMAGES_SELECT_FOLDER_TO_SAVE_NEW_IMAGE; ?>&nbsp;<img class="categories_images-image_change_save_path_toggle" src="ext/aas/images/circle_minus-15x15.png" alt="toggle folders list"></legend>
                  <ol id="categories_images-images_folders">
                  <?php if(!tep_is_writable(DIR_FS_CATALOG_IMAGES)) echo '<li><label style="color:lightGray"><input disabled="disabled" type="radio" name="image_folder" value="'.DIR_FS_CATALOG_IMAGES.'">'.AAS_CATEGORY_IMAGES_IMAGES.'<span class="no-writable">'.AAS_CATEGORY_IMAGES_NO_WRITABLE.'</span></label>';
                    else echo '<li><label><input type="radio" name="categories_images-image_folder" value="" class="categories_images-image_folder" checked="checked" >'.substr(DIR_FS_CATALOG_IMAGES, strlen(DIR_FS_CATALOG)).'</label>';

                    listFolders(realpath(DIR_FS_CATALOG_IMAGES),'categories_images-image_folder','categories_images-image_folder');
                  ?>
                  </li>
                  </ol>
                </fieldset>
            </fieldset>
<?php }else{ ?>
            <p class="no-writable-image-folders-wrapper"><strong><?php echo DIR_FS_CATALOG_IMAGES; ?></strong>&nbsp;<?php echo AAS_CATEGORY_IMAGES_NO_WRITABLE_FOLDERS_WARNING; ?></p>
<?php } ?>
          <div class="clear margin-20-auto"></div>
        </div>
    </div>
      <div class="clear"></div>
    </div>
  </div>
</div>
