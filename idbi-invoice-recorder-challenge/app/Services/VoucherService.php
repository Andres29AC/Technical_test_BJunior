<?php

namespace App\Services;

use App\Events\Vouchers\VouchersCreated;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherLine;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Enum;
use SimpleXMLElement;

class VoucherService
{
    public function getVouchers(int $page, int $paginate): LengthAwarePaginator
    {
        return Voucher::with(['lines', 'user'])->paginate(perPage: $paginate, page: $page);
    }
    public function getVouchersWithFilters(int $page, int $paginate, array $filters): LengthAwarePaginator
    {
        $query = Voucher::with(['lines', 'user'])
            ->where('user_id', auth()->id());

        // Aplicar filtros
        if (!empty($filters['invoice_series'])) {
            $query->where('invoice_series', 'like', '%' . $filters['invoice_series'] . '%');
        }

        if (!empty($filters['number'])) {
            $query->where('number', $filters['number']);
        }

        if (!empty($filters['invoice_type'])) {
            $query->where('invoice_type', $filters['invoice_type']);
        }

        if (!empty($filters['currency'])) {
            $query->where('currency', $filters['currency']);
        }

        if (!empty($filters['date_range'])) {
            $query->whereBetween('created_at', $filters['date_range']);
        }

        return $query->paginate(perPage: $paginate, page: $page);
    }


    public function storeVouchersFromXmlContents(array $xmlContents, User $user): array
    {
        $vouchers = [];
        foreach ($xmlContents as $xmlContent) {
            $vouchers[] = $this->storeVoucherFromXmlContent($xmlContent, $user);
        }

        VouchersCreated::dispatch($vouchers, $user);

        return $vouchers;
    }

    public function storeVoucherFromXmlContent(string $xmlContent, User $user): Voucher
    {
        $xml = new SimpleXMLElement($xmlContent);

        $issuerName = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyName/cbc:Name')[0];
        $issuerDocumentType = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0];
        $issuerDocumentNumber = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID')[0];

        $receiverName = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyLegalEntity/cbc:RegistrationName')[0];
        $receiverDocumentType = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0];
        $receiverDocumentNumber = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID')[0];

        $totalAmount = (string) $xml->xpath('//cac:LegalMonetaryTotal/cbc:TaxInclusiveAmount')[0];

        //Extraccion de nuevos campos
        $invoiceSeries = (string) $xml->xpath('//cbc:ID')[0];
        $invoiceNumber = (string) $xml->xpath('//cbc:ID')[0];
        $invoiceType = (string) $xml->xpath('//cbc:InvoiceTypeCode')[0];
        $currency = (string) $xml->xpath('//cbc:DocumentCurrencyCode')[0];

        $voucher = Voucher::where('xml_content', $xmlContent)->first();

        if ($voucher) {
            $voucher->update([
                'invoice_series' => $invoiceSeries,
                'number' => $invoiceNumber,
                'invoice_type' => $invoiceType,
                'currency' => $currency,
            ]);
        } else {
            $voucher = new Voucher([
                'issuer_name' => $issuerName,
                'issuer_document_type' => $issuerDocumentType,
                'issuer_document_number' => $issuerDocumentNumber,
                'receiver_name' => $receiverName,
                'receiver_document_type' => $receiverDocumentType,
                'receiver_document_number' => $receiverDocumentNumber,
                'total_amount' => $totalAmount,
                'xml_content' => $xmlContent,
                'user_id' => $user->id,
                'invoice_series' => $invoiceSeries,
                'number' => $invoiceNumber,
                'invoice_type' => $invoiceType,
                'currency' => $currency,
            ]);
            Log::info('Guardando voucher: ', ['voucher' => $voucher]);
            $voucher->save();
        }

        foreach ($xml->xpath('//cac:InvoiceLine') as $invoiceLine) {
            $name = (string) $invoiceLine->xpath('cac:Item/cbc:Description')[0];
            $quantity = (float) $invoiceLine->xpath('cbc:InvoicedQuantity')[0];
            $unitPrice = (float) $invoiceLine->xpath('cac:Price/cbc:PriceAmount')[0];

            $voucherLine = new VoucherLine([
                'name' => $name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'voucher_id' => $voucher->id,
            ]);

            $voucherLine->save();
        }

        return $voucher;
    }
}
