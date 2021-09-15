<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $todo = cache('todo') ?? [];
        return response()->json($todo);
    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'title'=>'required',
            'description'=>'required'
        ]);
      
        $data = $request->only(['title','description']);
        $data['id'] = md5(rand());
        $data['completed'] = false;
        $todo = cache('todo') ?? [];
        array_push($todo,$data);
        cache(['todo'=>$todo]);
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $todo = cache('todo') ?? [];
        $data = collect($todo)->firstWhere('id',$id);
        return response()->json($data);
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
        $this->validate($request,[
            'title'=>'required',
            'description'=>'required'
        ]);
      
        $data = $request->only(['title','description']);
        $todo = cache('todo') ?? [];
        $todo = collect($todo);
        $key = $todo->search(function($item) use ($id){
            return $item['id'] ==  $id;
        });
        $item = $todo->pull($key);
        $item['title'] = $data['title'];
        $item['description'] = $data['description'];
        $todo = $todo->all();
        array_push($todo,$item);
        cache(['todo'=>$todo]);
        return response()->json($item);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $todo = cache('todo') ?? [];
        $todo = collect($todo);
        $key = $todo->search(function($item) use ($id){
            return $item['id'] ==  $id;
        });
        $todo->pull($key);
        $todo = $todo->all();
        cache(['todo'=>$todo]);
        return response()->json();
    }

    public function complete($id)
    {
        $todo = cache('todo') ?? [];
        $todo = collect($todo);
        $key = $todo->search(function($item) use ($id){
            return $item['id'] ==  $id;
        });
        $item = $todo->pull($key);
        $item['completed'] = true;
        $todo = $todo->all();
        array_push($todo,$item);
        cache(['todo'=>$todo]);
        return response()->json($item);
    }
}
