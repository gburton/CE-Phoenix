<?php
/*
      QT Pro Version 5.4 BS
  
      pad_single_radioset.php
  
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
          03/2005 - Remove '&' for pass by reference from parameters to call of
                    _build_attributes_combinations.  Only needed on method definition and causes
                    error messages on some php versions/configurations
          
*******************************************************************************************
  
      QT Pro Product Attributes Display Plugin
      pad_single_radioset.php - Display stocked product attributes as a single radioset with entries
                                for each possible combination of attributes.
      Class Name: pad_single_radioset
      This class generates the HTML to display product attributes.  First, product attributes that
      stock is tracked for are displayed in a single radioset with entries for each possible
      combination of attributes..  Then attributes that stock is not tracked for are displayed,
      each attribute in its own dropdown list.
      Methods overidden or added:
        _draw_stocked_attributes             draw attributes that stock is tracked for
*/
  require_once('includes/classes/pad_single_dropdown.php');

  class pad_single_radioset extends pad_single_dropdown {


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
      
      $out= '' ;
      $combine_nostock = (MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_COMBINE_NON_STOCKED == 'True')? true : false;
      $attributes = $this->_build_attributes_array(true, $combine_nostock);
      if (sizeof($attributes) > 0) {
        $combinations = array();
        $selected_combination = 0;
        $this->_build_attributes_combinations($attributes, $this->show_out_of_stock == 'True', $this->mark_out_of_stock, $combinations, $selected_combination);
    
        $combname = '';
        foreach ($attributes as $attrib) {
          $combname .= ', ' . $attrib['oname'];
        }
        $combname = substr($combname,2).':';
        
        $out .= '<div class="col-md-3">' . "\n";
        $out .= '<strong>' . $combname . '</strong>' . "\n";
        $out .= '</div>' . "\n";
        $out .= '<div class="col-md-9">' . "\n";
        $out .= '<table class="table table-striped table-condensed table-hover">';

        foreach ($combinations as $combindex => $comb) {
          // BEGIN product optionsimages support
          if ($this->options_images) {
            if ( tep_not_null($comb['image']) ) {
              $images = null;
              for ($j=0, $k=sizeof($comb['image']); $j<$k; $j++) {
                if ( tep_not_null($comb['image'][$j]) ) {
                  $images .= tep_image('images/options/' . $comb['image'][$j], $comb['text'], '40', '40', null, 'false') . ' ';
                }
              }
            }
            $comb['text'] = $images . $comb['text'];
          }
          // END product optionsimages support
          $out .= '<tr class="table-selection">' . "\n";
          $out .= '<td>' . tep_draw_radio_field('attrcomb', $combinations[$combindex]['id'], ($combindex == $selected_combination)) . ' ' . $comb['text'] . '</td>' . "\n";
          $out .= '</tr>' . "\n";
        }
        $out .= '</table>' . "\n";
        $out .= '</div>' . "\n";
        $combname='';
      }
      
      $out .= $this->_draw_out_of_stock_message_js($attributes);
      
      return $out;
    }

  }
?>
