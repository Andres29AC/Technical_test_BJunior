<!DOCTYPE html>
<html>
<head>
    <title>Comprobantes Subidos</title>
</head>
<body>
    <h1>Estimado {{ $user->name }},</h1>
    <p>Hemos recibido tus comprobantes con los siguientes detalles:</p>
    @foreach ($vouchers as $voucher)
    <ul>
        <li>Nombre del Emisor: {{ $voucher->issuer_name }}</li>
        <li>Tipo de Documento del Emisor: {{ $voucher->issuer_document_type }}</li>
        <li>Número de Documento del Emisor: {{ $voucher->issuer_document_number }}</li>
        <li>Nombre del Receptor: {{ $voucher->receiver_name }}</li>
        <li>Tipo de Documento del Receptor: {{ $voucher->receiver_document_type }}</li>
        <li>Número de Documento del Receptor: {{ $voucher->receiver_document_number }}</li>
        <li>Monto Total: {{ $voucher->total_amount }}</li>
        <li>Serie: {{ $voucher->invoice_series }}</li>
        <li>Número: {{ $voucher->number }}</li>
        <li>Tipo de Comprobante: {{ $voucher->invoice_type}}</li>
        <li>Moneda: {{ $voucher->currency}}</li>
    </ul>
    @endforeach
    <h2>Comprobantes No Registrados</h2>
    @if(count($failedRegistered) > 0)
        @foreach ($failedRegistered as $failed)
        <ul>
            <li>XML: {{ $failed['xml_content'] }}</li>
            <li>Error: {{ $failed['error'] }}</li>
        </ul>
        @endforeach
    @else
        <p>No hubo errores en el registro de los comprobantes.</p>
    @endif
    <p>¡Gracias por usar nuestro servicio!</p>
</body>
</html>
