<?php

# ----------------------------------------------------
# Globally defined functions - useful across the site
# ----------------------------------------------------
$Months = array(
	1=>'January',
	2=>'February',
	3=>'March',
	4=>'April',
	5=>'May',
	6=>'June',
	7=>'July',
	8=>'August',
	9=>'September',
	10=>'October',
	11=>'November',
	12=>'December'
);

function format_html( $s, $keep_eol=false )
{
	# convert all newlines to \n
	$s = preg_replace( "/\\\\r\\\\n|\\\\r|\\\\n/", "\n", $s );

	# escape any html characters
	$s = htmlentities( $s );

	# convert escaped characters to actual tabs
	$s = str_replace('&amp;#160;', "&nbsp;&nbsp;&nbsp;&nbsp;", $s );
	$s = str_replace('&amp;quot;', '"', $s);
	$s = str_replace('&amp;amp;', '&amp;', $s);
	$s = str_replace('&amp;gt;', '&gt;', $s);
	$s = str_replace('&amp;lt;', '&lt;', $s);
	$s = str_replace('&amp;#', '&#', $s);

	# whether to keep newlines, so this wraps
	if ( !$keep_eol ) {
		$s = nl2br( $s );
	}

	return stripslashes( $s );
}

function format_email( $s ) {
	# convert all newlines to \n
	$s = preg_replace( "/\\\\r\\\\n|\\\\r/", "\n", $s );
	return stripslashes($s);
}

/**
 * Clean up user-supplied input, replacing certain characters for others within
 * the first 128 ascii characters.
 *
 * @param[in] str string input string to be sanitized
 * @return string the sanitized input
 */
function clean_html($str) { 
    # Quotes cleanup 
    $str = str_replace( chr(ord("`")), "'", $str );        # ` 
    $str = str_replace( chr(ord("´")), "'", $str );        # ´ 
    $str = str_replace( chr(ord("„")), ",", $str );        # „ 
    $str = str_replace( chr(ord("`")), "'", $str );        # ` 
    $str = str_replace( chr(ord("´")), "'", $str );        # ´ 
    $str = str_replace( chr(ord("“")), "\"", $str );        # “ 
    $str = str_replace( chr(ord("”")), "\"", $str );        # ” 
    $str = str_replace( chr(ord("´")), "'", $str );        # ´ 

    $unwanted_array = array(
		'Š'=>'S',
		'š'=>'s',
		'Ž'=>'Z',
		'ž'=>'z',
		'À'=>'A',
		'Á'=>'A',
		'Â'=>'A',
		'Ã'=>'A',
		'Ä'=>'A',
		'Å'=>'A',
		'Æ'=>'A',
		'Ç'=>'C',
		'È'=>'E',
		'É'=>'E',
		'Ê'=>'E',
		'Ë'=>'E',
		'Ì'=>'I',
		'Í'=>'I',
		'Î'=>'I',
		'Ï'=>'I',
		'Ñ'=>'N',
		'Ò'=>'O',
		'Ó'=>'O',
		'Ô'=>'O',
		'Õ'=>'O',
		'Ö'=>'O',
		'Ø'=>'O',
		'Ù'=>'U',
		'Ú'=>'U',
		'Û'=>'U',
		'Ü'=>'U',
		'Ý'=>'Y',
		'Þ'=>'B',
		'ß'=>'Ss',
		'à'=>'a',
		'á'=>'a',
		'â'=>'a',
		'ã'=>'a',
		'ä'=>'a',
		'å'=>'a',
		'æ'=>'a',
		'ç'=>'c',
		'è'=>'e',
		'é'=>'e',
		'ê'=>'e',
		'ë'=>'e',
		'ì'=>'i',
		'í'=>'i',
		'î'=>'i',
		'ï'=>'i',
		'ð'=>'o',
		'ñ'=>'n',
		'ò'=>'o',
		'ó'=>'o',
		'ô'=>'o',
		'õ'=>'o',
		'ö'=>'o',
		'ø'=>'o',
		'ù'=>'u',
		'ú'=>'u',
		'û'=>'u',
		'ý'=>'y',
		'ý'=>'y',
		'þ'=>'b',
		'ÿ'=>'y',
	); 
    $str = strtr($str, $unwanted_array); 

    # Bullets, dashes, and trademarks 
    $str = str_replace( chr(149), "&#8226;", $str );   # bullet • 
    $str = str_replace( chr(150), "&ndash;", $str );   # en dash 
    $str = str_replace( chr(151), "&mdash;", $str );   # em dash 
    $str = str_replace( chr(153), "&#8482;", $str );   # trademark 
    $str = str_replace( chr(169), "&copy;", $str );    # copyright mark 
    $str = str_replace( chr(174), "&reg;", $str );     # registration mark 

    return $str; 
}



# ----------------------------------------------------

class MyDate
{
	var $curyear;
	var $year;
	var $month;
	var $day;
	var $label;

	# MyDate
	function MyDate( $year='', $month='', $day='', $label=NULL)
	{
		$this->curyear = date('Y');
		$this->year = is_int( $year ) ? $year : $this->curyear;
		$this->month = is_int( $month ) ? $month : date('n');
		$this->day = is_int( $day ) ? $day : date('j');
		$this->label = $label;
	}

