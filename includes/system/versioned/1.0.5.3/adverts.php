<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class adverts {

    public $adverts;

    public function __construct($advert_group = '') {
      $adverts = [];

      if (tep_not_null($advert_group)) {
        $this->get_grouped_adverts($advert_group);
      }
    }

    public function get_grouped_adverts($advert_group) {
      $group = tep_db_prepare_input($advert_group);

      $advert_query = tep_db_query("SELECT * FROM advert WHERE advert_group = '" . tep_db_input($group) . "' and status = 1 order by sort_order");
      
      $num = 1;
      while ($advert = tep_db_fetch_array($advert_query)) {
        $adverts[$num] =  $advert;
        
        $num++;
      }
      
      return $adverts;
    }
    
    public static function advert_pull_down_groups($advert_id, $key = '') {
      $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

      $groups_array = [['id' => '0', 'text' => TEXT_DEFAULT]];
      $groups_query = tep_db_query("select DISTINCT advert_group from advert order by advert_group");
      while ($groups = tep_db_fetch_array($groups_query)) {
        $groups_array[] = ['id' => $groups['advert_group'], 'text' => $groups['advert_group']];
      }

      return tep_draw_pull_down_menu($name, $groups_array, $advert_id);
    }

    public static function advert_get_group($advert_id) {
      return $advert_id;
    }
  

  }
