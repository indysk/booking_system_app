<? $config=new Admin(); ?>
<h2 class="form_add_event_header">イベント追加フォーム</h2>
<form class="form_add_event" action="<?php echo $config->get_url('admin'); ?>" method="post">
  <input type="hidden" name="process" value="addEvent">
  <p>イベント名</p>
  <input type="text" name="yourname" <?php if($_POST['yourname']){echo 'value="'.$_POST['yourname'].'"';} ?>>
  <p>場所</p>
  <input type="text" name="locate" <?php if($_POST['locate']){echo 'value="'.$_POST['locate'].'"';} ?>>
  <p>日付</p>
  <input type="date" name="date" <?php if($_POST['date']){echo 'value="'.$_POST['date'].'"';} ?>>
  <p>時間</p>
  <input class="form_add_event_time" type="time" name="stime" <?php if($_POST['stime']){echo 'value="'.$_POST['stime'].'"';} ?>>
  ～
  <input class="form_add_event_time" type="time" name="ftime" <?php if($_POST['ftime']){echo 'value="'.$_POST['ftime'].'"';} ?>>
  <p>人数制限（制限なしのときは0を入力）</p>
  <input type="text" name="capacity" <?php if($_POST['capacity']){echo 'value="'.$_POST['capacity'].'"';} ?>>
  <p>説明</p>
  <textarea name="explanation"><?php if($_POST['explanation']){echo $_POST['explanation'];} ?></textarea>
  <label><input type="checkbox" name="permission_BUIN" value="1"　id="checkbox_booking_admin" />部員のみ予約可能にする</label>
  
  <div class="submit_btn_container_booking_admin">
    <input type="submit" value="イベントを追加">
  </div>
</form>