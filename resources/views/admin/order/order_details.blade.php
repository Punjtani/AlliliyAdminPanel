@extends('admin.layouts.app')

@section('panel')
    <div class="content-wrapper">
        <div class="container-fluid p-0">
            <div class="card">
                <div class="card-body">
                    <!-- Main content -->
                    <div class="invoice" id="invoice">
                        <!-- title row -->
                        <div class="row mt-3">
                            <div class="col-lg-6">
                                <h4><i class="fa fa-globe"></i> {{ __($general->sitename) }} </h4>
                            </div>
                            <div class="col-lg-6">
                                <h5 class="float-sm-right">{{ showDateTime($order->created_at, 'd/M/Y') }}</h5>
                            </div>
                        </div>
                        <hr>
                        <div class="row invoice-info">
                            <div class="col-md-4">
                                <h5 class="mb-2">@lang('User Details')</h5>
                                <address>
                                    <ul>
                                        <li>@lang('Name'): <strong>{{ @$order->customer->first_name }}
                                                {{ @$order->customer->last_name }}</strong></li>
                                        <li>@lang('Address'): {{ @$order->customer->default_address->address1 }}</li>
                                        <li>@lang('State'): {{ @$order->customer->state }}</li>
                                        <li>@lang('City'): {{ @$order->customer->default_address->city }}</li>
                                        <li>@lang('Zip'): {{ @$order->customer->default_address->zip }}</li>
                                        <li>@lang('Country'): {{ @$order->customer->default_address->country }}</li>
                                    </ul>

                                </address>
                            </div><!-- /.col -->
                            <div class="col-md-4">
                                <h5 class="mb-2">@lang('Shipping Address')</h5>
                                {{-- @php
                                $shipping_address = json_decode(@$order->shipping_address);
                            @endphp --}}
                                <address>
                                    <ul>
                                        <li>@lang('Name'): <strong>{{ @$order->shipping_address->first_name }}
                                                {{ @$order->shipping_address->last_name }}</strong></li>
                                        <li>@lang('Address'): {{ @$order->shipping_address->address1 }}</li>
                                        <li>@lang('State'): {{ @$order->shipping_address->province }}</li>
                                        <li>@lang('City'): {{ @$order->shipping_address->city }}</li>
                                        <li>@lang('Zip'): {{ @$order->shipping_address->zip }}</li>
                                        <li>@lang('Country'): {{ @$order->shipping_address->country }}</li>
                                    </ul>
                                </address>
                            </div><!-- /.col -->

                            <div class="col-md-4">
                                <b>@lang('Order ID'):</b> {{ @$order->order_number }}<br>
                                <b>@lang('Order Date'):</b> {{ showDateTime(@$order->created_at, 'd/m/Y') }} <br>
                                <b>@lang('Total Amount'):</b> {{ '$' . @$order->total_price }}
                            </div><!-- /.col -->
                        </div><!-- /.row -->
                        <!-- Table row -->

                        {{-- <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>@lang('SN.')</th>
                                    <th>@lang('Product')</th>
                                    <th>@lang('Variants')</th>
                                    <th>@lang('Discount')</th>
                                    <th>@lang('Quantity')</th>
                                    <th>@lang('Price')</th>
                                    <th>@lang('Total Price')</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $subtotal = 0;
                                    @endphp
                                    @foreach (@$order->orderDetail as $data)

                                    @php
                                    $details = json_decode($data->details);
                                    $offer_price = $details->offer_amount;
                                    $extra_price = 0;
                                    @endphp
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{$data->product->name}}</td>
                                        <td>
                                            @if ($details->variants)
                                            @foreach ($details->variants as $item)
                                               <span class="d-block">{{__($item->name)}} :  <b>{{__($item->value)}}</b></span>
                                               @php $extra_price += $item->price;  @endphp
                                            @endforeach
                                            @else
                                            @lang('N/A')
                                            @endif
                                        </td>
                                        @php $base_price = $data->base_price + $extra_price @endphp
                                        <td class="text-right">{{$general->cur_sym.getAmount($offer_price)}}/ @lang('Item')</td>
                                        <td class="text-center">{{$data->quantity}}</td>
                                        <td class="text-right">{{$general->cur_sym. ($data->base_price - getAmount($offer_price))}}</td>

                                        <td class="text-right">{{$general->cur_sym.getAmount(($base_price - $offer_price)*$data->quantity)}}</td>
                                        @php $subtotal += ($base_price - $offer_price) * $data->quantity @endphp
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div><!-- /.col -->
                    </div><!-- /.row --> --}}

                        <div class="row mt-4">
                            <!-- accepted payments column -->
                            <div class="col-lg-6">

                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th width="25%">@lang('Product Name')</td>
                                                <th width="25%">
                                                    @lang('Product Price')
                                                </td>
                                                <th width="25%">
                                                    @lang('Product Quantity')
                                                </td>
                                            </tr>
                                            @foreach ($order->line_items as $product)
                                                <tr>
                                                    <td>
                                                        {{ $product->name }}
                                                    </td>
                                                    <td>
                                                        {{ $product->price }}
                                                    </td>
                                                    <td>
                                                        {{ $product->quantity }}
                                                    </td>

                                                </tr>
                                            @endforeach


                                        </tbody>
                                    </table>
                                </div>



                            </div><!-- /.col -->
                            <div class="col-lg-6">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tbody>
                                            {{-- <tr>
                                            <th width="50%">@lang('Subtotal')</th>
                                            <td width="50%">{{@$general->cur_sym.getAmount(@$order->total_price, 2)}}</td>
                                        </tr> --}}
                                            {{-- @if (@$order->appliedCoupon)
                                        <tr>
                                            <th>(<i class="la la-minus"></i>) @lang('Coupon') ({{ @$order->appliedCoupon->coupon->coupon_code }})</th>
                                            <td> {{@$general->cur_sym.getAmount($order->appliedCoupon->amount, 2)}}</td>
                                        </tr>
                                        @endif --}}
                                            <tr>
                                                <th>@lang('Item Price')</th>
                                                <td>{{ @$order->total_line_items_price }}</td>
                                            </tr>
                                            <tr>
                                                <th>@lang('Shipping')</th>
                                                <td>{{ @$order->total_shipping_price_set->shop_money->amount }}</td>
                                            </tr>
                                            <tr>
                                                <th>@lang('Total Tax')</th>
                                                <td>{{ @$order->total_tax }}</td>
                                            </tr>
                                            <tr>
                                                <th>@lang('Total')</th>
                                                <td>{{ @$general->cur_sym . @$order->total_price }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div><!-- /.col -->
                        </div><!-- /.row -->
                        <!-- this row will not appear when printing -->
                    </div><!-- /.content -->
                    <hr>
                    <div class="no-print float-right">
                                <i
                                class="fa fa-print"></i>@lang('Print')

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
