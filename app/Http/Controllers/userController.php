<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Mail\Gmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Auth;


class userController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function validate(Request $request){
        $data=$request->all();
        $rules = [
            "name"=>"required|max:20|min:6",
            "email"=>"required|email|unique:users",
            "password"=>"required|max:20|min:6"
        ];
        $messages = [
            "name.required"=>"Bạn chưa nhập tên!!!","name.max"=>"Tên bạn nhập phải nhỏ hơn 20 kí tự!!!","name.min"=>"Tên bạn nhập phải lớn hơn 5 kí tự!!!",
            "email.required"=>"Bạn chưa nhập email!!!","email.email"=>"Vui lòng nhập đúng định dạng email!!!","email.unique"=>"Email bạn nhập đã trùng!!!",
            "password.required"=>"Bạn chưa nhập mật khẩu!!!","password.max"=>"Mật khẩu bạn nhập phải nhỏ hơn 20 kí tự!!!","password.min"=>"Mật khẩu bạn nhập phải lớn hơn 5 kí tự!!!"
        ];
        $validator = Validator::make($data, $rules,$messages);
        if($validator->fails()){
            return response()->json($validator->errors());
        }
    }
    public function create(Request $request)
    {
        $data=$request->all();
        $rules = [
            "name"=>"required|max:20|min:6",
            "email"=>"required|email|unique:users",
            "password"=>"required|max:20|min:6"
        ];
        $messages = [
            "name.required"=>"Bạn chưa nhập tên!!!","name.max"=>"Tên bạn nhập phải nhỏ hơn 20 kí tự!!!","name.min"=>"Tên bạn nhập phải lớn hơn 5 kí tự!!!",
            "email.required"=>"Bạn chưa nhập email!!!","email.email"=>"Vui lòng nhập đúng định dạng email!!!","email.unique"=>"Email bạn nhập đã trùng!!!",
            "password.required"=>"Bạn chưa nhập mật khẩu!!!","password.max"=>"Mật khẩu bạn nhập phải nhỏ hơn 20 kí tự!!!","password.min"=>"Mật khẩu bạn nhập phải lớn hơn 5 kí tự!!!"
        ];
        $validator = Validator::make($data, $rules,$messages);
        if($validator->fails()){
            return response()->json($validator->errors());
        }else{
            $data['password'] = Hash::make($data['password']);
            $details = [
                'title' => 'XIN CHÀO ' . $data['name'],
                'token' => Str::random(60),
            ];
            $userCreate = new User();
            $userCreate->name = $data["name"];
            $userCreate->email = $data["email"];
            $userCreate->password = $data["password"];
            $userCreate->token = $details["token"];
            $result=$userCreate->save();
            Mail::to($data['email'])->send(new Gmail($details));
            $token=$userCreate->createToken('authToken')->accessToken;
            return response()->json([
                'status'=>$userCreate,
            ]);
        }
    }
    public function userVerification($token){
        $data = User::where('token',$token)->first();
        if($data){
            $userUpdate=User::find($data['id']);
            $userUpdate->email_verified_at=Carbon::now()->toDateTimeString();
            $userUpdate->token='';
            $result=$userUpdate->save();
            return response()->json([
                'status'=>true,
                'message'=>'Xác thực thông tin thành công',
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'message'=>'Bạn đã xác thực thông tin trong quá khứ'
            ]);
        }
    }
    public function handleLogin(Request $request){
        $data = $request->all();
        $user = User::where('email',$data['email'])->first();
        if($user){
            if(Hash::check($data['password'],$user['password'])){
                if($user['email_verified_at']){
                    $user->token = $user->createToken('authToken')->accessToken;
                    return response()->json([
                        'status' => true,
                        'message' => 'Đăng nhập thành công',
                        'data' => $user,
                    ]);
                }else{
                    $user->token=$user->createToken('authToken')->accessToken;
                    return response()->json([
                        'status' => false,
                        'message' => 'Bạn chưa xác thực Gmail',
                        'data' => $user,
                    ]);
                };
            }
        };
        return response()->json([
            'status' => false,
            'message' => 'Sai tài khoản hoặc mật khẩu',
        ]);
    }
    public function resendEmail(Request $request){
        if(Auth::id()){
            $data = User::find(Auth::id());
            $details = [
                'title' => 'XIN CHÀO ' . $data['name'],
                'token' => $data['token'],
            ];
            Mail::to($data['email'])->send(new Gmail($details));
            return response()->json([
                'status' => true,
                'message' => 'Mail xác thực thông tin đã được gửi',
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Lỗi chưa đăng nhập'
            ]);
        }
    }
    public function checkInfo(Request $request){
        return response()->json(Auth::user());
    }
    public function forgetPassword(Request $request){
        // $data = User::where('email',$request->email)->first();
        validate();
        // $data=$request->all();
        // $validator = Validator::make($this->data, $this->rules,$this->messages);
        // if($validator->fails()){
        //     return response()->json($validator->errors());
        // }
        // if($data){
            
        // }else{
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Email bạn nhập không tồn tại',
        //     ]);
        // }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

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
        //
    }
}