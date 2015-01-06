<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com

  File information: contains the popup dialogs.

*/
defined('AAS') or die;
?>
<div class="dialog" id="dialog-unique-id-wrapper" title="<?php echo AAS_DIALOG_TITLE_WARNING_INFORMATION; ?>">
  <div style="color:#400;text-align:center;margin:15px auto;font-weight:bold;font-size:15px;"><?php echo AAS_DIALOGS_PHP_PRODUCT_DESCRIPTION_UNIQUE_ID_WRAPPER_USE_THIS_ONLY; ?></div>
  <p style="margin:20px auto;"><?php echo AAS_DIALOGS_PHP_PRODUCT_DESCRIPTION_UNIQUE_ID_WRAPPER_IN_ORDER; ?></p>
  <h1><?php echo AAS_DIALOGS_PHP_PRODUCT_DESCRIPTION_UNIQUE_ID_WRAPPER_INSTRUCTIONS; ?></h1>
  <hr />
  <?php echo AAS_DIALOGS_PHP_PRODUCT_DESCRIPTION_UNIQUE_ID_WRAPPER_OPEN_EDIT; ?>
  <div style="margin:10px auto;font-size:14px;color:#333">
    <pre style="color:#a00">&lt;?php echo stripslashes($product_info['products_description']); ?&gt;</pre>
    <?php echo AAS_DIALOGS_PHP_PRODUCT_DESCRIPTION_UNIQUE_ID_WRAPPER_TO; ?>
    <pre style="color:#060">&lt;?php echo '&lt;span id="aas"&gt;'.stripslashes($product_info['products_description']).'&lt;/span&gt;'; ?&gt;</pre>
    <?php echo AAS_DIALOGS_PHP_PRODUCT_DESCRIPTION_UNIQUE_ID_WRAPPER_OR; ?>
    <pre style="color:#060">&lt;span id="aas"&gt;&lt;?php echo stripslashes($product_info['products_description']); ?&gt;&lt;/span&gt;</pre>
    <div style="margin:20px auto;"></div>
    <?php  echo AAS_DIALOGS_PHP_PRODUCT_DESCRIPTION_UNIQUE_ID_WRAPPER_WHERE; ?> "<strong>aas</strong>" <?php echo AAS_DIALOGS_PHP_PRODUCT_DESCRIPTION_UNIQUE_ID_WRAPPER_WHERE_IS; ?>
  </div>
  <div style="margin:20px auto;font-size:14px;color:#333">
    <p><?php echo AAS_DIALOGS_PHP_PRODUCT_DESCRIPTION_UNIQUE_ID_WRAPPER_SAVE; ?></p>
    <p><?php echo AAS_DIALOGS_PHP_PRODUCT_DESCRIPTION_UNIQUE_ID_WRAPPER_NOW; ?></p>
    <p><?php echo AAS_DIALOGS_PHP_PRODUCT_DESCRIPTION_UNIQUE_ID_WRAPPER_SPAN; ?></p>
  </div>
</div>

<div class="dialog" id="dialog-error-unique-id-wrapper-not-found" title="<?php echo AAS_DIALOG_TITLE_ERROR; ?>">
  <div><?php echo AAS_DIALOGS_PHP_ERROR_UNIQUE_ID_WRAPPER_I_COULD_NOT; ?>&nbsp;<span id="uidwe"></span></div>
  <br /><br />
  <button id="uidwo"><?php echo AAS_DIALOGS_PHP_ERROR_UNIQUE_ID_WRAPPER_CLICK_HERE_TO_FIND_MORE; ?></button>
</div>

<div class="dialog" id="dialog-general" title="<?php echo AAS_DIALOG_TITLE_GENERAL; ?>">
  <div class="message" style="margin:10px 0;"></div>
</div>

<div class="dialog" id="dialog-success" title="<?php echo AAS_DIALOG_TITLE_SUCCESS; ?>!">
  <div class="message" style="margin:10px 0;"></div>
</div>

<div class="dialog" id="dialog-error" title="<?php echo AAS_DIALOG_TITLE_ERROR; ?>">
  <div class="message"></div>
</div>

<div class="dialog" id="dialog-ajaxFailed" title="<?php echo AAS_DIALOG_TITLE_AJAX_FAILED; ?>">
  <div class="message"><?php echo AAS_DIALOG_TEXT_AJAX_FAILED; ?></div>
</div>

<div class="dialog" id="dialog-confirm" title="<?php echo AAS_DIALOG_TITLE_CONFIRM_ACTION; ?>">
  <div class="message"></div>
