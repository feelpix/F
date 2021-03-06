<?php
// @codeCoverageIgnoreStart
require_once 'F/Technical/Translate/Service.php';
require_once 'F/Technical/Trace/Service.php';
/**
 * Show var content
 *
 * @param mixed $v var to watch
 * @param bool  $exit exit(true by default)
 * @param bool  $dump add var type (false by default)
 */
function f_dbg($v, $exit = true, $dump = false)
{
	$calledFrom = debug_backtrace();
	$calledFrom = "\n=== DEBUG FROM ". substr($calledFrom[0]['file'], 1) .' (line ' . $calledFrom[0]['line'].")\n\n";
	if (true === $dump) {
		if ( !isset($_SERVER['PROMPT']) ) {
		  //header('Content-Type: text/html');
		}
		echo $calledFrom;
		var_dump($v);
	} else {
		if ( !isset($_SERVER['PROMPT']) ) {
		  //header('Content-Type: text/plain');
		}
		echo $calledFrom;
		print_r($v);
	}

	echo "\n\n=== FIN DEBUG \n";

	if (true === $exit) {
		exit(-1);
	}
}

/**
 * usefull for integration test show result and json_encode
 *
 * @param $v
 */
function f_dbgTest($actual)
{
	f_dbg(array('result' => $actual, 'json' => json_encode($actual)));
}

/**
 * Raccourcie vers le service de traduction
 *
 * @param string $key clef
 * @param mixed $args arguements si nécessaires
 */
function t($key, $args=null)
{
	return \F\Technical\Translate\Service::singleton()->translate($key, $args);
}

/**
 * trace message
 *
 * @param string $key
 * @param mixed $params
 */
function f_trace($key, $params=null)
{
	return \F\Technical\Trace\Service::singleton()->trace($key, $params);
}

/**
 * active or desactive trace
 *
 * @param bool $activated
 * @param string $file
 */
function f_trace_out($activated=true, $file = "'php://stdout'")
{

	$config = array('file' => 'php://stdout', 'activated' => (true === $activated?1:0));
	\F\Technical\Trace\Service::singleton()->configure($config);
}

/**
 * Remove all accentuation from a string
 *
 * @param string $str      string
 * @return string the string without all the accents
 */
function stripAccent($str)
{
	$str = htmlentities($str, ENT_NOQUOTES, 'utf-8');

	$str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
	$str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
	$str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
	return $str;
}
// @codeCoverageIgnoreEnd