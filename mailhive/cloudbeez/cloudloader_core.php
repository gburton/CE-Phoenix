<?php
// thanks to octobercms.com

$install_lang = $_SESSION['language'];
if (!@include('cloudloader/languages/' . $install_lang . '.php')) {
    $install_lang = 'english';
    include('cloudloader/languages/' . $install_lang . '.php');
}

$base_path = '../mailhive/cloudbeez/';

include 'cloudloader/php/boot.php';
include 'cloudloader/php/CloudloaderInit.php';


if ($cloudloader_mode == 'install_core') {
    // install
    $inc_content_intro = $cloudloader->getContent('mailbeez_core_installer_intro/' . $inst_lang, array());
    $inc_content_progress = $cloudloader->getContent('mailbeez_core_installer_steps/' . $inst_lang, array());
    $inc_content_install_final = $cloudloader->getContent('mailbeez_core_installer_final/' . $inst_lang, array());
} else {
    // update
    $inc_content_intro = $cloudloader->getContent('mailbeez_core_update_intro/' . $inst_lang, array());
    $inc_content_progress = $cloudloader->getContent('mailbeez_core_update_steps/' . $inst_lang, array());
    $inc_content_update_final = $cloudloader->getContent('mailbeez_core_update_final/' . $inst_lang, array());

}
$inc_content_common = $cloudloader->getContent('mailbeez_core_common/' . $inst_lang, array());

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="iso-8859-15"/>
    <meta name="viewport" content="width=device-width">
    <title>MailBeez OS CloudLoader</title>
    <!-- Styles -->
    <link href="<?php echo $base_path; ?>cloudloader/css/vendor.css" rel="stylesheet">
    <link href="<?php echo $base_path; ?>cloudloader/css/layout.css" rel="stylesheet">
    <link href="<?php echo $base_path; ?>cloudloader/css/controls.css" rel="stylesheet">
    <link href="<?php echo $base_path; ?>cloudloader/css/animations.css" rel="stylesheet">
    <link href="<?php echo $base_path; ?>cloudloader/css/awesome/css/font-awesome.min.css" rel="stylesheet">

    <script>
        <!--
        installerBaseUrl = '<?php echo $cloudloader->getBaseUrl() ?>';
        // -->
    </script>
</head>
<body class="js">

<div id="wrap">

    <!-- Header -->
    <header>
        <div class="container" id="containerHeader"></div>

        <!-- Title -->
        <section class="title">
            <div class="container" id="containerTitle"></div>
        </section>

    </header>

    <!-- Body -->
    <section class="body">
        <div class="container" id="containerBody"></div>
    </section>

</div>

<!-- Footer -->
<footer>
    <div class="container" id="containerFooter"></div>
</footer>

<!-- Render Partials -->
<?php
$partialList = array(
    'header',
    'title',
    'footer',
    'check',
    'check/fail',
    'config',
    'progress',
    'progress/fail',
    'complete' => 'complete_install',
);

if ($_GET['cloudloader_mode'] == 'update_core') {
    $partialList['complete'] = 'complete_update';
}

?>

<?php foreach ($partialList as $step => $file): ?>
    <script type="text/template" data-partial="<?php echo (is_numeric($step)) ? $file : $step; ?>">
    <?php
    if (!@include('cloudloader/languages/' . $install_lang . '/partials/' . $file. '.htm')) {
        include('cloudloader/languages/english/partials/' . $file . '.htm');
    }
    ?>
  </script>
<?php endforeach ?>

<!-- Scripts -->
<script>
    window.cloudloader_mode = '<?php echo $_GET['cloudloader_mode'] ?>'
</script>
<script src="<?php echo $base_path; ?>cloudloader/js/vendor.js"></script>
<script src="<?php echo $base_path; ?>cloudloader/js/app.js"></script>
<script src="<?php echo $base_path; ?>cloudloader/js/check.js"></script>
<script src="<?php echo $base_path; ?>cloudloader/js/progress.js"></script>
<script src="<?php echo $base_path; ?>cloudloader/js/complete.js"></script>

<!-- Bespoke Properties -->
<script>
    /*
     * Checker Page
     */
    Installer.Pages.systemCheck.title = '<?php echo MAILBEEZ_INSTALL_SYSTEM_CHECK; ?>'
    Installer.Pages.systemCheck.nextButton = '<?php echo MAILBEEZ_INSTALL_SYSTEM_CONFIRM; ?>'
    Installer.Pages.systemCheck.cancelButton = '<?php echo MAILBEEZ_INSTALL_CANCEL; ?>'


    Installer.Pages.systemCheck.requirements = [
        { code: 'phpVersion', label: '<?php echo MAILBEEZ_INSTALL_SYSTEM_CHECK_PHP; ?>' },
        { code: 'safeMode', label: '<?php echo MAILBEEZ_INSTALL_SYSTEM_CHECK_SAFEMODE; ?>' },
        { code: 'curlLibrary', label: '<?php echo MAILBEEZ_INSTALL_SYSTEM_CHECK_CURL; ?>' },
        { code: 'liveConnection', label: '<?php echo MAILBEEZ_INSTALL_SYSTEM_CHECK_TEST_CONNECTION; ?>' },
        { code: 'writePermission', label: '<?php echo MAILBEEZ_INSTALL_SYSTEM_CHECK_TEST_WRITE_PERM; ?>' }
    ]

    /*
     * Progress Page
     */
    Installer.Pages.installProgress.title = '<?php echo ($cloudloader_mode == 'install_core') ? MAILBEEZ_INSTALL_INSTALL : MAILBEEZ_INSTALL_UPDATE; ?>'
    Installer.Pages.installProgress.cancelButton = '<?php echo MAILBEEZ_INSTALL_CANCEL; ?>'

    Installer.Pages.installProgress.steps = [
        { code: 'getMetaDataCore', label: '<?php echo MAILBEEZ_INSTALL_INSTALL_STEP1; ?>' },
        { code: 'downloadCore', label: '<?php echo MAILBEEZ_INSTALL_INSTALL_STEP2; ?>' },
        { code: 'backupZip', label: '<?php echo MAILBEEZ_INSTALL_INSTALL_STEP3; ?>' },
        { code: 'checkFilePermission', label: '<?php echo MAILBEEZ_INSTALL_INSTALL_STEP4; ?>' },
        { code: 'extractCore', label: '<?php echo MAILBEEZ_INSTALL_INSTALL_STEP5; ?>' },
        { code: 'finishInstall', label: '<?php echo ($cloudloader_mode == 'install_core') ? MAILBEEZ_INSTALL_INSTALL_STEP6 : MAILBEEZ_INSTALL_UPDATE_STEP6; ?>' }
    ]

    /*
     * Final Pages
     */
    Installer.Pages.installComplete.title = '<?php echo ($cloudloader_mode == 'install_core') ? MAILBEEZ_INSTALL_INSTALL_FINISH : MAILBEEZ_INSTALL_UPDATE_FINISH; ?>'
    Installer.Pages.installComplete.backuplocation = '<?php echo sprintf(MAILBEEZ_INSTALL_BACKUP_LOCATION, $_SESSION['mailbeez_installer_backup_location_dir']); ?>'

</script>
<?php echo $inc_content_common; ?>
</body>
</html>
