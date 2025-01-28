<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\InvoiceService;
use App\Services\ProductService;
use App\Traits\SystemTrait;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    use SystemTrait;

    protected $invoiceService, $productService;

    public function __construct(InvoiceService $invoiceService, ProductService $productService)
    {
        $this->invoiceService = $invoiceService;
        $this->productService = $productService;
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
            ]
        );
    }

    private function getDataFields()
    {
        return [
            ['fieldName' => 'index', 'class' => 'text-center'],
            ['fieldName' => 'order_date', 'class' => 'text-center'],
            ['fieldName' => 'order_id', 'class' => 'text-center'],
            ['fieldName' => 'patient_id', 'class' => 'text-center'],
            ['fieldName' => 'total', 'class' => 'text-center'],
            // ['fieldName' => 'branch_id', 'class' => 'text-center'],
            // ['fieldName' => 'billing_module_id', 'class' => 'text-center'],
            ['fieldName' => 'status', 'class' => 'text-center'],
        ];
    }
    private function getTableHeaders()
    {
        return [
            'Sl/No',
            'Order Date',
            'Order ID',
            // 'Branch Id',
            // 'Billing Module',
            'Person Name',
            'Total',
            'Payment Status',
            'Action'
        ];
    }

    private function getDatas()
    {
        $query = $this->invoiceService->list();

        $query->whereHas('invoiceData', function ($q) {
            $q->whereNull('room_id')->whereNull('bed_id');
        });

        if (request()->filled('order_id')) {
            //invoice_id = order_id
            $invoice_id = '%' . request()->order_id . '%';
            $query->where('id', 'like', $invoice_id);
        }

        if (request()->filled('name')) {
            $name = '%' . request()->name . '%';
            $query->where(function ($query) use ($name) {
                $query->whereHas('branch', function ($q) use ($name) {
                    $q->orWhere('name', 'like', $name);
                })->orWhereHas('patient', function ($q) use ($name) {
                    $q->orWhere('name', 'like', $name);
                })->orWhereHas('admin', function ($q) use ($name) {
                    $q->orWhere('first_name', 'like', $name)
                        ->orWhere('last_name', 'like', $name);
                });
            });
        }

        $datas = $query->paginate(request()->numOfData ?? 10)->withQueryString();
        $formatedDatas = $datas->map(function ($data, $index) {

            $firstName = $data->admin?->first_name ?? '';
            $lastName = $data->admin?->last_name ?? '';
            $customData = new \stdClass();
            $customData->index = $index + 1;
            $customData->order_date = date('d M Y H:i A', strtotime($data->created_at));
            $customData->order_id = $data->id;
            $customData->patient_id = $data->patient?->name ?? ($firstName . ' ' . $lastName) ?? '';
            $customData->total = $data->total_price;
            // $customData->branch_id = $data->branch?->name ?? '';
            // $customData->billing_module_id = $data->billingModule?->name ?? '';
            $customData->source = $data->invoice_data->source ?? '';

            $customData->status = $data->payment_status ?? "";
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
                    'linkClass' => 'bg-yellow-400 text-black semi-bold',
                    'link' => route('backend.invoice.edit', $data->id),
                    'linkLabel' => getLinkLabel('Edit', null, null)
                ],

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
            ]
        );
    }

    public function productDetails($productNo)
    {
        $productDetails = $this->productService->getByProductNumber($productNo);
        dd($productDetails);
    }
}
