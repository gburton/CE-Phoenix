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

define('MODULE_ADMIN_CURRENCIES_FIXER_TITLE', 'Fixer');
define('MODULE_ADMIN_CURRENCIES_FIXER_DESCRIPTION', 'Update Currencies via the Fixer API.<div class=\'secWarning\'>Requires an API key from https://fixer.io</div>');

// good
define('MODULE_ADMIN_CURRENCIES_FIXER_CURRENCIES_UPDATED', 'The exchange rate for %s was updated successfully via Fixer.');

// error codes
define('FIXER_ERROR_404', 'The requested resource does not exist.');
define('FIXER_ERROR_101', 'No API Key was specified or an invalid API Key was specified.');
define('FIXER_ERROR_103', 'The requested API endpoint does not exist.');
define('FIXER_ERROR_104', 'The maximum allowed API amount of monthly API requests has been reached, you may need to upgrade your plan.');
define('FIXER_ERROR_105', 'The current subscription plan does not support this API endpoint, you may need to upgrade your plan.');
define('FIXER_ERROR_106', 'The current request did not return any results.');
define('FIXER_ERROR_102', 'The account this API request is coming from is inactive.');
define('FIXER_ERROR_201', 'An invalid base currency has been entered.');
define('FIXER_ERROR_202', 'One or more invalid symbols have been specified.');
define('FIXER_ERROR_301', 'No date has been specified. [historical]');
define('FIXER_ERROR_302', 'An invalid date has been specified. [historical, convert]');
define('FIXER_ERROR_403', 'No or an invalid amount has been specified. [convert]');
define('FIXER_ERROR_501', 'No or an invalid timeframe has been specified. [timeseries]');
define('FIXER_ERROR_502', 'No or an invalid \'start_date\' has been specified. [timeseries, fluctuation]');
define('FIXER_ERROR_503', 'No or an invalid \'end_date\' has been specified. [timeseries, fluctuation]');
define('FIXER_ERROR_504', 'An invalid timeframe has been specified. [timeseries, fluctuation]');
define('FIXER_ERROR_505', 'The specified timeframe is too long, exceeding 365 days. [timeseries, fluctuation]');
