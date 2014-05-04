<?php
/**
* @file mod_pager.php
* @synopsis  分页模块
* @author Yee, <assad2008@sina.com>
* @version 1.0
* @date 2012-04-18 16:35:24
 */

!defined('PATH_ADMIN') &&exit('Forbidden');
class mod_pager
{

	public static $default_pages = 15;

	public static function get_page_number_list($total, $start, $page_rows)
	{
		if ($total < 1 || $start < 0 || $page_rows < 1) return false;

		// 非首页和尾页时，当前页两边最少显示的页数
		$least = (self::$default_pages - 5) / 2;

		if ($start < $page_rows) $start = 0;
		if ($start >= $total) $start = $total - 1;
		$page_num = ceil($total/$page_rows);

		$current_page = ceil($start/$page_rows);
		if ($start%$page_rows == 0) $current_page++;
		if ($current_page < 1) $current_page = 1;
		$output = array();
		// 如果小于显示默认显示页数
		if ($page_num <= self::$default_pages) 
		{
			// 上一页
			$prev = ($current_page - 2) * $page_rows;
			if ($prev < 0) $prev = 0;
			if ($start > 0) $output['prev'] = $prev;
			for ($i=0; $i<$page_num; ++$i) 
			{
				$tmp = $i * $page_rows;
				$t = $i + 1;
				if ($t == $current_page) $output[$t] = -1;
				else $output[$t] = $tmp;
			}
			// 下一页
			$next = $current_page * $page_rows;
			if ($next < $total) $output['next'] = $next;
		}else 
		{
			// 如果需要省略某些页
			if ($current_page - $least - 1 > 1 || $current_page + $least + 1 < $page_num) 
			{
				// 上一页
				$prev = ($current_page - 2) * $page_rows;
				if ($prev < 0) $prev = 0;
				if ($start > 0) $output['prev'] = $prev;
				for ($i = 0; $i < $page_num; ++$i) 
				{
					$tmp = $i * $page_rows;
					$t = $i + 1;
					if ($t < $current_page - $least && $page_num - self::$default_pages + 2 > $t && $t != 1) {
						$output['omitf'] = true;
						continue;
					}
					if ($t > $current_page + $least && $t > self::$default_pages - 1 && $t != $page_num) {
						$output['omita'] = true;
						continue;
					}
					if ($t == $current_page) $output[$t] = -1;
					else $output[$t] = $tmp;
				}
				// 下一页
				$next = $current_page * $page_rows;
				if ($next < $total) $output['next'] = $next;
			} else {
				// 上一页
				$prev = ($current_page - 2) * $page_rows;
				if ($prev < 0) $prev = 0;
				if ($start > 0) $output['prev'] = $prev;
				for ($i=0; $i<$page_num; ++$i) 
				{
					$tmp = $i * $page_rows;
					$t = $i + 1;
					if ($t == $current_page) $output[$t] = -1;
					else $output[$t] = $tmp;
				}
				// 下一页
				$next = $current_page * $page_rows;
				if ($next < $total) $output['next'] = $next;
			}
		}
		$output['numtotal'] = $total;
		return $output;
	}

}
?>
