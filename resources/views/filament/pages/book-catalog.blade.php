<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        @foreach ($this->getBooks() as $book)
            @php
                $alreadyBorrowed = auth()
                    ->user()
                    ->borrower->transactions()
                    ->where('book_id', $book->id)
                    ->where('status', 'borrowed')
                    ->exists();
            @endphp

            <x-filament::card>

                <div class="flex gap-4">
                    <div class="w-36 flex-shrink-0">
                        @if ($book->photo)
                            <img src="{{ asset('storage/' . $book->photo) }}" class="w-full h-52 object-cover rounded-lg"
                                alt="{{ $book->title }}">
                        @else
                            <div
                                class="w-full h-52 rounded-lg border border-dashed flex items-center justify-center text-center text-gray-500 p-2">
                                Gambar buku belum tersedia
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 flex flex-col justify-between">

                        <div class="space-y-2">

                            <h2 class="text-xl font-bold">
                                {{ $book->title }}
                            </h2>

                            <p>
                                <span class="font-semibold">Stock :</span>
                                {{ $book->stock }}
                            </p>

                            @if ($book->stock > 0)
                                <span class="text-success-600 font-medium">
                                    Available
                                </span>
                            @else
                                <span class="text-danger-600 font-medium">
                                    Out of Stock
                                </span>
                            @endif

                        </div>

                        <div class="mt-4">

                            @if ($alreadyBorrowed)
                                <x-filament::button color="gray" disabled>
                                    Already Borrowed
                                </x-filament::button>
                            @elseif($book->stock == 0)
                                <x-filament::button color="danger" disabled>
                                    Out of Stock
                                </x-filament::button>
                            @else
                                <x-filament::button color="primary" wire:click="borrow({{ $book->id }})">
                                    Borrow
                                </x-filament::button>
                            @endif

                        </div>

                    </div>

                </div>

            </x-filament::card>
        @endforeach

    </div>

</x-filament-panels::page>
