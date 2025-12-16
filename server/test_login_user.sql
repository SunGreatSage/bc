-- 测试彩票登录用户数据
-- 数据库: newbc
-- 使用方法: mysql -uroot -proot newbc < test_login_user.sql

-- 1. 查看 la_user 表结构
DESC la_user;

-- 2. 查看是否存在测试用户
SELECT id, username, nickname, mobile, status, create_time, login_time
FROM la_user
WHERE username = 'test001';

-- 3. 如果不存在，创建测试用户
-- 密码: 123456 (MD5: e10adc3949ba59abbe56e057f20f883e)
INSERT IGNORE INTO la_user (username, password, nickname, mobile, avatar, status, create_time, update_time)
VALUES ('test001', 'e10adc3949ba59abbe56e057f20f883e', '测试用户001', '', '', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 4. 获取用户ID
SET @user_id = (SELECT id FROM la_user WHERE username = 'test001');

-- 5. 创建账户（如果不存在）
INSERT IGNORE INTO la_user_account (user_id, balance, frozen_amount, total_recharge, total_withdraw, total_bet, total_prize, total_commission, status, version, created_at, updated_at)
VALUES (@user_id, 10000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 6. 创建用户扩展（如果不存在）
INSERT IGNORE INTO la_user_extend (user_id, parent_id, ancestor_ids, level, is_agent, agent_rate, total_downlines, direct_downlines, created_at, updated_at)
VALUES (@user_id, 0, '', 0, 0, 0.00, 0, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 7. 验证测试用户
SELECT
    u.id,
    u.username,
    u.nickname,
    u.mobile,
    u.status,
    FROM_UNIXTIME(u.create_time) as create_time,
    ua.balance,
    ua.total_bet,
    ua.total_prize
FROM la_user u
LEFT JOIN la_user_account ua ON u.id = ua.user_id
WHERE u.username = 'test001';

-- 完成提示
SELECT '✅ 测试用户创建完成！' as message,
       'username: test001' as username,
       'password: 123456' as password,
       'balance: 10000.00' as balance;
