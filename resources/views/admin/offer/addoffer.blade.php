@extends('admin_layouts/main')
@section('pageSpecificCss')
<link href="{{asset('assets/bundles/datatables/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/bundles/izitoast/css/iziToast.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/bundles/jquery-selectric/selectric.css')}}" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">

<style type="text/css">
  .custom-switch-input:checked~.custom-switch-indicator:before {
    left: calc(2rem + 1px);
  }

  .custom-switch-indicator:before {
    height: calc(2.25rem - 4px);
    width: calc(2.25rem - 4px);
  }

  .custom-switch-indicator {
    height: 2.25rem;
    width: 4.25rem;
  }


  .switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
  }

  .switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    -webkit-transition: .4s;
    transition: .4s;
  }

  .slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    -webkit-transition: .4s;
    transition: .4s;
  }

  input:checked+.slider {
    background-color: #2196F3;
  }

  input:focus+.slider {
    box-shadow: 0 0 1px #2196F3;
  }

  input:checked+.slider:before {
    -webkit-transform: translateX(26px);
    -ms-transform: translateX(26px);
    transform: translateX(26px);
  }

  /* Rounded sliders */
  .slider.round {
    border-radius: 34px;
  }

  .slider.round:before {
    border-radius: 50%;
  }
</style>
@stop
@section('content')
<section class="section">
  <div class="section-body">

    <div class="row">
      <div class="col-12 col-md-12 col-lg-12">

        <div class="card">
          <div class="card-body">

            <div class="card-body">
              @if(session()->has('message'))
              <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{session()->get('message')}}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              @endif
              <form class="forms-sample" method="post" enctype="multipart/form-data" action="{{route('saveoffer')}}">
                {{ csrf_field() }}

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="content_title">Content Title</label>
                    <input type="text" name="title" required class="form-control" id="content_title" placeholder="Content Title" value="">
                  </div>

                  <div class="form-group col-md-6">
                    <label for="content_type">Select Image</label>
                    <input type="file" name="file" required class="form-control">
                  </div>

                  <div class="form-group col-md-12">
                    <label for="content_title">Description</label>
                    <textarea name="description" id="summernote" required class="form-control"></textarea>
                  </div>



                </div>




            </div>


            <button type="submit" class="btn btn-primary mr-2 content_add">Submit</button>
            <a class="btn btn-light" href="{{route('offer/list')}}">Cancel</a>

            </form>
          </div>
        </div>
      </div>

    </div>
</section>

