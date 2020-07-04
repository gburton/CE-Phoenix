<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  abstract class abstract_shipping_module extends abstract_zoneable_module {

    public $tax_class;
    protected $icon = '';
    public $quotes;
    protected $country;

    public function __construct() {
      parent::__construct();
      $this->tax_class = $this->base_constant('TAX_CLASS') ?? 0;
    }

    public function quote_common() {
      global $order;

      if ($this->tax_class > 0) {
        $this->quotes['tax'] = tep_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
      }
      
      if (tep_not_null($this->icon) && ('True' === ($this->base_constant('DISPLAY_ICON') ?? 'True'))) {
        $this->quotes['icon'] = tep_image($this->icon, htmlspecialchars($this->title));
      }
    }

    public function calculate_handling() {
      return ($this->base_constant('HANDLING') ?? 0);
    }

  }

