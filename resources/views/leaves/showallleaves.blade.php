@extends('layouts.master')

@section('title', 'Leave Applications')

@section('mastercontent')

<div class="row">
    <div class="col-lg-offset-3 col-lg-6 mt-2">
        <div class="panel panel-default">
            <div class="panel-heading">
                Leaves Applications
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
                page: page,
                showall: 1
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
        
    });
</script>

@endsection