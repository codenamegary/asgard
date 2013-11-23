<?php
namespace Coxis\Utils;

class Debug {
	public static function d() {
		if(!\Coxis\Core\Context::get('config')->get('debug'))
			return;
		while(ob_get_length())
			ob_end_clean();
			
		if(php_sapi_name() != 'cli')
			echo '<pre>';
		foreach(func_get_args() as $arg)
			var_dump($arg);
		if(php_sapi_name() != 'cli')
			echo '</pre>';
		
		die(static::getReport(debug_backtrace()));
	}

	public static function getReport($backtrace) {
		$r = '';
		if(php_sapi_name() === 'cli')
			$r .= static::getCLIBacktrace($backtrace);
		else {
			$r .= static::getHTMLBacktrace($backtrace);
			$r .= static::getHTMLRequest();
		}
		return $r;
	}
	
	public static function getHTMLBacktrace($backtrace=null) {
		if(!$backtrace)
			$backtrace = debug_backtrace();

		$jquery = Context::get('url')->to('js/jquery.js');
			
		$r = '<b>Backtrace</b><br>'."\n";
		$r .= <<<EOT
<script src="$jquery"></script>
<style>
pre { display:inline; }
.toggle { cursor:pointer; }
.current_line { display:inline-block; }
</style>
<script>
$(function(){
	$('.toggle').click(function(e) {
		if($(e.currentTarget).parent().find('div').first().css('display') == 'block') {
			$(e.currentTarget).parent().find('div').first().css('display', 'none');
			$(e.currentTarget).find('span').text('+');
		}
		else {
			$(e.currentTarget).parent().find('div').first().css('display', 'block');
			$(e.currentTarget).find('span').text('-');
		}
	});

	$('li pre').each(function() {
		var e = $(this);
		var isShort = true;
		var longText = e.text();
		var shortText = longText.split("\\n")[0];
		if(shortText.length < longText.length)
			shortText += '...';
		e.text(shortText);
		e.click(function() {
			if(isShort)
				e.text(longText);
			else
				e.text(shortText);
			isShort = !isShort;
		});
	});
});
</script>
EOT;
		for($i=0; $i<sizeof($backtrace); $i++) {
			$trace = $backtrace[$i];
			if(isset($backtrace[$i+1]))
				$next = $backtrace[$i+1];
			else
				$next = $backtrace[sizeof($backtrace)-1];
			
			if(isset($trace['file']))
				$r .= '<a href="code:'.$trace['file'].':'.$trace['line'].'">'.$trace['file'].'</a> ('.$trace['line'].')';
			if(isset($next['class']))
				$r .= ' at '.$next['class'].(isset($next['function']) ? $next['type'].$next['function'].'()':'');
			elseif(isset($next['function']))
				$r .= ' at '.$next['function'];
			$r .= "<br>\n";

			if(sizeof($next['args']) > 0) {
				$r .= '<div><span class="toggle"><span>+</span>Args:</span>'."<br>\n";
				$r .= '<div style="display:none"><ul>';
				foreach($next['args'] as $arg) {
					$r .= '<li>';
					if(is_array($arg))
						$str = \Coxis\Utils\Tools::var_dump_to_string($arg);
					elseif(is_string($arg))
						$str = $arg;
					else
						$str = \Coxis\Utils\Tools::var_dump_to_string($arg);
					$r .= '<pre>'.$str.'</pre>';
					$r .= "</li>\n";
				}
				$r .= '</ul></div>';
				$r .= '</div>';
			}
			
			if(isset($trace['line'])) {
				$start = $trace['line']-5-1;
				if($start < 1)
					$start = 1;
				$end = $trace['line']+5-1;
				$pos = $trace['line']-$start;

				if(file_exists($trace['file'])) {
					ob_start();
					highlight_string(file_get_contents($trace['file']));
					$code = ob_get_contents();
					ob_end_clean();
					$code = explode('<br />', $code);
					$code = array_slice($code, $start, 11);
					
					if($code) {
						$r .= '<div><span class="toggle"><span>+</span>Code:</span>'."<br>\n";
						$r .= '<div style="display:none"><code>';
						foreach($code as $k=>$line) {
							if($pos == $k+1)
								$r .= '<span style="float:left; display:inline-block; width:50px; color:#000">'.($start+$k+1).'</span>
								<div class="current_line" style="display:inline-block; background-color:#ccc;">'.$line.'</div><br>';
							else
								$r .= '<span style="float:left; display:inline-block; width:50px; color:#000">'.($start+$k+1).'</span>'.$line.'<br>';
						}
						$r .= '</code></div></div>';
					}
				}
			}
			
			$r .= '<hr/>';
		}

		return $r;
	}
	
