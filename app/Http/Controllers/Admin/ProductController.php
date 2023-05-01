<?php

namespace App\Http\Controllers\Admin;

use App\Models\AssignProductAttribute;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductReview;
use App\Models\ProductStock;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ProductManager;
use App\Traits\ProductVariantManager;

class ProductController extends Controller
{
    use ProductManager, ProductVariantManager;

    public function index()
    {
        return view('admin.products.index', $this->products());
    }
    public function pending()
    {
        return view('admin.products.index', $this->pendingProducts());
    }

    public function adminProducts()
    {
        return view('admin.products.index', $this->productByVendor());
    }
    public function sellerProducts()
    {
        return view('admin.products.index', $this->productByVendor(false));
    }

    public function trashed()
    {
        return view('admin.products.index', $this->products(0, true));
    }

    public function create()
    {
        return view('admin.products.create', $this->productCreate());
    }

    public function edit($id)
    {
        return view('admin.products.create', $this->editProduct($id));
    }
    public function uploadProduct($product)
    {
        $image = getImage(imagePath()['product']['path'].  '/thumb_'. @$product->main_image);
        $data = [
            'product' => [
                'title' => $product->name,
                'body_html' => $product->description,
                'vendor' => 'My Company',
                'product_type' => 'Electronics',
                'variants' => [
                    [
                        'price' =>$product->base_price,
                        'sku' => $product->sku ?? 123,
                    ],
                ],
                // https://cdn.shopify.com/s/files/1/0739/8732/6231/products/mobile.jpg?v=1680623361
                "images"=> [
                    [
                        "src"=> $image
                    ]
                ],
                    // "images"=> [
                    //         "src"=> $image,
                    //   ],
            ],
        ];
        // dd($data);
            $curl = curl_init();   
            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://softwaredevelopmentpro.myshopify.com/admin/api/2023-04/products.json',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'X-Shopify-Access-Token: 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'
            ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return;
    }
    public function import($id)
    {
      $specificProduct =  Product::with('productImages')->where('id',$id)->first();
      $product = $this->getSpecificProduct($specificProduct->name);
      $res = !empty($product) ? (!empty($product->products) ? $product->products : null) : null;
          if( $res == null)
            {
                $this->uploadProduct($specificProduct);
            }
            $message = "Product Imported Successfully";
            $notify[]=['success', $message];
         return back()->withNotify($notify);
    }
    public function bulkImport()
    {
         $allProducts =  Product::with('productImages')->get();
        foreach ($allProducts as $key => $value) {
            $product = $this->getSpecificProduct($value->name);
            $res = !empty($product) ? (!empty($product->products) ? $product->products : null) : null;
            if( $res == null)
                {
                    $this->uploadProduct($value);
                }
        }
        $message = "Product Imported Successfully";
        $notify[]=['success', $message];
        return back()->withNotify($notify);
    }

    public function store(Request $request, $id){
          $data = [
                    'title' => $request->input('name'),
                    'body_html' => $request->description,
                    'vendor' => $request->input('vendor'),
                    'product_type' => $request->input('type'),
                    'tags' => $request->input('tags'),
                    'variants' => [
                        [
                            'price' => $request->input('base_price'),
                            'sku' => $request->input('sku'),
                        ],
                    ],
               ];
        return back()->withNotify(
                    $this->storeProduct($request, $id)
                );
    }

    public function delete($id)
    {
        return back()->withNotify(
            $this->deleteProduct($id)
        );
    }

    public function statusAction(Request $request)
    {

        $product = Product::findOrFail($request->product_id);
        if($product->status == 1){
            $product->status = 0;
            $msg = 'Product has been disabled';
        }else{
            $product->status = 1;
            $msg = 'Product has been approved';
        }
        $product->save();
        $notify[]=['success',$msg];
        return back()->withNotify($notify);
    }

    public function approveAll()
    {
        Product::pending()->update(['status' => 1]);
        $notify[]=['success','All pending product has been approved'];
        return back()->withNotify($notify);
    }

