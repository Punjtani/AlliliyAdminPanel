@extends($activeTemplate.'layouts.frontend')
@section('content')

    <main class="banner-body bg--section">
        <div class="container">
            <div class="banner-section overflow-hidden">
                @include($activeTemplate.'partials.left_category_menu')
                @include($activeTemplate.'sections.banner_sliders')
                @include($activeTemplate.'sections.banner_promotional')
            </div>
        </div>
        @include($activeTemplate.'sections.banner_categories')
    </main>

    @include($activeTemplate.'sections.invite')
    @if ($offers->count() > 0)
     @include($activeTemplate.'sections.offers')
    @endif
    @if ($featuredProducts->count() > 0)
      @include($activeTemplate.'sections.featured_products')
    @endif
    @if($latestProducts->count() > 0)
      @include($activeTemplate.'sections.latest_products')
    @endif
    @if ($featuredSeller->count() > 0)
     @include($activeTemplate.'sections.featured_seller')
    @endif
    @include($activeTemplate.'sections.invite_seller')
    @if ($topBrands->count() > 0)
      @include($activeTemplate.'sections.brands')
    @endif
    @if ($topSellingProducts->count() > 0)
      @include($activeTemplate.'sections.trending_products')
    @endif
    @include($activeTemplate.'sections.subscribe')
@endsection

@push('script')
    <script>
        'use strict';
        (function($){
            $(document).on('click','.subscribe-btn' , function(){
                var email = $('input[name="email"]').val();
                $.ajax({
                    headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                    url:"{{ route('subscribe') }}",
                    method:"POST",
                    data:{email:email},
                    success:function(response)
                    {
                        if(!response.success) {
                            notify('success', response.success);
                        }else{
                            notify('error', response);
                        }
                    }
                });
            });
        })(jQuery)
    </script>
@endpush
