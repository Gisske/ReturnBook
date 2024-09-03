/*
CREATE DATABASE IF NOT EXISTS db_library;

CREATE TABLE IF NOT EXISTS tb_member (
    m_user VARCHAR(40) NOT NULL PRIMARY KEY,
    m_pass VARCHAR(20) NOT NULL,
    m_name VARCHAR(50) NOT NULL,
    m_phone VARCHAR(10) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS tb_book (
    b_id VARCHAR(6) NOT NULL PRIMARY KEY,
    b_name VARCHAR(60) NOT NULL,
    b_writer VARCHAR(50) DEFAULT NULL,
    b_category TINYINT(2) NOT NULL,
    b_price INT(4) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS tb_borrow_book (
    br_date_br DATE NOT NULL,
    br_date_rt DATE NOT NULL,
    b_id VARCHAR(6) NOT NULL,
    m_user VARCHAR(40) NOT NULL,
    br_fine TINYINT(3) DEFAULT NULL,
    PRIMARY KEY (br_date_br, b_id, m_user),
    FOREIGN KEY (b_id) REFERENCES tb_book(b_id),
    FOREIGN KEY (m_user) REFERENCES tb_member(m_user)
);

INSERT INTO tb_member (m_user, m_pass, m_name, m_phone) VALUES
('member01', 'abc1111', 'สมหญิง จริงใจ', '0811111111'),
('member02', 'abc2222', 'สมชาย มั่นคง', '0822222222'),
('member03', 'abc3333', 'สมเกียรติเก่งกล้า', '0833333333'),
('member04', 'abc4444', 'สมสมรอิ่มเอม', '0844444444'),
('member05', 'abc5555', 'สมรักษ์ สะอาด', '0855555555');

INSERT INTO tb_borrow_book (br_date_br, br_date_rt, b_id, m_user, br_fine) VALUES
('2021-08-20', '2021-08-28', 'B00002', 'member03', 25),
('2021-08-21', '2021-08-22', 'B00001', 'member02', 0),
('2021-08-22', '2021-08-26', 'B00001', 'member02', 5),
('2021-08-23', '0000-00-00', 'B00003', 'member01', 0),
('2021-08-23', '0000-00-00', 'B00004', 'member05', 0);
*/