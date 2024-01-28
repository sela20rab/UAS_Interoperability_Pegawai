<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profiles;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ProfilesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function index(Request $request){
        $acceptHeader = $request->header('Accept');
        $authorization = $request->header('Authorization');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml'){
            if($authorization){
                $profiles = Profiles::Where(['user_id' => Auth::user()->id])->OrderBy("id", "DESC")->get();
            }else{
                $profiles = Profiles::OrderBy("id", "DESC")->paginate(2)->toArray();
            }

            if ($acceptHeader === 'application/json') {
                return response()->json($profiles, 200);
            } else {
                $xml = new \SimpleXMLElement('<profiles/>');
                foreach ($profiles->items('data') as $item) {
                    $xmlItem = $xml->addChild('profiles');

                    $xmlItem->addChild('id', $item->id);
                    $xmlItem->addChild('user_id', $item->user_id);
                    $xmlItem->addChild('first_name', $item->first_name);
                    $xmlItem->addChild('last_name', $item->last_name);
                    $xmlItem->addChild('summary', $item->summary);
                    $xmlItem->addChild('image', $item->image);
                }
                return $xml->asXML();
            }

            $outPut = [
                "message" => "Profiles",
                "result" => $profiles
            ];

            
        } else {
            return response('Not Acceptable!', 406);
        }
    }

    public function getall(Request $request){
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml'){
            $profiles = Profiles::OrderBy("id", "DESC")->paginate(2)->toArray();
            $response = [
                "total_count" => $profiles["total"],
                "limit" => $profiles["per_page"],
                "pagination" => [
                    "next_page" => $profiles["next_page_url"],
                    "current_page" => $profiles["current_page"]
                ],
                "data" => $profiles["data"],
            ];

            if ($acceptHeader === 'application/json') {
                return response()->json($response, 200);
            } else {
                $xml = new \SimpleXMLElement('<profiles/>');
                foreach ($profiles->items('data') as $item) {
                    $xmlItem = $xml->addChild('profiles');

                    $xmlItem->addChild('id', $item->id);
                    $xmlItem->addChild('user_id', $item->user_id);
                    $xmlItem->addChild('first_name', $item->first_name);
                    $xmlItem->addChild('last_name', $item->last_name);
                    $xmlItem->addChild('summary', $item->summary);
                    $xmlItem->addChild('image', $item->image);
                }
                return $xml->asXML();
            }

            $outPut = [
                "message" => "Profiles",
                "result" => $profiles
            ];

            
        } else {
            return response('Not Acceptable!', 406);
        }
    }

    public function store(Request $request){
        $acceptHeader = $request->header('Accept');
        $contentTypeHeader = $request->header('Content-Type');

        if ($acceptHeader === 'application/json') {
            $input = $request->all();
            $validationRules = [
                'first_name' => 'required',
                'last_name' => 'required',
                'summary' => 'required'
            ];

            $validator = Validator::make($input, $validationRules);

            if ($validator->fails()){
                return response()->json($validator->errors(),400);
            }

            $profile = Profiles::where('user_id', Auth::user()->id)->first();

            if(!$profile){
                $profile = new Profiles;
                $profile->user_id = Auth::user()->id;
            }

            $profile->first_name = $request->input('first_name');
            $profile->last_name = $request->input('last_name');
            $profile->summary = $request->input('summary');

            if($request->hasFile('image')){
                $firstName = str_replace(' ', '_', $request->input('first_name'));
                $lastName = str_replace(' ', '_', $request->input('last_name'));

                $imagName = Auth::user()->id . '_' . $firstName . '_' . $lastName;
                $request->file('image')->move(storage_path('uploads/image_profile'), $imagName);

                $current_image_path = storage_path('avatar') . '/' . $profile->iamge;
                if(file_exists($current_image_path)){
                    unlink($current_image_path);
                }

                $profile->image = $imagName;
            }

            $profile->save();
            return response()->json($profile, 200);

        }else{
            return response('Unsupported Media Type', 415);
        }
    }

    public function show($id, Request $request){
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml'){
            $profiles = Profiles::where('id',$id)->get();

            if ($acceptHeader === 'application/json') {
                return response()->json($profiles, 200);
            } else {
                $xml = new \SimpleXMLElement('<profiles/>');
                foreach ($profiles as $item) {
                    $xmlItem = $xml->addChild('profiles');

                    $xmlItem->addChild('id', $item->id);
                    $xmlItem->addChild('user_id', $item->user_id);
                    $xmlItem->addChild('first_name', $item->first_name);
                    $xmlItem->addChild('last_name', $item->last_name);
                    $xmlItem->addChild('summary', $item->summary);
                    $xmlItem->addChild('image', $item->image);
                }
                return $xml->asXML();
            }

            if(!$profiles) {
                abort(404);
            }
   
        } else {
            return response('Not Acceptable!', 406);
        }
    }

    // public function update(Request $request, $id){
    //     $acceptHeader = $request->header('Accept');
    //     $contentTypeHeader = $request->header('Content-Type');

    //     if ($acceptHeader === 'application/json') {
    //         $input = $request->all();
    //         $profiles = Profiles::find($id);

    //         if(!$profiles) {
    //             abort(404);
    //         }

    //         //validation
    //         $validationRules = [
    //             'first_name' => 'required',
    //             'last_name' => 'required',
    //             'summary' => 'required',
    //         ];

    //         $validator = Validator::make($input, $validationRules);

    //         if ($validator->fails()){
    //             return response()->json($validator->errors(),400);
    //         }
    //         $profiles = Profiles::where('user_id', Auth::user()->id)->first();

    //         if(!$profiles){
    //             $profile = new Profiles;
    //             $profile->user_id = Auth::user()->id;
    //         }

    //         $profiles->first_name = $request->input('first_name');
    //         $profiles->last_name = $request->input('last_name');
    //         $profiles->summary = $request->input('summary');
    //         $profiles->save();

    //         return response()->json($profiles, 200);
    //     }
    //     else{
    //         return response('Unsupported Media Type', 415);
    //     }
    // }

    // public function destroy($id, Request $request){
    //     $acceptHeader = $request->header('Accept');
    //     if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml'){
    //         $profiles = Profiles::find($id);

    //         if(!$profiles) {
    //             abort(404);
    //         }

    //         $profiles->delete();
    //         $message = ['message' => 'deleted successfully', 'user_id' => $id];
    //         return response()->json($message, 200);
   
    //     } else {
    //         return response('Not Acceptable!', 406);
    //     }
    // }

    public function image($imageName){
        $imagePath = storage_path('uploads/image_profile') . '/' . $imageName;
        if(file_exists($imagePath)){
            $file = file_get_contents($imagePath);
            return response($file, 200)->header('Content-Type', 'image/jpeg');
        }
        return response()->json(array(
            "message" => "Image not found"
        ), 401);
    }
}