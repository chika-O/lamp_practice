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

$db = get_db_connect();
$user = get_login_user($db);


// purchase_historiesのデータ取得
$purchase_histories = get_purchase_information($db, $user);

// dd($purchase_informations);

// 合計金額を求める
$total_price = $purchase_informations['purchase_price']*$purchase_informations['purchase_amount'];

include_once VIEW_PATH . '/purchase_histories_view.php';