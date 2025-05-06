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

        $this->middleware('auth:admin');
        $this->middleware('permission:product-add', ['only' => ['create', 'store']]);
        $this->middleware('permission:product-edit', ['only' => ['edit|update']]);
        $this->middleware('permission:product-list', ['only' => ['index']]);
        // $this->middleware('permission:product-delete', ['only' => ['destroy']]);
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
        $userId = auth('admin')->user()->id;
        $query = $this->productService->userWiseList($userId);

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
            $customData->product_no = $data->product_no;
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
            ['fieldName' => 'product_no', 'class' => 'text-center'],
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
            'Product Number',
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
        $from = $request->input('from');
        $to = $request->input('to');

        $query = $this->productService->activeList();
        if ($from && $to) {
            $query->skip($from - 1)->take($to - $from + 1);
        }

        $datas = $query->get();

        if ($datas->isEmpty()) {
            return redirect()->back();
        }

        $barcodeGenerator = new BarcodeGeneratorPNG();
        foreach ($datas as $data) {
            $barcodeImage = $barcodeGenerator->getBarcode($data->price . "-" . $data->size->size, $barcodeGenerator::TYPE_CODE_128, 2, 40);
            $data->barcode = 'data:image/png;base64,' . base64_encode($barcodeImage);
        }

        $pdf = PDF::loadView('pdf.productsPdf', compact('datas'));
        return $pdf->stream('products_list.pdf');
    }

    public function create()
    {
        return Inertia::render(
            'Backend/Product/Form',
            [
                'pageTitle' => fn() => 'Product Information',
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
            $data['author_id'] = auth('admin')->user()->id;

            if ($request->hasFile('image')) {
                $data['image'] = $this->imageUpload($request->file('image'), 'products');
            }

            $sizes = is_array($request->sizes) ? $request->sizes : [];
            $colorId = $request->color_id[0] ?? null;

            foreach ($sizes as $sizeEntry) {
                $sizeId = $sizeEntry['id']['id'];
                $quantity = $sizeEntry['quantity'];

                for ($i = 0; $i < $quantity; $i++) {
                    $uniqueProductNo = $this->productService->generateProductNo();

                    $product = $this->productService->create([
                        'name' => $data['name'],
                        'author_id' => $data['author_id'],
                        'category_id' => $data['category_id'],
                        'color_id' => $colorId,
                        'size_id' => $sizeId,
                        'price' => $data['price'],
                        'image' => $data['image'] ?? null,
                        'product_no' => $uniqueProductNo,
                        'status' => $data['status'] ?? 'Active',
                    ]);

                    $this->updateOrCreateInventory($data['category_id']);
                    $this->storeAdminWorkLog($product->id, 'products', 'Product created successfully');
                }
            }

            DB::commit();
            return redirect()->back()->with('successMessage', 'Product(s) created successfully');
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'ProductController', 'store', substr($err->getMessage(), 0, 1000));
            return redirect()->back()->with('errorMessage', 'Server error occurred. Please try again.');
        }
    }

    private function updateOrCreateInventory($categoryId)
    {
        $categoryInventory = $this->inventoryService->getInventoryByCategory($categoryId);

        if ($categoryInventory) {
            $categoryInventory->increment('quantity');
            $categoryInventory->increment('stock_in');
            $categoryInventory->increment('sku');
        } else {
            $this->inventoryService->create([
                'author_id' => auth('admin')->user()->id,
                'category_id' => $categoryId,
                'quantity' => 1,
                'stock_in' => 1,
                'sku' => 1,
                'status' => 'Active',
            ]);
        }
    }

    public function edit($id)
    {
        $product = $this->productService->find($id);

        return Inertia::render(
            'Backend/Product/EditForm',
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
            $deletionSuccess = $this->productService->delete($id);

            if ($deletionSuccess) {
                $message = 'Product deleted successfully';

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
