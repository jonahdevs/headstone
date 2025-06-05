<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class ReportsExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected array $data;
    protected array $headings;

    public function __construct(array $data)
    {
        // Convert stdClass objects to associative arrays
        $this->data = $data;
        $this->headings = $this->extractHeadings();
    }

    protected function extractHeadings(): array
    {
        return !empty($this->data) ? array_keys($this->data[0]) : ['No Data'];
    }

    public function collection(): Collection
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return $this->headings;
    }
}
