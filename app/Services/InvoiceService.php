<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceDetail;

class InvoiceService
{
    protected $invoiceModel, $invoiceDetailsModel;

    public function __construct(Invoice $invoiceModel, InvoiceDetail $invoiceDetailsModel)
    {
        $this->invoiceModel = $invoiceModel;
        $this->invoiceDetailsModel = $invoiceDetailsModel;
    }

    public function list()
    {
        return $this->invoiceModel->whereNull('deleted_at');
    }

    public function all()
    {
        return $this->invoiceModel->whereNull('deleted_at')->all();
    }

    public function find($id)
    {
        return $this->invoiceModel->find($id);
    }

    public function create(array $data)
    {
        return $this->invoiceModel->create($data);
    }

    public function update(array $data, $id)
    {
        $dataInfo = $this->invoiceModel->findOrFail($id);

        $dataInfo->update($data);

        return $dataInfo;
    }

    public function delete($id)
    {
        $dataInfo = $this->invoiceModel->find($id);

        if (!empty($dataInfo)) {

            $dataInfo->deleted_at = date('Y-m-d H:i:s');

            $dataInfo->status = 'Deleted';

            return ($dataInfo->save());
        }
        return false;
    }

    public function changeStatus($request)
    {
        $dataInfo = $this->invoiceModel->findOrFail($request->id);

        $dataInfo->status = $request->status;

        $dataInfo->update($request->all());

        return $dataInfo;
    }

    public function activeList()
    {
        return $this->invoiceModel->whereNull('deleted_at')->where('status', 'Active');
    }

    public function listWithDetails()
    {
        return $this->invoiceModel->with('invoiceDetails', 'invoiceDetails.product')->where('author_id', auth('admin')->user()->id)->where('status', 'Active')->whereNull('deleted_at');
    }

    public function findWithDetails($id)
    {
        return Invoice::with(['invoiceDetails.product'])->find($id);
    }
}
