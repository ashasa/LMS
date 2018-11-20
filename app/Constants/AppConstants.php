<?php
namespace App\Constants;

class AppConstants
{
    const SUPER_ADMIN_ROLE_ID = 1;
    const EMP_ROLE_ID = 3;
    const ADMIN_ROLE_ID = 2;
    const USER_DATE_FORMAT = 'dd/mm/yyyy';
    const USER_CARBON_FORMAT = 'd/m/Y';
    const DB_DATE_FORMAT = 'Y-m-d';
    const DB_CARBON_FORMAT = 'Y-m-d';

    const RequestStatusFailed = 0;
    const RequestStatusSuccess = 1;

    const RECORDS_PER_PAGE = 10;
}
