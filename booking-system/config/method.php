<?php

class Admin extends Config{
 
  public $FILE_NAME='booking-system';
  public $DIRECTORY_ADMIN='/booking-admin';
  public $DIRECTORY_BOOKING='/event-list';
  public $URL_ADMIN='booking-admin';
  public $URL_BOOKING='event-list';
  protected static $DB_BOOKING_LIST='booking_list';
  protected static $DB_EVENT_LIST='event_list';
  protected static $DB_BOOKING_LOG='booking_log';
  protected $flag=true;
  protected $error_messages=[];

  public function get_url($value){
    if($value==='admin'){
      return esc_url( home_url( '/'.$this->URL_ADMIN.'/' ) );
    }
    if($value==='booking'){
      return esc_url( home_url( '/'.$this->URL_BOOKING.'/' ) );
    }
  }

  protected function requestDB($sql,$values=[]){
    if($this->flag){
      $pdo=$this->connectDB();
      $stt=$this->requestSQL($pdo,$sql,$values);
      $pdo = null;
      return $stt;
    }
  }

  protected function connectDB(){
    //DB接続
    $dsn = self::$DSN;
    $dnsuser = self::$DSNUSER;
    $password = self::$PASSWORD;
    $pdo = new PDO($dsn, $dnsuser, $password);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); //静的プレースホルダ
    return $pdo;
  }

  protected function requestSQL($pdo='',$sql='',$values=[]){
    $flag=true;
    $pdo ? null : $flag=false;
    $sql ? null : $flag=false;

    if($flag){
      $stt = $pdo->prepare($sql);
      if($values){
        $i=1;
        foreach($values as &$value){
          $stt->bindParam($i, $value, PDO::PARAM_STR);
          $i++;
        }
        unset( $values );
      }
      $stt->execute();
      return $stt;
    }else{
      echo '<p>500 Inertnal Server Error on 002</p>';
      return;
    }
  }

  protected function e($str, $charset = 'UTF-8') {
    $ret = htmlspecialchars($str, ENT_QUOTES, $charset);
    return $ret;
  }

  protected function get_now_timestamp(){
    return time();
  }

  public function check_permission($values=[]){
    foreach($values as $value){
      if(current_user_can($value)){ return true; }
    }
    return false;
  }

  public function printError(){
    if(!$this->flag){
      foreach($this->error_messages as $e){
        echo !$this->isBlank($e) ? '※'.$e.'<br>' : '';
      }
    }
  }

  protected function isDouble($db_name){
    $stt=$this->requestDB('SELECT * FROM '.$db_name.' ORDER BY id DESC LIMIT 1;');
    while( $l = $stt->fetch(PDO::FETCH_ASSOC) ){
      if($db_name===self::$DB_EVENT_LIST && $l['name']===$this->name && $l['locate']===$this->locate && $l['date']===$this->date && date('G:i',strtotime($l['stime']))===$this->stime && date('G:i',strtotime($l['ftime']))===$this->ftime && $l['capacity']===$this->capacity && $l['explanation']===$this->explanation && $l['permission_BUIN']===$this->permission_BUIN){
        return true;
      }
      elseif($db_name===self::$DB_BOOKING_LIST && $l['name']===$this->name && $l['event-id']===$this->event_id && $l['number']===$this->number){
        return true;
      }else{
        return false;
      }
    }
  }

  protected function isLimited($event_id){
    $obj=new Events();
    return $obj->find_by_id($event_id)->get_booking_limit()<=0 ? true : false;
  }

  protected function isDuplication($properties,$db_name){
    if($this->flag){
      $sql='';$i=0;$ary=[];
      foreach($properties as $db_property=>$property){
        $ary[]=$this->$property;
        $sql = $i!==0 ?  $sql.' AND ' : $sql.'';
        $sql = $sql.'`'.(string)$db_property.'`=?';
        $i++;
      }
      $stt=$this->requestDB('SELECT * FROM '.$db_name.' WHERE '.$sql.';',$ary);
      while( $l = $stt->fetch(PDO::FETCH_ASSOC) ){
        return isset($l) ? true : false;
      }
    }
    return false;
  }

  protected function dontExist($properties,$db_name){
    //中身は同じ
    if($this->flag){
      return !$this->isDuplication($properties,$db_name);
    }
  }

  protected function noPermission($event_id){
    $flag=false;
    $obj=new Events();
    $event=$obj->find_by_id($event_id);
    $flag = $event->getPermission_BUIN()===1 && (!$event->check_user_id(array('executive','member','author','owner'))) ? true : false;

    return $flag;
  }

  protected function isBlank($value){
    //pregmatchは空白のときflaseを出力
    return !preg_match('/[^\s　]/', $value) ? true : false;
  }

  protected function overTextLimit($property,$upper_limit,$lower_limit=1){
    $length=mb_strlen($this->$property);
    return ($lower_limit <= $length && $length <= $upper_limit) ? false : ture;
  }

  protected function setErrorMessage($message){
    $this->flag = false;
    $this->error_messages[] = $message ? $message : null;
  }

  protected function checkError($method,$message,$property='',$db_name=''){
    $method==='blank' ? ($this->isBlank($this->$property) ? $this->setErrorMessage($message) : null) : null;
    $method==='double' ? ($this->isDouble($db_name) ? $this->setErrorMessage($message) : null) : null;
    $method==='duplication' ? ($this->isDuplication($property,$db_name) ? $this->setErrorMessage($message) : null) : null;
    $method==='dontexist' ? ($this->dontExist($property,$db_name) ? $this->setErrorMessage($message) : null) : null;
    $method==='limited' ? ($this->isLimited($this->$property) ? $this->setErrorMessage($message) : null) : null;
    $method==='permission' ? ($this->noPermission($this->$property) ? $this->setErrorMessage($message) : null) : null;
  }


  protected function validates(){}

  public function get_back_btn($value,$class){
    return '<input class="'.$class.'" type="button" onclick="history.back()" value="'.$value.'">';
  }

  protected function check_user_id($users=[]){
    $now_user_id=wp_get_current_user()->user_login;
    foreach($users as $user){
      if($now_user_id===$user){
        return true;
      }
    }
    return false;
  }
}