	function setDate( $date_string )
	{
		if ( preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $date_string, $Matches )) {
			$this->year = $Matches[1];
			$this->month = $Matches[2];
			$this->day = $Matches[3];
		}
	}

	function toString( )
	{
		return sprintf( "%04d-%02d-%02d", $this->year, 
			$this->month, $this->day );
	}

	function selectDate( )
	{
		global $Months;
		$disp_label = !is_null($this->label) ? 
			ucfirst($this->label) . ' ' : '';

		# create day drop-down
		$days = '';
		for ( $i=1; $i<=31; $i++ ) {
			$sel = ( $i == $this->day ) ? ' selected="selected"' : '';
			$days .= "<option value=\"{$i}\"{$sel}>{$i}</option>\n";
		}

		#create month drop-down
		$months = '';
		foreach( $Months as $num=>$m )
		{
			$sel = ( $num == $this->month ) ? ' selected="selected"' : '';
			$months .= "<option value=\"{$num}\"{$sel}>{$m}</option>\n";
		}

		#create year drop-down
		$years = '';
		for ( $i=2001; $i<=$this->curyear; $i++ ) {
			$sel = ( $i == $this->year ) ? ' selected="selected"' : '';
			$years .= "<option value=\"{$i}\"{$sel}>{$i}</option>\n";
		}

		return <<<EOHTML
		<p>{$disp_label}Date:
		<select name="{$this->label}day" size="1">{$days}</select>
		<select name="{$this->label}month" size="1">{$months}</select>
		<select name="{$this->label}year" size="1">{$years}</select>
		</p>
EOHTML;
	}
}

class Committee {
	var $cnum;
	var $cid;

	# Committee
	function Committee( $id='' ) {
		if ($id != '') {
			$this->setId($id);
		}
	}

	function setId($cid) {
		$this->cid = $cid;
	}

	# Committee
	function selectCommittee( $cid='' )
	{
		global $Cmtys;
		global $SubCmtys;

		$out = "<p>Committee:\n".'<select name="cid" size="1">';
		foreach( $Cmtys as $cmty_num=>$c ) {
			if ( $this->cnum == $cmty_num ) {
				$out .= '<option value="'.$cmty_num.'" selected="selected">'.
					"$c</option>\n";
			}
			else {
				$out .= '<option value="'.$cmty_num.'">'."$c</option>\n";
			}

			if ( isset( $SubCmtys[$cmty_num] )) {
				foreach( $SubCmtys[$cmty_num] as $scmty_num=>$sc ) {
					if ( $this->cnum == $scmty_num ) {
						$out .= '<option value="' . $scmty_num . 
							'" selected="selected">' . "$c:$sc</option>\n";
					}
					else {
						$out .= '<option value="' . $scmty_num . '">' . 
							"$c:$sc</option>\n";
					}
				}
			}

		}
		$out .= '</select></p>';

		return $out;
	}

	# Committee
	function getName() {
		if (is_null($this->cid)) {
			return;
		}

		global $Cmtys;
		global $SubCmtys;
		$name = '';

		$id = $this->cid;
		if ( isset( $Cmtys[$id] )) {
			return $Cmtys[$id];
		}
		foreach( $SubCmtys as $major => $Sub ) {
			if ( isset( $Sub[$id] )) {
				return $Cmtys[$major] . ': ' .$Sub[$id];
			}
		}

		echo <<<EOHTML
			<div class="error">Error! Could not find requested committee</div>
EOHTML;
		exit;
	}
}

/**
 * Parent class to both Agreements and Minutes
 */
class BOADoc {
	var $mysql;
	var $cmty;

	var $is_print_version = FALSE;

	function BOADoc() {
		global $HDUP;

		require_once 'logic/mysql_api.php';
		$this->mysql = new MysqlApi($HDUP['host'], $HDUP['database'],
			$HDUP['user'], $HDUP['password']);

		$this->cmty = new Committee();

		global $print_version;
		$this->is_print_version = $print_version;
	}
}

/**
 * Agreements
 */
class Agreement extends BOADoc
{
	var $doc_type = 'agreement';
	var $id = null;
	var $title = null;
	var $summary = null;
	var $full = null;
	var $background = null;
	var $comments = null;
	var $processnotes = null;
	var $cid = null;
	var $Date;
	var $surpassed_by;
	var $expired;
	var $search_points = 0;
	var $found = '';
	var $world_public = false;
	var $found_summary = false;

	var $diff_comments;
	var $previous_versions;

	var $diff_context = 5;

	// agreement id, version
	var $filename_format = '/tmp/book_of_agreements_%s_%s';

	# agreement
	function Agreement() {
		parent::BOADoc();
		$this->Date = new MyDate();
		$this->processRequest();
	}

	/**
	 * Process input from the POST.
	 */
	function processRequest() {
		if (isset($_REQUEST['num'])) {
			$this->id = intval($_REQUEST['num']);

			# if potentially valid id num
			if ( $this->id > 0) {
				$this->loadById( );
			}
		}

		if (isset($_REQUEST['diff_comments'])) {
			$this->diff_comments = mysql_real_escape_string(
				$_REQUEST['diff_comments']);
		}
	}

	function setId($id) {
		$this->id = $id;
	}

