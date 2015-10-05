<?php

/**
 * FrontendController
 *
 * @author vuongabc92@gmail.com
 */

namespace King\Frontend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Validator;
use App\Helpers\Upload;
use App\Helpers\FileName;
use App\Helpers\Image;
use App\Models\Product;
use App\Models\Pin;
use App\Models\Comment;
use App\Models\Store;

class StoreController extends FrontController
{
    /**
     * @var App\Models\Product
     */
    protected $_product;

    /**
     * Product image sizes type thumb, big, ...
     *
     * @var array
     */
    protected $_productImgSizes;

    public function __construct(Product $product) {
        $this->_product         = $product;
        $this->_productImgSizes = config('front.product_img_size');
    }

    /**
     * Display store page
     *
     * @return response
     */
    public function index() {
        return view('frontend::store.index', [
            'productCount' => store()->products->count(),
            'products'     => store()->products
        ]);
    }

    public function store($slug) {

        $store = Store::where('slug', $slug)->first();

        if ($store === null) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Store Not Found.');
        }

        return view('frontend::store.index', [
            'productCount' => $store->products->count(),
            'products'     => $store->products->sortBy('created_date'),
            'store'        => $store,
            'storeCover'   => config('front.cover_path') . $store->cover_big,
            'productPath'  => config('front.product_path') . $store->id . '/',
            'storeOwner'   => auth()->guest() ? false : ($store->user_id === user()->id)
        ]);
    }

    /**
     * Save product info
     *
     * @param Illuminate\Http\Request $request
     *
     * @return type
     */
    public function ajaxSaveProduct(Request $request) {

        //Only accept ajax request
        if ($request->ajax() && $request->isMethod('POST')) {

            if (store() === null) {
                return pong(0, _t('not_found'), 404);
            }

            $store     = store();
            $productId = (int) $request->get('id');

            if ($productId) {
                $product = $store->products->find($productId);
            } else {
                $product = new Product();
            }

            if ($product === null) {
                return pong(0, _t('not_found'), 404);
            }

            $rules     = $this->_product->getRules();
            $messages  = $this->_product->getMessages();

            if ($productId) {
                $rules = remove_rules($rules, 'product_image_1');
            }

            $validator  = Validator::make($request->all(), $rules, $messages);

            $tempImages = [
                $request->get('product_image_1'),
                $request->get('product_image_2'),
                $request->get('product_image_3'),
                $request->get('product_image_4')
            ];

            if ($validator->fails()) {
                return pong(0, $validator->messages(), is_null($product) ? 404 : 403);
            }

            /**
             *  1. Copy product images from temporary folder to product folder.
             *  2. Delete old product image(s).
             *  3. Save product.
             */
            try {

                // 1
                $images = $this->_copyTempProductImages($tempImages);

                // 2
                if ($productId) {
                    $this->_deleteOldImages($images, $product->images);
                }

                // 3
                $product->store_id    = $store->id;
                $product->name        = $request->get('name');
                $product->price       = $request->get('price');
                $product->old_price   = $request->get('old_price');
                $product->description = $request->get('description');
                $product->setImages($images);
                $product->save();

            } catch (Exception $ex) {
                return pong(0, _t('opp'), 500);
            }

            return pong(1, [
                'messages' => _t('saved_info'),
                'data' => [
                    'id'        => $product->id,
                    'name'      => $product->name,
                    'price'     => product_price($product->price),
                    'old_price' => product_price($product->old_price),
                    'image'     => (($i = $product->toImage()->image_1) !== null) ? product_image($i->medium) : ''
                ]
            ]);
        }
    }


    /**
     * Upload product image
     *
     * @param Illuminate\Http\Request $request
     *
     * return JSON
     */
    public function ajaxUploadProductImage(Request $request) {

        if ($request->isMethod('POST')) {

            $order     = (int) $request->get('order');
            $rules     = $this->_getProductImageRules();
            $messages  = $this->_getProductImageMessages();
            $validator = Validator::make($request->all(), $rules, $messages);

            // Check does the product image's order exist
            if ( ! $this->_checkProductImageOrder($order)) {
                $validator->after(function($validator) {
                    $validator->errors()->add('__product', _t('product_order_wrong'));
                });
            }

            if ($validator->fails()) {
                return file_pong([
                    'status'   => _const('AJAX_ERROR'),
                    'messages' => $validator->errors()->first()
                ], 403);
            }

            try {

                $file         = $request->file('__product');
                $currentImage = $request->get('current_image');
                $upload       = $this->_uploadProductImage($file, $currentImage);

            } catch (Exception $ex) {

                $validator->errors()->add('__product', _t('opp'));

                return file_pong([
                    'status'   => _const('AJAX_ERROR'),
                    'messages' => $validator->errors()->first()
                ], 500);
            }

            $resizes = $upload['image']->getResizes();

            return file_pong([
                'status'   => _const('AJAX_OK'),
                'messages' => _t('saved_info'),
                'data'     => [
                    'original' => $upload['filename']->getName(),
                    'thumb'    => asset($upload['temp_path'] . $resizes['thumb']),
                    'order'    => $order
                ]
            ]);

        }

    }

    /**
     * Delete product's temporary image when upload image to temp directory
     *
     * @param Illuminate\Http\Request $request
     *
     * @return JSON
     */
    public function ajaxDeleteProductTempImg(Request $request) {

        //Only accept ajax request
        if ($request->ajax()) {

            try {

                $this->_deleteProductTempImg($request);

            } catch (Exception $ex) {
                return pong(0, _t('opp'), 500);
            }

            return pong(1, _t('saved_info'));
        }
    }

    /**
     * Find product by id
     *
     * @param Illuminate\Http\Request $request
     * @param int                     $id
     *
     * @return type
     */
    public function ajaxFindProductById(Request $request, $id) {

        // Only accept AJAX with HTTP GET request
        if ($request->ajax() && $request->isMethod('GET')) {

            if (store() === null) {
                return pong(0, _t('not_found'), 404);
            }

            $id         = (int) $id;
            $store      = store();
            $product    = $store->products->find($id);
            $maxComment = config('front.max_load_comments');

            if ($product === null) {
                return pong(0, _t('not_found'), 404);
            }

            // Rebuild product data structure
            try {
                $product->toImage();
                $productPath = config('front.product_path') . $store->id . '/';
                $data = [
                    'id'          => $product->id,
                    'name'        => $product->name,
                    'price'       => $product->price,
                    'old_price'   => $product->old_price,
                    'description' => $product->description,
                    'images'      => [
                        'image_1' => ($product->image_1 !== null) ? asset($productPath . $product->image_1->thumb) : '',
                        'image_2' => ($product->image_2 !== null) ? asset($productPath . $product->image_2->thumb) : '',
                        'image_3' => ($product->image_3 !== null) ? asset($productPath . $product->image_3->thumb) : '',
                        'image_4' => ($product->image_4 !== null) ? asset($productPath . $product->image_4->thumb) : '',
                    ],
                    'last_modified' => $product->updated_at
                ];
            } catch (Exception $ex) {
                return pong(0, _t('opp'), 500);
            }

            return pong(1, ['data' => $data]);
        }
    }

    /**
     * Find product by id
     *
     * @param Illuminate\Http\Request $request
     * @param int                     $id
     *
     * @return type
     */
    public function ajaxGetQuickViewProduct(Request $request, $id, $store_slug) {

        // Only accept AJAX with HTTP GET request
        if ($request->ajax() && $request->isMethod('GET')) {

            $store = Store::where('slug', $store_slug)->first();

            if ($store === null) {
                return pong(0, _t('not_found'), 404);
            }

            $id         = (int) $id;
            $product    = $store->products->find($id);
            $maxComment = config('front.max_load_comments');

            if ($product === null) {
                return pong(0, _t('not_found'), 404);
            }

            // Rebuild product data structure
            try {

                $product->toImage();

                $productPath = config('front.product_path') . $store->id . '/';
                $comments    = $product->comments;
                $data        = [
                    'id'     => $product->id,
                    'images' => [
                        'image_1' => ($product->image_1 !== null) ? asset($productPath . $product->image_1->big) : '',
                        'image_2' => ($product->image_2 !== null) ? asset($productPath . $product->image_2->big) : '',
                        'image_3' => ($product->image_3 !== null) ? asset($productPath . $product->image_3->big) : '',
                        'image_4' => ($product->image_4 !== null) ? asset($productPath . $product->image_4->big) : '',
                    ],
                    'pin' => [
                        'count'             => $product->total_pin,
                        'viewer_has_pinned' => is_null($product->pin) ? false : $product->pin->isPinned()
                    ],
                    'comments' => [
                        'add_url'              => route('front_comments_add', $product->id),
                        'delete_url'           => route('front_comments_delete', ['product_id' => $product->id, 'store_slug' => $store->slug, 'comment_id' => '__COMMENT_ID']),
                        'more_url'             => route('front_comments_load_more', $product->id),
                        'count'                => $comments->count(),
                        'nodes'                => ($comments->count() > 0) ? $this->_rebuildComment($comments->take(-$maxComment)->all(), $store->user_id) : [],
                        'view_all'             => ($product->comments->count() > ($maxComment)),
                        'load_more_before'     => ($comments->count() > 0) ? $comments->take(-$maxComment)->first()->id : 0,
                        'viewer_has_commented' => (auth()->check() && $product->isCommented(user()->id)) ? true : false
                    ],
                    'last_modified' => $product->updated_at
                ];
            } catch (Exception $ex) {
                return pong(0, _t('opp'), 500);
            }

            return pong(1, ['data' => $data]);
        }
    }

    public function ajaxDeleteProduct(Request $request) {

        // Only accept ajax request with method is delete
        if ($request->ajax() && $request->isMethod('DELETE')) {

        }
    }

    /**
     * Pin product
     *
     * @param Illuminate\Http\Request $request
     *
     * @return type
     */
    public function ajaxPinProduct(Request $request) {

        // Only accept ajax request with post method
        if ($request->ajax() && $request->isMethod('POST')) {

            $slug       = $request->get('slug');
            $store      = Store::where('slug', $slug)->first();
            $productId  = (int) $request->get('product_id');

            if ($store === null) {
                return pong(0, _t('not_found'), 404);
            }

            $product = $store->products->find($productId);
            if ($product === null) {
                return pong(0, _t('not_found'), 404);
            }

            try {
                $pin = $this->_togglePin(user()->id, $product);
            } catch (Exception $ex) {
                return pong(0, _t('opp'), 500);
            }

            return pong(1, ['data' => [
                'pin' => [
                    'viewer_has_pinned' => $pin['isPinned'],
                    'count'             => $pin['totalPin']
                ]
            ]]);
        }
    }

    /**
     * Add product comment
     *
     * @param Illuminate\Http\Request $request
     * @param int                     $product_id
     *
     * @return JSON
     */
    public function ajaxProductAddComment(Request $request, $product_id) {

        // Only accept ajax request with post method
        if ($request->ajax() && $request->isMethod('POST')) {

            $commentText = $request->get('comment_text');
            $storeSlug   = $request->get('slug');
            $store       = Store::where('slug', $storeSlug)->first();

            if ($store === null) {
                return pong(0, _t('not_found'), 404);
            }

            $product = $store->products->find($product_id);
            if ($product === null) {
                return pong(0, _t('not_found'), 404);
            }

            try {
                $comment              = new Comment();
                $comment->product_id  = $product_id;
                $comment->user_id     = user()->id;
                $comment->text        = $commentText;
                $comment->create_time = time();
                $comment->save();

            } catch (Exception $ex) {
                return pong(0, _t('opp'), 500);
            }

            $commentRes            = $this->_rebuildComment($comment, $store->user_id);
            $commentRes['product'] = [
                'id'            => $product_id,
                'count_comment' => $product->comments->count(),
            ];

            return pong(1, ['data' => $commentRes]);
        }
    }

    /**
     * Delete product comment
     *
     * @param Illuminate\Http\Request $request
     * @param int                     $product_id
     * @param int                     $comment_id
     *
     * @return JSON
     */
    public function ajaxProductDeleteComment(Request $request, $product_id, $store_slug, $comment_id) {
        // Only accept ajax request with post method
        if ($request->ajax() && $request->isMethod('DELETE')) {

            $productId = (int) $product_id;
            $commentId = (int) $comment_id;
            $store     = Store::where('slug', $store_slug)->first();
            
            if ($store === null) {
                return pong(0, _t('not_found'), 404);
            }

            $product = $store->products->find($productId);
            if ($product === null) {
                return pong(0, _t('not_found'), 404);
            }

            $comment = $product->comments->find($commentId);
            if (is_null($comment)) {
                return pong(0, _t('not_found'), 404);
            }

            if (user()->id !== $comment->user_id) {
                return pong(0, _t('unauth'), 401);
            }

            try {
                $comment->delete();
            } catch (Exception $ex) {
                return pong(0, _t('opp'), 500);
            }

            return pong(1, [
                'data' => [
                    'comments' => [
                        'count'                => Product::find($productId)->comments->count(),
                        'viewer_has_commented' => $product->isCommented(user()->id)
                    ],
                    'product_id' => $product->id
                ]
            ]);
        }
    }

    /**
     * Load more product comments
     *
     * @param Illuminate\Http\Request $request
     * @param int                     $product_id
     *
     * @return JSON
     */
    public function ajaxLoadMoreComments(Request $request, $product_id) {

        // Only accept ajax request with post method
        if ($request->ajax() && $request->isMethod('POST')) {

            $store = Store::where('slug', $request->get('slug'))->first();
            if ($store === null) {
                return pong(0, _t('not_found'), 404);
            }

            $productId = (int) $product_id;
            $product   = $store->products->find($productId);
            if (is_null($product)) {
                return pong(0, _t('not_found'), 404);
            }

            $before = (int) $request->get('before');
            if ($product->comments->find($before) === null) {
                return pong(0, _t('not_found'), 404);
            }

            $max              = config('front.max_load_comments');
            $comments         = $this->_getLoadMoreComents($productId, $before)->take($max)->get();
            $commentsNextLoad = $this->_getLoadMoreComents($productId, $comments->last()->id)->take(1)->count();

            return pong(1, ['data' => [
                'comments' => [
                    'nodes'                => $this->_rebuildComment($comments->sortBy('id')->all(), $store->user_id),
                    'older_comments_empty' => $commentsNextLoad === 0,
                ]
            ]]);
        }
    }
    
    public function refresh(Request $request) {
        
        if ($request->ajax() && $request->isMethod('POST')) {
            
            $response        = [];
            $productQuantity = (int) $request->get('product_quantity');
            $slug            = $request->get('slug');
            $store           = store($slug, true);
            
            if ($store === null) {
                return pong(0, _t('not_found'), 404);
            }
            
            if ($store->products->count() > $productQuantity) {
                dd($store->ptoducts);
            }
            
        }
    }

    /**
     * Get load more product comments
     *
     * @param int $productId
     * @param int $before
     *
     * @return App\Models\Comment
     */
    protected function _getLoadMoreComents($productId, $before = 0) {

        $comment = Comment::where('product_id', $productId);

        if ($before) {
            $comment->where('id', '<', $before);
        }

        return $comment->orderBy('id', 'DESC');
    }

    /**
     * Rebuild comment with new data
     *
     * @param \Illuminate\Support\Collection $comment
     * @param int                            $storeUserId
     *
     * @return array
     */
    protected function _rebuildComment($comment, $storeUserId) {

        if (is_array($comment) && count($comment)) {
            $final = [];

            foreach ($comment as $one) {
                $final[] = $this->_rebuildComment($one, $storeUserId);
            }

            return $final;
        }

        return [
            'id' => $comment->id,
            'text' => $comment->text,
            'user' => [
                'id'       => $comment->user_id,
                'username' => $comment->user->user_name
            ],
            'is_one_post_product' => ($storeUserId === $comment->user_id),
            'is_owner' => (user() === null) ? false : ($comment->user_id === user()->id)
        ];
    }


    /**
     * Toggle pin product
     *
     * @param int                $userId
     * @param App\Models\Product $product
     *
     * return array
     */
    protected function _togglePin($userId, $product) {

        $pin     = $product->pin;
        $pinned  = false;

        if ($pin === null) {

            $pin                = new Pin();
            $pin->product_id    = $product->id;
            $pin->user_id       = json_encode([$userId => $userId]);
            $product->total_pin = ((int) $product->total_pin) + 1;
            $pinned             = true;

        } else {

            $uidArray = json_decode($pin->user_id, true);

            if (isset($uidArray[$userId])) {
                unset($uidArray[$userId]);
                $product->total_pin = ($p = ((int) $product->total_pin) > 0) ? $p - 1 : $p;
            } else {
                $uidArray[$userId] = $userId;
                $product->total_pin = ((int) $product->total_pin) + 1;
                $pinned             = true;
            }

            $pin->user_id = json_encode($uidArray);
        }

        $product->save();
        $pin->save();

        return ['isPinned' => $pinned, 'totalPin' => $product->total_pin];
    }

    /**
     * Copy temporary product image from temp folder to product folder
     * and delete images on temp folder
     *
     * @param array $tempImages Temporary product image that was updated
     * to temp folder
     *
     * @return Illuminate\Support\Collection
     */
    protected function _copyTempProductImages($tempImages) {

        $tempPath       = config('front.temp_path');
        $productPath    = config('front.product_path') . store()->id . '/';
        $images         = [];

        if (count($tempImages)) {

            foreach ($tempImages as $k => $image) {

                $imageSize = [];

                if (check_file($tempPath . $image) && count($this->_productImgSizes)) {

                    foreach ($this->_productImgSizes as $size) {

                        $nameBySize = str_replace(_const('TOBEREPLACED'), "_{$size}", $image);
                        if (copy($tempPath . $nameBySize, $productPath . $nameBySize)) {
                            $imageSize[$size] = $nameBySize;
                        }

                        delete_file($tempPath . $nameBySize);
                    }

                    delete_file($tempPath . $image);
                }

                if (count($imageSize)) {
                    $images[$k] = $imageSize;
                }
            }
        }

        return new Collection($images);
    }

    /**
     * Delete product old images
     *
     * @param Illuminate\Support\Collection $newImages
     * @param array                         $oldImages
     *
     * @return void
     */
    protected function _deleteOldImages($newImages, $oldImages) {

        $oldImages   = new Collection(json_decode($oldImages));
        $productPath = config('front.product_path') . store()->id . '/';

        if ($oldImages->count()) {
            foreach ($newImages as $k => $image) {
                if (isset($oldImages[$k])) {
                    foreach ($oldImages[$k] as $one) {
                        delete_file($productPath . $one);
                    }
                }
            }
        }
    }

    /**
     * Get product image rules
     *
     * @return array
     */
    protected function _getProductImageRules() {

        $maxFileSize = _const('PRODUCT_MAX_FILE_SIZE');

        return [
            '__product' => 'required|image|mimes:jpg,png,jpeg,gif|max:' . $maxFileSize
        ];
    }

    /**
     * Get product image rule messages
     *
     * @return array
     */
    protected function _getProductImageMessages() {

        return [
            '__product.required' => _t('no_file'),
            '__product.image'    => _t('file_not_image'),
            '__product.mimes'    => _t('file_image_mimes'),
            '__product.max'      => _t('avatar_max'),
        ];
    }

    /**
     * Check does the image order exist
     *
     * @param int $order product image order
     *
     * @return boolean
     */
    protected function _checkProductImageOrder($order) {

        $orderConfig = (array) config('front.product_img_order');

        if ( ! in_array($order, $orderConfig)) {

            return false;
        }

        return true;
    }

    /**
     * Upload and resize product image
     *
     * 1. Get path
     * 2. Generate file name
     * 3. Upload
     * 4. Resize
     * 5. Delete old temporary image(s)
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile|array $file
     * @param string                                                    $currentImage
     *
     * @return array
     */
    protected function _uploadProductImage($file, $currentImage) {

        // 1
        $tempPath = config('front.temp_path');

        // 2
        $filename = new FileName($tempPath, $file->getClientOriginalExtension());
        $filename->setPrefix(_const('PRODUCT_PREFIX'))->product()->generate();
        $filename->group([
            'big' => [
                'width'  => _const('PRODUCT_BIG'),
                'height' => _const('PRODUCT_BIG')
            ],
            'medium' => [
                'width'  => _const('PRODUCT_MEDIUM'),
                'height' => _const('PRODUCT_MEDIUM')
            ],
            'thumb' => [
                'width'  => _const('PRODUCT_THUMB'),
                'height' => _const('PRODUCT_THUMB')
            ],
        ], false);

        // 3
        $upload = new Upload($file);
        $upload->setDirectory($tempPath)->setName($filename->getName())->move();

        // 4
        $image = new Image($tempPath . $filename->getName());
        $image->setDirectory($tempPath)->resizeGroup($filename->getGroup());

        // 5
        foreach ($this->_productImgSizes as $size) {

            $nameBySize = str_replace(_const('TOBEREPLACED'), "_{$size}", $currentImage);

            delete_file($tempPath . $nameBySize);
        }

        delete_file($tempPath . $currentImage);

        return [
            'image'     => $image,
            'temp_path' => $tempPath,
            'filename'  => $filename
        ];
    }

    /**
     * Delete product temporary images that was uploaded to temp folder
     *
     * @param Illuminate\Http\Request $request
     *
     * @return void
     */
    protected function _deleteProductTempImg($request) {

        $tempPath = config('front.temp_path');

        foreach ([1, 2, 3, 4] as $one) {

            $imgToDel = $request->get("product_image_{$one}");

            if ($imgToDel !== '' && check_file($tempPath . $imgToDel)) {

                foreach ($this->_productImgSizes as $size) {

                    $nameBySize = str_replace(_const('TOBEREPLACED'), "_{$size}", $imgToDel);

                    delete_file($tempPath . $nameBySize);
                }

                delete_file($tempPath . $imgToDel);
            }
        }
    }

    /**
     * Rebuild product data, only get necessary infos
     *
     * @param App\Models\Product $product
     *
     * @return array
     */
    protected function _rebuildProductData($product) {

        $productPath = config('front.product_path');
        $product->toImage();

        return [
            'id'          => $product->id,
            'name'        => $product->name,
            'price'       => $product->price,
            'old_price'   => $product->old_price,
            'description' => $product->description,
            'images'      => [
                'image_1' => ($product->image_1 !== null) ? asset($productPath . $product->image_1->thumb) : '',
                'image_2' => ($product->image_2 !== null) ? asset($productPath . $product->image_2->thumb) : '',
                'image_3' => ($product->image_3 !== null) ? asset($productPath . $product->image_3->thumb) : '',
                'image_4' => ($product->image_4 !== null) ? asset($productPath . $product->image_4->thumb) : '',
            ],
            'lastModified' => $product->updated_at
        ];
    }
}
