<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>OSCOM, Starting Your Online Business with CE Phoenix</title>
    <meta name="robots" content="noindex,nofollow" />
    <link rel="icon" type="image/png" href="images/icon_phoenix.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.3/css/bootstrap.min.css" integrity="sha512-oc9+XSs1H243/FRN9Rw62Fn8EtxjEYWHXRvjS43YtueEewbS6ObfXcJNyohjHqVKFPoXXUxwc+q1K7Dee6vv9g==" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA==" crossorigin="anonymous" />
    <link rel="stylesheet" href="templates/main_page/stylesheet.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
  </head>

  <body>
    <div class="container">
      <div class="row">
        <div id="storeLogo" class="col-sm-6">
          <a href="index.php"><img src="images/phoenix.png" title="OSCOM CE Phoenix" style="margin: 10px 10px 0 10px;" /></a>
        </div>

        <div id="headerShortcuts" class="col-sm-6">
          <ul class="nav justify-content-end">
            <li class="nav-item"><a class="nav-link active" href="https://forums.oscommerce.com/clubs/1-phoenix/" target="_blank" rel="noreferrer">Website</a></li>
            <li class="nav-item"><a class="nav-link" href="https://forums.oscommerce.com/clubs/1-phoenix/" target="_blank" rel="noreferrer">Support</a></li>
          </ul>
        </div>
      </div>

      <hr>

      <?php require 'templates/pages/' . $page_contents; ?>

      <footer class="card bg-light mb-3 card-body text-center">OSCOM CE Phoenix &copy; 2000-<?= date('Y') ?></footer>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.3/js/bootstrap.min.js" integrity="sha512-8qmis31OQi6hIRgvkht0s6mCOittjMa9GMqtK9hes5iEQBQE/Ca6yGE5FsW36vyipGoWQswBj/QBm2JR086Rkw==" crossorigin="anonymous"></script>
  </body>
</html>