	public static function getCLIBacktrace($backtrace=null) {
		if(!$backtrace)
			$backtrace = static::getBacktrace();
			
		$r = '';
		for($i=0; $i<sizeof($backtrace); $i++) {
			$trace = $backtrace[$i];
			if(isset($backtrace[$i+1]))
				$next = $backtrace[$i+1];
			else
				$next = $backtrace[sizeof($backtrace)-1];
			
			if(isset($trace['file']))
				$r .= $trace['file'].':'.$trace['line']."\n";
		}

		return $r;
	}

	public static function getHTMLRequest() {
		$r = Context::get('request');
		$res = '<b>Request</b><br>';
		$res .= '<div>';

		if($r->get->size()) {
			$res .= '<div><span class="toggle"><span>+</span>GET:</span>';
			$res .= '<div style="display:none"><ul>';
			foreach($r->get->all() as $k=>$v) {
				$res .= '<li>'.$k.': ';
				if(is_array($v))
					$str = \Coxis\Utils\Tools::var_dump_to_string($v);
				elseif(is_string($v))
					$str = $v;
				else
					$str = \Coxis\Utils\Tools::var_dump_to_string($v);
				$res .= '<pre>'.$str.'</pre>';
				$res .= "</li>\n";
			}
			$res .= '</ul></div></div>';
		}

		if($r->post->size()) {
			$res .= '<div><span class="toggle"><span>+</span>POST:</span>';
			$res .= '<div style="display:none"><ul>';
			foreach($r->post->all() as $k=>$v) {
				$res .= '<li>'.$k.': ';
				if(is_array($v))
					$str = \Coxis\Utils\Tools::var_dump_to_string($v);
				elseif(is_string($v))
					$str = $v;
				else
					$str = \Coxis\Utils\Tools::var_dump_to_string($v);
				$res .= '<pre>'.$str.'</pre>';
				$res .= "</li>\n";
			}
			$res .= '</ul></div></div>';
		}

		if($r->file->size()) {
			$res .= '<div><span class="toggle"><span>+</span>FILES:</span>';
			$res .= '<div style="display:none"><ul>';
			foreach($r->file->all() as $k=>$v) {
				$res .= '<li>'.$k.': ';
				if(is_array($v))
					$str = \Coxis\Utils\Tools::var_dump_to_string($v);
				elseif(is_string($v))
					$str = $v;
				else
					$str = \Coxis\Utils\Tools::var_dump_to_string($v);
				$res .= '<pre>'.$str.'</pre>';
				$res .= "</li>\n";
			}
			$res .= '</ul></div></div>';
		}

		if($r->cookie->size()) {
			$res .= '<div><span class="toggle"><span>+</span>COOKIES:</span>';
			$res .= '<div style="display:none"><ul>';
			foreach($r->cookie->all() as $k=>$v) {
				$res .= '<li>'.$k.': ';
				if(is_array($v))
					$str = \Coxis\Utils\Tools::var_dump_to_string($v);
				elseif(is_string($v))
					$str = $v;
				else
					$str = \Coxis\Utils\Tools::var_dump_to_string($v);
				$res .= '<pre>'.$str.'</pre>';
				$res .= "</li>\n";
			}
			$res .= '</ul></div></div>';
		}

		if($r->session->size()) {
			$res .= '<div><span class="toggle"><span>+</span>SESSION:</span>';
			$res .= '<div style="display:none"><ul>';
			foreach($r->session->all() as $k=>$v) {
				$res .= '<li>'.$k.': ';
				if(is_array($v))
					$str = \Coxis\Utils\Tools::var_dump_to_string($v);
				elseif(is_string($v))
					$str = $v;
				else
					$str = \Coxis\Utils\Tools::var_dump_to_string($v);
				$res .= '<pre>'.$str.'</pre>';
				$res .= "</li>\n";
			}
			$res .= '</ul></div></div>';
		}

		if($r->server->size()) {
			$res .= '<div><span class="toggle"><span>+</span>SERVER:</span>';
			$res .= '<div style="display:none"><ul>';
			foreach($r->server->all() as $k=>$v) {
				$res .= '<li>'.$k.': ';
				if(is_array($v))
					$str = \Coxis\Utils\Tools::var_dump_to_string($v);
				elseif(is_string($v))
					$str = $v;
				else
					$str = \Coxis\Utils\Tools::var_dump_to_string($v);
				$res .= '<pre>'.$str.'</pre>';
				$res .= "</li>\n";
			}
			$res .= '</ul></div></div>';
		}

		$res .= '</div>';
		return $res;
	}
}