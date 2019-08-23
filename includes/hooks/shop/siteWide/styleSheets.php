<?php
/*
  Copyright (c) 2019, G Burton
  All rights reserved.

  Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

  1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

  2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

  3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

class hook_shop_siteWide_styleSheets {
  var $sitestart = null;
  
  function listen_injectSiteStart() {
    $this->sitestart .= '<!-- stylesheets hooked -->' . PHP_EOL;
    $this->sitestart .= '<style>* {min-height: 0.01px;}.form-control-feedback { position: absolute; width: auto; top: 7px; right: 45px; margin-top: 0; }@media (max-width: 575.98px) {.display-1 {font-size: 3rem;font-weight: 300;line-height: 1.0;}.display-2 {font-size: 2.75rem;font-weight: 300;line-height: 1.0;}.display-3 {font-size: 2.25rem;font-weight: 300;line-height: 1.0;}.display-4 {font-size: 1.75rem;font-weight: 300;line-height: 1.0;}h4 {font-size: 1rem;}}</style>' . PHP_EOL;
    $this->sitestart .= '<link href="user.css" rel="stylesheet">' . PHP_EOL;

    return $this->sitestart;
  }

}
