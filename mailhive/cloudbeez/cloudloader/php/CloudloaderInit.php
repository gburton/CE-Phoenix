<?php
$cloudloader = new Cloudloader();
$cloudloader->dir_permissions = 0755;
$cloudloader->file_permissions = 0755;
if (file_exists($cloudloader->deployDirectory . 'common/images/default_emailheader.gif')) {
    // updating
    $cloudloader->exclude_overwrite = array('catalog/mailhive/common/images/default_emailheader.gif');
}
$cloudloader->exclude_overwrite_package = array('');

$cloudloader->run();

$cloudloader->cleanLog();
@$cloudloader->cleanWorkDirectory();
$cloudloader->log('Host: %s', php_uname());
$cloudloader->log('Operating system: %s', PHP_OS);
$cloudloader->log('Memory limit: %s', ini_get('memory_limit'));
$cloudloader->log('Max execution time: %s', ini_get('max_execution_time'));

?>