<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\SellLog;
use App\Models\Shop;
use GuzzleHttp\Client;
class OrderController extends Controller
{
    public function allOrders()
    {
        $shop_name = Shop::where('seller_id',seller()->id)->first()->name;
        $result = array();
        $orders = getOrders();
        $total_amount = 0;
          foreach ($orders->orders as $key => $value1) {
            foreach ($value1->line_items as $key => $value2) {
                if($shop_name == $value2->vendor)
                {
                    $total_amount = $total_amount + $value2->price;
                    array_push($result,$value1);
                }
            }
          }
          $orders = $result;
        $pageTitle      = "All Orders";
        $emptyMessage   = 'No order found';
        // $orders         = $this->filterOrders(OrderDetail::orders());
        return view('seller.order.index', compact('pageTitle', 'orders', 'emptyMessage'));
    }

    public function codOrders()
    {
        $pageTitle      = "COD Orders";
        $emptyMessage   = 'No COD order found';
        $orders         = $this->filterOrders(OrderDetail::cod());

        return view('seller.order.index', compact('pageTitle', 'orders', 'emptyMessage'));
    }

    public function pending()
    {
        $pageTitle     = "Pending Orders";
        $emptyMessage  = 'No pending order found';
        $orders        = $this->filterOrders(OrderDetail::pendingOrder());

        return view('seller.order.index', compact('pageTitle', 'orders', 'emptyMessage'));
    }

    public function onProcessing()
    {
        $pageTitle      = 'Processing Orders';
        $emptyMessage   = "No processing order found";
        $orders         = $this->filterOrders(OrderDetail::processingOrder());
        return view('seller.order.index', compact('pageTitle', 'orders', 'emptyMessage'));
    }

    public function dispatched()
    {
        $pageTitle     = "Orders Dispatched";
        $emptyMessage  = 'No dispatched order found';
        $orders        = $this->filterOrders(OrderDetail::dispatchedOrder());
        return view('seller.order.index', compact('pageTitle', 'orders', 'emptyMessage'));
    }


    public function canceledOrders()
    {
        $pageTitle     = "Canceled Orders";
        $emptyMessage  = 'No cancelled order found';
        $orders        = $this->filterOrders(OrderDetail::cancelledOrder());
        return view('seller.order.index', compact('pageTitle', 'orders', 'emptyMessage'));
    }

    public function deliveredOrders()
    {
        $pageTitle     = "Delivered Orders";
        $emptyMessage  = 'No delivered order found';
        $orders        = $this->filterOrders(OrderDetail::deliveredOrder());
        return view('seller.order.index', compact('pageTitle', 'orders', 'emptyMessage'));
    }

    public function orderDetails($orderID)
    {
        $shop_name = Shop::where('seller_id',seller()->id)->first()->name;
        $pageTitle      = 'Order Details';
        $curl = curl_init();   
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://softwaredevelopmentpro.myshopify.com/admin/api/2023-04/orders/'.$orderID.'.json',
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
        $pageTitle = 'Order Details';
        $order = $data->order;
        return view('seller.order.details', compact('order','pageTitle','shop_name'));
    }

    function filterOrders($data)
    {
        return $data->where('seller_id',seller()->id)
        ->with(['order','order.user','order.deposit.gateway'])
        ->orderBy('id', 'DESC')
        ->paginate(getPaginate());
    }

}
