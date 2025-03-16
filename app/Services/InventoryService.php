<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Product;

class InventoryService
{
    protected $inventoryModel;

    public function __construct(Inventory $inventoryModel)
    {
        $this->inventoryModel = $inventoryModel;
    }

    public function list()
    {
        return $this->inventoryModel->whereNull('deleted_at');
    }

    public function all()
    {
        return $this->inventoryModel->whereNull('deleted_at')->all();
    }

    public function find($id)
    {
        return $this->inventoryModel->find($id);
    }

    public function create(array $data)
    {
        return $this->inventoryModel->create($data);
    }

    public function update(array $data, $id)
    {
        $dataInfo = $this->inventoryModel->findOrFail($id);

        $dataInfo->update($data);

        return $dataInfo;
    }

    public function delete($id)
    {
        $dataInfo = $this->inventoryModel->find($id);

        if (!empty($dataInfo)) {

            $dataInfo->deleted_at = date('Y-m-d H:i:s');

            $dataInfo->status = 'Deleted';

            return ($dataInfo->save());
        }
        return false;
    }

    public function changeStatus($request)
    {
        $dataInfo = $this->inventoryModel->findOrFail($request->id);

        $dataInfo->status = $request->status;

        $dataInfo->update($request->all());

        return $dataInfo;
    }

    public function activeList()
    {
        return $this->inventoryModel->with('category')->where('author_id', auth('admin')->user()->id)->whereNull('deleted_at');
    }

    public function getInventoryByCategory($categoryId)
    {
        return $this->inventoryModel->where('category_id', $categoryId)->where('author_id', auth('admin')->user()->id)->whereNull('deleted_at')->first();
    }

    public function decreaseStock($productId, $quantity)
    {
        $product = Product::find($productId);
        dd($product);
        if ($product) {
            $product->stock_quantity -= $quantity;
            $product->save();
        }
    }
}
