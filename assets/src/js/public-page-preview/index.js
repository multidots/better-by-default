/**
 * WordPress dependencies
 */
 import { registerPlugin } from '@wordpress/plugins';

 /**
  * Internal dependencies
  */
 import PreviewToggle from './component/index';
 
 registerPlugin( 'public-page-preview', {
	 render: PreviewToggle,
 } );
 