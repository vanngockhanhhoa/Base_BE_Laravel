<div class="password">
    <p><?= $user->name ?>様</p>
    <br/>
    <p>パスワードリセットを承りました。</p>
    <p>下記のボタンをクリックしてパスワードをリセットしてください。</p>
    <br/>
    <p><a class="button" href="<?=  $resetPasswordUrl ?>">Reset Password</a></p>
    <br/>
    <p>※認証URLの有効期限は24時間です。</p>
    <p>※「パスワードをリセット」ボタンを押下できない場合、下記のURLをコピーしてブラウザに貼り付けてください。</p>
    <p><?= $resetPasswordUrl ?></p>
</div>
<style>
    .button {
        text-decoration: none;
        background-color: rgb(29, 132, 155);
        color: #fff;
        padding: 6px 12px 6px 12px;
        font-weight: bold;
        border-radius: 4px;
        font-size: 14px;
        text-align: center;
    }
</style>
