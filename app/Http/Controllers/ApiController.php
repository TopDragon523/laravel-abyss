<?php

namespace App\Http\Controllers;

use App\Models\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use UrlSigner;
use Carbon\Carbon;

class ApiController extends Controller
{
    protected Data $data;
    public function __construct(Data $data)
    {
        $this->data = $data;
    }
    // upload image
    public function postImage(Request $request)
    {
        $data = new Data();

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => "failed",
                "error" => 'file validation failed'
            ], 200);
        } else {
            $file = $request->file('file');
            $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->extension();
            $path = $name . '.' . time() . '.' . $extension;

            $file_name = Storage::putFile(
                'private/images',
                $file
            );
            $path = str_replace('private/images/', '', $file_name);
            $data->file = $path;
            $data->name = $request->name;
            $data->description = $request->description;
            $data->type = $request->type;
            $data->save();
            return response()->json([
                "status" => "succeed",
                "name" => $data->name,
                "description" => $data->description,
                "type" => $data->type,
            ], 200);
        }
    }
    //get 10 per page
    public function getDataByPage()
    {
        $response = Data::select('name', 'description', 'type')->paginate(10);
        return response()->json([
            $response
        ], 200);
    }
    //get single record
    public function getDataById($id)
    {
        $data = Data::where('id', $id)->first();
        if (empty($data)) {
            return response()->json([
                "status" => "failed",
                "error" => "This data is not found"
            ], 404);
        }
        $disk = Storage::disk('private');
        $url =  $disk->temporaryUrl($data->file, Carbon::now()->addMinutes(5));
        return response()->json([
            'status' => "succeed",
            'name' => $data->name,
            'description' => $data->description,
            'file' => $url,
            'type' => $data->type
        ], 200);
    }
}
