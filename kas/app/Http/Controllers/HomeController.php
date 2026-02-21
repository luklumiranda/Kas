<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Customer;
use App\Models\Deposit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Get total balance (sum of current_balance dari deposits terbaru per customer)
        $totalBalance = DB::table('deposits')
            ->selectRaw('customer_id, MAX(current_balance) as latest_balance')
            ->groupBy('customer_id')
            ->get()
            ->sum('latest_balance');

        // Get monthly balance data for last 12 months using MySQL DATE_FORMAT
        $monthlyData = DB::table('deposits')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, MAX(current_balance) as balance")
            ->whereRaw("created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)")
            ->groupByRaw("DATE_FORMAT(created_at, '%Y-%m')")
            ->orderByRaw("DATE_FORMAT(created_at, '%Y-%m')")
            ->get();

        // Format monthly data for chart
        $months = [];
        $balances = [];
        
        foreach ($monthlyData as $data) {
            $date = Carbon::createFromFormat('Y-m', $data->month);
            $months[] = $date->format('M Y');
            $balances[] = (int)$data->balance;
        }

        return view('pages.dashboard', [
            'title' => 'Dashboard',
            'totalBalance' => $totalBalance,
            'months' => $months,
            'balances' => $balances
        ]);
    }

    public function profile()
    {
        return view('pages.profile', [
            'title' => 'Pengaturan',
            'profile' => Auth::user()
        ]);
    }

    public function update(UpdateProfileRequest $request)
    {
        try {
            $user = Auth::user();
            $data = $request->except('photo');
            $data['photo'] = $this->updateImage($request, $user->photo);
            $user->update($data);
            return back()->with('success', 'Berhasil mengupdate profil!');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function truncate()
    {
        try {
            Artisan::call('migrate:fresh --seed');
            Auth::logout();
            return back()->with('success', 'Berhasil mereset data!');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
}
