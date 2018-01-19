window.BC_RSS_Feed_Parser_Ancient = window.BC_RSS_Feed_Parser_Ancient || {};

(function (window, document, $, app, undefined) {
    'use strict';

	/**
	 * Caches elements
	 * @return null
	 */
    app.cache = function cache() {
        app.$ = {};
    };

	/**
	 * Initialization function
	 * @return  null
	 */
    app.init = function init() {
        app.cache();

        // Bail early if requirements aren't met.
        if ( ! app.meetsRequirements() ) {
            return;
        }
    };

	/**
	 * Determine if this script should run on the page.
	 *
	 * @return bool Whether or not the script should run on the page.
	 */
    app.meetsRequirements = function meetsRequirements() {
        return true;
    };

    // fire init on document.ready
    $( document ).ready( app.init );

    return app;

})( window, document, jQuery, window.BC_RSS_Feed_Parser_Ancient );
