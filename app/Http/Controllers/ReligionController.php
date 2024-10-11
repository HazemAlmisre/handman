<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\ReligionRequest;
use App\Models\Religion;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;


class ReligionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = [
            'status' => $request->status,
        ];
        $pageTitle = trans('Religion');
        $auth_user = authSession();
        $assets = ['datatable'];
        return view('religion.index', compact('pageTitle','auth_user','assets','filter'));
    }


    public function index_data(DataTables $datatable,Request $request)
    {
        $query = Religion::query()->list();
        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('name', $filter['column_status']);
            }
        }
        if (auth()->user()->hasAnyRole(['admin'])) {
            $query->newquery();
        }
        
        return $datatable->eloquent($query)
        ->addColumn('check', function ($row) {
            return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$row->id.'"  name="datatable_ids[]" value="'.$row->id.'" onclick="dataTableRowCheck('.$row->id.')">';
        })
     
        // ->editColumn('title', function($query){                
        //     if (auth()->user()->can('tax edit')) {

        //         $link = '<a class="btn-link btn-link-hover" href='.route('tax.create', ['id' => $query->id]).'>'.$query->title.'</a>';
        //     } else {
        //         $link = $query->title; 
        //     }
        //     return $link;
        // })



        // ->editColumn('status' , function ($query){
        //     return '<div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
        //         <div class="custom-switch-inner">
        //             <input type="checkbox" class="custom-control-input  change_status" data-type="tax_status" '.($query->status ? "checked" : "").'  value="'.$query->id.'" id="'.$query->id.'" data-id="'.$query->id.'">
        //             <label class="custom-control-label" for="'.$query->id.'" data-on-label="" data-off-label=""></label>
        //         </div>
        //     </div>';
        // })
        // ->editColumn('value' , function ($query){
        //     $value = getPriceFormat($query->value);
        //     if($query->type === 'percent'){
        //         $value = $query->value. '%';
        //     }
        //     return $value;
        // })
        ->addColumn('action', function($religion){
            return view('religion.action',compact('religion'))->render();
        })
        ->addIndexColumn()
        ->rawColumns(['check','title','action','status'])
            ->toJson();
    }

    /* bulck action method */
    public function bulk_action(Request $request)
    {
        // $ids = explode(',', $request->rowIds);

        // $actionType = $request->action_type;

        // $message = 'Bulk Action Updated';

       
            

        // return response()->json(['status' => true, 'message' => $message]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $pageTitle1 = trans('messages.setting');
        $page = 'religion';
        $id = $request->id;
        $auth_user = authSession();

        $religiondata = Religion::find($id);
        $pageTitle = trans('messages.update_form_title',['form'=>trans('Religion')]);
        
        if($religiondata == null){
            $pageTitle = trans('messages.add_button_form',['form' => trans('Religion')]);
            $religiondata = new Religion;
        }
        
        return view('religion.create', compact('pageTitle' ,'religiondata' ,'auth_user','pageTitle1','page' ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReligionRequest $request)
    {
        if(demoUserPermission()){
            return  redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }
     
        $data = $request->all();
        $page = 'religion';
      
        $result = Religion::updateOrCreate(['id' => $data['id'] ],$data);
 
    
        $message = trans('messages.update_form',['form' => trans('Religion')]);
        if($result->wasRecentlyCreated){
            $message = trans('messages.save_form',['form' => trans('Religion')]);
        }

        if($request->is('api/*')) {
            return comman_message_response($message);
		}

        return redirect(route('religion.index'))->withSuccess($message);        
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
        if(demoUserPermission()){
            return  redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }
        $religion = Religion::find($id);
        $msg= __('messages.msg_fail_to_delete',['item' => __('Religion')] );
        
        if($religion != '') { 
            $religion->delete();
            $msg= __('messages.msg_deleted',['name' => __('Religion')] );
        }
        return comman_custom_response(['message'=> $msg, 'status' => true]);
    }
}