@endsection
@section('pageSpecificJs')
<script src="{{asset('assets/bundles/jquery-ui/jquery-ui.min.js')}}"></script>
<script src="{{asset('assets/bundles/izitoast/js/iziToast.min.js')}}"></script>
<script src="{{asset('assets/bundles/jquery-selectric/jquery.selectric.min.js')}}"></script>
<script src="{{asset('assets/bundles/jquery-steps/jquery.steps.min.js')}}"></script>
<script src="{{asset('assets/js/page/form-wizard.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script>
  $(document).ready(function() {
    $('#summernote').summernote();
    $(document).on('change', '#content_type', function() {
      var value = $(this).val();
      if (value == 2) {
        $(".is_series").show();
        $(".is_movie").hide();
        $(".trailer_div").attr('style', 'display:none;');
      } else {
        $(".is_movie").show();
        $(".is_series").hide();
        $(".trailer_div").attr('style', 'display:block;');
      }

    });

    $(document).on('change', '#verticle_poster', function() {
      imagesPreview(this, '#verticle_poster_preview', width = "300", height = "500");
    });

    $(document).on('change', '#horizontal_poster', function() {
      imagesPreview(this, '#horizontal_poster_preview', width = "730", height = "500");
    });

    var imagesPreview = function(input, placeToInsertImagePreview, width, height) {

      if (input.files) {
        var filesAmount = input.files.length;
        var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.jfif|\.webp)$/i;
        for (i = 0; i < filesAmount; i++) {

          if (!allowedExtensions.exec(input.value)) {
            iziToast.error({
              title: 'Error!',
              message: 'Please upload file having extensions .jpeg/.jpg/.png only.',
              position: 'topRight'
            });
            input.value = '';
            return false;
          } else {

            var reader = new FileReader();

            reader.onload = function(event) {
              $(placeToInsertImagePreview).html('<div class="borderwrap" data-href="' + event.target.result + '"><div class="filenameupload"><img src="' + event.target.result + '" width="' + width + '" height="' + height + '">  </div></div>');
            }

            reader.readAsDataURL(input.files[i]);
          }
        }
      }
    };
    $('select[name="genre_id[]"]').on('change', function() { // fires when the value changes
      $(this).valid(); // trigger validation on hidden select
    });
    $("#addUpdateContent").validate({
      ignore: ':hidden:not(select)',
      rules: {
        content_title: {
          required: true,
        },
        description: {
          required: true,
        },
        duration: {
          required: true,
        },
        ratings: {
          required: true,
        },
        release_year: {
          required: true,
        },
        language_id: {
          required: true,
        },
        trailer_url: {
          required: true,
        },
        "genre_id[]": {
          required: true,
        },
        verticle_poster: {
          required: {
            depends: function(element) {
              return ($('#content_id').val() == "")
            }
          },
        },
        horizontal_poster: {
          required: {
            depends: function(element) {
              return ($('#content_id').val() == "")
            }
          },
        },
      },
      messages: {
        content_title: {
          required: "Please Enter Content Title",
        },
        description: {
          required: "Please Enter Description",
        },
        duration: {
          required: "Please Enter Duration",
        },
        ratings: {
          required: "Please Enter Ratings",
        },
        release_year: {
          required: "Please Enter Release Year",
        },
        language_id: {
          required: "Please Select Language",
        },
        trailer_url: {
          required: "Please Enter Youtube Trailer Id",
        },
        "genre_id[]": {
          required: "Please Select Genre",
        },
        verticle_poster: {
          required: "Please Upload Verticle Poster",
        },
        horizontal_poster: {
          required: "Please Upload Horizontal Poster",
        },
      },
      errorPlacement: function(error, element) {
        // check if element has Selectric initialized on it
        var data = element.data('selectric');
        error.appendTo(data ? element.closest('.' + data.classes.wrapper).parent() : element.parent());
      }

    });

    $(document).on('submit', '#addUpdateContent', function(e) {
      e.preventDefault();
      if (user_type == 1) {
        var formdata = new FormData($("#addUpdateContent")[0]);
        formdata.append('is_notify', $("#is_notify").val());
        $('.loader').show();
        $.ajax({
          url: '{{ route("addUpdateContent") }}',
          type: 'POST',
          data: formdata,
          dataType: "json",
          contentType: false,
          cache: false,
          processData: false,
          success: function(data) {
            $('.loader').hide();
            if (data.success == 1) {
              if (data.flag == 1) {
                window.location.href = '{{ url("content/edit") }}' + "/" + data.content_type + "/" + data.content_id;
              } else {
                var url = '{{ url("movie/source/list/1") }}' + "/" + data.content_id;
                // window.location.href = '{{ route("content/list") }}';
                $('.is_movie_source').attr('href', '{{ url("movie/source/list/1") }}' + "/" + data.content_id);
                $('.is_series_source').attr('href', '{{ url("series/source/list/2") }}' + "/" + data.content_id);
                $('.is_cast').attr('href', '{{ url("movie/cast/list") }}' + "/" + data.content_id);
                $('.is_movie_subtitles').attr('href', '{{url("movie/subtitle/list/1") }}' + "/" + data.content_id);
                $('.is_series_subtitles').attr('href', '{{url("series/subtitle/list/2") }}' + "/" + data.content_id);
                $('.is_season').attr('href', '{{url("series/season/list") }}' + "/" + data.content_id);
              }
              iziToast.success({
                title: 'Success!',
                message: data.message,
                position: 'topRight'
              });
            } else {
              iziToast.error({
                title: 'Error!',
                message: data.message,
                position: 'topRight'
              });
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
          }
        });
      } else {
        iziToast.error({
          title: 'Error!',
          message: ' you are Tester ',
          position: 'topRight'
        });
      }
    });


  });
</script>

@endsection