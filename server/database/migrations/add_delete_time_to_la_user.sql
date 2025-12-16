-- 为 la_user 表添加 delete_time 字段（软删除支持）
-- 执行时间: 2025-12-11

ALTER TABLE `la_user`
ADD COLUMN `delete_time` INT(10) UNSIGNED DEFAULT NULL COMMENT '删除时间' AFTER `update_time`;

-- 添加索引
ALTER TABLE `la_user`
ADD INDEX `idx_delete_time` (`delete_time`);
