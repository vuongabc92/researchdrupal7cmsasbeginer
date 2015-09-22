SETTING = {
    AJAX_OK: 'OK',
    AJAX_ERROR: 'ERROR',
    LOCATION_LI: '<li data-value="__VALUE"><span class="_fl location-name">__NAME</span><span class="_fr">__COUNT</span></li>',
    PRODUCT_IMG: '<img src="__SRC" class="_fwfl _fh" />',
    PRODUCT_IMG_EDIT: '<div class="edit-product-img"><span class="_fwfl _fh"><i class="fa fa-pencil"></i></span></div>',
    CSRF_TOKEN: $('body').find('#_csrf_token').data('csrf-token'),
    PIN_URI: $('body').find('#product-tree').data('pin-uri'),
    CAROUSEL_SLIDE: '<div class="item"><img class="lazyOwl" data-src="__SRC" /></div>',
    PRODUCT_CAROUSEL: null,
    COMMENT_NODE: '<li data-comment-id="__COMMENT_ID"><a class="comment-owner" href="__OWNER_HREF">__OWNER_NAME</a> <span>__CONTENT.</span></li>',
    COMMENT_NODE_OWNER: '<li data-comment-id="__COMMENT_ID"><button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button><a class="comment-owner" href="__OWNER_HREF">__OWNER_NAME</a> <span>__CONTENT.</span></li>',
    STORE_SLUG: $('#store-container').data('store-slug'),
}