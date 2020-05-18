<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  abstract class requirements_manager {

    protected $providers = [];
    protected $objects = [];

    private $matched_requirers = null;
    private $missing_abilities = null;

    public function get_module($field) {
      if (is_string($field)) {
        return $this->providers[$field];
      }

      if ($field instanceof abstract_module) {
        return $field;
      }

      return null;
    }

    public function act_on($field, $method, &$details = null) {
      $successful = true;
      $purveyor = $this->get_module($field);
      if (method_exists($purveyor, $method)) {
        $successful = $purveyor->$method($details);
      }

      return $successful;
    }

    public function process($requirements = []) {
      if ([] === $requirements) {
        $purveyors = $this->objects;
      } else {
        $purveyors = array_filter(array_map([$this, 'get_module'], $requirements));
      }

      usort($purveyors, function ($a, $b) {
        if (count(array_intersect(get_class($a)::PROVIDES, get_class($b)::REQUIRES)) > 0) {
          return -1;
        }

        if (count(array_intersect(get_class($b)::PROVIDES, get_class($a)::REQUIRES)) > 0) {
          return 1;
        }

        return strcmp($a->code, $b->code);
      });

      $successful = true;
      $details = [];
      foreach ($purveyors as $purveyor) {
        $successful = $successful && $this->act_on($purveyor, 'process', $details);
      }

      if (!$successful) {
        tep_block_form_processing();
      }

      return $details;
    }

    public function build_write($customer_details = [], $table = null) {
      $db_tables = [];
      foreach ($this->objects as $object) {
        if (method_exists($object, 'build_db_values')) {
          $object->build_db_values($db_tables, $customer_details, $table);
        }
      }

      return $db_tables;
    }

    public function build_db_table_values($criteria, $table = null) {
      $db_tables = [];
      foreach ($criteria as $field => $value) {
        if (isset($this->providers[$field]) && method_exists($this->providers[$field], 'build_db_values')) {
          $this->providers[$field]->build_db_values($db_tables, $criteria, $table);
        }
      }

      return $db_tables;
    }

    public function build_db_tables($requests, $table = null) {
      $db_tables = [];
      foreach ($requests as $request) {
        if (isset($this->providers[$request]) && method_exists($this->providers[$request], 'build_db_aliases')) {
          $this->providers[$request]->build_db_aliases($db_tables, $table);
        }
      }

      return $db_tables;
    }

    public function generate($details, $requirements) {
      foreach ($requirements as $requirement) {
        $this->providers[$requirement]->get($requirement, $details);
      }
    }

    public function list_all_capabilities() {
      $capabilities = [];

      foreach ($this->objects as $module) {
        $capabilities = array_merge($capabilities, get_class($module)::PROVIDES);
      }

      return array_unique($capabilities);
    }

    public function has($abilities) {
      $this->missing_abilities = [];
      foreach ((array)$abilities as $ability) {
        if (empty($this->providers[$ability])) {
          $this->missing_abilities[] = $ability;
        }
      }

      return [] === $this->missing_abilities;
    }

    public function find_providers($requirement, $exclude = '') {
      foreach ($this->objects as $object) {
        if (!empty($exclude) && $object instanceof $exclude) {
          continue;
        }

        if (in_array($requirement, $object::PROVIDES)) {
          unset($this->matched_requirers[$requirement]);
          return;
        }
      }
    }

    public function find_requirers($requirement, $exclude = '') {
      foreach ($this->objects as $object) {
        if (!empty($exclude) && $object instanceof $exclude) {
          continue;
        }

        if (in_array($requirement, $object::REQUIRES)) {
          tep_guarantee_subarray($this->matched_requirers, $requirement);
          $this->matched_requirers[$requirement][] = get_class($object);
        }
      }
    }

    public function has_requirements($requirements, $exclude = null) {
      $this->matched_requirers = [];
      foreach ($requirements as $requirement) {
        $this->find_requirers($requirement);
        $this->find_providers($requirement, $exclude);
      }

      return [] === $this->matched_requirers ? false : $this->matched_requirers;
    }

    public function get_last_matched_requirers() {
      return $this->matched_requirers;
    }

    public function get_last_missing_abilities() {
      return $this->missing_abilities;
    }

  }
