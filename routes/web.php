<?php

use App\Http\Controllers\Admin\adminLoginController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\DiscountCodeController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\admin\PageController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\productImageController;
use App\Http\Controllers\admin\productSubCategoryController;
use App\Http\Controllers\admin\SettingController;
use App\Http\Controllers\admin\ShippingController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\TempImagesController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ShopController;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/test', function () {
    orderEmail(16);
});


//Home Pages routes
Route::get('/',[FrontController::class,'index'])->name('front.home');
Route::get('/shop/{categorySlug?}/{subCategorySlug?}',[ShopController::class,'index'])->name('front.shop');
Route::get('/product/{slug}',[ShopController::class,'product'])->name('front.product');
// Add to Cart Routes
Route::get('/cart',[CartController::class,'cart'])->name('front.cart');
Route::post('/add-to-cart',[CartController::class,'addToCart'])->name('front.addToCart');
Route::post('/update-cart',[CartController::class,'updateCart'])->name('front.updateCart');
Route::post('/delete-item',[CartController::class,'deleteItem'])->name('front.deleteItem.cart');
// CheckOut Routes
Route::get('/checkout',[CartController::class,'checkout'])->name('front.checkout');
Route::post('/process-checkout',[CartController::class,'processCheckout'])->name('front.processCheckout');
Route::get('/thanks/{orderId}',[CartController::class,'thankyou'])->name('front.thankyou');
Route::post('/get-order-summery',[CartController::class,'getOrderSummery'])->name('front.getOrderSummery');
Route::post('/apply-discount',[CartController::class,'applyDiscount'])->name('front.applyDiscount');
Route::post('/remove-discount',[CartController::class,'removeCoupon'])->name('front.removeCoupon');
// Wishlist Route
Route::post('/add-to-wishlist',[FrontController::class,'addToWishlist'])->name('front.addToWishlist');
Route::get('/page/{slug}',[FrontController::class,'page'])->name('front.page');
// Contact Us Route
Route::post('/send-contact-email',[FrontController::class,'sendContactEmail'])->name('front.sendContactEmail');
// Forgot password Route
Route::get('/forgot-password',[AuthController::class,'forgotPassword'])->name('front.forgotPassword');
Route::post('/process-forgot-password',[AuthController::class,'processForgotPassword'])->name('front.processForgotPassword');
// Reset Password
Route::get('/reset-password/{token}',[AuthController::class,'resetPassword'])->name('front.resetPassword');
Route::post('/process-reset-password',[AuthController::class,'ProcessResetPassword'])->name('front.ProcessResetPassword');
// add Rating/Reviews Route
Route::post('/save-rating/{productId}',[ShopController::class,'saveRating'])->name('front.saveRating');


// Middleware for login
Route::group(['prefix'=>'account'],function(){
    Route::group(['middleware'=> 'guest'],function(){

        // Login, Regiser Routes
        Route::get('/register',[AuthController::class,'register'])->name('account.register');
        Route::get('/login',[AuthController::class,'login'])->name('account.login');
        Route::post('/login',[AuthController::class,'authenticate'])->name('account.authenticate');
        Route::post('/process-register',[AuthController::class,'processRegister'])->name('account.processRegister');
    });

        // Authenticate Route
        Route::group(['middleware'=> 'auth'],function(){
        Route::get('/profile',[AuthController::class,'profile'])->name('account.profile');
        Route::post('/update-profile',[AuthController::class,'updateProfile'])->name('account.updateProfile');
        Route::post('/update-address',[AuthController::class,'updateAddress'])->name('account.updateAddress');
        Route::get('/change-password',[AuthController::class,'showChangePasswordForm'])->name('account.showChangePasswordForm');
        Route::post('/process-change-password',[AuthController::class,'changePassword'])->name('account.changePassword');


        Route::get('/my-orders',[AuthController::class,'orders'])->name('account.orders');
        Route::get('/my-wishlist',[AuthController::class,'wishlist'])->name('account.wishlist');
        Route::post('/remove-product-from-wishlist',[AuthController::class,'removeProductFormWishList'])->name('account.removeProductFormWishList');
        Route::get('/order-detail/{orderId}',[AuthController::class,'orderDetail'])->name('account.orderDetail');
        Route::get('/logout',[AuthController::class,'logout'])->name('account.logout');

    });
});

