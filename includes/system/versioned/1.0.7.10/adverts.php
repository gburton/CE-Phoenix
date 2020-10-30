<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class adverts {

    public static function get_grouped_adverts($advert_group) {
      $group = tep_db_prepare_input($advert_group);

      $advert_query = tep_db_query("select a.*, ai.* from advert a, advert_info ai where a.advert_group = '" . tep_db_input($group) . "' and a.advert_id = ai.advert_id and ai.languages_id = " . (int)$_SESSION['languages_id'] . " and status = 1 order by sort_order");

      $num = 1; $adverts = [];
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
    
    public static function advert_get_html_text($advert_id, $language_id) {
      $advert_text_query = tep_db_query("SELECT advert_html_text FROM advert_info WHERE advert_id = " . (int)$advert_id . " AND languages_id = " . (int)$language_id);
      $advert_text = tep_db_fetch_array($advert_text_query);

      return $advert_text['advert_html_text'];
    }

  }
