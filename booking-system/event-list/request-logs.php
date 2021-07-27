<?php
$config=new Admin();
$bookings=new Bookings();
$bookings->find_by_user($_POST);
?>

<h2>予約内容</h2>
<h3><?php echo $bookings->getName(); ?>さんの予約一覧</h3>

<?php
if($bookings->get_bookings()){
  foreach($bookings->get_bookings() as $booking){
?>

    <table class="table_print_booking_log">
      <tr>
        <td>イベント名</td><td><?php echo $booking->getEvent_by_booking()->getName(); ?></td>
      </tr> 
    <tr>
    <tr>
        <td>場所</td><td><?php echo $booking->getEvent_by_booking()->getLocate(); ?></td>
      </tr> 
    <tr>
      <td>日付</td><td><?php echo date("Y年m月d日",$booking->getEvent_by_booking()->getDate()); ?></td>
    </tr>
    <tr>
      <td>時間</td><td><?php echo date( "H:i",$booking->getEvent_by_booking()->getStime()).'～'.date( "H:i", $booking->getEvent_by_booking()->getFtime()); ?></td>
    </tr>
    <tr>
      <td>詳細</td><td><?php echo $booking->getEvent_by_booking()->getExplanation(); ?></td>
    </tr>
  </table>

<?php
  }
}else{

    echo '過去の予約が見つかりませんでした。<br>';
    echo '入力内容を確認してください。<br>';
    echo '<input class="btn_add_booking_back" type="button" onclick="history.back()" value="入力画面に戻る">';

  }

  ?>


<a href="<?php echo $config->get_url('booking'); ?>">イベント一覧に戻る</a><br>