<?php

$config=new Admin();
$events=new Events();
$event=$events->find_by_id($_GET['id']);

?>

<h1>イベント詳細</h1>
<a href="<?php echo $config->get_url('booking'); ?>">イベント一覧に戻る</a>

<div>
  イベント名：<?php echo $event->getName(); ?>
</div>
<div>
  場所：<?php echo $event->getLocate(); ?>
</div>
<div>
  日付:<?php echo date( "Y年m月d日", $event->getDate()); ?>
</div>
<div>
  時間:<?php echo date( "H:i", $event->getStime()).'～'.date( "H:i", $event->getFtime()); ?>
</div>
<div>
  人数：<?php echo $event->getCapacity(); ?>人まで（残り<?php echo $event->get_booking_limit(); ?>人）
</div>
<div>
  詳細：<?php echo $event->getExplanation(); ?>
</div>