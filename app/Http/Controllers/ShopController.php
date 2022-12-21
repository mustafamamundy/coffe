<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\post;
use App\shopper;
use App\contact;
use App\shopping;
use App\cart;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{

    public function get_user_id(){
        return Auth::id();
    }

    public function index(){
        $guest = Cookie::has('guest') ? Cookie::get("guest") : Cookie::queue("guest" , rand().rand() ,4555);
        $products = post::orderBy('userid', 'ASC')->get();
        return view('shopping' , ["products" => $products , "guest" => $guest , "userid" => $this->get_user_id()]);
    }
    public function viewShpper(){
        $shoppers = shopper::all();
        return view('shopper.shopper' , ["shoppers" => $shoppers]);
    }
    public function show($id){
        $products = post::where('userid' , $id)->orderBy('title', 'DESC')->get();
        return view('shopper.profile' , ["products" => $products]);
    }
    public function getDetails($userid , $id){
        $product = post::findOrFail($id);
        $shoppers = shopper::findOrFail($userid);
        return view('shopper.details' , ["product" => $product , "shoppers" => $shoppers]);
    }
    public function update(){
        $OneImage = post::findOrFail(1);
        $OneImage->details = "hi this is updated";
        $OneImage->update(); 
    }
    public function delete($id){
        $getPost = post::findOrfail($id);
        $image_path = \public_path()."/upload/".$getPost->image; 
        if(File::exists($image_path)) {
        $k = $getPost::where(['userid' => $this->get_user_id() , 'id' => $id])->delete();
        if($k === 1 || $k === true){
         File::delete($image_path);
        }
    }
    }
    
    public function saveContact(){
        $contact = new contact();
        $contact->title = \request('title');
        $contact->desc = \request('desc');
        $k = $contact->save();
        if($k === 1 || $k === true){
            return redirect('/')->with("success" , "Thanks For Contacting Us");
        }
    }

    public function order(){

        $guest_id = Cookie::get("guest") ;
        $size = \request('sizes');
        $post_id =  \request('post_id');
        if(empty($guest_id) || empty($size) || empty($post_id)){
            return redirect('/')->with("file_order" , "Sorry Please Fill Blanks");
        }else{
            $cart = new cart();
            $cart->guest_id = $guest_id;
            $cart->size = $size;
            $cart->post_id = $post_id;
            $k = $cart->save();
            if($k === 1 || $k === true){
                return redirect('/')->with("success_order" , "Thanks For Order");
            }

        }



    }
    public function cart(){
        return view('/cart');
    }


    
}
