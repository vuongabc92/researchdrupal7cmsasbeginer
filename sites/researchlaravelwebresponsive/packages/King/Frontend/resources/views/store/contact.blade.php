@extends('frontend::layouts._frontend')

@section('title')
{{ _t('store_title') }}
@stop

@section('content')
<div class="_mw970 _ma" data-auto-refresh data-slug="{{ $store->slug }}">
    <div class="_fwfl store-container">
        @include('frontend::inc.store-header')

        <div class="_fwfl store-body store-contact-body">
            <div class="_fwfl contact-map" id="contact-map"></div>
            <div class="_fwfl _mt10 contact-info">
                <div class="_fwfl contact-field">
                    <i class="_fl _mr15 _r50 _tg5 contact-field-icon glyphicon glyphicon-map-marker"></i> 
                    <span class="_fl _tg5 contact-field-text">{{ store_address($store) }}</span>
                </div>
                <div class="_fwfl contact-field">
                    <i class="_mr15 _r50 _tg5 contact-field-icon fa fa-phone"></i> 
                    <span class="_tg5 contact-field-text">0998976755</span>
                </div>
                <div class="_fwfl _mt10 _p15 contact-other-info">
                    <h4 class="_fwfl _tg5 _fs17">About the store</h4>
                    <div class="_fwfl _mt10">
                        <textarea class="_fwfl setting-form-field"></textarea>
                    </div>
                    <button class="_fr _mt10 btn _btn _btn-sm _btn-blue1 _r2">Save about info</button>
                    <button class="_fr _mt10 btn _btn _btn-sm _btn-white _r2 _mr10">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="_csrf_token" data-csrf-token="{{ csrf_token() }}"/>
</div>
@stop

@section('js')
<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
<script src="{{ asset('tinymce/tinymce.min.js') }}"></script>
<script>

    function initMap() {
        var longLat = {lat: -33.8666, lng: 151.1958},
        map = new google.maps.Map(document.getElementById('contact-map'), {
            zoom: 17,
            center: longLat,
            mapTypeControlOptions: {
                mapTypeIds: [
                    google.maps.MapTypeId.ROADMAP,
                    google.maps.MapTypeId.SATELLITE
                ],
                position: google.maps.ControlPosition.BOTTOM_LEFT
            }
        });

        var marker = new google.maps.Marker({
            map: map,
            position: longLat,
            visible: true
        });

        google.maps.event.addListener(map, 'click', function(event) {
            $('#longitude').text(event.latLng.lng());
            $('#latitude').text(event.latLng.lat());

            placeMarker(event.latLng);
        });

        function placeMarker(location) {
            if (marker === undefined) {
                marker = new google.maps.Marker({
                    position: location,
                    map: map,
                    animation: google.maps.Animation.DROP,
                });
            } else {
                marker.setPosition(location);
            }
            map.setCenter(location);
        }
    }

    initMap();
</script>
@stop

@section('head_css')
@stop