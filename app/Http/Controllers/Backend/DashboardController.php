<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\AdminService;
use App\Services\DashboardService;
use Inertia\Inertia;
use Exception;

class DashboardController extends Controller
{
    protected $dashboardService, $viewAreaService, $adminService;


    public function __construct(DashboardService $dashboardService, AdminService $adminService)
    {
        $this->dashboardService = $dashboardService;
        $this->adminService = $adminService;
    }

    public function index()
    {
        $dashboardData['inActiveUsers'] = $this->dashboardService->countInActiveUser();
        $dashboardData['activeUsers'] = $this->dashboardService->countActiveUser();
        $dashboardData['authUserDetails'] = auth('admin')->user();

        $data = [
            'pageTitle' => 'Dashboard',
            'breadcrumbs' => [
                ['link' => route('backend.dashboard'), 'title' => 'Dashboard'],
            ],
            'dashboardData' => fn() => $dashboardData,
        ];

        return Inertia::render('Backend/Dashboard', $data);
    }
}
