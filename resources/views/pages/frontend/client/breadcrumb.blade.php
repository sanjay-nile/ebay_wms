{{-- <div class="we-page-title">
    <div class="row">
        <div class="col-md-8 align-self-left">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{!! route('front.client.dashboard') !!}"><i class="la la-home"></i> Home</a></li>
                <li class="breadcrumb-item active">{!! $title !!}</li>
            </ol>
        </div>
    </div>
</div> --}}

<div class="wrapper-header">
    <h4 class="text-center">{!! $title !!}</h4>
    <nav aria-label="breadcrumb mb-1">
        <ol class="breadcrumb breadcrumb-style1 mg-b-10">
            <li class="breadcrumb-item"><a href="{!! route('front.client.dashboard') !!}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">{!! $title !!}</li>
        </ol>
    </nav>
</div>