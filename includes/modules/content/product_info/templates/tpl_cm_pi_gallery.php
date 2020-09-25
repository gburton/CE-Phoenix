<div class="col-sm-<?php echo $content_width; ?> cm-pi-gallery">
  <?php
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
  ?>
</div>

<?php
    $swipe_arrows = '';
    if (MODULE_CONTENT_PI_GALLERY_SWIPE_ARROWS == 'True') {
      $swipe_arrows = '<a class="carousel-control-prev" href="#carousel" role="button" data-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span></a><a class="carousel-control-next" href="#carousel" role="button" data-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span></a>';
    }

    $indicators = '';
    if (MODULE_CONTENT_PI_GALLERY_INDICATORS == 'True') {
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

  echo $pi_image;
  echo $pi_thumb;

/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/
?>
