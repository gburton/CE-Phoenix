<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/
?>

  </div>

  <?php
  if (isset($_SESSION['admin'])) {
    require 'includes/footer.php';
  }

  echo $OSCOM_Hooks->call('siteWide', 'injectSiteEnd');
  ?>

  </div>
</div>

<?= $OSCOM_Hooks->call('siteWide', 'injectBodyEnd') ?>

</body>
</html>