	function setContent($t='', $s='', $f='', $b='', $c='', 
			$p='', $c_id='', $D='', $sb=0, $x='', $wp=false ) {
		$this->title = $t;
		$this->summary = $s;

		$f = str_replace('\r\n', "\n", $f);
		$f = str_replace('\n', "\n", $f);
		$f = str_replace('\r', "\n", $f);
		$this->full = $f;

		$this->background = $b;
		$this->comments = $c;
		$this->processnotes = $p;
		$this->cid = $c_id;
		$this->Date = $D;
		$this->surpassed_by = $sb;
		$this->expired = $x;
		$this->world_public = $wp;

		if ( !is_object( $D )) {
			$this->Date = new MyDate( );
		}

		if ($c_id != '') {
			$this->cmty->setId($c_id);
		}
	}

	function getId() {
		return $this->id;
	}

	# agreement
	function loadById( ) {
		global $PUBLIC_USER;

		if (!is_numeric($this->id)) {
			error_log("loadById was called with an invalid ID: {$this->id}");
			exit;
		}

		global $HDUP;
		global $G_DEBUG;
		$entryDate = new MyDate( );

		$pub_constraint = '';
		if ( $PUBLIC_USER ) {
			$pub_constraint = ' and agreements.world_public=1';
		}

		$sql = <<<EOSQL
			select committees.cmty, agreements.* from agreements, committees
			where agreements.id={$this->id} and committees.cid=agreements.cid
EOSQL;

/*
try mixing relevance in the SQL query...

SELECT *, ( (1.3 * (MATCH(title) AGAINST ('+term +term2' IN BOOLEAN MODE))) +
(0.6 * (MATCH(text) AGAINST ('+term +term2' IN BOOLEAN MODE))) ) AS relevance
FROM [table_name] WHERE ( MATCH(title,text) AGAINST ('+term +term2' IN BOOLEAN
MODE) ) HAVING relevance > 0 ORDER BY relevance DESC;
*/

		$data = my_getInfo( $G_DEBUG, $HDUP, $sql.$pub_constraint );
		if ( empty( $data )) {
			if ( $PUBLIC_USER ) {
				if (attempt_login()) {
					# run the query again, without the constraint
					$data = my_getInfo( $G_DEBUG, $HDUP, $sql );
				}
				else {
					return FALSE;
				}
			}
		}
		$data = array_pop($data);

		# if still empty... then punt
		if ( empty( $data )) {
			return FALSE;
		}

		$entryDate->setDate( $data['date'] );
		$this->setContent(
			$data['title'],
			$data['summary'],
			$data['full'],
			$data['background'],
			$data['comments'],
			$data['processnotes'],
			$data['cid'],
			$entryDate,
			$data['surpassed_by'],
			$data['expired'],
			$data['world_public']
		);
	}

	/**
	 * Validate the content for this agreement.
	 */
	function isValid( )
	{
		if (($this->id != 0) && empty($this->diff_comments)) {
			return FALSE;
		}

		# don't look for an id, allowing for "add"
		return (!empty($this->title) && !empty($this->full));
	}

	function actionChoices( )
	{
		if ( !$this->isValid( )) {
			return NULL;
		}

		$exp = ( $this->expired == 1 ) ? ' checked="checked"' : '';
		$spass = ( $this->surpassed_by > 0 ) ?
			' value="' . $this->surpassed_by . '"' : '';

		# special options go here
		echo <<<EOHTML
			<p>
				This agreement has expired: 
				<input type="checkbox" name="expired" {$exp} />
			</p>
			<p>
				This agreement has been surpassed by: 
				<input type="text" name="surpassed_by" maxlength="4" {$spass} size="4" />
				(agreement ID number)
			</p>
EOHTML;
	}

	/**
	 * Used for generating diffs.
	 */
	function getTextVersion() {
		global $sub_summary_length;
		$short = '';
		$surpassed_by = intval( $this->surpassed_by );
		$expired = intval( $this->expired );
		$pub = ( $this->world_public ) ? ' checked="checked"' : '';
		$date = $this->Date->toString( );

		$cmty_name = $this->cmty->getName();

		$out = '';
		if ( !empty( $this->summary )) {
			$out .= "Summary:\n{$this->summary}\n";
		}
		if ( !empty( $this->background )) {
			$out .= "Background:\n" . 
				wordwrap($this->background, 80, "\n");
		}
		if ( !empty( $this->full )) {
			$out .= "Proposal:\n" . 
				wordwrap($this->full, 80, "\n");
		}
		if ( !empty( $this->comments )) {
			$out .= "Comments:\n" . 
				wordwrap($this->comments, 80, "\n");
		}
		if ( !empty( $this->processnotes )) {
			$out .= "Process Comments:\n" .
				wordwrap($this->processnotes, 80, "\n");
		}

		return <<<EOTXT
Title: {$this->title}
Committee: {$cmty_name}
Date: {$date}
{$out}

EOTXT;
	}


