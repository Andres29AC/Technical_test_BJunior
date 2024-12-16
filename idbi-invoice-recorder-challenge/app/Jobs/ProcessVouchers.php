<?php

namespace App\Jobs;

use App\Mail\VouchersCreatedMail;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessVouchers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $vouchers;
    protected User $user;

    public function __construct(array $vouchers, User $user)
    {
        $this->vouchers = $vouchers;
        $this->user = $user;
    }

    public function handle()
    {
        //dd('La clase ProcessVouchers se está ejecutando');
        $successfullRegistered = [];
        $failedRegistered = [];
        
        Log::info('Iniciando el procesamiento de vouchers para el usuario: ' . $this->user->email);
        
        foreach ($this->vouchers as $voucher) {
            try {
                Log::info('Procesando voucher: ' . json_encode($voucher));

                $voucherModel = new Voucher();
                $voucherModel->fill($voucher);
                $voucherModel->save();
                
                $successfullRegistered[] = $voucherModel;
                Log::info('Voucher registrado exitosamente: ' . json_encode($voucher));

            } catch (\Exception $e) {
                Log::error('Error al procesar el voucher', [
                    'voucher' => $voucher,
                    'error_message' => $e->getMessage(),
                    'error_trace' => $e->getTraceAsString()
                ]);
                $failedRegistered[] = [
                    'xml' => $voucher['xml_content'],
                    'error' => $e->getMessage()
                ];

                Log::error('Error al procesar el voucher: ' . json_encode($voucher) . ' - Mensaje: ' . $e->getMessage());
            }
        }
        
        Log::info('Vouchers exitosos registrados: ' . count($successfullRegistered));
        Log::info('Vouchers fallidos registrados: ' . count($failedRegistered));
        
        Log::info('Enviando correo con el resumen...');
        
        try {
            Mail::to($this->user->email)
                ->send(new VouchersCreatedMail($successfullRegistered, $failedRegistered, $this->user));

            Log::info('Correo enviado exitosamente a: ' . $this->user->email);
        } catch (\Exception $e) {
            Log::error('Error al enviar el correo: ' . $e->getMessage());
        }
    }
}
