<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    protected $categoryModel;

    public function __construct(Category $categoryModel)
    {
        $this->categoryModel = $categoryModel;
    }

    public function list()
    {
        return $this->categoryModel->whereNull('deleted_at');
    }

    public function all()
    {
        return $this->categoryModel->whereNull('deleted_at')->all();
    }

    public function find($id)
    {
        return $this->categoryModel->find($id);
    }

    public function create(array $data)
    {
        return $this->categoryModel->create($data);
    }

    public function update(array $data, $id)
    {
        $dataInfo = $this->categoryModel->findOrFail($id);

        $dataInfo->update($data);

        return $dataInfo;
    }

    public function delete($id)
    {
        $dataInfo = $this->categoryModel->find($id);

        if (!empty($dataInfo)) {

            $dataInfo->deleted_at = date('Y-m-d H:i:s');

            $dataInfo->status = 'Deleted';

            return ($dataInfo->save());
        }
        return false;
    }

    public function changeStatus($request)
    {
        $dataInfo = $this->categoryModel->findOrFail($request->id);

        $dataInfo->status = $request->status;

        $dataInfo->update($request->all());

        return $dataInfo;
    }

    public function activeList()
    {
        return $this->categoryModel->whereNull('deleted_at');
    }
}
