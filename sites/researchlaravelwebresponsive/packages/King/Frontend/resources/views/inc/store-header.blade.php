<div class="_fwfl store-header">
    <div class="_fwfl store-cover">
        <div class="_fwfl _fh store-cover-img cover-big" style="background-image:url('{{ get_cover('big', $store->slug) }}')">
            <ul class="_fr _ls">
                @if($storeOwner)
                <li>
                    <button class="_fr _m10 _btn-black-opacity _r50 choose-cover-btn" data-event-trigger="#cover-file" data-event="click|click">
                        <b class="fa fa-pencil"></b>
                    </button>
                </li>
                <li>
                    <button class="_fr _btn-black-opacity _r50 store-add-product-btn" data-toggle="modal" data-target="#add-product-modal">
                        <b class="fa fa-plus"></b>
                    </button>
                </li>
                <div class="_fwfl _dn">
                    {!! Form::open(['route' => 'front_setting_change_cover', 'files' => true, 'method' => 'POST', 'id' => 'upload-cover-form', 'data-upload-cover']) !!}
                    {!! Form::file('__file', ['class' => 'field-file-hidden', 'id' => 'cover-file', 'accept' => 'image/*', 'data-event-trigger' => '#upload-cover-form', 'data-event' => 'change|submit']) !!}
                    {!! Form::close() !!}
                </div>
                @endif
                <li>
                    <a class="_fr _btn-black-opacity _r50 store-nav-contact" href="{{ route('front_store_contact', $store->slug) }}">
                        <b class="fa fa-map-marker _fs15"></b>
                    </a>
                </li>
            </ul>
            <img class="change-cover-loading" id="change-cover-loading" src="{{ asset('packages/king/frontend/images/loading-black-opacity-24x24.gif') }}" />
        </div>
    </div>
    <div class="_fwfl store-nav-bar">
        <ul class="_fl _fh _ls store-nav-list">
            <li>
                <a href="{{ route('front_astore', $store->slug) }}" title="{{ _t('store_product') }}">
                    <span>{{ _t('store_product') }}</span>
                    <span class="store-nav-count">{{ $store->products->count() }}</span>
                </a>
            </li>
            <li>
                <a href="#" title="{{ _t('store_followers') }}">
                    <span>{{ _t('store_followers') }}</span>
                    <span class="store-nav-count">22</span>
                </a>
            </li>
            <li>
                <a href="{{ route('front_store_contact', $store->slug) }}" title="{{ _t('store_contact') }}" class="store-nav-icon">
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