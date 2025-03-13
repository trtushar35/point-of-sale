<?php

namespace App\Http\Controllers\Backend;

use Exception;
use Inertia\Inertia;
use App\Traits\SystemTrait;
use App\Services\CategoryService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{
    use SystemTrait;

    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;

        $this->middleware('auth:admin');
        $this->middleware('permission:category-add', ['only' => ['create']]);
        $this->middleware('permission:category-add', ['only' => ['store']]);
        $this->middleware('permission:category-edit', ['only' => ['edit|update']]);
        $this->middleware('permission:category-list', ['only' => ['index']]);
        $this->middleware('permission:category-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        return Inertia::render(
            'Backend/Category/Index',
            [
                'pageTitle' => fn() => 'Category List',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'Category Manage'],
                    ['link' => route('backend.Category.index'), 'title' => 'Category List'],
                ],
                'tableHeaders' => fn() => $this->getTableHeaders(),
                'dataFields' => fn() => $this->dataFields(),
                'datas' => fn() => $this->getDatas(),
            ]
        );
    }

    private function getDatas()
    {
        $query = $this->categoryService->list();

        if (request()->filled('name'))
            $query->where('name', 'like', '%' . request()->name . '%');

        $datas = $query->paginate(request()->numOfData ?? 10)->withQueryString();

        $formatedDatas = $datas->map(function ($data, $index) {
            $customData = new \stdClass();
            $customData->index = $index + 1;
            $customData->name = $data->name;

            $customData->status = getStatusText($data->status);
            $customData->hasLink = true;
            $customData->links = [
                [
                    'linkClass' => 'semi-bold text-white statusChange ' . (($data->status == 'Active') ? "bg-gray-500" : "bg-green-500"),
                    'link' => route('backend.Category.status.change', ['id' => $data->id, 'status' => $data->status == 'Active' ? 'Inactive' : 'Active']),
                    'linkLabel' => getLinkLabel((($data->status == 'Active') ? "Inactive" : "Active"), null, null)
                ],
                [
                    'linkClass' => 'bg-yellow-400 text-black semi-bold',
                    'link' => route('backend.Category.edit',  $data->id),
                    'linkLabel' => getLinkLabel('Edit', null, null)
                ],
                [
                    'linkClass' => 'deleteButton bg-red-500 text-white semi-bold',
                    'link' => route('backend.Category.destroy', $data->id),
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
            ['fieldName' => 'name', 'class' => 'text-center'],
            ['fieldName' => 'status', 'class' => 'text-center'],
        ];
    }
    private function getTableHeaders()
    {
        return [
            'Sl/No',
            'Category Name',
            'Status',
            'Action',
        ];
    }

    public function create()
    {
        return Inertia::render(
            'Backend/Category/Form',
            [
                'pageTitle' => fn() => 'Category Create',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'Category Manage'],
                    ['link' => route('backend.Category.create'), 'title' => 'Category Create'],
                ],
            ]
        );
    }

    public function store(CategoryRequest $request)
    {
        DB::beginTransaction();
        try {

            $data = $request->validated();

            $dataInfo = $this->categoryService->create($data);

            if ($dataInfo) {
                $message = 'Category created successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'Categorys', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To create Category.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'CategoryController', 'store', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->with('errorMessage', $message);
        }
    }

    public function edit($id)
    {
        $Category = $this->categoryService->find($id);

        return Inertia::render(
            'Backend/Category/Form',
            [
                'pageTitle' => fn() => 'Category Edit',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'Category Manage'],
                    ['link' => route('backend.Category.edit', $Category->id), 'title' => 'Category Edit'],
                ],
                'category' => fn() => $Category,
                'id' => fn() => $id,
            ]
        );
    }

    public function update(CategoryRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $Category = $this->categoryService->find($id);
            $data = $request->validated();

            $dataInfo = $this->categoryService->update($data, $id);
            if ($dataInfo->wasChanged()) {
                $message = 'Category updated successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'Categories', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To update Category.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'CategoryController', 'update', substr($err->getMessage(), 0, 1000));
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
            // Attempt to delete the Category through your service
            $deletionSuccess = $this->categoryService->delete($id);

            if ($deletionSuccess) {
                $message = 'Category deleted successfully';

                // Log the successful deletion (assuming your logging method works)
                $this->storeAdminWorkLog($id, 'Categories', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed to delete Category. It may not exist or has already been deleted.";

                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $message = 'An error occurred while deleting the Category: ' . $e->getMessage();
            return redirect()
                ->back()
                ->with('errorMessage', $message);
        }
    }

    public function changeStatus()
    {
        DB::beginTransaction();

        try {
            $dataInfo = $this->categoryService->changeStatus(request());

            if ($dataInfo->wasChanged()) {
                $message = 'Category ' . request()->status . ' Successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'Categories', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To " . request()->status . " Category.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'CategoryController', 'changeStatus', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->with('errorMessage', $message);
        }
    }
}
