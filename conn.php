<?php
// conn.php : DB 연결하고 세션 시작하는 공통 파일

$conn = mysqli_connect("localhost", "root", "wjsansrk", "php_homework");

if (!$conn) {
    die("DB 연결 실패 ㅠㅠ : " . mysqli_connect_error());
}

// 한글 깨짐 방지
mysqli_query($conn, "set session character_set_connection=utf8;");
mysqli_query($conn, "set session character_set_results=utf8;");
mysqli_query($conn, "set session character_set_client=utf8;");

// 세션 시작 (로그인 정보 유지하려고)
session_start();
?>