
INSERT INTO `settings` (`name`, `value`, `shown_name_ar`, `shown_name_en`, `input_type`, `option_list`, `group_name`, `sort`, `created_at`, `updated_at`) VALUES
('font_size', '14', 'حجم خط الجداول', 'font size', 'text', NULL, 'style', 1, '2017-12-13 22:00:00', '2019-12-09 10:03:11'),
('font_weight', 'normal', 'سمك خط الجداول', 'table font weight', 'select', 'a:2:{s:6:\"normal\";s:6:\"normal\";s:4:\"bold\";s:4:\"bold\";}', 'style', 0, NULL, '2019-12-09 10:03:11'),
('menu_font_size', '14', 'حجم الخط القائمه', 'Menu font size', 'text', NULL, 'style', 2, NULL, '2019-12-09 10:03:11'),
('menu_font_weight', 'normal', 'سمك خط القائمه', 'Menu font weight', 'select', 'a:2:{s:6:\"normal\";s:6:\"normal\";s:4:\"bold\";s:4:\"bold\";}', 'style', 0, NULL, '2019-12-09 10:03:11');

ALTER TABLE `requests` ADD `property_model_id` VARCHAR(100) NULL AFTER `currency`;

ALTER TABLE `areas` ADD `has_property_model` TINYINT(1) NOT NULL DEFAULT '0' AFTER `longitude`;