</div>

<div class="dialog" id="dialog-processing" title="<?php echo AAS_DIALOG_TITLE_PROCESSING; ?>">
  <div class="message"></div>
  <div><img src="ext/aas/images/ajax-loader.gif" alt="loading" ></div>
</div>

<?php if($fieldsArray['products_linked']['visible']){ ?>
<div class="dialog" id="dialog-remove-linked-product-confirm" title="<?php echo AAS_DIALOG_TITLE_REMOVE_LINKED_PRODUCT; ?>">
  <div class="message"><?php echo AAS_DIALOG_TEXT_REMOVE_LINKED_PRODUCT_TEXT; ?></div>
  <div class="message note"><?php echo AAS_DIALOG_TEXT_REMOVE_LINKED_PRODUCT_TEXT_NOTE; ?></div>
</div>

<div class="dialog" id="dialog-remove-linked-product-from-parent-confirm" title="<?php echo AAS_DIALOG_TITLE_REMOVE_LINKED_PRODUCT_FROM_PARENT; ?>">
  <div class="message"><?php echo AAS_DIALOG_TEXT_REMOVE_LINKED_PRODUCT_FROM_PARENT_TEXT; ?></div>
  <div class="message note"><?php echo AAS_DIALOG_TEXT_REMOVE_LINKED_PRODUCT_FROM_PARENT_TEXT_NOTE; ?></div>
</div>
<?php } ?>

<div class="dialog" id="dialog-attributes" title="<?php echo AAS_DIALOG_TITLE_PRODUCT_ATTRIBUTES; ?>">
  <div id="dialog-attributes-options"></div>
</div>

