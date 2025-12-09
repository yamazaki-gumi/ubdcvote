// script.js
$(function () {

    const MAX_SENNTAX = 5;

    // ===== 変更フォーム初期化 =====
    function populateChangeForm() {
        const title = $('#displayTitle').text().trim();
        const start = $('#displayStart').text().trim();
        const end   = $('#displayEnd').text().trim();
        const classIdText = $('#displayClassId').text().trim();

        $('#newTitle').val(title);
        $('#newStart').val(start === '' || start === '9999-12-31' ? '' : start);
        $('#newEnd').val(end === '' || end === '9999-12-31' ? '' : end);

        // 「全体公開」なら空欄にする
        $('#newClassId').val(classIdText === '全体公開' ? '' : classIdText);
    }

    // ===== 選択肢数制限チェック =====
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

    // ===== 選択肢追加 =====
    $('#senntaForm').on('submit', function (e) {
        e.preventDefault();

        $.post('add_sennta.php', $(this).serialize(), function (data) {
            const text = $('#senntaInput').val();

            $('#senntaList').append(`
                <div class="sennta-item">
                    <div class="sennta-text">${escapeHtml(text)}</div>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="${data}">
                        削除
                    </button>
                </div>
            `);

            $('#senntaForm')[0].reset();
            checkSenntaLimit();
        });
    });

    // ===== 選択肢削除 =====
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

    // ===== 変更ボタン =====
    $('#changeBtn').on('click', function () {
        populateChangeForm();
        $('#changeFormWrap').slideDown(180);
        $('#changeMsg').text('');
    });

    // ===== キャンセル =====
    $('#cancelChangeBtn').on('click', function () {
        $('#changeFormWrap').slideUp(120);
    });

    // ===== 日付制限 =====
    const today = new Date().toISOString().split("T")[0];
    $('#newStart').attr('min', today);

    $('#newStart').on('change', function () {
        const startDate = $(this).val();
        if (startDate) {
            $('#newEnd').attr('min', startDate);
        }
    });

    // ===== 保存処理（完全修正版）=====
    $('#changeForm').on('submit', function (e) {
        e.preventDefault();

        const today = new Date().toISOString().split("T")[0];

        const startDate = $('#newStart').val();
        const endDate   = $('#newEnd').val();

        // ---- バリデーション ----
        if (startDate && startDate < today) {
            $('#changeMsg').text('開始日は今日以降の日付を選んでください。').css('color', 'red');
            return;
        }

        if (startDate && endDate && startDate === endDate) {
            $('#changeMsg').text('開始日と終了日は同じ日にできません。').css('color', 'red');
            return;
        }

        if (startDate && endDate && endDate < startDate) {
            $('#changeMsg').text('終了日は開始日以降の日付を選んでください。').css('color', 'red');
            return;
        }

        $('#saveChangeBtn').prop('disabled', true).text('保存中...');
        $('#changeMsg').text('');

        const payload = {
            vote_id: $('input[name="vote_id"]').val(),
            new_title: $('#newTitle').val(),
            new_class_id: $('#newClassId').val(),
            new_start: $('#newStart').val(),
            new_end: $('#newEnd').val()
        };

        $.ajax({
            url: 'update_vote.php',
            type: 'POST',
            data: payload,
            dataType: 'json'
        })
        .done(function (resp) {
            if (resp.status === 'OK') {

                // ===== 表示更新 =====
                $('#displayTitle').text(payload.new_title);

                if (payload.new_start) {
                    $('#displayStart').text(payload.new_start);
                }

                if (payload.new_end) {
                    $('#displayEnd').text(payload.new_end);
                }

                // クラスID表示更新（空欄なら全体公開）
                $('#displayClassId').text(
                    payload.new_class_id === '' ? '全体公開' : payload.new_class_id
                );

                $('#changeMsg').text('保存しました。').css('color', 'green');

                setTimeout(function () {
                    $('#changeFormWrap').slideUp(160);
                }, 700);

            } else {
                $('#changeMsg').text(resp.msg || '保存に失敗しました。').css('color', 'red');
            }
        })
        .fail(function () {
            $('#changeMsg').text('通信エラーが発生しました。').css('color', 'red');
        })
        .always(function () {
            $('#saveChangeBtn').prop('disabled', false).text('保存');
        });
    });

    // ===== 初期チェック =====
    checkSenntaLimit();

    // ===== HTMLエスケープ =====
    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

});
