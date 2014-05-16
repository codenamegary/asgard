<?php
namespace Asgard\Db\Tests;

class DALTest extends \PHPUnit_Framework_TestCase {
	protected $db;

	public static function setUpBeforeClass() {
		if(!defined('_ENV_'))
			define('_ENV_', 'test');

		$db = new \Asgard\Db\DB(array(
			'host' => 'localhost',
			'user' => 'root',
			'password' => '',
			'database' => 'asgard',
		));
		$db->import(__dir__.'/sql/dal.sql');
	}

/*
#new
setTable
	table
	addTable
	removeTable

select
	addSelect
	removeSelect
offset
limit
orderBy
groupBy
where

[http://dev.mysql.com/doc/refman/5.0/en/select.html
	having
	[ALL | DISTINCT | DISTINCTROW ]
]

leftjoin
	removeLeftjoin
rightjoin
	removeRightjoin
innerjoin
	removeInnerjoin
rsc
next
reset
query
first

paginate
get
update
insert
delete
count
min
max
avg
sum

	buildSQL
	buildInsertSQL
	buildDeleteSQL
*/
	protected function getDAL() {
		$db = new \Asgard\Db\DB(array(
			'host' => 'localhost',
			'user' => 'root',
			'password' => '',
			'database' => 'asgard',
		));
		if(!$this->db)
			$this->db = $db;
		return new \Asgard\Db\DAL($this->db);
	}

