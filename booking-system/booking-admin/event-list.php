<?php

$config=new Admin();
$obj=new Events();
$obj->find_by_ahead_event();
$events=$obj->events;

?>

<a href="<?php echo $config->get_url('admin'); ?>">更新</a><br>

<h2 class="event_list_header">追加済みイベント一覧</h2>

<div class="event_list_table">
  <div class="booking-admin_table_line">
    <div>イベント名</div>
    <div>日付</div>
    <div>人数</div>
    <div>時間</div>
  </div>

<?php foreach($events as $event){ ?>
  <div class="booking-admin_table_line">
    <div>
      <a href="javascript:OnLinkClick('event_details_',<?php echo $event->getId(); ?>);"><?php echo $event->getName(); ?></a>
    </div>
    <div>
      <?php echo date("Y-m-d",$event->getDate()); ?>
    </div>
    <div>
      <?php echo $event->getNumber_of_booking().'/'.$event->getCapacity(); ?>
    </div>
    <div>
      <?php echo date('H:i',$event->getStime()).'～'.date('H:i',$event->getFtime()); ?>
    </div>
  </div>

  <div style="display: none;" class="event_detail_container" id="event_details_<?php echo $event->getId(); ?>">
    <h3>イベント詳細</h3>
    <div class="event_detail_btns_container">
      <a class="event_detail_display_update_btn" href="javascript:OnLinkClick('event_updates_',<?php echo $event->getId(); ?>);">編集画面表示</a>
      <form class="event_detail_delete_btn" method="post" action="<?php echo $config->get_url('admin'); ?>">
        <input type="hidden" name="process" value="deleteEvent">
        <input class="form-event-delete" type="hidden" name="id" value="<?php echo $event->getId(); ?>">
        <input class="form-btn-delete" type="submit" value="削除">
      </form>
    </div>

    <div style="display: none;" class="event_update_container" id="event_updates_<?php echo $event->getId(); ?>">
      <h3>編集画面</h3>
      <form class="form_add_event" method="post" action="<?php echo $config->get_url('admin'); ?>">
        <input type="hidden" name="process" value="updateEvent">
        <input type="hidden" name="id" value="<?php echo $event->getId(); ?>">
        <br><p>イベント名</p>
        <input type="text" name="yourname" value="<?php echo $event->getName(); ?>">
        <br><p>場所</p>
        <input type="text" name="locate" value="<?php echo $event->getLocate(); ?>">
        <br><p>日付</p>
        <input type="date" name="date" value="<?php echo date('Y-m-d',$event->getDate()); ?>">
        <br><p>時間</p>
        <input class="form_add_event_time" type="time" name="stime" value="<?php echo date('H:i',$event->getStime()); ?>">
        ～
        <input class="form_add_event_time" type="time" name="ftime" value="<?php echo date('H:i',$event->getFtime()); ?>">
        <br><p>人数制限</p>
        <input type="text" name="capacity" value="<?php echo $event->getCapacity(); ?>">
        <br><p>説明</p>
        <textarea name="explanation"><?php echo $event->getExplanation(); ?></textarea>
        <input class="form_event_update_btn" type="submit" value="編集">
      </form>  
    </div>


    
    <div>閲覧制限：<?php echo $event->get_print_permission(); ?></div>
    <div>場所：<?php echo $event->getLocate(); ?></div>
    <div>説明：<?php echo $event->getExplanation(); ?></div>
    

    <div class="booking_list_details_table">
      <div class="booking-admin_table_line">
        <div>名前</div>
        <div>学籍番号</div>
        <div>備考</div>
      </div>

    <?php 
    $bookings=new Bookings();
    $bookings=$bookings->find_by_event_id($event->getId());
    if($bookings){
      foreach($bookings as $booking){
    ?>
      <div class="booking-admin_table_line">
        <div><?php echo $booking->getName(); ?></div>
        <div><?php echo $booking->getNumber(); ?></div>
        <div><?php echo $booking->getNote(); ?></div>
        <form class="booking_delete_form" method="post" action="<?php echo $config->get_url('admin'); ?>">
          <input type="hidden" name="process" value="deleteBooking">
          <input type="hidden" name="id" value="<?php echo $booking->getId(); ?>">
          <input class="btn-delete" type="submit" value="削除">
        </form>
      </div>
      <?php
      }
    }else{
      echo '予約なし';
    }
    ?>
    </div>
  </div>
<?php } ?>
</div>


<?php

