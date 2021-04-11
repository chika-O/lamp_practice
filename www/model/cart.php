<?php 
// ファイル読み込み
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

// cartsテーブルとitemsテーブルを結合し、fetchする
function get_user_carts($db, $user_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = :user_id
  ";

  $params = array(':user_id' => $user_id);
  // ここに引数を渡して取得
  return fetch_all_query($db, $sql,$params);
}

// cartsテーブルとitemsテーブルを結合、ユーザidとアイテムidが一致する商品情報を取得
function get_user_cart($db, $user_id, $item_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = {$user_id}
    AND
      items.item_id = {$item_id}
  ";

  return fetch_query($db, $sql);

}

// カートに商品があれば更新、なければ追加
function add_cart($db, $user_id, $item_id ) {
  $cart = get_user_cart($db, $user_id, $item_id);
  if($cart === false){
    return insert_cart($db, $user_id, $item_id);
  }
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}

// 指定されたIDをもとにカートに挿入
function insert_cart($db, $user_id, $item_id, $amount = 1){
  $sql = "
    INSERT INTO
      carts(
        item_id,      
        user_id,
        amount
      )
    VALUES(:item_id, :user_id, :amount)
  ";

  $params = array(':item_id'=>$item_id,':user_id'=>$user_id,':amount'=>$amount);
  return execute_query($db,$sql,$params);
}

// カート内容の更新
function update_cart_amount($db, $cart_id, $amount){
  $sql = "
    UPDATE
      carts
    SET
      amount = :amount
    WHERE
      cart_id = :cart_id
  // 最大で一行のデータに対して
    LIMIT 1
  ";

  $params = array(':amount' => $amount, ':cart_id' => $cart_id);
  // sql実行
  return execute_query($db,$sql,$params);
}

// カートの中身を削除
function delete_cart($db, $cart_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = :cart_id
    LIMIT 1
  ";

  $params = array(':cart_id' => $cart_id);
  return execute_query($db, $sql, $params);
}

// 購入処理
function purchase_carts($db, $carts){
  // 正常に購入できる状態かどうかをチェック
  if(validate_cart_purchase($carts) === false){
    return false;
  }
  $db->beginTransaction();


  // amount分を在庫から削除する
  foreach($carts as $cart){
    if(update_item_stock(
        $db, 
        $cart['item_id'], 
        $cart['stock'] - $cart['amount']
      ) === false){
      set_error($cart['name'] . 'の購入に失敗しました。');
    }
  }
  // 該当ユーザのカート情報を削除
  delete_user_carts($db, $carts[0]['user_id']);

  //追加テーブルへのデータ書き込み
  if (regist_purchase_histories($db,$carts) === true){
    $purchase_id = $db->lastInsertId();
    regist_purchase_details($db,$carts,$purchase_id);
  }
  

  //set_errorされたかをを判別
  if(has_error() === false){
    $db->commit();
  }else {
    $db->rollback();
    return false;
  }
}

// 該当するユーザIDの商品をcartsテーブルから削除
function delete_user_carts($db, $user_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      user_id = :user_id
  ";

  $params = array(':user_id'=>$user_id);

  execute_query($db, $sql, $params);
}

//purchase_historiesの更新
function regist_purchase_histories($db,$carts){
    $sql = "
      INSERT INTO
        purchase_histories(
          user_id
        )VALUES(
          :user_id
        )
    ";
    $params = array(':user_id' => $carts[0]['user_id']);
    return execute_query($db, $sql, $params);

  }

  //purchase_detailsの更新
  function regist_purchase_details($db,$carts,$purchase_id){
    foreach ($carts as $cart) {
      $sql = "
        INSERT INTO
          purchase_details(
            purchase_id,
            item_id,
            purchase_price,
            purchase_amount
          )VALUES(
            :purchase_id,
            :item_id,
            :purchase_price,
            :purchase_amount
          )
      ";
    
      $params = array(':purchase_id' => $purchase_id,':item_id' => $cart['item_id'],':purchase_price' =>$cart['price'],':purchase_amount' => $cart['amount']);
      execute_query($db, $sql, $params);
    }
  }


    

// カート内の値段*数量を計算し、カート全体の合計金額を取得
function sum_carts($carts){
  $total_price = 0;
  foreach($carts as $cart){
    $total_price += $cart['price'] * $cart['amount'];
  }
  return $total_price;
}

// エラーメッセージ　
function validate_cart_purchase($carts){
  // $cartsの中身が０ならエラーメッセージとfalseを返す
  if(count($carts) === 0){
    set_error('カートに商品が入っていません。');
    return false;
  }
  foreach($carts as $cart){
    if(is_open($cart) === false){
      set_error($cart['name'] . 'は現在購入できません。');
    }
    if($cart['stock'] - $cart['amount'] < 0){
      set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
    }
  }
  if(has_error() === true){
    return false;
  }
  return true;
}

