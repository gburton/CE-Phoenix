<?php
/*
  Copyright (c) 2020, G Burton
  All rights reserved.

  Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

  1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

  2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

  3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

  class cm_navbar extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_NAVBAR_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      global $oscTemplate;

      $style_array = [];
      $style_array[] = MODULE_CONTENT_NAVBAR_STYLE_BG;
      $style_array[] = MODULE_CONTENT_NAVBAR_STYLE_FG;
      $style_array[] = MODULE_CONTENT_NAVBAR_FIXED;
      $style_array[] = MODULE_CONTENT_NAVBAR_COLLAPSE;

      $navbar_style = implode(' ', $style_array);

      switch (MODULE_CONTENT_NAVBAR_FIXED) {
        case 'fixed-top':
          $custom_css = '<style>body { padding-top: ' . MODULE_CONTENT_NAVBAR_OFFSET . ' !important; }</style>';
          break;
        case 'fixed-bottom':
          $custom_css = '<style>body { padding-bottom: ' . MODULE_CONTENT_NAVBAR_OFFSET . ' !important; }</style>';
          break;
        default:
          $custom_css = null;
      }

      // workaround; padding needs to be set last
      $oscTemplate->addBlock($custom_css, 'footer_scripts');

      if ( defined('MODULE_CONTENT_NAVBAR_INSTALLED') && tep_not_null(MODULE_CONTENT_NAVBAR_INSTALLED) ) {
        $nav_array = explode(';', MODULE_CONTENT_NAVBAR_INSTALLED);

        $navbar_modules = [];

        foreach ( $nav_array as $nbm ) {
          $class = pathinfo($nbm, PATHINFO_FILENAME);

          $nav = new $class();
          if ( $nav->isEnabled() ) {
            $navbar_modules[] = $nav->getOutput();
          }
        }

        if ( [] !== $navbar_modules ) {
          $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
          include 'includes/modules/content/cm_template.php';
        }
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_NAVBAR_STATUS' => [
          'title' => 'Enable Navbar Module',
          'value' => 'True',
          'desc' => 'Should the Navbar be shown? ',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_NAVBAR_STYLE_BG' => [
          'title' => 'Background Colour Scheme',
          'value' => 'bg-light',
          'desc' => 'What background colour should the Navbar have?  See <a target="_blank" rel="noreferrer" href="https://getbootstrap.com/docs/4.5/utilities/colors/#background-color"><u>colors/#background-color</u></a>',
          'set_func' => "tep_cfg_select_option(['bg-primary', 'bg-secondary', 'bg-success', 'bg-danger', 'bg-warning', 'bg-info', 'bg-light', 'bg-dark', 'bg-white'], ",
        ],
        'MODULE_CONTENT_NAVBAR_STYLE_FG' => [
          'title' => 'Link Colour Scheme',
          'value' => 'navbar-light',
          'desc' => 'What foreground colour should the Navbar have?  See <a target="_blank" rel="noreferrer" href="https://getbootstrap.com/docs/4.5/components/navbar/#color-schemes"><u>navbar/#color-schemes</u></a>',
          'set_func' => "tep_cfg_select_option(['navbar-dark', 'navbar-light'], ",
        ],
        'MODULE_CONTENT_NAVBAR_FIXED' => [
          'title' => 'Placement',
          'value' => 'default',
          'desc' => 'Should the Navbar be Fixed/Sticky/Default behaviour? See <a target="_blank" rel="noreferrer" href="https://getbootstrap.com/docs/4.5/components/navbar/#placement"><u>navbar/#placement</u></a>',
          'set_func' => "tep_cfg_select_option(['fixed-top', 'fixed-bottom', 'sticky-top', 'default'], ",
        ],
        'MODULE_CONTENT_NAVBAR_OFFSET' => [
          'title' => 'Placement Offset',
          'value' => '4rem',
          'desc' => 'Offset if using fixed-* Placement.',
        ],
        'MODULE_CONTENT_NAVBAR_COLLAPSE' => [
          'title' => 'Collapse',
          'value' => 'navbar-expand-sm',
          'desc' => 'When should the Navbar Show? See <a target="_blank" rel="noreferrer" href="https://getbootstrap.com/docs/4.5/components/navbar/#how-it-works"><u>navbar/#how-it-works</u></a>',
          'set_func' => "tep_cfg_select_option(['navbar-expand', 'navbar-expand-sm', 'navbar-expand-md', 'navbar-expand-lg', 'navbar-expand-xl'], ",
        ],
        'MODULE_CONTENT_NAVBAR_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '10',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
