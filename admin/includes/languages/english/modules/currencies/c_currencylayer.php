<?php
/*
  Copyright (c) 2018, G Burton
  All rights reserved. 

  Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

  1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

  2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

  3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

define('MODULE_ADMIN_CURRENCIES_CURRENCYLAYER_TITLE', 'Currency Layer');
define('MODULE_ADMIN_CURRENCIES_CURRENCYLAYER_DESCRIPTION', 'Update Currencies via the CurrencyLayer API.<div class="secWarning">Requires an API key from https://currencylayer.com</div>');

// good
define('MODULE_ADMIN_CURRENCIES_CURRENCYLAYER_CURRENCIES_UPDATED', 'The exchange rate for %s was updated successfully via Currency Layer.');

// error codes
define('CURRENCYLAYER_ERROR_404', 'Requested a resource which does not exist.');
define('CURRENCYLAYER_ERROR_101', 'Did not supply an access key or an invalid access key.');
define('CURRENCYLAYER_ERROR_103', 'Requested a non-existent API function.');
define('CURRENCYLAYER_ERROR_104', 'Reached or exceeded subscription plan.');
define('CURRENCYLAYER_ERROR_105', 'Subscription plan does not support the requested API Function.');
define('CURRENCYLAYER_ERROR_106', 'Query did not return any results.');
define('CURRENCYLAYER_ERROR_102', 'Account is not active. Please get in touch with CurrencyLayer Customer Support.');
define('CURRENCYLAYER_ERROR_201', 'Entered an invalid Source Currency.');
define('CURRENCYLAYER_ERROR_202', 'Entered one or more invalid currency codes.');
define('CURRENCYLAYER_ERROR_301', 'Did not specify a date.[historical]');
define('CURRENCYLAYER_ERROR_302', 'Entered an invalid date. [historical, convert]');
define('CURRENCYLAYER_ERROR_401', 'Entered an invalid \'from\' property. [convert]');
define('CURRENCYLAYER_ERROR_402', 'Entered an invalid \'to\' property. [convert]');
define('CURRENCYLAYER_ERROR_403', 'Entered no or an invalid \'amount\' property. [convert]');
define('CURRENCYLAYER_ERROR_501', 'Did not specify a Time-Frame [timeframe, change].');
define('CURRENCYLAYER_ERROR_502', 'Entered an invalid \'start_date\' property. [timeframe, change]');
define('CURRENCYLAYER_ERROR_503', 'Entered an invalid \'end_date\' property. [timeframe, change]');
define('CURRENCYLAYER_ERROR_504', 'Entered an invalid Time-Frame. [timeframe, change]');
define('CURRENCYLAYER_ERROR_505', 'Time-Frame specified by the user is too long - exceeding 365 days');
