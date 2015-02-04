/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	config.language = 'en';
	config.uiColor = '#2A7987'; //2A7987

	config.extraPlugins = 'mediaembed,imagemap'; //Youtube etc
	//config.extraPlugins = 'mp3player';

	// Remove some buttons, provided by the standard plugins
	//config.removeButtons = 'Save,NewPage,Form,Checkbox,RadioButton,TextField,Textarea,SelectionField, Button, ImageButton, HiddenField, RemoveFormat, Flash, IFrame, ShowBlocks, AboutCkEditor';	

	config.toolbar = 
	[
	    ['Templates', '-', 'Preview'],
	    ['Cut', 'Copy', 'Paste', '-', 'PasteText', 'PasteFromWord'],
	    ['Undo', 'Redo'],
	    ['Print'],
	    ['Find', 'Replace'],
	    ['SelectAll', '-', 'Scayt'],
	    ['Maximize'],
	    ['Source'],
	    '/',
	    ['Bold', 'Italic', 'Underline', 'Strike', '-', 'Superscript', 'Subscript', '-', 'RemoveFormat'], 
	    ['Link', 'Unlink', 'Anchor'], 
	    ['NumberedList', 'BulletedList'],
	    ['Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
	    '/',
	    ['Format', 'Font', 'FontSize'],
	    ['TextColor', 'BGColor'],
	    ['Image', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak'],
	    ['MediaEmbed', '-', 'ImageMap']
	];

	config.width = 633;  
	config.height = 350;    

	config.colorButton_colors = '000,800000,8B4513,2F4F4F,008080,000080,4B0082,696969,B22222,A52A2A,DAA520,006400,40E0D0,0000CD,800080,808080,F00,FF8C00,FFD700,008000,0FF,00F,EE82EE,A9A9A9,FFA07A,FFA500,FFFF00,00FF00,AFEEEE,ADD8E6,DDA0DD,D3D3D3,FFF0F5,FAEBD7,FFFFE0,F0FFF0,F0FFFF,F0F8FF,E6E6FA,FFFFFF,DD642D,0091B1,FBAD17,6F3092,4B7031,A70531';

	//config.skin = 'kama';

	//config.contentsCss = ['contents.css', '/carecss/preview.css', '/carecss/carecss/dynamicstyles.css'];

	//config.format_h1 = { element: 'h2', attributes: { 'class': 'contentTitle1' } };

	config.allowedContent = true;

};