//=========================================================================


class Booking extends Admin{
  protected $id;
  protected $event_id;
  protected $name;
  protected $number;
  protected $note;
  protected $event;

  public function __construct($booking=[]){
    if($booking!==[]){
      $this->setId($booking['id']);
      $this->setEvent_id($booking['event-id']);
      $this->setName($booking['name']);
      $this->setNumber($booking['number']);
      $this->setNote($booking['note']);
    }
  }

  public function set_booking_form_to_property($booking){
    $this->setEvent_id($booking['event-id']);
    $this->setName($booking['yourname']);
    $this->setNumber($booking['number']);
    $this->setNote($booking['note']);
    $this->setEvent_by_booking();
  }

  protected function setId($id){
    $this->id=(int)mb_convert_kana($this->e($id));
  }
  protected function setEvent_id($event_id){
    $this->event_id=(int)mb_convert_kana($this->e($event_id));
  }
  protected function setEvent_by_booking(){
    $events=new Events();
    $events->find_by_id($this->event_id);
    $this->event=$events->events;
  }
  protected function setName($name){
    $this->name=(string)filter_var($this->e($name));
  }
  protected function setNumber($number){
    $this->number=(string)filter_var($this->e($number));
  }
  protected function setNote($note){
    $this->note=(string)filter_var($this->e($note));
  }
  
  public function getId(){
    return $this->id;
  }
  public function getEvent_id(){
    return $this->event_id;
  }
  public function getEvent_by_booking(){
    return $this->event;
  }
  public function getName(){
    return $this->name;
  }
  public function getNumber(){
    return $this->number;
  }
  public function getNote(){
    return $this->note;
  }
}



//====================================================================


class Event extends Admin{
  protected $id='';
  protected $name='';
  protected $date='';
  protected $stime='';
  protected $ftime='';
  protected $locate='';
  protected $capacity='';
  protected $explanation='';
  protected $number_of_booking='';
  protected $booking_limit='';
  protected $permission_BUIN='';

  public function __construct($event=[]){
    if($event!==[]){
      $this->setId($event['id']);
      $this->setName($event['name']);
      $this->setDate($event['date']);
      $this->setStime($event['stime']);
      $this->setFtime($event['ftime']);
      $this->setLocate($event['locate']);
      $this->setCapacity($event['capacity']);
      $this->setExplanation($event['explanation']);
      $this->setNumber_of_booking($event['id']);
      $this->setPermission_BUIN($event['permission_BUIN']);
    }
  }

