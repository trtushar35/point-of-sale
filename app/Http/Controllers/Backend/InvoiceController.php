<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Services\CategoryService;
use App\Services\ColorService;
use App\Services\InvoiceDetailService;
use App\Services\InvoiceService;
use App\Services\ProductService;
use App\Services\SizeService;
use App\Traits\SystemTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    use SystemTrait;

    protected $invoiceService, $productService, $colorService, $categoryService, $sizeService, $invoiceDetailsService;

    public function __construct(InvoiceService $invoiceService, ProductService $productService, ColorService $colorService, CategoryService $categoryService, SizeService $sizeService, InvoiceDetailService $invoiceDetailsService)
    {
        $this->invoiceService = $invoiceService;
        $this->productService = $productService;
        $this->sizeService = $sizeService;
        $this->categoryService = $categoryService;
        $this->colorService = $colorService;
        $this->invoiceDetailsService = $invoiceDetailsService;

        $this->middleware('auth:admin');
        $this->middleware('permission:invoice-add', ['only' => ['create']]);
        $this->middleware('permission:invoice-add', ['only' => ['store']]);
        $this->middleware('permission:invoice-edit', ['only' => ['edit|update']]);
        $this->middleware('permission:invoice-list', ['only' => ['index']]);
        $this->middleware('permission:invoice-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        return Inertia::render(
            'Backend/Invoice/Index',
            [
                'pageTitle' => fn() => 'Invoice List',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'Invoice Manage'],
                    ['link' => route('backend.invoice.index'), 'title' => 'Invoice List'],
                ],
                'tableHeaders' => fn() => $this->getTableHeaders(),
                'dataFields' => fn() => $this->getDataFields(),
                'datas' => fn() => $this->getDatas(),
            ]
        );
    }

    private function getDataFields()
    {
        return [
            ['fieldName' => 'index', 'class' => 'text-center'],
            ['fieldName' => 'invoice_date', 'class' => 'text-center'],
            ['fieldName' => 'invoice_no', 'class' => 'text-center'],
            ['fieldName' => 'product_no', 'class' => 'text-center'],
            ['fieldName' => 'total', 'class' => 'text-center'],
            ['fieldName' => 'status', 'class' => 'text-center'],
        ];
    }
    private function getTableHeaders()
    {
        return [
            'Sl/No',
            'Invoice Date',
            'Invoice No',
            'Product No',
            'Total',
            'Status',
            'Action'
        ];
    }

    private function getDatas()
    {
        $query = $this->invoiceService->listWithDetails();

        $datas = $query->paginate(request()->numOfData ?? 10)->withQueryString();
        $formatedDatas = $datas->map(function ($data, $index) {

            $customData = new \stdClass();
            $customData->index = $index + 1;
            $customData->invoice_date = date('d M Y H:i A', strtotime($data->invoice_date));
            $customData->invoice_no = $data->invoiceDetails->first()->invoice_id;

            $productNos = $data->invoiceDetails->pluck('product.product_no')->toArray();
            $customData->product_no = $productNos;

            $customData->total = $data->total_price;
            $customData->total = $data->total_price;

            $customData->status = $data->status ?? "";
            $customData->hasLink = true;
            $customData->links = [

                // [
                //     'linkClass' => 'semi-bold text-white statusChange ' . (($data->status == 'Active') ? "bg-gray-500" : "bg-green-500"),
                //     'link' => route('backend.invoice.status.change', ['id' => $data->id, 'status' => $data->status == 'Active' ? 'Inactive' : 'Active']),
                //     'linkLabel' => getLinkLabel((($data->status == 'Active') ? "Inactive" : "Active"), null, null)
                // ],

                [
                    'linkClass' => 'bg-green-600 text-white semi-bold',
                    'link' => route('backend.invoice.show', $data->id),
                    'linkLabel' => getLinkLabel('View', null, null)
                ],
                [
                    'linkClass' => 'bg-yellow-600 text-white semi-bold',
                    'link' => route('backend.invoice.show', $data->id),
                    'linkLabel' => getLinkLabel('Print', null, null)
                ],

                // [
                //     'linkClass' => 'bg-yellow-400 text-black semi-bold',
                //     'link' => route('backend.invoice.edit', $data->id),
                //     'linkLabel' => getLinkLabel('Edit', null, null)
                // ],

                // [
                //     'linkClass' => 'deleteButton bg-red-500 text-white semi-bold',
                //     'link' => route('backend.invoice.destroy', $data->id),
                //     'linkLabel' => getLinkLabel('Delete', null, null)
                // ]
            ];
            return $customData;
        });

        return regeneratePagination($formatedDatas, $datas->total(), $datas->perPage(), $datas->currentPage());
    }

    public function create()
    {
        return Inertia::render(
            'Backend/Invoice/Form',
            [
                'pageTitle' => fn() => 'Invoice Create',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'Invoice Manage'],
                    ['link' => route('backend.invoice.create'), 'title' => 'Invoice Create'],
                ],
                'colors' => fn() => $this->colorService->activeList()->get(),
                'categories' => fn() => $this->categoryService->activeList()->get(),
                'sizes' => fn() => $this->sizeService->activeList()->get(),
            ]
        );
    }

    public function productDetails($productNo)
    {
        $productDetails = $this->productService->getByProductNumber($productNo);

        if ($productDetails) {
            return response()->json($productDetails);
        }

        return response()->json(['message' => 'Product not found'], 404);
    }

    public function store(InvoiceRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            $invoiceData = [
                'invoice_date' => now(),
                'total_price' => $data['total_price']
            ];
            $invoiceInformation = $this->invoiceService->create($invoiceData);

            foreach ($data['products'] as $product) {

                $productDetails = $this->productService->getByProductNumber($product['product_no']);

                $invoiceDetailsData = [
                    'invoice_id' => $invoiceInformation->id,
                    'product_id' => $productDetails->id,
                    'price' => $product['price'],
                    'quantity' => $product['quantity']
                ];
                $this->invoiceDetailsService->create($invoiceDetailsData);
            }

            DB::commit();

            return redirect()
                ->back()
                ->with('successMessage', 'Invoice created successfully');
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'InvoiceController', 'store', substr($err->getMessage(), 0, 1000));
            return redirect()
                ->back()
                ->with('errorMessage', 'Server Errors Occur. Please Try Again.');
        }
    }

    public function show($id)
    {
        $invoice = $this->invoiceService->findWithDetails($id);

        $formattedInvoice = [
            'invoice_no' => $invoice->id,
            'invoice_date' => date('d M Y H:i A', strtotime($invoice->invoice_date)),
            'total_price' => $invoice->total_price,
            'status' => $invoice->status,
            'products' => $invoice->invoiceDetails->map(function ($detail) {
                return [
                    'product_no' => $detail->product->product_no,
                    'name' => $detail->product->name,
                    'quantity' => $detail->quantity,
                    'price' => $detail->price,
                    'total' => $detail->quantity * $detail->price,
                ];
            }),
        ];

        return Inertia::render(
            'Backend/Invoice/Preview',
            [
                'pageTitle' => fn() => 'Invoice Preview',
                'invoice' => $formattedInvoice,
            ],

        );
    }
}
