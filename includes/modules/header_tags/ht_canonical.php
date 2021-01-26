<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  class ht_canonical extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_HEADER_TAGS_CANONICAL_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    public function build_link() {
      global $PHP_SELF, $cPath, $current_category_id;

      switch (basename($PHP_SELF)) {
        case 'index.php':
          if (isset($cPath) && !Text::is_empty($cPath) && ($current_category_id > 0) && ($GLOBALS['category_depth'] != 'top')) {
            $canonical = Guarantor::ensure_global('category_tree')->find_path($current_category_id);

            return tep_href_link('index.php', 'view=all&cPath=' . $canonical, 'SSL', false);
          } elseif (isset($_GET['manufacturers_id']) && !Text::is_empty($_GET['manufacturers_id'])) {
            return tep_href_link('index.php', 'view=all&manufacturers_id=' . (int)$_GET['manufacturers_id'], 'SSL', false);
          }

          return tep_href_link('index.php', '', 'SSL', false);

        case 'product_info.php':
          return tep_href_link('product_info.php', 'products_id=' . (int)$_GET['products_id'], 'SSL', false);

        case 'products_new.php':
        case 'specials.php':
          return tep_href_link($PHP_SELF, 'view=all', 'SSL', false);

        default:
          return tep_href_link($PHP_SELF, '', 'SSL', false);
      }
    }

    public function execute() {
      $GLOBALS['oscTemplate']->addBlock('<link rel="canonical" href="' . $this->build_link() . '" />' . PHP_EOL, $this->group);
    }

    protected function get_parameters() {
      return [
        'MODULE_HEADER_TAGS_CANONICAL_STATUS' => [
          'title' => 'Enable Canonical Module',
          'value' => 'True',
          'desc' => 'Do you want to enable the Canonical module?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_HEADER_TAGS_CANONICAL_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
