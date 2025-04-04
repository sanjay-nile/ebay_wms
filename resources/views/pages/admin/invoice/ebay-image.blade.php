<!doctype html>
<html>
<head>
    <title>Image Slide SHow</title>
    <script type="text/javascript" src="{{ URL::asset('public/js/jquery-3.4.1.min.js') }}"></script>
    <link rel="stylesheet" href="{{ URL::asset('public/css/owlcarousel/css/owl.carousel.min.css') }}" />
    <script type="text/javascript" src="{{ URL::asset('public/css/owlcarousel/js/owl.carousel.min.js') }}"></script>
    <script type="text/javascript">
        $( document ).ready(function() {
            $('#myowlSlides').owlCarousel({
                loop:false,
                margin:10,
                nav:true,
                dots:false,
                responsive:{
                    0:{
                        items:1 
                    },
                    600:{
                        items:1
                    },
                    1000:{
                        items:1
                    }
                }
            })
        });
        
    </script>
    <style type="text/css"> 
        .order_invoice-image-section {padding: 2rem; background: #eee; }
        .order_invoice-image{    position: relative; height: 700px; width: 700px; margin: 0 auto;}
        .order_invoice-image img {position: absolute; bottom: 0; left: 0; right: 0; top: 0; margin: auto; max-width: 100%; max-height: 100%; }

  

        #myowlSlides .owl-nav button.owl-prev span,
         #myowlSlides .owl-nav button.owl-next span{background: #000; width: 40px; height: 40px; display: inline-block; color: #fff; font-size: 30px; }
 
         #myowlSlides .owl-nav button.owl-prev {position: absolute; top: 50%; left: 0; }
         #myowlSlides .owl-nav button.owl-next {position: absolute; top: 50%; right: 0; }
 

        .QRcode .content h3 {font-weight: 600; font-size: 32px; color: #2c4964; }
        .QRcode .content ul {list-style: none; padding: 0; margin: 0px; }
        .QRcode .content ul li {padding-bottom: 10px; }
        .QRcode {padding: 120px 0; }
        .QRcode .content ul li:last-child{padding-bottom: 0px;}
        .QRcode-container{ width: 100%; margin: auto;}
        * {box-sizing:border-box; margin: 0; padding:0;}

        /* Slideshow container */
        .slideshow-container {
          max-width: 1000px;
          position: relative;
          margin: auto;
        }

        /* Hide the images by default */
        .mySlides {
          display: none;
        }

        /* Next & previous buttons */
        .prev, .next {
          cursor: pointer;
          position: absolute;
          top: 50%;
          width: auto;
          margin-top: -22px;
          padding: 16px;
          color: white;
          font-weight: bold;
          font-size: 18px;
          transition: 0.6s ease;
          border-radius: 0 3px 3px 0;
          user-select: none;
        }

        /* Position the "next button" to the right */
        .next {
          right: 0;
          border-radius: 3px 0 0 3px;
        }

        /* On hover, add a black background color with a little bit see-through */
        .prev:hover, .next:hover {
          background-color: rgba(0,0,0,0.8);
        }

        /* Caption text */
        .text {
          color: #f2f2f2;
          font-size: 15px;
          padding: 8px 12px;
          position: absolute;
          bottom: 8px;
          width: 100%;
          text-align: center;
        }

        /* Number text (1/3 etc) */
        .numbertext {
          color: #f2f2f2;
          font-size: 12px;
          padding: 8px 12px;
          position: absolute;
          top: 0;
        }

        /* The dots/bullets/indicators */
        .dot {
          cursor: pointer;
          height: 15px;
          width: 15px;
          margin: 0 2px;
          background-color: #bbb;
          border-radius: 50%;
          display: inline-block;
          transition: background-color 0.6s ease;
        }

        .active, .dot:hover {
          background-color: #717171;
        }

        /* Fading animation */
        .fade {
          animation-name: fade;
          animation-duration: 1.5s;
        }

        @keyframes fade {
          from {opacity: .4}
          to {opacity: 1}
        }
    </style>
</head>
<body id="order_invoice">

    <div class="order_invoice-image-section">
        <div id="myowlSlides" class="owl-carousel owl-theme">
            @php
                $item = json_decode($item_data->package_data);
            @endphp
            @if(isset($item->imageUrls) && !empty($item->imageUrls))
            @forelse($item->imageUrls as $k)
                <div class="item">
                    <div class="order_invoice-image"><img src="{{ $k }}"></div>
                </div>
            @empty
            @endforelse
            @endif
        </div>
    </div>
</body>
</html> 