  protected function setId($id){
    $this->id=(int)mb_convert_kana($this->e($id));
  }
  protected function setName($name){
    $this->name=(string)filter_var($this->e($name));
  }
  protected function setDate($date){
    $this->date=(string)filter_var($this->e($date));
  }
  protected function setStime($stime){
    $this->stime=(string)filter_var($this->e($stime));
  }
  protected function setFtime($ftime){
    $this->ftime=(string)filter_var($this->e($ftime));
  }
  protected function setLocate($locate){
    $this->locate=(string)filter_var($this->e($locate));
  }
  protected function setCapacity($capacity){
    $this->capacity=(int)mb_convert_kana($this->e($capacity));
  }
  protected function setExplanation($explanation){
    $this->explanation=(string)filter_var($this->e($explanation));
  }
  protected function setNumber_of_booking($event_id){
    $event_id=(int)mb_convert_kana($this->e($event_id));
    $bookings=new Bookings();
    $bookings=$bookings->find_by_event_id($event_id);
    $this->number_of_booking=isset($bookings) ? (int)count($bookings) : (int)0;
  }
  protected function setPermission_BUIN($boolen){
    $this->permission_BUIN=(int)mb_convert_kana($this->e($boolen));
  }
  protected function setEvent($event){
    $this->setId($event['id']);
    $this->setName($event['yourname']);
    $this->setDate($event['date']);
    $this->setStime($event['stime']);
    $this->setFtime($event['ftime']);
    $this->setLocate($event['locate']);
    $this->setCapacity($event['capacity']);
    $this->setExplanation($event['explanation']);
    $this->setPermission_BUIN($event['permission_BUIN']);
  }
  
  public function getId(){
    return $this->id;
  }
  public function getName(){
    return $this->name;
  }
  public function getDate(){
    return strtotime($this->date);
  }
  public function getStime(){
    return strtotime($this->stime);
  }
  public function getFtime(){
    return strtotime($this->ftime);
  }
  public function getLocate(){
    return $this->locate;
  }
  public function getCapacity(){
    return $this->capacity;
  }
  public function getExplanation(){
    return $this->explanation;
  }
  public function getNumber_of_booking(){
    return $this->number_of_booking;
  }
  public function getPermission_BUIN(){
    return $this->permission_BUIN;
  }
  public function get_timestamp(){
    date_default_timezone_set('Asia/Tokyo');
    return strtotime($this->date.' '.$this->stime);
  }
  public function get_booking_limit(){
    $this->booking_limit=(int)$this->capacity - (int)$this->number_of_booking;
    return $this->booking_limit;
  }
  public function get_link($str,$url){
    return '<a href="'.$url.'">'.$str.'</a>';
  }
  public function get_url_by_id($id){
    return esc_url( home_url( '/'.$this->URL_BOOKING.'/'.'?id='.$id ) );
  }
  public function get_print_permission(){
    $str='';
    $this->getPermission_BUIN() ?  $str = $str.'部員 ':null;

    $str = $str!=='' ? $str.'のみ閲覧可能' : '制限なし';
    return $str;
  }
  public function get_event_title(){
    date_default_timezone_set('Asia/Tokyo');
    if($this->get_timestamp() > $this->get_now_timestamp()){
      return $this->get_link( $this->getName().' '.date('H:i',$this->getStime()).'～'.date('H:i',$this->getFtime()), $this->get_url_by_id( $this->getId() ) ).'(残り'.$this->get_booking_limit('calender').'人)';
    }else{
      return $this->getName().' '.date('H:i',$this->getStime()).'～'.date('H:i',$this->getFtime());
    }
  }
  
}


//=========================================================================


class Date extends Admin{
  protected $date;
  protected $yaer;
  protected $month;
  protected $day;
  protected $week;
  protected $week_number;

