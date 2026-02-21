<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function __construct()
    {
        $this->title = 'Siswa';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(User::query())
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    if ($row->id == auth()->user()->id) {
                        return '<a class="badge badge-secondary mx-auto p-2" href="' . route('profile.show') . '">Pengaturan</a>';
                    }

                    if ($row->role == 'manager') {
                        return '<span class="badge badge-warning mx-auto p-2">Aksi Dilarang</span>';
                    }

                    return '<a href="' . route('student.show', $row) . '" class="btn btn-success btn-xs px-2"> Detail </a>
                            <a href="' . route('student.edit', $row) . '" class="btn btn-primary btn-xs px-2 mx-1"> Edit </a>
                            <form class="d-inline" method="POST" action="' . route('student.destroy', $row) . '">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="' . csrf_token() . '" />
                                <button type="submit" class="btn btn-danger btn-xs px-2 delete-data"> Hapus </button>
                            </form>';
                })
                ->editColumn('role', function($row) {
                    $roles = [
                        'manager' => 'DEVELOPER',
                        'teller' => 'BENDAHARA',
                        'collector' => 'SISWA'
                    ];
                    return $roles[$row->role] ?? strtoupper($row->role);
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.user.index', [
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
        return view('pages.user.create', [
            'title' => $this->buildTitle('baru')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $data = $request->except('photo');
            $data['password'] = Hash::make($data['password']);
            $data['photo'] = $this->storeImage($request);
            User::create($data);
            return redirect()->route('student.index')->with('success', 'Berhasil menambahkan siswa!');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $karyawan
     * @return \Illuminate\Http\Response
     */
    public function show(User $siswa)
{
    return view('pages.user.show', [
        'title' => $this->buildTitle('detail'),
        'user' => $siswa
    ]);
}

public function edit(User $siswa)
{
    return view('pages.user.edit', [
        'title' => $this->buildTitle('edit'),
        'user' => $siswa
    ]);
}

public function update(UpdateUserRequest $request, User $siswa)
{
    try {
        $data = $request->except('photo');
        $data['photo'] = $this->updateImage($request, $siswa->photo);
        $siswa->update($data);

        return back()->with('success', 'Berhasil mengedit siswa!');
    } catch (\Throwable $th) {
        return back()->with('error', $th->getMessage());
    }
}

public function destroy(User $siswa)
{
    try {
        $this->deleteImage($siswa->photo);
        $siswa->delete();

        return back()->with('success', 'Berhasil menghapus siswa!');
    } catch (\Throwable $th) {
        return back()->with('error', $th->getMessage());
    }
}


    public function print(Request $request)
    {
        $data = User::all();
        $manager = User::where('role', 'manager')->first();
        $filter = null;
        $filename = Carbon::now()->isoFormat('DD-MM-Y') . '_-_laporan_data_siswa_' . time() . '.pdf';

        $pdf = PDF::loadView('pages.user.print', [
            'title' => 'Laporan Data Siswa',
            'user' => auth()->user(),
            'filter' => $filter ?? '-',
            'date' => Carbon::now()->isoFormat('dddd, D MMMM Y'),
            'manager' => $manager,
            'data' => $data
        ]);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download($filename);
    }

    /**
     * API: Get all users (untuk Mobile App - ambil dari users table)
     */
    public function apiIndex()
    {
        try {
            $users = User::where('role', '!=', 'manager')
                ->select(
                    'id',
                    'name',
                    'username',
                    'password',
                    'gender',
                    'birth',
                    'address',
                    'phone',
                    'last_education',
                    'photo',
                    'role',
                    'joined_at',
                    'created_at',
                    'updated_at'
                )->get()
                ->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'username' => $user->username,
                        'password' => $user->password,
                        'gender' => $user->gender,
                        'birth' => $user->birth,
                        'address' => $user->address,
                        'phone' => $user->phone,
                        'last_education' => $user->last_education,
                        'profession' => null,
                        'status' => 'active',
                        'photo' => $user->photo,
                        'role' => $user->role,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diambil',
                'data' => $users
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
     * API: Get single user detail
     */
    public function apiShow($id)
    {
        try {
            $user = User::findOrFail($id);

            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'password' => $user->password,
                'gender' => $user->gender,
                'birth' => $user->birth,
                'address' => $user->address,
                'phone' => $user->phone,
                'last_education' => $user->last_education,
                'profession' => null,
                'status' => 'active',
                'photo' => $user->photo,
                'role' => $user->role,
                'joined_at' => $user->joined_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at
            ];

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diambil',
                'data' => $userData
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan',
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
     * API: Create new user
     */
    public function apiStore(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'username' => 'required|string|unique:users,username',
                'password' => 'required|string|min:6',
                'gender' => 'nullable|in:L,P',
                'phone' => 'nullable|string',
                'address' => 'nullable|string',
                'birth' => 'nullable|date',
                'last_education' => 'nullable|string',
            ]);

            $data['password'] = bcrypt($data['password']);
            $data['role'] = 'collector';

            $user = User::create($data);

            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'password' => $user->password,
                'gender' => $user->gender,
                'birth' => $user->birth,
                'address' => $user->address,
                'phone' => $user->phone,
                'last_education' => $user->last_education,
                'profession' => null,
                'status' => 'active',
                'photo' => $user->photo,
                'role' => $user->role,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at
            ];

            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditambahkan',
                'data' => $userData
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
     * API: Update user
     */
    public function apiUpdate(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $data = $request->validate([
                'name' => 'sometimes|string',
                'username' => 'sometimes|string|unique:users,username,' . $id,
                'gender' => 'nullable|in:L,P',
                'phone' => 'nullable|string',
                'address' => 'nullable|string',
                'birth' => 'nullable|date',
                'last_education' => 'nullable|string',
            ]);

            $user->update($data);

            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'password' => $user->password,
                'gender' => $user->gender,
                'birth' => $user->birth,
                'address' => $user->address,
                'phone' => $user->phone,
                'last_education' => $user->last_education,
                'profession' => null,
                'status' => 'active',
                'photo' => $user->photo,
                'role' => $user->role,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at
            ];

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diperbarui',
                'data' => $userData
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan',
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
     * API: Delete user
     */
    public function apiDestroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus',
                'data' => null
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan',
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
