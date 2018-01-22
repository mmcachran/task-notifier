window.BC_RSS_Feed_Parser_Ancient = window.BC_RSS_Feed_Parser_Ancient || {};

(function (window, document, $, app, undefined) {
    'use strict';

	/**
	 * Caches elements
	 * @return null
	 */
    app.cache = function cache() {
        app.$ = {};

        // Search wrapper.
        app.$.search_wrapper = $(document.getElementById( 'search-wrapper' ) );
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

        app.$.search_wrapper.find( 'input.search' ).on( 'keyup', app.filterResults );
    };

    /**
     * Filter results when a search keywork is inputted.
     *
     * @param object evt The JS event object.
     * @return void
     */
    app.filterResults = function( evt ) {
        // Get the search keyword.
        var keyword = $( this ).val();

        // Bail early if no keyword.
        if ( '' === keyword ) {
            $('.project').find( '.tr' ).show();
            return;
        }

        $( '.project' ).find( '.table .tr' ).each( function() {
            var $row = $( this );

            // Hide by default.
            var hide = true;

            $row.find( '.td' ).each( function() {
                var $column = $( this );

                if ( ! hide || ( -1 === $column.text().toLowerCase().indexOf( keyword.toLowerCase() ) ) ) {
                    return;
                }

                // This row should display.
                hide = false;
            });

            // Hide the row if no matches are found.
            if ( hide ) {
                $row.hide();
            }
        });
    }; 

	/**
	 * Determine if this script should run on the page.
	 *
	 * @return bool Whether or not the script should run on the page.
	 */
    app.meetsRequirements = function meetsRequirements() {
        return true;
    };

    // Fire init on document.ready.
    $( document ).ready( app.init );

    return app;

})( window, document, jQuery, window.BC_RSS_Feed_Parser_Ancient );
