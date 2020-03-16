<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/
?>

  </div>

  <?php 
  if (tep_session_is_registered('admin')) require('includes/footer.php');

  echo $OSCOM_Hooks->call('siteWide', 'injectSiteEnd');
  ?>

  </div>
</div>

<?php
echo $OSCOM_Hooks->call('siteWide', 'injectBodyEnd');
?>

</body>
</html>
