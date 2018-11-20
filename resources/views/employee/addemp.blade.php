@extends('layouts.master')

@section('title', 'Add Employee')

@section('mastercontent')

<div class="row">
    <div class="col-lg-offset-3 col-lg-6 mt-2">
        <div class="panel panel-default">
            <div class="panel-heading">
                Add Employee
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">

                        <div id="divmsg" tabindex="-1"></div>

                        <form role="form" method="post" name="addempform" id="addempform">
                            @csrf
                            <div class="form-group">
                                <label>Employee name <span class="error">*</span></label>
                                <input class="form-control" type="text" name="empname" id="empname">
                            </div>

                            <div class="form-group">
                                <label>Employee code <span class="error">*</span></label>
                                <input class="form-control" type="text" name="empcode" id="empcode">
                            </div>
                            
                            <div class="form-group">
                                <label>Gender <span class="error">*</span></label>
                                <select class="form-control" type="text" name="gender" id="gender">
                                    <option value="">Select</option>
                                    @foreach ($mstrGender as $item)
                                        <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Designation <span class="error">*</span></label>
                                <select class="form-control" name="designation" id="designation">
                                    <option value="">Select</option>
                                    @foreach ($mstrDesigs as $item)
                                        <option value="{{ $item->pk_desig_id }}">{{ $item->designation_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Date of joining <span class="error">*</span></label>
                                <input class="form-control date-selector" type="text" name="doj" id="doj">
                            </div>
                            
                            <div class="form-group">
                                <label>Mobile number <span class="error">*</span></label>
                                <input class="form-control" type="text" name="mobilenum" id="mobilenum">
                            </div>

                            <div class="form-group">
                                <label>Address</label>
                                <textarea class="form-control" rows="3" name="address" id="address"></textarea>
                            </div>

                            <div class="form-group">
                                <label>E-mail <span class="error">*</span></label>
                                <input class="form-control" type="text" name="emailid" id="emailid">
                            </div>

                            <button type="button" class="btn btn-primary" name="btnsubmit" id="btnsubmit">Save</button>
                            <button type="reset" class="btn btn-default" name="btnreset" id="btnreset">Clear</button>
                        </form>
                    </div>
                </div>
                <!-- /.row (nested) -->
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->


@endsection

@section('css')

<link href="{{ asset('assets/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css"/>

@endsection

@section('js')

<script src="{{ asset('assets/jQuery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/jQuery-validation/additional-methods.min.js') }}"></script>
<script src="{{ asset('assets/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('assets/app-scripts/ajax-setup.js') }}"></script>

<script>

    $(document).ready(function(event){

        $('.date-selector').datepicker({
            format: '{{\App\Constants\AppConstants::USER_DATE_FORMAT}}',
            autoclose: true,
            startView: 1
        });

        var validatorAddEmpForm = $('#addempform').validate({
            rules: {
                empname: {required: true},
                empcode: {required: true},
                gender: {required: true},
                designation: {required: true},
                doj: {required: true},
                mobilenum: {required: true},
                emailid: {required: true}
	        },
	        messages: {
                empname: 'Please enter Employee name',
                empcode: 'Please enter Employee code',
                emailid: 'Please enter E-mail',
                emailid: 'The E-mail must be a valid email address',
                gender: 'Please select Gender',
                designation: 'Please select Designation',
                mobilenum: 'Please enter Mobile number',
                doj: 'Please enter Date of joining'
	        }
        });

        $('#btnsubmit').click(function(event){

            if($('#addempform').valid()) 
            {
                $.ajax({
                    url: '{{url('saveemp')}}',
                    method: 'post',
                    dataType: 'json',
                    data: $('#addempform').serialize(),
                    cache: false,
                    crossDomain: true,
                    success: function (response)
                    {
                        if( response.status === {{\App\Constants\AppConstants::RequestStatusSuccess}} )
                        {
                            $('#btnreset').trigger('click');
                            $('#divmsg').html('<div class="alert alert-success">' + response.message + '</div>');
                            $('#divmsg').focus();
                        }
                        else
                        {
                            $('#divmsg').html('<div class="alert alert-warning">' + response.message + '</div>');
                            $('#divmsg').focus();
                        }
                    },
                    error: function (data) {

                    }
                });
            }
        });
    });
</script>

@endsection