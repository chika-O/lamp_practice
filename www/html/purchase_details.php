<?php

require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';

session_start();

// ログインチェック
if(is_logined() === false){
    redirect_to(LOGIN_URL);
  }

// DB接続・ユーザ情報の取得
$db = get_db_connect();
$user = get_login_user($db);

// purchase_historiesから受け取り
$purchase_id = $_POST['purchase_id'];

// 注文履歴を1行取得
$data = get_purchase_history($db,$purchase_id);

// dd($data);

// adminのチェック
if(($data[0]['user_id'] !== $user['user_id']) && (is_admin($user) === false)) {
  redirect_to(LOGIN_URL);
}



// dd($purchase_id);

// 情報取得
$purchase_details = get_purchase_details($db,$purchase_id);

// dd($purchase_details);


include_once VIEW_PATH . '/purchase_details_view.php';