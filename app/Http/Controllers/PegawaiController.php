<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;


class PegawaiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */


    public function index(Request $request)
    {
        if(Gate::denies('read-pegawai')){
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized'
            ], 403);
        }

        $acceptHeader = $request->header('Accept');
            if ($acceptHeader === 'application/json') {
                $pegawai = Pegawai::with([
                    'jabatan' => function($query){
                        $query->select('id','jabatan');
                    }
                ])->OrderBy("id", "DESC")->paginate(10)->toArray();
                $response = [
                    "total_count" => $pegawai["total"],
                    "limit" => $pegawai["per_page"],
                    "pagination" => [
                        "next_page" => $pegawai["next_page_url"],
                        "current_page" => $pegawai["current_page"]
                    ],
                    "data" => $pegawai["data"],
                ];
                return response()->json($response, 200);
            } else {
                $pegawai = Pegawai::OrderBy("id", "DESC")->paginate(2);
                $xml = new \SimpleXMLElement('<pegawai/>');
                foreach ($pegawai->items('data') as $item) {
                    $xmlItem = $xml->addChild('pegawai');

                    $xmlItem->addChild('id', $item->id);
                    $xmlItem->addChild('nip', $item->nip);
                    $xmlItem->addChild('nama_pegawai', $item->nama_pegawai);
                    $xmlItem->addChild('alamat', $item->alamat);
                    $xmlItem->addChild('jenis_kelamin', $item->jenis_kelamin);
                    }
                return $xml->asXML();
            }


    }



    public function getall(Request $request){

    if(Gate::denies('read-pegawai')){
        return response()->json([
            'success' => false,
            'status' => 403,
            'message' => 'You are unauthorized'
        ], 403);
    }

    $acceptHeader = $request->header('Accept');

    if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml'){
        if ($acceptHeader === 'application/json') {
            $pegawai = Pegawai::OrderBy("id", "DESC")->paginate(2)->toArray();
            $response = [
                "total_count" => $pegawai["total"],
                "limit" => $pegawai["per_page"],
                "pagination" => [
                    "next_page" => $pegawai["next_page_url"],
                    "current_page" => $pegawai["current_page"]
                ],
                "data" => $pegawai["data"],
            ];
            return response()->json($response, 200);
        } else {
            $pegawai = Pegawai::OrderBy("id", "DESC")->paginate(2);
            $xml = new \SimpleXMLElement('<pegawai/>');
            foreach ($pegawai->items('data') as $item) {
                $xmlItem = $xml->addChild('pegawai');

                $xmlItem->addChild('id', $item->id);
                    $xmlItem->addChild('nip', $item->nip);
                    $xmlItem->addChild('nama_pegawai', $item->nama_pegawai);
                    $xmlItem->addChild('alamat', $item->alamat);
                    $xmlItem->addChild('jenis_kelamin', $item->jenis_kelamin);
            }
            return $xml->asXML();
        }

    } else {
        return response('Not Acceptable!', 406);
    }
}

public function store(Request $request){
    if(Gate::denies('create-pegawai')){
        return response()->json([
            'success' => false,
            'status' => 403,
            'message' => 'You are unauthorized'
        ], 403);
    }
    $acceptHeader = request()->header('Accept');
    $contentTypeHeader = request()->header('Content-Type');

    if ($acceptHeader === 'application/json') {
        $input = $request->all();
        $validationRules = [
            'nip' => 'required',
            'nama_pegawai' => 'required',
            'alamat' => 'required',
            'jenis_kelamin' => 'required',
            'jabatan_id' => 'required|exists:jabatan,id'
        ];


        $validator = Validator::make($input, $validationRules);

        if ($validator->fails()){
            return response()->json($validator->errors(),400);
        }

        $pegawai = Pegawai::create($input);
        return response()->json($pegawai, 200);

    }else{
        return response('Unsupported Media Type', 415);
    }
}

public function show($id, Request $request){
    $acceptHeader = $request->header('Accept');

    if(Gate::denies('read-pegawai')){
        return response()->json([
            'success' => false,
            'status' => 403,
            'message' => 'You are unauthorized'
        ], 403);
    }

    if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml'){
        $pegawai = Pegawai::with([
            'jabatan' => function($query){
                $query->select('id','nama_jabatan');
            }
        ])->where('id',$id)->get();

        if ($acceptHeader === 'application/json') {
            return response()->json($pegawai, 200);
        } else {
            $xml = new \SimpleXMLElement('<pegawai/>');
            foreach ($pegawai as $item) {
                $xmlItem = $xml->addChild('pegawai');

                $xmlItem->addChild('id', $item->id);
                    $xmlItem->addChild('nip', $item->nip);
                    $xmlItem->addChild('nama_pegawai', $item->nama_pegawai);
                    $xmlItem->addChild('alamat', $item->alamat);
                    $xmlItem->addChild('jenis_kelamin', $item->jenis_kelamin);
            }
            return $xml->asXML();
        }

        if(!$pegawai) {
            abort(404);
        }

    } else {
        return response('Not Acceptable!', 406);
    }
}

public function update(Request $request, $id){
    $acceptHeader = $request->header('Accept');
    $contentTypeHeader = $request->header('Content-Type');

    if ($acceptHeader === 'application/json') {
        $input = $request->all();
        $pegawai = Pegawai::find($id);

        if(!$pegawai) {
            abort(404);
        }

        if(Gate::denies('update-pegawai')){
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized'
            ], 403);
        }

        //validation
        $validationRules = [
            'nip' => 'required',
            'nama_pegawai' => 'required',
            'alamat' => 'required',
            'jenis_kelamin' => 'required',
            'jabatan_id' => 'required|exists:jabatan,id'
        ];

        $validator = Validator::make($input, $validationRules);

        if ($validator->fails()){
            return response()->json($validator->errors(),400);
        }

        $pegawai->fill($input);
        $pegawai->save();

        return response()->json($pegawai, 200);
    }
    else{
        return response('Unsupported Media Type', 415);
    }
}

public function destroy($id, Request $request){
    $acceptHeader = $request->header('Accept');
    if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml'){
        $Pegawai = Pegawai::find($id);
        if(Gate::denies('destroy-pegawai')){
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized'
            ], 403);
        }
        if(!$Pegawai) {
            abort(404);
        }

        $Pegawai->delete();
        $message = ['message' => 'deleted successfully', 'pegawai_id' => $id];
        return response()->json($message, 200);

    } else {
        return response('Not Acceptable!', 406);
    }
}

}
