<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use Session;
use Illuminate\support\Facades\Redirect;
session_start();

class ProductController extends Controller
{
    public function index()
    {	
    	$this->AdminAuthCheck();
    	return view('admin.add_product');
    }

	public function all_product()
    {	
    	$this->AdminAuthCheck();
    	$all_product_info= DB::table('tbl_products')
                        ->join('tbl_category','tbl_products.category_id','=','tbl_category.category_id')
                        ->join('tbl_manufacture','tbl_products.manufacture_id','=','tbl_manufacture.manufacture_id')
                        ->get();
                      
    	$manage_product=view('admin.all_product')
    	    ->with('all_product_info',$all_product_info); 
    	return view('admin_layout')
    		->with('admin.all_product',$manage_product);	
    }    

    public function save_product(Request $request)
    {	
    	
    	$this->AdminAuthCheck();
    	$data['product_name']=$request->product_name;
    	$data['category_id']=$request->category_id;

    	$data['manufacture_id']=$request->manufacture_id;
    	$data['product_short_description']=$request->product_short_description;
    	$data['product_long_description']=$request->product_long_description;

    	$data['product_price']=$request->product_price;
    	$data['product_size']=$request->product_size;

		$data['product_color']=$request->product_color;
        $data['publication_status']=$request->publication_status;
		 

	    $image=$request->file('product_image');
	    if($image) {
	    	$image_name=str_random(20);
	    	$ext=strtolower($image->getClientOriginalExtension());
	    	$image_full_name=$image_name.'.'.$ext;
	    	$upload_path='image/';
	    	$image_url=$upload_path.$image_full_name;
	    	$success=$image->move($upload_path,$image_full_name);
	    	if($success) {
	    		$data['product_image']=$image_url;
	    		DB::table('tbl_products')->insert($data);
	    		Session::put('message','Product added successfully!!');
	    		return Redirect::to('/add-product');
	    	}
	    }
	    $data['product_image']='';
	    	DB::table('tbl_products')->insert($data);
	    	Session::put('message','product added sccflly!');
	    	return Redirect::to('/add-product');


    }

    public function unactive_product($product_id)
    {
       DB::table('tbl_category')
            ->where('category_id',$product_id)
            ->update(['publication_status' => 0]);
            Session::put('message','Category Unactive successfully !!');
            return Redirect::to('/all-category');
    }



    public function AdminAuthCheck()
    {
      $admin_id = Session::get('admin_id');
      
      if ($admin_id) {
        return;
      }else{
        return Redirect::to('admin')->send();
      }
    }
}
