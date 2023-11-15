( function( $ ) {

    var initMap = function ( container, settings ) {
        var latlng = settings.defaultLatlng ? settings.defaultLatlng.split( ',' ) : [ 40.69847, -73.95144 ],
            zoom = settings.defaultZoom || 13,
            map,
            controlPosition = settings.mapControlPosition || 'bottomright';

        map = L.map( $( container ).get( 0 ), {
            zoomControl: false,
            maxZoom: 18,
            scrollWheelZoom: false,
        } ).setView( latlng, zoom );
        map.attributionControl.setPrefix( '' );
        L.tileLayer( 'https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        } ).addTo( map );
        L.control.zoom( { position: controlPosition } ).addTo( map );
        if (L.control.fullscreen) {
            map.addControl( new L.control.fullscreen( {
                position: controlPosition,
                forceSeparateButton: true,
            } ) );
        }

        return map;
    }

    var loadMarkers = function ( map, target, settings ) {
        var bounds = [];

        $( target ).find( $( settings.itemContainer ) ).each( function( i, e ) {
            var $item = $( e );
            $item.find( '.happyformsmap-map-latlng[data-part="' + settings.part + '"]' ).each( function( _i, _e ) {
                var $latlng = $( _e ),
                    latlng = $latlng.data( 'latlng' ) || $latlng.text();
                if ( latlng.length ) {
                    var marker = L.marker( latlng.split( ',' ) ).addTo( map ),
                        content = getItemContent( $item, settings );
                    if ( content.length ) marker.bindPopup( content, { maxWidth: 400, className: 'happyformsmap-map-popup' } );
                    bounds.push( marker.getLatLng() );
                }
            } );
        } );
        if ( bounds.length ) {
            map.fitBounds( bounds, { padding: [15, 15] } );
        }
    }

    var getItemContent = function ( item, settings ) {
        var $item = $(item), title = '', isTitle = false, content = '', url;

        $item.find( settings.itemContent ).each( function ( i, e ) {
            content += $( e ).prop( 'outerHTML' );
        } );
        if (content.length) {
            content = '<div class="happyformsmap-map-popup-content">' + content + '</div>';
        }
        if ( settings.itemTitle ) {
            title = $item.find( settings.itemTitle ).text();
            isTitle = true;
        }
        if ( settings.itemUrl ) {
            url = $item.find( settings.itemUrl );
            if ( url.length ) {
                if ( url[0].tagName === 'A' ) {
                    if ( ! title.length ) {
                        title = url.text();
                    }
                    url = url.attr( 'href' );
                } else {
                    url = url.text();
                    if ( ! title.length ) {
                        title = url;
                    }
                }
            }
        }
        if ( title.length && url.length ) {
            title = $( '<a></a>' ).attr( 'href', url ).text( title ).prop( 'outerHTML' );
            if ( isTitle ) {
                content = '<h3 class="happyformsmap-map-popup-title">' + title + '</h3>' + content;
            } else {
                content += '<div class="happyformsmap-map-popup-link">' + title + '</div>';
            }
        }

        return content;
    }
    
    var createMap = function ( target, settings ) {
        var $target = $( target ),
            $map = $target.parent().find( '.happyformsmap-map[data-part="' + settings.part + '"]' ),
            map;

        if ( ! $map.length ) {
            $map = $( '<div class="happyformsmap-map" data-part="' + settings.part + '" style="height:' + ( settings.mapHeight || 300 ) + 'px;"></div>' )
                .insertBefore( $target );
            if ( settings.mapClass ) {
                $map.addClass( settings.mapClass );
            }
            map = initMap( $map, settings );
            loadMarkers( map, $target, settings );
            $map.on( 'happyformsmap:hidden', function() { 
                map.closePopup(); 
            } );

            return $map;
        }

        return $map.first();
    }

    var mapExists = function ( target, settings ) {
        var $map = $( target ).parent().find( '.happyformsmap-map[data-part="' + settings.part + '"]' );

        return $map.length ? $map.first() : false;
    }

    var updateButton = function ( button, mapIsVisible ) {
        var $button = $(button);
        if ( mapIsVisible ) {
            if ( $button.data( 'label-close' ) ) {
                $button.data( 'label-open', $button.text() );
                $button.text( $button.data( 'label-close' ) );
            } else {
                $button.addClass( 'happyformsmap-map-trigger-open' );
            }
        } else {
            if ( $button.data( 'label-open' ) ) {
                $button.text( $button.data( 'label-open' ) );
            } else {
                $button.removeClass( 'happyformsmap-map-trigger-open' );
            }
        }
        $button.blur();
    }

    var closeOthersShown = function ( settings ) {
        $( '.happyformsmap-map-trigger.happyformsmap-map-trigger-open', settings.container ).each( function() {
            var $this = $( this );
            if ( $this.data( 'part' ) !== settings.part ) {
                $this.click();
            }
        } );
    }

    $( document ).on( 'click', '.happyformsmap-map-trigger', function( e ) {
        e.preventDefault();

        var $button = $( e.target ),
            settings = $button.data(),
            $target = $( settings.target, settings.container ),
            $map = mapExists( $target, settings );

        if ( $map ) {
            $map.slideToggle( 'fast', function() {
                var visible = $map.is( ':visible' );
                updateButton( $button, visible );
                if ( visible ) {
                    $map.trigger( 'happyformsmap:shown' );
                    closeOthersShown( settings );
                } else {
                    $map.trigger( 'happyformsmap:hidden' );
                }
            } );
        } else {
            $map = createMap( $target, settings );
            updateButton( $button, true );
            $map.trigger( 'happyformsmap:shown' );
            closeOthersShown( settings );
        }
    } );

} )( jQuery );