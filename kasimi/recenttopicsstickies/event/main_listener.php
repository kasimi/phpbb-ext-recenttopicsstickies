<?php

/**
 *
 * Recent Topics - Stickies. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018 kasimi - https://kasimi.net
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kasimi\recenttopicsstickies\event;

use phpbb\db\driver\driver_interface;
use phpbb\event\data;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class main_listener implements EventSubscriberInterface
{
	/** @var driver_interface */
	protected $db;

	/** @var array */
	protected $sticky_topics = [
		1,
		2,
		3,
	];

	/**
	 * @param driver_interface $db
	 */
	public function __construct(
		driver_interface $db
	)
	{
		$this->db  = $db;
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		return [
			'paybas.recenttopics.sql_pull_topics_data' => 'recenttopics_sql_pull_topics_data',
			'paybas.recenttopics.sql_pull_topics_list'	=> 'recenttopics_sql_pull_topics_list',
		];
	}

	/**
	 * @param data $event
	 */
	public function recenttopics_sql_pull_topics_data(data $event)
	{
		if ($this->sticky_topics)
		{
			$sql_array = $event['sql_array'];
			$sql_array['ORDER_BY'] = 't.topic_id <> ' . implode(', t.topic_id <> ', $this->sticky_topics) . ', ' .  $sql_array['ORDER_BY'];
			$event['sql_array'] = $sql_array;
		}
	}

	/**
	 * @param data $event
	 */
	public function recenttopics_sql_pull_topics_list(data $event)
	{
		if ($this->sticky_topics)
		{
			$sql_array = $event['sql_array'];
			$sql_array['WHERE'] .= ' OR ' . $this->db->sql_in_set('t.topic_id', $this->sticky_topics);
			$sql_array['ORDER_BY'] = $this->db->sql_in_set('t.topic_id', $this->sticky_topics, true) . ', ' .  $sql_array['ORDER_BY'];
			$event['sql_array'] = $sql_array;
		}
	}
}