<?php if($massColumnsEdit){ ?>
<div class="dialog" id="dialog-massColumnsEdit" title="<?php echo AAS_DIALOG_TITLE_MASS_COLUMNS_EDIT; ?>">
  <div class="message">
    <p style="display:inline-block;font-size:10px;"><?php echo AAS_DIALOG_TEXT_MASS_EDIT; ?></p>

    <select id="massColumnsEditOptions-select-sta">
      <option value="2" <?php echo $show_products_by_status==2 ? 'selected="selected"' :''; ?>><?php echo AAS_DIALOG_TEXT_MASS_EDIT_ALL_STATUS; ?></option>
      <option value="1" <?php echo $show_products_by_status==1 ? 'selected="selected"' :''; ?>><?php echo AAS_DIALOG_TEXT_MASS_EDIT_ACTIVE_STATUS; ?></option>
      <option value="0" <?php echo $show_products_by_status==0 ? 'selected="selected"' :''; ?>><?php echo AAS_DIALOG_TEXT_MASS_EDIT_INACTIVE_STATUS; ?></option>
    </select>

		<select id="massColumnsEditOptions-select-list" onchange="massColumnsEditOptions(this)">
			<option value="1"><?php echo AAS_DIALOG_TEXT_MASS_EDIT_SELECTED_PRODUCTS; ?></option>
			<option value="2"><?php echo AAS_DIALOG_TEXT_MASS_EDIT_SELECTED_PRODUCTS_FROM_TEMP_LIST; ?></option>
			<option value="3"><?php echo AAS_DIALOG_TEXT_MASS_EDIT_PRODUCTS_FROM; ?></option>
		</select>
  
    <select id="massColumnsEditOptions-select-cats">
      <?php echo $cats_fields; ?>
    </select>

    <select id="massColumnsEditOptions-select-rec">
      <option value="1"><?php echo AAS_TEXT_RECURSIVELY; ?></option>
      <option value="2"><?php echo AAS_TEXT_NON_RECURSIVELY; ?></option>
    </select>
    
    <img id="massColumnsEditOptions-select-rec-explain" class="explain-icon" src="ext/aas/images/glyphicons_194_circle_question_mark.png" alt="">
    <div class="info-explain"><?php echo AAS_DIALOG_TEXT_MASS_EDIT_RECURSIVELY_OPTION_EXPLAIN; ?></div>
        
    <div class="clear margin-20-auto"></div>
    <table class="tbl-attributes tablesorter">
      <thead>
      <tr><th><?php echo AAS_DIALOG_TEXT_HEADING_COLUMN; ?></th><th><?php echo AAS_DIALOG_TEXT_HEADING_VALUE; ?></th><th><?php echo AAS_DIALOG_TEXT_HEADING_OPTION; ?></th><th><?php echo AAS_DIALOG_TEXT_HEADING_ACTION; ?></th></tr>
      </thead>
      <tbody>
<?php foreach($fieldsArray as $key => $value){

    if($value['visible'] && $value['massEdit']){

      if(isset($aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']]) && $aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']] ) continue;

      switch($key){

        case'products_weight':
        case'products_quantity':
        ?>
        <tr data-column="<?php echo $key; ?>">
          <td><?php echo $value['theadText']; ?></td>
          <td><input type="number" name="massColumnsEditValue" class="massColumnsEditValue lfor" placeholder="Value" value="0" min="0" required="required"></td>
          <td>
            <select class="massColumnsEditSelectOption" name="massColumnsEditSelectOption">
            <option value="=">=</option>
            <option value="-">-</option>
            <option value="+">+</option>
            </select>
          </td>
          <td><input type="button" class="applyButton massColumnsEdit-applyButton" value="<?php echo AAS_DIALOG_TEXT_BUTTON_APPLY; ?>">
          </td>
        </tr>
        <?php break;
        case'products_price': ?>
        <tr data-column="<?php echo $key; ?>">
          <td><?php echo $value['theadText']; ?></td>
          <td><input type="number" name="massColumnsEditValue" class="massColumnsEditValue lfor" placeholder="Value" value="0" min="0" required="required"></td>
          <td>
            <select class="massColumnsEditSelectOption" name="massColumnsEditSelectOption">
            <option value="=">=</option>
            <option value="-%">-%</option>
            <option value="+%">+%</option>
            <option value="-">-</option>
            <option value="+">+</option>
            </select>
          </td>
          <td><input type="button" class="applyButton massColumnsEdit-applyButton" value="<?php echo AAS_DIALOG_TEXT_BUTTON_APPLY; ?>">
          </td>
        </tr>
      <?php break;
      case'products_price_gross': ?>
        <tr data-column="<?php echo $key; ?>">
          <td><?php echo $value['theadText']; ?></td>
          <td><input type="number" name="massColumnsEditValue" class="massColumnsEditValue lfor" placeholder="Value" value="0" min="0" required="required"></td>
          <td>
            <select class="massColumnsEditSelectOption" name="massColumnsEditSelectOption">
            <option value="=">=</option>
            <option value="-%">-%</option>
            <option value="+%">+%</option>
            <option value="-">-</option>
            <option value="+">+</option>
            </select>
          </td>
          <td><input type="button" class="applyButton massColumnsEdit-applyButton" value="<?php echo AAS_DIALOG_TEXT_BUTTON_APPLY; ?>">
          </td>
        </tr>
      <?php break;
      case'products_status': ?>
         <tr data-column="<?php echo $key; ?>">
          <td><?php echo $value['theadText']; ?></td>
          <td>
            <select class="massColumnsEditValue" name="massColumnsEditValue">
              <option value="1"><?php echo AAS_DIALOG_TEXT_ACTIVE; ?></option>
              <option value="0"><?php echo AAS_DIALOG_TEXT_INACTIVE; ?></option>
            </select>
          </td>
          <td>
            ---
          </td>
          <td><input type="button" class="applyButton massColumnsEdit-applyButton" value="<?php echo AAS_DIALOG_TEXT_BUTTON_APPLY; ?>">
          </td>
        </tr>
      <?php break;
      case'manufacturers_name': ?>
        <tr data-column="manufacturers_id">
          <td><?php echo $value['theadText']; ?></td>
          <td>
            <select class="massColumnsEditValue" name="massColumnsEditValue" >
              <option value="0" ><?php echo AAS_NONE; ?></option>
              <?php foreach($manufacturers_array as $man) echo '<option value="'.$man['id'].'" >'.$man['name'].'</option>'; ?>
            </select>
          </td>
          <td>
            ---
          </td>
          <td><input type="button" class="applyButton massColumnsEdit-applyButton" value="<?php echo AAS_DIALOG_TEXT_BUTTON_APPLY; ?>">
          </td>
        </tr>
      <?php break;
      case'date_added':?>
       <tr data-column="<?php echo 'products_'.$key; ?>">
        <td><?php echo $value['theadText']; ?></td>
        <td>
          <input type="text" name="massColumnsEditValue" class="massColumnsEditValue lfor datepickerMass_date_added" placeholder="Date" value="">
        </td>
        <td>
          ---
        </td>
        <td><input type="button" class="applyButton massColumnsEdit-applyButton" value="<?php echo AAS_DIALOG_TEXT_BUTTON_APPLY; ?>">
        </td>
      </tr>
      <?php break;
      case'products_date_available': ?>
       <tr data-column="<?php echo $key; ?>">
        <td><?php echo $value['theadText']; ?></td>
        <td>
          <input type="text" name="massColumnsEditValue" class="massColumnsEditValue lfor datepickerMass" placeholder="Date" value="">
        </td>
        <td>
          ---
        </td>
        <td><input type="button" class="applyButton massColumnsEdit-applyButton" value="<?php echo AAS_DIALOG_TEXT_BUTTON_APPLY; ?>">
        </td>
      </tr>
      <?php break;
      case'tax_class_title': ?>
       <tr data-column="products_tax_class_id">
        <td><?php echo $value['theadText']; ?></td>
        <td>
          <select class="massColumnsEditValue" name="massColumnsEditValue" id="tcts_<?php echo (int)$products['products_id']; ?>">
            <option value="0" selected="selected"><?php echo AAS_NONE; ?></option>
            <?php foreach($tax_class_array as $tca) echo '<option value="'.$tca['id'].'_'.$tca['tax_rate'].'" >'.$tca['title'].' '.$tca['tax_rate'].'%</option>'; ?>
          </select>
        </td>
        <td>
          ---
        </td>
        <td><input type="button" class="applyButton massColumnsEdit-applyButton" value="<?php echo AAS_DIALOG_TEXT_BUTTON_APPLY; ?>">
        </td>
      </tr>
      <?php
      break;

      }

   }

      } ?>
      </tbody>
    </table>
    <div class="clear margin-10-auto"></div>
  </div>
