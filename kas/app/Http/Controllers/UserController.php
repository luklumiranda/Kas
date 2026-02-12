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
}
