<?php

namespace App\Filament\Resources\Transactions\Tables;

use App\Models\Borrower;
use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class TransactionsTable
{
    protected static function borrower(): ?Borrower
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (! $user?->hasRole('Borrower')) {
            return null;
        }

        return $user->borrower;
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {

                if (static::borrower()) {
                    $query->where('borrower_id', static::borrower()->id);
                }

                return $query;
            })

            ->columns([
                TextColumn::make('borrower.name')
                    ->label('Borrower')
                    ->searchable()
                    ->visible(fn() => ! static::borrower()),

                TextColumn::make('book.title')
                    ->label('Book')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Officer')
                    ->searchable()
                    ->visible(fn() => ! static::borrower()),

                TextColumn::make('borrowed_at')
                    ->label('Borrowed At')
                    ->date()
                    ->sortable(),

                TextColumn::make('returned_at')
                    ->label('Returned At')
                    ->date()
                    ->sortable(),

                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('late_days')
                    ->label('Late Days')
                    ->sortable(),

                TextColumn::make('total_fine')
                    ->label('Total Fine')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'borrowed',
                        'info' => 'extended',
                        'success' => 'returned',
                        'danger' => 'late',
                    ]),

                TextColumn::make('extension_count')
                    ->label('Extension')
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->searchable()
                    ->visible(fn() => ! static::borrower()),

                TextColumn::make('updater.name')
                    ->label('Updated By')
                    ->searchable()
                    ->visible(fn() => ! static::borrower()),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->visible(fn() => ! static::borrower()),

                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->visible(fn() => ! static::borrower()),
            ])
            ->recordActions([
                RestoreAction::make(),
                Action::make('extendLoan')
                    ->label('Extend')
                    ->icon('heroicon-o-calendar-days')
                    ->color('warning')

                    ->visible(
                        fn(Transaction $record) =>
                        in_array($record->status, [
                            'borrowed',
                            'extended',
                        ])
                    )

                    ->modalHeading(function (Transaction $record) {

                        $isAllowed = now()->startOfDay()->gte(
                            Carbon::parse($record->due_date)
                                ->copy()
                                ->subDay()
                                ->startOfDay()
                        );

                        return $isAllowed
                            ? 'Extend Loan Confirmation'
                            : 'Extension Rejected';
                    })

                    ->modalDescription(function (Transaction $record) {

                        $isAllowed = now()->startOfDay()->gte(
                            Carbon::parse($record->due_date)
                                ->copy()
                                ->subDay()
                                ->startOfDay()
                        );

                        if (! $isAllowed) {

                            return new \Illuminate\Support\HtmlString("
                                <div style='text-align:center;padding:20px'>
                                    <p style='margin-bottom:10px'>
                                        Perpanjangan hanya dapat dilakukan mulai
                                        <strong>H-1 sebelum jatuh tempo</strong>.
                                    </p>

                                    <p>
                                        Tanggal jatuh tempo:
                                        <strong>{$record->due_date->format('d M Y')}</strong>
                                    </p>
                                </div>
                            ");
                        }
                        $maxBorrowDays = $record->book
                            ->category
                            ->max_borrow_days;
                        $fineAmount = $record->book
                            ->category()
                            ->withTrashed()
                            ->first()
                            ->fine_amount;
                        $lateDays = now()->startOfDay()->gt(
                            Carbon::parse($record->due_date)->startOfDay()
                        )
                            ? Carbon::parse($record->due_date)
                            ->startOfDay()
                            ->diffInDays(now()->startOfDay())
                            : 0;
                        $extendFine = $lateDays * $fineAmount;
                        $newDueDate = now()
                            ->copy()
                            ->addDays($maxBorrowDays);
                        return new \Illuminate\Support\HtmlString("
                            <div style='text-align:left !important;'>
                                <table style='width:100%; border-collapse:collapse;'>
                                    <tr>
                                        <td style='width:180px;font-weight:bold;'>Borrower</td>
                                        <td>{$record->borrower->name}</td>
                                    </tr>
                                    <tr>
                                        <td style='font-weight:bold;'>Book</td>
                                        <td>{$record->book->title}</td>
                                    </tr>
                                    <tr>
                                        <td style='font-weight:bold;'>Borrowed At</td>
                                        <td>{$record->borrowed_at->format('d M Y')}</td>
                                    </tr>
                                    <tr>
                                        <td style='font-weight:bold;'>Current Due Date</td>
                                        <td>{$record->due_date->format('d M Y')}</td>
                                    </tr>
                                    <tr>
                                        <td style='font-weight:bold;'>Extension Days</td>
                                        <td>{$maxBorrowDays} Days</td>
                                    </tr>
                                    <tr>
                                        <td style='font-weight:bold;'>Late Days</td>
                                        <td>{$lateDays} Days</td>
                                    </tr>
                                    <tr>
                                        <td style='font-weight:bold;'>Fine Per Day</td>
                                        <td>Rp " . number_format($fineAmount, 0, ',', '.') . "</td>
                                    </tr>
                                    <tr>
                                        <td style='font-weight:bold;'>Extension Fine</td>
                                        <td>Rp " . number_format($extendFine, 0, ',', '.') . "</td>
                                    </tr>
                                    <tr>
                                        <td style='font-weight:bold;'>New Due Date</td>
                                        <td>{$newDueDate->format('d M Y')}</td>
                                    </tr>

                                </table>
                            </div>
                        ");
                    })
                    ->modalSubmitAction(
                        fn(Transaction $record) =>
                        now()->startOfDay()->gte(
                            Carbon::parse($record->due_date)
                                ->copy()
                                ->subDay()
                                ->startOfDay()
                        )
                            ? null
                            : false
                    )
                    ->requiresConfirmation()
                    ->action(function (Transaction $record) {
                        $isAllowed = now()->startOfDay()->gte(
                            Carbon::parse($record->due_date)
                                ->copy()
                                ->subDay()
                                ->startOfDay()
                        );
                        if (! $isAllowed) {
                            return;
                        }
                        $maxBorrowDays = $record->book
                            ->category
                            ->max_borrow_days;

                        $fineAmount = $record->book
                            ->category()
                            ->withTrashed()
                            ->first()
                            ->fine_amount;
                        $lateDays = now()->startOfDay()->gt(
                            Carbon::parse($record->due_date)->startOfDay()
                        )
                            ? Carbon::parse($record->due_date)
                            ->startOfDay()
                            ->diffInDays(now()->startOfDay())
                            : 0;
                        $extendFine = $lateDays * $fineAmount;
                        $newDueDate = now()
                            ->copy()
                            ->addDays($maxBorrowDays);
                        $record->update([
                            'due_date' => $newDueDate,
                            'status' => 'extended',
                            'extension_count' => $record->extension_count + 1,
                            'late_days' => ($record->late_days ?? 0) + $lateDays,
                            'total_fine' => ($record->total_fine ?? 0) + $extendFine,
                        ]);
                    }),
                Action::make('returnBook')
                    ->label('Return')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->visible(
                        fn(Transaction $record) =>
                        in_array($record->status, ['borrowed', 'extended'])
                    )
                    ->modalHeading('Book Return Confirmation')
                    ->modalDescription(function (Transaction $record) {
                        $returnedAt = now();
                        $lateDays = 0;
                        if ($returnedAt->gt($record->due_date)) {
                            $lateHours = Carbon::parse($record->due_date)
                                ->diffInHours($returnedAt);
                            $lateDays = floor($lateHours / 24);
                        }
                        // dd($record->book->category()->withTrashed()->first());
                        $fineAmount = $record->book->category()->withTrashed()->first()->fine_amount;
                        $totalFine = $lateDays * $fineAmount;
                        return new \Illuminate\Support\HtmlString("
                            <div style='text-align:left !important;'>
                                <table style='width:100%; border-collapse:collapse; text-align:left !important;'>
                                    <tbody>
                                        <tr>
                                            <td style='width:180px; font-weight:bold; text-align:left;'>Borrower</td>
                                            <td style='text-align:left;'>{$record->borrower->name}</td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight:bold; text-align:left;'>Book</td>
                                            <td style='text-align:left;'>{$record->book->title}</td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight:bold; text-align:left;'>Borrowed At</td>
                                            <td style='text-align:left;'>{$record->borrowed_at->format('d M Y')}</td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight:bold; text-align:left;'>Due Date</td>
                                            <td style='text-align:left;'>{$record->due_date->format('d M Y')}</td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight:bold; text-align:left;'>Returned At</td>
                                            <td style='text-align:left;'>{$returnedAt->format('d M Y')}</td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight:bold; text-align:left;'>Late Days</td>
                                            <td style='text-align:left;'>{$lateDays}</td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight:bold; text-align:left;'>Fine Per Day</td>
                                            <td style='text-align:left;'>Rp " . number_format($fineAmount, 0, ',', '.') . "</td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight:bold; text-align:left;'>Total Fine</td>
                                            <td style='text-align:left;'>Rp " . number_format($totalFine, 0, ',', '.') . "</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        ");
                    })
                    ->requiresConfirmation()
                    ->action(function (Transaction $record) {
                        $returnedAt = now();
                        $lateDays = 0;
                        if ($returnedAt->gt($record->due_date)) {
                            $lateHours = Carbon::parse($record->due_date)
                                ->diffInHours($returnedAt);

                            $lateDays = floor($lateHours / 24);
                        }
                        $fineAmount = $record->book->category()->withTrashed()->first()->fine_amount;

                        $totalFine = $lateDays * $fineAmount;

                        $record->update([
                            'returned_at' => $returnedAt,
                            'late_days' => $lateDays,
                            'total_fine' => $totalFine,
                            'status' => $lateDays > 0
                                ? 'late'
                                : 'returned',
                        ]);
                        $record->book->increment('stock');
                    }),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
