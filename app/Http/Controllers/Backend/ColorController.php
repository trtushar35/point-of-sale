<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\ColorRequest;
use App\Services\ColorService;
use App\Traits\SystemTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ColorController extends Controller
{
    use SystemTrait;

    protected $colorService;

    public function __construct(ColorService $colorService)
    {
        $this->colorService = $colorService;

        $this->middleware('auth:admin');
        $this->middleware('permission:color-add', ['only' => ['create']]);
        $this->middleware('permission:color-add', ['only' => ['store']]);
        $this->middleware('permission:color-edit', ['only' => ['edit|update']]);
        $this->middleware('permission:color-list', ['only' => ['index']]);
        $this->middleware('permission:color-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        return Inertia::render(
            'Backend/Color/Index',
            [
                'pageTitle' => fn() => 'Color List',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'Color Manage'],
                    ['link' => route('backend.Color.index'), 'title' => 'Color List'],
                ],
                'tableHeaders' => fn() => $this->getTableHeaders(),
                'dataFields' => fn() => $this->dataFields(),
                'datas' => fn() => $this->getDatas(),
            ]
        );
    }

    private function getDatas()
    {
        $query = $this->colorService->list();

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
                    'link' => route('backend.Color.status.change', ['id' => $data->id, 'status' => $data->status == 'Active' ? 'Inactive' : 'Active']),
                    'linkLabel' => getLinkLabel((($data->status == 'Active') ? "Inactive" : "Active"), null, null)
                ],
                [
                    'linkClass' => 'bg-yellow-400 text-black semi-bold',
                    'link' => route('backend.Color.edit',  $data->id),
                    'linkLabel' => getLinkLabel('Edit', null, null)
                ],
                [
                    'linkClass' => 'deleteButton bg-red-500 text-white semi-bold',
                    'link' => route('backend.Color.destroy', $data->id),
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
            'Color Name',
            'Status',
            'Action',
        ];
    }

    public function create()
    {
        return Inertia::render(
            'Backend/Color/Form',
            [
                'pageTitle' => fn() => 'Color Create',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'Color Manage'],
                    ['link' => route('backend.Color.create'), 'title' => 'Color Create'],
                ],
            ]
        );
    }

    public function store(ColorRequest $request)
    {
        DB::beginTransaction();
        try {

            $data = $request->validated();

            $dataInfo = $this->colorService->create($data);

            if ($dataInfo) {
                $message = 'Color created successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'Colors', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To create Color.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'ColorController', 'store', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->with('errorMessage', $message);
        }
    }

    public function edit($id)
    {
        $color = $this->colorService->find($id);

        return Inertia::render(
            'Backend/Color/Form',
            [
                'pageTitle' => fn() => 'Color Edit',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'Color Manage'],
                    ['link' => route('backend.Color.edit', $color->id), 'title' => 'Color Edit'],
                ],
                'color' => fn() => $color,
                'id' => fn() => $id,
            ]
        );
    }

    public function update(ColorRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $Color = $this->colorService->find($id);
            $data = $request->validated();

            $dataInfo = $this->colorService->update($data, $id);
            if ($dataInfo->wasChanged()) {
                $message = 'Color updated successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'Colors', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To update Color.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'ColorController', 'update', substr($err->getMessage(), 0, 1000));
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
            // Attempt to delete the Color through your service
            $deletionSuccess = $this->colorService->delete($id);

            if ($deletionSuccess) {
                $message = 'Color deleted successfully';

                // Log the successful deletion (assuming your logging method works)
                $this->storeAdminWorkLog($id, 'Colors', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed to delete Color. It may not exist or has already been deleted.";

                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $message = 'An error occurred while deleting the Color: ' . $e->getMessage();
            return redirect()
                ->back()
                ->with('errorMessage', $message);
        }
    }

    public function changeStatus()
    {
        DB::beginTransaction();

        try {
            $dataInfo = $this->colorService->changeStatus(request());

            if ($dataInfo->wasChanged()) {
                $message = 'Color ' . request()->status . ' Successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'Colors', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To " . request()->status . " Color.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'ColorController', 'changeStatus', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->with('errorMessage', $message);
        }
    }
}
