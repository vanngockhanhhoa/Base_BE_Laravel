<?php
define('CODE_SUCCESS', 200);
define('CODE_CREATE_FAILED', 201);
define('CODE_DELETE_FAILED', 202);
define('CODE_MULTI_STATUS', 207);
define('CODE_NO_ACCESS', 403);
define('CODE_NOT_FOUND', 404);
define('CODE_ERROR_SERVER', 500);
define('CODE_UNAUTHORIZED', 401);

//Validate
define('V_VARCHAR_MAXLENGTH', '255');
define('V_VARCHAR_REQUIRED', 'required|max:' . V_VARCHAR_MAXLENGTH);
define('V_EMAIL_REQUIRED', 'required|email|max:' . V_VARCHAR_MAXLENGTH);
define('V_PASSWORD', 'required|max:20|min:8');

//Date Time Format
define('DATE_FORMAT', 'Y-m-d');
define('DATE_PICKER_FORMAT', 'Y/m/d');
define('DATE_EXPORT_FORMAT', 'Ymd');

// USER - max number login fail
define('MAX_LOGIN_FAIL', 10);

// Account role
define('ROLE_ADMIN', 1);

//TimeLock
define('TIME_LOCK_LOGIN', 1);
define('TIME_LOCK_EMAIL', 1);

// Account status
define('ACCOUNT_STATUS', [
    'ACTIVE' => 1,
    'INACTIVE' => 0
]);

// Room sale staus
const ROOM_SALE_STATUS = [
    'ON' => 'on',
    'OFF' => 'off'
];

// Rate status
const RATE_STATUS = [
    'ON_SALE' => 'on_sale',
    'STOPPED' => 'stopped'
];

// Transmission log process
const TRANSMISSION_LOG_PROCESS = [
    'FROM_TLL' => 'from_tll',
    'FROM_RS' => 'from_rs'
];

// Transmisstion log status
const TRANSMISSION_LOG_STATUS = [
    'NOT_DONE' => 'not_done',
    'IN_PROGRESS' => 'in_progress',
    'COMPLETED' => 'completed',
    'ERROR' => 'error'
];

// Transmisstion log endpoint
const TRANSMISSION_LOG_ENDPOINT = [
    'UPDATE_RATE' => 'update_rate',
    'PLAN_MASTER' => 'plan_master'
];

// Transmisstion log method
const TRANSMISSION_LOG_METHOD = [
    'GET' => 'get',
    'POST' => 'post',
    'PUT' => 'put',
    'DELETE' => 'delete',
    'PATCH' => 'patch'
];

define('LOG_ACTIONS', [
    'CREATING' => 'create',
    'UPDATING' => 'update',
    'DELETING' => 'delete'
]);

// days of month
define('DAYS_OF_MONTH', [
    1,
    2,
    3,
    4,
    5,
    6,
    7,
    8,
    9,
    10,
    11,
    12,
    13,
    14,
    15,
    16,
    17,
    18,
    19,
    20,
    21,
    22,
    23,
    24,
    25,
    26,
    27,
    28,
    29,
    30,
    31
]);
