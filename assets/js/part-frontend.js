( function( $ ) {

    HappyForms.parts = HappyForms.parts || {};

    HappyForms.parts.map = {
        init: function() {
            this.type = this.$el.data( 'happyforms-type' );
            this.$input = $( 'input, select', this.$el );
            this.$latlng = $( '.happyforms-part__el .happyformsmap-latlng', this.$el );
            this.$zoom = $( '.happyforms-part__el .happyformsmap-zoom', this.$el );
            this.$map = $( '.happyforms-part__el .happyformsmap-map', this.$el );
            this.map = this.initMap();
            this.marker = this.initMarker();

            this.map.on( 'click', this.onClickMap.bind( this ) );
            this.map.on( 'zoomend', this.onZoomendMap.bind( this ) );
        },

        initMap: function () {
            var map = L.map( this.$map.get( 0 ) ).setView( this.getLatLng(), this.getZoom() );
            L.tileLayer( 'https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                scrollWheelZoom: false,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            } ).addTo( map );
            map.attributionControl.setPrefix( '' );

            return map;
        },

        initMarker: function () {
            return this.$latlng.val() ? this.createMarker( this.getLatLng() ) : null;
        },

        getLatLng: function () {
            var latlng = this.$latlng.val() || this.$map.data( 'initial-latlng' ),
                _latlng;
            if ( latlng.length > 0 ) {
                _latlng = latlng.split( ',' );
                if ( _latlng.length === 2 ) {
                    return _latlng.map(function ( coord ) { return coord.trim() });
                }
            }
            
            return [40.69847, -73.95144];
        },

        getZoom: function () {
            var zoom = this.$zoom.val() || this.$map.data( 'initial-zoom' );

            return zoom >= 0 && zoom <= 18 ? zoom : 13;
        },

        createMarker: function ( latlng ) {
            var marker = L.marker( latlng )
                .addTo( this.map )
                .on( 'click', this.removeMarker.bind( this ) );
            this.map.panTo( marker.getLatLng(), { duration: .25 } );
            return marker;
        },

        removeMarker: function () {
            if ( this.marker ) {
                this.map.removeLayer( this.marker );
                this.marker = null;
            }
            this.$latlng.val( '' );
            this.$zoom.val( '' );
        },

        onClickMap: function ( e ) {
            this.removeMarker();
            this.marker = this.createMarker( e.latlng );
            this.$latlng.val( e.latlng.lat + ',' + e.latlng.lng );
            this.$zoom.val( this.map.getZoom() );
        },

        onZoomendMap: function ( e ) {
            this.$zoom.val( this.map.getZoom() );
        },
    };

} )( jQuery );
