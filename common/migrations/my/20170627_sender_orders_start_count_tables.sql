CREATE TABLE `sender_orders` (
  `id` int(11) NOT NULL,
  `panel_id` int(11) NOT NULL,
  `panel_db` varchar(300) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `start_count`
--

CREATE TABLE `start_count` (
  `id` int(11) NOT NULL,
  `db` varchar(500) NOT NULL,
  `pid` int(11) NOT NULL,
  `oid` int(11) NOT NULL,
  `link` varchar(1000) NOT NULL,
  `status` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `data` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sender_orders`
--
ALTER TABLE `sender_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status` (`status`),
  ADD KEY `panel_id` (`panel_id`,`status`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `panel_id_2` (`panel_id`),
  ADD KEY `panel_id_3` (`panel_id`,`order_id`);

--
-- Indexes for table `start_count`
--
ALTER TABLE `start_count`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status` (`status`),
  ADD KEY `pid` (`pid`,`oid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sender_orders`
--
ALTER TABLE `sender_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `start_count`
--
ALTER TABLE `start_count`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;