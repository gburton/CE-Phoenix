<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

define('MODULE_ADMIN_CURRENCIES_ECB_TITLE', 'ECB');
define('MODULE_ADMIN_CURRENCIES_ECB_DESCRIPTION', 'Update Currencies via ECB.<div class=\'alert alert-warning\'>Uses data from https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml which is updated daily at about 5pm European Time.<br><br>Covers the following currencies:<br>EUR, USD, JPY, BGN, CZK, DKK, GBP, HUF, PLN, RON, SEK, CHF, ISK, NOK, HRK, RUB, TRY, AUD, BRL, CAD, CNY, HKD, IDR, ILS, INR, KRW, MXN, MYR, NZD, PHP, SGD, THB, ZAR</div>');

// good
define('MODULE_ADMIN_CURRENCIES_ECB_CURRENCIES_UPDATED', 'The exchange rate for %s was updated successfully via ECB.');

// bad
// nothing