<?php

namespace App\Repositories\Invoice;

use App\Models\Invoice;
use Exception;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    /**
     * Obtiene todas las facturas.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return Invoice::all();
    }

    /**
     * Crea una nueva factura.
     *
     * @param array $data
     * @return \App\Models\Invoice
     */
    public function create(array $data)
    {
        try {
            $invoice = Invoice::create([
                'order_id' => $data['order_id'],
                'invoice_number' => $data['invoice_number'],
                'issued_at' => $data['issued_at'], // Fecha de emisión
                'total_amount' => $data['total_amount'], // Monto total
                'billing_address' => $data['billing_address'], // Dirección de facturación
                'billing_city' => $data['billing_city'], // Ciudad de facturación
                'billing_state' => $data['billing_state'], // Estado/Provincia de facturación
                'billing_postal_code' => $data['billing_postal_code'], // Código postal de facturación
                'billing_country' => $data['billing_country'], // País de facturación
            ]);
            return $invoice;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Encuentra una factura por ID.
     *
     * @param  int  $id
     * @return \App\Models\Invoice|null
     */
    public function findById(int $id)
    {
        return Invoice::find($id);
    }

    /**
     * Actualiza una factura existente.
     *
     * @param  int  $id
     * @param  array  $data
     * @return \App\Models\Invoice|null
     */
    public function update(int $id, array $data)
    {
        $invoice = $this->findById($id);
        if ($invoice) {
            $invoice->update($data);
        }
        return $invoice;
    }

    /**
     * Elimina una factura existente.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete(int $id)
    {
        $invoice = $this->findById($id);
        if ($invoice) {
            return $invoice->delete();
        }
        return false;
    }
}
