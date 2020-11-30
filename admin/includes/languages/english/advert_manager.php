<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

const HEADING_TITLE = 'Advert Manager';

const TABLE_HEADING_ADVERT = 'Advert';
const TABLE_HEADING_GROUP = 'Group';
const TABLE_HEADING_SORT_ORDER = 'Sort Order';
const TABLE_HEADING_STATUS = 'Status';
const TABLE_HEADING_ACTION = 'Action';

const TEXT_ADVERT_TITLE = 'Title';
const TEXT_ADVERT_URL = 'URL';
const TEXT_ADVERT_FRAGMENT = 'Fragment';
const TEXT_ADVERT_GROUP = 'Group';
const TEXT_ADVERT_SORT_ORDER = 'Sort Order';
const TEXT_ADVERT_NEW_GROUP = 'OR make a New Group';
const TEXT_ADVERT_IMAGE = 'Image';
const TEXT_ADVERT_IMAGE_LOCAL = 'OR enter local file name';
const TEXT_ADVERT_IMAGE_TARGET = 'Save To Directory';
const TEXT_ADVERT_HTML_TEXT = 'HTML Text';

const TEXT_ADVERT_TITLE_HELP = 'This is only used in the Listing of Adverts as a reminder for you.';
const TEXT_ADVERT_URL_HELP = 'Include https:// for external links or just a page name for internal links (eg product_info.php or advanced_search_result.php)';
const TEXT_ADVERT_FRAGMENT_HELP = 'Only used in conjunction with internal links in your Shop!  Fragment URL is (for example) products_id=3 or keywords=xyz';
const TEXT_ADVERT_SORT_HELP = 'This determines the Advert Sort Order *inside* it\'s group.';

const TEXT_ADVERT_NOTE = '
<strong>Advert Notes:</strong>
<ul>
  <li>You can use an image and/or HTML (usually text!).</li>
  <li>Output of the image/html will depend on the shop-side addon.</li>
</ul>';
const TEXT_INSERT_NOTE = '
<strong>Important Image Notes:</strong>
<ul>
  <li>Upload directories must have user (write) permissions setup!</li>
  <li>Do not fill out the "Save To Directory" field if you are not uploading an image to the webserver (ie, you are using a local (serverside) image).</li>
  <li>The "Save To Directory" field must be an existing directory with an ending slash (eg, adverts/ or carousels/).</li>
</ul>';

const TEXT_IMAGE_NONEXISTENT = 'Image MISSING!';

const TEXT_ADVERT_DATE_ADDED = 'Date Added: %s';
const TEXT_ADVERT_STATUS_CHANGE = 'Status Change: %s';

const TEXT_INFO_DELETE_INTRO = 'Are you sure you want to delete this Advert?';
const TEXT_INFO_DELETE_IMAGE = 'Delete Image';

const SUCCESS_IMAGE_INSERTED = '<strong>Success:</strong> New Advert inserted.';
const SUCCESS_IMAGE_UPDATED = '<strong>Success:</strong> This Advert has been updated.';
const SUCCESS_IMAGE_REMOVED = '<strong>Success:</strong> This Advert has been removed.';
const SUCCESS_ADVERT_STATUS_UPDATED = '<strong>Success:</strong> The status of this Advert has been updated.';

const ERROR_ADVERT_TITLE_REQUIRED = '<strong>Error:</strong> Advert TITLE required.';
const ERROR_ADVERT_GROUP_REQUIRED = '<strong>Error:</strong> Advert GROUP required.';
const ERROR_IMAGE_DIRECTORY_DOES_NOT_EXIST = '<strong>Error:</strong> Target directory does not exist: %s';
const ERROR_IMAGE_DIRECTORY_NOT_WRITEABLE = '<strong>Error:</strong> Target directory is not writeable: %s';
const ERROR_IMAGE_DOES_NOT_EXIST = '<strong>Error:</strong> Image does not exist.';
const ERROR_IMAGE_IS_NOT_WRITEABLE = '<strong>Error:</strong> Image can not be removed.';
const ERROR_ADVERT_IMAGE_OR_TEXT_REQUIRED = '<strong>Error:</strong> You have not inserted Image or Text.  Adverts need Image/Text/Both to display.';

const TEXT_DISPLAY_NUMBER_OF_ADVERTS = 'Displaying <b>%s</b> to <b>%s</b> of <b>%s</b> Adverts';
const IMAGE_NEW_ADVERT = 'New Advert';

const TEXT_ADVERT_EXTERNAL_URL = '<i class="fas fa-external-link-alt mr-1"></i> external link:<br>%s';
const TEXT_ADVERT_INTERNAL_URL = '<i class="fas fa-link mr-1"></i> internal link:<br>%s';

const IMAGE_IMPORT_ADVERT = 'Attempt Banner Import';
const IMAGE_IMPORT_ADVERT_EXPLANATION = 'This will import data from the "banners" database table.  You may need to delete some of the data after import as "banners" was never meant for Carousel use...';
const SUCCESS_BANNERS_IMPORTED = '<strong>Success:</strong>  Banners Table Imported!';
