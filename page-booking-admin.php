<?php //通常ページとAMPページの切り分け
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if (!is_amp()) {
  get_header();
} else {
  get_template_part('tmp/amp-header');
}
?>

<h1><?php the_title(); ?></h1>

<?php

try{

  get_template_part('booking-system/config/environment');
  get_template_part('booking-system/config/method');
  $config=new Admin();


  $obj_e=new Events();
  $obj_b=new Bookings();

  // POSTリクエストによるページ遷移かチェック
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if(isset($_POST['process'])){
    
      //イベントを削除したいとき
      if($_POST['process']==='deleteEvent'){
        $obj_e->delete_by_id($_POST['id']);
        $_POST=[];
      }
      //予約を削除したいとき
      elseif($_POST['process']==='deleteBooking'){
        $obj_b->delete_by_id($_POST['id']);
        $_POST=[];
      }
      //イベントを更新したいとき
      elseif($_POST['process']==='updateEvent'){
        $obj_e->update($_POST);
        $_POST=[];
      }
      //イベントを追加したい時
      elseif($_POST['process']==='addEvent' || true){
        $obj_e->create($_POST);
      }
    }
  }

  //イベントリストの表示
  get_template_part($config->FILE_NAME.$config->DIRECTORY_ADMIN.'/event-list');


  //イベント追加用フォーム
  get_template_part($config->FILE_NAME.$config->DIRECTORY_ADMIN.'/form-add-event');

 } catch (PDOException $e) {
  echo ("<p>500 Inertnal Server Error</p>");
  exit($e->getMessage());
}

?>


<?php get_footer(); ?>