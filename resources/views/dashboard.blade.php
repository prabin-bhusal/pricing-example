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
                    <form method="post" action="{{ route('checkout') }}">
                        @csrf
                        @method('POST')
                        <select name="event">
                            <option value="null">Select Product</option>
                            @foreach ($events as $event)
                                <option value={{ $event->id }}>{{ $event->name }}</option>
                            @endforeach
                        </select>

                        <button type="submit">Procceed to checkout (VISA cards)</button>
                    </form>

                    <div>
                        <a href="{{ route('event.create') }}">Create a Event</a>
                    </div>
                    <div>
                        <a href="{{ route('event.view') }}">View Event</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
