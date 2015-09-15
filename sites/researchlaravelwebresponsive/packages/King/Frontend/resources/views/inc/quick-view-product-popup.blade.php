<div class="modal fade -qvp-modal" id="quick-view-product-modal" data-product-id="0" tabindex="-1" role="dialog" aria-labelledby="quickViewProductLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="_fwfl _bgw _r0 modal-content">
            <div class="-qvp-modal-left">
                <div class="_fwfl -qvp-handle-container -qvp-handle-lg">
                    <ul class="_fwfl product-handle -qvp-handle">
                        <li>
                            <button class="product-pin" data-pin-trigger>
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
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </ul>
                </div>
                <div class="_fwfl product-carousel">
                    <div id="product-carousel" class="owl-carousel"></div>
                </div>
            </div>
            <div class="-qvp-modal-right">
                <div class="_fwfl -qvp-handle-container -qvp-handle-sm">
                    <ul class="_fwfl product-handle -qvp-handle">
                        <li>
                            <button class="product-pin" data-pin-trigger>
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
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </ul>
                </div>
                <div class="_fwfl _p20">
                    <button class="_fl btn _btn _btn-blue1 _btn-sm">Buy it</button>
                    <a href="#" class="_fr _fs13 _fwb _mt7 -qvp-details">Details...</a>
                </div>
                <span class="_fwfl qvp-view-all-comment">
                    <button class="-qvp-view-more-comments" id="qvp-load-comments" data-text="View all __COUNT comments" data-text-2="Load more comments" data-current="1" data-url data-load-before data-load-more-comments></button>
                </span>
                <ul class="_fwfl _ls -qvp-comments-tree product-comment-tree" data-delete-comment-url="" data-delete-comment></ul>
                <div class="_fwfl -qvp-comment-frm-container">
                    <form class="_fwfl -qvp-comment-form" id="qvp-comment-form" action="" method="POST" data-comments-product>
                        <img src="{{ asset('packages/king/frontend/images/loading-blue-white-16x16.gif') }}" class="-qvp-comment-loading" id="qvp-comment-loading"/>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <input type="text" name="comment_text" class="_fwfl _r2 -qvp-comment-text" id="qvp-comment-input" placeholder="Leave a comment..." autocomplete="off"/>
                    </form>
                </div>
            </div>


        </div>
    </div>
</div>