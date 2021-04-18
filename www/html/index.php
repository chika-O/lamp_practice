<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';

session_start();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$token = get_csrf_token();

// 並べ替え情報取得
$order = $_GET['order'];


// DB接続
$db = get_db_connect();
$user = get_login_user($db);


// 公開商品の取得
$items = get_open_items($db,$order);

include_once VIEW_PATH . 'index_view.php';