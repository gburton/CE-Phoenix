<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

// look in your $PATH_LOCALE/locale directory for available locales..
// Array examples which should work on all servers:
// 'en_US.UTF-8', 'en_US.UTF8', 'enu_usa'
// 'en_GB.UTF-8', 'en_GB.UTF8', 'eng_gb'
// 'en_AU.UTF-8', 'en_AU.UTF8', 'ena_au'

setlocale(LC_ALL, ['en_US.UTF-8', 'en_US.UTF8', 'enu_usa']);
const DATE_FORMAT_SHORT = '%m/%d/%Y';  // this is used for strftime()
const DATE_FORMAT_LONG = '%A %d %B, %Y'; // this is used for strftime()
const DATE_FORMAT = 'm/d/Y'; // this is used for date()
const PHP_DATE_TIME_FORMAT = 'm/d/Y H:i:s'; // this is used for date()
const DATE_TIME_FORMAT = DATE_FORMAT_SHORT . ' %H:%M:%S';
const JQUERY_DATEPICKER_I18N_CODE = ''; // leave empty for en_US; see http://jqueryui.com/demos/datepicker/#localization
const JQUERY_DATEPICKER_FORMAT = 'mm/dd/yy'; // see http://docs.jquery.com/UI/Datepicker/formatDate

// Global entries for the <html> tag
const HTML_PARAMS = 'dir="ltr" lang="en"';

// charset for web pages and emails
const CHARSET = 'utf-8';

// page title
const TITLE = 'OSCOM CE Phoenix Administration Tool';

// header text in includes/header.php
const HEADER_TITLE_ONLINE_CATALOG = '<i class="fas fa-shopping-cart text-primary"></i> <span class="border-bottom border-primary">Your Shop</span>';
const HEADER_TITLE_PHOENIX_CLUB = '<span class="border-bottom border-primary">Phoenix Club</span>';
const HEADER_TITLE_CERTIFIED_ADDONS = '<span class="border-bottom border-primary">Certified Addons & Services</span>';
const HEADER_TITLE_LOGOFF = '<i class="fas fa-lock"></i> <span class="border-bottom border-danger"> %s, securely logoff</span>';

// javascript messages
const JS_STATE_SELECT = '-- Select Above --';

// images
const IMAGE_ANI_SEND_EMAIL = 'Sending E-Mail';
const IMAGE_BACK = 'Back';
const IMAGE_BACKUP = 'Backup';
const IMAGE_CANCEL = 'Cancel';
const IMAGE_CONFIRM = 'Confirm';
const IMAGE_COPY = 'Copy';
const IMAGE_COPY_TO = 'Copy To';
const IMAGE_DETAILS = 'Details';
const IMAGE_DELETE = 'Delete';
const IMAGE_EDIT = 'Edit';
const IMAGE_EMAIL = 'Email';
const IMAGE_EXPORT = 'Export';
const IMAGE_ICON_STATUS_GREEN = 'Active';
const IMAGE_ICON_STATUS_GREEN_LIGHT = 'Set Active';
const IMAGE_ICON_STATUS_RED = 'Inactive';
const IMAGE_ICON_STATUS_RED_LIGHT = 'Set Inactive';
const IMAGE_ICON_INFO = 'Info';
const IMAGE_INSERT = 'Insert';
const IMAGE_LOCK = 'Lock';
const IMAGE_MODULE_INSTALL = 'Install Module';
const IMAGE_MODULE_REMOVE = 'Remove Module';
const IMAGE_MOVE = 'Move';
const IMAGE_NEW_CATEGORY = 'New Category';
const IMAGE_NEW_COUNTRY = 'New Country';
const IMAGE_NEW_CURRENCY = 'New Currency';
const IMAGE_NEW_CUSTOMER_DATA_GROUP = 'New Customer Data Group';
const IMAGE_NEW_FILE = 'New File';
const IMAGE_NEW_FOLDER = 'New Folder';
const IMAGE_NEW_LANGUAGE = 'New Language';
const IMAGE_NEW_NEWSLETTER = 'New Newsletter';
const IMAGE_NEW_PRODUCT = 'New Product';
const IMAGE_NEW_TAX_CLASS = 'New Tax Class';
const IMAGE_NEW_TAX_RATE = 'New Tax Rate';
const IMAGE_NEW_TAX_ZONE = 'New Tax Zone';
const IMAGE_NEW_ZONE = 'New Zone';
const IMAGE_ORDERS = 'Orders';
const IMAGE_ORDERS_INVOICE = 'Invoice';
const IMAGE_ORDERS_PACKINGSLIP = 'Packing Slip';
const IMAGE_PREVIEW = 'Preview';
const IMAGE_RESTORE = 'Restore';
const IMAGE_RESET = 'Reset';
const IMAGE_SAVE = 'Save';
const IMAGE_SEARCH = 'Search';
const IMAGE_SELECT = 'Select';
const IMAGE_SEND = 'Send';
const IMAGE_SEND_EMAIL = 'Send Email';
const IMAGE_UNLOCK = 'Unlock';
const IMAGE_UPDATE = 'Update';
const IMAGE_UPDATE_CURRENCIES = 'Update Exchange Rate';
const IMAGE_UPLOAD = 'Upload';

const ICON_CROSS = 'False';
const ICON_CURRENT_FOLDER = 'Current Folder';
const ICON_DELETE = 'Delete';
const ICON_ERROR = 'Error';
const ICON_FILE = 'File';
const ICON_FILE_DOWNLOAD = 'Download';
const ICON_FOLDER = 'Folder';
const ICON_LOCKED = 'Locked';
const ICON_PREVIOUS_LEVEL = 'Previous Level';
const ICON_PREVIEW = 'Preview';
const ICON_STATISTICS = 'Statistics';
const ICON_SUCCESS = 'Success';
const ICON_TICK = 'True';
const ICON_UNLOCKED = 'Unlocked';
const ICON_WARNING = 'Warning';