	/**
	 * Display the document in the format specified.
	 *
	 * @param[in] type string (default: document) specifies the output
	 * format. Possible options would be:
	 *     - form, the edit form
	 *     - search, display search results
	 *     - document, display full document for html presentation
	 */
	function display( $type='document' )
	{
		global $sub_summary_length;
		$admin_info = $this->adminActions( );
		$short = '';
		$surpassed_by = intval( $this->surpassed_by );
		$expired = intval( $this->expired );

		$pub = ( $this->world_public ) ? ' checked="checked"' : '';
		$title = format_html( $this->title );
		$summary = format_html( $this->summary );
		$full = format_html( $this->full );
		$background = format_html( $this->background );
		$comments = format_html( $this->comments );
		$processnotes = format_html( $this->processnotes );

		$condition = '';
		if ( $surpassed_by != 0 ) {
			$Replacement = new Agreement( $surpassed_by );
			if ( $Replacement->isValid( )) {
				$rep_title = format_html( $Replacement->title );
				$date_string = $Replacement->Date->toString();
				$condition = <<<EOHTML
				<p class="notice">Surpassed By: 
					<a href="?id=agreement&amp;num={$surpassed_by}">{$rep_title}</a>
					{$date_string}
				</p>
EOHTML;
			}
			else {
				$condition = '<p class="notice">This agreement was marked ' .
					'surpassed, but the overriding agreement is missing.</p>';
			}
		}
		elseif ( $this->expired ) {
			$condition = '<p class="notice">Agreement Expired</p>';
		}

		switch( $type ) {
			case 'form':
				$title = format_html( $this->title, true );
				$summary = format_html( $this->summary, true );
				$full = format_html( $this->full, true );
				$background = format_html( $this->background, true );
				$comments = format_html( $this->comments, true );
				$processnotes = format_html( $this->processnotes, true );

				$diff_comments = '';
				if ($this->id != 0) {
					$diff_comments = <<<EOHTML
						<h3>Diff comments:</h3>
						<input type="text" name="diff_comments" value="" size="70">
EOHTML;
				}

				echo <<<EOHTML
				<p>
					Make this agreement public to the world:
					<input type="checkbox" name="world_public" {$pub} />
				</p>

				<h3>Title:</h3>
				<input type="text" name="title" value="{$title}" size="70" />

				{$diff_comments}

				<h3>Summary:</h3>
				<textarea name="summary" cols="85" rows="3">{$summary}</textarea>

				<h3>Background:</h3>
				<textarea name="background" cols="85" 
					rows="7">{$background}</textarea>

				<h3>Proposal:</h3>
				<textarea name="full" cols="85" rows="30">{$full}</textarea>

				<h3>Comments:</h3>
				<textarea name="comments" cols="85" rows="5">{$comments}</textarea>

				<h3>Process Notes:</h3>
				<textarea name="processnotes" cols="85" 
					rows="3">{$processnotes}</textarea>
EOHTML;

				break;

			case 'search':
				if ( !empty( $this->found )) {
					$short = '<p class="short">' . $this->found . "</p>\n";
					if (!$this->found_summary) {
						$short .= "<br/>SUMMARY: $summary\n";
					}
				}
				else {
					$short = !empty($summary) ? $summary :
						substr( $full, 0, $sub_summary_length ) . '...';
				}

				$date = $this->Date->toString( );
				$cmty_name = $this->cmty->getName();

				echo <<<EOHTML
					<div class="agreement">
						<h2 class="agrm">
							{$date} 
							<a href="?id=agreement&amp;num={$this->id}">{$this->title}</a>
							[{$cmty_name}]
						</h2>
						{$condition}
						<div class="item_topic">
							<img class="topic_img tango" src="display/images/tango/32x32/mimetypes/application-certificate.png" alt="agreement">
							<div class="info">{$short}</div>
						</div>
					</div>
EOHTML;
				break;

			case 'document':
				// only show previous version disply with full document display
				$condition .= $this->displayPreviousVersions();

				$print_ver_label = '';
				$print_ver_dest = '';
				$cur_date = '';
				if (!$this->is_print_version) {
					$print_ver_label = <<<EOHTML
						<img class="tango" src="display/images/tango/32x32/devices/printer.png" border="0" alt="print">
						format for printing
EOHTML;
					$print_ver_dest = $_SERVER['QUERY_STRING'] . '&amp;print=1';
				}
				else {
					$print_ver_label = <<<EOHTML
						<img class="tango" src="display/images/tango/32x32/mimetypes/text-html.png" border="0" alt="full page">
						return to full page
EOHTML;
					$print_ver_dest = str_replace( '&print=1', '',
						$_SERVER['QUERY_STRING'] );
					$print_ver_dest = str_replace( '&amp;print=1', '',
						$print_ver_dest);

					$cur_date = '<p>As of: ' . date('r') . '</p>';
				}

				$date = $this->Date->toString( );

				$cmty_name = $this->cmty->getName();
				$content = '';

				if ( !empty( $summary )) {
					$content .= "<h3>Summary:</h3>\n$summary\n";
				}
				if ( !empty( $background )) {
					$content .= "<h3>Background:</h3>\n$background\n";
				}
				if ( !empty( $full )) {
					$content .= "<h3>Proposal:</h3>\n$full\n";
				}
				if ( !empty( $comments )) {
					$content .= "<h3>Comments:</h3>\n$comments\n";
				}
				if ( !empty( $processnotes )) {
					$content .= "<h3>Process Comments:</h3>\n$processnotes\n";
				}

				echo <<<EOHTML
					<div class="print_version_link">
						<a href="/?{$print_ver_dest}">{$print_ver_label}</a>
					</div>
					<div class="agreement">
						<h1 class="agrm">{$title}</h1>
						{$condition}
						{$admin_info}
						<div class="info">
							<h3>{$cmty_name}&nbsp;{$date}</h3>
							{$content}
						</div>
					</div>
					{$cur_date}
EOHTML;

				break;
		}

		return 1;
	}