// Admin Routes
Route::group(['prefix'=>'admin'],function(){

    // guest Route
    Route::group(['middleware'=> 'admin.guest'],function(){

        Route::get('/login',[adminLoginController::class,'index'])->name('admin.login');
        Route::post('/authenticate',[adminLoginController::class,'authenticate'])->name('admin.authenticate');

    });

    // Admin Route
    Route::group(['middleware'=> 'admin.auth'],function(){

        Route::get('/dashboard',[HomeController::class,'index'])->name('admin.dashboard');
        Route::get('/logout',[HomeController::class,'logout'])->name('admin.logout');

        // Categories Routes //
        Route::get('/categories',[CategoryController::class,'index'])->name('categories.index');
        Route::get('/categories/create',[CategoryController::class,'create'])->name('categories.create');
        Route::post('/categories',[CategoryController::class,'store'])->name('categories.store');
        // Edit category
        Route::get('/categories/{category}/edit',[CategoryController::class,'edit'])->name('categories.edit');
        // Update Category
        Route::put('/categories/{category}',[CategoryController::class,'update'])->name('categories.update');
        // delete Category
        Route::delete('/categories/{category}',[CategoryController::class,'destroy'])->name('categories.delete');

        // Sub Categories Routes //
        Route::get('/sub-categories/create',[SubCategoryController::class,'create'])->name('sub-categories.create');
        Route::post('/sub-categories',[SubCategoryController::class,'store'])->name('sub-categories.store');
        Route::get('/sub-categories',[SubCategoryController::class,'index'])->name('sub-categories.index');
        Route::get('/sub-categories/{subCategory}/edit',[SubCategoryController::class,'edit'])->name('sub-categories.edit');
        Route::put('/sub-categories/{subCategory}',[SubCategoryController::class,'update'])->name('sub-categories.update');
        Route::delete('/sub-categories/{subCategory}',[SubCategoryController::class,'destroy'])->name('sub-categories.delete');

        // Brands Routes
        Route::get('/brands',[BrandController::class,'index'])->name('brands.index');
        Route::get('/brands/create',[BrandController::class,'create'])->name('brands.create');
        Route::post('/brands',[BrandController::class,'store'])->name('brands.store');
        Route::get('/brands/{brand}/edit',[BrandController::class,'edit'])->name('brand.edit');
        Route::put('/brands/{brand}',[BrandController::class,'update'])->name('brands.update');
        Route::delete('/brands/{brand}',[BrandController::class,'destroy'])->name('brands.delete');

        // Product Routes
        Route::get('/products',[ProductController::class,'index'])->name('products.index');
        Route::get('/products/create',[ProductController::class,'create'])->name('products.create');
        Route::post('/products',[ProductController::class,'store'])->name('products.store');
        Route::get('/products/{product}/edit',[ProductController::class,'edit'])->name('products.edit');
        Route::put('/products/{product}',[ProductController::class,'update'])->name('products.update');
        Route::delete('/products/{product}',[ProductController::class,'destroy'])->name('products.delete');
        Route::get('/get-products',[ProductController::class,'getProducts'])->name('products.getProducts');
        Route::get('/ratings',[ProductController::class,'productRatings'])->name('products.productRatings');
        Route::get('/change-rating-status',[ProductController::class,'changeRatingStatus'])->name('products.changeRatingStatus');
        Route::delete('/ratings/{rating}',[ProductController::class,'destroyRating'])->name('products.destroyRating');

        // for Fetching product sub categories
        Route::get('/product-subcategories',[productSubCategoryController::class,'index'])->name('product-subcategories.index');

        Route::post('/product-images/update',[productImageController::class,'update'])->name('product-image.update');
        Route::delete('/product-images',[productImageController::class,'destroy'])->name('product-image.destroy');

        // Shipping Routes
        Route::get('/shipping/create',[ShippingController::class,'create'])->name('shipping.create');
        Route::post('/shipping',[ShippingController::class,'store'])->name('shipping.store');
        Route::get('/shipping/{id}',[ShippingController::class,'edit'])->name('shipping.edit');
        Route::put('/shipping/{id}',[ShippingController::class,'update'])->name('shipping.update');
        Route::delete('/shipping/{id}',[ShippingController::class,'destroy'])->name('shipping.delete');

        // Coupon Code Routes
        Route::get('/coupons',[DiscountCodeController::class,'index'])->name('coupons.index');
        Route::get('/coupons/create',[DiscountCodeController::class,'create'])->name('coupons.create');
        Route::post('/coupons',[DiscountCodeController::class,'store'])->name('coupons.store');
        Route::get('/coupons/{coupon}/edit',[DiscountCodeController::class,'edit'])->name('coupons.edit');
        Route::put('/coupons/{coupon}/update',[DiscountCodeController::class,'update'])->name('coupons.update');
        Route::delete('/coupons/{coupon}/delete',[DiscountCodeController::class,'destroy'])->name('coupons.delete');

        // Order Routes
        Route::get('/orders',[OrderController::class,'index'])->name('orders.index');
        Route::get('/orders/{id}',[OrderController::class,'detail'])->name('orders.detail');
        Route::post('/order/change-status/{id}',[OrderController::class,'changeOrderStatus'])->name('orders.changeOrderStatus');
        Route::post('/order/send-email/{id}',[OrderController::class,'sendInvoiceEmail'])->name('orders.sendInvoiceEmail');

        // Users Routes
        Route::get('/users',[UserController::class,'index'])->name('users.index');
        Route::get('/users/create',[UserController::class,'create'])->name('users.create');
        Route::post('/users',[UserController::class,'store'])->name('users.store');
        Route::get('/users/{user}/edit',[UserController::class,'edit'])->name('users.edit');
        Route::put('/users/{user}',[UserController::class,'update'])->name('users.update');
        Route::delete('/users/{user}',[UserController::class,'destroy'])->name('users.delete');


        // Pages Routes
        Route::get('/pages',[PageController::class,'index'])->name('pages.index');
        Route::get('/page/create',[PageController::class,'create'])->name('page.create');
        Route::post('/page',[PageController::class,'store'])->name('page.store');
        Route::get('/page/{page}/edit',[PageController::class,'edit'])->name('page.edit');
        Route::put('/page/{page}',[PageController::class,'update'])->name('page.update');
        Route::delete('/page/{page}',[PageController::class,'destroy'])->name('page.delete');

         // Settings Routes
         Route::get('/change-password',[SettingController::class,'showChangePasswordForm'])->name('setting.showChangePasswordForm');
         Route::post('/process-change-password',[SettingController::class,'processChangePassword'])->name('setting.processChangePassword');


        // temp-image.create
        Route::post('/upload-temp-image',[TempImagesController::class,'create'])->name('temp-images.create');


        Route::get('/getSlug',function(Request $request){

            $slug = '';

            if(!empty($request->title))
            {
               $slug = Str::slug($request->title);
            }
            return response()->json([
              'status' => true,
              'slug' => $slug
            ]);
        })->name('getSlug');

    });

});