// constants for use in tep_prev_next_display function
const TEXT_RESULT_PAGE = 'Page %s of %d';
const TEXT_DISPLAY_NUMBER_OF_COUNTRIES = 'Displaying <strong>%d</strong> to <strong>%d</strong> (of <strong>%d</strong> countries)';
const TEXT_DISPLAY_NUMBER_OF_CUSTOMER_DATA_GROUPS = 'Displaying <strong>%d</strong> to <strong>%d</strong> (of <strong>%d</strong> customer data groups)';
const TEXT_DISPLAY_NUMBER_OF_CUSTOMERS = 'Displaying <strong>%d</strong> to <strong>%d</strong> (of <strong>%d</strong> customers)';
const TEXT_DISPLAY_NUMBER_OF_CURRENCIES = 'Displaying <strong>%d</strong> to <strong>%d</strong> (of <strong>%d</strong> currencies)';
const TEXT_DISPLAY_NUMBER_OF_ENTRIES = 'Displaying <strong>%d</strong> to <strong>%d</strong> (of <strong>%d</strong> entries)';
const TEXT_DISPLAY_NUMBER_OF_LANGUAGES = 'Displaying <strong>%d</strong> to <strong>%d</strong> (of <strong>%d</strong> languages)';
const TEXT_DISPLAY_NUMBER_OF_MANUFACTURERS = 'Displaying <strong>%d</strong> to <strong>%d</strong> (of <strong>%d</strong> manufacturers)';
const TEXT_DISPLAY_NUMBER_OF_NEWSLETTERS = 'Displaying <strong>%d</strong> to <strong>%d</strong> (of <strong>%d</strong> newsletters)';
const TEXT_DISPLAY_NUMBER_OF_ORDERS = 'Displaying <strong>%d</strong> to <strong>%d</strong> (of <strong>%d</strong> orders)';
const TEXT_DISPLAY_NUMBER_OF_ORDERS_STATUS = 'Displaying <strong>%d</strong> to <strong>%d</strong> (of <strong>%d</strong> orders status)';
const TEXT_DISPLAY_NUMBER_OF_PRODUCTS = 'Displaying <strong>%d</strong> to <strong>%d</strong> (of <strong>%d</strong> products)';
const TEXT_DISPLAY_NUMBER_OF_PRODUCTS_EXPECTED = 'Displaying <strong>%d</strong> to <strong>%d</strong> (of <strong>%d</strong> products expected)';
const TEXT_DISPLAY_NUMBER_OF_REVIEWS = 'Displaying <strong>%d</strong> to <strong>%d</strong> (of <strong>%d</strong> product reviews)';
const TEXT_DISPLAY_NUMBER_OF_SPECIALS = 'Displaying <strong>%d</strong> to <strong>%d</strong> (of <strong>%d</strong> products on special)';
const TEXT_DISPLAY_NUMBER_OF_TAX_CLASSES = 'Displaying <strong>%d</strong> to <strong>%d</strong> (of <strong>%d</strong> tax classes)';
const TEXT_DISPLAY_NUMBER_OF_TAX_ZONES = 'Displaying <strong>%d</strong> to <strong>%d</strong> (of <strong>%d</strong> tax zones)';
const TEXT_DISPLAY_NUMBER_OF_TAX_RATES = 'Displaying <strong>%d</strong> to <strong>%d</strong> (of <strong>%d</strong> tax rates)';
const TEXT_DISPLAY_NUMBER_OF_ZONES = 'Displaying <strong>%d</strong> to <strong>%d</strong> (of <strong>%d</strong> zones)';

const PREVNEXT_BUTTON_PREV = '&lt;&lt;';
const PREVNEXT_BUTTON_NEXT = '&gt;&gt;';

const TEXT_DEFAULT = 'default';
const TEXT_SET_DEFAULT = 'Set as default';

const TEXT_NONE = '--none--';
const TEXT_TOP = 'Top';
const TEXT_ALL = 'All';

const ERROR_DESTINATION_DOES_NOT_EXIST = '<strong>Error:</strong> Destination does not exist.';
const ERROR_DESTINATION_NOT_WRITEABLE = '<strong>Error:</strong> Destination not writeable.';
const ERROR_FILE_NOT_SAVED = '<strong>Error:</strong> File upload not saved.';
const ERROR_FILETYPE_NOT_ALLOWED = '<strong>Error:</strong> File upload type not allowed.';
const SUCCESS_FILE_SAVED_SUCCESSFULLY = '<strong>Success:</strong> File upload saved successfully.';
const WARNING_NO_FILE_UPLOADED = '<strong>Warning:</strong> No file uploaded.';

// bootstrap helper
const MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION = '<p>Content Width can be 12 or less per column per row.</p><p>12/12 = 100% width, 6/12 = 50% width, 4/12 = 33% width.</p><p>Total of all columns in any one row must equal 12 (eg:  3 boxes of 4 columns each, 1 box of 12 columns and so on).</p>';

// seo helper
const PLACEHOLDER_COMMA_SEPARATION = 'Must, Be, Comma, Separated';

// message for required inputs
const FORM_REQUIRED_INPUT = '<span class="form-control-feedback text-danger"><i class="fas fa-asterisk"></i></span>';

const TEXT_IMAGE_NON_EXISTENT = 'IMAGE DOES NOT EXIST';
