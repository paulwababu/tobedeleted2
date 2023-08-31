<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    // create
    public function create(Request $request, Unit $unit)
    {
        if (!$request->user()->hasRole('admin') && $unit->status){
            return response()->json([
                'status' => false,
                'message' => 'Unit '.$unit->label.' is already approved. Contact system admin on '.env('ADMIN_MSISDN').' to add new photos'
            ]);
        }

        $request->validate([
            'photos' => ['required'],
            'photos.*' => ['mimes:jpeg,png,jpg', 'max:1024']
        ]);

        $photos = $request->file('photos');

        if (count($photos) > 4) {
            return response()->json([
                'status' => false,
                'message' => 'Only 4 unit photos(s) can be uploaded at a time'
            ]);
        }

        if (!$request->hasFile('photos')){
            return response()->json([
                'status' => false,
                'message' => 'Unit photo(s) upload failed. Please select attachments before uploading'
            ]);
        }

        foreach ($photos as $photo) {
            if (env('APP_ENV') == 'production'){
                $photo_extension = $request->file($photo)->extension();
                $photo_url = Storage::disk('do_spaces')->putFileAs('rent_easy/properties/units', $request->file($photo), time().'.'.$photo_extension, 'public');
                $photo_file_path = 'https://mobilesasa-attachments.fra1.digitaloceanspaces.com/'.$photo_url;
            } else {
                $photo_file_path = "https://via.placeholder.com/640x480.png";
            }

            $unit->photos()->create([
                'photo_url' => $photo_file_path
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Unit photo(s) uploaded successfully'
        ]);
    }

    // delete
    public function delete(Request $request, Photo $photo)
    {
        if (!$request->user()->hasRole('admin') && $photo->unit->status){
            return response()->json([
                'status' => false,
                'message' => 'Unit '.$photo->unit->label.' is already occupied. Contact system admin on '.env('ADMIN_MSISDN').' to delete photo(s)'
            ]);
        }

        $photo->forceDelete();
        if(env('APP_ENV') == 'production'){
            // delete from digital ocean spaces
            Storage::disk('do_spaces')->delete(str_replace('https://mobilesasa-attachments.fra1.digitaloceanspaces.com/', '', $photo->photo_url));
        }
        return response()->json([
            'status' => true,
            'message' => 'Unit photo deleted successfully'
        ]);
    }
}
