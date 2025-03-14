<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SPIn;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SpinAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $location_id = $request->id;
        return view('admin.merchant.SPIn.index')->with(['location_id' => $location_id]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = loginUser();
        // Validate the incoming request
        $validated = $request->validate([
            'tpn' => 'required|string|max:255',
            'auth_key' => 'required|string|max:255',
            'location_id' => 'required',
            'environment' => 'required|string|max:255',
        ]);
        $spin = new SPIn();
        $spin->tpn = $request->tpn;
        $spin->auth_key = $request->auth_key;
        $spin->location_id = $request->location_id ?? '';
        $spin->environment = $request->environment??'';

        $spin->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Record Added successfully.',
            'data' => $spin
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SPIn  $sPIn
     * @return \Illuminate\Http\Response
     */
    public function show(SPIn $sPIn)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SPIn  $sPIn
     * @return \Illuminate\Http\Response
     */
    public function edit(SPIn $sPIn)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SPIn  $sPIn
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'tpn' => 'required|string|max:255',
            'auth_key' => 'required|string|max:255',
            'environment' => 'required|string|max:255',
        ]);
        $spin = SPIn::findOrFail($request->id);
        $spin->tpn = $request->tpn;
        $spin->auth_key = $request->auth_key;
        $spin->environment = $request->environment;
        $spin->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Record updated successfully.',
            'data' => $spin
        ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SPIn  $sPIn
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $spin = SPIn::where('id', $id)->where('location_id', $request->location_id)->first();
        if (!$spin) {
            return response()->json(['code' => 400,  'message' => 'Record not found or location mismatch'], 404);
        }
        $spin->delete();
        return response()->json(['code' => 200, 'message' => 'Record deleted successfully']);
    }

    public function getTableData(Request $req)
    {
      
        $items = SPIn::where('location_id', $req->location_id);
        // Apply search filtering for specific columns
        if (!empty($req->search['value'])) {
            $searchValue = $req->search['value'];
            $items = $items->where(function ($query) use ($searchValue) {
                $query->where('tpn', 'like', "%{$searchValue}%")
                    ->orWhere('auth_key', 'like', "%{$searchValue}%");
            });
        }
        return DataTables::eloquent($items)
            ->editColumn('action', function ($spin) {
                return '
        <a href="javascript:void(0);" class="text-warning btn-edit" data-id="' . $spin->id . '"  data-environment="' . $spin->environment . '"data-tpn="' . $spin->tpn . '" data-auth-key="' . $spin->auth_key . '">
            <i class="fas fa-pencil-alt"></i>
        </a>
        <a href="javascript:void(0);" id="delete-spin" class="text-danger btn-delete" data-id="' . $spin->id . '" data-location-id="' . $spin->location_id . '" data-url="' . route('admin.merchant.spin.destroy', $spin->id) . '">
            <i class="fas fa-trash-alt"></i>
        </a>
        ';
            })
            ->setRowId(function ($item) {
                return "row_" . $item->id;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
