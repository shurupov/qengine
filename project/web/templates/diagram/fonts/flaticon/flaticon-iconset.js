/*!========================================================================
 * Iconset: Flaticons
 * ======================================================================== */

;(function($){

    var data = {
        iconClass: '',
        iconClassFix: 'flaticon-',
        icons: [],
        allVersions: [
            {
                version: '1',
                icons: [
                    'crane-transporting-construction-material-for-a-building',
                    'framework',
                    'bracket',
                    'tap',
                    'toxic-tank-container-with-ecological-risk',
                    'oil',
                    'refinery',
                    'brickwall-1',
                    'brickwall',
                    'gas-pipe-1',
                    'television-remote-control',
                    'gas-pipe',
                    'winch'
                ]
            }
        ]
    };

    var l = data.allVersions.length;
    data.icons = data.allVersions[l-1].icons;

    $.iconset_flaticon = data;

    // Iconpicker.ICONSET.flaticon = $.iconset_flaticon || Iconpicker.ICONSET_EMPTY;

})(jQuery);