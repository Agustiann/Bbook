<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use App\Models\Book;
use App\Models\Borrower;
use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($data['borrower_type'] === 'new') {

            $borrower = Borrower::create([
                'name' => $data['borrower_name'],
                'phone' => $data['borrower_phone'],
                'address' => $data['borrower_address'],
            ]);

            $data['borrower_id'] = $borrower->id;
        }

        $book = Book::with('category')->findOrFail($data['book_id']);

        $alreadyBorrowed = Transaction::query()
            ->where('borrower_id', $data['borrower_id'])
            ->where('book_id', $data['book_id'])
            ->where('status', 'borrowed')
            ->exists();

        if ($alreadyBorrowed) {
            throw ValidationException::withMessages([
                'book_id' => 'This borrower is still borrowing this book.',
            ]);
        }

        if ($book->stock <= 0) {
            throw ValidationException::withMessages([
                'book_id' => 'Book stock is empty.',
            ]);
        }

        $data['user_id'] = Auth::id();

        $data['borrowed_at'] = now();

        $data['returned_at'] = null;

        $data['due_date'] = Carbon::now()->addDays(
            $book->category->max_borrow_days
        );

        $data['late_days'] = 0;

        $data['total_fine'] = 0;

        $data['status'] = 'borrowed';
        $data['extension_count'] = 0;

        unset(
            $data['borrower_type'],
            $data['borrower_name'],
            $data['borrower_phone'],
            $data['borrower_address']
        );

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->book->decrement('stock');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
