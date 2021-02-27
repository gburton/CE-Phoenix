<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  abstract class abstract_executable_module extends abstract_module {

    protected $group;

    function __construct($filename) {
      parent::__construct();

      $this->group = basename(dirname($filename));
      if (isset($this->get_parameters()[static::CONFIG_KEY_BASE . 'CONTENT_WIDTH'])) {
        $this->description .= '<div class="alert alert-warning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';
      }
    }

    public function get_group() {
      return $this->group;
    }

    abstract public function execute();

  }
