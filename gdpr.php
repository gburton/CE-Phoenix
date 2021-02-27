<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  $OSCOM_Hooks->register_pipeline('loginRequired');
  $OSCOM_Hooks->call('gdpr', 'injectRedirect');

  $port_my_data = [];
  $OSCOM_Hooks->call('gdpr', 'injectData');

  require language::map_to_translation('gdpr.php');

  require $oscTemplate->map_to_template(__FILE__, 'page');

  require 'includes/application_bottom.php';
