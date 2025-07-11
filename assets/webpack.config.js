/**
 * Webpack configuration.
 */
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const { getWebpackEntryPoints } = require("@wordpress/scripts/utils/config");
const path = require('path');

// JS Directory path.
const JS_DIR = path.resolve(__dirname, 'src/js');
const CSS_DIR = path.resolve(__dirname, 'src/sass');

const entry = {
	...getWebpackEntryPoints(),
	main: JS_DIR + '/main.js',
	admin: JS_DIR + '/admin.js',
	'js/public-page-preview/index': JS_DIR + '/public-page-preview/index.js',
	'js/admin/password': JS_DIR + '/admin/password.js',
	'js/admin/activity-logs-admin': JS_DIR + '/admin/activity-logs-admin.js',
	'js/frontend/lazy-load-embeds': JS_DIR + '/frontend/lazy-load-embeds.js',
	'css/admin/password': CSS_DIR + '/admin/password.scss',
	'css/frontend/maintenance': CSS_DIR + '/frontend/maintenance.scss',
	'css/frontend/account-menu': CSS_DIR + '/frontend/account-menu.scss',
	'css/admin/dashboard-widget': CSS_DIR + '/admin/_dashboard-widget.scss',
	'css/admin/admin-color-branding-colors': CSS_DIR + '/admin/admin-css-color-scheme/_colors.scss',
	'css/admin/maintainance-mode': CSS_DIR + '/admin/_maintainance-mode.scss',
	'css/admin/activity-logs-admin': CSS_DIR + '/admin/activity-logs-admin.scss',
	'css/admin/pingback-admin': CSS_DIR + '/admin/pingback-admin.scss',
	'css/admin/hide-comments-admin': CSS_DIR + '/admin/hide-comments-admin.scss',
	'css/admin/customize-list-tables': CSS_DIR + '/admin/customize-list-tables.scss',
	'css/frontend/network-default-template': CSS_DIR + '/frontend/_network-default-template.scss',
};

const rules = [
	...defaultConfig.module.rules,
	{
		test: /\.(bmp|png|jpe?g|gif|webp)$/i,
		type: 'asset/resource',
		generator: {
			filename: 'images/[name].[hash:8][ext]',
			publicPath: '/wp-content/plugins/better-by-default/assets/build/',
		},
	},
	{
		test: /\.(woff|woff2|eot|ttf|otf)$/i,
		type: 'asset/resource',
		generator: {
			filename: 'fonts/[name].[hash:8][ext]',
			publicPath: '/wp-content/plugins/better-by-default/assets/build/',
		},
	},
];

/**
 * Since you may have to disambiguate in your webpack.config.js between development and production builds,
 * you can export a function from your webpack configuration instead of exporting an object
 *
 * @param {string} env environment ( See the environment options CLI documentation for syntax examples. https://webpack.js.org/api/cli/#environment-options )
 * @param argv options map ( This describes the options passed to webpack, with keys such as output-filename and optimize-minimize )
 * @return {{output: *, devtool: string, entry: *, optimization: {minimizer: [*, *]}, plugins: *, module: {rules: *}, externals: {jquery: string}}}
 *
 * @see https://webpack.js.org/configuration/configuration-types/#exporting-a-function
 */
module.exports = (env, argv) => ({
	...defaultConfig,
	entry,

	module: {
		...defaultConfig.module,
		rules,
	},

	externals: {
		jquery: 'jQuery',
	},
});
