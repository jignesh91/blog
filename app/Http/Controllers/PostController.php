<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controllers;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use DataTables;
use Auth;
use App\Posts;

class PostController extends Controller
{
    public function __construct()
    {
        $this->moduleRouteText = "post";
        $this->moduleViewName = "post";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "Post";
        $this->module = $module;
        
        $this->modelObj = new Posts();
        
        $this->addMsg = $module. " has been addded successfully!";
        $this->updateMsg = $module. " has been updated successfully!";
        $this->deleteMsg = $module. " has been deleted successfully!";
        $this->deleteErrorMsg = $module. " can not deleted!";

        view()->share('list_url',$this->list_url);
        view()->share('moduleRouteText',$this->moduleRouteText);
        view()->share('moduleViewName',$this->moduleViewName);
    }

    public function index()
    {
        $data = array();
        $data['title'] = "Post";
        $data['module_title'] = "Post";
        $data['add_url'] = route($this->moduleRouteText.".create");
        $data['addBtnName'] = "Add Post";
        $data['btnAdd'] = 1;

        //fetch 5 posts from database which are active and latest
        $posts = Posts::where('active',1)->orderBy('created_at','desc')->paginate(5);
        
        //return home.blade.php template from resources/views folder
        $data['posts'] = $posts;

        return view($this->moduleViewName.".index",$data);
    }

    public function create()
    {
        $data = array();
        $data['formObj'] = $this->modelObj;
        $data['title'] = "Add ".$this->module;
        $data['action_url'] = $this->moduleRouteText.".store";
        $data['action_params'] = 0;
        $data['buttonText'] = "Save";
        $data['method'] = "POST";

        $posts = Posts::where('active',1)->orderBy('created_at','desc');
    
        $data['posts'] = $posts;

        return view($this->moduleViewName.".add",$data);
    }

    public function store(Request $request)
    {
        $status = 1;
        $msg = $this->addMsg;
        $data = array();
        $module = $this->modelObj;

        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
            'description' => 'required',
            'image' => 'required|unique:posts',
            'tags' => 'required',
        ]);

        // check validations
        if ($validator->fails()) 
        {
            $messages = $validator->messages();
            
            $status = 0;
            $msg = "";
            
            foreach ($messages->all() as $message) 
            {
                $msg .= $message . "<br />";
            }
        }
        else
        {
            $u_id = Auth::id();
            $duplicate = Posts::where('title',$request->get('title'))->first();
            if ($duplicate) {
              return redirect('post')->withErrors('Title already exists.')->withInput();
            }

            $module->title = $request->get('title');
            $module->description = $request->get('description');
            $tgs_arr = implode(', ', $request->get('tags'));
            $module->tags = $tgs_arr;
            $id = $module->id;
            $module->author_id = $u_id;
            $module->active  = 1;

            $image = $request->file('image');
            if(!empty($image))
            {
                $destinationPath = 'uploads'.DIRECTORY_SEPARATOR.'post'.DIRECTORY_SEPARATOR.$id;

                $image_name  =  $image->getClientOriginalName();
                $extension   =  $image->getClientOriginalExtension();
                $image_name  =  md5($image_name);
                $file_image  =  $image_name.'.'.$extension;
                $file        =  $image->move($destinationPath, $file_image);

                $module->image = $file_image;
                
            }
            $module->save();
            session()->flash('success_message', $msg);
        }

        return ['status' => $status, 'msg' => $msg, 'data' => $data]; 
    }

    public function show($id)
    {
        $post = Posts::where('id',$id)->first();
        // print_r($post->toArray());
        if(!$post)
        {
           return redirect('/')->withErrors('requested page not found');
        }

        return view($this->moduleViewName.".show")->withPost($post);
    }

    public function edit($id)
    {
        $formObj = $this->modelObj->find($id);

        if(!$formObj)
        {
            abort(404);
        }   

        $data = array();
        $data['formObj'] = $formObj;
        $data['title'] = "Edit ".$this->module;
        $data['buttonText'] = "Update";
        $data['action_url'] = $this->moduleRouteText.".update";
        $data['action_params'] = $formObj->id;
        $data['method'] = "PUT";

        $posts = Posts::all();
        
        //return home.blade.php template from resources/views folder
        $data['posts'] = $posts;

        return view($this->moduleViewName.'.add', $data);
    }

    public function update(Request $request,$id)
    {
        
        $model = $this->modelObj->find($id);

        $status = 1;
        $msg = $this->updateMsg;
        $data = array();

        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
            'description' => 'required',
            'image' => 'required|unique:posts',
            'tags' => 'required',
        ]);
        
        // check validations
        if(!$model)
        {
            $status = 0;
            $msg = "Record not found !";
        }
        else if ($validator->fails()) 
        {
            $messages = $validator->messages();
            
            $status = 0;
            $msg = "";

            foreach ($messages->all() as $message) 
            {
                $msg .= $message . "<br />";
            }
        }         
        else
        {
            $u_id = Auth::id();

            $model->title = $request->get('title');
            $model->description = $request->get('description');
            $tgs_arr = implode(', ', $request->get('tags'));
            $model->tags = $tgs_arr;
            $id = $model->id;
            $model->author_id = $u_id;
            $model->active  = 1;

            $image = $request->file('image');

            $image = $request->file('image');
            if(!empty($image))
            {
                $destinationPath = 'uploads'.DIRECTORY_SEPARATOR.'post'.DIRECTORY_SEPARATOR.$id;

                $image_name  =  $image->getClientOriginalName();
                $extension   =  $image->getClientOriginalExtension();
                $image_name  =  md5($image_name);
                $file_image  =  $image_name.'.'.$extension;
                $file        =  $image->move($destinationPath, $file_image);

                $model->image = $file_image;
                
            }
            $model->save();

            session()->flash('success_message', $msg);
        }
        
        return ['status' => $status, 'msg' => $msg, 'data' => $data];       
    }

    public function delete($id,Request $request)
    {
        $modelObj = $this->modelObj->find($id);

        if($modelObj) 
        {
            try 
            {
                $url = public_path().'/uploads/post/'.$id.'/'.$modelObj->image;
                if (is_file($url)) {
                    unlink($url);
                }

                $backUrl = $request->server('HTTP_REFERER');
                $modelObj->delete();
                session()->flash('success_message', $this->deleteMsg); 

                return redirect($backUrl);
            } 
            catch (Exception $e) 
            {
                session()->flash('error_message', $this->deleteErrorMsg);
                return redirect($this->list_url);
            }
        } 
        else 
        {
            session()->flash('error_message', "Record not exists");
            return redirect($this->list_url);
        }
    }
}
