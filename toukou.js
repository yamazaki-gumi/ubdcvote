const postList = document.getElementById('postList');
const refreshBtn = document.getElementById('refreshBtn');
const sortBtn = document.getElementById('sortBtn');

let sortOrder = 'latest'; // 'latest' or 'top'

// ダミー投稿データ（実際はAPIから取得する想定）
let posts = [
{ id: 1, title: 'こんにちは！', likes: 2, createdAt: '2025-07-01' },
{ id: 2, title: 'JavaScript最高！', likes: 10, createdAt: '2025-07-02' },
{ id: 3, title: '今日の天気は？', likes: 4, createdAt: '2025-06-30' }
];

function renderPosts() {
  // 並び替え処理
const sorted = [...posts].sort((a, b) => {
    if (sortOrder === 'latest') {
    return new Date(b.createdAt) - new Date(a.createdAt);
    } else {
    return b.likes - a.likes;
    }
});

  // 投稿一覧を描画
postList.innerHTML = '';
sorted.forEach(post => {
    const div = document.createElement('div');
    div.className = 'post';
    div.innerHTML = `<strong>${post.title}</strong><br>いいね：${post.likes}`;
    div.addEventListener('click', () => {
      // 詳細ページに遷移
    window.location.href = `post-detail.html?id=${post.id}`;
    });
    postList.appendChild(div);
});
}

refreshBtn.addEventListener('click', () => {
console.log("投稿を更新中...");
  // 本来はAPIから再取得
alert('投稿を更新しました（仮）');
renderPosts();
});

sortBtn.addEventListener('click', () => {
sortOrder = (sortOrder === 'latest') ? 'top' : 'latest';
sortBtn.textContent = `並び替え: ${sortOrder === 'latest' ? '最新順' : 'トップ順'}`;
renderPosts();
});

renderPosts(); // 初期描画
