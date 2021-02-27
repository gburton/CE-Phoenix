<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class pi_gallery extends abstract_module  {

    const CONFIG_KEY_BASE = 'PI_GALLERY_';

    public $content_width;
    public $api_version;
    public $group;

    function __construct() {
      parent::__construct();
      $this->group = basename(dirname(__FILE__));

      $this->description .= '<div class="alert alert-warning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';
      $this->description .= '<div class="alert alert-info">' . $this->display_layout() . '</div>';

      if ( defined('PI_GALLERY_STATUS') ) {
        $this->group = 'pi_modules_' . strtolower(PI_GALLERY_GROUP);
        $this->content_width = (int)PI_GALLERY_CONTENT_WIDTH;
      }
    }

    function getOutput() {
      global $oscTemplate, $product_info;

      $content_width = $this->content_width;
      $thumbnail_width = PI_GALLERY_CONTENT_WIDTH_EACH;

      $pi_image = $pi_thumb = '';

      if (Text::is_empty($product_info['products_image'])) {
        return;
      }

      $album_name = sprintf(PI_GALLERY_ALBUM_NAME, $product_info['products_name']);
      $album_exit = PI_GALLERY_ALBUM_CLOSE;

      $pi_html = [];
      $pi_html[0] = ['image' => $product_info['products_image'], 'htmlcontent' => $product_info['products_name']];

      $pi_query = tep_db_query("SELECT image, htmlcontent FROM products_images WHERE products_id = " . (int)$product_info['products_id'] . " ORDER BY sort_order");
      $pi_total = mysqli_num_rows($pi_query);

      if ($pi_total > 0) {
        $pi_counter = 1;

        while ($pi = $pi_query->fetch_assoc()) {
          $pi_html[$pi_counter] = $pi;

          $pi_counter++;
        }
      }

      $active_image = array_shift($pi_html);
      $other_images = $pi_html;

      $modal_size = PI_GALLERY_MODAL_SIZE;

      $pi_image .= '<a href="#lightbox" class="lb" data-toggle="modal" data-slide="0">';
        $pi_image .= tep_image('images/' . $active_image['image'], htmlspecialchars( $active_image['htmlcontent']));
      $pi_image .= '</a>';

      $first_img_indicator = '<li data-target="#carousel" data-slide-to="0" class="pointer active"></li>';
      $first_img = '<div class="carousel-item text-center active">';
        $first_img .= tep_image('images/' . $active_image['image'], htmlspecialchars($active_image['htmlcontent']), '', '', 'loading="lazy"');
      $first_img .= '</div>';

// now create the thumbs
      $other_img_indicator = $other_img = '';
      if (count($other_images) > 0) {
        $pi_thumb .= '<div class="row">';
        foreach ($other_images as $k => $v) {
          $n = $k+1;
          $pi_thumb .= '<div class="' . $thumbnail_width . '">';
            $pi_thumb .= '<a href="#lightbox" class="lb" data-toggle="modal" data-slide="' . $n . '">';
              $pi_thumb .= tep_image('images/' . $v['image'], '', '', '', 'loading="lazy"');
            $pi_thumb .= '</a>';
          $pi_thumb .= '</div>';
        }
        $pi_thumb .= '</div>';

        foreach ($other_images as $k => $v) {
          $n = $k+1;
          $other_img_indicator .= '<li data-target="#carousel" data-slide-to="' . $n . '" class="pointer"></li>';
          $other_img .= '<div class="carousel-item text-center">';
          $other_img .= tep_image('images/' . $v['image'], '', '', '', 'loading="lazy"');
          if (!Text::is_empty($v['htmlcontent'])) {
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

    function display_layout() {
      return cm_pi_modular::display_layout();
    }

    protected function get_parameters() {
      return [
        'PI_GALLERY_STATUS' => [
          'title' => 'Enable Gallery Module',
          'value' => 'True',
          'desc' => 'Should this module be shown on the product info page?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'PI_GALLERY_GROUP' => [
          'title' => 'Module Display',
          'value' => 'B',
          'desc' => 'Where should this module display on the product info page?',
          'set_func' => "tep_cfg_select_option(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'], ",
        ],
        'PI_GALLERY_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'PI_GALLERY_CONTENT_WIDTH_EACH' => [
          'title' => 'Thumbnail Width',
          'value' => 'col-4 col-sm-6 col-lg-4',
          'desc' => 'What width container should each thumbnail be shown in? Default:  XS 3 each row, SM/MD 2 each row, LG/XL 3 each row.',
        ],
        'PI_GALLERY_MODAL_SIZE' => [
          'title' => 'Modal Popup Size',
          'value' => 'modal-md',
          'desc' => 'Choose the size of the Popup.  sm = small, md = medium etc.',
          'set_func' => "tep_cfg_select_option(['modal-sm', 'modal-md', 'modal-lg', 'modal-xl'], ",
        ],
        'PI_GALLERY_SWIPE_ARROWS' => [
          'title' => 'Show Swipe Arrows',
          'value' => 'True',
          'desc' => 'Swipe Arrows make for a better User Experience in some cases.',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'PI_GALLERY_INDICATORS' => [
          'title' => 'Show Indicators',
          'value' => 'True',
          'desc' => 'Indicators allow users to jump from image to image without having to swipe.',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'PI_GALLERY_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '200',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
