<div class="col-sm-<?php echo $content_width; ?> cm-t-list">
  <div class="row">
    <?php
    while ($testimonials = tep_db_fetch_array($testimonials_query)) {
      echo '<div class="col-sm-' . $item_width . '">' . PHP_EOL;
        echo '<blockquote class="blockquote">' . PHP_EOL;
          echo '<p class="font-weight-lighter">' . tep_output_string_protected($testimonials['testimonials_text']) . '</p>' . PHP_EOL;
          echo '<footer class="blockquote-footer">' . sprintf(MODULE_CONTENT_TESTIMONIALS_LIST_WRITERS_NAME_DATE, tep_output_string_protected($testimonials['customers_name']), tep_date_short($testimonials['date_added'])) . '</footer>' . PHP_EOL;
        echo '</blockquote>' . PHP_EOL;
      echo '</div>' . PHP_EOL;
    }
    ?>
  </div>
  <div class="row align-items-center">
    <div class="col-sm-6 d-none d-sm-block">
      <?php echo $testimonials_split->display_count(TEXT_DISPLAY_NUMBER_OF_TESTIMONIALS); ?>
    </div>
    <div class="col-sm-6">
      <?php echo $testimonials_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info'))); ?>
    </div>
  </div>
</div>

<?php
/*
  Copyright (c) 2019, G Burton
  All rights reserved.

  Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

  1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

  2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

  3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
?>
   