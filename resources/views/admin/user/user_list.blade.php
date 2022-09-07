@extends('admin_layouts/main')
@section('pageSpecificCss')
<!--<link href="{{asset('assets/bundles/datatables/datatables.min.css')}}" rel="stylesheet">-->
<link href="{{asset('assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/bundles/summernote/summernote-bs4.css')}}" rel="stylesheet">
<link href="{{asset('assets/bundles/izitoast/css/iziToast.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/bundles/datatables/data.min.css')}}" rel="stylesheet">
<!--<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.css" rel="stylesheet">-->
<style>
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
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4 class="m-0">App User (<span class="total_partners">
                {{ $total_user }}
              </span>)</h4>
          </div>

          <div class="card-body">
            <!--<div class="pull-right">-->
            <!--    <div class="buttons"> -->
            <!--        <button class="btn btn-primary text-light" data-toggle="modal" data-target="#partnerModal" data-whatever="@mdo" >Add Partner</button>-->
            <!--    </div>-->
            <!--</div>-->
            <div class="table-responsive">

              <table id="partner" style="width:100%" class="table table-striped table-sm display">
                <!--<div class="pull-right">-->
                  <!--<div class="buttons  pull-right-1">-->
                  <!--  <button class="btn btn-primary text-light" data-toggle="modal" data-target="#partnerModal" data-whatever="@mdo">Add Partner</button>-->
                  <!--</div>-->
                <!--</div>-->
                <thead>
                  <tr>
                    <th>Profile Image</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th style=" padding-right:100px";>Phone</th>
                    <th>Date</th>
                     <th>Referal Code</th>
                     <th>Pack</th>
                    <th style="text-align: center">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($appUser as $detail)
                  <tr>
                    <td><img height="60" width="60" src="{{ URL::asset($detail->profileImg) }}"></td>
                    <td>{{ $detail->fname }}</td>
                    <td>{{ $detail->lname }}</td> 
                    <td>{{ $detail->email }}</td>
                    <td>{{ $detail->mobile }}</td>
                    <td>{{ $detail->date }}</td>
                    <td>{{ $detail->referral_code}}</td>
                    <td>{{ $detail->pack}}</td>
                  
                    <td style="text-align: center">
                      <a onClick="editPartnerModal('<?php echo $detail->id; ?>')"><i class="i-cl-3 fa fa-edit col-blue font-20 pointer p-l-5 p-r-5"></i></a><br>
                      <a data-id="{{$detail->id}}" class="delete DeletePartner"><i class="fa fa-trash text-danger font-20 pointer p-l-5 p-r-5"></i></a>
                    </td>
                  </tr>
                  @endforeach

                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="modal fade" id="partnerModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ModalLabel"> Add Partner </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="addUpdatePartner" method="post" enctype="multipart">
        {{ csrf_field() }}
        <div class="modal-body">
          <div class="form-group">
            <label for="name">Partner Name</label>
            <input id="name" name="name" type="text" class="form-control form-control-danger" placeholder="Enter Partner Name">
          </div>

          <div class="form-group">
            <div class="input-group">
              <input id="referral_income_percentage" name="referral_income_percentage" type="text" class="form-control form-control-danger" placeholder="Enter Referral Percentage" min="10" max="100" onkeypress="return onlyNumberKey(event)">
              <label for="referral_income_percentage" style="flex: 0.1 !important; font-size: large;" class="form-control form-control">%</label>
            </div>
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" class="form-control form-control-danger" placeholder="Enter Partner Email">
          </div>

          <div class="form-group">
            <label for="mobile">Partner Mobile</label>
            <input id="mobile" name="mobile" type="text" class="form-control form-control-danger" onkeypress="return onlyNumberKey(event)" placeholder="Enter Partner Mobile Number">
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" class="form-control form-control-danger" placeholder="Enter Password">
          </div>

          <div class="form-group">
            <input id="referral_code" name="referral_code" type="hidden" value="<?php echo ("ref" . rand(1, 20) . date('d') . rand(1, 20)) ?>" class="form-control form-control-sm">
            <input id="referral_link" name="referral_link" type="hidden" value="https://goodwish9.com/partner/" class="form-control form-control-sm">
          </div>

          <div class="form-group">
            <input id="unique_key" name="unique_key" type="hidden" value="flix!123" class="form-control form-control-danger">
          </div>

          <div class="form-group">
            <label for="actorprofile">Profile Image</label>
            <input type="file" class="form-control-file file-upload custom_image valid" id="profile_image" name="profile_image" aria-required="true" aria-invalid="false">
            <label id="actor_image-error" class="error image_error" for="profile_image" style="display: none;"></label>
            <div class="preview_profileimg mt-4"></div>
          </div>

        </div>
        <div class="modal-footer">
          <input type="hidden" name="id" id="id" value="">
          <input type="hidden" name="role" id="id" value="partner">
          <button type="submit" class="btn btn-success">Save</button>
          <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="partnerEditModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ModalLabel"> Update User </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="updatepartner" method="post" enctype="multipart">
        {{ csrf_field() }}
        <div class="modal-body">
          <div class="form-group">
            <label for="name">Partner First Name</label>
            <input id="upd-fname" name="firstname" type="text" class="form-control form-control-danger" >
          </div>

          <div class="form-group">
            <label for="name">Partner last Name</label>
            <input id="upd-lname" name="lastname" type="text" class="form-control form-control-danger" >
          </div>

          <div class="form-group">
            <label for="name">Partner Email</label>
            <input id="upd-email" name="email" type="email" class="form-control form-control-danger" >
          </div>

          <div class="form-group">
            <label for="name">Partner Phone</label>
            <input id="upd-phone" name="phone" type="text" class="form-control form-control-danger" >
          </div>

     
          <div class="form-group">
            <label for="password">Password</label>
            <input id="upd-password" autocomplete="off" name="password" type="password" class="form-control form-control-danger" placeholder="Enter Password">
          </div>

      

          <div class="form-group">
            <input id="unique_key" name="unique_key" type="hidden" value="flix!123" class="form-control form-control-danger">
          </div>

          <!-- <div class="form-group">
            <label for="actorprofile">Profile Image</label>
            <input type="file" class="form-control-file file-upload custom_image valid" id="profile_image" name="profile_image" aria-required="true" aria-invalid="false">
            <label id="actor_image-error" class="error image_error" for="profile_image" style="display: none;"></label>
            <div class="preview_profileimg mt-4"></div>
            <img id="upd-img-1" style="display:none">
          </div> -->

        </div>
        <div class="modal-footer">
          <input type="hidden" name="id" id="upd-id">
          <button type="submit" class="btn btn-success">Submit</button>
          <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('pageSpecificJs')

