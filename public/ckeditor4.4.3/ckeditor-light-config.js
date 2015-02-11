/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	config.language = 'en-gb';
	config.toolbar = 'Full';
	 
	config.toolbar_Full =
	[
		{ name: 'document', items : ['Preview'] },
		{ name: 'clipboard', items : [ 'Cut','Copy','Paste'] },
		{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline' ] },
		{ name: 'html', items : ['Source'] },
		{ name: 'styles', items : [ 'Styles','Format' ] },
		{ name: 'colors', items : [ 'TextColor','BGColor' ] },
		{ name: 'insert', items : [ 'Image','Table','HorizontalRule','SpecialChar'] }
	];

	config.format_tags = 'p;h1;h2;h3;pre';
	config.allowedContent = true;		
};
