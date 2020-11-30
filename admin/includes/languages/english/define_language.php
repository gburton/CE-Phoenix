<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', 'Define Languages');

define('TABLE_HEADING_FILES', 'Files');
define('TABLE_HEADING_WRITABLE', 'Writable');
define('TABLE_HEADING_LAST_MODIFIED', 'Last Modified');

define('TEXT_EDIT_NOTE', '<p class="lead">Editing Definitions</p>

<p>Each language definition is set using the PHP <a href="http://www.php.net/define" target="_blank" rel="noreferrer">define()</a> function in the following manner:</p>

<p><pre>define(\'TEXT_MAIN\', \'<span style="background-color: #FFFF99;">This text can be edited. It\\\'s really easy to do!</span>\');</pre></p>

<p>The highlighted text can be edited. As this definition is using single quotes to contain the text, any single quotes within the text definition must be escaped with a backslash (eg, It\\\'s).</p>');

define('TEXT_FILE_DOES_NOT_EXIST', 'File does not exist.');

define('ERROR_FILE_NOT_WRITEABLE', '<strong>Error:</strong> I can not write to this file. Please set the right user permissions on: %s');
