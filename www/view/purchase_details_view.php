<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>購入明細</title>
</head>
<body>
<table border=1>
    <tr>
        <th>注文番号</th>
        <th>購入日時</th>
        <th>合計金額</th>
    </tr>
    <tr>
        <td><?php print $data[0]['purchase_id'];?></td>
        <td><?php print $data[0]['purchase_datetime'];?></td>
        <td><?php print $data[0]['total_amount'];?></td>
    </tr>
</table>
<h2>購入明細</h2>
   <table border=1>
    <tr>
        <th>商品名</th>
        <th>購入時の商品価格</th>
        <th>購入数</th>
        <th>小計</th>
    </tr>
    <?php foreach ($purchase_details as $purchase_detail) {?>
    <tr>
        <td><?php print h($purchase_detail['name']);?></td>
        <td><?php print h($purchase_detail['purchase_price']);?></td>
        <td><?php print h($purchase_detail['purchase_amount']);?></td>
        <td><?php print h($purchase_detail['purchase_price']*$purchase_detail['purchase_amount']);?></td>
    </tr>
    <?php }?>
   </table> 
</body>
</html>