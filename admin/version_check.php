<?php
/*
  $Id$
  
  CE Phoenix, E-Commerce Made Easy
  https://phoenixcart.org
  
  Copyright (c) 2021 Phoenix Cart
  
  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $current_version = tep_get_version();

  $new_versions = [];
  $check_message = [];

  $feed = Web::load_xml('https://feeds.feedburner.com/phoenixUpdate');

  foreach ($feed->channel->item as $item) {
    $compared_version = preg_replace('/[^0-9.]/', '', $item->title);

    if (version_compare($current_version, $compared_version, '<')) {
      $new_versions[] = $item;
    }
  }

  if (!empty($new_versions)) {
    $check_message = ['class' => 'alert alert-danger', 'message' => sprintf(VERSION_UPGRADES_AVAILABLE, $new_versions[0]->title)];
  } else {
    $check_message = ['class' => 'alert alert-success', 'message' => VERSION_RUNNING_LATEST];
  }

  require('includes/template_top.php');
?>

  <h1 class="display-4 mb-2"><?= HEADING_TITLE ?></h1>

  <p class="lead"><?= sprintf(TITLE_INSTALLED_VERSION, $current_version) ?></p>

  <div class="<?= $check_message['class'] ?>">
    <p class="lead"><?= $check_message['message'] ?></p>
  </div>

<?php
  if (!empty($new_versions)) {
  ?>
  <div class="table-responsive">
    <table class="table table-striped table-hover">
      <thead class="thead-dark">
        <tr>
          <th><?= TABLE_HEADING_VERSION ?></th>
          <th><?= TABLE_HEADING_RELEASED ?></th>
          <th class="text-right"><?= TABLE_HEADING_ACTION ?></th>
        </tr>
      </thead>
      <tbody>
      <?php
      foreach ($new_versions as $version) {
        $date = DateTime::createFromFormat(DATE_ATOM, $version->date);
        ?>
        <tr>
          <td><?= '<a href="' . $version->link . '" target="_blank" rel="noreferrer">' . $version->title . '</a>' ?></td>
          <td><?= $date->format('l jS F, Y') ?></td>
          <td class="text-right"><?= '<a href="' . $version->link . '" target="_blank" rel="noreferrer"><i class="fas fa-info-circle text-info"></i></a>' ?></td>
        </tr>
        <?php
      }
      ?>
      </tbody>
    </table>
  </div>
  <?php
  }

  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>