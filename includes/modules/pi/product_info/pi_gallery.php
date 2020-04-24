<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class pi_gallery {
    var $code = 'pi_gallery';
    var $group = 'pi_modules_b';
    var $title;
    var $description;
    var $content_width;
    var $sort_order;
    var $api_version;
    var $enabled = false;

    function __construct() {
      $this->group = basename(dirname(__FILE__));

      $this->title = PI_GALLERY_TITLE;
      $this->description = PI_GALLERY_DESCRIPTION;
      $this->description .= '<div class="alert alert-warning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';
      $this->description .= '<div class="alert alert-info">' . $this->display_layout() . '</div>';

      if ( defined('PI_GALLERY_STATUS') ) {
        $this->group = 'pi_modules_' . strtolower(PI_GALLERY_GROUP);
        $this->sort_order = PI_GALLERY_SORT_ORDER;
        $this->content_width = (int)PI_GALLERY_CONTENT_WIDTH;
        $this->enabled = (PI_GALLERY_STATUS == 'True');
      }
    }

    function getOutput() {
      global $oscTemplate, $product_info;
      
      $content_width = $this->content_width;
      $thumbnail_width = PI_GALLERY_CONTENT_WIDTH_EACH;

      $pi_image = $pi_thumb = null;

      if (tep_not_null($product_info['products_image'])) {
        $album_name = sprintf(PI_GALLERY_ALBUM_NAME, $product_info['products_name']);
        $album_exit = PI_GALLERY_ALBUM_CLOSE;

        $pi_html = [];
        $pi_html[0] = ['image' => $product_info['products_image'], 'htmlcontent' => $product_info['products_name']];

        $pi_query = tep_db_query("select image, htmlcontent from products_images where products_id = '" . (int)$product_info['products_id'] . "' order by sort_order");
        $pi_total = tep_db_num_rows($pi_query);

        if ($pi_total > 0) {
          $pi_counter = 1;

          while ($pi = tep_db_fetch_array($pi_query)) {
            $pi_html[$pi_counter] = $pi;

            $pi_counter++;
          }
        }

        $active_image = array_shift($pi_html);
        $other_images = $pi_html;

        $modal_size = PI_GALLERY_MODAL_SIZE;
        
        $pi_image .= '<a href="#lightbox" class="lb" data-toggle="modal" data-slide="0">';
          $pi_image .= tep_image('images/' . $active_image['image'], tep_db_output( $active_image['htmlcontent']));
        $pi_image .= '</a>';

        $first_img_indicator = '<li data-target="#carousel" data-slide-to="0" class="pointer active"></li>';
        $first_img = '<div class="carousel-item text-center active">';
          $first_img .= tep_image('images/' . $active_image['image'], tep_db_output($active_image['htmlcontent']), null, null, 'loading="lazy"');
        $first_img .= '</div>';

        // now create the thumbs
        if (count($other_images) > 0) {
          $pi_thumb .= '<div class="row">';
          foreach ($other_images as $k => $v) {
            $n = $k+1;
            $pi_thumb .= '<div class="' . $thumbnail_width . '">';
              $pi_thumb .= '<a href="#lightbox" class="lb" data-toggle="modal" data-slide="' . $n . '">';
                $pi_thumb .= tep_image('images/' . $v['image'], null, null, null, 'loading="lazy"');
              $pi_thumb .= '</a>';
            $pi_thumb .= '</div>';
          }
          $pi_thumb .= '</div>';

          $other_img_indicator = $other_img = null;
          foreach ($other_images as $k => $v) {
            $n = $k+1;
            $other_img_indicator .= '<li data-target="#carousel" data-slide-to="' . $n . '" class="pointer"></li>';
            $other_img .= '<div class="carousel-item text-center">';
            $other_img .= tep_image('images/' . $v['image'], null, null, null, 'loading="lazy"');
            if (tep_not_null($v['htmlcontent'])) {
              $other_img .= '<div class="carousel-caption d-none d-md-block">';
                $other_img .= $v['htmlcontent'];
              $other_img .= '</div>';
            }
            $other_img .= '</div>';
          }
        }
        
        $tpl_data = ['group' => $this->group, 'file' => __FILE__];
        include 'includes/modules/block_template.php';
        
        $swipe_arrows = '';
        if (PI_GALLERY_SWIPE_ARROWS == 'True') {
          $swipe_arrows = '<a class="carousel-control-prev" href="#carousel" role="button" data-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span></a><a class="carousel-control-next" href="#carousel" role="button" data-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span></a>';
        }

        $indicators = '';
        if (PI_GALLERY_INDICATORS == 'True') {
          $indicators .= '<ol class="carousel-indicators">';
            $indicators .= $first_img_indicator;
            $indicators .= $other_img_indicator;
          $indicators .= '</ol>';
        }
  
        $modal_gallery_footer = <<<mgf
<div id="lightbox" class="modal fade" role="dialog">
  <div class="modal-dialog {$modal_size}" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <div class="carousel slide" data-ride="carousel" tabindex="-1" id="carousel">
          {$indicators}
          <div class="carousel-inner">
            {$first_img}{$other_img}
          </div>
          {$swipe_arrows}
        </div>
      </div>
      <div class="modal-footer">
        <h5 class="text-uppercase mr-auto">{$album_name}</h5>
        <a href="#" role="button" data-dismiss="modal" class="btn btn-primary px-3">{$album_exit}</a>
      </div>
    </div>
  </div>
</div>
mgf;

        $oscTemplate->addBlock($modal_gallery_footer, 'footer_scripts');

        $modal_clicker = <<<mc
<script>$(document).ready(function() { $('a.lb').click(function(e) { var s = $(this).data('slide'); $('#lightbox').carousel(s); }); });</script>
mc;
        $oscTemplate->addBlock($modal_clicker, 'footer_scripts');
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('PI_GALLERY_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Gallery Module', 'PI_GALLERY_STATUS', 'True', 'Should this module be shown on the product info page?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Module Display', 'PI_GALLERY_GROUP', 'B', 'Where should this module display on the product info page?', '6', '2', 'tep_cfg_select_option(array(\'A\', \'B\', \'C\', \'D\', \'E\', \'F\', \'G\', \'H\', \'I\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'PI_GALLERY_CONTENT_WIDTH', '12', 'What width container should the content be shown in?', '6', '3', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Thumbnail Width', 'PI_GALLERY_CONTENT_WIDTH_EACH', 'col-4 col-sm-6 col-lg-4', 'What width container should each thumbnail be shown in? Default:  XS 3 each row, SM/MD 2 each row, LG/XL 3 each row.', '6', '4', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Modal Popup Size', 'PI_GALLERY_MODAL_SIZE', 'modal-md', 'Choose the size of the Popup.  sm = small, md = medium etc.', '6', '5', 'tep_cfg_select_option(array(\'modal-sm\', \'modal-md\', \'modal-lg\', \'modal-xl\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Show Swipe Arrows', 'PI_GALLERY_SWIPE_ARROWS', 'True', 'Swipe Arrows make for a better User Experience in some cases.', '6', '6', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Show Indicators', 'PI_GALLERY_INDICATORS', 'True', 'Indicators allow users to jump from image to image without having to swipe.', '6', '7', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'PI_GALLERY_SORT_ORDER', '200', 'Sort order of display. Lowest is displayed first.', '6', '8', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('PI_GALLERY_STATUS', 'PI_GALLERY_GROUP', 'PI_GALLERY_CONTENT_WIDTH', 'PI_GALLERY_CONTENT_WIDTH_EACH', 'PI_GALLERY_MODAL_SIZE', 'PI_GALLERY_SWIPE_ARROWS', 'PI_GALLERY_INDICATORS', 'PI_GALLERY_SORT_ORDER');
    }
    
    function display_layout() {
      include_once(DIR_FS_CATALOG . 'includes/modules/content/product_info/cm_pi_modular.php');
       
      return call_user_func(array('cm_pi_modular', 'display_layout'));
    }
    
  }
  