	/**
	 * Load the previous agreement version info from the database.
	 */
	function loadPreviousVersions() {
		if (is_null($this->id)) {
			return FALSE;
		}

		$sql = <<<EOSQL
			SELECT agr_version_num, updated_date, diff_comment
				FROM agreements_versions
				WHERE agr_id={$this->id}
				ORDER BY agr_version_num desc;
EOSQL;
		$this->previous_versions = $this->mysql->get($sql);
	}

	/**
	 * If this agreement has previous versions, then display them.
	 * @return string html to be displayed. If no previous versions, then
	 *     return NULL.
	 */
	function displayPreviousVersions() {
		if ($this->is_print_version) {
			return NULL;
		}

		$this->loadPreviousVersions();
		if (empty($this->previous_versions)) {
			return NULL;
		}

		$out = '';
		foreach($this->previous_versions as $entry) {
			$out .= <<<EOHTML
				<tr>
					<td>{$entry['agr_version_num']}</td>
					<td>
						<a href="?id=previous_version&agr_id={$this->id}&prev_id={$entry['agr_version_num']}">
							view diff</a>
					</td>
					<td>{$entry['diff_comment']}</td>
					<td>{$entry['updated_date']}</td>
				</tr>
EOHTML;
		}

		$display_show_diffs = '';
		$display_diff_list = ' style="display: none;"';
		if (isset($_GET['expand_diffs'])) {
			$display_show_diffs = ' style="display: none;"';
			$display_diff_list = '';
		}

		$num_diffs = count($this->previous_versions);
		return <<<EOHTML
			<div id="versions_reveal"{$display_show_diffs}>
				<div>
					<img src="display/images/tango/32x32/apps/preferences-system-windows.png" width="32" height="32">
					<a href="#" class="show">[+] show {$num_diffs} previous versions</a>
				</div>
			</div>
			<div id="versions"{$display_diff_list}>
				<div>
					<img src="display/images/tango/32x32/apps/preferences-system-windows.png" width="32" height="32">
					<a href="#" class="hide">[-] hide {$num_diffs} previous versions</a>
				</div>
				<p>The following dates are when they became obsolete.</p>
				<table cellpadding="3">
					<tr>
						<th>version</th>
						<th></th>
						<th>diff comment</th>
						<th>obsoleted date</th>
					</tr>
					{$out}
				</table>
			</div>
EOHTML;
	}

	# agreement
	function adminActions( )
	{
		$link = '';
		if ( isset( $_SESSION['admin'] ) && ( $_SESSION['admin'] ))
		{
			$link = <<<EOHTML
				<div class="actions">
					<a href="?id=admin&amp;doctype=agreement&amp;num={$this->id}">
						<img class="tango" src="display/images/tango/32x32/apps/accessories-text-editor.png" border="0" alt="edit" />
						edit
					</a>
					&nbsp;&nbsp;
					<a href="?id=admin&amp;doctype=agreement&amp;delete={$this->id}">
						<img class="tango" src="display/images/tango/32x32/actions/edit-delete.png" border="0" alt="delete" />
						delete
					</a>
				</div>
EOHTML;
		}
		return $link;
	}

	/**
	 * Save this agreement.
	 * @param[in] update boolean (default false). If TRUE, then update an
	 *     existing document. Otherwise, create a new one.
	 * @return boolean. If true, then the save was successful.
	 */
	function save( $update=false )
	{
		global $HDUP;
		global $G_DEBUG;
		$success = 0;
		if ( $this->id == 0 ) {
			$this->id = '';
		}

		# check for required items
		if ( !$this->isValid( )) {
			echo <<<EOHTML
				<div class="error">Missing content!
					<a href="javascript: history.go(-1);">Back</a></div>
EOHTML;
			return FALSE;
		}

		$type = '';
		if (( $update ) && ( is_numeric( $this->id ))) {
			$type = 'updated';
			$Info = array(
				'title="' . clean_html( $this->title ) . '"',
				'summary="' . clean_html( $this->summary ) . '"',
				'full="' . clean_html( $this->full ) . '"',
				'background="' . clean_html( $this->background ) . '"',
				'comments="' . clean_html( $this->comments ) . '"',
				'processnotes="' . clean_html( $this->processnotes ) . '"',
				'cid="' . intval( $this->cid ) . '"',
				'date="' . $this->Date->toString( ) . '"',
				'surpassed_by="' . intval( $this->surpassed_by ) . '"',
				'expired="' . intval( $this->expired ) . '"',
				'world_public=' . (( $this->world_public ) ? 1 : 0 )
			);
			$this->updateRevision();
			$condition = "where id=$this->id";
			$success = my_update( $G_DEBUG, $HDUP, 'agreements', 
				$Info, $condition );

			# grab the newly inserted document's ID number
			if ( !is_int( $this->id )) {
				$sql = 'select max( id ) as max from agreements';
				$Max = my_getInfo( $G_DEBUG, $HDUP, $sql );
				$this->id = $Max[0]['max'];
			}
		}
		else {
			$type = 'new';
			// this is a new document
			$Info = array(
				$this->id,
				clean_html( $this->title ),
				clean_html( $this->summary ),
				clean_html( $this->full ),
				clean_html( $this->background ),
				clean_html( $this->comments ),
				clean_html( $this->processnotes ),
				intval( $this->cid ),
				$this->Date->toString( ),
				intval( $this->surpassed_by ),
				intval( $this->expired ),
				(( $this->world_public ) ? 1 : 0 )
			);
			$success = my_insert( $G_DEBUG, $HDUP, 'agreements', $Info );
		}

        if ( !$success ) {
			echo "Save didn't work\n";
			return FALSE;
		}

		$this->sendEmail($type);
		return TRUE;
	}

