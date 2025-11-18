document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("regForm");
 
    form.addEventListener("submit", (e) => {
        // フォーム送信はそのまま行う
        // PHP の登録処理 → 成功 → HTML側に "保存できました！" が出る
        // それを少し待ってから次の画面へ遷移
        setTimeout(() => {
            window.location.href = "gamen2-1.html";
        }, 500); // 0.5秒後に遷移
    });
});
 