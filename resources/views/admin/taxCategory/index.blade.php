@extends('admin.layouts.app')
@section("breadcrumb")
<li class="breadcrumb-item active">Tax Category</li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tax Category List (<span id="tax_category_count">{{$tax_category_count}}</span>)</h3>
            </div>
            <div class="card-header mb-2">
                <div class="row">
                    <div class="input-group mb-3 col-md-4">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">Search:</span>
                        </div>
                        <input type="text" name="tax_category_search" id="search" class="form-control tax_category_search"
                            placeholder="Search.." autocomplete="off">
                    </div>
                    {{-- <div class="input-group mb-3 col-md-2">
                        <select name="is_active" id="is_active" class="form-control is_active">
                            <option selected value="">Select Status</option>
                            <option value="1">Active</option>
                            <option value="0">In Active</option>
                        </select>
                    </div> --}}
                    <div class="col-md-2">
                        <a type="button" class="btn refresh-btn mt-2 mt-md-0" style="font-size: 14px" id="reset"><i
                                class="fa fa-refresh" aria-hidden="true"></i></a>
                    </div>
                    <div class="col-md-6">
                        <a type="button" class="btn admin-btn mt-2 mt-md-0" id="addTaxCategory">Add New</a>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive">
                <table class="table" id="datatable_tax_category">
                    <thead class="thead-inverse">
                        <tr>
                            <th>Sr. No</th>
                            <th>Category Name</th>
                            <th>Tax Rate</th>
                            {{-- <th>Active Status</th> --}}
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addUpdateTaxCategory" autocomplete="off">
                    @csrf
                    <input type="hidden" value="" name="id" id="id">
                    <div class="form-group col-sm-12">
                        <label class="col-form-label">Tax Category Name</label>
                        <input type="text" name="tax_category_name" id="tax_category_name" class="form-control" placeholder="Enter Tax Category Name">
                    </div>
                    <div class="form-group col-sm-12">
                        <label class="col-form-label">Tax Rate</label>
                        <input type="text" name="tax_rate" id="tax_rate" onKeyPress="if(this.value.length==5) return false;" class="form-control numbersOnly" placeholder="Enter Tax Rate">
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="submitAddUpdate">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    $(function () {
        var table = $('#datatable_tax_category').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            searching: false,
            "aaSorting": [
                [0, "desc"]
            ],
            "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw" style="color:#f1c63a;"></i><span class="sr-only"></span> ',
            },
            contentType: 'application/json; charset=utf-8',
            ajax: {
                url: "{{route('listtaxCategory')}}",
                type: "POST",
                data: function (d) {
                    d.tax_category_search = $('.tax_category_search').val(),
                        d.is_active = $('.is_active').val(),
                        d._token = '{{csrf_token()}}'
                }
            },
            columns: [
                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'tax_category_name',
                    name: 'tax_category_name'
                },
                {
                    data: 'tax_rate',
                    name: 'tax_rate'
                },
                // {
                //     data: 'active_status',
                //     name: 'active_status'
                // },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $("#search").keyup(function () {
            $('#datatable_tax_category').DataTable().draw(true);
        });

        $("#is_active").change(function () {
            $('#datatable_tax_category').DataTable().draw(true);
        });
    });

    $('#addTaxCategory').on('click', function () {
        $('#addUpdateTaxCategory').trigger('reset');
        $('.modal-title').html('Add Tax Category');
        $('#id').val('');
        $('#tax_category_name').val('');
        $('#addModal').modal('show');
    });

    $('#reset').on('click', function () {
        $('#search').val('');
        $('#is_active').val('');
        $('#datatable_tax_category').DataTable().draw(true);
    });

    $('.numbersOnly').keypress(function(event)
    {
        if((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) 
        {
            event.preventDefault();
        }
    });

    $('#submitAddUpdate').click(function (event) {
        event.preventDefault()
        var myform = document.getElementById("addUpdateTaxCategory");
        var formData = new FormData(myform);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        window.swal({
            title: "Uploading...",
            text: "Please wait",
            imageUrl: "{{ asset('public/loader/ajaxloader.gif') }}",
            showConfirmButton: false,
            allowOutsideClick: false
        });
        $.ajax({
            type: "POST",
            url: "{{route('addUpdateTaxCategory')}}",
            data: formData,
            success: function (data) {
                if (data.status == 1) {
                    $('#addModal').modal('hide');
                    swal({
                        title: "Done!",
                        text: data.message,
                        type: "success"
                    }, function () {
                        document.getElementById("tax_category_count").innerHTML = data.tax_category_count;
                        $('#datatable_tax_category').DataTable().draw(true);
                    });
                } else {
                    swal("ERROR!", data.message, "error");
                }
            },
            error: function (data) {
                swal("ERROR!", data, "error");
                console.log(data);
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });

    $(document).on("click", ".edit", function () {
        var id = $(this).attr('data-id');
        $.ajax({
            url: "{{route('editTaxCategory')}}",
            type: "GET",
            data: {
                id: id
            },
            dataType: 'JSON',
            success: function (data) {
                $('.modal-title').html('Edit Tax Category');
                $('#id').val(data.id);
                $('#tax_category_name').val(data.tax_category_name);
                $('#tax_rate').val(data.tax_rate);
                $('#addModal').modal("show");
            }
        });
    });

    $(document).on('click', '.delete', function () {
        var id = $(this).attr('data-id');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this tax category!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: false
        }, function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: "{{route('deleteTaxCategory')}}",
                data: {
                    id: id
                },
                dataType: 'JSON',
                success: function (data) {
                    swal({
                        title: "Deleted!",
                        text: "Tax category has been deleted.",
                        type: "success"
                    }, function () {
                        document.getElementById("tax_category_count").innerHTML = data.tax_category_count;
                        $('#datatable_tax_category').DataTable().draw(true);
                    });
                }
            });
        });
    });

    $(document).on('click', '#taxCategoryChangeActiveStatus', function () {
        var id = $(this).attr('data-id');
        var status = $(this).attr('data-status');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: "{{route('taxCategoryChangeActiveStatus')}}",
            data: {
                "id": id,
                "status": status
            },
            success: function (data) {
                if (data.status == 1) {
                    iziToast.success({
                        title: 'Success!',
                        message: data.message,
                        position: 'topRight'
                    });
                    $('#datatable_tax_category').DataTable().draw(true);
                } else {
                    iziToast.error({
                        title: 'Error!',
                        message: data.message,
                        position: 'topRight'
                    });
                }
            },
            error: function (data) {
                swal("ERROR!", data, "error");
                console.log(data);
            }
        });
    });

</script>
@endsection
