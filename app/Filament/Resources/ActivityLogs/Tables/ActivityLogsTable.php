<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use App\Models\User;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('log_name')
                    ->label(__('Log'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'authentication' => 'info',
                        'payments'       => 'success',
                        'students'       => 'primary',
                        'enrollments'    => 'warning',
                        'guardians'      => 'gray',
                        'discounts'      => 'danger',
                        'expenses'       => 'warning',
                        'installments'   => 'primary',
                        'tuition-plans'  => 'success',
                        'academic-years' => 'info',
                        'users'          => 'danger',
                        'roles'          => 'gray',
                        'permissions'    => 'gray',
                        default          => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('description')
                    ->label(__('Description'))
                    ->wrap()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable()
                    ->limit(80)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('causer')
                    ->label(__('Performed by'))
                    ->formatStateUsing(function ($state, $record) {
                        $causer = $record->causer;
                        if (! $causer) {
                            return '-';
                        }

                        return $causer->name ?? $causer->full_name ?? ($causer->first_name ? trim("{$causer->first_name} {$causer->last_name}") : null) ?? $record->causer_type;
                    })
                    ->searchable(
                        isIndividual: true,
                        isGlobal: false,
                        query: fn (Builder $query, string $search): Builder => $query->whereHasMorph(
                            'causer',
                            [User::class],
                            fn (Builder $causer) => $causer->where('name', 'like', "%{$search}%"),
                        ),
                    )
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('subject_type')
                    ->label(__('Subject Type'))
                    ->wrap()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('properties.ip')
                    ->label(__('IP Address'))
                    ->badge()
                    ->color('gray')
                    ->searchable(
                        isIndividual: true,
                        isGlobal: false,
                        query: fn (Builder $query, string $search): Builder => $query->where('properties->ip', 'like', "%{$search}%"),
                    )
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('properties.user_agent')
                    ->label(__('User Agent'))
                    ->wrap()
                    ->limit(60)
                    ->searchable(
                        isIndividual: true,
                        isGlobal: false,
                        query: fn (Builder $query, string $search): Builder => $query->where('properties->user_agent', 'like', "%{$search}%"),
                    )
                    ->toggleable(isToggledHiddenByDefault: true),
                // Hidden by default. Renders per-row Student queries only when toggled on.
                TextColumn::make('properties')
                    ->label(__('Details'))
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(
                        isIndividual: true,
                        isGlobal: false,
                        query: fn (Builder $query, string $search): Builder => $query->where('properties', 'like', "%{$search}%"),
                    )
                    ->formatStateUsing(function ($state, $record) {
                        $props = $record->properties;
                        if ($props instanceof \Illuminate\Support\Collection) {
                            $props = $props->toArray();
                        }

                        if (is_array($props)) {
                            $studentId = $props['student_id'] ?? $props['old']['student_id'] ?? $props['attributes']['student_id'] ?? null;
                            if ($studentId && ! isset($props['student'])) {
                                $student = \App\Models\Student::withTrashed()
                                    ->select(['id', 'first_name_ar', 'last_name_ar'])
                                    ->find($studentId);
                                if ($student) {
                                    $props['student'] = [
                                        'first_name_ar' => $student->first_name_ar,
                                        'last_name_ar'  => $student->last_name_ar,
                                    ];
                                }
                            }

                            return json_encode($props, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                        }

                        return (string) $props;
                    })
                    ->limit(120),
                TextColumn::make('created_at')
                    ->label(__('When'))
                    ->badge()
                    ->color('primary')
                    ->searchable(
                        isIndividual: true,
                        isGlobal: false,
                        query: fn (Builder $query, string $search): Builder => $query->where('created_at', 'like', "%{$search}%"),
                    )
                    ->sortable(),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->with('causer'))
            ->filters([
                SelectFilter::make('log_name')
                    ->label(__('Log name'))
                    ->options([
                        'authentication' => __('Authentication'),
                        'payments'       => __('Payments'),
                        'students'       => __('Students'),
                        'enrollments'    => __('Enrollments'),
                        'guardians'      => __('Guardians'),
                        'discounts'      => __('Discounts'),
                        'expenses'       => __('Expenses'),
                        'installments'   => __('Installments'),
                        'tuition-plans'  => __('Tuition Plans'),
                        'academic-years' => __('Academic Years'),
                        'users'          => __('Users'),
                        'roles'          => __('Roles'),
                        'permissions'    => __('Permissions'),
                    ]),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label(__('From date')),
                        DatePicker::make('created_until')
                            ->label(__('Until date')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators[] = __('From') . ' ' . \Illuminate\Support\Carbon::parse($data['created_from'])->toFormattedDateString();
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators[] = __('Until') . ' ' . \Illuminate\Support\Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->recordAction(ViewAction::class)
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->deferLoading();
    }
}