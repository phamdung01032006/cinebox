-- CineBox relational constraints migration
-- Goal: enforce PK/FK/unique/index relationships that are already implied in code.
-- Safe to run multiple times (idempotent helper procedures included).

USE `cinebox`;

SET FOREIGN_KEY_CHECKS = 0;

-- Align username column lengths so FK to users.username can be created
-- (users.username is VARCHAR(50) in current schema).
ALTER TABLE `wishlist`
  MODIFY `username` VARCHAR(50) NOT NULL;

ALTER TABLE `entityratings`
  MODIFY `username` VARCHAR(50) NOT NULL;

SET FOREIGN_KEY_CHECKS = 1;

DELIMITER $$

DROP PROCEDURE IF EXISTS add_index_if_not_exists $$
CREATE PROCEDURE add_index_if_not_exists(
    IN p_table_name VARCHAR(64),
    IN p_index_name VARCHAR(64),
    IN p_ddl TEXT
)
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.statistics
        WHERE table_schema = DATABASE()
          AND table_name = p_table_name
          AND index_name = p_index_name
    ) THEN
        SET @s = p_ddl;
        PREPARE stmt FROM @s;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END $$

DROP PROCEDURE IF EXISTS add_fk_if_not_exists $$
CREATE PROCEDURE add_fk_if_not_exists(
    IN p_table_name VARCHAR(64),
    IN p_fk_name VARCHAR(64),
    IN p_column_name VARCHAR(64),
    IN p_ref_table_name VARCHAR(64),
    IN p_ref_column_name VARCHAR(64),
    IN p_ddl TEXT
)
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.table_constraints
        WHERE constraint_schema = DATABASE()
          AND table_name = p_table_name
          AND constraint_name = p_fk_name
          AND constraint_type = 'FOREIGN KEY'
    ) AND NOT EXISTS (
        SELECT 1
        FROM information_schema.key_column_usage
        WHERE constraint_schema = DATABASE()
          AND table_name = p_table_name
          AND column_name = p_column_name
          AND referenced_table_name = p_ref_table_name
          AND referenced_column_name = p_ref_column_name
    ) THEN
        SET @s = p_ddl;
        PREPARE stmt FROM @s;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END $$

DELIMITER ;

-- -----------------------------
-- Unique/index constraints
-- -----------------------------

-- users
CALL add_index_if_not_exists(
  'users',
  'uq_users_username',
  'ALTER TABLE `users` ADD UNIQUE KEY `uq_users_username` (`username`)'
);
CALL add_index_if_not_exists(
  'users',
  'uq_users_email',
  'ALTER TABLE `users` ADD UNIQUE KEY `uq_users_email` (`email`)'
);

-- videos / entities / categories
CALL add_index_if_not_exists(
  'entities',
  'idx_entities_category',
  'ALTER TABLE `entities` ADD KEY `idx_entities_category` (`categoryId`)'
);
CALL add_index_if_not_exists(
  'videos',
  'idx_videos_entity',
  'ALTER TABLE `videos` ADD KEY `idx_videos_entity` (`entityId`)'
);

-- entitytags
CALL add_index_if_not_exists(
  'entitytags',
  'uq_entitytags_entity_tag',
  'ALTER TABLE `entitytags` ADD UNIQUE KEY `uq_entitytags_entity_tag` (`entityId`, `tagId`)'
);
CALL add_index_if_not_exists(
  'entitytags',
  'idx_entitytags_tag',
  'ALTER TABLE `entitytags` ADD KEY `idx_entitytags_tag` (`tagId`)'
);

-- entitycategories
CALL add_index_if_not_exists(
  'entitycategories',
  'uq_entitycategories_entity_category',
  'ALTER TABLE `entitycategories` ADD UNIQUE KEY `uq_entitycategories_entity_category` (`entityId`, `categoryId`)'
);
CALL add_index_if_not_exists(
  'entitycategories',
  'idx_entitycategories_category',
  'ALTER TABLE `entitycategories` ADD KEY `idx_entitycategories_category` (`categoryId`)'
);