</div>
<?php } ?>

<div class="dialog" id="dialog-export" title="<?php echo AAS_DIALOG_TITLE_EXPORT; ?>">
  <div id="dialog-export-message" style="margin:10px 0;text-align:center">
    <div style="margin:10px auto;"><?php echo AAS_DIALOG_EXPORT_MESSAGE; ?></div>
    <form name="exportForm" id="exportForm" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post">
      <select id="select-export-type" name="select-export-type">
        <option value="csv"><?php echo AAS_DIALOG_EXPORT_TYPE_CSV; ?></option>
        <option value="json"><?php echo AAS_DIALOG_EXPORT_TYPE_JSON; ?></option>
        <option value="txt"><?php echo AAS_DIALOG_EXPORT_TYPE_TEXT; ?></option>
        <option value="xls"><?php echo AAS_DIALOG_EXPORT_TYPE_EXCEL; ?></option>
      </select>
      <select id="select-export-delimeter" name="select-export-delimeter">
        <option value=","><?php echo AAS_DIALOG_EXPORT_DEL_COMMA; ?></option>
        <option value=";"><?php echo AAS_DIALOG_EXPORT_DEL_SEMI_COLON; ?></option>
        <option value="~"><?php echo AAS_DIALOG_EXPORT_DEL_TILDE; ?></option>
        <option value="*"><?php echo AAS_DIALOG_EXPORT_DEL_SPLAT; ?></option>
        <option value="tab"><?php echo AAS_DIALOG_EXPORT_DEL_TAB; ?></option>
      </select>
      <input type="hidden" value="0" id="hidden-export-what-to-export" name="what_to_export">
    </form>
    <div style="margin:10px auto;color:#800;font-size:12px;"><?php echo AAS_DIALOG_EXPORT_MESSAGE_JSON_NOTE; ?></div>
  </div>
</div>

<div class="dialog" id="dialog-massProductsDelete" title="<?php echo AAS_DIALOG_TITLE_DELETE_SELECTED_PRODUCTS; ?>">
<div id="dialog-massProductsDelete-message" style="margin:10px 0;">
<?php echo AAS_DIALOGS_PHP_MASS_DELETE_ARE_YOU_SURE; ?>
<div style="margin:20px;color:#800"><?php echo AAS_DIALOGS_PHP_MASS_DELETE_WARNING;?></div>
</div>
</div>

<div class="dialog" id="dialog-information" title="<?php echo AAS_DIALOG_TITLE_INFORMATION; ?>">
  <div class="message" style="text-align:left;"></div>
</div>

<div class="dialog" id="dialog-warning" title="<?php echo AAS_DIALOG_TITLE_WARNING; ?>">
  <div class="message" style="text-align:center;"></div>
</div>

<div class="dialog" id="dialog-sessiontimeout" title="<?php echo AAS_DIALOG_TITLE_SESSION_TIMEOUT; ?>">
  <div class="message"><?php echo AAS_DIALOGS_PHP_SESSION_TIMEOUT; ?></div>
</div>

