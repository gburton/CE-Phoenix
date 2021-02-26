<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License

  USAGE
  By default, the module comes with support for 1 zone.  This can be
  easily changed by editing the line below in the zones constructor
  that defines static::ZONE_COUNT.

  Next, you will want to activate the module by going to the Admin screen,
  clicking on Modules, then clicking on Shipping.  A list of all shipping
  modules should appear.  Click on the green dot next to the one labeled
  zones.php.  A list of settings will appear to the right.  Click on the
  Edit button.

  PLEASE NOTE THAT YOU WILL LOSE YOUR CURRENT SHIPPING RATES AND OTHER
  SETTINGS IF YOU TURN OFF THIS SHIPPING METHOD.  Make sure you keep a
  backup of your shipping settings somewhere at all times.

  If you want an additional handling charge applied to orders that use this
  method, set the Handling Fee field.

  Next, you will need to define which countries are in each zone.  Determining
  this might take some time and effort.  You should group a set of countries
  that has similar shipping charges for the same weight.  For instance, when
  shipping from the US, the countries of Japan, Australia, New Zealand, and
  Singapore have similar shipping rates.  As an example, one of my customers
  is using this set of zones:
    1: USA
    2: Canada
    3: Austria, Belgium, Great Britain, France, Germany, Greenland, Iceland,
       Ireland, Italy, Norway, Holland/Netherlands, Denmark, Poland, Spain,
       Sweden, Switzerland, Finland, Portugal, Israel, Greece
    4: Japan, Australia, New Zealand, Singapore
    5: Taiwan, China, Hong Kong

  When you enter these country lists, enter them into the Zone X Countries
  fields, where "X" is the number of the zone.  They should be entered as
  two character ISO country codes in all capital letters.  They should be
  separated by semi-colons with no spaces or other punctuation. For example:
    1: US
    2: CA
    3: AT;BE;GB;FR;DE;GL;IS;IE;IT;NO;NL;DK;PL;ES;SE;CH;FI;PT;IL;GR
    4: JP;AU;NZ;SG
    5: TW;CN;HK

  Now you need to set up the shipping rate tables for each zone.  Again,
  some time and effort will go into setting the appropriate rates.  You
  will define a set of weight ranges and the shipping price for each
  range.  For instance, you might want an order than weighs more than 0
  and less than or equal to 3 to cost 5.50 to ship to a certain zone.
  This would be defined by this:  3:5.5

  You should combine a bunch of these rates together in a comma delimited
  list and enter them into the "Zone X Shipping Table" fields where "X"
  is the zone number.  For example, this might be used for Zone 1:
    1:3.5,2:3.95,3:5.2,4:6.45,5:7.7,6:10.4,7:11.85, 8:13.3,9:14.75,10:16.2,11:17.65,
    12:19.1,13:20.55,14:22,15:23.45

  The above example includes weights over 0 and up to 15.  Note that
  units are not specified in this explanation since they should be
  specific to your locale.

  CAVEATS
  At this time, it does not deal with weights that are above the highest amount
  defined.  This will probably be the next area to be improved with the
  module.  For now, you could have one last very high range with a very
  high shipping rate to discourage orders of that magnitude.  For
  instance:  999:1000

  If you want to be able to ship to any country in the world, you will
  need to enter every country code into the Country fields. For most
  shops, you will not want to enter every country.  This is often
  because of too much fraud from certain places. If a country is not
  listed or if the shipping weight is larger than the largest amount
  set in the table, then the module will add a $0.00 shipping charge
  and will indicate that shipping is not available to that destination.
  PLEASE NOTE THAT THE ORDER CAN STILL BE COMPLETED AND PROCESSED!

  It appears that the osC shipping system automatically rounds the
  shipping weight up to the nearest whole unit.  This makes it more
  difficult to design precise shipping tables.  If you want to, you
  can modify this module to duplicate the weight calculation without
  the rounding.

*/

  class zones extends abstract_shipping_module {

    const CONFIG_KEY_BASE = 'MODULE_SHIPPING_ZONES_';

// CUSTOMIZE THIS SETTING FOR THE NUMBER OF ZONES NEEDED
    const ZONE_COUNT = 1;

    protected $destination_zone = false;

    public function update_status_by($address) {
      if (!$this->enabled || (false !== $this->destination_zone) || !isset($address['country']['iso_code_2'])) {
        return;
      }

      for ($i = 1; $i <= static::ZONE_COUNT; $i++) {
        if (in_array($address['country']['iso_code_2'], explode(';', $this->base_constant("COUNTRIES_$i")))) {
          $this->destination_zone = $i;
          return;
        }
      }

      $this->enabled = false;
    }

    public function quote($method = '') {
      global $order, $shipping_weight, $shipping_num_boxes;
      $this->quotes = [
        'id' => $this->code,
        'module' => MODULE_SHIPPING_ZONES_TEXT_TITLE,
        'methods' => [],
      ];

      if (false !== $this->destination_zone) {
        $zones_table = preg_split('{[:,]}' , $this->base_constant("COST_{$this->destination_zone}"));
        for ($i = 0, $size = count($zones_table); $i < $size; $i += 2) {
          if ($shipping_weight <= $zones_table[$i]) {
            $this->quotes['methods'][] = [
              'id' => $this->code,
              'title' => sprintf(MODULE_SHIPPING_ZONES_TEXT_WAY,
                $order->delivery['country']['iso_code_2'],
                $shipping_weight),
              'cost' => ($zones_table[$i+1] * $GLOBALS['shipping_num_boxes'])
                      + $this->base_constant("HANDLING_{$this->destination_zone}"),
            ];
            break;
          }
        }

        if (!isset($this->quotes['methods'][0])) {
          error_log(sprintf('Weight [%d] larger than maximum in table [%s] for [%s].',
            $shipping_weight,
            $this->base_constant("COST_$dest_zone"),
            $order->delivery['country']['iso_code_2']));
        }
      }

      $this->quote_common();

      return $this->quotes;
    }

    protected function get_parameters() {
      $parameters = [
        $this->config_key_base . 'STATUS' => [
          'title' => 'Enable Zones Method',
          'value' => 'True',
          'desc' => 'Do you want to offer zone rate shipping?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        $this->config_key_base . 'TAX_CLASS' => [
          'title' => 'Tax Class',
          'value' => '0',
          'desc' => 'Use the following tax class on the shipping fee.',
          'use_func' => 'tep_get_tax_class_title',
          'set_func' => 'tep_cfg_pull_down_tax_classes(',
        ],
        $this->config_key_base . 'SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display.',
        ],
      ];

      for ($i = 1; $i <= static::ZONE_COUNT; $i++) {
        $parameters = array_merge($parameters, [
          "{$this->config_key_base}COUNTRIES_$i" => [
            'title' => "Zone $i Countries",
            'value' => (($i == 1) ? 'US;CA' : ''),
            'desc' => "Semi-colon separated list of two character ISO country codes that are part of Zone $i.",
          ],
          "{$this->config_key_base}COST_$i" => [
            'title' => "Zone $i Shipping Table",
            'value' => '3:8.50,7:10.50,99:20.00',
            'desc' => <<<"EOT"
Shipping rates to Zone $i destinations based on a group of maximum order weights.
Example: 3:8.50,7:10.50,...
Weights less than or equal to 3 would cost 8.50 for Zone $i destinations.
EOT
          ],
          "{$this->config_key_base}HANDLING_$i" => [
            'title' => "Zone $i Handling Fee",
            'value' => '0',
            'desc' => 'Handling Fee for this shipping zone',
          ],
        ]);
      }

      return $parameters;
    }

  }
