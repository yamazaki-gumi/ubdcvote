// 戻るボタンの処理
document.addEventListener("DOMContentLoaded", function () {
  const btn = document.getElementById("backBtn");
  btn.addEventListener("click", function () {
    // 戻るボタンを押したら main.php に遷移
    window.location.href = "zumi.php";
  });
});
 
 