    public function addVariant($product_id)
    {
        return view('admin.products.variant.create', $this->addProductVariant($product_id));
    }

    public function storeVariant(Request $request, $id)
    {
        return back()->withNotify(
            $this->storeProductVariant($request, $id)
        );
    }
    function getSpecificProduct($product)
    {
        //////////////////////////////
        $curl = curl_init();   
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://softwaredevelopmentpro.myshopify.com/admin/api/2023-04/products.json?title='.$product,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_POSTFIELDS => null,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'X-Shopify-Access-Token: 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'
        ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($response);
        return $data;
    }

    public function updateVariant(Request $request, $id)
    {
        $attr_data = AssignProductAttribute::findOrFail($id);
        if($attr_data->productAttribute->type == 1 || $attr_data->productAttribute->type == 2){
            $request->validate([
                'name'  =>'required',
                'value' =>'required',
                'price' =>'required',
            ]);
        }elseif($attr_data->productAttribute->type == 3){
            $request->validate([
                'name'    => 'required',
                'image'   => ['required','image', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
                'price'   => 'required'
                ]);

                $old_img =(isset($attr_data->value))? $attr_data->value :'';

                if($request->hasFile('image')) {
                try {
                    $request->merge(['value' => uploadImage($request->image, imagePath()['attribute']['path'], imagePath()['attribute']['size'], $old_img)]);
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Couldn\'t upload the Image.'];
                    return back()->withNotify($notify);
                }
            }
        }
        $attr_data->name   = $request->name;
        $attr_data->value  = $request->value ?? '';
        $attr_data->extra_price  = $request->price;
        $attr_data->save();
        $notify[] = ['success', 'Product Variant Updated Successfully'];
        return redirect()->back()->withNotify($notify);
    }

    public function featured(Request $request)
    {
        $request->validate([
            'product_id'    =>'required|integer|gt:0',
        ]);

        $product = Product::findOrFail($request->product_id);

        if($product->is_featured == 1){
            $product->is_featured = 0;
            $msg = "Product removed from featured";
        } else{
            $product->is_featured = 1;
            $msg = "Product marked as featured";
        }

        $product->save();

        $notify[] = ['success', $msg];
        return redirect()->back()->withNotify($notify);
    }

    public function deleteVariant($id)
    {
        return back()->withNotify(
            $this->deleteProductVariant($id)
        );
    }

    public function reviews()
    {
        return view('admin.products.reviews', $this->productReviews());
    }


    public function reviewSearch(Request $request)
    {
        if($request->key != null) {
            return view('admin.products.reviews', $this->productReviewSearch($request->key));
        }else{
            return redirect()->route('admin.product.reviews');
        }
    }

    public function trashedReviews()
    {
        $pageTitle      = "All Product Reviews";
        $emptyMessage   = "No Review Yet";
        $reviews = ProductReview::onlyTrashed()->with(['product','user'])
                                    ->whereHas('product')->whereHas('user')
                                    ->latest()
                                    ->paginate(getPaginate());
        return view('admin.products.reviews', compact('pageTitle', 'emptyMessage', 'reviews'));
    }

    public function reviewDelete ($id)
    {
        $review = ProductReview::where('id', $id)->withTrashed()->first();
        if ($review->trashed()){
            $review->restore();
            $notify[] = ['success', 'Review Restored Successfully'];
            return redirect()->back()->withNotify($notify);
        }else{
            $review->delete();
            $notify[] = ['success', 'Review Deleted Successfully'];
            return redirect()->back()->withNotify($notify);
        }
    }

    public function addVariantImages($id)
    {
        return view('admin.products.variant.image', $this->addProductVariantImages($id));
    }

    public function saveVariantImages(Request $request, $id)
    {
        $storeImages = $this->saveProductVariantImages($request, $id);

        return redirect()->back()->withNotify($storeImages);
    }

}
