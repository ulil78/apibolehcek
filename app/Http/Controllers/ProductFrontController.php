<?php

namespace App\Http\Controllers;
use App\Product;

use Illuminate\Http\Request;

class ProductFrontController extends Controller
{
    public function getProductLevelOne($id)
    {
        $products = Product::with('product_images')
                            ->where('cat_level_one_id', $id)
                            ->where('status', 'true')
                            ->get();

        return $products->toJson();

    }

    public function getProductLevelTwo($id)
    {
        $products = Product::with('product_images')
                            ->where('cat_level_two_id', $id)
                            ->where('status', 'true')
                            ->get();

        return $products->toJson();

    }

    public function getProductLevelThree($id)
    {
        $products = Product::with('product_images')
                            ->where('cat_level_three_id', $id)
                            ->where('status', 'true')
                            ->get();

        return $products->toJson();

    }

    public function getProductDetail($id)
    {
      $products = Product::with('product_images')
                          ->where('id', $id)
                          ->where('status', 'true')
                          ->get();

      return $products->toJson();
    }

    public function getSeller($id)
    {
        $seller = \App\Seller::find($id);
        return $seller->toJson();


    }

    public function getSellerProducts($id)
    {
        $seller_product = \App\Seller::where('id', $id)
                              ->with('products')
                              ->first();
        return $seller_product->toJson();

    }

    public function getReviewProduct($id)
    {
       $reviews  = \App\Review::where('product_id', $id)
                              ->get();
        return $reviews->toJson();
    }
}
