<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/
?>

<div class="row row-cols-1 row-cols-md-2">
  <div class="col mb-4">
    <div class="card">
      <div class="card-header">
        <?= $OSCOM_PayPal->getDef('online_documentation_title') ?>
      </div>
      <div class="card-body">
        <div class="pp-panel pp-panel-info">
          <?= $OSCOM_PayPal->getDef('online_documentation_body', ['button_online_documentation' => $OSCOM_PayPal->drawButton($OSCOM_PayPal->getDef('button_online_documentation'), 'https://library.oscommerce.com/Package&paypal&oscom23', 'info', 'target="_blank" rel="noreferrer"')]) ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col mb-4">
    <div class="card">
      <div class="card-header">
        <?= $OSCOM_PayPal->getDef('online_forum_title') ?>
      </div>
      <div class="card-body">
        <div class="pp-panel pp-panel-warning">
          <?= $OSCOM_PayPal->getDef('online_forum_body', ['button_online_forum' => $OSCOM_PayPal->drawButton($OSCOM_PayPal->getDef('button_online_forum'), 'https://forums.oscommerce.com/forum/117-topics/', 'warning', 'target="_blank" rel="noreferrer"')]) ?>
        </div>
      </div>
    </div>
  </div>
</div>
