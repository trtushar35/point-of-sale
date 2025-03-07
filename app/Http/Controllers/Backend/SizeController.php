<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\SizeRequest;
use App\Services\CategoryService;
use App\Services\SizeService;
use App\Traits\SystemTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SizeController extends Controller
{
    use SystemTrait;

    protected $SizeService, $categoryService;

    public function __construct(SizeService $SizeService, CategoryService $categoryService)
    {
        $this->SizeService = $SizeService;
        $this->categoryService = $categoryService;

        $this->middleware('auth:admin');
        $this->middleware('permission:size-add', ['only' => ['create|store']]);
        $this->middleware('permission:size-edit', ['only' => ['edit|update']]);
        $this->middleware('permission:size-list', ['only' => ['index']]);
        $this->middleware('permission:size-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        return Inertia::render(
            'Backend/Size/Index',
            [
                'pageTitle' => fn() => 'Size List',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'Size Manage'],
                    ['link' => route('backend.Size.index'), 'title' => 'Size List'],
                ],
                'tableHeaders' => fn() => $this->getTableHeaders(),
                'dataFields' => fn() => $this->dataFields(),
                'datas' => fn() => $this->getDatas(),
                'categories' => fn() => $this->categoryService->activeList()->get(),
            ]
        );
    }

    
    private function dataFields()
    {
        return [
            ['fieldName' => 'index', 'class' => 'text-center'],
            ['fieldName' => 'category_id', 'class' => 'text-center'],
            ['fieldName' => 'size', 'class' => 'text-center'],
            ['fieldName' => 'status', 'class' => 'text-center'],
        ];
    }
    
    private function getTableHeaders()
    {
        return [
            'Sl/No',
            'Category',
            'Size',
            'Status',
            'Action',
        ];
    }

    private function getDatas()
    {
        $query = $this->SizeService->list();

        if (request()->filled('name'))
            $query->where('name', 'like', '%' . request()->name . '%');
        
            if (request()->filled('category_id'))
            $query->where('category_id', 'like', request()->category_id);

        $datas = $query->paginate(request()->numOfData ?? 10)->withQueryString();

        $formatedDatas = $datas->map(function ($data, $index) {
            $customData = new \stdClass();
            $customData->index = $index + 1;
            $customData->category_id = $data->category->name;
            $customData->size = $data->size;

            $customData->status = getStatusText($data->status);
            $customData->hasLink = true;
            $customData->links = [
                [
                    'linkClass' => 'semi-bold text-white statusChange ' . (($data->status == 'Active') ? "bg-gray-500" : "bg-green-500"),
                    'link' => route('backend.Size.status.change', ['id' => $data->id, 'status' => $data->status == 'Active' ? 'Inactive' : 'Active']),
                    'linkLabel' => getLinkLabel((($data->status == 'Active') ? "Inactive" : "Active"), null, null)
                ],
                [
                    'linkClass' => 'bg-yellow-400 text-black semi-bold',
                    'link' => route('backend.Size.edit',  $data->id),
                    'linkLabel' => getLinkLabel('Edit', null, null)
                ],
                [
                    'linkClass' => 'deleteButton bg-red-500 text-white semi-bold',
                    'link' => route('backend.Size.destroy', $data->id),
                    'linkLabel' => getLinkLabel('Delete', null, null)
                ]

            ];
            return $customData;
        });

        return regeneratePagination($formatedDatas, $datas->total(), $datas->perPage(), $datas->currentPage());
    }

    public function create()
    {
        return Inertia::render(
            'Backend/Size/Form',
            [
                'pageTitle' => fn() => 'Size Create',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'Size Manage'],
                    ['link' => route('backend.Size.create'), 'title' => 'Size Create'],
                ],
                'categories' => fn() => $this->categoryService->activeList()->get(),
            ]
        );
    }

    public function store(SizeRequest $request)
    {
        DB::beginTransaction();
        try {

            $data = $request->validated();

            $dataInfo = $this->SizeService->create($data);

            if ($dataInfo) {
                $message = 'Size created successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'Sizes', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To create Size.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'SizeController', 'store', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->with('errorMessage', $message);
        }
    }

    public function edit($id)
    {
        $Size = $this->SizeService->find($id);

        return Inertia::render(
            'Backend/Size/Form',
            [
                'pageTitle' => fn() => 'Size Edit',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'Size Manage'],
                    ['link' => route('backend.Size.edit', $Size->id), 'title' => 'Size Edit'],
                ],
                'Size' => fn() => $Size,
                'id' => fn() => $id,
                'categories' => fn() => $this->categoryService->activeList()->get(),
            ]
        );
    }

    public function update(SizeRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $Size = $this->SizeService->find($id);
            $data = $request->validated();

            $dataInfo = $this->SizeService->update($data, $id);
            if ($dataInfo->wasChanged()) {
                $message = 'Size updated successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'Categories', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To update Size.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'SizeController', 'update', substr($err->getMessage(), 0, 1000));
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
            // Attempt to delete the Size through your service
            $deletionSuccess = $this->SizeService->delete($id);

            if ($deletionSuccess) {
                $message = 'Size deleted successfully';

                // Log the successful deletion (assuming your logging method works)
                $this->storeAdminWorkLog($id, 'Categories', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed to delete Size. It may not exist or has already been deleted.";

                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $message = 'An error occurred while deleting the Size: ' . $e->getMessage();
            return redirect()
                ->back()
                ->with('errorMessage', $message);
        }
    }

    public function changeStatus()
    {
        DB::beginTransaction();

        try {
            $dataInfo = $this->SizeService->changeStatus(request());

            if ($dataInfo->wasChanged()) {
                $message = 'Size ' . request()->status . ' Successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'Categories', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To " . request()->status . " Size.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'SizeController', 'changeStatus', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->with('errorMessage', $message);
        }
    }
}
