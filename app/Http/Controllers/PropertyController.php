<?php

namespace App\Http\Controllers;

use App\Models\County;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PropertyController extends Controller
{
    // Manage properties
    public function managePage()
    {
        return view('manage-properties');
    }

    // load
    public function load(Request $request)
    {
        $user = $request->user();
        $properties = Property::filter($request)->with(['county']);
        if ($user->hasRole('owner') || $user->hasRole('agent')) {
            $properties = $properties->where('company_id', $request->user()->company_id);
        }
        return $properties->paginate(20);
    }

    // create
    public function create(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:20'],
            'no_of_units' => ['required', 'numeric'],
            'type' => ['required', 'numeric', Rule::in([1,2])],
            'description' => ['required', 'string', 'max:2000'],
            'features' => ['required', 'string', 'max:2000'],
            'location' => ['required', 'string', 'max:30'],
            'county_id' => ['required', 'string'],
            'kyc_docs_url' => ['required', 'mimes:jpeg,png,jpg,pdf', 'max:1024'],
            'google_map_embed_code' => ['nullable', 'string', 'max:500'],
            'photo_url' => ['required', 'mimes:jpeg,png,jpg', 'max:1024'],
            'youtube_embed_code' => ['required', 'string', 'max:500']
        ]);

        if ($request->hasFile('kyc_docs_url') && $request->hasFile('photo_url')){
            if (env('APP_ENV') == 'production'){
                $kyc_docs_url_extension = $request->file('kyc_docs_url')->extension();
                $kyc_docs_url = Storage::disk('do_spaces')->putFileAs('rent_easy/properties', $request->file('kyc_docs_url'), time().'.'.$kyc_docs_url_extension, 'public');

                $photo_url_extension = $request->file('photo_url')->extension();
                $photo_url = Storage::disk('do_spaces')->putFileAs('rent_easy/properties', $request->file('photo_url'), time().'.'.$photo_url_extension, 'public');

                $kyc_docs_url_file_path = 'https://mobilesasa-attachments.fra1.digitaloceanspaces.com/'.$kyc_docs_url;
                $photo_url_file_path = 'https://mobilesasa-attachments.fra1.digitaloceanspaces.com/'.$photo_url;
            } else {
                $kyc_docs_url_file_path = "https://via.placeholder.com/640x480.png";
                $photo_url_file_path = "https://via.placeholder.com/640x480.png";
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Property kyc docs & cover photo upload failed. Please select attachments before uploading'
            ]);
        }

        Property::create([
            'user_id' => $request->user()->id,
            'company_id' => $request->user()->company_id,
            'name' => $request->input('name'),
            'no_of_units' => $request->input('no_of_units'),
            'type' => $request->input('type'),
            'description' => $request->input('description'),
            'features' => $request->input('features'),
            'location' => $request->input('location'),
            'county_id' => $request->input('county_id'),
            'kyc_docs_url' => $kyc_docs_url_file_path,
            'google_map_embed_code' => $request->input('google_map_embed_code'),
            'photo_url' => $photo_url_file_path,
            'youtube_embed_code' => $request->input('youtube_embed_code')
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Property saved successfully'
        ]);
    }

    // update
    public function update(Request $request, Property $property)
    {
        if (!$request->user()->hasRole('admin') && $property->status){
            return response()->json([
                'status' => false,
                'message' => 'Property '.$property->name.' is already occupied. Contact system admin on '.env('ADMIN_MSISDN').' to update its details'
            ]);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:20'],
            'no_of_units' => ['required', 'numeric'],
            'type' => ['required', 'numeric', Rule::in([1,2])],
            'description' => ['required', 'string', 'max:2000'],
            'features' => ['required', 'string', 'max:2000'],
            'location' => ['required', 'string', 'max:30'],
            'county_id' => ['required', 'string'],
            'kyc_docs_url' => ['nullable', 'mimes:jpeg,png,jpg,pdf', 'max:1024'],
            'google_map_embed_code' => ['nullable', 'string', 'max:500'],
            'photo_url' => ['nullable', 'mimes:jpeg,png,jpg', 'max:1024'],
            'youtube_embed_code' => ['required', 'string', 'max:500']
        ]);

        if ($request->hasFile('kyc_docs_url') && $request->hasFile('photo_url')){
            if (env('APP_ENV') == 'production'){
                $kyc_docs_url_extension = $request->file('kyc_docs_url')->extension();
                $kyc_docs_url = Storage::disk('do_spaces')->putFileAs('rent_easy/properties', $request->file('kyc_docs_url'), time().'.'.$kyc_docs_url_extension, 'public');

                $photo_url_extension = $request->file('photo_url')->extension();
                $photo_url = Storage::disk('do_spaces')->putFileAs('rent_easy/properties', $request->file('photo_url'), time().'.'.$photo_url_extension, 'public');

                $kyc_docs_url_file_path = 'https://mobilesasa-attachments.fra1.digitaloceanspaces.com/'.$kyc_docs_url;
                $photo_url_file_path = 'https://mobilesasa-attachments.fra1.digitaloceanspaces.com/'.$photo_url;

                // delete from digital ocean spaces
                Storage::disk('do_spaces')->delete(str_replace('https://mobilesasa-attachments.fra1.digitaloceanspaces.com/', '', $property->kyc_docs_url));
                Storage::disk('do_spaces')->delete(str_replace('https://mobilesasa-attachments.fra1.digitaloceanspaces.com/', '', $property->photo_url));
            } else {
                $kyc_docs_url_file_path = $property->kyc_docs_url;
                $photo_url_file_path = $property->photo_url;
            }
        } else {
            $kyc_docs_url_file_path = $property->kyc_docs_url;
            $photo_url_file_path = $property->photo_url;
        }

        $property->update([
            'name' => $request->input('name'),
            'no_of_units' => $request->input('no_of_units'),
            'type' => $request->input('type'),
            'description' => $request->input('description'),
            'features' => $request->input('features'),
            'location' => $request->input('location'),
            'county_id' => $request->input('county_id'),
            'kyc_docs_url' => $kyc_docs_url_file_path,
            'google_map_embed_code' => $request->input('google_map_embed_code'),
            'photo_url' => $photo_url_file_path,
            'youtube_embed_code' => $request->input('youtube_embed_code')
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Property updated successfully'
        ]);
    }
}
