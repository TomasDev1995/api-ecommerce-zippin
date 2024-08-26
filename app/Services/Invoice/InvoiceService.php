<?php

namespace App\Services\Invoice;

use App\Repositories\Invoice\InvoiceRepositoryInterface;

class InvoiceService
{
    protected $invoiceRepository;

    public function __construct(InvoiceRepositoryInterface $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * Obtiene todas las facturas.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->invoiceRepository->getAll();
    }

    /**
     * Crea una nueva factura.
     *
     * @param  array  $data
     * @return \App\Models\Invoice
     */
    public function create(array $data)
    {
        return $this->invoiceRepository->create($data);
    }

    /**
     * Encuentra una factura por ID.
     *
     * @param  int  $id
     * @return \App\Models\Invoice|null
     */
    public function findById(int $id)
    {
        return $this->invoiceRepository->findById($id);
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
        return $this->invoiceRepository->update($id, $data);
    }

    /**
     * Elimina una factura existente.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete(int $id)
    {
        return $this->invoiceRepository->delete($id);
    }
}
