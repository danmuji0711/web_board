<?php
include "conn.php";

// ==========================================
// 1. PHP 처리 로직 (Logic)
// ==========================================

$msg = ""; // 사용자에게 보여줄 메시지

// (1) 로그아웃 처리 (GET 방식)
if (isset($_GET['mode']) && $_GET['mode'] == 'logout') {
    session_destroy();
    header("Location: index.php?msg=logout_done");
    exit;
}

// (2) 폼 전송 처리 (POST 방식)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 어떤 폼인지 구분하기 위한 값
    $action = $_POST['action'];

    // --- A. 회원가입 처리 ---
    if ($action == 'register') {
        $uid = $_POST['uid'];
        $upw = $_POST['upw'];
        $uname = $_POST['uname'];

        if(!$uid || !$upw || !$uname) {
            header("Location: index.php?msg=empty");
            exit;
        }

        $sql = "INSERT INTO member (uid, upw, uname) VALUES ('$uid', '$upw', '$uname')";
        $result = mysqli_query($conn, $sql);

        if($result) {
            header("Location: index.php?msg=reg_ok");
        } else {
            header("Location: index.php?msg=fail");
        }
        exit;
    }

    // --- B. 로그인 처리 ---
    else if ($action == 'login') {
        $uid = $_POST['uid'];
        $upw = $_POST['upw'];

        if(!$uid || !$upw) {
            header("Location: index.php?msg=empty");
            exit;
        }

        $sql = "SELECT * FROM member WHERE uid='$uid' AND upw='$upw'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);

        if($row) {
            $_SESSION['userid'] = $row['uid'];
            $_SESSION['username'] = $row['uname'];
            header("Location: index.php?msg=login_ok");
        } else {
            header("Location: index.php?msg=login_fail");
        }
        exit;
    }

    // --- C. 방명록 글쓰기 처리 ---
    else if ($action == 'write') {
        // 로그인 체크
        if(!isset($_SESSION['userid'])) {
            header("Location: index.php?msg=need_login");
            exit;
        }

        $content = $_POST['content'];
        $writer = $_SESSION['username'];

        if(!$content) {
            header("Location: index.php?msg=empty");
            exit;
        }

        $sql = "INSERT INTO board (writer, content, wdate) VALUES ('$writer', '$content', now())";
        $result = mysqli_query($conn, $sql);

        if($result) {
            header("Location: index.php?msg=write_ok");
        } else {
            header("Location: index.php?msg=fail");
        }
        exit;
    }
}

// (3) 메시지 처리 (GET 방식)
if (isset($_GET['msg'])) {
    switch($_GET['msg']) {
        case 'logout_done': $msg = "로그아웃 되었습니다."; break;
        case 'empty': $msg = "빈칸을 채워주세요."; break;
        case 'reg_ok': $msg = "회원가입 완료! 로그인하세요."; break;
        case 'fail': $msg = "작업 실패 (오류 발생)"; break;
        case 'login_ok': $msg = "로그인 되었습니다."; break;
        case 'login_fail': $msg = "아이디/비번이 틀립니다."; break;
        case 'need_login': $msg = "로그인이 필요합니다."; break;
        case 'write_ok': $msg = "글 등록 완료."; break;
    }
}
?>

<!-- ==========================================
     2. 프론트엔드 화면 (HTML)
     ========================================== -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>방명록 페이지</title>
</head>
<body>

    <h1>방명록 페이지</h1>

    <!-- 알림 메시지 표시 -->
    <?php if($msg) { ?>
        <p style="color:blue; font-weight:bold;">[알림] <?php echo $msg; ?></p>
        <hr>
    <?php } ?>

    <!-- 1. 로그인 상태창 -->
    <div>
        <?php if(isset($_SESSION['userid'])) { ?>
            <!-- 로그인 했을 때 -->
            <h3>내 정보</h3>
            <p>
                <b><?php echo $_SESSION['username']; ?></b>님 환영합니다.
                <a href="index.php?mode=logout">[로그아웃]</a>
            </p>
        <?php } else { ?>
            <!-- 로그인 안 했을 때 -->
            <h3>로그인</h3>
            <form action="index.php" method="post">
                <input type="hidden" name="action" value="login"> <!-- 중요: 로그인임을 알림 -->
                아이디: <input type="text" name="uid" size="10"><br>
                비밀번호: <input type="password" name="upw" size="10"><br>
                <input type="submit" value="로그인">
            </form>
        <?php } ?>
    </div>
    <hr>

    <!-- 2. 회원가입 (로그인 안 했을 때만 표시) -->
    <?php if(!isset($_SESSION['userid'])) { ?>
        <div>
            <h3>회원가입</h3>
            <form action="index.php" method="post">
                <input type="hidden" name="action" value="register"> <!-- 중요: 회원가입임을 알림 -->
                아이디: <input type="text" name="uid" size="10"><br>
                비밀번호: <input type="password" name="upw" size="10"><br>
                닉네임: <input type="text" name="uname" size="10"><br>
                <input type="submit" value="가입하기">
            </form>
        </div>
        <hr>
    <?php } ?>

    <!-- 3. 방명록 -->
    <div>
        <h3>한줄 방명록</h3>
        
        <!-- 글쓰기 (로그인 했을 때만 표시) -->
        <?php if(isset($_SESSION['userid'])) { ?>
            <form action="index.php" method="post">
                <input type="hidden" name="action" value="write"> <!-- 중요: 글쓰기임을 알림 -->
                <textarea name="content" cols="60" rows="3" placeholder="내용 입력"></textarea><br>
                <input type="submit" value="남기기">
            </form>
        <?php } else { ?>
            <p>※ 글을 쓰려면 로그인이 필요합니다.</p>
        <?php } ?>

        <br>
        
        <!-- 목록 출력 -->
        <table border="1" width="100%" cellspacing="0" cellpadding="5">
            <tr bgcolor="#8e8e8eff">
                <th width="50">번호</th>
                <th width="100">작성자</th>
                <th>내용</th>
                <th width="150">작성일</th>
            </tr>
            <?php
            // DB에서 글 가져오기
            $sql = "SELECT * FROM board ORDER BY no DESC";
            $result = mysqli_query($conn, $sql);
            while($row = mysqli_fetch_array($result)) {
            ?>
            <tr>
                <td align="center"><?php echo $row['no']; ?></td>
                <td align="center"><?php echo $row['writer']; ?></td>
                <td><?php echo $row['content']; ?></td>
                <td align="center"><?php echo $row['wdate']; ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>

</body>
</html>