<?php

// 関数ファイルを読み込む
require_once __DIR__ . '/../common/functions.php';

//セッション開始
session_start();

$current_user = '';
$id = 0;
$photo = '';
$description = '';
$upload_file = '';
$upload_tmp_file = '';
$errors = [];

$id = filter_input(INPUT_GET, 'id');

// セッションにidが保持されていなければログイン画面にリダイレクト
// パラメータを受け取れなけれらば一覧画面にリダイレクト
if (empty($_SESSION['current_user'])) {
    header('Location: ../users/login.php');
    exit;
} elseif (empty($id)) {
    header('Location: index.php');
    exit;
}

$current_user = $_SESSION['current_user'];
$photo = find_photo_by_id($id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = filter_input(INPUT_POST, 'description');
    // アップロードした画像のファイル名
    // 変更がない場合は画像は更新しない
    if (
        !empty($_FILES['image']['name']) &&
        $_FILES['image']['name'] != $photo['image']
    ) {
        $upload_file = $_FILES['image']['name'];
        // サーバー上で一時的に保存されるテンポラリファイル名
        $upload_tmp_file = $_FILES['image']['tmp_name'];
        $old_image = '../images/' . $photo['image'];
        $image_name = date('YmdHis') . '_' . $_FILES['image']['name'];
        $path = '../images/' . $image_name;
    }

    $errors = update_validate($description, $upload_file);

    if (
        empty($errors) &&
        update_photo($id, $description, $image_name)
    ) {

        if (
            !empty($upload_file) &&
            move_uploaded_file($upload_tmp_file, $path)
        ) {
            unlink($old_image);
        }

        header('Location: show.php?id=' . $id);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<?php include_once __DIR__ . '/../common/_head.html' ?>

<body>
    <?php include_once __DIR__ . '/../common/_header.php' ?>

    <main class="main_content content_center wrapper">
        <div class="form_flex">
            <?php include_once __DIR__ . '/../common/_errors.php' ?>

            <form action="" method="post" class="upload_content_form" enctype="multipart/form-data">
                <label for="file_upload" id="preview">
                    <img id="old_img" src="../images/<?= h($photo['image']) ?>">
                </label>
                <input class="input_file" type="file" id="file_upload" name="image">
                <textarea class="input_text" name="description" rows="5" placeholder="画像の詳細を入力してください" name="description"><?= h($photo['description']) ?></textarea>
                <div class="button">
                    <input type="submit" value="更 新" class="update_button">
                </div>
            </form>
        </div>
    </main>

    <?php include_once __DIR__ . '/../common/_footer.html' ?>

    <script src="../js/app.js"></script>
</body>

</html>