<div class="dialog" id="dialog-settings" title="<?php echo AAS_DIALOG_TITLE_SETTINGS; ?>">
  <div id="radio-show-alerts" style="text-align:right;">
    <label><?php echo AAS_SETTINGS_DISPLAY_SUCCESS_MESSAGES; ?>
    <select onchange="settings(this.value,'show_success_alert_messages')">
      <option <?php echo $show_success_alert_messages ? 'selected="selected"' : ''; ?> value="1"><?php echo AAS_DIALOG_BUTTON_YES; ?></option>
      <option <?php echo !$show_success_alert_messages ? 'selected="selected"' : ''; ?> value="0"><?php echo AAS_DIALOG_BUTTON_NO; ?></option>
    </select>
    </label>

    <div class="clear margin-10-auto"></div>
    <label><?php echo AAS_SETTINGS_DISPLAY_ERROR_MESSAGES; ?>
    <select onchange="settings(this.value,'show_error_alert_messages')">
      <option <?php echo $show_error_alert_messages ? 'selected="selected"' : ''; ?> value="1"><?php echo AAS_DIALOG_BUTTON_YES; ?></option>
      <option <?php echo !$show_error_alert_messages ? 'selected="selected"' : ''; ?> value="0"><?php echo AAS_DIALOG_BUTTON_NO; ?></option>
    </select>
    </label>

    <div class="clear margin-10-auto"></div>
    <label><?php echo AAS_SETTINGS_ENABLE_COLUMN_SORTING; ?>
    <select onchange="settings(this.value,'enable_column_sorting')">
      <option <?php echo $enableColumnSorting ? 'selected="selected"' : ''; ?> value="1"><?php echo AAS_DIALOG_BUTTON_YES; ?></option>
      <option <?php echo !$enableColumnSorting ? 'selected="selected"' : ''; ?> value="0"><?php echo AAS_DIALOG_BUTTON_NO; ?></option>
    </select>
    </label>

    <div class="clear margin-10-auto"></div>
    <label><?php echo AAS_SETTINGS_RESET_TOOLBOX_HEIGHT; ?> <button class="applyButton" onclick="toolbox_reset_height()"><?php echo AAS_SETTINGS_RESET_BUTTON; ?></button></label>

    <div class="clear margin-10-auto"></div>
    <label><?php echo AAS_SETTINGS_RESET_COLUMNS_ORDER; ?> <button class="applyButton" onclick="reset_columns_order()"><?php echo AAS_SETTINGS_RESET_BUTTON; ?></button></label>

    <?php if($_SESSION['admin']['id']=='1'){ ?>
    <div class="clear margin-10-auto"></div>
    <label><?php echo AAS_DIALOG_SETTINGS_AO; ?><button id="aacTriggerButton" class="applyButton"><?php echo AAS_DIALOG_SETTINGS_EDIT; ?></button></label>
    <?php } ?>

  </div>
</div>

<div class="dialog" id="dialog-massedit" title="<?php echo AAS_DIALOG_TITLE_ALL_EDIT; ?>">
  <div class="message"><?php echo AAS_DIALOG_ALL_EDIT_MESSAGE; ?></div>
  <div class="clear margin-10-auto"></div>
  <p class="note"><?php echo AAS_DIALOG_ALL_EDIT_MESSAGE_NOTE_1; ?></p>
  <p class="note"><?php echo AAS_DIALOG_ALL_EDIT_MESSAGE_NOTE_2; ?></p>
</div>

<div class="dialog" id="dialog-calendar" title="<?php echo AAS_DIALOG_TITLE_CALENDAR; ?>">
  <div class="message"></div>
  <input type="text" name="calendar-title-field" id="calendar-title-field" class="lfora" placeholder="Event Title" style="display:inline-block;">
  <div class="clear margin-10-auto"></div>
  <textarea id="calendar-notes-field" class="lfora" placeholder="Event Notes" style="height:100px;" ></textarea>
</div>

<div class="dialog" id="dialog-specials" title="<?php echo AAS_DIALOG_TITLE_SPECIALS; ?>">
  <div class="message"></div>
    <label><?php echo AAS_SPECIALS_DIALOG_TEXT_PRODUCT; ?>&nbsp;&nbsp;<span id="specials_product_name"></span>&nbsp;&nbsp;(<span id="specials_oldPrice"></span>)</label>
    <div class="clear margin-10-auto" id="specials_edit_wrapper">
    <label><?php echo AAS_SPECIALS_DIALOG_TEXT_SPECIAL_PRICE; ?>&nbsp;&nbsp;<input type="text" name="specials-special-price-field" id="specials-special-price-field" class="lfora" placeholder="" style="display:inline-block;"></label>
    <div style="font-size:15px;">
      <?php echo AAS_SPECIALS_DIALOG_TEXT_SPECIAL_NOTES; ?>
    </div>
  </div>
