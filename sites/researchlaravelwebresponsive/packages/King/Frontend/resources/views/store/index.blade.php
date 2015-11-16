@extends('frontend::layouts._frontend')

@section('title')
{{ _t('store_title') }}
@stop

@section('content')
<div class="_mw970 _ma" data-auto-refresh data-slug="{{ $store->slug }}">
    <div class="_fwfl store-container">
        @include('frontend::inc.store-header')

        <div class="_fwfl store-body">
            <ol class="_fwfl _ls product-tree" id="product-tree" data-quantity="{{ $store->products->count() }}" data-pin-uri="{{ route('front_product_pin') }}">
                @set $i = 0
                @foreach( $products as $product )
                    @set $image = $product->toImage()
                    @set $i = $i + 1
                    @if( ! is_null($product->pin) && $product->pin->isPinned())
                        @set $pinned = 'pinned'
                    @else
                        @set $pinned = ''
                    @endif

                    <li>
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
                                        <button class="product-share" data-toggle="modal" data-target=".share-product-modal">
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
@include('frontend::inc.product-share-modal', ['slug' => $store->slug])
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
        $('.product-comment').on('click', function(e){
            $('#qvp-comment-input').focus();
        });
        $('#qvp-comment-input').on('focus', function(e){
            $('.product-comment-tree').animate({
                scrollTop: $('.product-comment-tree li:last-child').offset().top
            });
        });
    });

</script>
@stop

@section('head_css')
<link rel="stylesheet" href="{{ asset('packages/king/frontend/css/owl.carousel.css') }}">
<link rel="stylesheet" href="{{ asset('packages/king/frontend/css/owl.theme.css') }}">
@stop