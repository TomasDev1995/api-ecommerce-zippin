<?php

namespace App\Repositories\Invoice;

use App\Models\Invoice;

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
     * @param  array  $data
     * @return \App\Models\Invoice
     */
    public function create(array $data)
    {
        return Invoice::create($data);
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
