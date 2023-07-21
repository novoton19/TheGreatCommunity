#https://stackoverflow.com/questions/26981901/mysql-insert-with-while-loop
#Dummy account password: Prase200#


DROP PROCEDURE IF EXISTS createUsers;
TRUNCATE TABLE users;
DELIMITER $$ 
CREATE PROCEDURE createUsers()
BEGIN
    DECLARE accountNum int DEFAULT 1;
    WHILE accountNum <= 50 DO
        INSERT INTO users (username, email, password) VALUES (CONCAT('test', accountNum), CONCAT('test', accountNum, '@test.com'), '$2y$10$NxMtF7a.MZqltGziB4rT6OoDVejtxL49ZK4mFi3WEBKjHqUdbvYCC');
        SET accountNum = accountNum + 1;
    END WHILE;
END $$
DELIMITER ;
CALL createUsers();
DROP PROCEDURE IF EXISTS createUsers;