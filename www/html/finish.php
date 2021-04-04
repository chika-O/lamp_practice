<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';

session_start();

// ログインできていなければリダイレクト
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$token = get_csrf_token();

// DB接続、ユーザidからデータ取得
$db = get_db_connect();
$user = get_login_user($db);

// カート情報取得
$carts = get_user_carts($db, $user['user_id']);

// 購入処理
if(purchase_carts($db, $carts) === false){
  set_error('商品が購入できませんでした。');
  redirect_to(CART_URL);
} 

$total_price = sum_carts($carts);

include_once '../view/finish_view.php';