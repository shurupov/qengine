(function(){

    if ($('#gmap').length > 0 && latlng != undefined) {

        var map;

        map = new GMaps({
            el: '#gmap',
            lat: latlng.lat,
            lng: latlng.lng,
            scrollwheel:false,
            zoom:15,
            zoomControl : true,
            panControl : false,
            streetViewControl : true,
            mapTypeControl: false,
            overviewMapControl: false,
            clickable: false
        });

        var image = '/templates/diagram/images/map-icon.png';
        map.addMarker({
            lat: latlng.lat,
            lng: latlng.lng,
            icon: image,
            animation: google.maps.Animation.DROP,
            verticalAlign: 'bottom',
            horizontalAlign: 'center',
            backgroundColor: '#d3cfcf',
        });

    }

}());