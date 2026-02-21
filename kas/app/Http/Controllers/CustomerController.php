<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\Deposit;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->title = 'Nasabah';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(Customer::query())
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('customer.show', $row) . '" class="btn btn-success btn-xs px-2"> Detail </a>
                            <a href="' . route('customer.edit', $row) . '" class="btn btn-primary btn-xs px-2 mx-1"> Edit </a>
                            <form class="d-inline" method="POST" action="' . route('customer.destroy', $row) . '">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="' . csrf_token() . '" />
                                <button type="submit" class="btn btn-danger btn-xs px-2 delete-data"> Hapus </button>
                            </form>';
                })
                ->editColumn('joined_at', function($row) {
                    return Carbon::parse($row->joined_at)->isoFormat('DD-MM-Y');
                })
                ->editColumn('status', function($row) {
                    if ($row->status == 'blacklist') {
                        return '<span class="badge d-block p-2 badge-danger">Blacklist</span>';
                    }
                    return '<span class="badge d-block p-2 badge-success">Active</span>';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('pages.customer.index', [
            'title' => $this->title
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.customer.create', [
            'title' => $this->buildTitle('baru')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCustomerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCustomerRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->except('amount');
            $data['photo'] = $this->storeImage($request);
            $customer = Customer::create($data);
            Deposit::create([
                'created_at' => $request->joined_at,
                'amount' => $request->get('amount'),
                'current_balance' => $request->get('amount'),
                'type' => 'pokok',
                'customer_id' => $customer->id,
            ]);

            DB::commit();
            return redirect()->route('customer.index')->with('success', 'Berhasil menambahkan nasabah!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $nasabah
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $nasabah)
    {
        // $data = Deposit::selectRaw("customer_id, DATE(created_at) as tanggal, SUM(CASE WHEN type='pokok' THEN amount ELSE 0 END) as pokok, SUM(CASE WHEN type='sukarela' THEN amount ELSE 0 END) as sukarela, SUM(CASE WHEN type='wajib' THEN amount ELSE 0 END) as wajib, SUM(CASE WHEN type='pokok' THEN amount ELSE 0 END) + SUM(CASE WHEN type='sukarela' THEN amount ELSE 0 END) + SUM(CASE WHEN type='wajib' THEN amount ELSE 0 END) AS saldo")->where('customer_id', 5)->groupByRaw('customer_id, DATE(created_at)')->orderByRaw('DATE(created_at) DESC')->get();
        // dd($data);
        // SELECT SUM(CASE WHEN type='pokok' THEN amount ELSE 0 END) as pokok, SUM(CASE WHEN type='sukarela' THEN amount ELSE 0 END) as sukarela, SUM(CASE WHEN type='wajib' THEN amount ELSE 0 END) as wajib, SUM(CASE WHEN type='pokok' THEN amount ELSE 0 END) + SUM(CASE WHEN type='sukarela' THEN amount ELSE 0 END) + SUM(CASE WHEN type='wajib' THEN amount ELSE 0 END)  AS saldo FROM deposits WHERE customer_id = 5;
        // SELECT customer_id, SUM(CASE WHEN type='pokok' THEN amount ELSE 0 END) as pokok, SUM(CASE WHEN type='sukarela' THEN amount ELSE 0 END) as sukarela, SUM(CASE WHEN type='wajib' THEN amount ELSE 0 END) as wajib, SUM(CASE WHEN type='pokok' THEN amount ELSE 0 END) + SUM(CASE WHEN type='sukarela' THEN amount ELSE 0 END) + SUM(CASE WHEN type='wajib' THEN amount ELSE 0 END)  AS saldo FROM deposits GROUP By customer_id;
        // SELECT DATE(created_at) as tanggal, SUM(CASE WHEN type='pokok' THEN amount ELSE 0 END) as pokok, SUM(CASE WHEN type='sukarela' THEN amount ELSE 0 END) as sukarela, SUM(CASE WHEN type='wajib' THEN amount ELSE 0 END) as wajib, SUM(CASE WHEN type='pokok' THEN amount ELSE 0 END) + SUM(CASE WHEN type='sukarela' THEN amount ELSE 0 END) + SUM(CASE WHEN type='wajib' THEN amount ELSE 0 END)  AS saldo FROM deposits WHERE customer_id = 5 GROUP BY DATE(created_at);
        // SELECT customer_id, DATE(created_at) as tanggal, SUM(CASE WHEN type='pokok' THEN amount ELSE 0 END) as pokok, SUM(CASE WHEN type='sukarela' THEN amount ELSE 0 END) as sukarela, SUM(CASE WHEN type='wajib' THEN amount ELSE 0 END) as wajib, SUM(CASE WHEN type='pokok' THEN amount ELSE 0 END) + SUM(CASE WHEN type='sukarela' THEN amount ELSE 0 END) + SUM(CASE WHEN type='wajib' THEN amount ELSE 0 END)  AS saldo FROM deposits GROUP BY customer_id, DATE(created_at);
        return view('pages.customer.show', [
            'title' => $this->buildTitle('detail'),
            'user' => $nasabah
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $nasabah
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $nasabah)
    {
        return view('pages.customer.edit', [
            'title' => $this->buildTitle('edit'),
            'user' => $nasabah
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCustomerRequest  $request
     * @param  \App\Models\Customer  $nasabah
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCustomerRequest $request, Customer $nasabah)
    {
        try {
            $data = $request->except('photo');
            $data['photo'] = $this->updateImage($request, $nasabah->photo);
            $nasabah->update($data);
            return back()->with('success', 'Berhasil mengedit nasabah!');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $nasabah
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $nasabah)
    {
        try {
            DB::beginTransaction();
            $this->deleteImage($nasabah->photo);
            $nasabah->visits()->delete();
            $nasabah->foreclosures()->delete();
            $nasabah->deposits()->delete();
            $nasabah->loans()->delete();
            $nasabah->collaterals()->delete();
            $nasabah->delete();
            DB::commit();
            return back()->with('success', 'Berhasil menghapus nasabah!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function print(Request $request)
    {
        $filter = $request->validate([
            'time_from' => 'required',
            'time_to' => 'required',
        ]);

        $time_from = date('d-m-Y', strtotime($filter['time_from']));
        $time_to = date('d-m-Y', strtotime($filter['time_to']));

        $data = Customer::whereBetween('joined_at', $filter)->orderBy('joined_at')->get();
        $manager = User::where('role', 'manager')->first();
        $filename = Carbon::now()->isoFormat('DD-MM-Y') . '_-_laporan_data_nasabah_periode_' . $time_from . '_-_' . $time_to  . '_' . time() . '.pdf';

        $pdf = PDF::loadView('pages.customer.print', [
            'title' => 'Laporan Data Nasabah',
            'user' => auth()->user(),
            'filter' => "$time_from sampai $time_to",
            'date' => Carbon::now()->isoFormat('dddd, D MMMM Y'),
            'manager' => $manager,
            'data' => $data
        ]);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download($filename);
    }

    public function loan($id)
    {
        try {
            $data = Loan::with(['collateral'])->where('customer_id', $id)->get();
            return response()->json([
                'status' => 'success',
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ]);
        }
    }

    public function currentBalanceByDeposit($id)
    {
        try {
            $savings = Deposit::where('customer_id', $id)->where('type', 'sukarela')->sum('amount');
            $withdrawal = Deposit::where('customer_id', $id)->where('type', 'penarikan')->sum('amount');
            $data = Deposit::where('customer_id', $id)->latest()->first();
            $data->current_balance = $savings - $withdrawal;
            $data->current_balance_formatted = 'Rp' . number_format($data->current_balance, '2', ',', '.');
            return response()->json([
                'status' => 'success',
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     * API Login untuk Mobile App
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiLogin(Request $request)
    {
        try {
            $credentials = $request->validate([
                'number' => 'nullable|string',
                'username' => 'nullable|string',
                'password' => 'required|string',
            ]);

            // Cari dari users table dulu (dengan field username)
            $user = null;
            if (!empty($credentials['username'])) {
                $user = \App\Models\User::where('username', $credentials['username'])->first();
            } elseif (!empty($credentials['number'])) {
                $user = Customer::where('number', $credentials['number'])->first();
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Username/Number harus diisi',
                    'data' => null
                ], 422);
            }

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan',
                    'data' => null
                ], 401);
            }

            // Validasi password
            if (!password_verify($credentials['password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password salah',
                    'data' => null
                ], 401);
            }

            // Generate token
            $token = $user->createToken('mobile-app')->plainTextToken;

            // Format response
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username ?? $user->number ?? '',
                'gender' => $user->gender ?? null,
                'birth' => $user->birth ?? null,
                'address' => $user->address ?? null,
                'phone' => $user->phone ?? null,
                'last_education' => $user->last_education ?? null,
                'profession' => $user->profession ?? null,
                'status' => $user->status ?? 'active',
                'photo' => $user->photo ?? null,
                'role' => $user->role ?? 'Siswa'
            ];

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'user' => $userData,
                    'token' => $token
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $th) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $th->validator->errors()->flatten()->all()),
                'data' => null
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $th->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * API Register untuk Mobile App
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiRegister(Request $request)
    {
        try {
            $data = $request->validate([
                'nik' => 'required|string|unique:customers,nik',
                'name' => 'required|string',
                'number' => 'required|string|unique:customers,number',
                'password' => 'required|string|min:6',
                'gender' => 'required|in:L,P',
                'phone' => 'nullable|string|unique:customers,phone',
                'address' => 'nullable|string',
                'birth' => 'nullable|date',
                'last_education' => 'nullable|string',
                'profession' => 'nullable|string',
            ]);

            // Hash password
            $data['password'] = bcrypt($data['password']);
            $data['status'] = 'active';
            $data['joined_at'] = now();

            // Create customer
            $customer = Customer::create($data);

            // Buat deposit awal (opsional)
            Deposit::create([
                'customer_id' => $customer->id,
                'amount' => 0,
                'current_balance' => 0,
                'type' => 'pokok',
                'created_at' => now()
            ]);

            // Generate token
            $token = $customer->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil',
                'data' => [
                    'user' => [
                        'id' => $customer->id,
                        'nik' => $customer->nik,
                        'name' => $customer->name,
                        'number' => $customer->number,
                        'gender' => $customer->gender,
                        'birth' => $customer->birth,
                        'address' => $customer->address,
                        'phone' => $customer->phone,
                        'last_education' => $customer->last_education,
                        'profession' => $customer->profession,
                        'status' => $customer->status,
                        'photo' => $customer->photo,
                        'joined_at' => $customer->joined_at,
                        'role' => 'Nasabah'
                    ],
                    'token' => $token
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $th) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $th->validator->errors()->flatten()->all()),
                'data' => null
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $th->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * API: Get all customers untuk Mobile App
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiIndex()
    {
        try {
            $customers = Customer::select(
                'id',
                'nik',
                'name',
                'number',
                'gender',
                'birth',
                'address',
                'phone',
                'last_education',
                'profession',
                'status',
                'photo',
                'joined_at',
                'created_at',
                'updated_at'
            )->get();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diambil',
                'data' => $customers
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $th->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * API: Get single customer detail untuk Mobile App
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiShow($id)
    {
        try {
            $customer = Customer::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diambil',
                'data' => $customer
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return response()->json([
                'success' => false,
                'message' => 'Nasabah tidak ditemukan',
                'data' => null
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $th->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * API: Create new customer untuk Mobile App
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiStore(Request $request)
    {
        try {
            $data = $request->validate([
                'nik' => 'required|string|unique:customers,nik',
                'name' => 'required|string',
                'number' => 'required|string|unique:customers,number',
                'password' => 'required|string|min:6',
                'gender' => 'required|in:L,P',
                'phone' => 'nullable|string|unique:customers,phone',
                'address' => 'nullable|string',
                'birth' => 'nullable|date',
                'last_education' => 'nullable|string',
                'profession' => 'nullable|string',
            ]);

            $data['password'] = bcrypt($data['password']);
            $data['status'] = 'active';
            $data['joined_at'] = now();

            $customer = Customer::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Nasabah berhasil ditambahkan',
                'data' => $customer
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $th) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'data' => null
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $th->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * API: Update customer untuk Mobile App
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiUpdate(Request $request, $id)
    {
        try {
            $customer = Customer::findOrFail($id);

            $data = $request->validate([
                'nik' => 'sometimes|string|unique:customers,nik,' . $id,
                'name' => 'sometimes|string',
                'number' => 'sometimes|string|unique:customers,number,' . $id,
                'gender' => 'sometimes|in:L,P',
                'phone' => 'nullable|string|unique:customers,phone,' . $id,
                'address' => 'nullable|string',
                'birth' => 'nullable|date',
                'last_education' => 'nullable|string',
                'profession' => 'nullable|string',
                'status' => 'sometimes|in:active,blacklist',
            ]);

            $customer->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Nasabah berhasil diperbarui',
                'data' => $customer
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return response()->json([
                'success' => false,
                'message' => 'Nasabah tidak ditemukan',
                'data' => null
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $th->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * API: Delete customer untuk Mobile App
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiDestroy($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Nasabah berhasil dihapus',
                'data' => null
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return response()->json([
                'success' => false,
                'message' => 'Nasabah tidak ditemukan',
                'data' => null
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $th->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
