
<?php

$config=new Admin();
$booking=new Bookings();
if($booking->create($_POST)){

?>

<a href="<?php echo $config->get_url('booking'); ?>">イベント一覧に戻る</a><br>

<br>以下の内容で予約を受け付けました。<br>
スクリーンショットなどで保存することをおすすめします。<br>
また、「<a href="<?php echo $config->get_url('booking').'?process=frl' ?>">過去の予約内容を確認する</a>」から過去の予約一覧を確認できます。<br>

<h3>入力内容</h3>
<table class="table_add_booking_confirm">
  <tr>
    <td>お名前</td><td><?php echo $booking->getName(); ?></td>
  </tr>
  <tr>
    <td>学籍番号</td><td><?php echo $booking->getNumber(); ?></td>
  </tr>
  <tr>
    <td>備考</td><td><?php echo $booking->getNote(); ?></td>
  </tr>
</table>
<h3>イベント内容</h3>
<table class="table_add_booking_confirm">
  <tr>
    <td>イベント名</td><td><?php echo $booking->getEvent_by_booking()->getName(); ?></td>
  </tr>
  <tr>
    <td>日付</td><td><?php echo date("Y-m-d",$booking->getEvent_by_booking()->getDate()); ?></td>
  </tr>
  <tr>
    <td>時間</td><td><?php echo date('H:i',$booking->getEvent_by_booking()->getStime()).'～'.date('H:i',$booking->getEvent_by_booking()->getFtime()); ?></td>
  </tr>
  <tr>
    <td>場所</td><td><?php echo $booking->getEvent_by_booking()->getLocate(); ?></td>
  </tr>
  <tr>
    <td>説明</td><td><?php echo $booking->getEvent_by_booking()->getExplanation(); ?></td>
  </tr>
</table>
  
<?php } ?>