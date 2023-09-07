<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\User;
use App\Http\Resources\ProductResource;
use App\Models\Photo;
use App\Models\Sell_Buy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Null_;

use function PHPUnit\Framework\isNull;

class ProductController extends Controller
{
    public function __construct()
    {
         $this->middleware('auth');
        // $this->guard = "api";
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // $id=1;
        // $pro=User::find($id);
        // $pro->delete();
    //    $products = Product::all();
        $products = DB::table('products')
        ->whereNotIn('user_id',[Auth::user()->id])
        ->where('deleted_at',Null)
        ->get();

        // $products= Product::withTrashed()->get();


        // $products = Product::


       foreach ($products as $ph)
        {
            $files = Storage::disk('public_uploads')->getDriver()->getAdapter()->applyPathPrefix($ph->id.'/main.jpg');
            $ph->photo= $files;
        }
        return $this->sendResponse(ProductResource::collection($products), 'Products retrieved successfully.');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $imageName = $request->photo->getClientOriginalName();
        $imageName1 = $request->photo1->getClientOriginalName();
        $imageName2 = $request->photo2->getClientOriginalName();
            $product =   Product::create([
            'section'=>$request->section,
            'tybe'=>$request->tybe,
            'title'=>$request->title,
            'description'=>$request->description,
            'price'=>$request->price,
            'location_x'=>$request->location_x,
            'location_y'=>$request->location_y,
            'photo'=>$imageName,
            'old_new'=>$request->old_new,
            'user_id'=>(Auth::user()->id),
        ]);

        $product_id = Product::latest()->first()->id;
           Photo::create([
            'product_id'=>$product_id,
            'photo'=>$imageName1
        ]);

        Photo::create([
            'product_id'=>$product_id,
            'photo'=>$imageName2
        ]);

        //main photo
        $imageName = $request->photo->getClientOriginalName();
        $request->photo->move(public_path('Attachments/' . $product_id), 'main.jpg');


        $imageName1 = $request->photo1->getClientOriginalName();
        $request->photo1->move(public_path('Attachments/' . $product_id), '1.jpg');

        $imageName2 = $request->photo2->getClientOriginalName();
        $request->photo2->move(public_path('Attachments/' . $product_id), '2.jpg');


        return $this->sendResponse($product, 'Products Created successfully.');



    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($section)
    {

          $product = Product::where('section','LIKE','%'.$section.'%')->
          orwhere('title','LIKE','%'.$section.'%')->
          orwhere('tybe','LIKE','%'.$section.'%')->get();
          if (count($product)<1) {
            return $this->sendResponse($section, "Sorry! Products Not Found");
        }



        foreach ($product as $ph)
        {
            $files = Storage::disk('public_uploads')->getDriver()->getAdapter()->applyPathPrefix($ph->id.'/main.jpg');
            $ph->photo= $files;

        }

        return $this->sendResponse(ProductResource::collection($product), 'Product retrieved successfully.');
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
    public function updatee(Request $request)
    {
        // $imageName = $request->photo->getClientOriginalName();
        //  DB::table('products')->where('id',$request->id)->update([
        //     'section'=>$request->section,
        //     'tybe'=>$request->tybe,
        //     'title'=>$request->title,
        //     'description'=>$request->description,
        //     'price'=>$request->price,
        //     'location_x'=>$request->location_x,
        //     'location_y'=>$request->location_y,
        //     // 'photo'=>$imageName,
        //     'old_new'=>$request->old_new,
        //     'user_id'=>(Auth::user()->id),
        // ]);
        $Product = Product::find($request->id);
        if ($Product==null) {

            echo "sorry not found  product";
        }else{
            $Product->update([
            'section'=>$request->section,
            'tybe'=>$request->tybe,
            'title'=>$request->title,
            'description'=>$request->description,
            'price'=>$request->price,
            'location_x'=>$request->location_x,
            'location_y'=>$request->location_y,
            // 'photo'=>$imageName,
            'old_new'=>$request->old_new,
            'user_id'=>(Auth::user()->id),
        ]);
        }

    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Product = Product::find($id);
        if ($Product==null) {

            echo "sorry not found user for this product";
        }
        else{
            $Product->delete();
            return $this->sendResponse($id, 'Product retrieved successfully.');
        }
    }



    public function filter($name)
    {

            $products = Product::where('section',$name)->get();
            if (count($products)<1) {
                return $this->sendResponse($name, "Sorry! Products Not Found");
            }
            foreach ($products as $ph)
            {
                $files = Storage::disk('public_uploads')->getDriver()->getAdapter()->applyPathPrefix($ph->id.'/main.jpg');
                $ph->photo= $files;

            }
            return $this->sendResponse(ProductResource::collection($products), 'Product Filter Successfully.');
    }



    public function Detail($id)
    {
        // $product = Product::find($id);
        $product =Product::
        select('products.*','users.name')
        ->join("users",function ($join){
            $join->on('products.user_id','=','users.id');
        })
        ->where('products.id','=',$id)
        ->first();

        if (is_null($product)) {
            return $this->sendResponse($id, "Sorry Product Not Found");
        }
            $files = Storage::disk('public_uploads')->getDriver()->getAdapter()->applyPathPrefix($id.'/main.jpg');
            $product->photo= $files;
            //   return response()->file($files);
            $files2 = Storage::disk('public_uploads')->getDriver()->getAdapter()->applyPathPrefix($id.'/1.jpg');
            $files3=$files = Storage::disk('public_uploads')->getDriver()->getAdapter()->applyPathPrefix($id.'/2.jpg');
            $photos=([
          'ph1'=>$files2,
          'ph2'=>$files3
        ]);
            return $this->sendResponse(compact('product', 'photos'), 'Product Details Successfully.');

    }

    public function Sell($id)
    {
        // $user_id=Product::select('user_id')->where('id', $id)->first();
        $pro_id=Product::where('id', $id)->first();

        // echo $pro_id;
            if (is_null($pro_id)) {

                echo "sorry not found user for this product";
            }
        // echo $user_id;
               else{
        //        Sell_Buy::create([
        //         'usersell_id'=>$user_id,
        //         'userbuy_id'=>(Auth::user()->id),
        //          'product_id'=>$id,
        // ]);
        $sell = new Sell_Buy;
        $sell->usersell_id=$pro_id->user_id;
        $sell->userbuy_id=(Auth::user()->id);
        $sell->product_id=$pro_id->id;
        $sell->save();
               $Product = Product::find($id);
               $Product->delete();
               return $this->sendResponse($id, 'Product sell successfully.');
    }
        // echo $user,$user_id,$id;
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
