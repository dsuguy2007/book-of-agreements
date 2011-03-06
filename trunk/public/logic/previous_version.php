<?php
$agr_id = intval($_REQUEST['agr_id']);
$version = intval($_REQUEST['prev_id']);

require_once 'logic/mysql_api.php';
global $HDUP;
$mysql = new MysqlApi($HDUP['host'], $HDUP['database'], $HDUP['user'],
	$HDUP['password']);

$older_filename = get_version_filename($agr_id, $version);
$summary = load_doc_by_version($mysql, $agr_id, $version, $older_filename);
echo $summary . "\n";

$version++;
$newer_filename = get_version_filename($agr_id, $version);
load_doc_by_version($mysql, $agr_id, $version, $newer_filename);
display_diff($older_filename, $newer_filename);

/**
 * Display the diff
 * @param[in] older_filename string, the name of the previous verions's temp
 *     file.
 * @param[in] newer_filename string, the name of the newer verions's temp file.
 */
function display_diff($older_filename, $newer_filename) {
	if (file_exists($older_filename) && file_exists($newer_filename)) {
		$diff = shell_exec("diff --unified=3 -b {$older_filename} {$newer_filename}");
		if (empty($diff)) {
			echo <<<EOHTML
		<div class="no_difference">
			<img src="display/images/tango/32x32/actions/format-indent-more.png"
				width="32" height="32"/>
			<img src="display/images/tango/32x32/actions/format-indent-less.png"
				width="32" height="32"/>
			There was no difference between these file versions.
		</div>

EOHTML;
			return;
		}

		$lines = explode("\n", $diff);
		foreach($lines as $index=>&$l) {
			if ((strpos($l, '---') === 0) ||
				(strpos($l, '+++') === 0)) {
				unset($lines[$index]);
				continue;
			}
			$l = trim($l);
			$l = str_replace('\r\n', "\n", $l);
			$l = str_replace('\n', "\n", $l);
			$l = str_replace('\r', "\n", $l);
			$l = wordwrap($l, 90);

			if (strpos($l, '-') === 0) {
				$l = "<span class=\"diff_removed\">{$l}</span>";
			}
			else if (strpos($l, '+') === 0) {
				$l = "<span class=\"diff_added\">{$l}</span>";
			}
		}
		$diff = implode("\n", $lines);

		echo <<<EOHTML
	<div id="diff">{$diff}</div>
EOHTML;
	}
}

/**
 * Get the filename for the temporary content dump to be used for diffing.
 */
function get_version_filename($agr_id, $version) {
	return "/tmp/book_of_agreements_{$agr_id}_{$version}";
}


/**
 * Load the document at a specific version and display the summary info.
 * @param[in] mysql MysqlApi object.
 * @param[in] agr_id int the agreement's ID number.
 * @param[in] version int the previous version ID.
 * @param[in] file string the constructed filename where to write out the
 *     temporary file contents.
 * @return string HTML to display above the diff summary.
 */
function load_doc_by_version($mysql, $agr_id, $version, $file) {
	$sql = <<<EOSQL
		SELECT * from agreements_versions where agr_id={$agr_id}
			AND agr_version_num={$version}
EOSQL;
	$data = $mysql->get($sql);
	$a = array_pop($data);

	$Agr = new Agreement();

	// if this isn't a previous version, but the current one, then simply load
	// the Agreement
	if (empty($a)) {
		$id = $_GET['agr_id'];
		$Agr->setId($id);
		$Agr->loadById();
	}
	else {
		$id = $a['agr_id'];
		$Agr->setId($id);
		$Agr->setContent($a['title'], $a['summary'], $a['full'],
			$a['background'], $a['comments'], $a['processnotes'], $a['cid'],
			$a['date'], $a['surpassed_by'], $a['expired'], $a['world_public']);
	}

	file_put_contents($file, $Agr->getTextVersion());

	$prev = '';
	if ($version > 1) {
		$prev_ver = $version - 1;
		$prev = <<<EOHTML
			<a href="/?id=previous_version&agr_id={$id}&prev_id={$prev_ver}">&larr;
				previous version ({$prev_ver})</a>
EOHTML;
	}

	return <<<EOHTML
		<h3>Diff summary for 
			"<a href="/?id=agreement&amp;num={$id}&amp;expand_diffs=1">{$a['title']}</a>":</h3>
		{$prev}
		
		<p>
		Updated: {$a['updated_date']}
		<br />Comment: {$a['diff_comment']}
		</p>
EOHTML;
}
?>
