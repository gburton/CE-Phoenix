<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  require 'includes/template_top.php';

  $feed = Web::load_xml('https://feeds.feedburner.com/PhoenixAddons');
  $num = 0;
?>

  <div class="row">
    <div class="col"><h1 class="display-4 mb-2"><?= HEADING_TITLE ?></h1></div>
    <div class="col-sm-4 text-right align-self-center">
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text"><?= TEXT_CERTIFIED_SEARCH_LABEL ?></span>
        </div>
        <?= tep_draw_input_field('filter', null, 'placeholder="' . TEXT_CERTIFIED_SEARCH_PLACEHOLDER . '" id="input-filter"') ?>
      </div>
    </div>
  </div>

  <div class="alert alert-warning"><?php printf(TEXT_CERTIFIED_ADDONS, $feed->channel->description) ?></div>

  <div class="table-responsive">
    <table class="table table-striped table-hover table-filter">
      <thead class="thead-dark">
        <tr>
          <th><?= TABLE_CERTIFIED_ADDONS_TITLE ?></th>
          <th><?= TABLE_CERTIFIED_ADDONS_OWNER ?></th>
          <th><?= TABLE_CERTIFIED_ADDONS_RATING ?></th>
          <th><?= TABLE_CERTIFIED_ADDONS_REMARKS ?></th>
          <th class="text-right d-none d-md-table-cell"><?= TABLE_CERTIFIED_ADDONS_DATE ?></th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach ($feed->channel->item as $item) {
          $num++;
          $filter = implode(',', explode(' ', $item->title));

          echo '<tr data-owner="' . strtolower($item->owner) . '" data-filter="' . strtolower($filter) . '" data-tags="' . strtolower($item->tags) . '">';
            echo '<td class="w-50"><a href="' . $item->link . '" target="_blank" rel="noreferrer"><i class="fas fa-external-link-alt mr-2"></i>' . $item->title . '</a></td>';
            echo '<td>' . $item->owner . '</td>';
            echo '<td>' . tep_draw_stars($item->rating) . '</td>';
            echo '<td>' . $item->remarks ?? '&nbsp;' . '</td>';
            echo '<td class="text-right d-none d-md-table-cell">' . date("j/m/Y", strtotime($item->pubDate)) . '</td>';
          echo '</tr>';
        }
        ?>
      </tbody>
    </table>
  </div>

  <p id="count"><?php printf(NUM_CERTIFIED_ADDONS, $num) ?></p>

  <script>var tr = $('.table-filter > tbody > tr:visible').length; i_empty();$('#input-filter').on('keyup', function() {i_empty();var keyword = $(this).val().toLowerCase(); $('.table-filter > tbody > tr').each( function() { $(this).toggle(keyword.length < 1 || $(this).attr('data-filter').indexOf(keyword) > -1 || $(this).attr('data-tags').indexOf(keyword) > -1 || $(this).attr('data-owner').indexOf(keyword) > -1); }); var tr_filtered = $('.table-filter > tbody > tr:visible').length; if (tr_filtered != tr) {i_empty(); var str = '<?= NUM_FILTERED_ADDONS ?>'; var res = str.replace('{X}', tr_filtered); $('#count').append(res); } });function i_empty() {$('.filtered-result').detach();}</script>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
