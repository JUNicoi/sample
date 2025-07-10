<?php
/*
Plugin Name: BASIC認証 htpasswd 生成ツール
Description: ショートコードで htpasswd 形式を出力します（モーダル通知付き・スクロール防止対応）
Version: 1.3
Author: パピィ
*/

function generate_htpasswd_shortcode()
{
    ob_start();
?>
    <form id="htpasswd-form">
        <label>ユーザー名：<input type="text" name="user" required></label><br><br>
        <label>パスワード：<input type="password" name="pass" required></label><br><br>
        <button type="submit">生成</button>
    </form>

    <div id="htpasswd-result" style="margin-top:20px;"></div>

    <!-- モーダル -->
    <div id="copy-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:10000; align-items:center; justify-content:center;">
        <div style="background:white; padding:20px 30px; box-shadow:0 0 10px rgba(0,0,0,0.3); text-align:center;">
            <p style="font-size:16px; margin:0 0 10px;">コピーしました！</p>
            <button onclick="closeModal()" style="padding:5px 15px;">OK</button>
        </div>
    </div>

    <script>
        document.getElementById("htpasswd-form").addEventListener("submit", function(e) {
            e.preventDefault(); // ★ここでリロードを止める
            const form = e.target;
            const formData = new FormData(form);

            fetch("<?= admin_url('admin-ajax.php') ?>", {
                    method: "POST",
                    body: new URLSearchParams({
                        action: "generate_htpasswd_ajax",
                        user: formData.get("user"),
                        pass: formData.get("pass")
                    })
                })
                .then(res => res.text())
                .then(result => {
                    document.getElementById("htpasswd-result").innerHTML = result;
                });
        });

        function copyHtpasswd() {
            const textarea = document.getElementById('htpasswd-output');
            textarea.select();
            document.execCommand('copy');
            showModal();
        }

        function showModal() {
            document.getElementById('copy-modal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('copy-modal').style.display = 'none';
        }
    </script>
<?php
    return ob_get_clean();
}
add_shortcode('htpasswd_generator', 'generate_htpasswd_shortcode');

// Ajax ハンドラー
function generate_htpasswd_ajax()
{
    if (!isset($_POST['user'], $_POST['pass'])) {
        echo '不正なリクエストです';
        wp_die();
    }

    $user = sanitize_text_field($_POST['user']);
    $pass = $_POST['pass'];
    $htpasswd = $user . ':' . password_hash($pass, PASSWORD_BCRYPT);

    echo '<p>生成結果：</p>';
    echo '<textarea id="htpasswd-output" rows="2" style="width:100%;" readonly>' . esc_html($htpasswd) . '</textarea><br>';
    echo '<button onclick="copyHtpasswd()">コピー</button>';

    wp_die();
}
add_action('wp_ajax_generate_htpasswd_ajax', 'generate_htpasswd_ajax');
add_action('wp_ajax_nopriv_generate_htpasswd_ajax', 'generate_htpasswd_ajax');
