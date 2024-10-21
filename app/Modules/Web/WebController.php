<?php

namespace App\Modules\Web;

use App\Http\Controllers\Controller;

class WebController extends Controller{

    protected $viewData = [];


    public function index(){
        return redirect('/system');
    }

    protected function view($file,array $data = []){
        return view('web.'.$file,$data);
    }

    protected function response($status,$code = '200',$message = 'Done',$data = []): array {
        return [
            'status'=> $status,
            'code'=> $code,
            'message'=> $message,
            'data'=> $data
        ];
    }


}