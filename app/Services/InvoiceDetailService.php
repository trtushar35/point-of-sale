<?php

namespace App\Services;

use App\Models\InvoiceDetail;

class InvoiceDetailService
{
    protected $invoiceDetailsModel;

    public function __construct(InvoiceDetail $invoiceDetailsModel)
    {
        $this->invoiceDetailsModel = $invoiceDetailsModel;
    }

    public function list()
    {
        return $this->invoiceDetailsModel->with('invoice', 'product')->whereNull('deleted_at')->where('status', 'Active');
    }

    public function all()
    {
        return $this->invoiceDetailsModel->whereNull('deleted_at')->all();
    }

    public function find($id)
    {
        return $this->invoiceDetailsModel->find($id);
    }

    public function create(array $data)
    {
        return $this->invoiceDetailsModel->create($data);
    }

    public function update(array $data, $id)
    {
        $dataInfo = $this->invoiceDetailsModel->findOrFail($id);

        $dataInfo->update($data);

        return $dataInfo;
    }

    public function delete($id)
    {
        $dataInfo = $this->invoiceDetailsModel->find($id);

        if (!empty($dataInfo)) {

            $dataInfo->deleted_at = date('Y-m-d H:i:s');

            $dataInfo->status = 'Deleted';

            return ($dataInfo->save());
        }
        return false;
    }

    public function changeStatus($request)
    {
        $dataInfo = $this->invoiceDetailsModel->findOrFail($request->id);

        $dataInfo->status = $request->status;

        $dataInfo->update($request->all());

        return $dataInfo;
    }

    public function activeList()
    {
        return $this->invoiceDetailsModel->with('category')->whereNull('deleted_at');
    }
}
