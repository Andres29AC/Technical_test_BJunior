<?php

namespace App\Http\Resources\Vouchers;

use App\Http\Resources\Users\UserResource;
use App\Http\Resources\VoucherLines\VoucherLineResource;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

class VoucherResource extends JsonResource
{
    /**
     * @var Voucher
     */
    public $resource;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'issuer_name' => $this->resource->issuer_name,
            'issuer_document_type' => $this->resource->issuer_document_type,
            'issuer_document_number' => $this->resource->issuer_document_number,
            'receiver_name' => $this->resource->receiver_name,
            'receiver_document_type' => $this->resource->receiver_document_type,
            'receiver_document_number' => $this->resource->receiver_document_number,
            'total_amount' => $this->resource->total_amount,
            //Nuevos campos
            'invoice_series' => $this->resource->invoice_series,
            'number' => $this->resource->number,
            'invoice_type' => $this->resource->invoice_type,
            'currency' => $this->resource->currency,
            'user' => $this->whenLoaded(
                'user',
                fn () => UserResource::make($this->resource->user),
            ),
            'lines' => $this->whenLoaded(
                'lines',
                fn () => VoucherLineResource::collection($this->resource->lines),
            ),
        ];
    }
}