// メッセージ表示を少しの間でフェードアウト
const msg = document.querySelector('.message');
if (msg.textContent.trim() !== "") {
    setTimeout(() => {
        msg.style.transition = "opacity 0.8s";
        msg.style.opacity = "0";
    }, 2500);
}