	/**
	 * Send email notice of a new or updated agreement.
	 * @param[in] type string ('new' or 'updated').
	 */
	function sendEmail($type) {
		$msg = <<<EOHTML
{$type} agreement http://{$_SERVER['SERVER_NAME']}/?id=agreement&num={$this->id}

Title: {$this->title}
Summary: {$this->summary}

Agreement:
----------------
{$this->full}
EOHTML;
		$msg = format_email($msg);

		// send audit-trail email
		// to, subject, message, addl headers
		$ret = mail(
			AUDIT_CONTACT,
			"{$_SERVER['SERVER_NAME']} BOA: {$type} {$this->title}",
			$msg,
			'From: Book of Agreements <' . FROM_ADDRESS . ">\r\n" .
				'Reply-To: process@gocoho.org'
		);

		if (!$ret) {
			echo '<p class="error">Could not send mail</p>' . "\n";
			return FALSE;
		}

		echo <<<EOHTML
			<script type="text/javascript">
				window.location = "/?id=agreement&num={$this->id}";
			</script>
EOHTML;
		return TRUE;
	}

	/**
	 * On update, save the previous version of this document into a separate
	 * table for auditing purposes.
	 *
	 * @return boolean If TRUE, then the update save was successful.
	 */
	function updateRevision() {
		// first, find out if there are previous "old" versions of this
		// agreement, and grab the latest sub-ID.
		$sql = <<<EOSQL
			SELECT agr_version_num
				FROM agreements_versions
				WHERE agr_id={$this->id}
					ORDER BY agr_version_num DESC limit 1;
EOSQL;
		$prev_sub_id_info = $this->mysql->get($sql, 'agr_version_num');
		$cur_sub_id = empty($prev_sub_id_info) ? 1 :
			array_shift(array_keys($prev_sub_id_info)) + 1;

		$sql = <<<EOSQL
			INSERT INTO agreements_versions
				SELECT '', NOW(), {$cur_sub_id}, '{$this->diff_comments}',
					agreements.* from agreements
				WHERE id={$this->id};
EOSQL;
		return (!is_null($this->mysql->query($sql)));
	}

	/**
	 * Delete the current agreement.
	 */
	function delete( ) {
		global $Cmtys;
		global $HDUP;
		global $G_DEBUG;

		if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
			$this->setId($_GET['delete']);
		} 

		if (!isset($_GET['confirm_del'])) { 
			$date = $this->Date->toString( );
			$title = format_html( $this->title, true );
			echo <<<EOHTML
				<div class="agreement">
					<h2>Are you sure you want to delete this entry?</h2>
					<h1 class="agrm">{$title} agreement: {$date}</h1>
				</div>

				<form action="?" method="get">
				<input type="hidden" name="id" value="admin" />
				<input type="hidden" name="doctype" value="agreement" />
				<input type="hidden" name="delete" value="{$this->id}" />
				<div align="right">
					<a href="?id=admin&amp;doctype=agreement&amp;delete={$this->id}&amp;confirm_del=1">
						<img class="tango" src="display/images/tango/32x32/actions/edit-delete.png" border="0" alt="delete" />
						confirm delete</a>
				</div>
				</form>
EOHTML;
			return FALSE;
		}

		$HDUP['table'] = 'agreements';
		$success = my_delete( $G_DEBUG, $HDUP, 'id', $this->id );
		if ( !$success ) {
			echo '<div class="error">Error: Item was not deleted</div>' . "\n";
			return FALSE;
		}

		// also delete any related previous versions
		$HDUP['table'] = 'agreements_versions';
		$success = my_delete( $G_DEBUG, $HDUP, 'agr_id', $this->id );
		if ( !$success ) {
			echo <<<EOHTML
				<div class="error">Error: Prior versions were not deleted</div>
EOHTML;
			return FALSE;
		}

