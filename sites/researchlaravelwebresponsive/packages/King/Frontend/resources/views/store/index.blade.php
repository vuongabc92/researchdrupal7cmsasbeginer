@extends('frontend::layouts._frontend')

@section('title')
{{ _t('store_title') }}
@stop

@section('content')
<div class="_mw970 _ma">
    <div class="_fwfl store-container">
        <div class="_fwfl store-header">
            <div class="_fwfl store-cover">
                <div class="_fwfl _fh store-cover-img cover-big" style="background-image:url('{{ get_cover('big', $store->slug) }}')">
                    @if($storeOwner)
                    <button class="_fr _m10 btn _btn-sm _btn-black-opacity choose-cover-btn" data-event-trigger="#cover-file" data-event="click|click">
                        <img class="loading-in-btn-sm" src="{{ asset('packages/king/frontend/images/loading-black-opacity-24x24.gif') }}" />
                        <!--<b>{{ _t('store_change_cover') }}</b>-->
                        <b>{{ _t('store_change_cover') }}</b>
                        <i class="fa fa-check _dn"></i>
                    </button>
                    <div class="_fwfl _dn">
                        {!! Form::open(['route' => 'front_setting_change_cover', 'files' => true, 'method' => 'POST', 'id' => 'upload-cover-form', 'data-upload-cover']) !!}
                        {!! Form::file('__file', ['class' => 'field-file-hidden', 'id' => 'cover-file', 'accept' => 'image/*', 'data-event-trigger' => '#upload-cover-form', 'data-event' => 'change|submit']) !!}
                        {!! Form::close() !!}
                    </div>
                    @endif
                </div>
            </div>
            <div class="_fwfl store-nav-bar">
                <ul class="_fl _fh _ls store-nav-list">
                    <li>
                        <a href="{{ route('front_astore', $store->slug) }}" title="{{ _t('store_product') }}">
                            <span>{{ _t('store_product') }}</span>
                            <span class="store-nav-count">{{ $productCount }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" title="{{ _t('store_followers') }}">
                            <span>{{ _t('store_followers') }}</span>
                            <span class="store-nav-count">22</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" title="{{ _t('store_contact') }}" class="store-nav-icon">
                            <i class="fa fa-map-marker"></i>
                        </a>
                    </li>
                    @if($storeOwner)
                    <li data-toggle="modal" data-target="#add-product-modal">
                        <a href="javascript:;" class="add-product-nav store-nav-icon" title="{{ _t('add_product') }}">
                            <i class="fa fa-plus"></i>
                        </a>
                    </li>
                    @endif
                </ul>
                <button class="_fr btn _btn _btn-blue _btn-sm _m12"><i class="fa fa-thumbs-o-up"></i> {{ _t('store_follow') }}</button>
            </div>
        </div>

        <div class="_fwfl store-body">
            <ol class="_fwfl _ls product-tree" id="product-tree" data-pin-uri="{{ route('front_product_pin') }}">
                @set $i = 1
                @foreach( $products as $product )
                    @set $image = $product->toImage()

                    @if( ! is_null($product->pin) && $product->pin->isPinned())
                        @set $pinned = 'pinned'
                    @else
                        @set $pinned = ''
                    @endif
                    <li class="{{ (($i++)%3 === 0) ? 'the-3th-product' : '' }}">
                        <div class="product product-{{ $product->id }} {{ $product->id }}" data-product-id="{{ $product->id }}">
                            <div class="product-head">
                                <ul class="product-handle">
                                    <li>
                                        <button class="product-pin {{ $pinned }}" {{ (auth()->check()) ? 'data-pin-product' : '' }} data-slug="{{ $store->slug }}">
                                            <i class="fa fa-thumb-tack"></i>
                                            <b>{{ $product->total_pin }}</b>
                                        </button>
                                    </li>
                                    <li>
                                        <button class="product-share">
                                            <i class="fa fa-share-alt"></i>
                                            <b>42</b>
                                        </button>
                                    </li>
                                    <li>
                                        <button class="product-comment {{ (auth()->check() && $product->isCommented(user()->id)) ? 'commented' : '' }}">
                                            <i class="fa fa-comments-o"></i>
                                            <b>{{ $product->comments->count() }}</b>
                                        </button>
                                    </li>
                                </ul>
                                @if($storeOwner)
                                <div class="product-control">
                                    <div class="btn-group">
                                        <i class="fa fa-gear product-config-btn _r2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                                        <ul class="dropdown-menu product-control-drop">
                                            <li>
                                                <a href="{{ route('front_find_product_by_id', ['id' => $product->id, 'store_slug' => $store->slug, 'type' => 'edit']) }}" data-edit-product-form class="product-edit" data-toggle="tooltip" data-placement="left" data-original-title="{{ _t('product_edit') }}">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                            </li>
                                            <li><a href="#" class="product-hide" data-toggle="tooltip" data-placement="left" data-original-title="{{ _t('product_hide') }}"><i class="fa fa-genderless"></i></a></li>
                                            <li>
                                                <a href="{{ route('front_delete_product') }}" data-delete-product="" class="product-remove" data-toggle="tooltip" data-placement="left" data-original-title="{{ _t('product_remove') }}">
                                                    <i class="fa fa-close"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="product-body">
                                <div class="product-image">
                                    <a href="{{ route('front_product_quick_view', ['id' => $product->id, 'store_slug' => $store->slug]) }}" data-product-quick-view>
                                        <img src="{{ ($product->image_1 !== null) ? asset($productPath . $product->image_1->medium) : '' }}" alt="{{ $product->name }}" />
                                    </a>
                                </div>
                                <div class="product-info">
                                    <span class="product-name-box">
                                        <a href="#" class="product-name" title="{{ $product->name }}">{{ str_limit($product->name, 70) }}</a>
                                    </span>
                                    <div class="_fwfl _mt5">
                                        <span class="product-price"><b class="_fwn">{{ product_price($product->price) }}</b> <sup>đ</sup></span>
                                        @if( $product->old_price !== null )
                                        <span class="product-old-price"><b class="_fwn">{{ product_price($product->old_price) }}</b> <sup>đ</sup></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
    <input type="hidden" id="_csrf_token" data-csrf-token="{{ csrf_token() }}"/>
</div>
    @if($storeOwner)
    @include('frontend::inc.save-product-popup', ['slug' => $store->slug])
    @endif
@include('frontend::inc.quick-view-product-popup', ['slug' => $store->slug])
@stop

@section('js')
<script src="{{ asset('packages/king/frontend/js/owl.carousel.js') }}"></script>
<script>

    $('#add-product-tooltip').tooltip();
    $('#search-product-tooltip').tooltip();
    $('.product-edit').tooltip();
    $('.product-hide').tooltip();
    $('.product-remove').tooltip();
    $(document).ready(function() {

    });

</script>
@stop

@section('head_css')
<link rel="stylesheet" href="{{ asset('packages/king/frontend/css/owl.carousel.css') }}">
<link rel="stylesheet" href="{{ asset('packages/king/frontend/css/owl.theme.css') }}">
@stop