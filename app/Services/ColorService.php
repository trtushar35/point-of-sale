<?php

namespace App\Services;

use App\Models\Color;

class ColorService
{
    protected $colorModel;

    public function __construct(Color $colorModel)
    {
        $this->colorModel = $colorModel;
    }

    public function list()
    {
        return $this->colorModel->where('author_id', auth('admin')->user()->id)->whereNull('deleted_at');
    }

    public function all()
    {
        return $this->colorModel->whereNull('deleted_at')->all();
    }

    public function find($id)
    {
        return $this->colorModel->find($id);
    }

    public function create(array $data)
    {
        return $this->colorModel->create($data);
    }

    public function update(array $data, $id)
    {
        $dataInfo = $this->colorModel->findOrFail($id);

        $dataInfo->update($data);

        return $dataInfo;
    }

    public function delete($id)
    {
        $dataInfo = $this->colorModel->find($id);

        if (!empty($dataInfo)) {

            $dataInfo->deleted_at = date('Y-m-d H:i:s');

            $dataInfo->status = 'Deleted';

            return ($dataInfo->save());
        }
        return false;
    }

    public function changeStatus($request)
    {
        $dataInfo = $this->colorModel->findOrFail($request->id);

        $dataInfo->status = $request->status;

        $dataInfo->update($request->all());

        return $dataInfo;
    }

    public function activeList()
    {
        return $this->colorModel->whereNull('deleted_at');
    }
}
