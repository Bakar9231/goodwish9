@extends('admin_layouts/main')
@section('pageSpecificCss')
<style>
.small-text{
    font-size: 15px;
}
.card-icon2 {
    width: 50px;
    height: 50px;
    line-height: 50px;
    font-size: 22px;
    margin: 25px 65px;
    box-shadow: 5px 3px 10px 0 rgba(21,15,15,0.3);
    border-radius: 10px;
    background: #6777ef;
    text-align: center;
}
.card-icon2 i{
    font-size: 22px;
    color: #fff;
}
</style>
@stop
@section('content')

<section class="section">
    
    <div class="row ">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-dark">
                <i class="fas fa-users"></i>
                </div>
                <div class="card-wrap">
                <div class="padding-20">
                    <div class="text-right">
                    <h3 class="font-light mb-0">
                        <i class="ti-arrow-up text-success"></i>  <h2>{{$totalUser}}</h2>
                    </h3>
                    <a href="{{ url('/partner/users/list') }}"> <h5 class="font-15">Total Users</h5> </a>   
                    </div>
                </div>
                </div>
            </div>
        </div>
        
         <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-dark">
                <i class="fas fa-tv"></i>
                </div>
                <div class="card-wrap">
                <div class="padding-20">
                    <div class="text-right">
                    <h3 class="font-light mb-0">
                        <i class="ti-arrow-up text-success"></i>  <h2>{{$income}}</h2>
                    </h3>
                    <a href="{{ url('/genre/list')}}"> <h5 class="font-15">Total Income</h5> </a>
                    </div>
                </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-dark">
                <i class="fas fa-film"></i>
                </div>
                <div class="card-wrap">
                <div class="padding-20">
                    <div class="text-right">
                    <h3 class="font-light mb-0">
                        <i class="ti-arrow-up text-success"></i>  <h2>50</h2>
                    </h3>
                    <a href="{{ url('/partner/users/list') }}"><h5 class="font-15"> Paid</h5></a>
                    </div>
                </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-dark">
                <i class="fas fa-play"></i>
                </div>
                <div class="card-wrap">
                <div class="padding-20">
                    <div class="text-right">
                    <h3 class="font-light mb-0">
                        <i class="ti-arrow-up text-success"></i>  <h2>70</h2>
                    </h3>
                    <a href="{{ url('/partner/users/offers') }}"> <h5 class="font-15"> Unpaid</h5> </a>
                    </div>
                </div>
                </div>
            </div>
        </div>
        <!--<div class="col-lg-3 col-md-6 col-sm-6 col-12">-->
        <!--    <div class="card card-statistic-1">-->
        <!--        <div class="card-icon bg-dark">-->
        <!--        <i class="fas fa-tv"></i>-->
        <!--        </div>-->
        <!--        <div class="card-wrap">-->
        <!--        <div class="padding-20">-->
        <!--            <div class="text-right">-->
        <!--            <h3 class="font-light mb-0">-->
        <!--                <i class="ti-arrow-up text-success"></i>  <h2>{{$totalUser}}</h2>-->
        <!--            </h3>-->
        <!--            <a href="{{ url('/partner/users/list') }}"> <h5 class="font-15">Total Users</h5> </a>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    </div>
</section>

@endsection
@section('pageSpecificJs')
<script src="{{asset('assets/bundles/chartjs/chart.min.js')}}"></script>
<script src="{{asset('assets/dist/js/custom.js')}}"></script>
@stop