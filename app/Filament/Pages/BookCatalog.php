<?php

namespace App\Filament\Pages;

use App\Models\Book;
use App\Models\Transaction;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class BookCatalog extends Page
{
    protected string $view = 'filament.pages.book-catalog';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Book Catalog';

    public function getBooks()
    {
        return Book::with('category')->get();
    }

    public function borrow(int $bookId): void
    {
        $book = Book::with('category')->findOrFail($bookId);

        $borrower = Auth::user()->borrower;

        if (! $borrower) {
            Notification::make()
                ->title('Borrower profile not found.')
                ->danger()
                ->send();

            return;
        }

        $alreadyBorrowed = Transaction::query()
            ->where('borrower_id', $borrower->id)
            ->where('book_id', $book->id)
            ->where('status', 'borrowed')
            ->exists();

        if ($alreadyBorrowed) {
            Notification::make()
                ->title('You are already borrowing this book.')
                ->warning()
                ->send();

            return;
        }

        if ($book->stock <= 0) {
            Notification::make()
                ->title('Book stock is empty.')
                ->danger()
                ->send();

            return;
        }

        Transaction::create([
            'borrower_id' => $borrower->id,
            'book_id' => $book->id,
            'user_id' => Auth::id(),
            'borrowed_at' => now(),
            'returned_at' => null,
            'due_date' => now()->addDays($book->category->max_borrow_days),
            'late_days' => 0,
            'total_fine' => 0,
            'status' => 'borrowed',
            'extension_count' => 0,
        ]);

        $book->decrement('stock');

        Notification::make()
            ->title('Book borrowed successfully.')
            ->success()
            ->send();
    }
}
