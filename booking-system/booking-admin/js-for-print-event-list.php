<script>
function OnLinkClick(element,value="") {
  target = document.getElementById(element + value);

  //要素が非表示の時
  if (target.getAttribute("style") == "display: none;") {
    target.style.display = "block";
  }
  
  //要素が表示されている時
  else {
    if(element=='event_details_'){
      addTarget = document.getElementById('event_updates_' + value);
      addTarget.style.display = "none";
    }
    target.style.display = "none";
  }
  return null;
}

// 削除のときの確認
jQuery('.booking_delete_form,.event_detail_delete_btn').on('submit', function(e, context) {
  // 実行指示のパラメータがあれば何もしない
  if (context === 'execute') {
    return;
  }
  e.preventDefault();
  if (confirm('削除しますか？')) {
    $(this).trigger('submit', ['execute']);
  }
});

</script>