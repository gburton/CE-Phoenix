<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  abstract class abstract_module {

    const CONFIG_KEY_BASE = self::CONFIG_KEY_BASE;

    public $code;
    public $title;
    public $description;
    public $enabled = false;
    protected $_check;
    protected $config_key_base;
    protected $status_key;
    public $sort_order;

    protected static function get_constant($constant_name) {
      return defined($constant_name) ? constant($constant_name) : null;
    }

    public function base_constant($suffix) {
      return $this->get_constant($this->config_key_base . "$suffix");
    }

    public function __construct() {
      if (is_null($this->config_key_base)) {
        $this->config_key_base = static::CONFIG_KEY_BASE;
      }

      $this->code = get_class($this);
      $this->title = self::get_constant(static::CONFIG_KEY_BASE . 'TEXT_TITLE')
                  ?? self::get_constant(static::CONFIG_KEY_BASE . 'TITLE');
      $this->description = self::get_constant(static::CONFIG_KEY_BASE . 'TEXT_DESCRIPTION')
                        ?? self::get_constant(static::CONFIG_KEY_BASE . 'DESCRIPTION');

      $this->status_key = $this->config_key_base . 'STATUS';
      if (defined($this->status_key)) {
        $this->enabled = ('True' === constant($this->status_key));
      }

      $this->sort_order = $this->base_constant('SORT_ORDER') ?? 0;
    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("SELECT configuration_value FROM configuration WHERE configuration_key = '" . $this->status_key . "'");
        $this->_check = tep_db_num_rows($check_query);
      }

      return $this->_check;
    }

    protected function _install($parameters) {
      $sort_order = 1;
      foreach ($parameters as $key => $data) {
        $sql_data = [
          'configuration_title' => $data['title'],
          'configuration_key' => $key,
          'configuration_value' => ($data['value'] ?? ''),
          'configuration_description' => $data['desc'],
          'configuration_group_id' => 6,
          'sort_order' => (int)$sort_order,
          'date_added' => 'NOW()',
        ];

        if (isset($data['set_func'])) {
          $sql_data['set_function'] = $data['set_func'];
        }

        if (isset($data['use_func'])) {
          $sql_data['use_function'] = $data['use_func'];
        }

        tep_db_perform('configuration', $sql_data);
        $sort_order++;
      }
    }

    public function install($parameter_key = null) {
      $parameters = $this->get_parameters();
      if (isset($parameter_key)) {
        if (isset($parameters[$parameter_key])) {
          $parameters = [$parameter_key => $parameters[$parameter_key]];
        } else {
          $parameters = [];
        }
      }

      self::_install($parameters);
    }

    public function remove() {
      tep_db_query("DELETE FROM configuration WHERE configuration_key IN ('" . implode("', '", $this->keys()) . "')");
    }

    public function keys() {
      $parameters = $this->get_parameters();

      if ($this->check()) {
        $missing_parameters = array_filter($parameters, function ($k) { return !defined($k); }, ARRAY_FILTER_USE_KEY);

        if ($missing_parameters) {
          self::_install($missing_parameters);
        }
      }

      return array_keys($parameters);
    }

    abstract protected function get_parameters();

    public static function list_exploded($value) {
      return nl2br(implode("\n", explode(';', $value)));
    }

  }
