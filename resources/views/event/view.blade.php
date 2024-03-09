<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Event Product in Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        Product name
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Product Description
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Price
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Product_id
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Price_id
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($events as $event)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <th scope="row"
                                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            <a href="{{ route('event.show', $event->id) }}"> {{ $event->name }}</a>
                                        </th>
                                        <td class="px-6 py-4">
                                            {{ $event->description }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $event->price }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $event->product_id }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $event->price_id }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>No Product</tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
</x-app-layout>
