<?php

namespace App\Http\Controllers;

use App\Jobs\RegisterPaymentProvider;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MerchantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.merchant.index');
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function getTableData(Request $req)
    {
        $items = User::whereNotNull('location_id');

        // Apply search filtering for specific columns
        if (!empty($req->search['value'])) {
            $searchValue = $req->search['value'];
            $items = $items->where(function ($query) use ($searchValue) {
                $query->where('location_id', 'like', "%{$searchValue}%")
                    ->orWhere('name', 'like', "%{$searchValue}%");
            });
        }
        return DataTables::eloquent($items)
            ->editColumn('action', function ($item) {
                $keysToFetch = ['hpp_tpn', 'hpp_auth_token', 'environment'];
                $settings = $item->getSpecificSettings($keysToFetch);
                $encodedSettings = base64_encode(json_encode($settings));
                return '<a href="javascript:void(0);" class="text-warning btn-add-hpp" data-setting="' . $encodedSettings . '" data-id="' . $item->id . '" data-location-id="' . $item->location_id . '" data-location-name="' . $item->name . '">
                            <i class="fas fa-cog fa-2x"></i>
                        </a>
                        <a href="' . route('admin.merchant.spin.index', ['id' => $item->location_id]) . '" class="text-warning btn-spin-list" data-id="' . $item->location_id . '" data-location-name="' . $item->name . '">
                            <i class="fas fa-spinner fa-2x"></i>
                        </a>
                        <a href="javascript:void(0);" class="text-warning btn-setup-payment-provider" data-id="' . $item->location_id . '" data-location-name="' . $item->name . '">
                            <i class="fas fa fa-credit-card fa-2x"></i>
                        </a>';
            })
            ->setRowId(function ($item) {
                return "row_" . $item->id;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function setupHpp(Request $request)
    {
        $user_id = decrypt_cipher($request->user_id);
        $message = 'Something Went Wrong';
        if($user_id)
        {
            foreach ($request->setting ?? [] as $key => $value) {
                save_settings($key, $value, $user_id);
            }
            $message = 'Data saved successfully';
        }
        return response()->json(['success' => true, 'message' => $message]);
    }
    public function setupCRMProvider(Request $request)
    {
        $user = User::where('location_id',$request->location_id)->first();
        RegisterPaymentProvider::dispatch($user)->onQueue(config('app.job_queue'));
        return response()->json([
            'message' => 'Request successfully!'
        ], 200);
    }
}