<script src="{{asset('assets/bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/bundles/jquery-ui/jquery-ui.min.js')}}"></script>
<script src="{{asset('assets/js/page/datatables.js')}}"></script>
<script src="{{asset('assets/bundles/izitoast/js/iziToast.min.js')}}"></script>
<script src="{{asset('assets/bundles/summernote/summernote-bs4.js')}}"></script>

<!-- Javascript for responsive collapsable data Tables -->
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.colVis.min.js"></script>
<script src="{{asset('assets/bundles/datatables/export-tables/dataTables.buttons.min.js')}}"></script>

<script>
  function editPartnerModal(id) {
    
    $.ajax({
      type: "GET",
      url: "{{route('getuserdetail')}}",
      data: {
        id: id
      },
      // add another line here to convert response text i.e data to json format, server will send response text in string to do so,
      dataType: 'json',
      success: function(data) {
        if (data.success == true) {
 
          $("#upd-fname").val(data.partner.firstname);
          $("#upd-lname").val(data.partner.lastname);
          $("#upd-email").val(data.partner.email);
          $("#upd-phone").val(data.partner.phone);
          $("#upd-id").val(data.partner.user_id);
          $("#partnerEditModal").modal('show');
        }


      }
    });
  }
</script>

<script>
  $(document).ready(function() {
    $("table.display").DataTable({
      responsive: {
        details: {
          type: 'column',
          target: 0
        }
      },
      dom: 'Bflrtip',
      "paging": true,
      language: {
        search: '',
        searchPlaceholder: "Search"
      },
      columnDefs: [{
          responsivePriority: 1,
          targets: 0
        },
        {
          responsivePriority: 2,
          targets: -1
        },
        {
          orderable: false,
          targets: 0
        },
        {
          className: 'dtr-control',
          orderable: false,
          targets: 0
        }
      ],
    });

  })
  $(document).ready(function() {

    $(document).on('change', '#profile_image', function() {
      CheckFileExtention(this, 'preview_profileimg');
    });

    var CheckFileExtention = function(input, cl) {

      if (input.files) {
        var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
        if (!allowedExtensions.exec(input.value)) {
          iziToast.error({
            title: 'Error!',
            message: 'Please upload file having extensions .jpeg/.jpg/.png only.',
            position: 'topRight'
          });
          input.value = '';
          return false;
        } else {
          if (cl.length > 0) {
            var reader = new FileReader();

            reader.onload = function(e) {
              $('.' + cl).html('<div class=""><img src="' + e.target.result + '" width="150" height="150"/> </div>');
            }

            reader.readAsDataURL(input.files[0]);
          }
        }
      }
    };



    $("#addUpdatePartner").validate({
      rules: {
        name: {
          required: true,
          remote: {
            url: '{{ route("CheckExistPartner") }}',
            type: "post",
            data: {
              name: function() {
                return $("#name").val();
              },
              username: function() {
                return $("#username").val();
              },
              id: function() {
                return $("#id").val();
              },
            }
          }
        },
      },
      messages: {
        name: {
          required: "Please Enter Actor Name",
          remote: "Partner Name or Username Already Exist.",
        },

      }
    });

    // $('#partnerModal').on('hidden.bs.modal', function(e) {
    //     $("#addUpdatePartner")[0].reset();
    //     $('.modal-title').text('Add Partner');
    //     $('#id').val("");
    //     $('#about_actor').summernote("code", "");
    //     $('.preview_profileimg').html("");
    //     var validator = $("#addUpdatePartner").validate();
    //     validator.resetForm();
    // });

    // $("#partner-listing").on("click", ".UpdatePartner", function() {
    //     $('.modal-title').text('Update');
    //     $('#id').val($(this).attr('data-id'));
    //     $('#name').val($(this).attr('data-name'));
    //     $('#username').val($(this).attr('data-username'));
    //     $('#email').val($(this).attr('data-email'));
    //     $('#password').val($(this).attr('data-password'));
    //     $('#mobile').val($(this).attr('data-mobile'));
    //     $('#referral_code').val($(this).attr('data-referral_code'));
    //     $('#unique_key').val($(this).attr('data-unique_key'));


    //     var image = $(this).attr('data-image');
    //     $('.preview_profileimg').html('<div class=""><img src="'+image+'" width="150" height="150"/> </div>');
    // });

    $(document).on('submit', '#updatepartner', function(e) {
      e.preventDefault();
      let user_type = 1;
      let referralCode = $('#referral_code');
      let referralLink = $('#referral_link');
      let generatedRefLink = referralLink.val() + referralCode.val();
      referralLink.val(generatedRefLink);
      console.log(generatedRefLink);

      if (user_type == 1) {
        var formdata = new FormData($("#updatepartner")[0]);
        $('.loader').show();
        $.ajax({
          url: '{{ route("updateappuser") }}',
          type: 'POST',
          data: formdata,
          dataType: "json",
          contentType: false,
          cache: false,
          processData: false,
          success: function(data) {
            $('.loader').hide();
            $('#partnerModal').modal('hide');
            if (data.success == 1) {
              $('.total_partners').text(data.total_partner);
              iziToast.success({
                title: 'Success!',
                message: data.message,
                position: 'topRight'
              });
              window.location.href = '';
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
          message: ' Something went wrong',
          position: 'topRight'
        });
      }
    });

    $("input[name='percentage']").on('input', function() {
      $(this).val(function(i, v) {
        return v.replace('%', '') + '%';
      });
    });

  });

  function updateStatus(status,id) {
    var status = status;
    var id = id;
      if (confirm("Are you sure to update partner status?") == true) {

        $('.loader').show();
        $.ajax({
          type: 'POST',
          url: '{{ route("updatePartnerStatus") }}',
          dataType: 'json',
          data: {
            "_token": "{{ csrf_token() }}",
            id: id,
            status: status
          },
        }).done(function(response) {
          $('.loader').hide();
          if (data.success == 1) {
            $('#partnerStatus').val(data.status);
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

        }).fail(function(error) {
          console.log(error);
          alert('Something went wrong..Check browser console');
        }); // Ajax Call

      } else {
        var switchStatus = false;
        $("#partnerStatus").on('change', function() {
          if ($(this).is(':checked')) {
            switchStatus = $(this).is(':checked');
            alert(switchStatus); // To verify
          } else {
            switchStatus = $(this).is(':checked');
            alert(switchStatus); // To verify
          }
        });
      }

    
  }

  function onlyNumberKey(evt) {

    // Only ASCII character in that range allowed
    var ASCIICode = (evt.which) ? evt.which : evt.keyCode
    if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
      return false;
    return true;
  }

  function editCityModal(maps) {
    document.getElementById('id').value = maps.id;
    document.getElementById('name').value = maps.name;
    document.getElementById('mobile').value = maps.mobile;
  }
</script>

<script>
  $(document).on('click', '.delete', function(e) {
    e.preventDefault();
    var id = $(this).data('id');
    var parent = $(this).parent().parent();


    swal({
        title: "Are you sure?",
        text: "",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "No, cancel please!",
        closeOnConfirm: false,
        closeOnCancel: false
      },
      function(isConfirm) {
        if (isConfirm) {
          //console.log("sdsd");

          $.ajax({
            type: "POST",
            url: "{{route('deleteappuser')}}",

            data: {
              '_token': "{{csrf_token()}}",
              id: id
            },
            success: function(data) {
              if (data.success == true) {
                swal("Partner Deleted Succfully", "", "success");
                parent.fadeOut('slow', function() {
                  $(this).remove();
                });
              }


            }
          }); // submitting the form when user press yes
        } else {
          swal("Cancelled", "Your record is safe :)", "error");
        }
      });

  });
</script>

@endsection