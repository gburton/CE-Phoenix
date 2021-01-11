<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2020 osCommerce; http://www.oscommerce.com
 * @license GNU General Public License; http://www.oscommerce.com/gpllicense.txt
 */

  abstract class displayable_tree_accessor {

    protected $tree;

    protected $max_level = 0;
    protected $root_start_string = '';
    protected $root_end_string = '';
    protected $parent_start_string = '';
    protected $parent_end_string = '';
    protected $parent_group_start_string = '<ul>';
    protected $parent_group_end_string = '</ul>';
    protected $parent_group_apply_to_root = false;
    protected $child_start_string = '<li>';
    protected $child_end_string = '</li>';
    protected $breadcrumb_separator = '_';
    protected $breadcrumb_usage = true;
    protected $spacer_string = '';
    protected $spacer_multiplier = 1;
    protected $follow_path = false;
    protected $path_array = [];
    protected $path_start_string = '';
    protected $path_end_string = '';

    public function __construct($tree) {
      $this->tree = $tree;
    }

    public function setMaximumLevel($max_level) {
      $this->max_level = $max_level;
    }

    public function setRootString($root_start_string, $root_end_string) {
      $this->root_start_string = $root_start_string;
      $this->root_end_string = $root_end_string;
    }

    public function setParentString($parent_start_string, $parent_end_string) {
      $this->parent_start_string = $parent_start_string;
      $this->parent_end_string = $parent_end_string;
    }

    public function setParentGroupString($parent_group_start_string, $parent_group_end_string, $apply_to_root = false) {
      $this->parent_group_start_string = $parent_group_start_string;
      $this->parent_group_end_string = $parent_group_end_string;
      $this->parent_group_apply_to_root = $apply_to_root;
    }

    public function setChildString($child_start_string, $child_end_string) {
      $this->child_start_string = $child_start_string;
      $this->child_end_string = $child_end_string;
    }

    public function setBreadcrumbSeparator($breadcrumb_separator) {
      $this->breadcrumb_separator = $breadcrumb_separator;
    }

    public function setBreadcrumbUsage($breadcrumb_usage) {
      $this->breadcrumb_usage = ($breadcrumb_usage === true);
    }

    public function setSpacerString($spacer_string, $spacer_multiplier = 2) {
      $this->spacer_string = $spacer_string;
      $this->spacer_multiplier = $spacer_multiplier;
    }

    public function setPath($path_string, $path_start_string = '', $path_end_string = '') {
      $this->follow_path = true;
      $this->path_array = explode($this->breadcrumb_separator, $path_string);
      $this->path_start_string = $path_start_string;
      $this->path_end_string = $path_end_string;
    }

    public function setFollowPath($follow_path) {
      $this->follow_path = ($follow_path === true);
    }

    public function setPathString($path_start_string, $path_end_string) {
      $this->path_start_string = $path_start_string;
      $this->path_end_string = $path_end_string;
    }

  }
