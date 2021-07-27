
<?php


try{
  $config=new Admin();
  $booking=new Bookings();
  $booking->confirm($_POST);
  
?>

<h2>予約内容の確認</h2>
<a href="<?php echo $config->get_url('booking'); ?>">イベント一覧に戻る</a><br>

      <table class="table_add_booking_confirm">
        <tr>
          <td>イベント名</td><td><?php echo $booking->getEvent_by_booking()->getName(); ?></td>
        </tr>
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


      <?php //不可視のフォーム ?>
      <form action="<?php echo $config->get_url('booking'); ?>" method="post">
        <input type="hidden" name="event-id" value="<?php echo $booking->getEvent_id(); ?>">
        <input type="hidden" name="yourname" value="<?php echo $booking->getName(); ?>">
        <input type="hidden" name="number" value="<?php echo $booking->getNumber(); ?>">
        <input type="hidden" name="note" value="<?php echo $booking->getNote(); ?>">
        <input type="hidden" name="process" value="thanks">


        <div style="display:flex;justify-content: space-between;">
          <?php echo $booking->get_back_btn('戻る','btn_add_booking_back'); ?>
          <?php echo $booking->get_add_booking_confirm_btn(); ?>
         </div>
      </form>    

<?php

} catch (PDOException $e) {
  echo ("<p>500 Inertnal Server Error</p>");
  exit($e->getMessage());
}
?>

