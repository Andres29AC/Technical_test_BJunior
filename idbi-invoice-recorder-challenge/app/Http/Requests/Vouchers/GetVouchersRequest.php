<?php

namespace App\Http\Requests\Vouchers;

use Illuminate\Foundation\Http\FormRequest;

class GetVouchersRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page' => ['required', 'int', 'gt:0'],
            'paginate' => ['required', 'int', 'gt:0'],
            //filtros avanzados
            'invoice_series' => ['nullable', 'string', 'max:20'],
            'number' => ['nullable', 'string', 'max:20'],
            'invoice_type' => ['nullable', 'string', 'in:01,03'],
            'currency' => ['nullable', 'string', 'in:PEN,USD'],
            'date_range' => ['nullable', 'array', 'size:2'],
            'date_range.0' => ['required_with:date_range', 'date'],
            'date_range.1' => ['required_with:date_range', 'date', 'after_or_equal:date_range.0'],
        ];
    }
    public function filters():array{
        return $this->only([
            'invoice_series',
            'number',
            'invoice_type',
            'currency',
            'date_range',
        ]);
    }
}
