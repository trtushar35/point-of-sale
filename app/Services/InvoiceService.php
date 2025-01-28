<?php

namespace App\Services;

use App\Models\Invoice;

class InvoiceService
{
    protected $invoiceModel;

    public function __construct(Invoice $invoiceModel)
    {
        $this->invoiceModel = $invoiceModel;
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
        return $this->invoiceModel->with('category')->whereNull('deleted_at');
    }
}
