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

    <title>OSCOM, Starting Your Online Business with CE Phoenix</title>
    <meta name="robots" content="noindex,nofollow" />
    <link rel="icon" type="image/png" href="images/oscommerce_icon.png" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-1/css/all.min.css" integrity="sha256-4w9DunooKSr3MFXHXWyFER38WmPdm361bQS/2KUWZbU=" crossorigin="anonymous" />
    <link rel="stylesheet" href="templates/main_page/stylesheet.css" />
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  </head>

  <body>
    <div class="container">
      <div class="row">
        <div id="storeLogo" class="col-sm-6">
          <a href="index.php"><img src="images/oscommerce.png" title="OSCOM CE Phoenix" style="margin: 10px 10px 0 10px;" /></a>
        </div>

        <div id="headerShortcuts" class="col-sm-6">
          <ul class="nav justify-content-end">
            <li class="nav-item"><a class="nav-link active" href="http://www.oscommerce.com" target="_blank">osCommerce Website</a></li>
            <li class="nav-item"><a class="nav-link" href="http://www.oscommerce.com/support" target="_blank">Support</a></li>
            <li class="nav-item"><a class="nav-link" href="http://www.oscommerce.info" target="_blank">Documentation</a></li>
          </ul>
        </div>
      </div>
      
      <hr>

      <?php require('templates/pages/' . $page_contents); ?>

      <footer>
        <div class="card bg-light mb-3 card-body text-center"><p>OSCOM CE Phoenix &copy; 2000-<?php echo date('Y'); ?> <a href="http://www.oscommerce.com" target="_blank">osCommerce</a> (<a href="http://www.oscommerce.com/Us&amp;Legal" target="_blank">Copyright and Trademark Policy</a>)</p></div>
      </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>   
  </body>
</html>
