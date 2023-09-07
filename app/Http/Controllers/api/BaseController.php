<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sell_Buy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Rate_User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class BaseController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->guard = "api";
    }

    /**
     * Register user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
        $token = auth()->attempt($validator->validated());
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user,
            'token'=>$this->respondWithToken($token),

        ], 201);
    }

    public function editPro(Request $request)
    {
        $user = User::find((Auth::user()->id));
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:100',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $user->update([
            'name' => $request->name,
            'password' => Hash::make($request->password)
        ]);
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ]);

    }

    /**
     * login user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);

        // return response()->json([
        //     $this->respondWithToken($token),
        //     'message' => 'User successfully login',


        // ], 201);
    }

    /**
     * Refresh token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth::refresh());
    }


    public function logout()
    {
        auth::logout();

        return response()->json(['message' => 'User successfully logged out.']);
    }

    public function profile()
    {
        // return response()->json(auth()->user());

        $account = User::select('name','email','created_at')
        ->find(auth()->user());

        $products=Product::where('user_id','=',(Auth::user()->id))->get();

        $products_sell=Product::onlyTrashed()
        ->where('user_id','=',(Auth::user()->id))
        ->get();

        // $products_buy=Sell_Buy::where('userbuy_id','=',(Auth::user()->id))->get();
        // $products_buy=DB::onlyTrashed()->
        // table('products')
        // ->join('sell__buys','sell__buys.userbuy_id','=','products.id')
        // ->where('user_id','=',(Auth::user()->id))
        // // ->wherenotNull('deleted_at')
        // ->get();
        // $query = Sell_Buy::join('products','sell__buys.userbuy_id','=','products.id');
        // $buy=Product::all();
        // $buys = $query->select('products.*')
        // ->where('products.deleted_at',null)
        // ->get();
        // $query = Product::all();
        // echo $query;
        // $query = Sell_Buy::all();

        $products_buy =Product::onlyTrashed()
        ->select('products.*')
        ->join("sell__buys",function ($join){
            $join->on('products.id','=','sell__buys.product_id');
        })
        ->where('sell__buys.userbuy_id','=',(Auth::user()->id))
        ->get();

        $myprofile=array([
            'account'=>$account,
            'myproducts'=>$products,
            'myproducts_sell'=>$products_sell,
            'myproducts_buy'=>$products_buy
        ]);

        return $this->sendResponse($myprofile, 'successfully.');
    }

    //Rate ++
    public function ratep($id)
    {
        $user=Product::where('id',$id)->first();


        $rate = new Rate_User;
        $rate->user_id=$user->user_id;
        $rate->rate=1;
        $rate->save();

        //Rate --
         // $rate = new Rate_User;
        // $rate->user_id=$user->user_id;
        // $rate->rate=-1;
        // $rate->save();

        //Rate get sum
        //  $rates=DB::table('rate_users')
        //  ->select(DB::raw('sum(rate) as user_rate,user_id'))
        // ->where('user_id',$user->user_id)
        // ->groupBy('user_id')
        // ->get();
        // echo $rates;


        // get products without users thier rate <=-5
        // $products = DB::table('products')
        // ->join('rate_users','products.user_id','=','rate_users.user_id')
        // ->whereNotIn('products.user_id',[Auth::user()->id])
        // ->where('deleted_at',Null)
        // ->where('sum(rate_users.rate) as user_rate,user_id)','>=',4)
        // ->groupBy('rate_users.user_id')
        // ->get();

        // $products =Product::
        // select('products.*')
        // ->join("rate_users",function ($join){
        //     $join->on('products.user_id','=','rate_users.user_id');
        // })
        // // ->whereNotIn('products.user_id',[Auth::user()->id])
        // ->where('deleted_at',Null)
        // ->select(DB::raw('count(rate) as user_rate,rate_users.user_id'))
        // ->where('rate_users.rate','>=',0)
        // ->groupBy('user_id')
        // ->get();

        // $products= Product::select('products.user_id','rate_users.user_id',DB::raw('sum(rate_users.rate) as rates'))
        // ->leftjoin('rate_users','rate_users.user_id','=','products.user_id')
        // ->groupBy('user_id')
        // ->get();

        // $products= "select id from products p join (select user_id,sum(rate) as rates from rate_users
        // )as t on p.user_id = t.user_id";

        // $q=Product::select('*')
        // ->where('id',$products)
        // ->get();

        // $products =Rate_User::
        // select(DB::raw('sum(rate) as user_rate,user_id'))
        // ->groupBy('user_id')
        // ->get();

        // $p=Product::
        // join('rate_users','products.user_id','=','rate_users.user_id')
        // ->where('$products->rate','>',1)
        // ->groupBy('user_id')
        // ->get();

        // $pro=DB::table('products')->select('products.*')
        // ->join('rate_users',function ($join){
        //     $join->on('products.user_id','=','rate_users.user_id')
        //     // ->where(DB::raw('sum(rate) as user_rate,user_id'))
        //     ->where('rate_users.rate','>',0)
        //     // ->groupBy('user_id')
        //     ;
        // })->get();

        return 'successfully.';



    }
     //  Rate --
    public function ratem($id)
    {
        $user=Product::where('id',$id)->first();
        $rate = new Rate_User;
        $rate->user_id=$user->user_id;
        $rate->rate=-1;
        $rate->save();
        return 'successfully.';
    }

    public function getrate($id)
    {
         //Rate get sum
         $user=Product::where('id',$id)->first();
         $rates=DB::table('rate_users')
         ->select(DB::raw('sum(rate) as user_rate,user_id'))
        ->where('user_id',$user->user_id)
        ->groupBy('user_id')
        ->get();
        // echo $rates;

        return $this->sendResponse($rates, 'successfully.');
    }




    /**
     *
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth::factory()->getTTL() * 60*24*7
        ]);
    }


    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];


        return response()->json($response, 200);
    }
}