</div>

<div class="dialog" id="dialog-specials-add" title="<?php echo AAS_DIALOG_TITLE_ADD_SPECIAL; ?>">
  <div class="message"></div>
  <div class="clear margin-20-auto" style="text-align:right">
    <label><?php echo AAS_SPECIALS_DIALOG_TEXT_PRODUCT; ?>&nbsp;&nbsp;<span style="display:inline-block;" id="specials_add_select_products-list"></span></label>
    <div class="clear margin-10-auto"></div>
    <label><?php echo AAS_SPECIALS_DIALOG_TEXT_SPECIAL_PRICE; ?>&nbsp;&nbsp;<input type="text" name="specials-add-special-price-field" id="specials-add-special-price-field" class="lfora" placeholder="" style="display:inline-block;"></label>
    <div class="clear margin-10-auto"></div>
    <label><?php echo AAS_SPECIALS_DIALOG_TEXT_EXPIRY_DATE; ?>&nbsp;&nbsp;<input type="text" name="specials-add-special-expiry-date-field" id="specials-add-special-expiry-date-field" class="lfora" placeholder="" style="display:inline-block;text-align:center;cursor:pointer;"></label>
  </div>
  <div class="clear margin-20-auto" style="font-size:15px;text-align:left"><?php echo AAS_SPECIALS_DIALOG_TEXT_SPECIAL_NOTES; ?></div>
</div>

<?php if(DOWNLOAD_ENABLED=='true' && ($fieldsArray['attributes']['visible']==true || $fieldsArray['products_description']['visible']==true) ){ ?>
<div class="dialog" id="dialog-downloadable-products-manager" title="<?php echo AAS_DIALOG_TITLE_DOWNLOADABLE_ATTRIBUTES_CONTENT_MANAGER; ?>">
  <div class="message"></div>
  <div class="clear margin-20-auto">
    <div class="clear">
      <input id="search_filename" placeholder="<?php echo AAS_DIALOG_TITLE_DOWNLOADABLE_ATTRIBUTES_CONTENT_MANAGER_SEARCH; ?>" type="text" class="lfor" />
      <div class="clear margin-20-auto"></div>
      <fieldset id="listFiles-fieldset"><legend><?php echo AAS_DIALOG_TITLE_DOWNLOADABLE_ATTRIBUTES_CONTENT_MANAGER_MESSAGE; ?></legend>
      <?php listFiles(realpath(DIR_FS_DOWNLOAD)); ?>
      </fieldset>
    </div>
  </div>
</div>
<?php } ?>

<div class="dialog" id="dialog-upload_module" title="<?php echo AAS_DIALOG_TITLE_UPLOAD_MODULE; ?>">
  <div class="message"></div>
  <div class="message-note"><?php echo AAS_DIALOG_UPLOAD_MODULE_NOTE;  ?></div>
  <div class="clear margin-20-auto">
    <div id="upload_module_upload_area">
      <h2><?php echo AAS_DIALOG_UPLOAD_MODULE_DRAG_N_DROP; ?></h2><br /><?php echo AAS_DIALOG_UPLOAD_MODULE_OR; ?><input id="upload_module_file_upload" type="file" name="module_file" accept="application/octet-stream,application/zip,application/x-zip,application/x-zip-compressed" />
    </div>
  </div>
  <div class="tabContainerModules">
    <ul class="tabContainerUl">
      <li><a class="active" data-rel="aas_upload_module-tab1" href="#"><?php echo AAS_DIALOG_UPLOAD_MODULE_INSTALLED_MODULES; ?></a></li>
      <li><a data-rel="aas_upload_module-tab2" id="aas_upload_module-check_for_new_modules" href="#"><?php echo AAS_DIALOG_UPLOAD_MODULE_AVAILABLE_MODULES; ?></a></li>
      <li><a data-rel="aas_upload_module-tab3" id="aas_upload_module-request_modules" href="#"><?php echo AAS_DIALOG_UPLOAD_MODULE_REQUEST_MODULES; ?></a></li>
    </ul>
    <div class="clear"></div>
    <div class="clear margin-20-auto"></div>
      <div class="tabDetails">
        <div id="aas_upload_module-tab1" class="tabContents" style="display: block;overflow:auto;max-height:350px;">
