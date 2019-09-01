<?php
/*
      QT Pro Version 5.4 BS
  
      pad_single_dropdown.php
  
      Contribution extension to:
        osCommerce, Open Source E-Commerce Solutions
        http://www.oscommerce.com
     
      Copyright (c) 2017 Rainer Schmied
      Based on prior works released under the GNU General Public License:

        Copyright (c) 2004, 2005 Ralph Day
        
        QT Pro & CPIL prior versions
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
      pad_single_dropdown.php - Display stocked product attributes as a single dropdown with entries
                                for each possible combination of attributes.
      Class Name: pad_single_dropdown
      This class generates the HTML to display product attributes.  First, product attributes that
      stock is tracked for are displayed in a single dropdown list with entries for each possible
      combination of attributes..  Then attributes that stock is not tracked for are displayed,
      each attribute in its own dropdown list.
      Methods overidden or added:
        _draw_stocked_attributes            draw attributes that stock is tracked for
        _draw_out_of_stock_message_js       draw Javascript to display out of stock message for out of
                                            stock attribute combinations
  
*/
  require_once('includes/classes/pad_base.php');

  class pad_single_dropdown extends pad_base {


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
      $combine_nostock = (MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_COMBINE_NON_STOCKED == 'True')? true : false;
      $attributes = $this->_build_attributes_array(true, $combine_nostock);
      if (sizeof($attributes) > 0) {
        $combinations = array();
        if ( MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_SHOW_PLEASE_SELECT == 'True' ) {
          // BEGIN product optionsimages support
          if ($this->options_images) {
            $combinations[] =  array('id'=> null, 'text'=> PULL_DOWN_DEFAULT, 'image' => null);
          } else {
            $combinations[] =  array('id'=> null, 'text'=> PULL_DOWN_DEFAULT);
          }
          // END product optionsimages support
        }
        $selected_combination = 0;
        $this->_build_attributes_combinations($attributes, $this->show_out_of_stock == 'True', $this->mark_out_of_stock, $combinations, $selected_combination);
        
        $combname = '';
        foreach ($attributes as $attrib) {
          $combname .= ', ' . $attrib['oname'];
        }
        $combname=substr($combname,2);
        $out .= '<div class="col-md-3">' . "\n";
        $out .= '<strong>' . $combname . ': </strong>' . "\n";
        $out .= '</div>' . "\n";
        $out .= '<div class="col-md-9">' . "\n";
        // BEGIN product optionsimages support
        if ($this->options_images) {
          $out .= $this->tep_draw_pull_down_menu_options('attrcomb', $combinations, $combinations[$selected_combination]['id'], ' id=option' . $combinations[$selected_combination]['id'] . ' required aria-required="true"') . '<br>' . "\n"; 
        } else {
          $out .= tep_draw_pull_down_menu('attrcomb', $combinations, $combinations[$selected_combination]['id'], ' id=option' . $combinations[$selected_combination]['id'] . ' required aria-required="true"') . "\n"; 
        }
        // END product optionsimages support
        $out .= '</div>' . "\n";
      }
      
      if(MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_OUT_OF_STOCK_MSGLINE == 'True')
        $out .= $this->_draw_out_of_stock_message_js($attributes);
      
      return $out;
    }


/*
    Method: _draw_out_of_stock_message_js
    draw Javascript to display out of stock popup message if an attempt is made to add an out of
    stock attribute combination to the cart
    Parameters:
      $attributes     array   Array of attributes for the product.  Format is as returned by
                              _build_attributes_array.
    Returns:
      string:         Javascript to display out of stock message for out of stock attribute combinations
*/
    function _draw_out_of_stock_message_js($attributes) {
      $out = '';
  
      if (($this->show_out_of_stock == 'True') && ($this->no_add_out_of_stock == 'True')) {
        $out .= "<SCRIPT><!--\n";
        $combinations = array();
        $selected_combination = 0;
        $this->_build_attributes_combinations($attributes, false, 'None', $combinations, $selected_combination);
        
        $out .= "  function chkstk(frm) {\n";
      
        // build javascript array of in stock combinations of the form
        // {optval1:{optval2:{optval3:1,optval3:1}, optval2:{optval3:1}}, optval1:{optval2:{optval3:1}}};
        $out .= "    var stk=" . $this->_draw_js_stock_array($combinations) . ";\n";
        $out .= "    var instk=false;\n";
      
        // build javascript to extract attribute values and check stock  
        $out .= "    if (frm.attrcomb.type=='select-one') {\n";
        $out .= "      var attrs=frm.attrcomb.value.split(',');\n";
        $out .= "    }\n";
        $out .= "    else {\n";
        $out .= "      for (i=0; i,frm.attrcomb.length; i++) {\n";
        $out .= "        if (frm.attrcomb[i].checked) {\n";
        $out .= "          var attrs=frm.attrcomb[i].value.split(',');\n";
        $out .= "          break;\n";
        $out .= "        }\n";
        $out .= "      }\n";
        $out .= "    }\n";
        $out .= "    var id=Array(" . sizeof($attributes) . ");\n";
        $out .= "    for (i=0; i<attrs.length; i++) {\n";
        $out .= "      id[i]=attrs[i].split('-')[1];\n";
        $out .= "    }\n";
        $out .= '    ';
        for ($i = 0; $i < sizeof($attributes); $i++) {
          $out .= 'if (stk';
          for ($j = 0; $j <= $i; $j++) {
            $out .= "[id[" . $j . "]]";
          }
          $out .= ') ';
        }
        
        $out .= "instk=true;\n";
        $out .= "  return instk;\n";
        $out .= "  }\n";

        if ($this->no_add_out_of_stock == 'True') {
          // js to not allow add to cart if selection is out of stock
          $out .= "  function chksel() {\n";
          $out .= "    var instk=chkstk(document.cart_quantity);\n";
          $out .= "    if (!instk) alert('" . MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_OUT_OF_STOCK_MESSAGE . "');\n";
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
            $field .= ' data-content=\'';
            for ($j=0, $k=sizeof($values[$i]['image']); $j<$k; $j++) {
              if ( tep_not_null($values[$i]['image'][$j]) ) {
                $field .= tep_image('images/options/' . $values[$i]['image'][$j], $option_name, '40', '40', null, 'false') . '&nbsp;';
              }
            }
            $field .= $option_name . '\'';
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