  public function set_date($date,$event=null){
    $this->date=strtotime($date);
    $this->set_year(date('Y',$this->date));
    $this->set_month(date('m',$this->date));
    $this->set_day(date('d',$this->date));
    $this->set_week();
  }
  public function set_year($year=null){
    $year=(int)mb_convert_kana($this->e($year));
    $this->year = is_null($year) ? date('Y',$this->date) : $year;
  }
  public function set_month($month=null){
    $month=(int)mb_convert_kana($this->e($month));
    $this->month = is_null($month) ? date('m',$this->date) : $month;
  }
  public function set_day($day=null){
    $day=(int)mb_convert_kana($this->e($day));
    $this->day = is_null($day) ? date('d',$this->date) : $day;
  }
  public function set_week(){
    $this->set_week_number();
    $ary = array('日', '月', '火', '水', '木', '金', '土');
    $this->week = $ary[date('w', $this->date )];
  }
  public function set_week_number(){
    $this->week_number = date('w', $this->date );
  }

  public function get_date(){
    return $this->date;
  }
  public function get_year(){
    return $this->year;
  }
  public function get_month($int=false){
    return $int===true ? $this->month : sprintf('%02d', $this->month);
  }
  public function get_day(){
    return $this->day;
  }
  public function get_week(){
    return $this->week;
  }
  public function get_week_number(){
    return $this->week_number;
  }
  public function get_today(){
    return strtotime(date('Ymd'));
  }
  public function get_year_today(){
    return date('Y');
  }
  public function get_month_today(){
    return date('m');
  }
  public function get_day_today(){
    return date('d');
  }


}



//====================================================================


class Bookings extends Booking{
  public function __construct(){}
  protected $bookings=[];

  protected function validates(){
    $this->checkError('blank','event_idエラー','event_id');
    $this->checkError('blank','名前を入力してください','name');
    $this->checkError('blank','学籍番号を選択してください','number');
    $this->checkError('limited','残り人数が0人です','event_id');
    $this->checkError('duplication','過去に同じ予約をした可能性があります',array('event-id'=>'event_id','name'=>'name','number'=>'number'),self::$DB_BOOKING_LIST);
    $this->checkError('permission','権限がありません','event_id');
    $this->checkError('dontexist','event_idエラー',array('id'=>'event_id'),self::$DB_EVENT_LIST);
  }

  public function all(){
    $stt=$this->requestDB('SELECT * FROM '.self::$DB_BOOKING_LIST.';');

    $bookings=[];
    while( $value = $stt->fetch(PDO::FETCH_ASSOC) ){ $bookings[] = new Booking($value); }
    $this->bookings=$bookings;
  }

  public function find_by_event_id($event_id){
    $event_id=(int)mb_convert_kana($this->e($event_id));
    $stt=$this->requestDB('SELECT * FROM '.self::$DB_BOOKING_LIST.' WHERE `event-id`=?;',array($event_id));

    $bookings=[];
    while( $value = $stt->fetch(PDO::FETCH_ASSOC) ){ $bookings[] = new Booking($value); }
    $this->bookings=$bookings;
    return $this->bookings;
  }

  public function find_by_user($user){
    $this->setName($user['yourname']);
    $this->setNumber($user['number']);
    $this->checkError('blank','名前を入力してください','name');
    $this->checkError('blank','学籍番号を選択してください','number');

    $stt=$this->requestDB('SELECT * FROM '.self::$DB_BOOKING_LIST.' WHERE `name`=? AND `number`=?;',array($this->name,$this->number));

    if($this->flag){
      $bookings=[];
      while( $value = $stt->fetch(PDO::FETCH_ASSOC) ){
        $booking=new Booking($value);
        $booking->setEvent_by_booking();
        $bookings[] = $booking;
      }
      $this->bookings=$bookings;
      return $this->bookings;
    }else{
      $this->printError();
      return false;
    }
  }

  public function confirm($booking){
    $this->set_booking_form_to_property($booking);
    $this->validates();
    if(! $this->flag){
      $this->printError();
    }
  }

  public function create($booking){
    $this->set_booking_form_to_property($booking);
    $this->validates();
    if($this->flag){
      $stt=$this->requestDB('INSERT INTO '.self::$DB_BOOKING_LIST.'(`event-id`, `name`, `number`, `note`) VALUES (?,?,?,?);',
        array($this->event_id,$this->name,$this->number,$this->note));
      $this->create_log();
      return true;
    }else{
      $this->printError();
      return false;
    }
  }

