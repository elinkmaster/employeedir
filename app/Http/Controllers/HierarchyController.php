<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HierarchyController extends Controller
{
    public function hierarchy(){
        return view('employee.hierarchy');
    }

    public function updateHierarchy(Request $request){
	$this->validate($request, [
            'file' => 'mimes:png, jpg, jpeg |max:4096',
        ],
            $messages = [
                'required' => 'The :attribute field is required.',
                'mimes' => 'Only png, jpg, jpeg are allowed.'
            ]
        );
        if ($request->hasFile("file")) {
            $extension = $request->file('file')->guessExtension();
            // $path = $request->file->storeAs('/', 'company-hierarchy.jpeg');
            $path = Storage::disk('public')->putFileAs('img', $request->file, 'company-hierarchy.jpeg');
        }

        return back()->with('success', "Successfully changed the employee hierarchy image.");
    }
}
