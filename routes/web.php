<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StribeWebhookController;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Laravel\Cashier\Cashier;
use Stripe\Stripe;
use Stripe\StripeClient;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [EventController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post("/checkout", [EventController::class, 'pay'])->name('checkout');

Route::get('/pay', function (Request $request) {
    $stripePriceId = 'price_1OrZjo1YWGG4ANGtNL6kACYB';

    $quantity = 1;

    return $request->user()->checkout([$stripePriceId => $quantity], [
        'payment_method_types' => ['card', 'konbini'],
        'success_url' => route('checkout-success') . '?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => route('checkout-cancel'),
    ]);
})->name('pay');

Route::get('/konbini-pay', function (Request $request) {
    $stripeApiKey = 'sk_test_51OrZgN1YWGG4ANGtQbzzCBR9DMiKOuBQrWDjgFRYSe4OmLb1VFV5eeTaJS11rjrl4BtDe63txfhQo6Ydq6T1bNIX00a4jV7uSD';
    // dd($stripeApiKey);
    Stripe::setApiKey($stripeApiKey);
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card', 'konbini'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'jpy',
                'product_data' => [
                    'name' => 'Laptop',
                ],
                'unit_amount' => 5000,
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => route('checkout-success') . '?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => route('checkout-cancel'),
    ]);

    return Redirect::to($session->url);
})->name('konbini-pay');

Route::get('success', function (Request $request) {

    $sessionId = $request->get('session_id');

    if ($sessionId === null) {
        return;
    }

    $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

    if ($session->payment_status !== 'paid') {
        return;
    }
    return view('checkout.success');
})->name('checkout-success');


Route::get('cancel', function () {
    return view('checkout.cancel');
})->name('checkout-cancel');

Route::get("/product", function () {
    $stripe = Cashier::stripe();

    // $response = $stripe->products->retrieve('prod_PhLhI7uCy11rnk', []);

    $response = $stripe->prices->all([
        // 'limit' => 2,
        'active' => true,
        'expand' => ['data.product'], // Expand product within the within price
    ]);

    dd($response);
});

Route::get('create/product', function () {

    $stripe = Cashier::stripe();


    $response = $stripe->products->create(['name' => 'Redmi Note 11S', 'description' => 'This is mobile phone on sell okkk', 'default_price_data' => ['currency' => 'jpy', 'unit_amount_decimal' => 200.85]]);

    dd($response); // we get all detail of product including price_id in this $response

    return response()->json(['message' => "Successfully added"], 200);
});

Route::get('/update/product', function () {
    $stripe = Cashier::stripe();

    // we cannot update price direclty but what can we do is change default one inactive and add new price to product and retrieve its price id and store in database
    // $response = $stripe->products->update('price_1Orx0P1YWGG4ANGtPb9S32tn', ['default_price_data' => ['unit_amount_decimal' => 150.0]]);


    $response = $stripe->prices->create([
        'product' => 'prod_PhLaMTYnlxaXqn', // The ID of the existing product
        'unit_amount_decimal' => 181.1, // The updated price
        'currency' => 'jpy', // The currency of the price
        'active' => true
    ]);


    // from here we get new product price id: $response->id


    // now make it default one
    $product = $stripe->products->retrieve('prod_PhLaMTYnlxaXqn', []);

    $product->updateAttributes([
        'default_price' => $response->id
    ]);

    // we will archive previous one
    $stripe->prices->update('price_1Orx0P1YWGG4ANGtPb9S32tn', ['active' => false]);

    dd($response->id);
});

Route::get("delete/product", function () {
    $stripe = Cashier::stripe();

    $response = $stripe->products->delete('prod_PhLhI7uCy11rnk', []);

    dd($response);
});

// for subscription product

Route::get('create/subscription-product', function () {
    $stripe = Cashier::stripe();

    $response = $stripe->products->create([
        'name' => 'Monthly Subscription', // Name of the subscription product
        'type' => 'service', // Type of product
    ]);

    dd($response);

    // $response->id gives product id
});

Route::get('create/subscription-price', function () {
    $stripe = Cashier::stripe();

    $response = $stripe->prices->create([
        'product' => 'prod_PhNHt3JyDT62gH', // The ID of the subscription product
        'unit_amount' => 1000, // The amount in cents for the monthly subscription (e.g., $10)
        'currency' => 'usd', // The currency of the price
        'recurring' => [
            'interval' => 'month', // Billing interval
        ],
        'nickname' => 'Monthly Subscription', // A brief description of the price
        'active' => true,
    ]);

    dd($response);

    // $response->id gives price id of that 
});

Route::get('/update/subscription-price', function () {
    $stripe = Cashier::stripe();

    $response = $stripe->prices->create([
        'product' => 'prod_PhNHt3JyDT62gH', // The ID of the existing subscription
        'unit_amount' => 1200, // The updated price in cents for the monthly subscription (e.g., $12)
        'currency' => 'usd', // The currency of the price
        'recurring' => [
            'interval' => 'month', // Billing interval
        ],
        'nickname' => 'Monthly Subscription', // A brief description of the price
        'active' => true,

    ]);


    // from here we get new product price id: $response->id


    // now make it default one
    $product = $stripe->products->retrieve('prod_PhNHt3JyDT62gH', []);

    $product->updateAttributes([
        'default_price' => $response->id
    ]);

    // we will archive previous one
    $stripe->prices->update('price_1OryZJ1YWGG4ANGtCDFsHT1M', ['active' => false]);

    dd($response->id);
});



Route::get('/delete/subscription-product', function () {

    $stripe = Cashier::stripe();

    $response = $stripe->products->delete('prod_PhNHt3JyDT62gH', []);

    dd($response);
});

Route::get("/view/event", [EventController::class, 'view'])->name('event.view');

Route::get("/show/event/{id}", [EventController::class, 'show'])->name('event.show');
Route::get("/delete/event/{id}", [EventController::class, 'destroy'])->name('event.delete');
Route::get("/edit/event/{id}", [EventController::class, 'edit'])->name('event.edit');
Route::post("/update/event/{id}", [EventController::class, 'update'])->name('event.update');

Route::get("/create/event", function () {
    return view('event.create');
})->name('event.create');

Route::post("/store/event", [EventController::class, 'store'])->name('event.store');

// Route::post('webhooks/stripe', [StribeWebhookController::class, 'handleWebhook']);

require __DIR__ . '/auth.php';



// https://github.com/stripe/stripe-cli