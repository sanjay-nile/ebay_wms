@extends('layouts.admin.layout')

@push('css')
<style type="text/css">
   button.btn-Search-1 {
    border: none;
    position: relative;
    padding: 8px 10px;
    background: #34bb63;
    margin-top: 20px;
    border-radius: 5px;
    color: #fff;
    font-weight: 600;
    font-size: 14px;
    width: 100%;
}

.ShowLiveData-btn{
    border: none;
    position: relative;
    padding: 8px 10px;
    background: #3d2a67;
    margin-top: 20px;
    border-radius: 5px;
    color: #fff;
    font-weight: 600;
    font-size: 14px;
    width: 100%;
}

.btnExport {
    border: none;
    position: relative;
    padding: 8px 10px;
    background: #34bb63;
    margin-top: 20px;
    border-radius: 5px;
    color: #fff;
    font-weight: 600;
    font-size: 14px;
    width: 100%;
}

a.btnExport {
    border: none;
    position: relative;
    padding: 9px 10px;
    background: #3d2a67;
    margin-top: 20px;
    border-radius: 5px;
    color: #fff;
    font-weight: 600;
    font-size: 14px;
    width: 100%;
}

.order-text h2 {
    color: #605082;
}

.overview-section .card-box {
    min-height: 206px;
}

</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="we-page-title">
                <div class="row">
                    <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
                        <div class="row breadcrumbs-top d-inline-block">
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                    <li class="breadcrumb-item active">Dashboard</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <section class="overview-section">
                <div class="card rack-info-box">
                    <div class="card-header">
                        <h5 class="card-title">Live Detailed Data Dashboard <span id="shw" style="color: red;"></span></h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-row align-items-center">
                                    <div class="form-group col-md-4">
                                        <label>Search by Date (Default Display Today)</label>
                                        <input type="text" name="from_date" id="start_date" value="" class="form-control dt" placeholder="From Date">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>&nbsp;</label>
                                        <input type="text" name="to_date" id="end_date" value="" class="form-control dt" placeholder="To Date">
                                    </div>
                                    <div class="form-group col-md-1">
                                        <button type="button" class="btn-Search-1" id="search-btn">Search</button>
                                    </div>
                                    <div class="form-group col-md-1 mt-2">
                                        <a href="{{ route('admin.graph.dashboard') }}" class="btnExport">Reset</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 col-sm-12 col-lg-12">
                                <canvas id="userChart"></canvas>
                            </div>

                            <div class="col-md-12 col-sm-12 col-lg-12 mt-2">
                                <div class="table-responsive">
                                    <div class="tableorder" id="op-div"></div> 
                                </div>   
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.dt').datepicker({autoclose: true,todayHighlight: true,format: "yyyy-mm-dd", orientation: "bottom left"});

        function fetchData(page = 1, live_Data = '') {
            let startDate = $("#start_date").val();
            let endDate = $("#end_date").val();

            $.ajax({
                url: "{{ route('admin.live.graph.dashboard') }}", // Change to your Laravel route
                type: "get",
                data : {
                    page: page,
                    start_date: startDate,
                    end_date: endDate,
                    dt_type: live_Data
                },
                dataType: 'json',
                beforeSend : function(){
                    $('#shw').html(`Sending.. <i class="la la-spinner la-spin"></i>`);
                },
                success: function(response) {
                    $('#shw').html(' ');
                    $('#op-div').html(response.opreator);
                    // console.log(response);
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: " + error);
                }
            });
        }

        // Call fetchData immediately on page load
        fetchData();

        // Set interval to call fetchData every 1 minute (60000 ms)
        setInterval(fetchData, 60000);

        // Handle pagination link clicks
        $(document).on("click", ".pagination a", function (event) {
            event.preventDefault();
            let page = $(this).attr("href").split("page=")[1];
            fetchData(page);
        });

        // Handle pagination link clicks
        $(document).on("click", "#search-btn", function (event) {
            fetchData(1);
            // Fetch data initially
            fetchChartData();
        });

        // Handle pagination link clicks
        $(document).on("click", "#live-data-btn", function (event) {
            fetchData(1, 'live');
        });
    });

    let userChart;
    function fetchChartData() {
        let startDate = $("#start_date").val();
        let endDate = $("#end_date").val();

        // Build query string
        let queryParams = new URLSearchParams();
        if (startDate) queryParams.append('start_date', startDate);
        if (endDate) queryParams.append('end_date', endDate);

        fetch(`chart-data?${queryParams.toString()}`)
        .then(response => response.json())
        .then(data => {
            // Define colors based on value
            const backgroundColors = data.data.map(value => {
                if (value <= 50) return 'rgba(255, 99, 132, 0.6)';  // Low (Red)
                if (value > 50 && value <= 150) return 'rgba(255, 206, 86, 0.6)'; // Middle (Yellow)
                return 'rgba(75, 192, 192, 0.6)';  // High (Green)
            });

            if (userChart) {
                userChart.data.labels = data.labels;
                userChart.data.datasets[0].data = data.data;
                userChart.data.datasets[0].backgroundColor = backgroundColors;
                userChart.update();  // Update chart with new data
            } else {
                const ctx = document.getElementById('userChart').getContext('2d');
                userChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Total Item Processed ',
                            data: data.data,
                            backgroundColor: backgroundColors
                        }]
                    }
                });
            }
        });
    }

    // Fetch data initially
    fetchChartData();

    // Refresh chart data every minute (60000 ms)
    setInterval(fetchChartData, 60000);
</script>
@endpush