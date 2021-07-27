<?php
$config=new Admin();
?>

<br><br>
<h2>予約フォーム</h2>
<form class="form_add_booking" action="<?php echo $config->get_url('booking'); ?>" method="post">
  <input type="hidden" name="process" value="addBooking">
  <input type="hidden" name="event-id" value="<?php echo (int)$_GET["id"]; ?>">
  <input type="hidden" name="process" value="confirm">
  <p>名前</p>
  <input type="text" name="yourname">
  <p>学籍番号</p>
  <input type="text" name="number">
  <p>備考</p>
  <textarea name="note" cols="30" rows="5"></textarea>
  <div>
    <input type="submit" value="予約">
  </div>
</form>