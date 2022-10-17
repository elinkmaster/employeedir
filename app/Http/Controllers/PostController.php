<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Posts;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('post.index')->with('posts', Posts::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('post.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $post = new Posts();
        $post->posted_by_id = Auth::user()->id;

        if ($request->has('enabled')) {
            $post->enabled = 1;
        } else {
            $post->enabled = 0;
        }

        $post->save();

        if ($request->hasFile("images_videos")) {
            $extension = $request->file('images_videos')->guessExtension();
            $path = $request->images_videos->storeAs('images/posts/'.$post->id, $post->id . '.' . $extension);
            $post->image = asset('storage/app/'.$path);
            $post->save();
        }


        return redirect('posts')->with('success', "Successfully created Employee");
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
        $post = Posts::find($id);
        if($post->delete()){

            return  redirect('posts')->with('success', 'Successfully deleted a post');
        } else {
            return redirect('posts')->with('error', 'Something went wrong!');
        }
    }

    public function enabled(Request $request, $id){
        $post = Posts::find($id);
        $post->enabled = $request->enabled;
        if($post->save()){
            $action = "enabled";
            if ($request->enabled == 1) {
                $action = 'enabled';
            } else{
                $action = 'disabled';
            }
            return  redirect('posts')->with('success', 'Successfully ' . $action . ' a post.');
        } else {
            return redirect('posts')->with('error', 'Something went wrong!');
        }

    }
}
