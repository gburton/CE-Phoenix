<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/
?>

<div class="row mb-1">
  <div class="col">
    <h5><?= $OSCOM_PayPal->getDef('paypal_installed'); ?></h5>
    <div id="appPayPalToolbar">
      <?php
      foreach ( $OSCOM_PayPal->getModules() as $m ) {
        if ( $OSCOM_PayPal->isInstalled($m) ) {
          echo $OSCOM_PayPal->drawButton($OSCOM_PayPal->getModuleInfo($m, 'short_title'), tep_href_link('paypal.php', 'action=configure&module=' . $m), 'info', 'data-module="' . $m . '"') . "\n";
        }
      }
      echo $OSCOM_PayPal->drawButton($OSCOM_PayPal->getDef('section_general'), tep_href_link('paypal.php', 'action=configure&module=G'), 'info', 'data-module="G"'); 
      ?>
    </div>
  </div>
  <div class="col">
    <h5><?= $OSCOM_PayPal->getDef('paypal_not_installed'); ?></h5>
    <nav id="appPayPalToolbarMore" class="nav">
      <?php
      foreach ( $OSCOM_PayPal->getModules() as $m ) {
        if ( !$OSCOM_PayPal->isInstalled($m) ) {
          echo '<a class="nav-link btn btn-sm btn-secondary mr-1" title="' . $OSCOM_PayPal->getModuleInfo($m, 'short_title') . '" href="' . tep_href_link('paypal.php', 'action=configure&module=' . $m) . '">' . $m . '</a>';
        }
      }
      ?>
    </nav>
  </div>
</div>

<?php
  if ( $OSCOM_PayPal->isInstalled($current_module) || ($current_module == 'G') ) {
    $current_module_title = ($current_module != 'G') ? $OSCOM_PayPal->getModuleInfo($current_module, 'title') : $OSCOM_PayPal->getDef('section_general');
    $req_notes = ($current_module != 'G') ? $OSCOM_PayPal->getModuleInfo($current_module, 'req_notes') : null;

    if ( is_array($req_notes) && !empty($req_notes) ) {
      foreach ( $req_notes as $rn ) {
        echo '<div class="alert alert-warning"><p>' . $rn . '</p></div>';
      }
    }
?>

<form name="paypalConfigure" action="<?php echo tep_href_link('paypal.php', 'action=configure&subaction=process&module=' . $current_module); ?>" method="post">

<h1 class="display-4"><?php echo $current_module_title; ?></h1>

<div class="card">
  <div class="card-body">
    <?php 
    foreach ( $OSCOM_PayPal->getInputParameters($current_module) as $cfg ) {
      echo $cfg;
    };
    ?>
  </div>
</div>

<div class="row">
  <div class="col">
    <p class="mt-2"><?= $OSCOM_PayPal->drawButton($OSCOM_PayPal->getDef('button_save'), null, 'success');?></p>
  </div>
  <?php 
  if ( $current_module != 'G' ) {
    ?>
    <div class="col text-right">
      <button type="button" class="btn btn-danger mt-2" data-toggle="modal" data-target="#delModal">
        <?= $OSCOM_PayPal->getDef('dialog_uninstall_title'); ?>
      </button>
    </div>
    <?php
  }
  ?>
  </div>
</div>

</form>

<?php
  } else {
?>

<h1 class="display-4"><?= $OSCOM_PayPal->getModuleInfo($current_module, 'title'); ?></h1>

<div class="alert alert-info"><?= $OSCOM_PayPal->getModuleInfo($current_module, 'introduction'); ?></div>

<p><?= $OSCOM_PayPal->drawButton($OSCOM_PayPal->getDef('button_install_title', array('title' => $OSCOM_PayPal->getModuleInfo($current_module, 'title'))), tep_href_link('paypal.php', 'action=configure&subaction=install&module=' . $current_module), 'success'); ?></p>

<?php
  }
?>

<div class="modal fade" id="delModal" tabindex="-1" aria-labelledby="..." aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="..."><?= sprintf($OSCOM_PayPal->getDef('modal_uninstall_title'), $current_module); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="<?= $OSCOM_PayPal->getDef('modal_uninstall_cancel'); ?>">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?= sprintf($OSCOM_PayPal->getDef('dialog_uninstall_body'), $OSCOM_PayPal->getModuleInfo($current_module, 'title')); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $OSCOM_PayPal->getDef('modal_uninstall_cancel'); ?></button>
        <a role="button" class="btn btn-danger" href="<?= tep_href_link('paypal.php', 'action=configure&subaction=uninstall&module=' . $current_module); ?>"><?= $OSCOM_PayPal->getDef('modal_uninstall_do_it'); ?></a>
      </div>
    </div>
  </div>
</div>

<script>
$(function() {
  $('#appPayPalToolbar a[data-module="<?php echo $current_module; ?>"]').addClass('active');
});
</script>
