<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExportAction extends Action
{
    protected string|\Closure|null $fileName = null;
    protected ?\Closure $query = null;
    protected array|\Closure $columns = [];
    protected array|\Closure|null $prependRows = null;
    protected bool|\Closure $withBOM = false;
    protected ?\Closure $mapping = null;

    public static function getDefaultName(): ?string
    {
        return 'exportCsv';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Export CSV'))
            ->icon('heroicon-s-arrow-down-tray')
            ->action(function () {
                $query = $this->evaluate($this->query);
                $columns = $this->evaluate($this->columns);

                if (empty($columns) && $query) {
                    $model = $query->getModel();
                    $columns = Schema::getColumnListing($model->getTable());
                }

                $fileName = $this->evaluate($this->fileName) ?? 'export-' . now()->format('Y-m-d-H-i-s') . '.csv';

                $headers = [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                ];

                $prependRows = $this->evaluate($this->prependRows);
                $withBOM = $this->withBOM;
                $mapping = $this->mapping;

                return new StreamedResponse(function () use ($query, $columns, $prependRows, $withBOM, $mapping) {
                    $handle = fopen('php://output', 'w');

                    if ($withBOM) {
                        fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
                    }

                    if ($prependRows) {
                        foreach ($prependRows as $row) {
                            fputcsv($handle, $row, ';');
                        }
                    }

                    // Add headers
                    fputcsv($handle, array_values($columns), ';');

                    // Add data rows
                    if ($query) {
                        $query->chunk(100, function ($records) use ($handle, $columns, $mapping) {
                            foreach ($records as $record) {
                                if ($mapping) {
                                    $row = $this->evaluate($mapping, ['record' => $record]);
                                } else {
                                    $row = [];
                                    foreach (array_keys($columns) as $column) {
                                        $row[] = data_get($record, $column);
                                    }
                                }
                                fputcsv($handle, $row, ';');
                            }
                        });
                    }

                    fclose($handle);
                }, 200, $headers);
            });
    }

    public function fileName(string|\Closure|null $fileName): static
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function query(\Closure $query): static
    {
        $this->query = $query;

        return $this;
    }

    public function columns(array|\Closure $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    public function prependRows(array|\Closure|null $rows): static
    {
        $this->prependRows = $rows;

        return $this;
    }

    public function withBOM(bool $withBOM = true): static
    {
        $this->withBOM = $withBOM;

        return $this;
    }

    public function mapping(\Closure $mapping): static
    {
        $this->mapping = $mapping;

        return $this;
    }
}