  public function create_log(){
      $stt=$this->requestDB('INSERT INTO '.self::$DB_BOOKING_LOG.'(`booking_name`, `booking_number`, `booking_note`, `event_name`, `event_capacity`, `event_locate`, `event_date`, `event_stime`, `event_ftime`) VALUES (?,?,?,?,?,?,?,?,?);',
      array($this->name,$this->number,$this->note,$this->getEvent_by_booking()->getName(),$this->getEvent_by_booking()->getCapacity(),$this->getEvent_by_booking()->getLocate(),date("Y-m-d",$this->getEvent_by_booking()->getDate()),date('H:i',$this->getEvent_by_booking()->getStime()),date('H:i',$this->getEvent_by_booking()->getFtime()) ));
      $pdo=null;
  }

  public function delete_by_id($id){
    $this->setId($id);
    if($this->flag){
      $stt=$this->requestDB('DELETE FROM '.self::$DB_BOOKING_LIST.' WHERE `id` = ?;',array($this->id,));
    }else{
      $this->printError();
    }
  }

  public function delete_by_eventId($event_id){
    $this->setEvent_id($event_id);
    if($this->flag){
      $stt=$this->requestDB('DELETE FROM '.self::$DB_BOOKING_LIST.' WHERE `event-id` = ?;',array($this->event_id,));
    }else{
      $this->printError();
    }
  }

  public function get_add_booking_confirm_btn(){
    return $this->flag ? '<input class="btn_add_booking_submit" type="submit" value="確定">' : '';
  }

  public function get_bookings(){
    return $this->bookings;
  }

}


//====================================================================

//Eventsでeventを$this->find_by_event_id($id)のように取得できる
//eventを取得したら$this->evetnsに保管する。
//
class Events extends Event{
  public function __construct(){}
  public $events=[];

  protected function validates(){
    $this->checkError('blank','名前を入力してください','name');
    $this->checkError('blank','場所を入力してください','locate');
    $this->checkError('blank','日付を選択してください','date');
    $this->checkError('blank','開始時間を設定してください','stime');
    $this->checkError('blank','終了時間を設定してください','ftime');
    $this->checkError('blank','人数制限を入力してください','capacity');
    $this->checkError('blank','説明を入力してください','explanation');
    $this->checkError('double','','',self::$DB_EVENT_LIST);
  }

  public function all(){
    $stt=$this->requestDB('SELECT * FROM '.self::$DB_EVENT_LIST.' ORDER BY `date`;');

    $events=[];
    while( $value = $stt->fetch(PDO::FETCH_ASSOC) ){ $events[] = new Event($value); }
    $this->events = $events;
  }

  public function find_by_id($event_id){
    $event_id=(int)mb_convert_kana($this->e($event_id));
    $stt=$this->requestDB('SELECT * FROM '.self::$DB_EVENT_LIST.' WHERE `id`=?;',array($event_id));

    while( $value = $stt->fetch(PDO::FETCH_ASSOC) ){ $event = new Event($value); }
    $this->events = $event ? $event : new Event();
    return $this->events;
  }

  public function find_by_ahead_event(){
    $now=date('Y-m-d');
    $stt=$this->requestDB('SELECT * FROM '.self::$DB_EVENT_LIST.' WHERE `date` >= ? ORDER BY `date`,`stime`;',array($now));

    $events=[];
    while( $value = $stt->fetch(PDO::FETCH_ASSOC) ){ $events[] = new Event($value); }
    $this->events = $events;
  }

  public function find_by_previous_event(){
    $now=date('Y-m-d');
    $stt=$this->requestDB('SELECT * FROM '.self::$DB_EVENT_LIST.' WHERE `date` < ? ORDER BY `date`,`stime`;',array($now));

    $events=[];
    while( $value = $stt->fetch(PDO::FETCH_ASSOC) ){ $events[] = new Event($value); }
    $this->events = $events;
  }

  public function find_by_date_range($lower_limit,$upper_limit){
    $lower_limit=(string)filter_var($this->e($lower_limit));
    $upper_limit=(string)filter_var($this->e($upper_limit));
    $stt=$this->requestDB('SELECT * FROM '.self::$DB_EVENT_LIST.' WHERE `date` BETWEEN ? AND ? ORDER BY `date`,`stime`;',
      array($lower_limit,$upper_limit));

    $events=[];
    while( $value = $stt->fetch(PDO::FETCH_ASSOC) ){ $events[] = new Event($value); }
    $this->events=$events;
    return $this->events;
  }

