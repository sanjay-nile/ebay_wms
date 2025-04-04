@include('pages.frontend.client.breadcrumb', ['title' => 'All Returns'])

@push('css')
<link id="bsdp-css" href="{{ asset('plugins/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
@endpush

@push('js')
<script src="{{ asset('plugins/js/bootstrap-datepicker.min.js') }}"></script>
<script>
$(document).ready(function() {
    $('input[name="start"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd"});
    $('input[name="end"]').datepicker({autoclose: true,todayHighlight: true,format: "yyyy/mm/dd"});    

    $(".fiter-form").submit(function() {
        $(this).find(":input").filter(function(){ return !this.value; }).attr("disabled", "disabled");
        return true; // ensure form still submits
    });
});
</script>
<script>
$(document).ready(function() {
    $("#process-btn").click(function () {
        $("#refund-status").submit();
    });

    $("#select-all").click(function () {
        $('input:checkbox').not(this).prop('checked', this.checked);
    });

    $("#parcel-excel-btn").click(function () {
        $('#export_to').val('parcel-excel');
        $("#filter-frm").submit();
    });

    $("#item-excel-btn").click(function () {
        $('#export_to').val('item-excel');
        $("#filter-frm").submit();
    });

    $(".search-btn").click(function () {
        $('#export_to').val('');
    });
});
</script>
@endpush


@if ($client->client_type == '1')
    @include('pages.frontend.client.html.olive-return-order')
@elseif ($client->client_type == '2')
    @include('pages.frontend.client.html.missguided-return-order')
@elseif ($client->client_type == '4')
    @include('pages.frontend.client.html.curated-return-order')
@else
    @include('pages.frontend.client.html.normal-return-order')
@endif