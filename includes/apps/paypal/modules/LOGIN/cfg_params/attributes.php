<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class OSCOM_PayPal_LOGIN_Cfg_attributes {
    public $default = ''; // set in classs constructor
    public $title;
    public $description;
    public $sort_order = 700;

    public $attributes = [
      'personal' => [
        'full_name' => 'profile',
        'date_of_birth' => 'profile',
        'age_range' => 'https://uri.paypal.com/services/paypalattributes',
        'gender' => 'profile',
      ],
      'address' => [
        'email_address' => 'email',
        'street_address' => 'address',
        'city' => 'address',
        'state' => 'address',
        'country' => 'address',
        'zip_code' => 'address',
        'phone' => 'phone',
      ],
      'account' => [
        'account_status' => 'https://uri.paypal.com/services/paypalattributes',
        'account_type' => 'https://uri.paypal.com/services/paypalattributes',
        'account_creation_date' => 'https://uri.paypal.com/services/paypalattributes',
        'time_zone' => 'profile',
        'locale' => 'profile',
        'language' => 'profile',
      ],
      'checkout' => [
        'seamless_checkout' => 'https://uri.paypal.com/services/expresscheckout',
      ],
    ];

    public $required = [
      'full_name',
      'email_address',
      'street_address',
      'city',
      'state',
      'country',
      'zip_code',
    ];

    function __construct() {
      global $OSCOM_PayPal;

      $this->default = implode(';', $this->getAttributes());

      $this->title = $OSCOM_PayPal->getDef('cfg_login_attributes_title');
      $this->description = $OSCOM_PayPal->getDef('cfg_login_attributes_desc');
    }

    function getSetField() {
      global $OSCOM_PayPal;

      $values_array = explode(';', OSCOM_APP_PAYPAL_LOGIN_ATTRIBUTES);

      $input = null;

      foreach ( $this->attributes as $group => $attributes ) {
        $input .= '<h6 class="mt-2 mb-0">' . $OSCOM_PayPal->getDef('cfg_login_attributes_group_' . $group) . '</h6>';

        foreach ( $attributes as $attribute => $scope ) {
          if ( in_array($attribute, $this->required) ) {
            $input .= '<div class="custom-control custom-radio custom-control-inline">';
              $input .= '<input type="radio" class="custom-control-input" id="ppLogInAttributesSelection' . ucfirst($attribute) . '" name="ppLogInAttributesTmp' . ucfirst($attribute) . '" value="' . $attribute . '" checked="checked" />';
              $input .= '<label class="custom-control-label" for="ppLogInAttributesSelection' . ucfirst($attribute) . '">' . $OSCOM_PayPal->getDef('cfg_login_attributes_attribute_' . $attribute) . '</label>';
            $input .= '</div>';
          } else {
            $input .= '<div class="custom-control custom-checkbox custom-control-inline">';
              $input .= '<input type="checkbox" class="custom-control-input" id="ppLogInAttributesSelection' . ucfirst($attribute) . '" name="ppLogInAttributes[]" value="' . $attribute . '"' . (in_array($attribute, $values_array) ? ' checked="checked"' : '') . ' />';
              $input .= '<label class="custom-control-label" for="ppLogInAttributesSelection' . ucfirst($attribute) . '">' . $OSCOM_PayPal->getDef('cfg_login_attributes_attribute_' . $attribute) . '</label>';
            $input .= '</div>';
          }
        }
      }

      $input .= '<input type="hidden" name="attributes" value="" />';

      $result = <<<EOT
<h5>{$this->title}</h5>
<p>{$this->description}</p>

<div class="mb-3" id="attributesSelection">{$input}</div>

<script>
function ppLogInAttributesUpdateCfgValue() {
  var pp_login_attributes_selected = '';

  if ( $('input[name^="ppLogInAttributesTmp"]').length > 0 ) {
    $('input[name^="ppLogInAttributesTmp"]').each(function() {
      pp_login_attributes_selected += $(this).attr('value') + ';';
    });
  }

  if ( $('input[name="ppLogInAttributes[]"]').length > 0 ) {
    $('input[name="ppLogInAttributes[]"]:checked').each(function() {
      pp_login_attributes_selected += $(this).attr('value') + ';';
    });
  }

  if ( pp_login_attributes_selected.length > 0 ) {
    pp_login_attributes_selected = pp_login_attributes_selected.substring(0, pp_login_attributes_selected.length - 1);
  }

  $('input[name="attributes"]').val(pp_login_attributes_selected);
}

$(function() {
  ppLogInAttributesUpdateCfgValue();

  if ( $('input[name="ppLogInAttributes[]"]').length > 0 ) {
    $('input[name="ppLogInAttributes[]"]').change(function() {
      ppLogInAttributesUpdateCfgValue();
    });
  }
});
</script>
EOT;

      return $result;
    }

    function getAttributes() {
      $data = [];

      foreach ( $this->attributes as $group => $attributes ) {
        foreach ( $attributes as $attribute => $scope ) {
          $data[] = $attribute;
        }
      }

      return $data;
    }
  }
