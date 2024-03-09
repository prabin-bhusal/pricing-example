<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1>Visa Card Method</h1>
                    <div class="max-w-sm mx-auto mt-20 bg-white rounded-md shadow-md overflow-hidden">
                        <div class="px-6 py-4 bg-gray-900 text-white">
                            <h1 class="text-lg font-bold">Credit Card</h1>
                        </div>
                        <div class="px-6 py-4">

                            <form method="get" action="{{ route('pay') }}">
                                <div class="mb-4">
                                    <label class="block text-gray-700 font-bold mb-2" for="card-number">
                                        Card Number
                                    </label>
                                    <input
                                        class="appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        id="card-number" type="text" placeholder="**** **** **** ****">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 font-bold mb-2" for="expiration-date">
                                        Expiration Date
                                    </label>
                                    <input
                                        class="appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        id="expiration-date" type="text" placeholder="MM/YY">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 font-bold mb-2" for="cvv">
                                        CVV
                                    </label>
                                    <input
                                        class="appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        id="cvv" type="text" placeholder="***">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 font-bold mb-2" for="cvv">
                                        Cardholder Name
                                    </label>
                                    <input
                                        class="appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        type="text" placeholder="Full Name">
                                </div>

                                <button
                                    class="bg-blue-500 hover:bg-blue-600 text-black font-bold py-2 px-4 rounded-full">
                                    Pay Now
                                </button>
                            </form>

                            <form method="get" action="{{ route('konbini-pay') }}">
                                <button
                                    class="bg-blue-500 hover:bg-blue-600 text-black font-bold py-2 px-4 rounded-full">Pay
                                    with Konbini</button>
                            </form>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
