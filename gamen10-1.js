// 戻るボタンの処理
document.addEventListener("DOMContentLoaded", function () {
  const btn = document.getElementById("backBtn");
  btn.addEventListener("click", function () {
    // 戻るボタンを押したら test_main.php に遷移
    window.location.href = "test_main.php";
  });
});
 
 