CREATE TABLE `ticket_notes` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `note` varchar(1000) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `ticket_messages`
--
ALTER TABLE `ticket_notes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `ticket_messages`
--
ALTER TABLE `ticket_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

ALTER TABLE `ticket_notes` MODIFY note varchar(1000) CHARACTER SET utf8;