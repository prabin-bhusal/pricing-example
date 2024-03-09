<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;

class EventController extends Controller
{

    public function dashboard()
    {
        $events = Event::all();

        return view('dashboard', ['events' => $events]);
    }

    public function view()
    {
        $events = Event::all();

        return view('event.view', ['events' => $events]);
    }

    public function store(Request $request)
    {
        $name = $request->name;
        $description = $request->description;
        $price = $request->price;
        // dd($price);

        $stripe = Cashier::stripe();



        $productResponse = $stripe->products->create([
            'name' => $name,
            'description' => $description,
            // 'default_price_data' => [
            //     'currency' => 'jpy',
            //     'unit_amount_decimal' => (int) $price,
            // ]
        ]);

        // Create the price
        $priceResponse = $stripe->prices->create([
            'product' => $productResponse->id,
            'unit_amount_decimal' => (float) $price,
            'currency' => 'jpy',
        ]);


        $event = Event::create([
            'name' => $name,
            'description' => $description,
            'product_id' => $productResponse->id,
            'price_id' => $priceResponse->id,
            'price' => $price
        ]);

        return redirect()->route('event.view');
    }

    public function show(String $event)
    {
        $id = (int) $event;
        $event = Event::find($id);
        return view('event.show', ['event' => $event]);
    }

    public function edit(String $event)
    {
        $id = (int) $event;
        $event = Event::find($id);
        // dd($event);
        return view('event.edit', ['event' => $event]);
    }

    public function update(String $event, Request $request)
    {
        $id = (int) $event;
        $event = Event::find($id);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'price' => (float) $request->price
        ];

        $stripe = Cashier::stripe();
        $product = $stripe->products->retrieve($event->product_id, []);

        // dd($event->price, $data['price']);

        if ($data['price'] != $event->price) {
            // dd($event->price, $data['price']);

            $response = $stripe->prices->create([
                'product' => $event->product_id, // The ID of the existing product
                'unit_amount_decimal' => $data['price'], // The updated price
                'currency' => 'jpy', // The currency of the price
                'active' => true
            ]);

            $product->updateAttributes([
                'default_price' => $response->id,
                'name' => $request->name,
                'description' => $request->description
            ]);

            // we will archive previous one
            $stripe->prices->update($event->price_id, ['active' => false]);

            // now in our database also

            $updatedEvent = $event->update([
                'price_id' => $response->id,
                'name' => $request->name,
                'description' => $request->description,
                'price' => $data['price']
            ]);
        }


        $product->updateAttributes([
            'name' => $request->name,
            'description' => $request->description
        ]);

        $updatedEvent = $event->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return redirect()->route('event.view');
    }

    public function destroy(String $event)
    {

        $event = Event::find($event);


        $stripe = Cashier::stripe();

        // we cannot delete product with prices.. so we need to archive it
        $stripe->products->update($event->product_id, ['active' => false]);

        $delFromDatabase = $event->delete();

        return redirect()->route('event.view');
    }

    public function pay(Request $request)
    {
        if ($request->event == "null") {
            dd("null value");
        }
        $event = Event::find((int)$request->event);
        // dd($event);
        $stripePriceId = $event->price_id;

        $quantity = 1;

        return $request->user()->checkout([$stripePriceId => $quantity], [
            'payment_method_types' => ['card', 'konbini'],
            'success_url' => route('checkout-success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout-cancel'),
        ]);
    }
}
