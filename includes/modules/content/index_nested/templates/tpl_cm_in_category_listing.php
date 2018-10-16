<div class="col-sm-<?php echo $content_width; ?> cm-in-category-listing">
  <div itemscope itemtype="http://schema.org/ItemList">
    <meta itemprop="itemListOrder" content="http://schema.org/ItemListUnordered" />
    <meta itemprop="name" content="<?php echo $category_name; ?>" />    
    
    <div class="card-deck">    
      <?php
      $item = 1;
      
      foreach ($category_array as $k => $v) {
        echo '<div class="card card-body text-center border-0">' . PHP_EOL;
          echo '<a href="' . tep_href_link('index.php', 'cPath=' . $v['id']) . '">' . tep_image('images/' . $v['image'], htmlspecialchars($v['title'])) . '</a>';
          echo '<a class="card-link" href="' . tep_href_link('index.php', 'cPath=' . $v['id']) . '"><span itemprop="itemListElement">' . $v['title'] . '</span></a>';
        echo '</div>' . PHP_EOL;   

        if ( $item%MODULE_CONTENT_IN_CATEGORY_LISTING_DISPLAY_ROW_SM == 0 ) echo '<div class="w-100 d-none d-sm-block d-md-none"></div>' . PHP_EOL; 
        if ( $item%MODULE_CONTENT_IN_CATEGORY_LISTING_DISPLAY_ROW_MD == 0 ) echo '<div class="w-100 d-none d-md-block d-lg-none"></div>' . PHP_EOL; 
        if ( $item%MODULE_CONTENT_IN_CATEGORY_LISTING_DISPLAY_ROW_LG == 0 ) echo '<div class="w-100 d-none d-lg-block d-xl-none"></div>' . PHP_EOL;
        if ( $item%MODULE_CONTENT_IN_CATEGORY_LISTING_DISPLAY_ROW_XL == 0 ) echo '<div class="w-100 d-none d-xl-block"></div>' . PHP_EOL;
        $item++;        
      }
      ?>
    </div>
    
  </div>
</div>

<?php
/*
  Copyright (c) 2018, G Burton
  All rights reserved.

  Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

  1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

  2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

  3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
?>