		echo "<p>Item deleted</p>\n";
		return TRUE;
	}

	/**
	 * Get the the diff text.
	 *
	 * @param[in] version int, the previous version of the document to
	 *     use as a starting point to generate the diff.
	 * @return string, HTML displaying the diff betweeen the versions.
	 */
	function getDiff($version) {
		$prev_agreement = TRUE;
		$older_filename = $this->loadDocByVersion($version, $prev_agreement);
		$newer_filename = $this->loadDocByVersion($version + 1);

		if (!file_exists($older_filename) || !file_exists($newer_filename)) {
			return;
		}

		$diff = shell_exec("diff --unified={$this->diff_context} -b ".
			"{$older_filename} {$newer_filename}");
		if (empty($diff)) {
			return <<<EOHTML
		<div class="no_difference">
			<img src="display/images/tango/32x32/actions/format-indent-more.png"
				width="32" height="32"/>
			<img src="display/images/tango/32x32/actions/format-indent-less.png"
				width="32" height="32"/>
			There was no difference between these file versions.
		</div>
EOHTML;
		}

		$out = $this->getDiffSummary($version, $prev_agreement);

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

		return <<<EOHTML
			{$out}
			<div id="diff">{$diff}</div>
EOHTML;
	}

	/**
	 * Load the document at a specific version and display the summary info.
	 *
	 * @param[in,out] prev_agreement if set to NULL, this is ignored.
	 *     Otherwise, contains the array of key-value pairs defining the
	 *     previous version of the agreement.
	 * @param[in] version int the previous version ID.
	 * @return string The temp filename where the text-version of this document
	 *     has been dumped to.
	 */
	function loadDocByVersion($version, &$prev_agreement=NULL) {
		$sql = <<<EOSQL
			SELECT * from agreements_versions where agr_id={$this->id}
				AND agr_version_num={$version}
EOSQL;
		$data = $this->mysql->get($sql);
		$a = array_pop($data);

		// if this isn't a previous version, but the current one, then simply load
		// the Agreement
		if (empty($a)) {
			$this->loadById();
		}
		else {
			$this->setContent($a['title'], $a['summary'], $a['full'],
				$a['background'], $a['comments'], $a['processnotes'],
				$a['cid'], $a['date'], $a['surpassed_by'], $a['expired'],
				$a['world_public']);

			if (!is_null($prev_agreement)) {
				$prev_agreement = $a;
			}
		}

		$file = sprintf($this->filename_format, $this->id, $version);
		file_put_contents($file, $this->getTextVersion());
		return $file;
	}

	/**
	 * Get the summary html for this diff.
	 * @param[in] version int, the number of the previous version diff to
	 *     reference.
	 * @param[in] prev_agreement array of key-value pairs mapping the various
	 *     table column fields to data in the previous agreement.
	 */
	function getDiffSummary($version, $prev_agreement=NULL) {
		$prev = '';
		if ($version > 1) {
			$prev_ver = $version - 1;
			$prev = <<<EOHTML
				<a href="/?id=previous_version&agr_id={$this->id}&prev_id={$prev_ver}">
					&larr; previous version ({$prev_ver})</a>
EOHTML;
		}

		return <<<EOHTML
			<h3>Diff summary for 
				"<a href="/?id=agreement&amp;num={$this->id}&amp;expand_diffs=1">
					{$prev_agreement['title']}</a>":</h3>
			{$prev}
			
			<p>Updated: {$prev_agreement['updated_date']}
			<br />Comment: {$prev_agreement['diff_comment']}
			</p>
EOHTML;
	}
}

/**
 * Minutes
 */
class Minutes extends BOADoc {
	var $doc_type = 'minutes';
	var $m_id = 0;
	var $notes = null;
	var $agenda = null;
	var $content = null;
	var $cid = 0;
	var $Date;
	var $search_points = 0;
	var $found = '';
	var $found_agenda = false;

	# minutes
	function Minutes( $m='', $n='', $a='', $c='', $c_id='', $D='' )
	{
		parent::BOADoc();

		$this->notes = clean_html($n);
		$this->agenda = clean_html($a);
		$this->content = clean_html($c);
		$this->cid = $c_id;

		if ( empty( $D )) { $this->Date = new MyDate( ); }
		else { $this->Date = $D; }

		$this->m_id = $m;

		# if potentially valid id num
		if ( intval( $this->m_id ) > 0 ) {
			# check to see if the required entries are valid
			if ( empty( $this->agenda ) && empty( $this->content ))
			{ $this->loadById( $this->m_id ); }
		}
	}

	# minutes
	function loadById( $id='' )
	{
		global $HDUP;
		global $G_DEBUG;
		$entryDate = new MyDate( );

		$min_id = $id;
		if ( $id == '' ) { $min_id = $this->m_id; }

		$sql = 'select committees.cmty, minutes.* from minutes, '.
			"committees where m_id=$min_id  and committees.cid=minutes.cid";
		$Min = my_getInfo( $G_DEBUG, $HDUP, $sql );

		if ( empty( $Min )) {
			return;
		}
		$entryDate->setDate( $Min[0]['date'] );

		$this->Minutes( $Min[0]['m_id'], $Min[0]['notes'], 
			$Min[0]['agenda'], $Min[0]['content'], $Min[0]['cid'], $entryDate );
	}