	public function test1() {
		/* TABLES */
		$this->assertEquals('SELECT * FROM `news`', $this->getDAL()->from('news')->buildSQL());
		$this->assertEquals('SELECT * FROM `news` `n`', $this->getDAL()->from('news n')->buildSQL());
		$this->assertEquals('SELECT * FROM `news`, `category` `c`', $this->getDAL()->from('news, category c')->buildSQL());
		$this->assertEquals('SELECT * FROM `news`, `category` `c`', $this->getDAL()->from('news')->addFrom('category c')->buildSQL());
		$this->assertEquals('SELECT * FROM `news` `n`', $this->getDAL()->from('news n, category c')->removeFrom('c')->buildSQL());

		/* SELECT / FROM */
		$this->assertEquals('SELECT `title` FROM `news`', $this->getDAL()->from('news')->select('title')->buildSQL());
		$this->assertEquals('SELECT `title` AS `t` FROM `news`', $this->getDAL()->from('news')->select('title t')->buildSQL());
		$this->assertEquals('SELECT COUNT(*) AS `c` FROM `news`', $this->getDAL()->from('news')->select('COUNT(*) c')->buildSQL());
		$this->assertEquals('SELECT `id` AS `i`, `title` AS `t` FROM `news`', $this->getDAL()->from('news')->select('id i, title t')->buildSQL());
		$this->assertEquals('SELECT `id` AS `i` FROM `news`', $this->getDAL()->from('news')->select('id i, title t')->removeSelect('t')->buildSQL());
		$this->assertEquals('SELECT `id` AS `i`, `title` AS `t` FROM `news`', $this->getDAL()->from('news')->select('id i')->addSelect('title t')->buildSQL());

		/* OFFSET */
		$this->assertEquals('SELECT * FROM `news` LIMIT 10, 18446744073709551615', $this->getDAL()->from('news')->offset(10)->buildSQL());

		/* LIMIT */
		$this->assertEquals('SELECT * FROM `news` LIMIT 10', $this->getDAL()->from('news')->limit(10)->buildSQL());

		/* BOTH */
		$this->assertEquals('SELECT * FROM `news` LIMIT 10, 20', $this->getDAL()->from('news')->offset(10)->limit(20)->buildSQL());

		/* ORDER BY */
		$this->assertEquals('SELECT * FROM `news` ORDER BY COUNT(*) ASC', $this->getDAL()->from('news')->orderBy('COUNT(*) ASC')->buildSQL());
		$this->assertEquals('SELECT * FROM `news` ORDER BY `id` ASC', $this->getDAL()->from('news')->orderBy('id ASC')->buildSQL());
		$this->assertEquals('SELECT * FROM `news` ORDER BY `news`.`id` ASC', $this->getDAL()->from('news')->orderBy('news.id ASC')->buildSQL());
		$this->assertEquals('SELECT * FROM `news` ORDER BY `id` ASC, `title` DESC', $this->getDAL()->from('news')->orderBy('id ASC, title DESC')->buildSQL());

		/* GROUP BY */
		$this->assertEquals('SELECT * FROM `news` GROUP BY COUNT(*)', $this->getDAL()->from('news')->groupBy('COUNT(*)')->buildSQL());
		$this->assertEquals('SELECT * FROM `news` GROUP BY `id`', $this->getDAL()->from('news')->groupBy('id')->buildSQL());
		$this->assertEquals('SELECT * FROM `news` GROUP BY `news`.`id`', $this->getDAL()->from('news')->groupBy('news.id')->buildSQL());
		$this->assertEquals('SELECT * FROM `news` GROUP BY `id`, `title`', $this->getDAL()->from('news')->groupBy('id, title')->buildSQL());

		/* WHERE */
		$this->assertEquals('SELECT * FROM `news` WHERE COUNT(*) NOT IN (SELECT * FROM tests)', $this->getDAL()->from('news')->where('COUNT(*) NOT IN (SELECT * FROM tests)')->buildSQL());
		$this->assertEquals('SELECT * FROM `news` WHERE `id`=4', $this->getDAL()->from('news')->where('id=4')->buildSQL());
		$this->assertEquals('SELECT * FROM `news` WHERE `id`=?', $this->getDAL()->from('news')->where('id=?', 4)->buildSQL());
		$this->assertEquals('SELECT * FROM `news` WHERE `id`=?', $this->getDAL()->from('news')->where('id', 4)->buildSQL());
		$this->assertEquals('SELECT * FROM `news` WHERE `id`=?', $this->getDAL()->from('news')->where(array('id' => 4))->buildSQL());
		$this->assertEquals('SELECT * FROM `news` WHERE `id`=?', $this->getDAL()->from('news')->where(array('id=?' => 4))->buildSQL());

		$dal = $this->getDAL()->from('news')->where(array(
			'id=?' => 4,
			'news.title LIKE ?' => '%test%'
		));
		$this->assertEquals('SELECT * FROM `news` WHERE `id`=? AND `news`.`title` LIKE ?', $dal->buildSQL());
		$this->assertEquals(array(4, '%test%'), $dal->getParameters());

		$dal = $this->getDAL()->from('news')->where(array(
			'or' => array(
				'id=?' => 4,
				'and' => array(
					'news.title LIKE ?' => '%test%',
					'news.content LIKE ?' => '%bla%'
				)
			)
		));
		$this->assertEquals('SELECT * FROM `news` WHERE `id`=? OR (`news`.`title` LIKE ? AND `news`.`content` LIKE ?)', $dal->buildSQL());
		$this->assertEquals(array(4, '%test%', '%bla%'), $dal->getParameters());

		$dal = $this->getDAL()->from('news')->where(array(
			array('news.title LIKE ?' => '%test%'),
			array('news.title LIKE ?' => '%bla%'),
		));
		$this->assertEquals('SELECT * FROM `news` WHERE `news`.`title` LIKE ? AND `news`.`title` LIKE ?', $dal->buildSQL());
		$this->assertEquals(array('%test%', '%bla%'), $dal->getParameters());

		/* LEFTJOIN, #rightjoin, #innerjoin (d'autres joins? voir le guide des jointures) */
		$this->assertEquals('SELECT * FROM `news` `n` LEFT JOIN `category` ON COUNT(*)=3', $this->getDAL()->from('news n')->leftjoin('category', 'COUNT(*)=3')->buildSQL());

		$dal = $this->getDAL()->from('news n')->leftjoin('category', array('COUNT(*)=?' => 3));
		$this->assertEquals('SELECT * FROM `news` `n` LEFT JOIN `category` ON COUNT(*)=?', $dal->buildSQL());
		$this->assertEquals(array(3), $dal->getParameters());

		$dal = $this->getDAL()->from('news n')->leftjoin('category', array('COUNT(*)=?' => 3));
		$this->assertEquals('SELECT * FROM `news` `n` LEFT JOIN `category` ON COUNT(*)=?', $dal->buildSQL());
		$this->assertEquals(array(3), $dal->getParameters());

		$this->assertEquals('SELECT * FROM `news` `n` LEFT JOIN `category` ON `category`.`id`=`news`.`id`', $this->getDAL()->from('news n')->leftjoin('category', 'category.id=news.id')->buildSQL());
		$this->assertEquals('SELECT * FROM `news` `n` LEFT JOIN `category` `c` ON `c`.`id`=`news`.`id`', $this->getDAL()->from('news n')->leftjoin('category c', 'c.id=news.id')->buildSQL());
		$this->assertEquals('SELECT * FROM `news` `n` LEFT JOIN `category` `c` ON `c`.`id`=`news`.`id`', $this->getDAL()->from('news n')->leftjoin('category c', array('c.id=news.id'))->buildSQL());

		$this->assertEquals('SELECT * FROM `news` `n`', $this->getDAL()->from('news n')->leftjoin('category c', array('c.id=news.id'))->removeJointure('c')->buildSQL());

		$this->assertEquals('SELECT * FROM `news` `n` LEFT JOIN `category` `c` ON `c`.`id`=`news`.`id` LEFT JOIN `tag` `t` ON `t`.`id`=`news`.`id` AND `t`.`id`=`news`.`id`', $this->getDAL()->from('news n')->leftjoin(array(
			'category c' => 'c.id=news.id',
			'tag t' => array(
				't.id=news.id',
				't.id=news.id',
			)
		))->buildSQL());

		/* NEXT, QUERY AND GET */
		$query = $this->getDAL()->query('SELECT * FROM news');
		$this->assertInstanceOf('Asgard\Db\Query', $query);
		$data = $query->all();
		$this->assertCount(3, $data);

		$dal = $this->getDAL()->from('news');
		$_data = array();
		while($n = $dal->next())
			$_data[] = $n;
		$this->assertEquals($data, $_data);

		/* RESET */
		$dal = $this->getDAL()->from('news')
			->select('test')
			->leftjoin('category1')
			->rightjoin('category2')
			->innerjoin('category3')
			->where('1=1')
			->orderBY('id ASC')
			->groupBY('id');
		$this->assertEquals(
			'SELECT `test` FROM `news` LEFT JOIN `category1` RIGHT JOIN `category2` INNER JOIN `category3` WHERE 1=1 GROUP BY `news`.`id` ORDER BY `news`.`id` ASC',
			$dal->buildSQL()
		);
		$this->assertEquals('SELECT * FROM `ble`', $dal->reset()->addFrom('ble')->buildSQL());

		/* FIRST */
		$this->assertEquals(1, $this->getDAL()->from('news')->first()['id']);

		/* PAGINATOR */
		$dal = $this->getDAL()->from('news');
		$dal->paginate(3, 10);
		$this->assertEquals('SELECT * FROM `news` LIMIT 20, 10', $dal->buildSQL());
		$this->assertInstanceOf('Asgard\Utils\Paginator', $dal->getPaginator());

		/* COUNT */
		$this->assertEquals(3, $this->getDAL()->from('news')->count());
		$res = $this->getDAL()->from('news')->count('category_id');
		$this->assertEquals(array(1=>'2', 2=>'1'), $res);

		/* MIN, MAX, AVG, SUM */
		$this->assertEquals(1, $this->getDAL()->from('news')->min('score'));
		$res = $this->getDAL()->from('news')->min('score', 'category_id');
		$this->assertEquals(array(1=>'2', 2=>'1'), $res);

		/* UPDATE */
		$dal = $this->getDAL()->from('news n')->where('id>?', 3)->orderBy('id ASC')->limit(5);
		$sql = $dal->buildUpdateSQL(array(
			'title' => 'bla',
			'content' => 'ble',
		));
		$this->assertEquals('UPDATE `news` `n` SET `title`=?, `content`=? WHERE `id`>? ORDER BY `id` ASC LIMIT 5', $sql);
		$this->assertEquals(array('bla', 'ble', 3), $dal->getParameters());
		$this->assertEquals(0, $dal->update(array('title' => 'bla', 'content' => 'ble')));

		$dal = $this->getDAL()->from('news n, category c')->where('id>?', 3)->orderBy('id ASC')->limit(5);
		$sql = $dal->buildUpdateSQL(array(
			'title' => 'bla',
			'content' => 'ble',
		));
		$this->assertEquals('UPDATE `news` `n`, `category` `c` SET `title`=?, `content`=? WHERE `id`>? ORDER BY `id` ASC LIMIT 5', $sql);
		$this->assertEquals(array('bla', 'ble', 3), $dal->getParameters());

		/* DELETE */
		$dal = $this->getDAL()->from('news n')->where('id>?', 3)->orderBy('id ASC')->limit(5);
		$this->assertEquals('DELETE FROM `news` WHERE `id`>? ORDER BY `id` ASC LIMIT 5', $dal->buildDeleteSQL());
		$this->assertEquals(array(3), $dal->getParameters());
		$this->assertEquals(0, $dal->delete());

		$dal = $this->getDAL()->from('news n, category c')->where('id>?', 3)->orderBy('id ASC')->limit(5);
		$this->assertEquals('DELETE FROM `news`, `category` WHERE `id`>? ORDER BY `id` ASC LIMIT 5', $dal->buildDeleteSQL());
		$this->assertEquals(array(3), $dal->getParameters());

		/* INSERT */
		$dal = $this->getDAL()->into('news');
		$this->assertEquals('INSERT INTO `news` (`id`) VALUES (?)', $dal->buildInsertSQL(array('id'=>5)));
		$this->assertEquals(array(5), $dal->getParameters());
		$this->assertEquals(5, $dal->insert(array('id'=>5)));

		/* EXCEPTION */
		$this->setExpectedException('Exception');
		$this->getDAL()->get();
	}
}