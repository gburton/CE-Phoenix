<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>osCommerce, Starting Your Online Business</title>
    <meta name="robots" content="noindex,nofollow" />
    <link rel="icon" type="image/png" href="images/oscommerce_icon.png" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">
    <link rel="stylesheet" href="templates/main_page/stylesheet.css" />
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  </head>

  <body>
    <div class="container">
      <div class="row">
        <div id="storeLogo" class="col-sm-6">
          <a href="index.php"><img src="images/oscommerce.png" title="osCommerce Online Merchant" style="margin: 10px 10px 0 10px;" /></a>
        </div>

        <div id="headerShortcuts" class="col-sm-6">
          <ul class="nav justify-content-center">
            <li class="nav-item"><a class="nav-link active" href="http://www.oscommerce.com" target="_blank">osCommerce Website</a></li>
            <li class="nav-item"><a class="nav-link" href="http://www.oscommerce.com/support" target="_blank">Support</a></li>
            <li class="nav-item"><a class="nav-link" href="http://www.oscommerce.info" target="_blank">Documentation</a></li>
          </ul>
        </div>
      </div>
      
      <hr>

      <?php require('templates/pages/' . $page_contents); ?>

      <footer>
        <div class="card bg-light mb-3 card-body text-center"><p>osCommerce Online Merchant Copyright &copy; 2000-<?php echo date('Y'); ?> <a href="http://www.oscommerce.com" target="_blank">osCommerce</a> (<a href="http://www.oscommerce.com/Us&amp;Legal" target="_blank">Copyright and Trademark Policy</a>)</p></div>
      </footer>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  </body>
</html>