<?php if($modules_count>0){ ?>
          <table class="tablesorter tbl-general" style="width:100%">
            <thead>
              <tr>
                <th><?php echo AAS_DIALOG_UPLOAD_MODULE_DIESI; ?></th>
                <th><?php echo AAS_DIALOG_UPLOAD_MODULE_MODULE; ?></th>
                <th><?php echo AAS_DIALOG_UPLOAD_MODULE_VERSION; ?></th>
                <th><?php echo AAS_DIALOG_UPLOAD_MODULE_DEVELOPERS; ?></th>
                <?php if($_SESSION['admin']['id']=='1'){ ?><th><?php echo AAS_DIALOG_UPLOAD_MODULE_TEXT_AAC; ?></th><?php } ?>
              </tr>
            </thead>
            <tbody>
<?php $modules_counter=1; foreach($modules as $key => $module){ ?>
              <tr class="<?php echo ($modules_counter & 1) && $defaults['colorEachTableRowDifferently'] ? 'odd' : 'even'; ?>">
                <td class="centerAlign"><?php echo $modules_counter++; ?></td>
                <td class="centerAlign"><?php echo $module['title']; ?></td>
                <td class="centerAlign"><?php echo $module['version']; ?></td>
                <td class="centerAlign"><?php $modDevsArray=array();

                foreach($module['developers'] as $modDevs) $modDevsArray[]= tep_not_null($modDevs['website']) ? '<a href="'.$modDevs['website'].'">'.$modDevs['fullname'].'</a>' : $modDevs['fullname'];

                echo implode(', ',$modDevsArray);

                ?></td>
                <?php if($_SESSION['admin']['id']=='1'){ ?><td><button data-name="<?php echo $module['title']; ?>" data-code="<?php echo $module['code']; ?>" class="aas_upload_module-aac applyButton"><?php echo AAS_DIALOG_UPLOAD_MODULE_TEXT_EDIT; ?></button></td><?php } ?>

              </tr>
<?php } ?>
            </tbody>
            </table>
<?php }else echo AAS_DIALOG_UPLOAD_MODULE_NO_MODULES_INSTALLED; ?>
          </div>
          <div id="aas_upload_module-tab2" class="tabContents">
            <div class="loading_wrapper"><img src="ext/aas/images/loading.gif" alt="loading new modules list"></div>
            <table class="tablesorter tbl-general" id="aas_upload_module-tab2-new_modules_check_table">
            <thead>
              <tr>
                <th><?php echo AAS_DIALOG_UPLOAD_MODULE_DIESI; ?></th>
                <th><?php echo AAS_DIALOG_UPLOAD_MODULE_MODULE; ?></th>
                <th><?php echo AAS_DIALOG_UPLOAD_MODULE_VERSION; ?></th>
                <th><?php echo AAS_DIALOG_UPLOAD_MODULE_DEVELOPERS; ?></th>
                <th><?php echo AAS_DIALOG_UPLOAD_MODULE_INSTALLATION_STATUS; ?></th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>

        <div id="aas_upload_module-tab3" class="tabContents">
          <div class="clear margin-10-auto"></div>
          <?php echo AAS_DIALOG_UPLOAD_MODULE_TEXT_CLICK; ?> <a target="_blank" href="http://www.alternative-administration-system.com/modules/osCommerce/creation-request"><?php echo AAS_DIALOG_UPLOAD_MODULE_TEXT_HERE; ?></a> <?php echo AAS_DIALOG_UPLOAD_MODULE_TEXT_VISIT_CREATION_REQUEST_PAGE; ?>
          <br><br>
          <?php echo AAS_DIALOG_UPLOAD_MODULE_TEXT_CLICK; ?> <a target="_blank" href="http://www.alternative-administration-system.com/modules/osCommerce/modification-request"><?php echo AAS_DIALOG_UPLOAD_MODULE_TEXT_HERE; ?></a> <?php echo AAS_DIALOG_UPLOAD_MODULE_TEXT_VISIT_MODIFICATION_REQUEST_PAGE; ?>
        </div>

      </div>
  </div>
</div>

<div class="dialog" id="dialog-reload-page" title="<?php echo AAS_DIALOG_TITLE_RELOAD_PAGE; ?>">
  <div class="message"></div>
</div>

<div class="dialog" id="dialog-confirm-largeImageRemoval" title="<?php echo AAS_DIALOG_TITLE_DELETE_LARGE_IMAGE; ?>">
  <div class="message"><?php echo AAS_DIALOG_TEXT_DELETE_SELECTED_IMAGE; ?></div>
