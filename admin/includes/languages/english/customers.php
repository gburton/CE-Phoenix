<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

const HEADING_TITLE = 'Customers';
const HEADING_TITLE_SEARCH = 'Search:';

const TABLE_HEADING_NAME = 'Name';
const TABLE_HEADING_ACCOUNT_CREATED = 'Account Created';
const TABLE_HEADING_ACTION = 'Action';

const TEXT_DATE_ACCOUNT_CREATED = 'Account Created: %s';
const TEXT_DATE_ACCOUNT_LAST_MODIFIED = 'Last Modified: %s';
const TEXT_INFO_DATE_LAST_LOGON = 'Last Logon: %s';
const TEXT_INFO_NUMBER_OF_LOGONS = 'Number of Logons: %s';
const TEXT_INFO_COUNTRY = 'Country: %s';
const TEXT_INFO_NUMBER_OF_REVIEWS = 'Number of Reviews: %s';
const TEXT_DELETE_INTRO = 'Are you sure you want to delete this customer?';
const TEXT_DELETE_REVIEWS = 'Delete %s review(s)';
const TEXT_INFO_HEADING_DELETE_CUSTOMER = 'Delete Customer';
const TYPE_BELOW = 'Type below';
const PLEASE_SELECT = 'Select One';

const PULL_DOWN_DEFAULT = PLEASE_SELECT;

const ERROR_PAGE_HAS_UNMET_REQUIREMENT = <<<'EOT'
  The customers page requires the 'sortable_name', 'name', 'email_address', 'country_id', and 'id' customer data modules to be installed.  Missing:
EOT;
