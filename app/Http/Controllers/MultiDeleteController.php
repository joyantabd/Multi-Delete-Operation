<?php

namespace App\Http\Controllers;

use App\MultiDelete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MultiDeleteController extends Controller
{
    public function index(Request $request)
    {

        if(request()->ajax())
        {
            return datatables()->of(MultiDelete::latest()->get())
                ->addColumn('action', function($data){
                    $button = '<button type="button" name="edit" id="'.$data->id.'" class="edit btn btn-primary btn-sm" title="Edit Data"><i class="fa fa-edit"></i></button>';
                    $button .= '&nbsp;&nbsp;';
                    $button .= '<button type="button" name="delete" id="'.$data->id.'" class="delete btn btn-danger btn-sm" title="Delete Data"><i class="fa fa-trash"></i></button>';
                    return $button;
                })
                ->addColumn('checkbox', '<input type="checkbox" name="multidelete_checkbox[]"  class="multidelete_checkbox" value="{{$id}}" />')
                ->rawColumns(['checkbox','action'])
                ->make(true);
        }
        return view('multidelete.index');
    }



    public function create()
    {
        //
    }


    public function store(Request $request)
    {

        $rules = array(
            'name'    =>  'required',
            'address'    =>  'required',
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }


        $form_data = array(
            'name'        =>  $request->name,
            'address'             => $request->address,

        );

        MultiDelete::create($form_data);

        return response()->json(['success' => 'Successfully Created']);
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        if(request()->ajax())
        {
            $data = MultiDelete::find($id);
            return response()->json(['data' => $data]);
        }
    }

    public function update(Request $request)
    {

        $rules = array(
            'name'    =>  'required',
            'address'    =>  'required',

        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }


        $form_data = array(
            'name'        =>  $request->name,
            'address'        =>  $request->address,
        );



        MultiDelete::whereId($request->hidden_id)->update($form_data);
        return response()->json(['success' => 'Successful']);

    }


    public function destroy($id)
    {
        $data = MultiDelete::findOrFail($id);
        $data->delete();
    }

    public function mass(Request $request)
    {
        $joy_id_array = $request->input('id');
        $joy = MultiDelete::whereIn('id', $joy_id_array);
        if($joy->delete())
        {
            return 'Data Deleted !!!!!';
        }
    }
}
