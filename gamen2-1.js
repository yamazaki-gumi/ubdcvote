// 戻るボタンの処理
document.addEventListener("DOMContentLoaded", function () {
  const btn = document.getElementById("backBtn");
  btn.addEventListener("click", function () {
    // 戻るボタンを押したら gamen1 に遷移
    window.location.replace("gamen1.php");
  });
});
 
 