-- entityratings
CALL add_index_if_not_exists(
  'entityratings',
  'uq_entityratings_user_entity',
  'ALTER TABLE `entityratings` ADD UNIQUE KEY `uq_entityratings_user_entity` (`username`, `entityId`)'
);
CALL add_index_if_not_exists(
  'entityratings',
  'idx_entityratings_entity',
  'ALTER TABLE `entityratings` ADD KEY `idx_entityratings_entity` (`entityId`)'
);
CALL add_index_if_not_exists(
  'entityratings',
  'idx_entityratings_username',
  'ALTER TABLE `entityratings` ADD KEY `idx_entityratings_username` (`username`)'
);

-- wishlist
CALL add_index_if_not_exists(
  'wishlist',
  'uq_wishlist_user_entity',
  'ALTER TABLE `wishlist` ADD UNIQUE KEY `uq_wishlist_user_entity` (`username`, `entityId`)'
);
CALL add_index_if_not_exists(
  'wishlist',
  'idx_wishlist_entity',
  'ALTER TABLE `wishlist` ADD KEY `idx_wishlist_entity` (`entityId`)'
);
CALL add_index_if_not_exists(
  'wishlist',
  'idx_wishlist_username',
  'ALTER TABLE `wishlist` ADD KEY `idx_wishlist_username` (`username`)'
);

-- videoprogress
CALL add_index_if_not_exists(
  'videoprogress',
  'uq_videoprogress_user_video',
  'ALTER TABLE `videoprogress` ADD UNIQUE KEY `uq_videoprogress_user_video` (`username`, `videoId`)'
);
CALL add_index_if_not_exists(
  'videoprogress',
  'idx_videoprogress_video',
  'ALTER TABLE `videoprogress` ADD KEY `idx_videoprogress_video` (`videoId`)'
);
CALL add_index_if_not_exists(
  'videoprogress',
  'idx_videoprogress_username',
  'ALTER TABLE `videoprogress` ADD KEY `idx_videoprogress_username` (`username`)'
);

-- user_subscriptions
CALL add_index_if_not_exists(
  'user_subscriptions',
  'idx_user_subscriptions_user',
  'ALTER TABLE `user_subscriptions` ADD KEY `idx_user_subscriptions_user` (`userId`)'
);
CALL add_index_if_not_exists(
  'user_subscriptions',
  'idx_user_subscriptions_plan',
  'ALTER TABLE `user_subscriptions` ADD KEY `idx_user_subscriptions_plan` (`planId`)'
);
CALL add_index_if_not_exists(
  'user_subscriptions',
  'idx_user_subscriptions_paypal_order',
  'ALTER TABLE `user_subscriptions` ADD KEY `idx_user_subscriptions_paypal_order` (`paypalOrderId`)'
);
CALL add_index_if_not_exists(
  'user_subscriptions',
  'idx_user_subscriptions_paypal_subscr',
  'ALTER TABLE `user_subscriptions` ADD KEY `idx_user_subscriptions_paypal_subscr` (`paypalSubscrId`)'
);

-- -----------------------------
-- Foreign keys
-- -----------------------------

CALL add_fk_if_not_exists(
  'entities',
  'fk_entities_category',
  'categoryId',
  'categories',
  'id',
  'ALTER TABLE `entities` ADD CONSTRAINT `fk_entities_category` FOREIGN KEY (`categoryId`) REFERENCES `categories`(`id`) ON DELETE CASCADE ON UPDATE CASCADE'
);

CALL add_fk_if_not_exists(
  'videos',
  'fk_videos_entity',
  'entityId',
  'entities',
  'id',
  'ALTER TABLE `videos` ADD CONSTRAINT `fk_videos_entity` FOREIGN KEY (`entityId`) REFERENCES `entities`(`id`) ON DELETE CASCADE ON UPDATE CASCADE'
);

CALL add_fk_if_not_exists(
  'entitytags',
  'fk_entitytags_entity',
  'entityId',
  'entities',
  'id',
  'ALTER TABLE `entitytags` ADD CONSTRAINT `fk_entitytags_entity` FOREIGN KEY (`entityId`) REFERENCES `entities`(`id`) ON DELETE CASCADE ON UPDATE CASCADE'
);
CALL add_fk_if_not_exists(
  'entitytags',
  'fk_entitytags_tag',
  'tagId',
  'tags',
  'id',
  'ALTER TABLE `entitytags` ADD CONSTRAINT `fk_entitytags_tag` FOREIGN KEY (`tagId`) REFERENCES `tags`(`id`) ON DELETE CASCADE ON UPDATE CASCADE'
);

