<?php
// 接口返回错误码定义
define('STP_ERR_UNAUTHORIZED', 101);//未授权，未登录，没有正确传递token
define('STP_ERR_EMAIL_EXIST', 102);//账号已存在（注册时）
define('STP_ERR_EMAIL_NOTEXIST', 103);//账号不存在（登录时）

