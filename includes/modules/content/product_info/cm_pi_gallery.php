<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_pi_gallery {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_PI_GALLERY_TITLE;
      $this->description = MODULE_CONTENT_PI_GALLERY_DESCRIPTION;
      $this->description .= '<div class="secWarning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';

      if ( defined('MODULE_CONTENT_PI_GALLERY_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_PI_GALLERY_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_PI_GALLERY_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate, $product_info;

      $content_width = (int)MODULE_CONTENT_PI_GALLERY_CONTENT_WIDTH;
      $thumbnail_width = MODULE_CONTENT_PI_GALLERY_CONTENT_WIDTH_EACH;

      $pi_image = $pi_thumb = null;

      if (tep_not_null($product_info['products_image'])) {
        $album_name = sprintf(MODULE_CONTENT_PI_GALLERY_ALBUM_NAME, $product_info['products_name']);
        $album_exit = MODULE_CONTENT_PI_GALLERY_ALBUM_CLOSE;

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

        $pi_image .= '<a href="#lightbox" class="lb" data-toggle="modal" data-slide="0">';
          $pi_image .= tep_image('images/' . $active_image['image'], tep_db_output( $active_image['htmlcontent']));
        $pi_image .= '</a>';

        $first_img_indicator = '<li data-target="#carousel" data-slide-to="0" class="pointer active"></li>';
        $first_img = '<div class="carousel-item text-center active">';
          $first_img .= tep_image('images/' . $active_image['image'], tep_db_output($active_image['htmlcontent']), null, null, 'loading="lazy"');
        $first_img .= '</div>';

        // now create the thumbs
        $other_images = $pi_html;

        if (sizeof($other_images) > 0) {
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

        $swipe_arrows = null;
        if (MODULE_CONTENT_PI_GALLERY_SWIPE_ARROWS == 'True') {
          $swipe_arrows = '<a class="carousel-control-prev" href="#carousel" role="button" data-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span></a><a class="carousel-control-next" href="#carousel" role="button" data-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span></a>';
        }

        $indicators = null;
        if (MODULE_CONTENT_PI_GALLERY_INDICATORS == 'True') {
          $indicators .= '<ol class="carousel-indicators">';
            $indicators .= $first_img_indicator;
            $indicators .= $other_img_indicator;
          $indicators .= '</ol>';
        }
        
        $modal_size = MODULE_CONTENT_PI_GALLERY_MODAL_SIZE;

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

        $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
        include 'includes/modules/content/cm_template.php';
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_PI_GALLERY_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Gallery Module', 'MODULE_CONTENT_PI_GALLERY_STATUS', 'True', 'Should this module be shown on the product info page?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_CONTENT_PI_GALLERY_CONTENT_WIDTH', '4', 'What width container should the content be shown in?', '6', '2', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Thumbnail Width', 'MODULE_CONTENT_PI_GALLERY_CONTENT_WIDTH_EACH', 'col-4 col-sm-6 col-lg-4', 'What width container should each thumbnail be shown in? Default:  XS 3 each row, SM/MD 2 each row, LG/XL 3 each row.', '6', '3', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Modal Popup Size', 'MODULE_CONTENT_PI_GALLERY_MODAL_SIZE', 'modal-md', 'Choose the size of the Popup.  sm = small, md = medium etc.', '6', '1', 'tep_cfg_select_option(array(\'modal-sm\', \'modal-md\', \'modal-lg\', \'modal-xl\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Show Swipe Arrows', 'MODULE_CONTENT_PI_GALLERY_SWIPE_ARROWS', 'True', 'Swipe Arrows make for a better User Experience in some cases.', '6', '4', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Show Indicators', 'MODULE_CONTENT_PI_GALLERY_INDICATORS', 'True', 'Indicators allow users to jump from image to image without having to swipe.', '6', '5', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_PI_GALLERY_SORT_ORDER', '65', 'Sort order of display. Lowest is displayed first.', '6', '6', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_CONTENT_PI_GALLERY_STATUS', 'MODULE_CONTENT_PI_GALLERY_CONTENT_WIDTH', 'MODULE_CONTENT_PI_GALLERY_CONTENT_WIDTH_EACH', 'MODULE_CONTENT_PI_GALLERY_MODAL_SIZE', 'MODULE_CONTENT_PI_GALLERY_SWIPE_ARROWS', 'MODULE_CONTENT_PI_GALLERY_INDICATORS', 'MODULE_CONTENT_PI_GALLERY_SORT_ORDER');
    }
  }
