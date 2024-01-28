<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class JabatanController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function index(Request $request){
        if(Gate::denies('read-jabatan')){
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized'
            ], 403);
        }

        $acceptHeader = $request->header('Accept');
        $authorization = $request->header('Authorization');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml'){
            $jabatan = Jabatan::OrderBy("id", "DESC")->paginate(2)->toArray();
            $response = [
                "total_count" => $jabatan["total"],
                "limit" => $jabatan["per_page"],
                "pagination" => [
                    "next_page" => $jabatan["next_page_url"],
                    "current_page" => $jabatan["current_page"]
                ],
                "data" => $jabatan["data"],
            ];

            if ($acceptHeader === 'application/json') {
                return response()->json($response, 200);
            } else {
                $xml = new \SimpleXMLElement('<jabatan/>');
                foreach ($jabatan->items('data') as $item) {
                    $xmlItem = $xml->addChild('jabatan');

                    $xmlItem->addChild('id', $item->id);
                    $xmlItem->addChild('jabatan', $item->jabatan);

                }
                return $xml->asXML();
            }

            $outPut = [
                "message" => "Jabatan",
                "result" => $jabatan
            ];


        } else {
            return response('Not Acceptable!', 406);
        }
    }

    public function getall(Request $request){
        if(Gate::denies('read-jabatan')){
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized'
            ], 403);
        }

        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml'){
            $jabatan = Jabatan::OrderBy("id", "DESC")->paginate(2)->toArray();
            $response = [
                "total_count" => $jabatan["total"],
                "limit" => $jabatan["per_page"],
                "pagination" => [
                    "next_page" => $jabatan["next_page_url"],
                    "current_page" => $jabatan["current_page"]
                ],
                "data" => $jabatan["data"],
            ];

            if ($acceptHeader === 'application/json') {
                return response()->json($response, 200);
            } else {
                $xml = new \SimpleXMLElement('<jabatan/>');
                foreach ($jabatan->items('data') as $item) {
                    $xmlItem = $xml->addChild('jabatan');

                    $xmlItem->addChild('id', $item->id);
                    $xmlItem->addChild('jabatan', $item->jabatan);

                }
                return $xml->asXML();
            }

            $outPut = [
                "message" => "Jabatan",
                "result" => $jabatan
            ];


        } else {
            return response('Not Acceptable!', 406);
        }
    }

    public function store(Request $request){
        if(Gate::denies('create-jabatan')){
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized'
            ], 403);
        }
        $acceptHeader = $request->header('Accept');
        $contentTypeHeader = $request->header('Content-Type');

        if ($acceptHeader === 'application/json') {
            $input = $request->all();
            $validationRules = [
                'jabatan' => 'required',
            ];

            $validator = Validator::make($input, $validationRules);

            if ($validator->fails()){
                return response()->json($validator->errors(),400);
            }

            $jabatan = Jabatan::create($input);
            return response()->json($jabatan, 200);

        }else{
            return response('Unsupported Media Type', 415);
        }
    }

    public function show($id, Request $request){
        if(Gate::denies('read-jabatan')){
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized'
            ], 403);
        }
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml'){
            $jabatan = Jabatan::where('id',$id)->get();

            if ($acceptHeader === 'application/json') {
                return response()->json($jabatan, 200);
            } else {
                $xml = new \SimpleXMLElement('<jabatan/>');
                foreach ($jabatan as $item) {
                    $xmlItem = $xml->addChild('jabatan');

                    $xmlItem->addChild('id', $item->id);
                    $xmlItem->addChild('Jabatan', $item->jabatan);
                }
                return $xml->asXML();
            }

            if(!$jabatan) {
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
            $jabatan = Jabatan::find($id);

            if(!$jabatan) {
                abort(404);
            }

            if(Gate::denies('update-jabatan')){
                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'message' => 'You are unauthorized'
                ], 403);
            }

            //validation
            $validationRules = [
                'jabatan' => 'required'

            ];

            $validator = Validator::make($input, $validationRules);

            if ($validator->fails()){
                return response()->json($validator->errors(),400);
            }

            $jabatan->fill($input);
            $jabatan->save();

            return response()->json($jabatan, 200);
        }
        else{
            return response('Unsupported Media Type', 415);
        }
    }

    public function destroy($id, Request $request){
        $acceptHeader = $request->header('Accept');
        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml'){
            $jabatan = Jabatan::find($id);

            if(!$jabatan) {
                abort(404);
            }
            if(Gate::denies('destroy-jabatan')){
                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'message' => 'You are unauthorized'
                ], 403);
            }

            $jabatan->delete();
            $message = ['message' => 'deleted successfully', 'jabatan_id' => $id];
            return response()->json($message, 200);

        } else {
            return response('Not Acceptable!', 406);
        }
    }
}
