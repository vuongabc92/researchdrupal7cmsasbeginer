<div class="modal fade -qvp-modal" id="quick-view-product-modal" data-product-id="0" tabindex="-1" role="dialog" aria-labelledby="quickViewProductLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="_fwfl _bgw _r0 modal-content">
            <div class="-qvp-modal-left">
                <div class="_fwfl -qvp-handle-container -qvp-handle-lg modal-header">
                    <ul class="_ls _fl _fh product-handle -qvp-handle">
                        <li>
                            <button class="product-pin" {{ (auth()->check()) ? 'data-pin-trigger' : '' }}>
                                <i class="fa fa-thumb-tack"></i>
                                <b class="quick-view-product-pin">0</b>
                            </button>
                        </li>
                        <li>
                            <button class="product-share">
                                <i class="fa fa-share-alt"></i>
                                <b class="quick-view-product-share">0</b>
                            </button>
                        </li>
                        <li>
                            <button class="product-comment">
                                <i class="fa fa-comments-o"></i>
                                <b class="quick-view-product-comments">0</b>
                            </button>
                        </li>
                    </ul>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="_fwfl product-carousel-container -qvp-carousel">
                    <div id="product-carousel" class="owl-carousel"></div>
                    <b class="_fl fa fa-chevron-left -pcn -pcn-disabled -pcn-prev"></b>
                    <b class="_fr fa fa-chevron-right -pcn -pcn-disabled -pcn-next"></b>
                </div>
            </div>
            <div class="-qvp-modal-right">
                <div class="_fwfl -qvp-handle-container -qvp-handle-sm modal-header">
                    <ul class="_fl product-handle -qvp-handle">
                        <li>
                            <button class="product-pin" {{ (auth()->check()) ? 'data-pin-trigger' : '' }}>
                                <i class="fa fa-thumb-tack"></i>
                                <b class="quick-view-product-pin">0</b>
                            </button>
                        </li>
                        <li>
                            <button class="product-share">
                                <i class="fa fa-share-alt"></i>
                                <b class="quick-view-product-share">0</b>
                            </button>
                        </li>
                        <li>
                            <button class="product-comment">
                                <i class="fa fa-comments-o"></i>
                                <b class="quick-view-product-comments">0</b>
                            </button>
                        </li>
                    </ul>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="_fwfl -qvp-other-link">
                    <button class="_fl btn _btn _btn-blue1 _btn-sm">{{ _t('buy_it') }}</button>
                    <a href="#" class="_fr _fs13 _fwb _mt7 -qvp-details">{{ _t('details') }}</a>
                </div>
                <ul class="_fwfl _ls -qvp-comments-tree product-comment-tree" data-delete-comment-url="" data-delete-comment data-slug="{{ $slug }}">
                    <li><button class="-qvp-view-more-comments" id="qvp-load-comments" data-text="{{ _t('view_all_comments') }}" data-text-2="{{ _t('more_comments') }}" data-url data-load-before data-load-more-comments data-slug="{{ $slug }}"></button> <img src="{{ asset('packages/king/frontend/images/loading-blue-white-16x16.gif') }}" class="-qvp-more-comment-loading" id="qvp-more-comment-loading"/></li>
                </ul>
                <div class="_fwfl -qvp-comment-frm-container">
                    <form class="_fwfl -qvp-comment-form" id="qvp-comment-form" action="" method="POST" data-comments-product>
                        <img src="{{ asset('packages/king/frontend/images/loading-blue-white-16x16.gif') }}" class="-qvp-comment-loading" id="qvp-comment-loading"/>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <input type="hidden" name="slug" value="{{ $slug }}" />
                        <input type="text" name="comment_text" class="_fwfl _r2 -qvp-comment-text" id="qvp-comment-input" placeholder="{{ auth()->guest() ? _t('login2Comment') : _t('leave_comment') }}" autocomplete="off" {{ auth()->guest() ? 'disabled' : '' }}/>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>