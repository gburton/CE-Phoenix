<div class="col-sm-12">
  <div class="panel panel-success cm-cs-downloads">
    <div class="panel-heading"><?php echo MODULE_CONTENT_CHECKOUT_SUCCESS_DOWNLOADS_PUBLIC_TITLE; ?></div>
    <div class="panel-body">
      <?php echo $download_content; ?>
      <p><?php echo sprintf(MODULE_CONTENT_CHECKOUT_SUCCESS_DOWNLOADS_FOOTER_, tep_href_link('account.php', '', 'SSL')); ?></p>
    </div>
  </div>
</div>
