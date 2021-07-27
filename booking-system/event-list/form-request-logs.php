<?php
$config=new Admin();
?>

<h2>予約内容を確認</h2>
<p>名前と学籍番号を入力してください</p>
<form class="form_add_booking" action="<?php echo $config->get_url('booking'); ?>" method="post">
  <input type="hidden" name="process" value="request-logs">
  <p>名前</p>
  <input type="text" name="yourname">
  <p>学籍番号</p>
  <input type="text" name="number">
  <div>
    <input type="submit" value="確定">
  </div>
</form>

<a href="<?php echo $config->get_url('booking'); ?>">イベント一覧に戻る</a><br>