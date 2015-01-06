<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com
  
  Information: contains all AAS core translations
  
*/
  defined('AAS') or die; //DO NOT ALTER THIS

	define('AAS_TITLE','Alternative Administration System');
	define('AAS_VERSION','0.3');
	 	
  define('AAS_HEADING_TITLE_BACK_TO_PARENT', 'Go Back');
  define('AAS_HEADING_TITLE_LOGOFF', 'Logoff');
	define('AAS_HEADING_TITLE_SEARCH', 'Search');
	define('AAS_HEADING_CATEGORIES_PRODUCTS', 'Name');
	define('AAS_HEADING_ID','Id');
	define('AAS_HEADING_PRODUCTS_MODEL', 'Model');
	define('AAS_HEADING_PRODUCTS_QUANTITY', 'Quantity');
	define('AAS_HEADING_PRODUCTS_PRICE', 'Price (Net)');
	define('AAS_HEADING_PRODUCTS_PRICE_GROSS', 'Price (Gross)');
	define('AAS_HEADING_PRODUCTS_WEIGHT', 'Weight');
	define('AAS_HEADING_PRODUCTS_STATUS', 'Status');
	define('AAS_HEADING_PRODUCTS_DATE_AVAILABLE', 'Date Available');
	define('AAS_HEADING_PRODUCTS_DESCRIPTION', 'Description');
	define('AAS_HEADING_PRODUCTS_ORDERED', 'Ordered');
	define('AAS_HEADING_PRODUCTS_VIEWED','Viewed');
	define('AAS_HEADING_PRODUCTS_URL','Url');
	define('AAS_HEADING_PRODUCTS_LINKED','Linked Products');
	define('AAS_HEADING_LAST_MODIFIED','Last Modified');
	define('AAS_HEADING_DATE_ADDED','Date Added');	
	
	define('AAS_HEADING_MANUFACTURERS_NAME', 'Manufacturer');
	define('AAS_HEADING_TAX_RATE', 'Tax Rate');
	define('AAS_HEADING_PRODUCTS_IMAGE', 'Image');
	define('AAS_HEADING_TAX_CLASS_TITLE', 'Tax Class / Rate');
	define('AAS_HEADING_ATTRIBUTES', 'Attributes');
	define('AAS_HEADING_CATEGORIES_SORT_ORDER', 'Categories Sort Order');
	define('AAS_HEADING_PRODUCTS_SORT_ORDER', 'Sort Order');
	define('AAS_HEADING_SORT_ORDER', 'Sort Order');
	define('AAS_HEADING_TITLE_BACK_TO_DEFAULT_ADMIN', 'Back To Default Administration Panel :( ');
	define('AAS_HEADING_TITLE_DISPLAY_SETTINGS', 'Settings');
	define('AAS_HEADING_PRODUCTS_COUNT', ' Product/s');
	define('AAS_HEADING_SUBCATEGORIES_COUNT', ' Subcategory/s');
	define('AAS_HEADING_SPECIAL', 'Special');
	define('AAS_HEADING_PRODUCTS_ORDER_STATUS', 'Products Quantity Order Status');
	define('AAS_HEADING_OPT_DIESI','#');
	define('AAS_HEADING_OPT_NAME','Option Name');
	define('AAS_HEADING_OPT_VALUE','Option Value');
	define('AAS_HEADING_OPT_PRICE','Option Price');
	define('AAS_HEADING_OPT_PRICE_PREFIX','Option Prefix');
	define('AAS_HEADING_OPT_ACTION','Action');
	
	define('AAS_ERROR_EMPTY_VALUE', 'Error: empty value is unaccepted. Old value still in use.');
	define('AAS_ERROR_NONUMERIC_VALUE', 'Error: value must be numeric. Old value still in use.');
	define('AAS_ERROR_STATUS_VALUE_LENGTH_MUST_BE_ONE', 'Error: status value length must be 1. Old value still in use.');
	define('AAS_ERROR_STATUS_VALUE_MUST_BE_0_OR_1', 'Error: status value must be 0 or 1. Old value still in use.');
	
	define('AAS_ACTIVE_PRODUCTS', 'Active Products');
	define('AAS_ALL_PRODUCTS', 'Both');
	define('AAS_INACTIVE_PRODUCTS', 'Inactive Products');

	define('AAS_DISPLAY_CATEGORIES', 'Display Categories');
	define('AAS_CATEGORIES_VISIBLE', 'Categories Visible');
	define('AAS_HIDE_CATEGORIES', 'Hide Categories');
	define('AAS_CATEGORIES_HIDDEN', 'Categories Hidden');

	define('AAS_SHOWING_PRODUCTS', 'Showing:');
	
	define('AAS_VIEW_EDIT', 'View/edit');
	define('AAS_VIEW_EDIT_DESCRIPTION', 'View/edit description');
	define('AAS_VIEW_EDIT_ATTRIBUTES', 'View/edit attributes');
	define('AAS_NONE','--none--');
	define('AAS_VIEW_PRODUCTS_PAGE','View product\'s page');
	define('AAS_VIEW_CATEGORYS_PAGE','View category\'s page');
	
	define('AAS_COUNT_PRODUCTS', 'Count Products');
	define('AAS_COUNT_SUBCATEGORIES', 'Count Subcategories');

	define('AAS_AJAX_TAX_CLASS_CHANGE_SUCCESS','Successfully changed Tax Class!');
	define('AAS_AJAX_MANUFACTURER_CHANGE_SUCCESS','Successfully changed Manufacturer!');
	define('AAS_AJAX_DATE_AVAILABLE_CHANGE_SUCCESS','Successfully changed Date Available!');

	define('AAS_TEXT_SORT_BY_PRODUCTS_NAME','sort by Name');
	define('AAS_TEXT_SORT_BY_LAST_MODIFIED','sort by Last modified');
	define('AAS_TEXT_SORT_BY_DATE_ADDED','sort by Date added');
	define('AAS_TEXT_SORT_BY_PRODUCTS_VIEWED','sort by Views');
	define('AAS_TEXT_SORT_BY_PRODUCTS_ORDERED','sort by Orders');
	define('AAS_TEXT_SORT_BY_PRODUCTS_PRICE','sort by Price');
	define('AAS_TEXT_SORT_BY_PRODUCTS_MODEL','sort by Model');
	define('AAS_TEXT_SORT_BY_PRODUCTS_SORT_ORDER','sort by Sort Order');
	define('AAS_TEXT_ASC','Asc');
	define('AAS_TEXT_DESC','Desc');
	define('AAS_APPLY','Apply');
	define('AAS_TEXT_COLUMNS','Columns:');
	define('AAS_TEXT_ALL','All');
	define('AAS_TEXT_AGO','ago!');
	define('AAS_TEXT_A_MOMENT_AGO','A moment ago!');
	define('AAS_TEXT_SEARCH_IN_PRODUCTS_NAME','in products / categories name');
	define('AAS_TEXT_SEARCH_IN_PRODUCTS_MODEL','in products model');
	define('AAS_TITLE_TEXT_NOT_AVAILABLE_DATE','Set Available Date to Null');
	define('AAS_TEXT_NO_RECORDS_FOUND','No records found!');
	define('AAS_TEXT_SELECTED_LANGUAGE','Selected language: ');

	/*TOOLTIP*/
	define('AAS_BUTTON_TOOLTIP_SEARCH','Search');
	define('AAS_BUTTON_TOOLTIP_CHANGE_PRODUCTS_STATUS','Change Status of selected Products!');
	define('AAS_BUTTON_TOOLTIP_DELETE_PRODUCTS','Delete Products!');
	define('AAS_BUTTON_TOOLTIP_DISCOUNT','Discount - Edit Products\' prices!');
	define('AAS_BUTTON_TOOLTIP_PRINT','Print Categories - Products Entries');
	define('AAS_BUTTON_TOOLTIP_EXPORT','Export Products Data');
	define('AAS_BUTTON_TOOLTIP_IMPORT','Import Products Data');
	define('AAS_BUTTON_TOOLTIP_COLUMNS','Show/hide Columns');
	define('AAS_BUTTON_TOOLTIP_ALL_EDIT','All Edit!');
	define('AAS_BUTTON_TOOLTIP_MASS_EDIT','Mass Columns Edit!');
	define('AAS_BUTTON_TOOLTIP_ADD_SELECTED_TO_SAVED_LIST','Add selected products to Temp list');
	define('AAS_BUTTON_TOOLTIP_ONLINE_USERS','Online Users');
	define('AAS_BUTTON_TOOLTIP_ATTRIBUTES_MANAGER','Attributes Manager');
	define('AAS_BUTTON_TOOLTIP_CLOCKS','Display world Clocks');
	define('AAS_BUTTON_TOOLTIP_CALENDAR','Events Calendar');
	define('AAS_BUTTON_TOOLTIP_SPECIALS','Specials Manager');
	define('AAS_BUTTON_TOOLTIP_DONATION','Make a Donation');
	define('AAS_BUTTON_TOOLTIP_CONTACTME','Send me a message, requests e.t.c.');
	define('AAS_BUTTON_TOOLTIP_TOGGLE_TOOLBOX','Toggle Toolbox panel');
	define('AAS_BUTTON_TOOLTIP_PRODUCTS_EXPECTED','Expected Products');
	
	/*PAGINATION*/
	define('AAS_PAGINATION_MAX_PRODUCTS','Max products : ');
	define('AAS_PAGINATION_DISPLAYING',' displaying ');
	define('AAS_PAGINATION_FROM',' from ');
	define('AAS_PAGINATION_PRODUCTS',' products');	
	define('AAS_PAGINATION_FIRST','«« First');
	define('AAS_PAGINATION_LAST','Last »»');
	define('AAS_PAGINATION_NEXT','Next »');
	define('AAS_PAGINATION_PREVIOUS','« Prev');
	define('AAS_PAGINATION_JUMP','...');
	define('AAS_PAGINATION_JUMP_TO_PAGE','jump to page');
		
	define('AAS_OPT_ACTION_DELETE','Delete');
	define('AAS_OPT_ACTION_REMOVE','Remove');
	define('AAS_OPT_ACTION_SMART_COPY','sc');
	define('AAS_OPT_ACTION_EXCLUDE','Exclude');
	
	define('AAS_ACTION_STATUS_SUCCESSFULLY_CHANGED','Status successfully changed');
	define('AAS_ACTION_STATUS_NOT_CHANGED','Something went wrong. Status not changed!');

	//dialog titles
	define('AAS_DIALOG_TITLE_PRODUCT_DESCRIPTION','Editing/Viewing Product Description');
	define('AAS_DIALOG_TITLE_PRODUCT_PREVIEW','Product Preview');
	define('AAS_DIALOG_TITLE_PRODUCTS','Products');
	define('AAS_DIALOG_TITLE_WARNING_INFORMATION','Warning/Information');
	define('AAS_DIALOG_TITLE_ERROR','Error');
	define('AAS_DIALOG_TITLE_SUCCESS','Success');
	define('AAS_DIALOG_TITLE_PRODUCT_ATTRIBUTES','Product Attributes');
	define('AAS_DIALOG_TITLE_CONFIRM_ACTION','Confirm Action');

	define('AAS_DIALOG_TITLE_DELETE_SELECTED_PRODUCTS','Delete selected Products?');

	define('AAS_DIALOG_TITLE_SESSION_TIMEOUT','Warning!');
	define('AAS_DIALOG_TITLE_SETTINGS','Settings');
	define('AAS_DIALOG_TITLE_EXPORT','Export Products');
	define('AAS_DIALOG_TITLE_EDIT_OPTION_NAME','Edit products options name');
	define('AAS_DIALOG_TITLE_INSERT_OPTION_NAME','Insert products options name');
	define('AAS_DIALOG_TITLE_DELETE_OPTION_NAME','Delete products options name');
	define('AAS_DIALOG_TITLE_EDIT_OPTION_VALUE','Edit products option values');
	define('AAS_DIALOG_TITLE_INSERT_OPTION_VALUE','Insert products options value');
	define('AAS_DIALOG_TITLE_DELETE_OPTION_VALUE','Delete products options value name?');
	define('AAS_DIALOG_TITLE_INFORMATION','Information');
	define('AAS_DIALOG_TITLE_WARNING','Warning');
	define('AAS_DIALOG_TITLE_ALL_EDIT','All Edit:');
	define('AAS_DIALOG_TITLE_PROCESSING','Processing');
	define('AAS_DIALOG_TITLE_GENERAL','General');
	define('AAS_DIALOG_TITLE_CHANGE_IMAGE','Change Image');
	define('AAS_DIALOG_TITLE_UPLOAD_MODULE','Upload Module');
	define('AAS_DIALOG_TITLE_RELOAD_PAGE','Reload Page?');

	define('AAS_DIALOG_TITLE_DELETE_LARGE_IMAGE','Delete Large image');
	define('AAS_DIALOG_TITLE_ADMIN_OPTIONS','Admin Options');
	define('AAS_DIALOG_TITLE_MASS_COLUMNS_EDIT','Mass Columns Edit');
	define('AAS_DIALOG_TITLE_ATTRIBUTES_VISUALIZER','Products Attributes Visualizer');
	define('AAS_DIALOG_TITLE_ATTRIBUTES_SMRT_COPY','Products Attributes Smart Copy');
	define('AAS_DIALOG_TITLE_ATTRIBUTES_MANAGER','Attributes Manager');

	/*DIALOG BUTTONS*/
	define('AAS_DIALOG_BUTTON_PREVIEW_CHANGES','Preview Changes');
	define('AAS_DIALOG_BUTTON_SUBMIT_CHANGES','Submit Changes');
	define('AAS_DIALOG_BUTTON_SUBMIT','Submit');
	define('AAS_DIALOG_BUTTON_PREVIEW','Preview');
	define('AAS_DIALOG_BUTTON_CLOSE','Close');
	define('AAS_DIALOG_BUTTON_RELOAD','Reload Preview');
	define('AAS_DIALOG_BUTTON_ADD_NEW_ATTRIBUTE','Add new Attribute');
	define('AAS_DIALOG_BUTTON_ADD','Add');
	define('AAS_DIALOG_BUTTON_YES','Yes');
	define('AAS_DIALOG_BUTTON_NO','No');
	define('AAS_DIALOG_BUTTON_APPLY','Apply');
	define('AAS_DIALOG_BUTTON_EXPORT','Export');
	define('AAS_DIALOG_BUTTON_SAVE','Save');
	define('AAS_DIALOG_BUTTON_CANCEL','Cancel');
	define('AAS_DIALOG_BUTTON_LOGIN','Login');
	define('AAS_DIALOG_BUTTON_INSERT','Insert');
	define('AAS_DIALOG_BUTTON_YES_DELETE_IT','Yes delete it');
	define('AAS_DIALOG_BUTTON_DELETE','Delete');
	define('AAS_DIALOG_BUTTON_ENABLE','Enable');
	define('AAS_DIALOG_BUTTON_SELECT','Select');
	define('AAS_DIALOG_BUTTON_UPDATE','Update');
	define('AAS_DIALOG_BUTTON_PRINT','Print');
	define('AAS_DIALOG_BUTTON_ADD_AVAILABLE_ATTRIBUTES','Add available Attributes');
			
	//Export Dialog
	define('AAS_DIALOG_EXPORT_MESSAGE','Please select type and delimeter to export:');
	define('AAS_DIALOG_EXPORT_MESSAGE_JSON_NOTE','Delimeter does not used when type is Json');
	define('AAS_DIALOG_EXPORT_TYPE_CSV','CSV');
	define('AAS_DIALOG_EXPORT_TYPE_JSON','JSON');
	define('AAS_DIALOG_EXPORT_TYPE_TEXT','TEXT');
	define('AAS_DIALOG_EXPORT_TYPE_EXCEL','EXCEL');
	define('AAS_DIALOG_EXPORT_DEL_COMMA',', Comma');
	define('AAS_DIALOG_EXPORT_DEL_SEMI_COLON','; Semi-colon');
	define('AAS_DIALOG_EXPORT_DEL_TILDE','~ Tilde');
	define('AAS_DIALOG_EXPORT_DEL_SPLAT','* Splat');
	define('AAS_DIALOG_EXPORT_DEL_TAB',' \t Tab');

	define('AAS_DIALOG_SETTINGS_SOMETHING_WENT_WRONG','Something went wrong!<br />Please contact webmaster in order to solve this.');
	define('AAS_DIALOG_SETTINGS_YOU_MUST_RELOAD_THE_WEB_PAGE','In order for changes to apply you need to reload the web page');
	define('AAS_DIALOG_SETTINGS_RELOAD_NOW','Reload Now');
	define('AAS_DIALOG_SETTINGS_AO','Admin options: ');
	define('AAS_DIALOG_SETTINGS_EDIT','edit');
	define('AAS_DIALOG_ATTRIBUTES_SUCCESSFULLY_SUBMITED_ATTRIBUTES_CHANGES','Successfully submitted attributes changes');
	define('AAS_DIALOG_ATTRIBUTES_THERE_WAS_AN_ERROR','There was an error during submiting attributes changes');

	define('AAS_DIALOG_DELETE_PRODUCTS_DELETING','Deleting, please wait!');
	define('AAS_DIALOG_DELETE_PRODUCTS_SUCCESSFULLY_DELETED','Selected Products Deleted successfully');
	define('AAS_DIALOG_DELETE_PRODUCTS_SOMETHING_WENT_WRONG','Something went wrong. Please try again!');
	define('AAS_DIALOG_ATTRIBUTES_SUCCESSFULLY_DELETED_ATTRIBUTE','Successfully deleted selected attribute/s');
	define('AAS_DIALOG_ATTRIBUTES_SOMETHING_WENT_WRONG','Something went wrong. Please try again!');
	define('AAS_DIALOG_SESSION_TIMEOUT_IN_ORDER', 'In order for changes to apply you need to reload the web page');
	define('AAS_DIALOG_SESSION_TIMEOUT_SOMETHING_WENT_WRONG_CONTACT', 'Something went wrong!<br />Please contact webmaster in order to solve this.');
	define('AAS_DIALOG_TEXT_SOMETHING_WENT_WRONG_TRY_AGAIN','Something went wrong. Please try again!');
	define('AAS_DIALOG_TEXT_WRONG_AMOUNT','Wrong amount input');
	define('AAS_DIALOG_TEXT_APPLY_TO_SELECTION','Wrong apply to selection');
	define('AAS_DIALOG_TEXT_SUCCESSFULLY_ALTERED_OPTION_PRICES','Successfully altered option prices');
	define('AAS_DIALOG_TEXT_COULD_NOT_ALTER_OPTION_PRICES','Could not alter option prices');
	define('AAS_DIALOG_TEXT_OPTION_NAME_DONT_HAVE_OPTION_VALUES_ASSIGNED','Selected Option Name does not have any Option Values assigned to it!');

	define('AAS_DIALOGS_PHP_PRODUCT', 'Product: ');
	define('AAS_DIALOGS_PHP_EDITING_PRODUCT_IN', 'Editing product in ');
	define('AAS_DIALOGS_PHP_LANGUAGE', ' language');
	define('AAS_DIALOGS_PHP_PRODUCT_DESCRIPTION_UNIQUE_ID_WRAPPER', 'Product Description Unique Id Wrapper:&nbsp;');
	define('AAS_DIALOGS_PHP_PRODUCT_DESCRIPTION_UNIQUE_ID_WRAPPER_USE_THIS_ONLY', 'Use this only if you want to have the abillity to preview product description before applying any changes');
	define('AAS_DIALOGS_PHP_PRODUCT_DESCRIPTION_UNIQUE_ID_WRAPPER_IN_ORDER', 'In order to be able to preview the product description before applying changes,<br>you need to set up a unique id that is wrapped on the product description code<br>which can be found in the catalog\'s product_info.php page.');
	define('AAS_DIALOGS_PHP_PRODUCT_DESCRIPTION_UNIQUE_ID_WRAPPER_INSTRUCTIONS', 'Instructions');
	define('AAS_DIALOGS_PHP_PRODUCT_DESCRIPTION_UNIQUE_ID_WRAPPER_OPEN_EDIT', 'Open - edit product_info.php and change:');
	define('AAS_DIALOGS_PHP_PRODUCT_DESCRIPTION_UNIQUE_ID_WRAPPER_TO', 'to');
	define('AAS_DIALOGS_PHP_PRODUCT_DESCRIPTION_UNIQUE_ID_WRAPPER_OR', 'or to');	
	define('AAS_DIALOGS_PHP_PRODUCT_DESCRIPTION_UNIQUE_ID_WRAPPER_WHERE', 'where');
	define('AAS_DIALOGS_PHP_PRODUCT_DESCRIPTION_UNIQUE_ID_WRAPPER_WHERE_IS', ' is a unique id, feel free to change.');
	define('AAS_DIALOGS_PHP_PRODUCT_DESCRIPTION_UNIQUE_ID_WRAPPER_SAVE', 'Save product_info.php');
	define('AAS_DIALOGS_PHP_PRODUCT_DESCRIPTION_UNIQUE_ID_WRAPPER_NOW', 'Now every product_info.php page has a unique span id.');
	define('AAS_DIALOGS_PHP_PRODUCT_DESCRIPTION_UNIQUE_ID_WRAPPER_SPAN', 'SPAN tag does not apply any styling, so no changes will be done to your theme.');
	define('AAS_DIALOGS_PHP_ERROR_UNIQUE_ID_WRAPPER_I_COULD_NOT','I could not find a unique id with the name:');
	define('AAS_DIALOGS_PHP_ERROR_UNIQUE_ID_WRAPPER_CLICK_HERE_TO_FIND_MORE','Click Here to find out about this awesome feature!');
	define('AAS_DIALOGS_PHP_SUCCESS_SUBMITED_SUCCESSFULLY','Changes Submitted Successfully');
	define('AAS_DIALOGS_PHP_MASS_DELETE_ARE_YOU_SURE','Are you sure you want to delete selected products?');
	define('AAS_DIALOGS_PHP_MASS_DELETE_WARNING','Warning: after deletion product/s cannot be recovered!');
	define('AAS_DIALOGS_PHP_SESSION_TIMEOUT','Session Timeout!<br>Please login again!');
	define('AAS_DIALOGS_TEXT_SUCCESSFULLY_SET_PRODUCTS_AVAILABLE_DATE_TO_NULL','Successfully set products available date to null!');
	define('AAS_DIALOGS_TEXT_COULD_NOT_SET_PRODUCTS_AVAILBALE_TO_NULL','Could not set products available date to null!');
	
	define('AAS_DIALOG_TITLE_AJAX_FAILED','Ajax failed!');	
	define('AAS_DIALOG_TEXT_AJAX_FAILED','Sorry but ajax failed!');
	define('AAS_DIALOG_TITLE_NOT_WRITABLE_IMAGE_FOLDERS_WARNING','and its subfolders are not writable.<br>Make it writable or any of its subfolders and then refresh this page.');
	define('AAS_DIALOG_TEXT_NO_WRITABLE','no writable!');
	define('AAS_DIALOG_TEXT_ABOUT_TO_CHANGE_THE_IMAGE_OF','You are about to change the image of: ');
	define('AAS_DIALOG_TEXT_DRAG_N_DROP_NEW_IMAGE','Drag &amp; Drop new image here!');
	define('AAS_DIALOG_TEXT_IMAGES_DIR','Images dir: ');
	define('AAS_DIALOG_ALL_EDIT_MESSAGE','Press the "Enable" button to make all cells editable!<br />Edit values as you wish and press the "Submit Changes" button.');
	define('AAS_DIALOG_ALL_EDIT_MESSAGE_NOTE_1','Gross price editing is disabled.');
	define('AAS_DIALOG_ALL_EDIT_MESSAGE_NOTE_2','Closing this dialog causes no data change.');
	
	define('AAS_DIALOG_TEXT_SELECT_FOLDER_TO_SAVE_NEW_IMAGE','Select a folder to save new image:');
	define('AAS_DIALOG_TEXT_OR','or');
	
	define('AAS_DIALOG_TITLE_REMOVE_LINKED_PRODUCT','Unlink selected Product?');
	define('AAS_DIALOG_TEXT_REMOVE_LINKED_PRODUCT_TEXT','Are you sure you want to unlink the selected product from this category?');
	define('AAS_DIALOG_TEXT_REMOVE_LINKED_PRODUCT_TEXT_NOTE','Note: this action will not delete the product but it will just remove it from this category.');
	define('AAS_DIALOG_TEXT_LINKED_PRODUCT_SUCCESSFULLY_REMOVED','Successfully unlinked product from selected category.');
	define('AAS_DIALOG_TEXT_LINKED_PRODUCT_NOT_REMOVED','Cannot unlink product from the selected category. Please try again!');

	define('AAS_DIALOG_TITLE_REMOVE_LINKED_PRODUCT_FROM_PARENT','Unlink this Product from selected category?');
	define('AAS_DIALOG_TEXT_REMOVE_LINKED_PRODUCT_FROM_PARENT_TEXT','Are you sure you want to unlink the selected product from selected category?');
	define('AAS_DIALOG_TEXT_REMOVE_LINKED_PRODUCT_FROM_PARENT_TEXT_NOTE','Note: this action will not delete the product but it will just remove it from selected category.');

	define('AAS_DIALOG_TITLE_DOWNLOADABLE_ATTRIBUTES_CONTENT_MANAGER','Downloadable Attributes Content Manager');
	define('AAS_DIALOG_TITLE_DOWNLOADABLE_ATTRIBUTES_CONTENT_MANAGER_MESSAGE','Select a file and press the Select button:');
	define('AAS_DIALOG_TITLE_DOWNLOADABLE_ATTRIBUTES_CONTENT_MANAGER_SEARCH','Start typing...');
	define('AAS_DIALOG_TITLE_DOWNLOADABLE_ATTRIBUTES_CONTENT_MANAGER_YOU_DID_NOT_SELECT_A_FILE','You did not select a file');
	
	define('AAS_DIALOG_ATTRIBUTES_DOWNLOADABLE_PRODUCTS','Downloadable products:');
	define('AAS_DIALOG_ATTRIBUTES_DOWNLOADABLE_PRODUCTS_SUBHEADERS','Filename | Expiry days | Maximum download count');
	define('AAS_DIALOG_ATTRIBUTES_ORDERED_BY_OPTION_PRICE','Ordered by Option Price');
	define('AAS_DIALOG_ATTRIBUTES_ORDERED_BY_OPTION_NAME','Ordered by Option Name');
	define('AAS_DIALOG_ATTRIBUTES_ORDERED_BY_OPTION_ID_PRICE','Ordered by Option Id, Price');
	
	define('AAS_DIALOG_ATTRIBUTES_OPTION_PREFIX_PLUS','+');
	define('AAS_DIALOG_ATTRIBUTES_OPTION_PREFIX_MINUS','-');
	define('AAS_DIALOG_ATTRIBUTES_OPTION_PREFIX_BLANK','None');
	
	define('AAS_DIALOG_ATTRIBUTES_DELETE_SELECTED','Delete selected');
	define('AAS_DIALOG_ATTRIBUTES_NO_SELECTED_ATTRIBUTES_FOUND','No selected attributes found!');
	
	define('AAS_DIALOG_ATTRIBUTES_ICON_TITLE_RELOAD_ATTRIBUTES_LIST','Reload Attributes List');
	define('AAS_DIALOG_ATTRIBUTES_ICON_TITLE_ATTRIBUTES_MANAGER','Attributes Manager');
	define('AAS_DIALOG_ATTRIBUTES_ICON_TITLE_PRODUCTS_ATTRIBUTES_VISUALIZER','Products Attributes Visualizer');
	
	//ext/aas/ja/dialogs.js
	
	define('AAS_DIALOG_ATTRIBUTES_NO_ATTRIBUTES_FOUND','No attributes found. Please add attributes by clicking on the [ Add new Attribute ] button.');
	define('AAS_DIALOG_ATTRIBUTES_FOUND_OPTION_NAME_WITHOUT_OPTION_VALUES_ASSIGNED_TO_IT','Found Option Name that has not Option Values assigned to it.<br>Please use the AAS attributes manager to assign or select another Option Name that already has Option Values.');
	
  define('AAS_DIALOG_ATTRIBUTES_FOUND_DUPLICATE_ATTRIBUTES_ON_EXISTING_ENTRIES','Found duplicate attributes on existing entries.<br>Are you sure you want to proceed Submitting Changes?');
  define('AAS_DIALOG_ATTRIBUTES_FOUND_DUPLICATE_ATTRIBUTES_ON_ABOUT_TO_ADD_ENTRIES','Found duplicate attributes on entries you are about to add.<br>Are you sure you want to proceed adding the new attributes?');
  define('AAS_DIALOG_ATTRIBUTES_FOUND_DUPLICATE_ATTRIBUTES_BETWEEN_EXISTING_AND_ABOUT_TO_ADD_ENTRIES','Found duplicate attributes between existing entries and those about to add.<br>Are you sure you want to proceed?');	
	
	define('AAS_DIALOG_ATTRIBUTES_NO_ATTRIBUTES_FOUND_FOR_SMART_COPY','No attributes found available to add.');

	define('AAS_DIALOG_ATTRIBUTES_COULD_NOT_UPDATE_OPTION_NAMES','Something went wrong. Could not update option names!');
	define('AAS_DIALOG_ATTRIBUTES_COULD_NOT_INSERT_OPTION_NAMES','Something went wrong. Could not insert option names!');
	define('AAS_DIALOG_ATTRIBUTES_SUCCESSFULLY_INSERTED_OPTION_NAMES','Successfully inserted option names.');
	define('AAS_DIALOG_ATTRIBUTES_SUCCESSFULLY_CHANGED_OPTION_NAMES','Successfully changed option names.');
  define('AAS_DIALOG_ATTRIBUTES_SUCCESSFULLY_DELETED_OPTION_NAME','Successfully deleted option name.');
  define('AAS_DIALOG_ATTRIBUTES_COULD_NOT_DELETE_OPTION_NAME','Something went wrong. Could not delete option name');

	define('AAS_DIALOG_ATTRIBUTES_COULD_NOT_DELETE_OPTION_VALUE_NAME','Something went wrong. Could not delete option value!');
	define('AAS_DIALOG_ATTRIBUTES_COULD_NOT_UPDATE_OPTION_VALUES_NAMES','Something went wrong. Could not update option values names!');
	define('AAS_DIALOG_ATTRIBUTES_COULD_NOT_INSERT_OPTION_VALUES_NAMES','Something went wrong. Could not insert option values names!');
	define('AAS_DIALOG_ATTRIBUTES_SUCCESSFULLY_INSERTED_OPTION_VALUES','Successfully inserted option values.');
	define('AAS_DIALOG_ATTRIBUTES_SUCCESSFULLY_CHANGED_OPTION_VALUES_NAMES','Successfully changed option values names.');
	define('AAS_DIALOG_ATTRIBUTES_SUCCESSFULLY_DELETED_OPTION_VALUE_NAME','Successfully deleted option value name.');
	
	
	define('AAS_DIALOG_TEXT_COLUMNS_PANEL_INFORMATION','Check the column fields you need to display and press the Apply button.<br />You can also reorder the column fields via drag n drop.');

	define('AAS_DIALOG_TEXT_DELETE_SELECTED_IMAGE','Are you sure you want to delete selected Large Image?');
	
	define('AAS_DIALOG_TEXT_MASS_EDIT','Mass edit');
	define('AAS_DIALOG_TEXT_MASS_EDIT_ALL_STATUS','all status');
	define('AAS_DIALOG_TEXT_MASS_EDIT_ACTIVE_STATUS','active status');
	define('AAS_DIALOG_TEXT_MASS_EDIT_INACTIVE_STATUS','inactive status');
	
	define('AAS_DIALOG_TEXT_MASS_EDIT_SELECTED_PRODUCTS','selected products');
	define('AAS_DIALOG_TEXT_MASS_EDIT_SELECTED_PRODUCTS_FROM_TEMP_LIST','selected products from temp list');
	define('AAS_DIALOG_TEXT_MASS_EDIT_PRODUCTS_FROM','products from');
  define('AAS_DIALOG_TEXT_MASS_EDIT_RECURSIVELY_OPTION_EXPLAIN','Recursively option will apply changes to all the products found within a category and its subcategories.');
	
	define('AAS_DIALOG_TEXT_APPLIES_TO_SELECTED_PRODUCTS_ONLY','Applies at Selected Products Only.');
  define('AAS_DIALOG_TEXT_HEADING_COLUMN','Column');
  define('AAS_DIALOG_TEXT_HEADING_VALUE','Value');
  define('AAS_DIALOG_TEXT_HEADING_OPTION','Option');
  define('AAS_DIALOG_TEXT_HEADING_ACTION','Action');
  define('AAS_DIALOG_TEXT_BUTTON_APPLY','Apply');

  define('AAS_DIALOG_TITLE_ADD_SPECIAL','Add Special');
  define('AAS_DIALOG_TITLE_CALENDAR','Calendar:');
  define('AAS_DIALOG_TITLE_SPECIALS','Specials:');
  
  define('AAS_DIALOG_TEXT_ACTIVE','Active');
  define('AAS_DIALOG_TEXT_INACTIVE','InActive');
  define('AAS_DIALOG_TEXT_SELECT_MANUFACTURER','Select Manufacturer');
  
  define('AAS_DIALOG_TEXT_FIELDS_DISABLE_ACTIONS_NOTE','In order to disable a column action for a certain admin just click on the apropriate checkbox and save.');
  
  //define('AAS_DIALOG_TEXT_SELECTED_OPTION_NAME_NO_OPTION_VALUES_ASSIGNED','Selected Option Name has not Option Values assigned to it!');
	
	define('AAS_DIALOG_ATTRIBUTES_SMART_COPY_CHOOSEN_ATTRIBUTE','Choosen attribute to Smart Copy from:');
	define('AAS_DIALOG_ATTRIBUTES_SMART_COPY_ALREADY_EXIST','List of Attributes that already exist minus the choosen one');
	define('AAS_DIALOG_ATTRIBUTES_SMART_COPY_AVAILABLE_FOR_ADDING','List of Attributes available for Smart Coping:');
	define('AAS_DIALOG_ATTRIBUTES_SMART_COPY_SUCCESS_ADD','Successfully added attributes via Smart Copy method.');	
	
	//Setings Dialog

	define('AAS_SETTINGS_DIALOG_TITLE','Settings');
	define('AAS_SETTINGS_TRUE','True');
	define('AAS_SETTINGS_FALSE','False');
	define('AAS_SETTINGS_DISPLAY_SUCCESS_MESSAGES','Display success messages:');
	define('AAS_SETTINGS_DISPLAY_ERROR_MESSAGES','Display error messages:');	
	define('AAS_SETTINGS_DISPLAY_ALERTS_MESSAGE','Display success &amp; error messages:&nbsp;');
	define('AAS_SETTINGS_ENABLE_COLUMN_SORTING','Enable column sorting:');
	define('AAS_SETTINGS_RESET_TOOLBOX_HEIGHT','Reset toolbox height:');
	define('AAS_SETTINGS_RESET_BUTTON','reset');
	define('AAS_SETTINGS_RESET_COLUMNS_ORDER','Reset columns order:');
	define('AAS_SETTINGS_RESET_COLUMNS_ORDER_SUCCESS','Successfully reset the columns order.<br>Changes will be visible after you reload the page.');

  /*ext/aas/dialogs.php*/

	define('AAS_STATUS_ICON_SET_CATEGORY_HIDDEN', 'Make Category and its subcategories hidden');
	define('AAS_STATUS_ICON_SET_CATEGORY_VISIBLE', 'Make Category and its subcategories visible');
	define('AAS_STATUS_ICON_SET_IN_STOCK', 'Set Product In Stock');
	define('AAS_STATUS_ICON_SET_OUT_OF_STOCK', 'Set Product Out Of Stock');
	define('AAS_STATUS_ICON_IN_STOCK', 'In Stock');
	define('AAS_STATUS_ICON_OUT_OF_STOCK', 'Out Of Stock');

	define('AAS_TEXT_EDITING','Editing');
	define('AAS_TEXT_PRODUCTS_DESCRIPTION','product\'s description');
	define('AAS_TEXT_VIEW_PRODUCTS_PAGE','View product\'s page');
	define('AAS_TEXT_CURRENT_LANGUAGE','Current Language: ');
	define('AAS_TEXT_LANGUAGE','Language: ');
	define('AAS_TEXT_TOGGLE_EDITOR','Toggle Editor');
	define('AAS_TEXT_ALSO_EDIT',', also edit in: ');
	define('AAS_TEXT_CANNOT_FETCH_PRODUCTS_DESCRIPTION','Cannot fetch product\'s description for selected language');
	define('AAS_TEXT_NO_PRODUCT_ID_FOUND','No product Id found');
	define('AAS_TEXT_NO_CATEGORY_ID_FOUND','No category Id found');
	
	define('AAS_BUTTON_TOOLTIP_EDIT_PREVIOUS_PRODUCT_DESCRIPTION','Edit previous product\'s description');
	define('AAS_BUTTON_TEXT_PREVIOUS','&laquo; Previous');
	define('AAS_BUTTON_TOOLTIP_SAVE_ALL_DESC_CHANGES','Save all description changes');
	define('AAS_BUTTON_TEXT_SUBMIT_CHANGES','Submit Changes');
	define('AAS_BUTTON_TOOLTIP_PREVIEW_DESC_CHANGES','Preview changes before submit');
	define('AAS_BUTTON_TEXT_PREVIEW_CHANGES','Preview Changes');
	define('AAS_BUTTON_TOOLTIP_RELOAD_ALL_PREVIEWS','Reloads all visible preview screens');
	define('AAS_BUTTON_TEXT_RELOAD_PREVIEW','Reload Preview');
	define('AAS_BUTTON_TOOLTIP_CLOSE_DESC_EDITING','Any changes not applied will be lost!');
	define('AAS_BUTTON_TEXT_CLOSE','Close');
	define('AAS_BUTTON_TOOLTIP_EDIT_NEXT_PORDUCTS_DESC','Edit next product\'s description');
	define('AAS_BUTTON_TEXT_NEXT','Next &raquo;');
	
	define('AAS_BUTTON_TEXT_TOGGLE_EDITORS','Toggle Editors');
	define('AAS_BUTTON_TOOLTIP_TOGGLE_EDITORS','Toggle all editors');
	
	//TOOLBOX
	
	define('AAS_TEXT_MOVE_SELECTED_PRODUCTS_IN','Move selected products into');
	define('AAS_TEXT_COPY_SELECTED_PRODUCTS_IN','Copy selected products into');
	define('AAS_TEXT_LINK_SELECTED_PRODUCTS_IN','Link selected products with');
	define('AAS_TEXT_AND_STAY_HERE','and stay here');
	define('AAS_TEXT_AND_RELOAD_THIS_PAGE','and reload this page');
	define('AAS_TEXT_AND_GO_TO_SELECTED_CATEGORY','and go to selected category page');	
	define('AAS_TEXT_GO','Go');
	define('AAS_TEXT_COPY_ATTRIBUTES_FROM','Copy attributes from');
	define('AAS_TEXT_DELETE_ATTRIBUTES','Delete attributes');
	define('AAS_TEXT_TO_SELECTED_PRODUCTS','into selected products');
	define('AAS_TEXT_TO_SELECTED_PRODUCTS_FROM_TEMP_LIST','into selected products from temp list');
	define('AAS_TEXT_TO_ALL_PRODUCTS_IN','into all products in');
	define('AAS_TEXT_FROM_SELECTED_PRODUCTS','from selected products');
	define('AAS_TEXT_FROM_SELECTED_PRODUCTS_FROM_TEMP_LIST','from selected products from temp list');
	define('AAS_TEXT_FROM_ALL_PRODUCTS_IN','from all products in');
	define('AAS_TEXT_RECURSIVELY','recursively');
	define('AAS_TEXT_NON_RECURSIVELY','non recursively');	
	define('AAS_TEXT_DELETE_EXISTING_ATTRIBUTES',', delete existing attributes');
	define('AAS_TEXT_ALLOW_DUPLICATE_ATTRIBUTES',', allow duplicate attributes');
	define('AAS_TEXT_DISALLOW_DUPLICATE_ATTRIBUTES',', disallow duplicate attributes');
	define('AAS_TEXT_MASS_PRODUCTS_ACTIONS_HELP','<div style=font-size:14px;color:#800>on Copy, all product\'s attributes are copied as well.<br>on Move, a duplicate check is performed because you may want <br>to move a product from the temp list to a category that already exists in.</div>');
	define('AAS_TEXT_ATTRIBUTES_ACTIONS_HELP','<div style=font-size:14px;color:#800>Recursively means copy or delete (depending on action) all products that are in the subcategories.<br>Non Recursively copies or deletes attributes from selected category only.<br><br>Delete existing attributes means that AAS will delete found attributes and then apply the new ones from the selected product.</div>');
	define('AAS_TEXT_APPLY_TO_ALL_OPTION_PRICES','Apply to all option prices');
	define('AAS_TEXT_APPLY_TO_SELECTED_ATTRIBUTES_OPTION_PRICES','Apply to selected option prices');
	define('AAS_TEXT_DISCOUNT_EDIT_OPTION_PRICES','Discount - Edit Option Prices');
	
	//SPECIALS MANAGER
	
	define('AAS_SPECIALS_TITLE','Specials Manager');
	define('AAS_SPECIALS_ADD_PRODUCT','Add Product');
	define('AAS_SPECIALS_RELOAD','Reload');
	define('AAS_SPECIALS_CLOSE','Close');
	define('AAS_SPECIALS_HEADING_PRODUCTS','Products');
	define('AAS_SPECIALS_HEADING_PRODUCTS_PRICE','Product\'s Price');
	define('AAS_SPECIALS_HEADING_PERCENTAGE','Percentage');
	define('AAS_SPECIALS_HEADING_STATUS','Status');
	define('AAS_SPECIALS_HEADING_DATE_ADDED','Date Added:');
	define('AAS_SPECIALS_HEADING_LAST_MODIFIED','Last Modified:');
	define('AAS_SPECIALS_HEADING_EXPIRES_AT','Expires at:');
	define('AAS_SPECIALS_HEADING_ACTIONS','Actions:');
	define('AAS_SPECIALS_TEXT_NEVER_EXPIRE','Never Expire!');
	define('AAS_SPECIALS_TEXT_EDIT','Edit');
	define('AAS_SPECIALS_TEXT_DELETE','Delete');
	define('AAS_SPECIALS_TEXT_VIEW_PRODUCTS_PAGE','View Product\'s page!');
	define('AAS_SPECIALS_DIALOG_TITLE_CONFIRM_SPECIAL_DELETION','Confirm Special deletion?');
	define('AAS_SPECIALS_DIALOG_TEXT_STATUS_SUCCESSFULLY_CHANGED','Status successfully changed');
	define('AAS_SPECIALS_DIALOG_TEXT_SUCCESSFULLY_SET_EXPIRE_DATE','Successfully set expire date');
	define('AAS_SPECIALS_DIALOG_TEXT_DELETE_SPECIAL','Are you sure you want to delete Special?');
	define('AAS_SPECIALS_DIALOG_TEXT_CANNOT_UPDATE_STATUS','Something went wrong, cannot update special status');
	define('AAS_SPECIALS_DIALOG_TEXT_COULD_NOT_SET_AVAILABLE_DATE_TO_NULL','Could not set products available date to null!');
	define('AAS_SPECIALS_DIALOG_TEXT_CANNOT_UPDATE_SPECIALS_PRICE','Something went wrong, cannot update specials price!');
	define('AAS_SPECIALS_DIALOG_TEXT_SUCCESSFULLY_UPDATED_VALUES','Successfully updated special values!');
	define('AAS_SPECIALS_DIALOG_TEXT_SUCCESSFULLY_DELETED','Special successfully deleted!');
	define('AAS_SPECIALS_DIALOG_TEXT_CANNOT_DELETE_SPECIAL','Something went wrong, cannot delete special!');
	define('AAS_SPECIALS_DIALOG_TEXT_EMPTY_SPECIAL_PRICE_FOUND','Empty special price found!');
	define('AAS_SPECIALS_DIALOG_TEXT_ADDED_NEW_SPECIAL_LOADING_SPECIALS_TABLE','Successfully added new Special.<br>Please wait while loading Specials table');
	define('AAS_SPECIALS_DIALOG_TEXT_ERROR_FETCHING_CELL_DATA','Error fetching cell Data. please reload page!');
	define('AAS_SPECIALS_DIALOG_TEXT_CANNOT_ADD_SPECIAL_TRY_AGAIN','Cannot add special. Please try again!');
	define('AAS_SPECIALS_DIALOG_TEXT_CANNOT_ADD_SPECIAL_NO_VALID_PRODUCT_SELECTION','Cannot add special. No valid product selection!');
	define('AAS_SPECIALS_DIALOG_TEXT_SOMETHING_WENT_WRONG_TRY_AGAIN','Something went wrong. Please try again!');
	define('AAS_SPECIALS_TEXT_EDIT_SPECIAL','Edit Special');
	define('AAS_SPECIALS_TEXT_STATUS','Status: ');
	define('AAS_SPECIALS_TEXT_EXPIRES_AT','expires at: ');
	define('AAS_SPECIALS_TEXT_NOT_A_SPECIAL_YET','Not a special Yet!');
	define('AAS_SPECIALS_TEXT_ADD','Add');
	define('AAS_SPECIALS_DIALOG_TEXT_SPECIAL_NOTES','<strong>Specials Notes:</strong><ul><li>You can enter a percentage to deduct in the Specials Price field, for example: <strong>20%</strong></li><li>If you enter a new price, the decimal separator must be a '.' (decimal-point), example: <strong>49.99</strong></li><li>Leave the expiry date empty for no expiration</li></ul>');
	define('AAS_SPECIALS_DIALOG_TEXT_PRODUCT','Product:');
	define('AAS_SPECIALS_DIALOG_TEXT_SPECIAL_PRICE','Special Price:');
	define('AAS_SPECIALS_DIALOG_TEXT_EXPIRY_DATE','Expiry Date:');
	
	//SPECIALS MANAGER

	//PRODUCTS EXPECTED PLUGIN
	define('AAS_PRODUCTS_EXPECTED_DIALOG_TEXT_SUCCESSFULLY_ALTERED_EXPECTED_DATE','Successfully altered expected date!');
	define('AAS_PRODUCTS_EXPECTED_DIALOG_TEXT_COULD_NOT_ALTER_AVAILABLE_DATE','Could not alter expected date!');
	define('AAS_PRODUCTS_EXPECTED_TEXT_NOT_EXPECTED','Make product not expected!');
	define('AAS_PRODUCTS_EXPECTED_CLOSE','Close');
	
	//TEMP LIST
	define('AAS_TEXT_TEMP_TITLE','Temp Products list');
	define('AAS_TEXT_TEMP_PRODUCTS','product/s');
	define('AAS_TEXT_TEMP_SELECT_ACTION','Select action:');
	define('AAS_TEXT_TEMP_SELECT_ALL','Select all');
	define('AAS_TEXT_TEMP_UNSELECT_ALL','Unselect all');
	define('AAS_TEXT_TEMP_REMOVE_SELECT','Remove selected');
	define('AAS_TEXT_TEMP_EXPORT_LIST','Export list');
	define('AAS_TEXT_TEMP_SAVED_PRODUCTS','Saved Products');
	define('AAS_TEXT_STAYED_IN_THIS_PAGE',' , time passed: ');
	define('AAS_TEXT_CREATED_BY','Created with passion by ');
	define('AAS_TEXT_VERSION',' version ');
	define('AAS_TEXT_PLACEHOLDER_SEARCH','Search');
	
	//ATTRIBUTES MANAGER

	define('AAS_TEXT_AM_TOOLTIP_ADD_NEW_PRODUCT_OPTION','Add new Product Option!');
	define('AAS_TEXT_AM_TOOLTIP_ADD_NEW_PRODUCT_VALUE','Add new Product Value!');
	define('AAS_TEXT_AM_TOOLTIP_CLOSE','Close Attributes Manager');
	define('AAS_TEXT_AM_ADD_OPTION','+ Add Product Option');
	define('AAS_TEXT_AM_ADD_OPTION_VALUE','+ Add Option Value');
	define('AAS_TEXT_AM_PRODUCTS_OPTIONS','Product Options');
	define('AAS_TEXT_AM_PRODUCTS_OPTIONS_VALUES','Option Values');

	define('AAS_TEXT_AM_OPTION','Option Name');
	define('AAS_TEXT_AM_OPTION_VALUE','Option Value');

	define('AAS_TEXT_AM_ID','Id');
	define('AAS_TEXT_AM_OPTION_NAME','Option Name');
	define('AAS_TEXT_AM_ACTION','Action');
	define('AAS_TEXT_AM_EDIT','Edit');
	define('AAS_TEXT_AM_DELETE','Delete');
	//define('AAS_TEXT_AM_OPTION_VALUE','Option Value');
	define('AAS_TEXT_AM_PRODUCT_NAME','Product Name');
	
	define('AAS_TEXT_AM_WARNING_OPTION_HAS_PRODUCTS_AND_VALUES','This option is assigned to products therefore it is not safe to delete it. If you delete it then this attribute option will be deleted from all products in all languages. If you delete it then this attribute option will be deleted from the products in all languages.<br> Note: any option values assigned to that option name will also be deleted!');
	
	define('AAS_TEXT_AM_WARNING_HAS_NO_PRODUCTS_AND_VALUES','This option name is not assigned to any products therefore it is safe to delete it.<br> Note: any option values assigned to that option name will also be deleted!');
	
	define('AAS_TEXT_AM_WARNING_OPTION_VALUE_HAS_PRODUCTS_AND_VALUES','This option value is assigned to an option name that is linked to products therefore it is not safe to delete it.<br>If you delete it then this attribute option value will be deleted from the products in all languages.');
	
	define('AAS_TEXT_AM_WARNING_OPTION_VALUE_SAFE_TO_DELETE','This option value is assigned to an option name that is not linked to any products therefore it is safe to delete it.');
	
	define('AAS_TEXT_IN_TABLE_SEARCH','In Table Search');
	
	define('AAS_TEXT_AM_DIALOG_TITLE_EDITING_OPTION_NAME','Editing products option name');
	define('AAS_TEXT_AM_DIALOG_TITLE_DELETE_OPTION_NAME','Delete products option name: ');
	define('AAS_TEXT_AM_DIALOG_TITLE_DELETE_OPTION_VALUE','Delete option value: ');
		
	//ONLINE USERS
	
	define('AAS_TEXT_OU_MOST_USERS_ONLINE','max online users:');
	define('AAS_TEXT_OU_AT','at:');
	define('AAS_TEXT_OU_ONLINE_USERS','Online Users');
	define('AAS_TEXT_OU_SUBTITLE','Alternative Administration System: Online Users Viewer');
	
	//IMPORT
	
	define('AAS_TEXT_IMPORT_TITLE','Products\' Changes File Importer');
	define('AAS_TEXT_IMPORT_VERSION','version: 0.1 beta');
	define('AAS_TEXT_IMPORT_WARNING','Warning: Upload only files that are first created by the Alternative Administration System export method!');
	define('AAS_TEXT_IMPORT_DRAG_N_DROP','Drag & Drop files here!');
	define('AAS_TEXT_IMPORT_SUPPORTED_FILES','Supported files: csv, txt, supported delimeters: , ; * and tab character');
	define('AAS_TEXT_IMPORT_WARNING_MESSAGE','Any product\'s changes from the Imported File will not be automatically applied.<br /> After uploading you will have the oportunity to double check the changes and then apply!');
	define('AAS_TEXT_IMPORT_WARNING_MESSAGE_1','Uploaded file is not saved on server. After parsing the data the uploaded file is auto deleted!');
	define('AAS_TEXT_IMPORT_TOOLTIP_SUBMIT_CHANGES','Submit changes from file to associated products!');
	define('AAS_TEXT_IMPORT_SUBMIT_CHANGES','Submit Product\'s Changes');
	define('AAS_TEXT_IMPORT_TOOLTIP_UPLOAD_NEW_FILE','Upload new file, any changes not submitted will be lost!');
	define('AAS_TEXT_IMPORT_UPLOAD_NEW_FILE','Upload new file!');
	define('AAS_TEXT_IMPORT_TOOLTIP_CLOSE','Close File Importer!');
	define('AAS_TEXT_IMPORT_CLOSE','Close');
	define('AAS_TEXT_IMPORT_BROWSER_NOT_SUPPORTED','Your browser does not support HTML5 file uploads!');
	define('AAS_TEXT_IMPORT_TOO_MANY_FILES','Too many files! Please select only one!');
	define('AAS_TEXT_IMPORT_FILE_TOO_LARGE','is too large! Please upload files up to 6mb.');
	define('AAS_TEXT_IMPORT_FILETYPE_NOT_ALLOWED','File type is not allowed.');
	define('AAS_TEXT_IMPORT_FILE_EXTENSION_NOT_ALLOWED','File extension not allowed.');
	define('AAS_TEXT_IMPORT_ERROR_PARSING_DATA','Error parsing data. File is corrupted or not recognized!<br>Please make sure you are importing a file<br>that has first been exported by AAS Export products method!');
	define('AAS_TEXT_IMPORT_PLEASE_WAIT_WHILE_UPDATING','Please wait while updating products values!');
	define('AAS_TEXT_IMPORT_SUCCESSFULLY_UPDATED','Product\'s values successfully updated.<br>In order to view the updates please reload the page.');
	define('AAS_TEXT_IMPORT_RELOAD_NOW','Reload Now');
	define('AAS_TEXT_IMPORT_SOMETHING_WENT_WRONG','Something went wrong. Product\'s values did not updated. Please try again!');
	define('AAS_TEXT_IMPORT_COULD_NOT_UPDATE_PRODUCTS_DATA','Could not update products data!<br>Make sure the imported file is correct.');
	define('AAS_TEXT_IMPORT_EMPTY_DATA','Empty Data!');
	
	//GENERAL
	define('AAS_TEXT_SELECTED_PRODUCTS_LINKED_SUCCESSFULLY','Selected products linked with selected category successfully.');
	define('AAS_TEXT_SELECTED_PRODUCTS_LINKED_FAILED','Selected products failed to be linked with selected category.');
	define('AAS_TEXT_SELECTED_PRODUCTS_LINKED_FAILED_NOCOLUMN_LINKED','Selected products failed to be linked with selected category<br>because the column [ <b>linked</b> ] does not exist in [ <b>'.TABLE_PRODUCTS_TO_CATEGORIES.'</b> ] table.');
	define('AAS_TEXT_SELECTED_PRODUCTS_LINKED_ABORTED_NO_PRODUCTS_TO_LINK','Selected products failed to be linked with selected category because they may already be linked to this category.');
	
	define('AAS_TEXT_CANNOT_OPEN_DIALOG','Cannot open dialog!');
	define('AAS_TEXT_CANNOT_CLOSE_DIALOG','Cannot close dialog. Dialog does not exist!');
	define('AAS_TEXT_CANNOT_MOVE_SELECTED_PRODUCTS','Cannot move selected products to same category!');
	define('AAS_TEXT_SELECTED_PRODUCTS_HAVE_BEEN_MOVED_TO_SELECTED_CATEGORY','Selected products have been moved to selected category!');
	define('AAS_TEXT_SELECTED_PRODUCTS_HAVE_NOT_BEEN_MOVED_TO_SELECTED_CATEGORY','Selected products have not been moved to selected category!');
	define('AAS_TEXT_SELECTED_PRODUCTS_HAVE_BEEN_COPIED_TO_SELECTED_CATEGORY','Selected products have been copied to selected category!');
	define('AAS_TEXT_SELECTED_PRODUCTS_HAVE_NOT_BEEN_COPIED_TO_SELECTED_CATEGORY','Selected products have not been copied to selected category!');
	define('AAS_TEXT_NO_PRODUCTS_SELECTED','No Products Selected!');
	define('AAS_TEXT_ATTRIBUTES_HAVE_BEEN_DELETED','Attributes have been deleted!');	
	define('AAS_TEXT_ATTRIBUTES_HAVE_BEEN_COPIED','Attributes have been copied!');
	define('AAS_TEXT_NO_PRODUCTS_FOUND_TO_DELETE_THEIR_ATTRIBUTES','No products found to delete their attributes');
	define('AAS_TEXT_SELECTED_PRODUCT_HAS_NOT_ATTRIBUTES_TO_COPY','Selected product has not Attributes to copy from!');
	define('AAS_TEXT_NOT_FOUND_PRODUCTS_TO_COPY_ATTRIBUTES','Not found products to copy Attributes to!');
	define('AAS_TEXT_ATTRIBUTES_HAVE_NOT_BEEN_COPIED','Something went wrong. Attributes have not been copied!');
	define('AAS_TEXT_SELECTED_PRODUCTS_SUCCESSFULLY_ADDED_TO_TEMP_LIST','Selected Products successfully added to temp List!');
	define('AAS_TEXT_WAIT_WHILE_UPDATING_VALUES','Please wait while updating values!');
	define('AAS_TEXT_SUCCESSFULLY_UPDATED_VALUES','Successfully Updated values');
	define('AAS_TEXT_VALUES_HAVE_NOT_BENN_UPDATED','Something went wrong, Values have not been updated');
	define('AAS_TEXT_SUCCESSFULLY_CHANGED_SETTING','Successfully changed setting!');
	define('AAS_TEXT_IN_ORDER_TO_CHANGE_SETTING_RELOAD_PAGE','In order to change the setting you must reload the page.');
	define('AAS_TEXT_RELOAD_NOW','Reload Now');
	define('AAS_TEXT_ARE_YOU_SURE_TO_DELETE_ATTRIBUTE','Are you sure you want to delete the attribute:');
	define('AAS_TEXT_ARE_YOU_SURE_TO_DELETE_SELECTED_ATTRIBUTES','Are you sure you want to delete the selected attributes:');
	define('AAS_TEXT_SUCCESSFULLY_CHANGED_AVAILABLE_DATE','Successfully changed Available Date!');
	define('TEXT_IMAGE_NONEXISTENT','Not found Image');
	define('AAS_TEXT_EDIT_DESCRIPTION_IN','Edit description in');
	define('AAS_TEXT_EDIT_DESCRIPTION_IN_LANGUAGE',' language.');
	
	define('AAS_TEXT_NO_CATEGORIES_FOUND','No Categories Found!');
	define('AAS_TEXT_NO_FOUND','Not found!');
	define('AAS_TEXT_TOP','Top');
	define('AAS_TEXT_ORIGINAL_PRODUCT','Original Product.');
	define('AAS_TEXT_ORIGINAL_PRODUCT_AT','Original product\'s category:&nbsp;');
	define('AAS_TEXT_LINKED_WITH','Linked with:');
	define('AAS_TEXT_LINKED_PRODUCT','Linked!');
	define('AAS_TEXT_REMOVE_LINKED_PRODUCT','Unlink');
	define('AAS_TEXT_ALSO_LINKED_WITH','Also linked with:&nbsp;');
	define('AAS_TEXT_NOT_LINKED','Not linked!');
	define('AAS_TEXT_EDIT_DISABLED','Edit Disabled');
	define('AAS_TEXT_UPLOAD_MODULE','Upload Module');
	
	define('AAS_TEXT_PRINT_CATEGORIES_AND_PRODUCTS_TABLE','Print Categories & Products table');
	define('AAS_TEXT_PRINT_CATEGORIES_TABLE','Print Categories table');
	define('AAS_TEXT_PRINT_PRODUCTS_TABLE','Print Products table');
	define('AAS_TEXT_PRINT_INCLUDE_PAGINATION','Include Pagination');
	
	define('AAS_TEXT_PAGE_PREVIEW_REFRESH','Refresh page Preview');
	define('AAS_TEXT_PAGE_PREVIEW_OPEN','Open in new Window');
	define('AAS_TEXT_PAGE_PREVIEW_CLOSE','Close page Preview');
	
	define('AAS_TEXT_EMPTY_FIELDS_FOUND','Empty fields found!');
	define('AAS_TEXT_ARE_YOU_SURE_TO_LOGOFF','Are you sure you want to logoff?');
	
	define('AAS_TEXT_ADD_LINKED_COLUMN_QUERY_SUCCESS','Successfully executed query.<br>New column named [ linked ] has been  added into '.TABLE_PRODUCTS_TO_CATEGORIES.'.<br>Please reload this page.');
	define('AAS_TEXT_ADD_LINKED_COLUMN_QUERY_FAIL','Something went wrong.<br>Could not execute query.<br>Please try again or do it manually.');
	
	define('AAS_TEXT_SUBMITTED_WRONG_VALUE','Wrong value entered.');
	
  //used from aas format seconds	
	define('AAS_TEXT_YEAR','year');
	define('AAS_TEXT_YEARS','years');
	define('AAS_TEXT_MONTH','month');
	define('AAS_TEXT_MONTHS','months');
	define('AAS_TEXT_WEEK','week');
	define('AAS_TEXT_WEEKS','weeks');
  define('AAS_TEXT_DAY','day');
	define('AAS_TEXT_DAYS','days');
	define('AAS_TEXT_HOUR','hour');
	define('AAS_TEXT_HOURS','hours');
	define('AAS_TEXT_MINUTE','min');
	define('AAS_TEXT_MINUTES','mins');
	define('AAS_TEXT_SECOND','sec');
	define('AAS_TEXT_SECONDS','secs');
	
  //ALERTS TEXT
  define('AAS_ALERT_NO_COLUMN_LINKED_FOUND','Warning: AAS requires a new column in the table "'.TABLE_PRODUCTS_TO_CATEGORIES.'" for the linked products to work right. Please click <img class="toolBox-help-icon" src="ext/aas/images/glyphicons_194_circle_question_mark.png" alt="explain"><div class="info-explain">The new column required is called [ <b>linked</b> ] and must be added into [ <b>'.TABLE_PRODUCTS_TO_CATEGORIES.'</b> ] table.<br>It helps AAS to determine if the product is in its original category or not.<br>If its linked, AAS will colorize the tbl row, with a light blue, in order for admins to know its a linked product.<br><br/>Execute the sql code bellow through phpmyadmin.<br><br><pre>ALTER TABLE  `products_to_categories` ADD  `linked` TINYINT( 1 ) NOT NULL DEFAULT  "0"</pre><br>or click <button id="explain_add_linked_column_btn" class="applyButton">here</button> to automatically execute that query.<br><br><i>Note: this small table modification does not affect your store structure.</i></div> for more information.');

	//CLOCKS
	define('AAS_CLOCK_TITLE_LOCAL','Local');
	define('AAS_CLOCK_TITLE_ATHENS','Athens');
	define('AAS_CLOCK_TITLE_LOS_ANGELES','Los Angeles');
	define('AAS_CLOCK_TITLE_PARIS','France');
	define('AAS_CLOCK_TITLE_SYDNEY','Sydney');
	
	//DONATIONS
	define('AAS_DONATIONS_TITLE','Donation for Alternative Administration System');
	define('AAS_DONATIONS_CLOSE','Close');
	define('AAS_DONATIONS_DONATE_CUSTOM_AMOUNT','Donate custom amount');
	define('AAS_DONATIONS_OR','or');
	
	define('AAS_DONATIONS_TEXT_1','If you like <span style="font-weight:bold">Alternative Administration System</span> (AAS) please make a donation.');
	define('AAS_DONATIONS_TEXT_2','I\'ve spent lots of my free time developing AAS for you.<br>It is absolutely free but in order for me to be happy and able to continue developing it, fixing bugs e.t.c. you have to make a donation.');
	define('AAS_DONATIONS_TEXT_3','By donating even one penny you\'ll have free support!');
	define('AAS_DONATIONS_TEXT_4','I can provide support through Skype or by email any time thats best for you!');
	define('AAS_DONATIONS_TEXT_5','Thank you for making a donation.');
	
	//EVENTS CALENDAR
  define('AAS_CALENDAR_ENTER_EVENT_TITLE','	Please enter an event Title.');
	define('AAS_CALENDAR_EVENT_NOT_UPDATED','Something went wrong. Event has not been updated!');
	define('AAS_CALENDAR_EVENT_NOT_DELETED','Something went wrong. Event has not been deleted!');
	define('AAS_CALENDAR_EVENT_NOT_ADDED','Something went wrong. Cannot add Event. Please try again!');
	define('AAS_CALENDAR_SUCCESSFULLY_UPDATED_EVENT','Successfully updated event!');
	define('AAS_CALENDAR_SUCCESSFULLY_DELETED_EVENT','Successfully deleted event!');
	define('AAS_CALENDAR_EDIT_EVENT','Edit Event:');
	define('AAS_CALENDAR_NEW_EVENT','New Event:');
	define('AAS_CALENDAR','Events Calendar');
	define('AAS_CALENDAR_CLOSE','Close');
	
	//CONTACT ME
	define('AAS_CONTACTME_TITLE','Contact Me');
	define('AAS_CONTACTME_CLOSE','Close');
	define('AAS_CONTACTME_MESSAGE_COULD_NOT_BE_SENT','Something went wrong. Message could not be sent. Please try again!');
	define('AAS_CONTACTME_MESSAGE_SUCCESSFULLY_SENT','Your message successfully sent. I will contact you as soon as possible.');
	define('AAS_CONTACTME_EMPTY_FIELDS_FOUND','Empty fields found!');
	define('AAS_CONTACTME_HELLO_MY_NAME_IS','Hello my name is ');
	define('AAS_CONTACTME_I_AM_THE_CREATOR_OF_AAS',' I\'m the creator of AAS.');
	define('AAS_CONTACTME_TEXT_1','If you have any questions or need some help or want me to to develop something special to be');
	define('AAS_CONTACTME_TEXT_2','included in AAS just send me a message using the form below or at ');
	define('AAS_CONTACTME_BUTTON_SEND_MESSAGE','Send Message');
	define('AAS_CONTACTME_TEXT_NOTE','Note: by pressing the Send Message button you\'ll send me an email<br> at jbqwerty@gmail.com using the default osc mail function.');
	
	//PRODUCT IMAGES
	define('AAS_PRODUCT_IMAGES_TITLE','Product Images');
	define('AAS_PRODUCT_IMAGES_CLOSE','Close');
	define('AAS_PRODUCT_IMAGES_PRODUCT_NAME','Product Name: ');
	define('AAS_PRODUCT_IMAGES_CURRENT_SMALL_IMAGE','Current Small Image: ');
	define('AAS_PRODUCT_IMAGES_NEW_SMALL_IMAGE','New Small Image:');
	define('AAS_PRODUCT_IMAGES_DRAG_N_DROP_NEW_IMAGE_HERE','Drag n Drop new Image Here');
	define('AAS_PRODUCT_IMAGES_DRAG_N_DROP_NEW_IMAGE_HERE_OR','or');
	define('AAS_PRODUCT_IMAGES_SELECT_FOLDER_TO_SAVE_NEW_IMAGE','select folder to save new image');
	define('AAS_PRODUCT_IMAGES_IMAGES','images&nbsp;');
	define('AAS_PRODUCT_IMAGES_NO_WRITABLE','no writable!');
	define('AAS_PRODUCT_IMAGES_NO_WRITABLE_FOLDERS_WARNING','No writable image folders found!');
	define('AAS_PRODUCT_IMAGES_PRODUCT_LARGE_IMAGES','Product Large Images');
	define('AAS_PRODUCT_IMAGES_ADD_LARGE_IMAGE','Add Large Image');
	define('AAS_PRODUCT_IMAGES_UPDATE_HTML_CONTENT','Update HTML Content');
	define('AAS_PRODUCT_IMAGES_HTML_CONTENT','HTML Content for (popup)');
	define('AAS_PRODUCT_IMAGES_LARGE_IMAGE_PREVIEW','Large Image Preview');
	define('AAS_PRODUCT_IMAGES_CHANGE_NEW_IMAGE_LOCATION','Change new Image location:');
	
	define('AAS_PRODUCT_IMAGES_IF_IMAGE_EXISTS_THEN','If an image with same filename already exists then: ');
	define('AAS_PRODUCT_IMAGES_OVERWRITE_IT','Overwrite it');
	define('AAS_PRODUCT_IMAGES_AUTO_RENAME_THE_NEW_IMAGE','Auto rename the new image');
	define('AAS_PRODUCT_IMAGES_CANCEL_IMAGE_UPLOAD','Cancel image upload');
	
	
	
	//PRODUCT IMAGES JS
	
	define('AAS_PRODUCT_IMAGES_HTML_CONTENT_NOT_UPDATED','Could not update HTML Content. Please try again.');
	define('AAS_PRODUCT_IMAGES_HTML_CONTENT_UPDATED','Successfully updated HTML Content.');
	define('AAS_PRODUCT_IMAGES_COULD_NOT_DELETE_LARGE_IMAGE','Could not delete Large Image. Please try again.');
	define('AAS_PRODUCT_IMAGES_SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN','Something went wrong. Please try again.');
	
	//CATEGORY IMAGE
	define('AAS_CATEGORY_IMAGES_TITLE','Category Image');
	define('AAS_CATEGORY_IMAGES_CLOSE','Close');
	define('AAS_CATEGORY_IMAGES_CATEGORY_NAME','Category Name: ');
	define('AAS_CATEGORY_IMAGES_CURRENT_SMALL_IMAGE','Current Small Image: ');
	define('AAS_CATEGORY_IMAGES_NEW_SMALL_IMAGE','New Small Image:');
	define('AAS_CATEGORY_IMAGES_DRAG_N_DROP_NEW_IMAGE_HERE','Drag n Drop new Image Here');
	define('AAS_CATEGORY_IMAGES_DRAG_N_DROP_NEW_IMAGE_HERE_OR','or');
	define('AAS_CATEGORY_IMAGES_SELECT_FOLDER_TO_SAVE_NEW_IMAGE','select folder to save new image');
	define('AAS_CATEGORY_IMAGES_IMAGES','images&nbsp;');
	define('AAS_CATEGORY_IMAGES_NO_WRITABLE','no writable!');
	define('AAS_CATEGORY_IMAGES_NO_WRITABLE_FOLDERS_WARNING','No writable image folders found!');
	define('AAS_CATEGORY_IMAGES_CHANGE_NEW_IMAGE_LOCATION','Change new Image location:');
		
	//MODULES
	define('AAS_MODULES_TITLE','Modules');
	define('AAS_MODULES_SELECT_TITLE','Modules');
	define('AAS_MODULES_ADDITIONAL_FEATURES','Modules provide additional features to A.A.S');

	define('AAS_DIALOG_UPLOAD_MODULE_NOTE','Existing Modules WILL be overwritten.');
	define('AAS_DIALOG_UPLOAD_MODULE_DRAG_N_DROP','Drag n Drop zip File here');
	define('AAS_DIALOG_UPLOAD_MODULE_OR',' or ');
	define('AAS_DIALOG_UPLOAD_MODULE_INSTALLED_MODULES','Installed Modules');
	define('AAS_DIALOG_UPLOAD_MODULE_AVAILABLE_MODULES','Available Modules');
	define('AAS_DIALOG_UPLOAD_MODULE_REQUEST_MODULES','Request Modules');
	define('AAS_DIALOG_UPLOAD_MODULE_DIESI','#');
	define('AAS_DIALOG_UPLOAD_MODULE_MODULE','Module');
	define('AAS_DIALOG_UPLOAD_MODULE_VERSION','Version');
	define('AAS_DIALOG_UPLOAD_MODULE_DEVELOPERS','Developers');
	define('AAS_DIALOG_UPLOAD_MODULE_INSTALLATION_STATUS','Installation Status');
	
	define('AAS_UPLOAD_MODULE_STATUS_INSTALLED_AND_UPDATED','Latest version Installed');
	define('AAS_UPLOAD_MODULE_STATUS_INSTALLED_BUT_OUTDATED','Installed but Outdated');
	define('AAS_UPLOAD_MODULE_STATUS_NOT_INSTALLED','Not Installed');

	define('AAS_UPLOAD_MODULE_TEXT_CLICK','Click ');
	define('AAS_UPLOAD_MODULE_TEXT_HERE','here');
	define('AAS_UPLOAD_MODULE_TEXT_TO_GET_NEWEST_VERSION',' to get the newest version.');
	define('AAS_UPLOAD_MODULE_TEXT_TO_INSTALL_IT',' to install it.');
	define('AAS_UPLOAD_MODULE_TEXT_TO_FIND_OUT_MORE',' to find out more');
	define('AAS_UPLOAD_MODULE_TEXT_WRONG_VERSION_INSTALLED','Wrong version installed!');
	