CALL add_fk_if_not_exists(
  'entitycategories',
  'fk_entitycategories_entity',
  'entityId',
  'entities',
  'id',
  'ALTER TABLE `entitycategories` ADD CONSTRAINT `fk_entitycategories_entity` FOREIGN KEY (`entityId`) REFERENCES `entities`(`id`) ON DELETE CASCADE ON UPDATE CASCADE'
);
CALL add_fk_if_not_exists(
  'entitycategories',
  'fk_entitycategories_category',
  'categoryId',
  'categories',
  'id',
  'ALTER TABLE `entitycategories` ADD CONSTRAINT `fk_entitycategories_category` FOREIGN KEY (`categoryId`) REFERENCES `categories`(`id`) ON DELETE CASCADE ON UPDATE CASCADE'
);

CALL add_fk_if_not_exists(
  'entityratings',
  'fk_entityratings_entity',
  'entityId',
  'entities',
  'id',
  'ALTER TABLE `entityratings` ADD CONSTRAINT `fk_entityratings_entity` FOREIGN KEY (`entityId`) REFERENCES `entities`(`id`) ON DELETE CASCADE ON UPDATE CASCADE'
);
CALL add_fk_if_not_exists(
  'entityratings',
  'fk_entityratings_user',
  'username',
  'users',
  'username',
  'ALTER TABLE `entityratings` ADD CONSTRAINT `fk_entityratings_user` FOREIGN KEY (`username`) REFERENCES `users`(`username`) ON DELETE CASCADE ON UPDATE CASCADE'
);

CALL add_fk_if_not_exists(
  'wishlist',
  'fk_wishlist_entity',
  'entityId',
  'entities',
  'id',
  'ALTER TABLE `wishlist` ADD CONSTRAINT `fk_wishlist_entity` FOREIGN KEY (`entityId`) REFERENCES `entities`(`id`) ON DELETE CASCADE ON UPDATE CASCADE'
);
CALL add_fk_if_not_exists(
  'wishlist',
  'fk_wishlist_user',
  'username',
  'users',
  'username',
  'ALTER TABLE `wishlist` ADD CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`username`) REFERENCES `users`(`username`) ON DELETE CASCADE ON UPDATE CASCADE'
);

CALL add_fk_if_not_exists(
  'videoprogress',
  'fk_videoprogress_video',
  'videoId',
  'videos',
  'id',
  'ALTER TABLE `videoprogress` ADD CONSTRAINT `fk_videoprogress_video` FOREIGN KEY (`videoId`) REFERENCES `videos`(`id`) ON DELETE CASCADE ON UPDATE CASCADE'
);
CALL add_fk_if_not_exists(
  'videoprogress',
  'fk_videoprogress_user',
  'username',
  'users',
  'username',
  'ALTER TABLE `videoprogress` ADD CONSTRAINT `fk_videoprogress_user` FOREIGN KEY (`username`) REFERENCES `users`(`username`) ON DELETE CASCADE ON UPDATE CASCADE'
);

CALL add_fk_if_not_exists(
  'user_subscriptions',
  'fk_user_subscriptions_user',
  'userId',
  'users',
  'id',
  'ALTER TABLE `user_subscriptions` ADD CONSTRAINT `fk_user_subscriptions_user` FOREIGN KEY (`userId`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE'
);
CALL add_fk_if_not_exists(
  'user_subscriptions',
  'fk_user_subscriptions_plan',
  'planId',
  'plans',
  'id',
  'ALTER TABLE `user_subscriptions` ADD CONSTRAINT `fk_user_subscriptions_plan` FOREIGN KEY (`planId`) REFERENCES `plans`(`id`) ON DELETE SET NULL ON UPDATE CASCADE'
);

-- Cleanup helper procedures
DROP PROCEDURE IF EXISTS add_index_if_not_exists;
DROP PROCEDURE IF EXISTS add_fk_if_not_exists;