  public function create($event){
    $this->setEvent($event);
    $this->validates();
    if($this->flag){
      $stt=$this->requestDB('INSERT INTO '.self::$DB_EVENT_LIST.'(name, date, stime, ftime, locate, capacity, explanation, permission_BUIN) VALUES (?,?,?,?,?,?,?,?);',
        array($this->name,$this->date,$this->stime,$this->ftime,$this->locate,$this->capacity,$this->explanation,$this->permission_BUIN,));
    }else{
      $this->printError();
    }
  }

  public function delete_by_id($id){
    $this->setId($id);
    if($this->flag){
      $stt=$this->requestDB('DELETE FROM '.self::$DB_EVENT_LIST.' WHERE `id` = ?;',array($this->id,));

      $delete_booking=new Bookings;
      $delete_booking->delete_by_eventId($this->id);
    }else{
      $this->printError();
    }
  }

  public function update($event){
    $this->setEvent($event);
    $this->validates();
    if($this->flag){
      $stt=$this->requestDB('UPDATE '.self::$DB_EVENT_LIST.' SET `name`=?,`date`=?,`stime`=?,`ftime`=?,`locate`=?,`capacity`=?,`explanation`=? WHERE `id` = ?;',
        array($this->name,$this->date,$this->stime,$this->ftime,$this->locate,$this->capacity,$this->explanation,$this->id));
    }else{
      $this->printError();
    }
  }

}


//================================================


class Calender extends Date{
  protected $days=[];
  protected $schedule=[];
  protected $events=[];
  protected $count_month=0;

  public function set_date($date,$events=null){
    $this->date=strtotime($date);
    $this->set_year(date('Y',$this->date));
    $this->set_month(date('m',$this->date));
    $this->set_day(date('d',$this->date));
    $this->set_week();
    $this->set_events($events);
  }
  
  public function set_focus_month($count_month=0){
    $this->set_count_month($count_month);
    $this->set_date(date('Y-m-01').'+'.$this->get_count_month().' month');
  }

  public function set_events($events=array()){
    $this->events= $events;
  }

  protected function set_count_month($count_month=0){
    $this->count_month=(int)mb_convert_kana($this->e($count_month));
  }

  public function get_days(){
    $obj_e=new Events();
    //指定の月のイベントを取得
    $events=$obj_e->find_by_date_range($this->get_date_beginning_of_the_month(),$this->get_date_end_of_the_month());
    //日付をキーにして配列に挿入
    foreach($events as $event){
      if ( ($event->getPermission_BUIN()===1 && $event->check_user_id(array('executive','member','author','owner'))) || $event->getPermission_BUIN()===0) {
        $this->schedule[ (int)date('d',$event->getDate())][]=$event;
      }
    }
    
    $days = [];
    for ($i = 1; $i <= (int)$this->get_day_end_of_the_month(); $i++){
      $today_events=[];
      if(isset($this->schedule[$i])){
        foreach($this->schedule[$i] as $today_event){ $today_events[]=$today_event; }
      }
      $obj_c=new Calender();
      $obj_c->set_date($this->get_year().$this->get_month().sprintf('%02d', $i),$today_events);
      $days[$i]=$obj_c;
    }
    $this->days=$days;
    return $this->days;
  }

  public function get_day_class(){
    if($this->get_date()===$this->get_today()){
      echo 'today';
    }else{
      echo 'week'.$this->get_week_number();
    }

  }

  public function get_events(){
    return $this->events;
  }

  public function get_count_month(){
    return $this->count_month;
  }

  protected function get_day_end_of_the_month(){
    return date('t', strtotime($this->get_year().$this->get_month().'01'));
  }

  protected function get_date_beginning_of_the_month(){
    return date('Y-m-d', strtotime($this->get_year().$this->get_month().'01'));
  }

  protected function get_date_end_of_the_month(){
    return date('Y-m-d', strtotime($this->get_year().$this->get_month().$this->get_day_end_of_the_month()));
  }

}

?>