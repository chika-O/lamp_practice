<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <?php include VIEW_PATH . 'templates/head.php'; ?>
    <title>購入履歴</title>
    <style>
       table {
         width: 1000px;
         border-collapse: collapse;
      }

      table,
      tr,
      th,
      td {
         border: solid 1px;
         padding: 10px;
      }
   </style>
    </style>
</head>
<body>
    <h2>購入履歴</h2>
    <table border=1>
        <tr>
            <th>注文番号</th>
            <th>購入日時</th>
            <th>合計金額</th>
            <th>操作</th>
        </tr>
        <?php foreach ($purchase_histories as $purchase_history){ ?>
        <tr>
            <td><?php print h($purchase_history['purchase_id']);?></td>   
            <td><?php print h($purchase_history['purchase_datetime']);?></td>   
            <td><?php print h($purchase_history['total_amount']); ?></td>  
            <td>
                <form method="post" action="purchase_details.php">
                <input type="hidden" name="purchase_id" value=<?php print $purchase_history['purchase_id'];?>>
                <!-- <input type="submit" value="購入明細表示"> -->
                <button>購入明細表示</button>
                </form>
            </td> 
            
        <?php }?>
        </tr>
    </table>
    
</body>
</html>