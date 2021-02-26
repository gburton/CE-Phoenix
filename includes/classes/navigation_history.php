<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class navigationHistory {

    public $path, $snapshot;

    public function __construct() {
      $this->reset();
    }

    public function reset() {
      $this->path = [];
      $this->snapshot = [];
    }

    public function add_current_page() {
      global $cPath;

      for ($i = 0, $n = count($this->path); $i < $n; $i++) {
        if ($this->path[$i]['page'] == $GLOBALS['PHP_SELF']) {
          if (!isset($cPath)) {
            array_splice($this->path, ($i));
            break;
          }

          if (!isset($this->path[$i]['get']['cPath'])) {
            continue;
          }

          if ($this->path[$i]['get']['cPath'] == $cPath) {
            array_splice($this->path, ($i+1));
            return;
          }

          $old_cPath = explode('_', $this->path[$i]['get']['cPath']);
          $new_cPath = explode('_', $cPath);

          for ($j = 0, $n2 = count($old_cPath); $j < $n2; $j++) {
            if ($old_cPath[$j] != $new_cPath[$j]) {
              array_splice($this->path, ($i));
              break 2;
            }
          }
        }
      }

      $this->path[] = [
        'page' => $GLOBALS['PHP_SELF'],
        'get' => $this->filter_parameters($_GET),
        'post' => $this->filter_parameters($_POST),
      ];
    }

    public function remove_current_page() {
      $last_entry_position = count($this->path) - 1;
      if ($this->path[$last_entry_position]['page'] == $GLOBALS['PHP_SELF']) {
        unset($this->path[$last_entry_position]);
      }
    }

    public function set_snapshot($page = null) {
      if (isset($page['page'])) {
        $this->snapshot = [
          'page' => $page['page'],
          'get' => $this->filter_parameters($page['get'] ?? []),
          'post' => $this->filter_parameters($page['post'] ?? []),
        ];
      } else {
        $this->snapshot = [
          'page' => $GLOBALS['PHP_SELF'],
          'get' => $this->filter_parameters($_GET),
          'post' => $this->filter_parameters($_POST),
        ];
      }
    }

    public function clear_snapshot() {
      $this->snapshot = [];
    }

    public function set_path_as_snapshot($history = 0) {
      $pos = (count($this->path) - 1 - $history);
      $this->snapshot = [
        'page' => $this->path[$pos]['page'],
        'get' => $this->path[$pos]['get'],
        'post' => $this->path[$pos]['post'],
      ];
    }

    public function pop_snapshot_as_link() {
      if (empty($this->snapshot)) {
        return tep_href_link('index.php');
      }

      $origin_href = tep_href_link(
        $this->snapshot['page'],
        tep_array_to_string($this->snapshot['get'], [session_name()]));

      $this->clear_snapshot();
      return $origin_href;
    }

    public function debug() {
      foreach ($this->path as $step) {
        echo $step['page'] . '?';
        foreach ($step['get'] as $key => $value) {
          echo $key . '=' . $value . '&';
        }

        if (count($step['post']) > 0) {
          echo '<br>';
          foreach ($step['post'] as $key => $value) {
            echo '&nbsp;&nbsp;<strong>' . $key . '=' . $value . '</strong><br>';
          }
        }
        echo '<br>';
      }

      if (count($this->snapshot) > 0) {
        echo '<br><br>';

        echo $this->snapshot['page'] . '?' . tep_array_to_string($this->snapshot['get'], [session_name()]) . '<br>';
      }
    }

    protected function filter_parameters($parameters) {
      $clean = [];

      foreach (($parameters ?? []) as $key => $value) {
        if (!strpos($key, '_nh-dns')) {
          $clean[$key] = $value;
        }
      }

      return $clean;
    }

  }
