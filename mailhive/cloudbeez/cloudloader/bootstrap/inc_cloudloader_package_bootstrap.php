<?php

$base_path = '../mailhive/cloudbeez/';

include $base_path . 'cloudloader/php/boot.php';

$cloudloader = new Cloudloader();
$inc_content = $cloudloader->getContent('mailbeez_package_select/' . $inst_lang, array('IMG' => $screen_img));
$inc_content_common = $cloudloader->getContent('mailbeez_core_common/' . $inst_lang, array());

// todo
// test

$install_url = 'mailbeez.php?cloudloader_mode=install_package'



?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="iso-8859-15"/>
    <meta name="viewport" content="width=device-width">
    <title>MailBeez OS Cloudloader Packages</title>
    <!-- Styles -->
    <link href="<?php echo $base_path; ?>cloudloader/css/vendor.css" rel="stylesheet">
    <link href="<?php echo $base_path; ?>cloudloader/css/layout.css" rel="stylesheet">
    <link href="<?php echo $base_path; ?>cloudloader/css/controls.css" rel="stylesheet">
    <link href="<?php echo $base_path; ?>cloudloader/css/animations.css" rel="stylesheet">
    <link href="<?php echo $base_path; ?>cloudloader/css/awesome/css/font-awesome.min.css" rel="stylesheet">


    <style>
        .device:after {
            background-image: -moz-linear-gradient(center top, rgba(250, 250, 250, 0) 0%, #ECF0F1 100%);
            bottom: 0;
            content: "";
            height: 40%;
            left: 0;
            position: absolute;
            width: 100%;
            z-index: 1;
        }

        .device {
            height: 440px;
            margin-left: -24px;
            margin-top: 20px;
            overflow: hidden;
            position: relative;
            width: 987px;
        }

        .device img {
            animation: 600ms ease-out 0s normal none 1 flyUp;
            display: inline-block;
            vertical-align: middle;
        }

    </style>

</head>
<body class="js">

<div id="wrap">
    <!-- Header -->
    <header>
        <div class="container" id="containerHeader">


            <div class="row">
                <div class="col-md-12">

                    <!-- Logo -->
                    <h1>MailBeez</h1>

                </div>
            </div>

        </div>

        <!-- Title -->
        <section class="title">
            <div class="container" id="containerTitle">
                <div class="row">
                    <div class="col-xs-7">

                        <!-- Heading -->
                        <h2 class="animate move_right"><?php echo MAILBEEZ_INSTALL_PACKAGE_TITLE; ?></h2>

                    </div>
                    <div class="col-xs-5 visible-xs visible-sm visible-md visible-lg">
                        <!-- Step progress -->
                        <div class="steps row animate move_up">
                            <div class="col-xs-4"><p>1</p></div>
                            <div class="col-xs-4"><p>2</p></div>
                            <div class="col-xs-4"><p>3</p></div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

    </header>

    <!-- Body -->
    <section class="body">

        <div class="container" id="containerBody">

        <pre>

            Check / Enter API key

            url calls cloudbeez api
            gets zip archive based on plan
            unzip


            check if API key:
            <a href="<?php echo $install_url; ?>">install</a>


        </pre>

            <?php echo $inc_content; ?>
        </div>

    </section>


</div>

<!-- Footer -->
<footer>
    <div class="container" id="containerFooter"></div>
</footer>

<?php echo $inc_content_common; ?>
</body>
</html>


