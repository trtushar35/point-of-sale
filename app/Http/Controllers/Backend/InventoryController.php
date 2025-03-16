<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryRequest;
use App\Services\InventoryService;
use App\Services\CategoryService;
use App\Traits\SystemTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class InventoryController extends Controller
{
    use SystemTrait;

    protected $inventoryService, $categoryService;

    public function __construct(InventoryService $inventoryService, categoryService $categoryService)
    {
        $this->inventoryService = $inventoryService;
        $this->categoryService = $categoryService;

        $this->middleware('auth:admin');
        $this->middleware('permission:inventory-add', ['only' => ['create']]);
        $this->middleware('permission:inventory-add', ['only' => ['store']]);
        $this->middleware('permission:inventory-edit', ['only' => ['edit|update']]);
        $this->middleware('permission:inventory-list', ['only' => ['index']]);
        $this->middleware('permission:inventory-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        return Inertia::render(
            'Backend/Inventory/Index',
            [
                'pageTitle' => fn() => 'Inventory List',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'Inventory Manage'],
                    ['link' => route('backend.inventory.index'), 'title' => 'Inventory List'],
                ],
                'tableHeaders' => fn() => $this->getTableHeaders(),
                'dataFields' => fn() => $this->dataFields(),
                'datas' => fn() => $this->getDatas(),
                'categories' => fn() => $this->categoryService->list()->get(),
            ]
        );
    }

    private function getDatas()
    {
        $query = $this->inventoryService->activeList();

        if (request()->filled('category_id'))
            $query->where('category_id', request()->category_id);

        if (request()->filled('priceFrom') && request()->filled('priceTo')) {
            $query->whereBetween('price', [request()->priceFrom, request()->priceTo]);
        } elseif (request()->filled('priceFrom')) {
            $query->where('price', '>=', request()->priceFrom);
        } elseif (request()->filled('priceTo')) {
            $query->where('price', '<=', request()->priceTo);
        }

        $datas = $query->paginate(request()->numOfData ?? 10)->withQueryString();

        $formatedDatas = $datas->map(function ($data, $index) {
            $customData = new \stdClass();
            $customData->index = $index + 1;
            $customData->category_id = $data->category->name;
            $customData->quantity = $data->quantity;
            $customData->stock_in = $data->stock_in;
            $customData->stock_out = $data->stock_out;
            $customData->sku = $data->sku;
            $customData->status = getStatusText($data->status);

            $customData->hasLink = true;
            $customData->links = [
                [
                    'linkClass' => 'semi-bold text-white statusChange ' . (($data->status == 'Active') ? "bg-gray-500" : "bg-green-500"),
                    'link' => route('backend.inventory.status.change', ['id' => $data->id, 'status' => $data->status == 'Active' ? 'Inactive' : 'Active']),
                    'linkLabel' => getLinkLabel((($data->status == 'Active') ? "Inactive" : "Active"), null, null)
                ],
                [
                    'linkClass' => 'bg-yellow-400 text-black semi-bold',
                    'link' => route('backend.inventory.edit',  $data->id),
                    'linkLabel' => getLinkLabel('Edit', null, null)
                ],
                [
                    'linkClass' => 'deleteButton bg-red-500 text-white semi-bold',
                    'link' => route('backend.inventory.destroy', $data->id),
                    'linkLabel' => getLinkLabel('Delete', null, null)
                ]

            ];
            return $customData;
        });

        return regeneratePagination($formatedDatas, $datas->total(), $datas->perPage(), $datas->currentPage());
    }

    private function dataFields()
    {
        return [
            ['fieldName' => 'index', 'class' => 'text-center'],
            ['fieldName' => 'category_id', 'class' => 'text-center'],
            ['fieldName' => 'quantity', 'class' => 'text-center'],
            ['fieldName' => 'stock_in', 'class' => 'text-center'],
            ['fieldName' => 'stock_out', 'class' => 'text-center'],
            ['fieldName' => 'sku', 'class' => 'text-center'],
            ['fieldName' => 'status', 'class' => 'text-center'],
        ];
    }
    private function getTableHeaders()
    {
        return [
            'Sl/No',
            'Category Name',
            'Quantity',
            'Stock In',
            'Stock Out',
            'Stock Keeping Unit',
            'Status',
            'Action',
        ];
    }

    public function create()
    {
        return Inertia::render(
            'Backend/Inventory/Form',
            [
                'pageTitle' => fn() => 'Inventory Create',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'Inventory Manage'],
                    ['link' => route('backend.inventory.create'), 'title' => 'Inventory Create'],
                ],
                'categories' => fn() => $this->categoryService->list()->get(),
            ]
        );
    }

    public function store(InventoryRequest $request)
    {
        DB::beginTransaction();
        try {

            $data = $request->validated();

            $data['author_id'] = auth('admin')->user()->id;
            $dataInfo = $this->inventoryService->create($data);

            if ($dataInfo) {
                $message = 'Inventory created successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'inventories', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To create inventory.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'InventoryController', 'store', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->with('errorMessage', $message);
        }
    }

    public function edit($id)
    {
        $inventory = $this->inventoryService->find($id);

        return Inertia::render(
            'Backend/Inventory/Form',
            [
                'pageTitle' => fn() => 'Inventory Edit',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'Inventory Manage'],
                    ['link' => route('backend.inventory.edit', $inventory->id), 'title' => 'Inventory Edit'],
                ],
                'inventory' => fn() => $inventory,
                'id' => fn() => $id,
                'products' => fn() => $this->categoryService->activeList(),
            ]
        );
    }

    public function update(InventoryRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $inventory = $this->inventoryService->find($id);
            $data = $request->validated();

            $dataInfo = $this->inventoryService->update($data, $id);
            if ($dataInfo->wasChanged()) {
                $message = 'Inventory updated successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'inventories', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To update Inventory.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'InventoryController', 'update', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->with('errorMessage', $message);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $deletionSuccess = $this->inventoryService->delete($id);

            if ($deletionSuccess) {
                $message = 'Inventory deleted successfully';

                $this->storeAdminWorkLog($id, 'inventories', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed to delete inventory. It may not exist or has already been deleted.";

                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $message = 'An error occurred while deleting the inventory: ' . $e->getMessage();
            return redirect()
                ->back()
                ->with('errorMessage', $message);
        }
    }

    public function changeStatus()
    {
        DB::beginTransaction();

        try {
            $dataInfo = $this->inventoryService->changeStatus(request());

            if ($dataInfo->wasChanged()) {
                $message = 'Inventory ' . request()->status . ' Successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'inventories', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To " . request()->status . " Inventory.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'InventoryController', 'changeStatus', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->with('errorMessage', $message);
        }
    }
}
