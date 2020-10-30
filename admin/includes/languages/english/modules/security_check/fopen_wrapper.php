<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

const MODULE_SECURITY_CHECK_FOPEN_WRAPPER_TITLE = 'allow_url_fopen';
const MODULE_SECURITY_CHECK_FOPEN_WRAPPER_ERROR = <<<'EOT'
allow_url_fopen should be enabled in php.ini<br>
This is a hosting setting and may be able to be performed via your hosting control panel - if not, ask your host.
EOT;
