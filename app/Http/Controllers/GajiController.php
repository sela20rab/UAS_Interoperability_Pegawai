<?php

namespace App\Http\Controllers;

use App\Models\Gaji;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class GajiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

     public function index(Request $request){
        if(Gate::denies('read-gaji')){
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized' 
            ], 403);
        }

        $acceptHeader = $request->header('Accept');
        $authorization = $request->header('Authorization');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml'){
            $gaji = Gaji::OrderBy("id", "DESC")->paginate(2)->toArray();
            $response = [
                "total_count" => $gaji["total"],
                "limit" => $gaji["per_page"],
                "pagination" => [
                    "next_page" => $gaji["next_page_url"],
                    "current_page" => $gaji["current_page"]
                ],
                "data" => $gaji["data"],
            ];

            if ($acceptHeader === 'application/json') {
                return response()->json($response, 200);
            } else {
                $xml = new \SimpleXMLElement('<gaji/>');
                foreach ($gaji->items('data') as $item) {
                    $xmlItem = $xml->addChild('gaji');

                    $xmlItem->addChild('id', $item->id);
                    $xmlItem->addChild('gaji_pokok', $item->gaji_pokok);
                    $xmlItem->addChild('tunjangan', $item->tunjangan);
                    
                }
                return $xml->asXML();
            }

            $outPut = [
                "message" => "Gaji",
                "result" => $gaji
            ];

            
        } else {
            return response('Not Acceptable!', 406);
        }
    }

    public function getall(Request $request){
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml'){
            $gaji = Gaji::OrderBy("id", "DESC")->paginate(2)->toArray();
            $response = [
                "total_count" => $gaji["total"],
                "limit" => $gaji["per_page"],
                "pagination" => [
                    "next_page" => $gaji["next_page_url"],
                    "current_page" => $gaji["current_page"]
                ],
                "data" => $gaji["data"],
            ];

            if ($acceptHeader === 'application/json') {
                return response()->json($response, 200);
            } else {
                $xml = new \SimpleXMLElement('<gajii/>');
                foreach ($gaji->items('data') as $item) {
                    $xmlItem = $xml->addChild('gaji');

                    $xmlItem->addChild('id', $item->id);
                    
                    $xmlItem->addChild('gaji_pokok', $item->gaji_pokok);
                    $xmlItem->addChild('tunjangan', $item->tunjangan);
                }
                return $xml->asXML();
            }

            $outPut = [
                "message" => "Gaji",
                "result" => $gaji
            ];


        } else {
            return response('Not Acceptable!', 406);
        }
    }

    public function store(Request $request){
        $acceptHeader = $request->header('Accept');
        // $contentTypeHeader = $request->header('Content-Type');

        if ($acceptHeader === 'application/json') {
            $input = $request->all();
            $validationRules = [
                'gaji_pokok' => 'required',
                'tunjangan' => 'required'
            ];

            $validator = Validator::make($input, $validationRules);

            if ($validator->fails()){
                return response()->json($validator->errors(),400);
            }

            $gaji = Gaji::create($input);
            return response()->json($gaji, 200);

        }else{
            return response('Unsupported Media Type', 415);
        }
    }

    public function show($id, Request $request){
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml'){
            $gaji = Gaji::with([
                'user' => function($query){
                    $query->select('id','name');
                },
                'pegawai' => function($query){
                    $query->select('id','nama_pegawai');
                }
            ])->where('id',$id)->get();

            if ($acceptHeader === 'application/json') {
                return response()->json($gaji, 200);
            } else {
                $xml = new \SimpleXMLElement('<gaji/>');
                foreach ($gaji as $item) {
                    $xmlItem = $xml->addChild('gaji');

                    $xmlItem->addChild('id', $item->id);
                   
                    $xmlItem->addChild('gaji_pokok', $item->gaji_pokok);
                    $xmlItem->addChild('tunjangan', $item->tunjangan);
                }
                return $xml->asXML();
            }

            if(!$gaji) {
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
            $gaji = Gaji::find($id);

            if(!$gaji) {
                abort(404);
            }

            //validation
            $validationRules = [
                
                'gaji_pokok' => 'required',
                'tunjangan' => 'required'
            ];

            $validator = Validator::make($input, $validationRules);

            if ($validator->fails()){
                return response()->json($validator->errors(),400);
            }

            $gaji->fill($input);
            $gaji->save();

            return response()->json($gaji, 200);
        }
        else{
            return response('Unsupported Media Type', 415);
        }
    }

    public function destroy($id, Request $request){
        $acceptHeader = $request->header('Accept');
        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml'){
            $gaji = Gaji::find($id);

            if(!$gaji) {
                abort(404);
            }

            $gaji->delete();
            $message = ['message' => 'deleted successfully', 'gaji_id' => $id];
            return response()->json($message, 200);

        } else {
            return response('Not Acceptable!', 406);
        }
    }
}
