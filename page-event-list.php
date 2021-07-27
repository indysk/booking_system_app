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
  

try{

  get_template_part('booking-system/config/environment');
  get_template_part('booking-system/config/method');
  $config=new Admin();


  $obj_e=new Events();
  $obj_b=new Bookings();

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //予約の確認ページ
    if($_POST['process']==='confirm'){
      
      get_template_part($config->FILE_NAME.$config->DIRECTORY_BOOKING.'/add-booking-confirm');

    //予約完了ページ
    }elseif($_POST['process']==='thanks'){
      get_template_part($config->FILE_NAME.$config->DIRECTORY_BOOKING.'/add-booking-thanks');

    //過去の予約確認ページ
    }elseif($_POST['process']==='request-logs'){
      get_template_part($config->FILE_NAME.$config->DIRECTORY_BOOKING.'/request-logs');
    }

  }

  //イベント詳細ページ
  elseif ( isset($_GET['id']) ) {
    get_template_part($config->FILE_NAME.$config->DIRECTORY_BOOKING.'/event-details');
    get_template_part($config->FILE_NAME.$config->DIRECTORY_BOOKING.'/form-add-booking');
  }

  //過去の予約確認ページへのフォーム
  elseif($_GET['process']==='frl'){
    get_template_part($config->FILE_NAME.$config->DIRECTORY_BOOKING.'/form-request-logs');
  }

  //カレンダー
  elseif(isset($_GET['month']) || true){
    get_template_part($config->FILE_NAME.$config->DIRECTORY_BOOKING.'/calender');
  }

} catch (PDOException $e) {
echo ("<p>500 Inertnal Server Error</p>");
exit($e->getMessage());
}

get_footer();