<?php
/*
      QT Pro Version 6.2 BS
  
      pad_base.php
  
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
          12/2004 - Fix _draw_js_stock_array to prevent error when all attribute combinations are
                    out of stock.
  
*******************************************************************************************
  
      QT Pro Product Attributes Display Plugin
  
      pad_base.php - Base Class
  
      Class Name: pad_base
  
      This base class, although functional, is not intended to be installed and used
      directly.  It is extended by other classes to provide different display options
      for product attributes on the product information page (product_info.php).
  
  
      Methods:
  
        pad_base                            constructor
        _SetConfigurationProperties         set local properties from DB config constants
        draw                                draw the product attributes
        _draw_table_start                   draw start of the table to enclose the attributes display
        _draw_stocked_attributes            draw attributes that stock is tracked for
        _draw_nonstocked_attributes         draw attributes that stock is not tracked for
        _draw_table_end                     draw end of the table to enclose the attributes display
        _draw_js_stock_array                draw a Javascript array of in stock attribute combinations
        _build_attributes_array             build an array of the attributes for the product
        _build_attributes_combinations      build an array of the attribute combinations for the product
  
      Properties:
  
        products_id                         the product id for attribute display
        products_tax_class_id               the products tax class id
        show_out_of_stock                   show out of stock attributes flag
        mark_out_of_stock                   mark out of stock attributes flag
        out_of_stock_msgline                show out of stock message line flag
        no_add_out_of_stock                 prevent add to cart of out of stock attributes combinations
        options_images                      support for options images
  
*/
  class pad_base {
    var $products_id;
    var $products_tax_class_id;
    var $show_out_of_stock;
    var $mark_out_of_stock;
    var $out_of_stock_msgline;
    var $no_add_out_of_stock;
    var $options_images;


/*
    Method: pad_base
    Class constructor
    Parameters:
      $products_id      integer     The product id of the product attributes are to be displayed for
    Returns:
      nothing
*/
    function __construct($products_id=0) {
      $this->products_id = $products_id;
      if ($this->products_id != 0) {
        $tax_class_query = tep_db_query("SELECT p.products_tax_class_id, IF(s.status, s.specials_new_products_price, p.products_price) as products_price FROM products p left join specials s on p.products_id = s.products_id WHERE p.products_id = '" . (int)$products_id . "'");
        $tax_class_array = tep_db_fetch_array($tax_class_query);
        $this->products_tax_class_id = $tax_class_array['products_tax_class_id'];
        $this->products_original_price = $tax_class_array['products_price'];
      }
      $this->_SetConfigurationProperties('MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_');
    }


/*
    Method: _SetConfigurationProperties
    Set local configuration properties from osCommerce configuration DB constants
    Parameters:
      $prefix      sting     Prefix for the osCommerce DB constants
    Returns:
      nothing
*/
    function _SetConfigurationProperties($prefix) {
      $this->show_out_of_stock    = constant($prefix . 'SHOW_OUT_OF_STOCK');
      $this->mark_out_of_stock    = constant($prefix . 'MARK_OUT_OF_STOCK');
      $this->out_of_stock_msgline = constant($prefix . 'OUT_OF_STOCK_MSGLINE');
      $this->no_add_out_of_stock  = constant($prefix . 'NO_ADD_OUT_OF_STOCK');
      // BEGIN options images support
      if ( defined('MODULE_HEADER_TAGS_BOOTSTRAP_SELECT_STATUS') && MODULE_HEADER_TAGS_BOOTSTRAP_SELECT_STATUS == 'True' )
        $this->options_images = true;
    }
    
/*
    Method: draw
    Draws the product attributes.  This is the only method other than the constructor that is
    intended to be called by a user of this class.
    Attributes that stock is tracked for are grouped first and drawn with one dropdown list per
    attribute.  All attributes are drawn even if no stock is available for the attribute and no 
    indication is given that the attribute is out of stock.
    Attributes that stock is not tracked for are then drawn with one dropdown list per
    attribute.
    Parameters:
      none
    Returns:
      string:       HTML for displaying the product attributes
*/
    function draw() {
      $out=$this->_draw_table_start();
      $out.=$this->_draw_stocked_attributes();
      $out.=$this->_draw_nonstocked_attributes();
      $out.=$this->_draw_table_end();
      return $out;
    }
    
/*
    Method: _draw_table_start
    Draws the start of a table to wrap the product attributes display.
    Intended for class internal use only.
    Parameters:
      none
    Returns:
      string:       HTML for start of table
*/
    function _draw_table_start() {
      $out ='           ';
      return $out;
    }
    
/*
    Method: _draw_stocked_attributes
    Draws the product attributes that stock is tracked for.
    Intended for class internal use only.
    Attributes that stock is tracked for are drawn with one dropdown list per attribute.
    All attributes are drawn even if no stock is available for the attribute and no 
    indication is given that the attribute is out of stock.
    Parameters:
      none
    Returns:
      string:       HTML for displaying the product attributes that stock is tracked for
*/
    function _draw_stocked_attributes() {
      global $currencies;
      $out = '';
      $attributes = $this->_build_attributes_array(true, false);
      if (sizeof($attributes)>0) {
        foreach ($attributes as $stocked) {
          $out .= '<div class="col-md-3">' . "\n";
          $out .= ' <strong>' . $stocked['oname'] . $stocked['price'] . ': </strong>' . "\n";
          $out .= '</div>' . "\n";
          $out .= '<div class="col-md-9">' . "\n";
          // BEGIN product optionsimages support
          if ($this->options_images) {
            $out .=   $this->tep_draw_pull_down_menu_options('id[' . $stocked['oid'] . ']', array_values($stocked['ovals']), $stocked['default'], 'required aria-required="true"') . '<br>' . "\n";
          } else {
            $out .=   tep_draw_pull_down_menu('id[' . $stocked['oid'] . ']', array_values($stocked['ovals']), $stocked['default'], 'required aria-required="true"') . "\n";
          }
          // END product optionsimages support
          $out .= '</div>' . "\n";
        }
      }
      return $out;
    }
    
/*
    Method: _draw_nonstocked_attributes
    Draws the product attributes that stock is not tracked for.
    Intended for class internal use only.
    Attributes that stock is not tracked for are drawn with one dropdown list per attribute.
    Parameters:
      none
    Returns:
      string:       HTML for displaying the product attributes that stock is not tracked for
*/
    function _draw_nonstocked_attributes() {
      global $currencies;
      $out = '';
      if (MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_COMBINE_NON_STOCKED == 'True' && 
          (MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_PLUGIN == 'single_dropdown' || MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_PLUGIN == 'single_radioset')) {
        $show_nostock = false;
      } else {
        $show_nostock = true;
      }
      $nonstocked_attributes = $this->_build_attributes_array(false, $show_nostock);
      if (tep_not_null($nonstocked_attributes)) {
        foreach ($nonstocked_attributes as $nonstocked) {
          $out .= '<div class="col-md-3">' . "\n";
          $out .= ' <strong>' . $nonstocked['oname'] . $nonstocked['price'] . ': </strong>' . "\n";
          $out .= '</div>' . "\n";
          $out .= '<div class="col-md-9">' . "\n";
          // BEGIN product optionsimages support
          if ($this->options_images) {
            $out .=   $this->tep_draw_pull_down_menu_options('id[' . $nonstocked['oid'] . ']', array_values($nonstocked['ovals']), $nonstocked['default'], 'required aria-required="true"') . '<br>' . "\n";
          } else {
            $out .=   tep_draw_pull_down_menu('id[' . $nonstocked['oid'] . ']', array_values($nonstocked['ovals']), $nonstocked['default'], 'required aria-required="true"') . "\n";
          }
          // END product optionsimages support
          $out .= '</div>' . "\n";
        }
        return $out;
      }
    }
    
/*
    Method: _draw_table_end
  
    Draws the end of a table to wrap the product attributes display.
    Intended for class internal use only.
    Parameters:
      none
    Returns:
      string:       HTML for end of table
*/
    function _draw_table_end() {
      return '           ';
    }
    
/*
    Method: _build_attributes_array
    Build an array of the attributes for the product
    Parameters:
      $build_stocked        boolean   Flag indicating if stocked attributes should be built.
      $build_nonstocked     boolean   Flag indicating if non-stocked attribute should be built.
    Returns:
      array:                Array of attributes for the product of the form:
                              'oid'       => integer: products_options_id
                              'oname'     => string:  products_options_name
                              'ovals'     => array:   option values for the option id of the form
                                             'id'    => integer:  products_options_values_id
                                             'text'  => string:   products_options_values_name
                              'default'   => integer: products_options_values_id that the product id
                                                      contains for this option id and should be the
                                                      default selection when this attribute is drawn.
                                                      Set to zero if the product id did not contain
                                                      this option. 
  
*/
    function _build_attributes_array($build_stocked, $build_nonstocked) {
      global $languages_id;
      global $currencies;
      global $cart;
    
      if ( !($build_stocked | $build_nonstocked) ) return null;
      
      if ($build_stocked && $build_nonstocked) {
        $stocked_where='';
      } elseif ($build_stocked) {
        $stocked_where="and popt.products_options_track_stock = '1'";
      } elseif ($build_nonstocked) {
        $stocked_where="and popt.products_options_track_stock = '0'";
      }
      // product options sort order support
      if ( defined('MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_USE_OPT_ATTR_SORT_ORDER') && MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_USE_OPT_ATTR_SORT_ORDER == 'True' ) {
        $products_options_sort = 'popt.products_options_sort_order';
      } else {
        $products_options_sort = 'popt.products_options_name';
      }

      $products_options_name_query = tep_db_query("select distinct popt.products_options_id, popt.products_options_name, popt.products_options_track_stock from products_options popt, products_attributes patrib where patrib.products_id='" . (int)$this->products_id . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . (int)$languages_id . "' " . $stocked_where . " order by " . $products_options_sort);
      $attributes = array();
	  
      while ( $products_options_name = tep_db_fetch_array($products_options_name_query) ) {
        $products_options_array = array();
        // product options sort order support
        if ( defined('MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_USE_OPT_ATTR_SORT_ORDER') && MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_USE_OPT_ATTR_SORT_ORDER == 'True' ) {
          $products_attributes_sort = ' order by pa.products_options_sort_order';
        } else {
          $products_attributes_sort = '';
        }
        // product optionsimages support
        if ($this->options_images) {
          $products_options_query = tep_db_query("select pov.products_options_values_id, pov.products_options_values_name, pov.products_options_values_image, pa.options_values_price, pa.price_prefix, pa.option_image from products_attributes pa, products_options_values pov where pa.products_id = '" . (int)$this->products_id . "' and pa.options_id = '" . (int)$products_options_name['products_options_id'] . "' and pa.options_values_id = pov.products_options_values_id and pov.language_id = '" . (int)$languages_id . "'" . $products_attributes_sort);
        } else {
          $products_options_query = tep_db_query("select pov.products_options_values_id, pov.products_options_values_name, pa.options_values_price, pa.price_prefix from products_attributes pa, products_options_values pov where pa.products_id = '" . (int)$this->products_id . "' and pa.options_id = '" . (int)$products_options_name['products_options_id'] . "' and pa.options_values_id = pov.products_options_values_id and pov.language_id = '" . (int)$languages_id . "'" . $products_attributes_sort);
        }
        if ( MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_PLUGIN == 'multiple_dropdowns' && MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_SHOW_PLEASE_SELECT == 'True' ) {
          // product optionsimages support
          if ($this->options_images) {
            $products_options_array[] =  array('id'=> null, 'text'=> PULL_DOWN_DEFAULT, 'image' => null, 'data-price' => null, 'data-prefix' => null);
          } else {
            $products_options_array[] =  array('id'=> null, 'text'=> PULL_DOWN_DEFAULT, 'data-price' => null, 'data-prefix' => null);
          }
        }

        while ( $products_options = tep_db_fetch_array($products_options_query) ) {
          // product optionsimages support
          if ($this->options_images) {
            $products_options_array[] = array('id' => $products_options['products_options_values_id'], 'text' => $products_options['products_options_values_name'], 'image' => (tep_not_null($products_options['option_image'])? $products_options['option_image'] : $products_options['products_options_values_image']));
          } else {
            $products_options_array[] = array('id' => $products_options['products_options_values_id'], 'text' => $products_options['products_options_values_name']);
          }
        
          if(MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_ACTUAL_PRICE_PULL_DOWN == 'True'){
            //Option prices will displayed as a final product price. This can (currently) only be used with a satisfying result if you have only one option per product.
            if ($products_options['price_prefix'] == '-') {// in case price lowers, don't add values, subtract.
              $show_price = 0.0 + $this->products_original_price - $products_options['options_values_price']; // force float (in case) using the 0.0;
            } else {
              $show_price = 0.0 + $this->products_original_price + $products_options['options_values_price']; // force float (in case) using the 0.0;
            }

            if ( MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_PLUGIN == 'base' ) {
              $products_options_array[sizeof($products_options_array)-1]['text'] .= ' '.$currencies->display_price( $show_price, tep_get_tax_rate($this->products_tax_class_id)).' ';
            }
            $products_options_array[sizeof($products_options_array)-1]['price'] = $products_options['options_values_price'];
            $products_options_array[sizeof($products_options_array)-1]['prefix'] = $products_options['price_prefix'];

          } else { //Display the option prices as differece prices with +/- prefix as usually
            if ($products_options['options_values_price'] != '0') {
              $products_options_array[sizeof($products_options_array)-1]['text'] .= ' (' . $products_options['price_prefix'] . $currencies->display_price($products_options['options_values_price'], tep_get_tax_rate($this->products_tax_class_id)) .')';
            }
          }

        }
        if (isset($cart->contents[$this->products_id]['attributes'][$products_options_name['products_options_id']])) {
          $selected = $cart->contents[$this->products_id]['attributes'][$products_options_name['products_options_id']];
        } else {
          $selected = 0;
        }
        $attributes[]=array('oid'=>$products_options_name['products_options_id'],
                          'oname'=>$products_options_name['products_options_name'],
                          'ovals'=>$products_options_array,
                          'price'=>$products_options['price'],
                          'prefix'=>$products_options['price_prefix'],
                        'default'=>$selected);
      }
      return $attributes;
    }


/*
    Method: _build_attributes_combinations
    A recursive method for building an array enumerating the attribute combinations for the product
    Parameters:
      $attributes             array     An array of the attributes that combinations will be built for.
                                        Format is as returned by _build_attributes_array.
      $showoos                boolean   Flag indicating if non-stocked attributes should be built.
      $markoos                string    'Left' if out of stock indication is to be appended in front of the
                                        attribute combination text.  'Right' if out of stock indication is
                                        to be appended at the end of the attribute combination text.
      $combinations           array     Array of the attribute combinations is returned in this parameter.
                                        Should be set to an empty array before an external call to this method. 
                                          'comb'        => array:   array of a single attribute combination
                                                                      options_id => options_value_id
                                          'id'          => string:  options/values string for this 
                                                                    combination in the form for the
                                                                    key of the products_stock table
                                                                      opt_id-val_id,opt_id-val_id,...
                                          'text'        => string:  Text for this combination.  Values text
                                                                    is as built by _build_attributes_array
                                                                     and contains the add/subtract price for
                                                                     the option value if applicable.  Form is:
                                                                       values_text, values_text
      $selected_combination   integer   Index into the $combinations array of the combination that should
                                        be the default selection when the combination is drawn is returned in
                                        this parameter.  Determined from product id.  Should be set to zero
                                        before an external call to this method.
    Parameters for internal recursion use only:
      $oidindex               integer   Index into the $attributes array of the option to operate on.
      $comb                   array     Array containing option id/values of combination built so far
                                          products_options_id => products_options_value_id
      $id                     string    Contains string of options/values built so far
      $text                   string    Text for the options values constructed so far.
      $isselected             boolean   Flag indicating if so far all option values in this combination
                                        were indicated to be defaults in the product id.
    Returns:
      see $combinations and $selected_combination parameters above
      no actual function return value.
  
*/
    function _build_attributes_combinations($attributes, $showoos, $markoos, &$combinations, &$selected_combination, $oidindex=0, $comb=array(), $id="", $text='', $isselected=true, $price='', $image=array()) {
      global $cart, $currencies;
      foreach ($attributes[$oidindex]['ovals'] as $attrib) {
        $newcomb = $comb;
        $newcomb[$attributes[$oidindex]['oid']] = $attrib['id'];
        $newid = $id.','.$attributes[$oidindex]['oid'].'-'.$attrib['id'];
        $newtext = $text.", ".$attrib['text'];
        $newprice = $price;
        // product optionsimages support
        $newimage = $image;        
        if ($this->options_images) {
          $newimage[] .= $attrib['image'];
        }
        
        if(MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_ACTUAL_PRICE_PULL_DOWN == 'True') {
          if ($attrib['prefix'] == '-') {// in case price lowers, don't add values, subtract.
            $newprice = ((int)$price - (int)$attrib['price']);
          } else {
            $newprice = ((int)$price + (int)$attrib['price']);
          }
        }
        if (isset($cart->contents[$this->products_id]['attributes'][$attributes[$oidindex]['oid']])) {
          $newisselected = ($cart->contents[$this->products_id]['attributes'][$attributes[$oidindex]['oid']] == $attrib['id']) ? $isselected : false;
        } else {
          $newisselected = false;
        }
        if (isset($attributes[$oidindex+1])) {
          $this->_build_attributes_combinations($attributes, $showoos, $markoos, $combinations, $selected_combination, $oidindex+1, $newcomb, $newid, $newtext, $newisselected, $newprice, $newimage);
        } else {
          $is_out_of_stock = $this->check_stock_qtpro(tep_get_prid($this->products_id),1,$newcomb);
          if ( !$is_out_of_stock || ($showoos == true) ) {
            if(MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_ACTUAL_PRICE_PULL_DOWN == 'True') {
              $combprice = ' ' . $currencies->display_price( $newprice + $this->products_original_price, tep_get_tax_rate($this->products_tax_class_id));
            } else {
              $combprice = null;
            }
            switch ($markoos) {
              case 'Left':   $newtext=($is_out_of_stock ? MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_OUT_OF_STOCK . ' - ' : '') . substr($newtext,2) . $combprice;
                             break;
              case 'Right':  $newtext=substr($newtext,2) . $combprice . ($is_out_of_stock ? ' - ' . MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_OUT_OF_STOCK : '');
                             break;
              default:       $newtext=substr($newtext,2) . $combprice;
                             break;
            }
            // product optionsimages support
            if ($this->options_images) {
              $combinations[] = array('comb'=>$newcomb, 'id'=>substr($newid,1), 'text'=>$newtext, 'image'=>$newimage);
            } else {
              $combinations[] = array('comb'=>$newcomb, 'id'=>substr($newid,1), 'text'=>$newtext);
            }
            if ($newisselected) $selected_combination = sizeof($combinations)-1;
          }
        }
      }
    }


/*
    Method: _draw_js_stock_array
    Draw a Javascript array containing the given attribute combinations.
    Generally used to draw array of in-stock combinations for Javascript out of stock
    validation and messaging.
    Parameters:
      $combinations        array   Array of combinations to build the Javascript array for.
                                   Array must be of the form returned by _build_attributes_combinations
                                   Usually this array only contains in-stock combinations.
    Returns:
      string:                 Javacript array definition.  Excludes the "var xxx=" and terminating ";".  Form is:
                              {optval1:{optval2:{optval3:1,optval3:1}, optval2:{optval3:1}}, optval1:{optval2:{optval3:1}}}
                              For example if there are 3 options and the instock value combinations are:
                                opt1   opt2   opt3
                                  1      5      4
                                  1      5      8
                                  1     10      4
                                  3      5      8
                              The string returned would be
                                {1:{5:{4:1,8:1}, 10:{4:1}}, 3:{5:{8:1}}}
*/
    function _draw_js_stock_array($combinations) {
      if ( !((isset($combinations)) && (is_array($combinations)) && (sizeof($combinations) > 0)) ) {
        return '{}';
      }
      $out='';
      foreach ($combinations[0]['comb'] as $oid=>$ovid) {
        $out .= '{'.$ovid.':';
        $opts[] = $oid;
      }
      $out .= '1';
      
      for ($combindex = 1; $combindex < sizeof($combinations); $combindex++) {
        $comb = $combinations[$combindex]['comb'];
        for ($i=0; $i<sizeof($opts)-1; $i++) {
          if ($comb[$opts[$i]] != $combinations[$combindex-1]['comb'][$opts[$i]]) break;
        }
        $out .= str_repeat('}',sizeof($opts)-1-$i).',';
        if ( $i<sizeof($opts)-1 ) {
          for ( $j = $i; $j < sizeof($opts)-1; $j++)
            $out .= $comb[$opts[$j]] . ':{';
        }
        $out .= $comb[$opts[sizeof($opts)-1]] . ':1';
      }
      $out .= str_repeat('}',sizeof($opts));
      
      return $out;
    }
    
    ////
    // Check if the required stock is available
    // If insufficent stock is available return $out_of_stock = true
    function check_stock_qtpro($products_id, $products_quantity, $attributes=array()) {
      $stock_left = $this->get_products_stock_qtpro($products_id, $attributes) - $products_quantity;
      $out_of_stock = '';

      if ($stock_left < 0) {
        $out_of_stock = true;
      }

      return $out_of_stock;
    }
  
    ////
    // Return a product's stock
    // TABLES: products. products_stock
    function get_products_stock_qtpro($products_id, $attributes=array()) {
      global $languages_id;
      $products_id = tep_get_prid($products_id);
      $all_nonstocked = true;
      if (sizeof($attributes)>0) {
        $attr_list='';
        $options_list=implode(",",array_keys($attributes));
        $track_stock_query=tep_db_query("select products_options_id, products_options_track_stock from products_options where products_options_id in ($options_list) and language_id= '" . (int)$languages_id . "order by products_options_id'");
        while($track_stock_array=tep_db_fetch_array($track_stock_query)) {
          if ($track_stock_array['products_options_track_stock']) {
            $attr_list.=$track_stock_array['products_options_id'] . '-' . $attributes[$track_stock_array['products_options_id']] . ',';
            $all_nonstocked=false;
          }
        }
        $attr_list=substr($attr_list,0,strlen($attr_list)-1);
      }
    
      if ((sizeof($attributes)==0) | ($all_nonstocked)) {
        $stock_query = tep_db_query("select products_quantity as quantity from products where products_id = '" . (int)$products_id . "'");
      } else {
        $stock_query=tep_db_query("select products_stock_quantity as quantity from products_stock where products_id='". (int)$products_id . "' and products_stock_attributes='$attr_list'");
      }
      if (tep_db_num_rows($stock_query)>0) {
        $stock=tep_db_fetch_array($stock_query);
        $quantity=$stock['quantity'];
      } else {
        $quantity = 0;
      }
      return $quantity;
    }
  
  } // end class
?>
