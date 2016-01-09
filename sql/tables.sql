CREATE TABLE `media` (
  `id` int(11) NOT NULL,
  `title` varchar(500) DEFAULT NULL,
  `url` varchar(500) NOT NULL,
  `preview_image` varchar(500) DEFAULT NULL,
  `video_url` varchar(500) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `media`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `queue` (
  `id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `queue` CHANGE `status` `status` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `queue`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `queue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;




CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `content` varchar(255) NOT NULL,
  `accepted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;