//	define('AAS_DIALOG_UPLOAD_MODULE_TEXT_AAC_UPDATED','Admin Access Control for this Module successfully changed.<br /><br />If changes affect your admin account, those will be applied after a page refresh.');

	define('AAS_DIALOG_UPLOAD_MODULE_TEXT_AAC','Admin Access Control');
	define('AAS_DIALOG_UPLOAD_MODULE_TEXT_EDIT','Edit');
	
	define('AAS_DIALOG_UPLOAD_MODULE_SUCCESS','Module Uploaded successfully.<br />In order to be activated you must reload the page.<br /><br />Reload now?');
	define('AAS_DIALOG_UPLOAD_MODULE_ERROR','Please try again!');
  
  define('AAS_DIALOG_UPLOAD_MODULE_NO_MODULES_INSTALLED','No Modules installed');
  
  define('AAS_DIALOG_UPLOAD_MODULE_TEXT_CLICK','Click');
  define('AAS_DIALOG_UPLOAD_MODULE_TEXT_HERE','here');
  define('AAS_DIALOG_UPLOAD_MODULE_TEXT_VISIT_CREATION_REQUEST_PAGE','to visit the modules creation request page.');
  define('AAS_DIALOG_UPLOAD_MODULE_TEXT_VISIT_MODIFICATION_REQUEST_PAGE','to visit the modules modification request page.');
  

	
	//ADMIN ACCESS CONTROL | ADMIN OPTIONS

	define('AAS_AAC_DIALOG_BUTTON_SAVE_CHANGES','Save Changes For Selected Tab');
		
	define('AAS_TEXT_DESCRIPTION','Description');
	define('AAS_TEXT_ADMINISTRATORS','Administrators');
	
	define('AAS_TEXT_COLUMN','Column');
	define('AAS_TEXT_VISIBLE','Visible');
	define('AAS_TEXT_LOCK_VISIBILITY','Lock Visibility');
	define('AAS_TEXT_OVERRIDE_DEFAULTS','Override defaults');
		
	define('AAS_AAC_TAB_TITLE_ACTIONS','Actions');
	define('AAS_AAC_TAB_TITLE_FIELDS_DISPLAY','Columns Display');
	define('AAS_AAC_TAB_TITLE_FIELDS_DISABLE_ACTIONS','Column Fields Disable Actions');
	define('AAS_AAC_TAB_TITLE_EXTRAS','Extras');
	define('AAS_AAC_TAB_TITLE_MODULES','Modules');
	define('AAS_DIALOG_AAC_TEXT_UPDATED','Successfully Updated!');
	
	define('AAS_DIALOG_AAC_TEXT_NOTE','Changes are lost unless each tab is saved!');
	define('AAS_AAC_TEXT_ADMIN_OPTIONS_SAVED','Successfully saved. Changes will take effect upon page refresh.');
	
  define('AAS_AAC_TAB_TITLE_DEFAULT_COLUMNS_DISPLAY','Default Columns Display');	          
  define('AAS_AAC_TAB_TITLE_DEFAULT_COLUMNS_DISPLAY_PER_ADMIN','Columns Display per Admin');
  
  	
	//Descriptions
	define('AAS_AAC_DISPLAY_FIELDS_PANEL','Hide the columns selection panel from:');
	define('AAS_AAC_DISPLAY_LANGUAGE_SELECTION','Hide the language selection dropdown for:');
	define('AAS_AAC_DISPLAY_BOTTOM_INFORMATION','Hide the bottom Information, such as Credits and AAS version.');
	define('AAS_AAC_DISPLAYCOUNTPRODUCTS','Hide the Count Products and Count Subcategories checkboxes (applies to categories).');
	define('AAS_AAC_DISABLE_ATTRIBUTES_MANAGER','Disable the attributes manager. Add options, option values e.t.c.');
	define('AAS_AAC_DISABLE_TEMP_PRODUCTS_LIST','Disable temporary products list. Ability to store there various products from diferrent categories in order to mass edit them later.');
	define('AAS_AAC_DISABLE_TOOLBOX','Disable toolbox panel. (Copy, Move or Link products || Copy or Delete products attributes)');
	define('AAS_AAC_DISABLE_CLOCKS','Hide clocks.');
	define('AAS_AAC_DISABLE_SPECIALS','Disable specials manager.');
	//define('AAS_AAC_ENABLE_PRODUCTS_EXPECTED','Disable products expected.');
	define('AAS_AAC_DISABLE_CALENDAR','Disable events calendar.');
	define('AAS_AAC_DISABLE_ONLINE_USERS','Disable online users viewer display.');
	define('AAS_AAC_DISABLE_CONTACT_ME','Disable contact me dialog (ability to contact the creator of AAS via submitting a form).');
	define('AAS_AAC_DISABLE_DONATIONS','Disable donations dialog. You can DONATE any amount to AAS creator so he can continue developing this great addon.');
  define('AAS_AAC_DISABLE_MODULES_MANAGER_DIALOG','Disable Modules Upload Manager. ');
	
	define('AAS_AAC_DISPLAY_PRODUCTS_IMAGE','Display product image column.');
	define('AAS_AAC_DISPLAY_PRODUCTS_MODEL','Display product model column.');
	define('AAS_AAC_DISPLAY_PRODUCTS_QUANTITY','Display product quantity column.');
	define('AAS_AAC_DISPLAY_PRODUCTS_PRICE','Display product price (NET) column.');
	define('AAS_AAC_DISPLAY_PRODUCTS_PRICE_GROSS','Display product price (GROSS) column.');
	define('AAS_AAC_DISPLAY_TAX_CLASS_TITLE','Display tax class title column.');
	define('AAS_AAC_DISPLAY_PRODUCTS_WEIGHT','Display product weight column.');
	define('AAS_AAC_DISPLAY_PRODUCTS_STATUS','Display product status column.');
	define('AAS_AAC_DISPLAY_PRODUCTS_DATE_AVAILABLE','Display product date available column.');
	define('AAS_AAC_DISPLAY_PRODUCTS_DESCRIPTION','Display product description column.');
	define('AAS_AAC_DISPLAY_PRODUCTS_ORDERED','Display product ordered column.');
	define('AAS_AAC_DISPLAY_PRODUCTS_URL','Display product url column.');
	define('AAS_AAC_DISPLAY_PRODUCTS_VIEWED','Display product viewed column.');
	define('AAS_AAC_DISPLAY_MANUFACTURERS_NAME','Display manufactures column.');
	define('AAS_AAC_DISPLAY_ATTRIBUTES','Display attributes column.');
	define('AAS_AAC_DISPLAY_SPECIAL','Display specials column.');
	define('AAS_AAC_DISPLAY_PRODUCTS_LINKED','Display linked products column.');
	define('AAS_AAC_DISPLAY_PRODUCTS_ORDER_STATUS','Display product order status column');
	
	define('AAS_AAC_DISABLE_ACTION_FOR_PRODUCTS_IMAGE','Disable products image edit.');
	define('AAS_AAC_DISABLE_ACTION_FOR_PRODUCTS_MODEL','Disable products model edit.');
	define('AAS_AAC_DISABLE_ACTION_PRODUCTS_QUANTITY','Disable products quantity edit.');
	define('AAS_AAC_DISABLE_ACTION_PRODUCTS_PRICE','Disable products price net edit.');
	define('AAS_AAC_DISABLE_ACTION_PRODUCTS_PRICE_GROSS','Disable products price gross edit.');
	define('AAS_AAC_DISABLE_ACTION_TAX_CLASS_TITLE','Disable tax class change.');
	define('AAS_AAC_DISABLE_ACTION_PRODUCTS_WEIGHT','Disable products weight edit.');
	define('AAS_AAC_DISABLE_ACTION_PRODUCTS_STATUS','Disable products status change.');
	define('AAS_AAC_DISABLE_ACTION_PRODUCTS_DATE_AVAILABLE','Disable products available date change.');
	define('AAS_AAC_DISABLE_ACTION_PRODUCTS_DESCRIPTION','Disable products description edit.');
	define('AAS_AAC_DISABLE_ACTION_PRODUCTS_URL','Disable products url edit.');
	define('AAS_AAC_DISABLE_ACTION_MANUFACTURERS_NAME','Disable products manufacturer change.');
	define('AAS_AAC_DISABLE_ACTION_ATTRIBUTES','Disable products attributes edit.');
	define('AAS_AAC_DISABLE_ACTION_SPECIAL','Disable specials edit.');
	define('AAS_AAC_DISABLE_ACTION_PRODUCTS_LINKED','Disable products linked edit.');
	define('AAS_AAC_DISABLE_ACTION_PRODUCTS_NAME','Disable products name edit.');	
	define('AAS_AAC_DISABLE_ACTION_CATEGORIES_NAME','Disable categories name edit.');
	define('AAS_AAC_DISABLE_ACTION_SORT_ORDER','Disable sort order edit.');
	
	define('AAS_AAC_DISABLE_DELETE_PRODUCTS','Disable products deletion.');
	define('AAS_AAC_DISABLE_IMPORT','Disable Import products data.');
	define('AAS_AAC_DISABLE_EXPORT','Disable Export products data.');
	define('AAS_AAC_DISABLE_SEARCH','Disable search');
	define('AAS_AAC_DISABLE_PRINT','Disable print page');
	define('AAS_AAC_DISABLE_ALL_EDIT','Disable All Edit!');
	define('AAS_AAC_DISABLE_MASS_COLUMNS_EDIT','Disable Mass Columns Edit');

?>
