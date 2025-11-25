// script.js
$(function () {
 
    const MAX_SENNTAX = 5;
 
    // 初期化: 変更フォームに現在の値をセット（もし表示されていたら）
    function populateChangeForm() {
        const title = $('#displayTitle').text().trim();
        const start = $('#displayStart').text().trim();
        const end = $('#displayEnd').text().trim();
 
        $('#newTitle').val(title === '' ? '' : title);
        // 日付表示が空または '9999-12-31' の時は空欄に
        $('#newStart').val(start === '' ? '' : start === '9999-12-31' ? '' : start);
        $('#newEnd').val(end === '' ? '' : end === '9999-12-31' ? '' : end);
    }
 
    // 個数チェック（追加・削除のたびに呼ぶ）
    function checkSenntaLimit() {
        const count = $('#senntaList').children().length;
 
        if (count >= MAX_SENNTAX) {
            $('#senntaInput').prop('disabled', true);
            $('#senntaAddBtn').prop('disabled', true).addClass('btn-secondary');
        } else {
            $('#senntaInput').prop('disabled', false);
            $('#senntaAddBtn').prop('disabled', false).removeClass('btn-secondary');
        }
    }
 
    // 追加処理
    $('#senntaForm').on('submit', function (e) {
        e.preventDefault();
 
        $.post('add_sennta.php', $(this).serialize(), function (data) {
            // data は追加された選択肢のIDを返す想定
            const text = $('#senntaInput').val();
 
            $('#senntaList').append(`
                <div class="sennta-item">
                    <div class="sennta-text">${escapeHtml(text)}</div>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="${data}">
                        削除
                    </button>
                </div>
            `);
 
            //$('#senntaForm')[0].reset();
            checkSenntaLimit();
        });
    });
 
    // 削除処理（動的要素）
    $('#senntaList').on('click', '.delete-btn', function () {
        const sennta_id = $(this).data('id');
        const targetDiv = $(this).closest('.sennta-item');
 
        $.post('delete_sennta.php', { id: sennta_id }, function (res) {
            if (res.trim() === 'OK') {
                targetDiv.remove();
                checkSenntaLimit();
            } else {
                alert('削除に失敗しました。');
            }
        });
    });
 
    // 変更ボタン表示
    $('#changeBtn').on('click', function () {
        populateChangeForm();
        $('#changeFormWrap').slideDown(180);
        $('#changeMsg').text('');
    });
 
    // キャンセル
    $('#cancelChangeBtn').on('click', function () {
        $('#changeFormWrap').slideUp(120);
    });
 
    // Ajaxで更新（非同期）
    $('#changeForm').on('submit', function (e) {
        e.preventDefault();
        $('#saveChangeBtn').prop('disabled', true).text('保存中...');
        $('#changeMsg').text('');
 
        const payload = {
            vote_id: $(this).find('input[name="vote_id"]').val(),
            title: $('#newTitle').val(),
            start_date: $('#newStart').val(),
            end_date: $('#newEnd').val()
        };
 
        $.ajax({
            url: 'update_vote.php',
            method: 'POST',
            data: payload,
            dataType: 'json'
        }).done(function (resp) {
            if (resp && resp.status === 'OK') {
                // 画面表示を更新して編集フォームを閉じる
                $('#displayTitle').text(payload.title);
                $('#displayStart').text(payload.start_date || '');
                $('#displayEnd').text(payload.end_date || '');
                $('#changeMsg').text('保存しました。').css('color', 'green');
                setTimeout(function () {
                    $('#changeFormWrap').slideUp(160);
                }, 700);
            } else {
                $('#changeMsg').text('保存に失敗しました。').css('color', 'red');
            }
        }).fail(function () {
            $('#changeMsg').text('通信エラーが発生しました。').css('color', 'red');
        }).always(function () {
            $('#saveChangeBtn').prop('disabled', false).text('保存');
        });
    });
 
    // 初期チェック
    checkSenntaLimit();
 
    // HTMLエスケープ（簡易）
    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }
});
 
 