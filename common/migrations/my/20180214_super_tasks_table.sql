CREATE TABLE super_tasks (
  id int(11) NOT NULL,
  task tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - restart nginx',
  status tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - pending, 1 - completed, 3 - error',
  created_at int(11) NOT NULL DEFAULT '0',
  done_at int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE super_tasks
  ADD PRIMARY KEY (id);

ALTER TABLE super_tasks
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;