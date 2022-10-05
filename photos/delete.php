<?php

// 関数ファイルを読み込む
require_once __DIR__ . '/../common/functions.php';

// セッション開始
session_start();

$id = 0;
$photo = '';
$old_image = '';

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

$photo = find_photo_by_id($id);
$old_image = '../images/' . $photo['image'];

if (delete_photo_by_id($id)) {
    // imagesフォルダに存在する画像の削除
    unlink($old_image);

    header('Location: index.php');
    exit;
} else {
    header('Location: show.php?id=' . $id);
    exit;
}
