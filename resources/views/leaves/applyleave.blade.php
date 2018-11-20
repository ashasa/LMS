@extends('layouts.master')

@section('title', 'Apply for Leave')

@section('mastercontent')

<div class="row">
    <div class="col-lg-offset-3 col-lg-6 mt-2">
        <div class="panel panel-default">
            <div class="panel-heading">
                    Apply for Leave
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">

                        <div id="divmsg" tabindex="-1"></div>

                        <form role="form" method="post" name="leaveform" id="leaveform">
                            @csrf
                            <div class="form-group">
                                <label>From date <span class="error">*</span></label>
                                <input class="form-control date-selector" type="text" name="fromdate" id="fromdate">
                            </div>

                            <div class="form-group">
                                <label>To date <span class="error">*</span></label>
                                <input class="form-control date-selector" type="text" name="todate" id="todate">
                            </div>
                           
                            <div class="form-group">
                                <label>Reason for leave <span class="error">*</span></label>
                                <textarea class="form-control" rows="3" name="reason" id="reason"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Backup Employee <span class="error">*</span></label>
                                <select class="form-control" name="otheremp" id="otheremp">
                                    <option value="">Select</option>
                                    @foreach ($otherEmpls as $item)
                                        <option value="{{ $item->pk_user_id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
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

<div class="row">
    <div class="col-lg-offset-3 col-lg-6 mt-2">
        <div class="panel panel-default">
            <div class="panel-heading">
                Leaves applied
            </div>
            <div class="panel-body">
                <div class="table-responsive" id="divLeaveList">

                </div>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>

@endsection

@section('css')

<link href="{{ asset('assets/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css"/>

@endsection

@section('js')

<script src="{{ asset('assets/jQuery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/jQuery-validation/additional-methods.min.js') }}"></script>
<script src="{{ asset('assets/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('assets/app-scripts/ajax-setup.js') }}"></script>
<script src="{{ asset('assets/app-scripts/validator-settings.js') }}"></script>

<script>

    function updateLeaveList(page)
    {
        $.ajax({
            url: '{{url('getleaves')}}',
            method: 'post',
            dataType: 'json',
            data: {
                page: page
            },
            cache: false,
            crossDomain: true,
            success: function (response)
            {
                if( response.status === {{\App\Constants\AppConstants::RequestStatusSuccess}} )
                {
                    $('#divLeaveList').html(response.data);
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

    $(document).ready(function(event){

        updateLeaveList(1);

        $(document).on('click', '.pagination a',function(event)
		{
			event.preventDefault();

			$('li').removeClass('active');
			$(this).parent('li').addClass('active');

			var url = $(this).attr('href');
			var page = url.split('page=')[1];
			
			updateLeaveList(page);
		});

        $('.date-selector').datepicker({
            format: '{{\App\Constants\AppConstants::USER_DATE_FORMAT}}',
            autoclose: true,
            startView: 1
        });

        var validatorAddEmpForm = $('#leaveform').validate({
            rules: {
                fromdate: {required: true},
                todate: {
                    required: true,
                    greaterThanEqualToDMY: '#fromdate'
                },
                reason: {required: true},
                otheremp: {required: true}
	        },
	        messages: {
                fromdate: 'Please enter From date',
                todate: {
                    required: 'Please enter To date',
                    greaterThanEqualToDMY: 'To date must be greater than From date'
                },
                reason: 'Please enter reason',
                otheremp: 'Please select Backup Employee'
	        }
        });

        $('#btnsubmit').click(function(event){

            if($('#leaveform').valid()) 
            {
                $.ajax({
                    url: '{{url('saveleave')}}',
                    method: 'post',
                    dataType: 'json',
                    data: $('#leaveform').serialize(),
                    cache: false,
                    crossDomain: true,
                    success: function (response)
                    {
                        if( response.status === {{\App\Constants\AppConstants::RequestStatusSuccess}} )
                        {
                            updateLeaveList(1);
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