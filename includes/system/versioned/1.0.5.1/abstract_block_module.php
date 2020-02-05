<?php
/*
 $Id$

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2020 osCommerce

 Released under the GNU General Public License
*/

  abstract class abstract_block_module extends abstract_module {

    protected $group;
    protected $mr_auto;

    function __construct() {
      parent::__construct();

      if ( defined(static::CONFIG_KEY_BASE . 'STATUS') ) {
        switch (constant(static::CONFIG_KEY_BASE . 'CONTENT_PLACEMENT')) {
          case 'Home':
            $this->group = 'navbar_modules_home';
            break;
          case 'Left':
            $this->group = 'navbar_modules_left';
            break;
          case 'Right':
            $this->group = 'navbar_modules_right';
            break;
          case 'Left Column':
            $this->group = 'boxes_column_left';
            break;
          case 'Right Column':
            $this->group = 'boxes_column_right';
            break;
        }
      }
    }

    public function get_group() {
      return $this->group;
    }

  }
