@extends('frontend::layouts._frontend')

@section('title')
{{ _t('store_title') }}
@stop

@section('content')
<div class="_mw970 _ma" data-auto-refresh data-slug="{{ $store->slug }}">
    <div class="_fwfl store-container">
        @include('frontend::inc.store-header')

        <div class="_fwfl store-body store-contact-body">
            <div class="_fwfl contact-map"></div>
            <div class="_fwfl _mt10 contact-info">
                <div class="_fwfl contact-field">
                    <i class="_fl _r50 _tg5 contact-field-icon glyphicon glyphicon-map-marker"></i> 
                    <span class="_fl _ml15 _tg5 contact-field-text">{{ store_address($store) }}</span>
                </div>
                <div class="_fwfl contact-field">
                    <i class="_fl _r50 _tg5 contact-field-icon fa fa-phone"></i> 
                    <span class="_fl _ml15 _tg5 contact-field-text">0998976755</span>
                </div>
                <div class="_fwfl _mt10 _p15 contact-other-info">
                    <h4 class="_fwfl _tg5 _fs17">Other info</h4>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="_csrf_token" data-csrf-token="{{ csrf_token() }}"/>
</div>
@stop

@section('js')
@stop

@section('head_css')
@stop