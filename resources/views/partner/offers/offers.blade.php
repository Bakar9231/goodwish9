@extends('admin_layouts/main')
@section('pageSpecificCss')
<link href="{{asset('assets/bundles/datatables/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@stop
@section('content')
<section class="section">
  <div class="section-body">
    
  <div class="row ">
    @foreach($appUser as $detail)
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
          <div class="card">
            <img src="{{ URL::asset($detail->image) }}" class="card-img-top" alt="...">
            <div class="card-body">
              <h6>{{ $detail->title }}</h6>
              <p class="card-text">{{ $detail->description }}.</p>
            </div>
          </div>
        </div>
        @endforeach
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    </div>
    </div>
    
  </div>
</section>
@endsection

@section('pageSpecificJs')

<script src="{{asset('assets/bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/bundles/jquery-ui/jquery-ui.min.js')}}"></script>
<script src="{{asset('assets/js/page/datatables.js')}}"></script>
<script src="{{asset('assets/bundles/izitoast/js/iziToast.min.js')}}"></script>

<script>

</script>

@endsection
