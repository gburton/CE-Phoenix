<?php
/*
      QT Pro Version 5.4 BS
  
      pad_multiple_dropdowns.php
  
      Contribution extension to:
        osCommerce, Open Source E-Commerce Solutions
        http://www.oscommerce.com
     
      Copyright (c) 2017 Rainer Schmied
      Based on prior works released under the GNU General Public License:

        Copyright (c) 2004, 2005 Ralph Day
  
        QT Pro prior versions
          Ralph Day, October 2004
          Tom Wojcik aka TomThumb 2004/07/03 based on work by Michael Coffman aka coffman
          FREEZEHELL - 08/11/2003 freezehell@hotmail.com Copyright (c) 2003 IBWO
          Joseph Shain, January 2003
          osCommerce MS2
          Copyright (c) 2003 osCommerce
          
      Modifications made:
          11/2004 - Created
          12/2004 - Fix _draw_out_of_stock_message_js to add semicolon to end of js stock array
          03/2005 - Remove '&' for pass by reference from parameters to call of
                    _build_attributes_combinations.  Only needed on method definition and causes
                    error messages on some php versions/configurations
  
*******************************************************************************************
  
      QT Pro Product Attributes Display Plugin
      pad_multiple_dropdowns.php - Display stocked product attributes first as one dropdown for each attribute.
      Class Name: pad_multiple_dropdowns
      This class generates the HTML to display product attributes.  First, product attributes that
      stock is tracked for are displayed, each attribute in its own dropdown list.  Then attributes that
      stock is not tracked for are displayed, each attribute in its own dropdown list.
      Methods overidden or added:
        _draw_stocked_attributes            draw attributes that stock is tracked for
        _draw_out_of_stock_message_js       draw Javascript to display out of stock message for out of
                                            stock attribute combinations
*/
  require_once('includes/classes/pad_base.php');

  class pad_multiple_dropdowns extends pad_base {


/*
    Method: _draw_stocked_attributes
    draw dropdown lists for attributes that stock is tracked for
    Parameters:
      none
    Returns:
      string:         HTML to display dropdown lists for attributes that stock is tracked for
*/
    function _draw_stocked_attributes() {
      global $languages_id, $currencies;
      
      $out = '';
      
      $attributes = $this->_build_attributes_array(true, false);
      if (sizeof($attributes)>0) {
        for($o = 0; $o < sizeof($attributes); $o++) {
          $s = sizeof($attributes[$o]['ovals']);
          for ($a = 0; $a < $s; $a++) {
            $attribute_stock_query = tep_db_query("select products_stock_quantity from products_stock where products_id = '" . (int)$this->products_id . "' AND products_stock_attributes REGEXP '(^|,)" . (int)$attributes[$o]['oid'] . "-" . (int)$attributes[$o]['ovals'][$a]['id'] . "(,|$)' AND products_stock_quantity > 0");
            $out_of_stock = (tep_db_num_rows($attribute_stock_query) == 0);
            if(MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_ACTUAL_PRICE_PULL_DOWN == 'True') {
              $attributes[$o]['ovals'][$a]['text'] .= ' ' . $currencies->display_price( $attributes[$o]['ovals'][$a]['price'] + $this->products_original_price, tep_get_tax_rate($this->products_tax_class_id));
            }
            if ($out_of_stock && ($this->show_out_of_stock == 'True') && tep_not_null($attributes[$o]['ovals'][$a]['id']) ) {
              switch ($this->mark_out_of_stock) {
                case 'Left':   $attributes[$o]['ovals'][$a]['text'] = MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_OUT_OF_STOCK . ' - ' . $attributes[$o]['ovals'][$a]['text'];
                               break;
                case 'Right':  $attributes[$o]['ovals'][$a]['text'] .= ' - ' . MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_OUT_OF_STOCK;
                               break;
              }
            } elseif ($out_of_stock && ($this->show_out_of_stock != 'True')) {
              unset($attributes[$o]['ovals'][$a]);
            }
          }
          $out .= '<div class="col-md-3">' . "\n";
          $out .= '<strong>' . $attributes[$o]['oname'] . ':</strong>' . "\n";
          $out .= '</div>' . "\n";
          $out .= '<div class="col-md-9">' . "\n";
          // BEGIN product optionsimages support
          if ($this->options_images) {
            $out .= $this->tep_draw_pull_down_menu_options('id[' . $attributes[$o]['oid'] . ']', array_values($attributes[$o]['ovals']), $attributes[$o]['default'], ' id=option' . $attributes[$o]['oid'] . ' required aria-required="true" onchange="stkmsg(this.form);"') . '<br>';         
          } else {
            $out .= tep_draw_pull_down_menu('id[' . $attributes[$o]['oid'] . ']', array_values($attributes[$o]['ovals']), $attributes[$o]['default'], ' id=option' . $attributes[$o]['oid'] . ' required aria-required="true" onchange="stkmsg(this.form);"');         
          }
          // END product optionsimages support
          $out .= '</div>' . "\n";
        }        
        $out .= $this->_draw_out_of_stock_message_js($attributes);
        
        return $out;
      }
    }


/*
    Method: _draw_out_of_stock_message_js
    draw Javascript to display out of stock message for out of stock attribute combinations
    Parameters:
      $attributes     array   Array of attributes for the product.  Format is as returned by
                              _build_attributes_array.
    Returns:
      string:         Javascript to display out of stock message for out of stock attribute combinations
*/
    function _draw_out_of_stock_message_js($attributes) {
      $out = '';
      
      $out .= '<span id="oosmsg" class=text-danger></span>' . "\n";
  
      if (($this->out_of_stock_msgline == 'True' | $this->no_add_out_of_stock == 'True')) {
        $out .= '<SCRIPT><!--' . "\n";
        $combinations = array();
        $selected_combination = 0;
        $this->_build_attributes_combinations($attributes, false, 'None', $combinations, $selected_combination);
        
        $out .= "  function chkstk(frm) {\n";
        
        // build javascript array of in stock combinations
        $out .= "    var stk=".$this->_draw_js_stock_array($combinations).";\n";
        $out .= "    var instk=false;\n";
      
        // build javascript if statement to test level by level for existance  
        $out .= '    ';
        for ($i=0; $i<sizeof($attributes); $i++) {
          $out .= 'if (stk';
          for ($j = 0; $j <= $i; $j++) {
            $out .= "[frm['id[".$attributes[$j]['oid']."]'].value]";
          }
          $out .= ') ';
        }
        
        $out .= "instk=true;\n";
        $out .= "  return instk;\n";
        $out .= "  }\n";

        if ($this->out_of_stock_msgline == 'True') {
          // set/reset out of stock message based on selection
          $out .= "  function stkmsg(frm) {\n";
          $out .= "    var defoption = false;\n";
          for ($k=0; $k<sizeof($attributes); $k++) {
            $out .= "    var optionid = document.getElementById(\"option".$attributes[$k]['oid']."\").value;\n";
            $out .= "    if (optionid < 1)\n";
            $out .= "      defoption = true;\n";
          }
          $out .= "    var instk=chkstk(frm);\n";
          $out .= "    var span=document.getElementById(\"oosmsg\");\n";
          $out .= "    while (span.childNodes[0])\n";
          $out .= "      span.removeChild(span.childNodes[0]);\n";
          $out .= "    if (!instk && !defoption)\n";
          $out .= "      span.appendChild(document.createTextNode(\"".MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_OUT_OF_STOCK_MESSAGE."\"));\n";
          $out .= "    else\n";
          $out .= "      span.appendChild(document.createTextNode(\" \"));\n";
          $out .= "  }\n";
          //initialize out of stock message
          $out .= "  stkmsg(document.cart_quantity);\n";
        }
      
        if ($this->no_add_out_of_stock == 'True') {
          // js to not allow add to cart if selection is out of stock
          $out .= "  function chksel() {\n";
          $out .= "    var instk=chkstk(document.cart_quantity);\n";
          $out .= "    if (!instk) alert('".MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_OUT_OF_STOCK_MESSAGE."');\n";
          $out .= "    return instk;\n";
          $out .= "  }\n";
          $out .= "  document.cart_quantity.onsubmit=chksel;\n";
        }
        $out .= "//--></SCRIPT>\n";
      }
      
      return $out;
    }

////
// Output a form pull down menu for Option Images BS
    function tep_draw_pull_down_menu_options($name, $values, $default = '', $parameters = '', $required = false) {
      if ($this->options_images) {
        global $_GET, $_POST;

        $field = '<select name="' . tep_output_string($name) . '"';

        if (tep_not_null($parameters)) $field .= ' ' . $parameters;

        $field .= ' class="form-control selectpicker">';

        if (empty($default) && ( (isset($_GET[$name]) && is_string($_GET[$name])) || (isset($_POST[$name]) && is_string($_POST[$name])) ) ) {
          if (isset($_GET[$name]) && is_string($_GET[$name])) {
            $default = stripslashes($_GET[$name]);
          } elseif (isset($_POST[$name]) && is_string($_POST[$name])) {
            $default = stripslashes($_POST[$name]);
          }
        }

        for ($i=0, $n=sizeof($values); $i<$n; $i++) {
          $field .= '<option value="' . tep_output_string($values[$i]['id']) . '"';
          if ($default == $values[$i]['id']) {
            $field .= ' selected="selected"';
          }

          $option_name = tep_output_string($values[$i]['text'], array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;'));
	     
          if( tep_not_null( $values[$i]['image'] ) ) {
            $field .= ' data-content=\'' . tep_image('images/options/' . $values[$i]['image'], $option_name, '40', '40', null, 'false') . ' ' . $option_name . '\'';
          }
	  
          $field .= '>' . $option_name . '</option>';
        }

        $field .= '</select>';

        if ($required == true) $field .= TEXT_FIELD_REQUIRED;

        return $field;
      }  
    }

  }
?>
