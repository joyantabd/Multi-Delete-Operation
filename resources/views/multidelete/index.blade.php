@extends('layouts.app')

@section('title','Web-Cam')

@push('css')

    <style type="text/css">
        #results {  background:#ccc; }
    </style>
@endpush

@section('content')
    <div class="content-wrapper">
        <br>
        <button type="button" name="create_record" id="create_record" data-toggle="modal"  class="btn btn-info float-right mb-2">
            <i class="fa fa-plus"></i> Add  Appointment</button>
        <br>
        <br>
        <div class="container-fluid">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="delete_table">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Action</th>
                        <th><button type="button" name="bulk_delete" id="bulk_delete" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@endsection



@push('scripts')
    <div id="formModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">New Image Capture</h4>
                </div>
                <div class="modal-body">
                    <span id="form_result"></span>
                    <form method="post" id="sample_form" class="form-horizontal" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label class="control-label col-md-3" >Name<big class="text-danger">*</big></label>
                            <div class="col-md-8">
                                <input type="text"  name="name" id="name" class="form-control" placeholder="Enter Name"  required/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3" >Address<big class="text-danger">*</big></label>
                            <div class="col-md-8">
                                <input type="text"  name="address" id="address" class="form-control" placeholder="Enter Address"  required/>
                            </div>
                        </div>



                        <div class="form-group" align="center">
                            <input type="hidden" name="action" id="action" />
                            <input type="hidden" name="hidden_id" id="hidden_id" />
                            <input type="submit" name="action_button" id="action_button" class="btn btn-success" value="Add" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="confirmModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-confirm">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="icon-box">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </div>
                    <h4 class="modal-title">Are you sure?</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Do you really want to delete these records? This process cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
                    <button type="button" name="ok_button" id="ok_button" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            $('#delete_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('multidelete.index') }}",
                },
                columns: [
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'address',
                        name: 'address',
                    },

                    {
                        data: 'action',
                        name: 'action',
                        orderable: false
                    },
                    {
                        data: 'checkbox',
                        orderable:false
                    }

                ]
            });

            $('#create_record').click(function () {
                $('.modal-title').text("Add New Record");
                $('#action_button').val("Add");
                $('#action').val("Add");
                $('#formModal').modal('show');
            });

            $('#sample_form').on('submit', function (event) {
                event.preventDefault();
                if ($('#action').val() == 'Add') {
                    $.ajax({
                        url: "{{ route('multidelete.store') }}",
                        method: "POST",
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData: false,
                        dataType: "json",
                        success: function (data) {
                            var html = '';
                            if (data.errors) {
                                html = '<div class="alert alert-danger">';
                                for (var count = 0; count < data.errors.length; count++) {
                                    html += '<p>' + data.errors[count] + '</p>';
                                }
                                html += '</div>';
                            }
                            if (data.success) {
                                html = '<div class="alert alert-success">' + data.success + '</div>';
                                $('#sample_form')[0].reset();
                                $('#delete_table').DataTable().ajax.reload();


                            }
                            $('#form_result').html(html);
                        }
                    })
                }

                if ($('#action').val() == "Edit") {
                    $.ajax({
                        url: "{{ route('multidelete.update') }}",
                        method: "POST",
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData: false,
                        dataType: "json",
                        success: function (data) {
                            var html = '';
                            if (data.errors) {
                                html = '<div class="alert alert-danger">';
                                for (var count = 0; count < data.errors.length; count++) {
                                    html += '<p>' + data.errors[count] + '</p>';
                                }
                                html += '</div>';
                            }
                            if (data.success) {
                                html = '<div class="alert alert-success">' + data.success + '</div>';
                                $('#sample_form')[0].reset();
                                $('#delete_table').DataTable().ajax.reload();


                            }
                            $('#form_result').html(html);
                        }
                    });
                }
            });

            $(document).on('click', '.edit', function () {
                var id = $(this).attr('id');
                $('#form_result').html('');
                $.ajax({
                    url: "multidelete/" + id + "/edit",
                    dataType: "json",
                    success: function (html) {
                        $('#name').val(html.data.name);
                        $('#address').val(html.data.address);
                        $('#hidden_id').val(html.data.id);
                        $('.modal-title').text("Edit Record");
                        $('#action_button').val("Edit");
                        $('#action').val("Edit");
                        $('.hide_in_edit').html('');
                        $('#formModal').modal('show');

                    }
                })
            });




            var joy_id;
            $(document).on('click', '.delete', function(){
                joy_id = $(this).attr('id');
                $('#confirmModal').modal('show');
            });
            $('#ok_button').click(function(){
                $.ajax({
                    url:"multidelete/destroy/"+joy_id,
                    beforeSend:function(){
                        $('#ok_button').text('Deleting...');
                    },
                    success:function(data)
                    {
                        setTimeout(function(){
                            $('#confirmModal').modal('hide');
                            $('#delete_table').DataTable().ajax.reload();
                        }, 2);
                    }
                })
            });

            $(document).on('click', '#bulk_delete', function(){
                var id = [];
                if(confirm("Are you sure you want to Delete this data?"))
                {

                    $('.multidelete_checkbox:checked').each(function(){

                        id.push($(this).val());
                    });
                    if(id.length > 0)
                    {
                        $.ajax({
                            url:"{{ route('multidelete.mass')}}",
                            method:"get",
                            data:{id:id},
                            success:function(data)
                            {

                                alert(data);
                                $('#delete_table').DataTable().ajax.reload();
                            }
                        });
                    }
                    else
                    {
                        alert("Please select at least one checkbox !!!");
                    }
                }
            });
        });


    </script>

@endpush

