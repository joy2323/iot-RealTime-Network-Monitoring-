<?php

namespace App\Http\Controllers;

use App\LoginActivities;
use App\User;
use Auth;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Response;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $masterUser = Auth::user();
        $admin = array();
        $index = 0;
        $users = User::where('users.dashboard_id', $masterUser->dashboard_id)
            ->select([
                'users.id', 'users.master_role', 'users.name', 'users.ctrl_auth', 'users.email', 'users.activate',
            ])->get();

        foreach ($users as $key => $value) {
            # code...
            $email = $value->email;
            $key = "susejgroup.net";
            if (Str::contains($email, $key) == false) {
                $loginActivities = LoginActivities::where("email", $email)->orderBy('id', 'DESC')->first();
                if ($loginActivities) {
                    $admin[$index] = $value;
                    $admin[$index]->updated_at = $loginActivities->updated_at;
                    $admin[$index]->login_count = $loginActivities->login_count;
                    $index++;
                }
            }

        }

        if (request()->ajax()) {
            return DataTables::of($admin)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    if ($row->master_role == 1) {
                        $btn = '<a class="edit" data-toggle="modal"  data-target="#modal-edit-admin" data-id="' . $row->id . '" data-placement="top" title="Edit" ><i class="fa fa-edit text-info"></i></a>
                        &nbsp; &nbsp;
                        <a class="" data-toggle="modal" disabled style="cursor: not-allowed;"  data-name="' . $row->email . '"  data-id="' . $row->id . '" data-original-title="delete"><span class="fa fa-trash  text-danger" disabled ></span></a>';

                    } else {
                        $btn = '<a class="edit" data-toggle="modal" data-target="#modal-edit-admin" data-id="' . $row->id . '" data-placement="top" title="Edit" ><i class="fa fa-edit text-info"></i></a>
                        &nbsp; &nbsp;
                        <a class="delete" data-toggle="modal"  data-name="' . $row->email . '" data-target="#modal-delete-api" data-id="' . $row->id . '" data-original-title="delete"><span class="fa fa-trash  text-danger"></span></a>';

                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admins');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $email = $request->email;

        $checkEmail = User::where("email", $email)->first();
        if ($checkEmail) {
            return response()->json(['status' => false, 'message' => "Email Already Exist."]);
            //
        }
        $masterUser = Auth::user();
        $masterOwner = $masterUser->owner_id;
        $masterRole = $masterUser->role;
        $masterAddress = $masterUser->address;
        $masterNumber = $masterUser->phone_number;
        $masterImg = $masterUser->image;
        $masterdash = $masterUser->dashboard_id;

        $createAdmin = User::create([
            'name' => $masterUser->name,
            'email' => $email,
            'role' => $masterRole,
            'phone_number' => $masterNumber,
            'address' => $masterAddress,
            'image' => $masterImg,
            'password' => Hash::make($request->password),
            'owner_id' => $masterUser->id,
            'dashboard_id' => $masterdash,
            'dash_label1' => $masterUser->dash_label1,
            'dash_label2' => $masterUser->dash_label2,
            'owner_id' => $masterOwner,
            'ctrl_auth' => $request->ctrl,
            'activate' => $request->activate,
            'master_role' => 0,
        ]);

        $createAdmin->save();
        return response()->json(['status' => true, 'message' => "Account created successfully."]);
        //
    }

    public function deleteAdmin(Request $request)
    {
        $email = User::where("id", $request->id)->value("email");
        $admin = User::findOrfail($request->id);
        $admin->delete();

        $logAct = LoginActivities::where("email", $email);
        $logAct->delete();
        return response()->json(['status' => true, 'message' => "Admin deleted successfully."]);
    }
    public function editAdmin(Request $request)
    {
        $editAdmin = User::findOrfail($request->id);

        $editAdmin->email = $request->emailadd;
        $editAdmin->ctrl_auth = $request->ctrl_auth;
        $editAdmin->activate = $request->activate_edit;
        if ($request->passwrod) {
            $editAdmin->paswword = Hash::make($request->password);
        }
        $editAdmin->save();

        if ($editAdmin) {
            return response()->json([
                "status" => 200,
                "message" => "Admin info updated successfully",
                "id" => $request->id,
            ]);
        } else {
            return response()->json([
                "status" => 500,
                "message" => "Failed to update Admin info",
            ]);
        }
    }
    public function getAdmin($id)
    {
        $admin = User::where('users.id', $id)
            ->select([
                'users.id', 'users.name', 'users.email', 'users.ctrl_auth', 'activate'])->first();
        return response()->json($admin, 200);

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
}