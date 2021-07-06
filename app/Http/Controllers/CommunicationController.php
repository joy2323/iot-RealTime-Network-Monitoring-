<?php

namespace App\Http\Controllers;

use App\Communication;
use App\User;
use Auth;
use DataTables;
use Illuminate\Http\Request;
use App\Dashboards;
use Illuminate\Support\Str;

class CommunicationController extends Controller
{
    //
    public function viewCommuncation()
    {
        if (Auth::user()->role == 'Super admin') {
            $getClientCommunication = Communication::join('users', 'users.id', '=', 'communications.user_id')
                ->select([
                    'users.name AS name', 'communications.*',
                ])
                ->get();
            if (request()->ajax()) {
                return DataTables::of($getClientCommunication)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $btn = '&nbsp; &nbsp;&nbsp; &nbsp;<a class="edit" data-toggle="modal" data-target="#modal-edit-communication" data-id="' . $row->id . '" data-placement="top" title="Edit Communication" ><i class="fa fa-edit text-success"></i></a>
                        &nbsp; &nbsp;<a class="delete" data-name="' . $row->name . '" data-toggle="modal" data-target="#modal-delete-api" data-id="' . $row->id . '" data-placement="top" title="Delete Communication" ><i class="fa fa-trash text-danger"></i></a>';
                        return $btn;

                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('super_admin.communication');
        } else if (Auth::user()->role == 'Client admin') {
            $masterid= Dashboards::where("id",Auth::user()->dashboard_id)->value("master_id");
        
            $getClientCommunication = Communication::where('communications.user_id', $masterid)->orWhere('communications.owner_id', $masterid)
                ->join('users', 'users.id', '=', 'communications.user_id')
                ->where('users.role', '=', 'BU admin')
                //->where('users.role', '=', 'Client admin')
                ->select([
                    'users.name AS name', 'communications.*',
                ])
                ->get();
               
            if (request()->ajax()) {
                return DataTables::of($getClientCommunication)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $btn = '&nbsp; &nbsp;&nbsp; &nbsp;<a class="edit" data-toggle="modal" data-target="#modal-edit-communication" data-id="' . $row->id . '" data-placement="top" title="Edit Communication" ><i class="fa fa-edit text-success"></i></a>
                        &nbsp; &nbsp;<a class="delete" data-name="' . $row->name . '" data-toggle="modal" data-target="#modal-delete-api" data-id="' . $row->id . '" data-placement="top" title="Delete Communication" ><i class="fa fa-trash text-danger"></i></a>';
                        return $btn;

                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }

            return view('client_admin.communication');
        } else if (Auth::user()->role == 'BU admin') {
            $masterid= Dashboards::where("id",Auth::user()->dashboard_id)->value("master_id");
            $getCommunication = Communication::where('user_id', $masterid)->orWhere('communications.owner_id', $masterid)
                ->join('users', 'users.id', '=', 'communications.user_id')
                ->select([
                    'users.name AS name','users.master_role', 'communications.*',
                ])
                ->get();
            if (request()->ajax()) {
                return DataTables::of($getCommunication)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        if ($row->role == 'BU admin' && $row->master_role == 1) {
                            $btn = '&nbsp; &nbsp;&nbsp; &nbsp;<a class="" disabled style="cursor: not-allowed;"   data-placement="top" title="Edit Comm" ><i class="fa fa-edit text-success"></i></a>&nbsp; &nbsp;
                            <a class=""  data-placement="top"   disabled style="cursor: not-allowed;"  title="Edit Comm" ><i class="fa fa-trash text-danger"></i></a>';
                            return $btn;
                           
                        } else {
                            $btn = '&nbsp; &nbsp;&nbsp; &nbsp;<a class="edit"  data-toggle="modal" data-target="#modal-edit-communication" data-id="' . $row->id . '" data-placement="top" title="Edit Comm" ><i class="fa fa-edit text-success"></i></a>
                    &nbsp; &nbsp;<a class="delete" data-name="' . $row->name . '" data-toggle="modal" data-target="#modal-delete-api" data-id="' . $row->id . '" data-placement="top" title="Delete Communication" ><i class="fa fa-trash text-danger"></i></a>';
                            return $btn;
                        }
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }

            return view('bu_admin.communication');
        } else if (Auth::user()->role == 'UT admin') {
            $masterid= Dashboards::where("id",Auth::user()->dashboard_id)->value("master_id");
            $getCommunication = Communication::where('user_id',  $masterid)->orWhere('communications.owner_id',  $masterid)
                ->join('users', 'users.id', '=', 'communications.user_id')
                ->select([
                    'users.name AS name','users.master_role', 'communications.*',
                ])
                ->get();
            if (request()->ajax()) {
                return DataTables::of($getCommunication)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        if ($row->role == 'UT admin' && $row->master_role ==1) {
                            $btn = '&nbsp; &nbsp;&nbsp; &nbsp;<a class="" disabled style="cursor: not-allowed;"  data-placement="top" title="Edit Comm" ><i class="fa fa-edit text-warning"></i></a>&nbsp; &nbsp;
                            <a class=""  data-placement="top" disabled style="cursor: not-allowed;"  title="Edit Comm" ><i class="fa fa-trash text-danger"></i></a>';
                            return $btn;
                        
                        } else {
                            $btn = '&nbsp; &nbsp;&nbsp; &nbsp;<a class="edit" data-toggle="modal" data-target="#modal-edit-communication" data-id="' . $row->id . '" data-placement="top" title="Edit Comm" ><i class="fa fa-edit text-success"></i></a>
                            &nbsp; &nbsp;<a class="delete" data-name="' . $row->name . '" data-toggle="modal" data-target="#modal-delete-api" data-id="' . $row->id . '" data-placement="top" title="Delete Communication" ><i class="fa fa-trash text-danger"></i></a>';
                            return $btn;

                        }
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }

            return view('ut_admin.communication');

        }
    }

    public function addCommunication()
    {
        $masterid= Dashboards::where("id",Auth::user()->dashboard_id)->value("master_id");
        if (Auth::user()->role == 'Super admin') {
            $getClient = User::where('role', 'Client admin')->get();
            return view('super_admin.create-communication', compact('getClient'));
        } else if (Auth::user()->role == 'Client admin') {
            $getBUOwners = User::where("owner_id",  $masterid)->where('role', 'BU admin')->get();
            return view('client_admin.create-communication', compact('getBUOwners'));
        } else if (Auth::user()->role == 'BU admin') {
            $getUT = User::where("owner_id",  $masterid)->where('role', 'UT admin')->get();
            return view('bu_admin.create-communication', compact('getUT'));
        } else if (Auth::user()->role == 'UT admin') {
            $getUser = User::where("owner_id",  $masterid)->where('role', 'Site admin')->get();
            return view('ut_admin.create-communication', compact('getUser'));
        } else if (Auth::user()->role == 'INJ admin') {
            $getUser = User::where("owner_id",  $masterid)->where('role', 'Site admin')->get();
            return view('inj_admin.create-communication', compact('getUser'));
        }

    }

    public function viewINJCommuncation()
    {
        $masterid= Dashboards::where("id",Auth::user()->dashboard_id)->value("master_id");
        if (Auth::user()->role == 'Super admin') {
            $getClientCommunication = Communication::where('communications.role', 'INJ admin')->join('users', 'users.id', '=', 'communications.user_id')
                ->where('users.role', '=', 'INJ admin')
                ->select([
                    'users.name AS name', 'communications.*',
                ])
                ->get();
            if (request()->ajax()) {
                return DataTables::of($getClientCommunication)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $btn = '&nbsp; &nbsp;&nbsp; &nbsp;<a class="edit" data-toggle="modal" data-target="#modal-edit-communication" data-id="' . $row->id . '" data-placement="top" title="Edit Communication" ><i class="fa fa-edit text-success"></i></a>
                &nbsp; &nbsp;<a class="delete" data-toggle="modal" data-id="' . $row->id . '" data-placement="top" title="Delete Communication" ><i class="fa fa-trash text-danger"></i></a>';
                        return $btn;

                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }

            return view('inj_admin.communication');
        } else if (Auth::user()->role == 'Client admin') {
            $getClientCommunication = Communication::where('communications.role', 'INJ admin')->where('communications.user_id',  $masterid)->orWhere('communications.owner_id',  $masterid)
                ->join('users', 'users.id', '=', 'communications.user_id')
                ->where('users.role', '=', 'INJ admin')
                ->select([
                    'users.name AS name', 'communications.*',
                ])
                ->get();
            if (request()->ajax()) {
                return DataTables::of($getClientCommunication)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $btn = '&nbsp; &nbsp;&nbsp; &nbsp;<a class="edit" data-toggle="modal" data-target="#modal-edit-communication" data-id="' . $row->id . '" data-placement="top" title="Edit Communication" ><i class="fa fa-edit text-success"></i></a>
                    &nbsp; &nbsp; <a class="delete" data-toggle="modal" data-id="' . $row->id . '" data-placement="top" title="Delete Communication" ><i class="fa fa-trash text-danger"></i></a>
                    ';
                        return $btn;

                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('inj_admin.communication');
        } else if (Auth::user()->role == 'INJ admin') {
            $getClientCommunication = Communication::where('communications.role', 'INJ admin')->where('communications.user_id',  $masterid)->orWhere('communications.owner_id',  $masterid)
                ->join('users', 'users.id', '=', 'communications.user_id')
                ->select([
                    'users.name AS name', 'communications.*',
                ])
                ->get();
            if (request()->ajax()) {
                return DataTables::of($getClientCommunication)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $btn = '&nbsp; &nbsp;&nbsp; &nbsp;<a class="edit" data-toggle="modal" data-target="#modal-edit-communication" data-id="' . $row->id . '" data-placement="top" title="Edit Communication" ><i class="fa fa-edit text-success"></i></a>
                &nbsp; &nbsp; <a class="delete" data-toggle="modal" data-id="' . $row->id . '" data-placement="top" title="Delete Communication" ><i class="fa fa-trash text-danger"></i></a>
                ';
                        return $btn;

                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }

            return view('inj_admin.communication');
        }
    }

    public function addINJCommunication()
    {
        $masterid= Dashboards::where("id",Auth::user()->dashboard_id)->value("master_id");
        if (Auth::user()->role == 'Super admin') {
            $getOwners = User::where('role', 'INJ admin')->get();
        } else if (Auth::user()->role == 'Client admin') {
            $getOwners = User::where("owner_id",  $masterid)->where('role', 'INJ admin')->get();
        }
        return view('inj_admin.create-communication', compact('getOwners'));

    }
    public function storeCommunication(Request $request)
    {
        $masterid= Dashboards::where("id",Auth::user()->dashboard_id)->value("master_id");
        $number = explode('-', $request->phone_number);
        $phone_number = $number[1];
        $timeresponse = '0';
        $communication = Communication::where('user_id',  $masterid)
            ->where('email_address', $request->email)
            ->where('sms_mobile_number', $phone_number)->get()->count() > 0;
        if (Str::contains($request->respondent, 'First')) {
            $timeresponse = '0';
        } else if (Str::contains($request->respondent, 'Second')) {
            $timeresponse = '3';
        } else if (Str::contains($request->respondent, 'Third')) {
            $timeresponse = '6';
        } else if (Str::contains($request->respondent, 'Fourth')) {
            $timeresponse = '8';
        } else if (Str::contains($request->respondent, 'Fifth')) {
            $timeresponse = '12';
        }
        $owner =$masterid;
        $role = User::where("id", $request->userid)->value('role');
        $number = explode('-', $request->phone_number);
        $phone_number = $number[1];
        if (!$communication) {
            $communication = Communication::create([
                'sms_user_type' => $request->respondent,
                'sms_mobile_number' => $phone_number,
                'sms_enable' => '0',
                'email_user_type' => $request->respondent,
                'email_address' => $request->email,
                'email_enable' => '1',
                'user_id' => $request->userid,
                'owner_id' => $owner,
                'role' => $role,
                'schedule_time' => $timeresponse,
            ]);
            $communication->save();
        } else {
            return redirect()->back()->with('status', 'Email already exist!');
        }
        return redirect()->back()->with('status', 'Operation Successfully!');

    }

    public function getCommunication($id)
    {
        $getCommunication = Communication::find($id);

        return response()->json($getCommunication, 200);
    }

    public function deleteCommunication($id)
    {
        $getCommunication = Communication::findorFail($id)->delete();

        return response()->json(['status' => true, 'message' => "Operation successful."]);
    }

    public function deleteCommunications(Request $request)
    {

        $idreq = $request->ids;
        $ids = explode(',', $idreq);
        foreach ($ids as $id) {
            # code...
            $this->deleteCommunication($id);
        }
        return response()->json(['status' => true, 'message' => "Operation successful."]);

    }

    public function editCommunication(Request $request)
    {
    
        $number = explode('-', $request->phone_number);
        $phone_number = $number[1];
        $editCommunication = Communication::findOrfail($request->id);
        $editCommunication->email_address = $request->email_address;
        $editCommunication->sms_mobile_number = $phone_number;
        $editCommunication->email_enable = $request->enable;
        if($request->enablesms !=null ||$request->enablesms!=""){
            $editCommunication->sms_enable = $request->enablesms;
        }
        $editCommunication->save();

        if ($editCommunication) {
            return response()->json([
                "status" => 200,
                "message" => "Communication Info updated successfully",
                "id" => $request->id,
            ]);
        } else {
            return response()->json([
                "status" => 500,
                "message" => "Failed to update Communication Info",
            ]);
        }
    }
}