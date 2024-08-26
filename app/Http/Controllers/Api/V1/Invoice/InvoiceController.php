<?php

namespace App\Http\Controllers\Api\V1\Invoice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\CreateInvoiceRequest;
use App\Http\Requests\Invoice\UpdateInvoiceRequest;
use App\Services\Invoice\InvoiceService;
use App\Traits\ApiResponse;

class InvoiceController extends Controller
{
    use ApiResponse;

    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;        
    }

    /**
     * Muestra una lista de facturas.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $invoices = $this->invoiceService->getAll();

        if (empty($invoices)) {
            return $this->error("No hay facturas cargadas.", 404);
        }

        return $this->success($invoices);
    }

    /**
     * Crea una nueva factura.
     *
     * @param  \App\Http\Requests\Invoice\CreateInvoiceRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateInvoiceRequest $request)
    {
        $invoice = $this->invoiceService->create($request->validated());

        if (!$invoice) {
            return $this->error("No se pudo crear la factura.", 500);
        }

        return $this->success($invoice, "Factura creada exitosamente.", 201);
    }

    /**
     * Muestra los detalles de una factura específica.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $invoice = $this->invoiceService->findById($id);

        if (!$invoice) {
            return $this->error("Factura no encontrada.", 404);
        }

        return $this->success($invoice);
    }

    /**
     * Actualiza una factura existente.
     *
     * @param  \App\Http\Requests\Invoice\UpdateInvoiceRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateInvoiceRequest $request, int $id)
    {
        $validatedData = $request->validated();
        $invoice = $this->invoiceService->update($id, $validatedData);

        if (!$invoice) {
            return $this->error("No se pudo actualizar la factura.", 500);
        }

        return $this->success($invoice, "Factura actualizada exitosamente.");
    }

    /**
     * Elimina una factura existente.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $result = $this->invoiceService->delete($id);

        if (!$result) {
            return $this->error("No se pudo eliminar la factura.", 500);
        }

        return $this->success(null, "Factura eliminada exitosamente.");
    }
}
