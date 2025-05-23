<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Services\CategoryService;
use App\Services\ColorService;
use App\Services\InventoryService;
use App\Services\ProductService;
use App\Services\SizeService;
use App\Traits\SystemTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Picqer\Barcode\BarcodeGeneratorPNG;

class ProductController extends Controller
{
    use SystemTrait;

    protected $productService, $colorService, $categoryService, $sizeService, $inventoryService;

    public function __construct(ProductService $productService, ColorService $colorService, CategoryService $categoryService, SizeService $sizeService, InventoryService $inventoryService)
    {
        $this->productService = $productService;
        $this->colorService = $colorService;
        $this->categoryService = $categoryService;
        $this->sizeService = $sizeService;
        $this->inventoryService = $inventoryService;
    }

    public function index()
    {
        return Inertia::render(
            'Backend/Product/Index',
            [
                'pageTitle' => fn() => 'Product List',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'Product Manage'],
                    ['link' => route('backend.product.index'), 'title' => 'Product List'],
                ],
                'tableHeaders' => fn() => $this->getTableHeaders(),
                'dataFields' => fn() => $this->dataFields(),
                'datas' => fn() => $this->getDatas(),
                'colors' => fn() => $this->colorService->activeList()->get(),
                'categories' => fn() => $this->categoryService->activeList()->get(),
            ]
        );
    }

    private function getDatas()
    {
        $query = $this->productService->activeList();

        if (request()->filled('category_id'))
            $query->where('category_id', request()->category_id);

        if (request()->filled('color_id')) {
            $query->where('color_id', request()->color_id);
        }

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
            $customData->name = $data->name ?? '';
            $customData->size_id = $data->size->size;
            $customData->color_id = $data->color->name ?? '';
            $customData->price = $data->price;
            $customData->role_name = $data->role?->name;
            $customData->image = $data->image ? '<img src="' . $data->image . '" height="50" width="50"/>' : 'No Image';
            $customData->status = getStatusText($data->status);

            $customData->hasLink = true;
            $customData->links = [
                [
                    'linkClass' => 'semi-bold text-white statusChange ' . (($data->status == 'Active') ? "bg-gray-500" : "bg-green-500"),
                    'link' => route('backend.product.status.change', ['id' => $data->id, 'status' => $data->status == 'Active' ? 'Inactive' : 'Active']),
                    'linkLabel' => getLinkLabel((($data->status == 'Active') ? "Inactive" : "Active"), null, null)
                ],
                [
                    'linkClass' => 'bg-yellow-400 text-black semi-bold',
                    'link' => route('backend.product.edit',  $data->id),
                    'linkLabel' => getLinkLabel('Edit', null, null)
                ],
                [
                    'linkClass' => 'deleteButton bg-red-500 text-white semi-bold',
                    'link' => route('backend.product.destroy', $data->id),
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
            ['fieldName' => 'name', 'class' => 'text-center'],
            ['fieldName' => 'size_id', 'class' => 'text-center'],
            ['fieldName' => 'color_id', 'class' => 'text-center'],
            ['fieldName' => 'price', 'class' => 'text-center'],
            ['fieldName' => 'image', 'class' => 'text-center'],
            ['fieldName' => 'status', 'class' => 'text-center'],
        ];
    }
    private function getTableHeaders()
    {
        return [
            'Sl/No',
            'Category Name',
            'Product Name',
            'Size',
            'Color',
            'Price',
            'Image',
            'Status',
            'Action',
        ];
    }

    public function downloadPdf(Request $request)
    {
        $query = $this->productService->list()->with('color');
        $datas = $query->get();

        if ($datas->isEmpty()) {
            return redirect()->back();
        }

        $barcodeGenerator = new BarcodeGeneratorPNG();
        foreach ($datas as $data) {
            $data->barcode = 'data:image/png;base64,' . base64_encode(
                $barcodeGenerator->getBarcode($data->id, $barcodeGenerator::TYPE_CODE_128)
            );
        }

        $pdf = PDF::loadView('pdf.productsPdf', compact('datas'));
        return $pdf->download('products_list.pdf');
    }


    public function create()
    {
        return Inertia::render(
            'Backend/Product/Form',
            [
                'pageTitle' => fn() => 'Product Create',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'Product Manage'],
                    ['link' => route('backend.product.create'), 'title' => 'Product Create'],
                ],
                'colors' => fn() => $this->colorService->activeList()->get(),
                'categories' => fn() => $this->categoryService->activeList()->get(),
                'sizes' => fn() => $this->sizeService->activeList()->get(),
            ]
        );
    }

    public function store(ProductRequest $request)
    {
        DB::beginTransaction();
        try {

            $data = $request->validated();

            $data['product_no'] = $this->productService->generateProductNo();

            if ($request->hasFile('image'))
                $data['image'] = $this->imageUpload($request->file('image'), 'products');

            $dataInfo = $this->productService->create($data);

            $categoryInventory = $this->inventoryService->getInventoryByCatgegory($dataInfo->category_id);

            // dd($categoryInventory);

            if ($categoryInventory) {
                $categoryInventory->quantity = $categoryInventory->quantity + 1;
                $categoryInventory->stock_in = $categoryInventory->stock_in + 1;
                $categoryInventory->sku = $categoryInventory->sku + 1;
                $categoryInventory->save();
            } else {
                $inventoryData = [
                    'category_id' => $dataInfo->category_id,
                    'quantity' => 1,
                    'stock_in' => 1,
                    'sku'=> 1,
                    'status' => 'Active'
                ];
                $this->inventoryService->create($inventoryData);
            }

            if ($dataInfo) {
                $message = 'Product created successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'products', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To create product.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'ProductController', 'store', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->with('errorMessage', $message);
        }
    }

    public function edit($id)
    {
        $product = $this->productService->find($id);

        return Inertia::render(
            'Backend/Product/Form',
            [
                'pageTitle' => fn() => 'Product Edit',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'Product Manage'],
                    ['link' => route('backend.product.edit', $product->id), 'title' => 'Product Edit'],
                ],
                'product' => fn() => $product,
                'id' => fn() => $id,
                'colors' => fn() => $this->colorService->activeList()->get(),
                'categories' => fn() => $this->categoryService->activeList()->get(),
                'sizes' => fn() => $this->sizeService->activeList()->get(),
            ]
        );
    }

    public function update(ProductRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $product = $this->productService->find($id);
            $data = $request->validated();
            if ($request->hasFile('image')) {
                $data['image'] = $this->imageUpload($request->file('image'), 'products');
                if (isset($product->image)) {
                    $path = strstr($product->image, 'storage/');
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
            } else {
                $data['image'] = strstr($product->image, 'products/');
            }

            $dataInfo = $this->productService->update($data, $id);
            if ($dataInfo->wasChanged()) {
                $message = 'Product updated successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'products', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To update Product.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'ProductController', 'update', substr($err->getMessage(), 0, 1000));
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
            // Attempt to delete the product through your service
            $deletionSuccess = $this->productService->delete($id);

            if ($deletionSuccess) {
                $message = 'Product deleted successfully';

                // Log the successful deletion (assuming your logging method works)
                $this->storeAdminWorkLog($id, 'products', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed to delete product. It may not exist or has already been deleted.";

                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $message = 'An error occurred while deleting the product: ' . $e->getMessage();
            return redirect()
                ->back()
                ->with('errorMessage', $message);
        }
    }

    public function changeStatus()
    {
        DB::beginTransaction();

        try {
            $dataInfo = $this->productService->changeStatus(request());

            if ($dataInfo->wasChanged()) {
                $message = 'Product ' . request()->status . ' Successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'products', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To " . request()->status . " Product.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'ProductController', 'changeStatus', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->with('errorMessage', $message);
        }
    }

    public function categoryWiseSizes($categoryId)
    {
        $sizes = $this->sizeService->categorySizes($categoryId);

        return $sizes;
    }
}
