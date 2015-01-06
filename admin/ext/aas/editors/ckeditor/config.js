/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	
	config.enterMode = CKEDITOR.ENTER_BR,
  config.shiftEnterMode = CKEDITOR.ENTER_P
	config.language = 'en';
	config.entities = false; 
	config.entities_greek = false; 
	config.entities_latin = false;

	
};