$config=new Admin();
$obj=new Events();
$obj->find_by_previous_event();
$events=$obj->events;

?>


<div class="pt20"><a href="javascript:OnLinkClick('previous_event_detail');">過去のイベント</a></div>
<div style="display: none;" class="event_list_table" id="previous_event_detail">
  <div class="booking-admin_table_line">
    <div>イベント名</div>
    <div>日付</div>
    <div>人数</div>
    <div>時間</div>
  </div>

<?php foreach($events as $event){ ?>
  <div class="booking-admin_table_line">
    <div>
      <a href="javascript:OnLinkClick('event_details_',<?php echo $event->getId(); ?>);"><?php echo $event->getName(); ?></a>
    </div>
    <div>
      <?php echo date("Y-m-d",$event->getDate()); ?>
    </div>
    <div>
      <?php echo $event->getNumber_of_booking().'/'.$event->getCapacity(); ?>
    </div>
    <div>
      <?php echo date('H:i',$event->getStime()).'～'.date('H:i',$event->getFtime()); ?>
    </div>
  </div>

  <div style="display: none;" class="event_detail_container" id="event_details_<?php echo $event->getId(); ?>">
    <h3>イベント詳細</h3>
    <div class="event_detail_btns_container">
      <a class="event_detail_display_update_btn" href="javascript:OnLinkClick('event_updates_',<?php echo $event->getId(); ?>);">編集画面表示</a>
      <form class="event_detail_delete_btn" method="post" action="<?php echo $config->get_url('admin'); ?>">
        <input type="hidden" name="process" value="deleteEvent">
        <input class="form-event-delete" type="hidden" name="id" value="<?php echo $event->getId(); ?>">
        <input class="form-btn-delete" type="submit" value="削除">
      </form>
    </div>

    <div style="display: none;" class="event_update_container" id="event_updates_<?php echo $event->getId(); ?>">
      <h3>編集画面</h3>
      <form class="form_add_event" method="post" action="<?php echo $config->get_url('admin'); ?>">
        <input type="hidden" name="process" value="updateEvent">
        <input type="hidden" name="id" value="<?php echo $event->getId(); ?>">
        <br><p>イベント名</p>
        <input type="text" name="yourname" value="<?php echo $event->getName(); ?>">
        <br><p>場所</p>
        <input type="text" name="locate" value="<?php echo $event->getLocate(); ?>">
        <br><p>日付</p>
        <input type="date" name="date" value="<?php echo date('Y-m-d',$event->getDate()); ?>">
        <br><p>時間</p>
        <input class="form_add_event_time" type="time" name="stime" value="<?php echo date('H:i',$event->getStime()); ?>">
        ～
        <input class="form_add_event_time" type="time" name="ftime" value="<?php echo date('H:i',$event->getFtime()); ?>">
        <br><p>人数制限</p>
        <input type="text" name="capacity" value="<?php echo $event->getCapacity(); ?>">
        <br><p>説明</p>
        <textarea name="explanation"><?php echo $event->getExplanation(); ?></textarea>
        <input class="form_event_update_btn" type="submit" value="編集">
      </form>  
    </div>


    
    <div>閲覧制限：<?php echo $event->get_print_permission(); ?></div>
    <div>場所：<?php echo $event->getLocate(); ?></div>
    <div>説明：<?php echo $event->getExplanation(); ?></div>
    

    <div class="booking_list_details_table">
      <div class="booking-admin_table_line">
        <div>名前</div>
        <div>学籍番号</div>
        <div>備考</div>
      </div>

    <?php 
    $bookings=new Bookings();
    $bookings=$bookings->find_by_event_id($event->getId());
    if($bookings){
      foreach($bookings as $booking){
    ?>
      <div class="booking-admin_table_line">
        <div><?php echo $booking->getName(); ?></div>
        <div><?php echo $booking->getNumber(); ?></div>
        <div><?php echo $booking->getNote(); ?></div>
        <form class="booking_delete_form" method="post" action="<?php echo $config->get_url('admin'); ?>">
          <input type="hidden" name="process" value="deleteBooking">
          <input type="hidden" name="id" value="<?php echo $booking->getId(); ?>">
          <input class="btn-delete" type="submit" value="削除">
        </form>
      </div>
      <?php
      }
    }else{
      echo '予約なし';
    }
    ?>
    </div>
  </div>
<?php } ?>
</div>



<?php
get_template_part($config->FILE_NAME.$config->DIRECTORY_ADMIN.'/js-for-print-event-list',null,$var);
?>