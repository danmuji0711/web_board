
CREATE DATABASE IF NOT EXISTS web_board DEFAULT CHARACTER SET utf8;

-- 데이터베이스 선택
USE web_board;

-- 1. 회원 테이블 (member)
-- no: 고유 번호 (자동 증가)
-- uid: 아이디
-- upw: 비밀번호
-- uname: 닉네임 (이름)
CREATE TABLE IF NOT EXISTS member (
    no INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(20) NOT NULL,
    upw VARCHAR(20) NOT NULL,
    uname VARCHAR(20) NOT NULL
);

-- 2. 방명록 테이블 (board)
-- no: 고유 번호 (자동 증가)
-- writer: 작성자 (회원 닉네임 저장)
-- content: 내용
-- wdate: 작성일시
CREATE TABLE IF NOT EXISTS board (
    no INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    writer VARCHAR(20) NOT NULL,
    content TEXT NOT NULL,
    wdate DATETIME NOT NULL
);
