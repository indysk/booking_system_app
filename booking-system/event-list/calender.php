<?php

$config=new Admin();
date_default_timezone_set('Asia/Tokyo');
$obj=new Calender();
$obj->set_focus_month($_GET['month']);

?>


<h1><?php the_title(); ?></h1>
<a href="<?php echo $config->get_url('booking').'?process=frl'; ?>">過去の予約内容を確認する</a>

<?php the_content(); ?>

<div class="calender_pagination">
  <a id="calender_pagination_previous" href="<?php echo $config->get_url('booking').'?month='.(string)((int)$obj->get_count_month() - 1); ?>"><?php echo (int)$obj->get_month()-1; ?>月</a>
  <a id="calender_pagination_next" href="<?php echo $config->get_url('booking').'?month='.(string)((int)$obj->get_count_month() + 1); ?>"><?php echo (int)$obj->get_month()+1; ?>月</a>
</div>
<h2 class="calender_header"><?php echo (int)$obj->get_month(); ?>月の予定</h2>
<table class="calender_column">

  <?php foreach($obj->get_days() as $oneday){ ?>

    <tr class="<?php echo $oneday->get_day_class(); ?>">
      <td>
        <?php echo $oneday->get_day(false); ?>(<?php echo $oneday->get_week(); ?>)
      </td>
      <td>
        <? $count=count($oneday->get_events());$i=0;
        foreach($oneday->get_events() as $event){
          $i++;
          echo $event->get_event_title();
          echo $count > $i ? '<br>' : null;
        } ?>
      </td>
    </tr>
    
  <?php } ?>

</table>