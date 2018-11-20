@extends('layouts.master')

@section('title', 'Employee list')

@section('mastercontent')

<div class="row">
    <div class="col-lg-12 mt-2">
        <div class="panel panel-default">
            <div class="panel-heading">
                Employees
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    
                    <div id="divmsg" tabindex="-1"></div>

                    <div class="col-lg-12 text-center">
                        {{ $allEmplyees->links() }}
                    </div>

                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Emp. Code</th>
                                <th>Name</th>
                                <th>E-mail</th>
                                <th>Mobile number</th>
                                <th>Role</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($allEmplyees as $item)
                                <tr>
                                    <td>{{ $item->employee_code }}</td>
                                    <td class='emp-name'>{{ $item->name }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>{{ $item->mobile_number }}</td>
                                    <td>{{ $item->role->role_name }}</td>
                                    <td>
                                        @php
                                            $newRoleName = '';
                                            $newRoleId = '';
                                            if($item->fk_role_id == \App\Constants\AppConstants::EMP_ROLE_ID)
                                            {
                                                $newRoleId = \App\Constants\AppConstants::ADMIN_ROLE_ID;
                                                $newRoleName = $mstrRoles[$newRoleId]->role_name;
                                            }
                                            elseif($item->fk_role_id == \App\Constants\AppConstants::ADMIN_ROLE_ID)
                                            {
                                                $newRoleId = \App\Constants\AppConstants::EMP_ROLE_ID;
                                                $newRoleName = $mstrRoles[$newRoleId]->role_name;
                                            }

                                            if(empty($newRoleName) === false && empty($newRoleId) === false) 
                                            {
                                        @endphp
                                                <a href="javascript:void(0)" class="change-role" data-userid="{{ $item->pk_user_id }}" data-roleid="{{ $newRoleId }}" data-rolename="{{ $newRoleName }}">Change role to {{ $newRoleName }}</a>
                                        @php
                                            }
                                        @endphp
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>

@endsection

@section('js')

<script src="{{ asset('assets/app-scripts/ajax-setup.js') }}"></script>
<script src="{{ asset('assets/bootstrap-confirmation/bootstrap-confirmation.min.js') }}"></script>

<script>

    $(document).ready(function(event){

        $('.change-role').each(function (index, element) {
            var el = $(element);
            console.log(el.parents('tr').find('td.emp-name'));
            el.confirmation('destroy');
            el.confirmation({
                rootSelector: '.change-role',
                singleton: true,
                popout: true,
                title: 'Are you sure to change the role of employee ' + el.parents('tr').find('td.emp-name').text() + ' to ' +  $(this).data('rolename') + '?',
                onConfirm: function (event) {
                    var userId = $(this).data('userid');
                    var roleId = $(this).data('roleid');
                    $.ajax({
                        url: '{{url('changerole')}}',
                        method: 'post',
                        dataType: 'json',
                        data: {
                            roleId: roleId,
                            userId: userId
                        },
                        cache: false,
                        crossDomain: true,
                        success: function (response)
                        {
                            if( response.status === {{\App\Constants\AppConstants::RequestStatusSuccess}} )
                            {
                                window.location.reload();
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

    });
</script>

@endsection