	# minutes
	function display( $type='document' )
	{
		global $sub_summary_length;
		$admin_info = $this->adminActions( );
		$short = '';

		$notes = format_html( $this->notes );
		$agenda = format_html( $this->agenda );
		$content = format_html( $this->content );

		switch( $type )
		{
			case 'form':
				$notes = format_html( $this->notes, true );
				$agenda = format_html( $this->agenda, true );
				$content = format_html( $this->content, true );

				$notes = '<input type="text" name="notes" value="'.
					$notes . '" size="50" />' . "\n";
				$agenda = '<textarea name="agenda" cols="85" rows="10">'.
					$agenda . "</textarea>\n";
				$content = '<textarea name="content" cols="85" rows="35">'.
					$content . "</textarea>\n";

				if ( !empty( $notes ))
				{ echo "<h3>Special Notes:</h3>\n$notes\n"; }
				if ( !empty( $agenda ))
				{ echo "<h3>Agenda:</h3>\n$agenda\n"; }
				if ( !empty( $content ))
				{ echo "<h3>Minutes:</h3>\n$content\n"; }

				break;

			case 'compact':
				echo "<tr>\n" .
					"\t<td>" . $this->cmty->getName() . "</td>\n" .
					"\t<td>" . '<a href="?id=minutes&num=' . $this->m_id . '">' .
						$this->Date->toString( ) . "</a></td>\n" . 
					"\t<td>" . $notes . "</td>\n";
					"</tr>\n";
				break;

			case 'search':
				if ( !empty( $this->found )) {
					$short = '<p class="short">FOUND:' . $this->found . "</p>\n";
					if (!$this->found_agenda) {
						$short .= "<br/>AGENDA: $agenda\n";
					}
				}

			case 'summary':
				if ( empty( $short )) { $short = $agenda . $notes; }
				if ( empty( $short ))
				{ $short = substr( $content, 0, $sub_summary_length ) . '...'; }

				$date_string = $this->Date->toString( );
				$cmty_name = $this->cmty->getName();
				echo <<<EOHTML
					<div class="minutes">
						<h2 class="mins">
							<a href="?id=minutes&num={$this->m_id}">{$date_string} 
								{$cmty_name}</a> minutes
						</h2>
						<div class="item_topic">
							<img class="topic_img tango" src="display/images/tango/32x32/mimetypes/text-x-generic.png" alt="minutes">
							<div class="info">{$short}</div>
						</div>
					</div>
EOHTML;
				break;

			case 'document':
				echo '<div class="minutes">' . "\n" .
					'<h1 class="mins">' . $this->cmty->getName() .
					' minutes: ' . $this->Date->toString( ) . "</h1>\n" .
					'<div class="info">' . $admin_info;

				if ( !empty( $notes ))
				{ echo "<h3>Special Notes:</h3>\n$notes\n"; }
				if ( !empty( $agenda ))
				{ echo "<h3>Agenda:</h3>\n$agenda\n"; }
				if ( !empty( $content ))
				{ echo "<h3>Minutes:</h3>\n$content\n"; }

				echo "</div>\n</div>\n\n";
				break;
		}

		return 1;
	}

	# minutes
	function adminActions( )
	{
		$link = '';
		if ( isset( $_SESSION['admin'] ) && ( $_SESSION['admin'] ))
		{
			$link = <<<EOHTML
				<div class="actions">
					<a href="?id=admin&amp;doctype=minutes&amp;num={$this->m_id}">
						<img class="tango" src="display/images/tango/32x32/apps/accessories-text-editor.png" border="0" alt="edit" />
						edit
					</a>
					&nbsp;&nbsp;
					<a href="?id=admin&amp;doctype=minutes&amp;delete={$this->m_id}">
						<img class="tango" src="display/images/tango/32x32/actions/edit-delete.png" border="0" alt="delete">
						delete
						</a>
				</div>
EOHTML;
		}
		return $link;
	}

	# minutes
	function save( $update=false )
	{
		global $HDUP;
		global $G_DEBUG;
		$success = 0;
		if ( $this->m_id == 0 ) {
			$this->m_id = '';
		}

		# check for required items
		if ( empty( $this->content )) {
			echo <<<EOHTML
				<div class="error">Missing content! 
					<a href="javascript:history.go(-1)">Back</a></div>
EOHTML;
			return FALSE;
		}

		# if an update then keep the id
		if (( $update ) && ( is_int( $this->m_id ))) {
			$Info = array( 'notes="' . $this->notes . '"',
				'agenda="' . $this->agenda . '"',
				'content="' . $this->content . '"',
				'cid="' . intval( $this->cid ) . '"',
				'date="' . $this->Date->toString( ) . '"'
			);

			$condition = "where m_id=$this->m_id";
			$success = my_update( $G_DEBUG, $HDUP, 'minutes', 
				$Info, $condition );
		}
		# otherwise, treat this as a new entry
		else {
			$Info = array( $this->m_id,
				$this->notes,
				$this->agenda,
				$this->content,
				intval( $this->cid ), 
				$this->Date->toString( )
			);
			$success = my_insert( $G_DEBUG, $HDUP, 'minutes', $Info );
		}

		if ( !$success ) {
			echo "Save didn't work\n";
			return FALSE;
		}

		if ( !is_int( $this->m_id )) {
			$sql = 'select max( m_id ) as max from minutes';
			$Max = my_getInfo( $G_DEBUG, $HDUP, $sql );
			$this->m_id = $Max[0]['max'];
		}

		echo <<<EOHTML
			<script type="text/javascript">
				window.location = "/?id=minutes&num={$this->m_id}";
			</script>
EOHTML;

		return TRUE;
	}

	# minutes
	function delete( $confirm )
	{
		global $HDUP;
		global $G_DEBUG;

		if ( !$confirm )
		{
			$date_string = $this->Date->toString( );
			$cmty_name = $this->cmty->getName();
			echo <<<EOHTML
			<div class="minutes">
				<h2>Are you sure you want to delete these minutes?</h2>
				<h1 class="mins">{$cmty_name}: {$date_string}</h1>
			</div>
			<div class="actions">
				<a href="?id=admin&amp;doctype=minutes&amp;delete={$this->m_id}&confirm_del=1">
					<img class="tango" src="display/images/tango/32x32/actions/edit-delete.png" border="0" alt="delete">
						confirm delete</a>
			</div>
EOHTML;
		}
		else
		{
			$HDUP['table'] = 'minutes';
			$success = my_delete( $G_DEBUG, $HDUP, 'm_id', $this->m_id );
			if ( $success ) { echo "<p>Item deleted\n"; }
			else
			{ echo '<div class="error">Error: Item was not deleted</div>' . "\n"; }
			
		}
	}
}

?>