</div>

<?php if($_SESSION['admin']['id']=='1'){ //load this setting only for first admin with id = 1 ?>
<div class="dialog" id="dialog-aac" title="<?php echo AAS_DIALOG_TITLE_ADMIN_OPTIONS; ?>">
  <div class="message"><p class="admin_options_note"><?php echo AAS_DIALOG_AAC_TEXT_NOTE; ?></p></div>
    <div class="tabContainer" style="min-width:750px;">
      <ul class="tabContainerUl">
        <li><a class="active" data-rel="aas_dcd-tab1" href="#"><?php echo AAS_AAC_TAB_TITLE_DEFAULT_COLUMNS_DISPLAY; ?></a></li>
        <li><a data-rel="aas_dcd-tab2" href="#"><?php echo AAS_AAC_TAB_TITLE_DEFAULT_COLUMNS_DISPLAY_PER_ADMIN; ?></a></li>
        <li><a data-rel="aas_aac-tab3" href="#"><?php echo AAS_AAC_TAB_TITLE_FIELDS_DISABLE_ACTIONS; ?></a></li>
        <li><a data-rel="aas_aac-tab1" href="#"><?php echo AAS_AAC_TAB_TITLE_EXTRAS; ?></a></li>
        <li><a data-rel="aas_aac-tab4" href="#" id="modulesTabLink"><?php echo AAS_AAC_TAB_TITLE_MODULES; ?></a></li>
      </ul>

      <div class="tabDetails" style="max-height:400px;overflow:auto">
        <div id="aas_dcd-tab1" data-type="default-dcd" class="tabContents" style="display: block;">
          <table class="tablesorter tbl-general">
          <thead>
            <tr>
              <th><?php echo AAS_DIALOG_UPLOAD_MODULE_DIESI; ?></th>
              <th><?php echo AAS_TEXT_COLUMN; ?></th>
              <th><?php echo AAS_TEXT_VISIBLE; ?></th>
              <th><?php echo AAS_TEXT_LOCK_VISIBILITY; ?></th>
            </tr>
          </thead>
          <tbody></tbody>
          </table>
        </div>
        <div id="aas_dcd-tab2" data-type="admins_columns_display" class="tabContents"></div>
        <div id="aas_aac-tab1" data-type="default" class="tabContents">
          <table class="tablesorter tbl-general">
          <thead>
            <tr>
              <th><?php echo AAS_DIALOG_UPLOAD_MODULE_DIESI; ?></th>
              <th><?php echo AAS_TEXT_DESCRIPTION; ?></th>
              <th><?php echo AAS_TEXT_ADMINISTRATORS; ?></th>
            </tr>
          </thead>
          <tbody></tbody>
          </table>
        </div>
        <div id="aas_aac-tab3" data-type="fields_disable_action" class="tabContents">
          <p class="admin_options_note_fields_disable_action"><?php echo AAS_DIALOG_TEXT_FIELDS_DISABLE_ACTIONS_NOTE; ?></p>
          <table class="tablesorter tbl-general">
          <thead>
            <tr>
              <th><?php echo AAS_DIALOG_UPLOAD_MODULE_DIESI; ?></th>
              <th><?php echo AAS_TEXT_COLUMN; ?></th>
              <th><?php echo AAS_TEXT_ADMINISTRATORS; ?></th>
            </tr>
          </thead>
          <tbody></tbody>
          </table>
        </div>
        <div id="aas_aac-tab4" data-type="modules" class="tabContents">
          <table class="tablesorter tbl-general">
          <thead>
            <tr>
              <th><?php echo AAS_DIALOG_UPLOAD_MODULE_DIESI; ?></th>
              <th><?php echo AAS_TEXT_DESCRIPTION; ?></th>
              <th><?php echo AAS_TEXT_ADMINISTRATORS; ?></th>
            </tr>
          </thead>
          <tbody></tbody>
          </table>
        </div>
      </div>
  </div>
</div>
<?php } ?>

<div class="dialog" id="dialog-attributes-visualizer" title="<?php echo AAS_DIALOG_TITLE_ATTRIBUTES_VISUALIZER; ?>">
  <div id="attributes-chart" class="message orgChart"></div>
</div>

<div class="dialog" id="dialog-attributes-clever-copy" title="<?php echo AAS_DIALOG_TITLE_ATTRIBUTES_SMRT_COPY; ?>">
  <div class="message"></div>
</div>
