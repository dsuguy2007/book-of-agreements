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

# clean up user-supplied input
function clean_html( $text )
{
	if ($text == '') {
		return '';
	}

	$Bad = array( 
		128 => 1,
		131 => 1,
		162 => 1,
		175 => 1,
		189 => 1,
		191 => 1,
		194 => 1,
		195 => 1,
		226 => 1
	);

	$lsq = chr(152);
	$rsq = chr(153);
	$ldq = chr(156);
	$rdq = chr(157);
	$dash = chr(147);

	# strip out any "smart quotes"
	$Chars = str_split($text);
	foreach($Chars as &$c) {
		if (isset($Bad[ord($c)])) {
			$c = '';
			continue;
		}

		switch($c) {
			# single quotes
			case $lsq:
			case $rsq: $c = "'"; break;

			# double quotes
			case $ldq:
			case $rdq: $c = '"'; break;

			# long dash
			case $dash: $c = '-'; break;
		}
	}
	$text = htmlentities(implode('', $Chars));

	return $text;
}


# ----------------------------------------------------

class MyDate {
	public $curyear;
	public $year;
	public $month;
	public $day;
	public $label;

	# MyDate
	function __construct( $year='', $month='', $day='', $label=NULL)
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
	public $cnum;

	# Committee
	function __construct( $n='' )
	{
		$this->cnum = $n;
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
	function getName( $id )
	{
		global $Cmtys;
		global $SubCmtys;
		$name = '';

		if ( isset( $Cmtys[$id] )) {
			return $Cmtys[$id];
		}
		foreach( $SubCmtys as $major => $Sub ) {
			if ( isset( $Sub[$id] )) {
				return $Cmtys[$major] . ': ' .$Sub[$id];
			}
		}

		echo '<div class="error">Error! Could not find requested record</div>' . "\n";
		exit;
	}
}

/**
 * Parent class to both Agreements and Minutes
 */
class BOADoc {
	protected $mysql;

	public function __construct() {
		global $HDUP;

		require_once 'logic/mysql_api.php';
		$this->mysql = new MysqlApi($HDUP['host'], $HDUP['database'],
			$HDUP['user'], $HDUP['password']);
	}
}

/**
 * Agreements
 */
class Agreement extends BOADoc {
	public $doc_type = 'agreement';
	public $id = null;
	public $title = null;
	public $summary = null;
	public $full = null;
	public $background = null;
	public $comments = null;
	public $processnotes = null;
	public $cid = null;
	public $Date;
	public $surpassed_by;
	public $expired;
	public $search_points = 0;
	public $found = '';
	public $world_public = false;
	public $found_summary = false;

	# agreement
	function __construct( $i='', $t='', $s='', $f='', $b='', $c='', 
			$p='', $c_id='', $D='', $sb=0, $x='', $wp=false )
	{
		parent::__construct();

		$this->id = $i + 0;
		$this->title = $t;
		$this->summary = $s;
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

		# if potentially valid id num
		if ( $this->id > 0 && !$this->isValid( )) {
			$this->constructFromId( );
		}
	}

	# agreement
	function constructFromId( )
	{
		global $PUBLIC_USER;
	
		if (!is_int($this->id)) {
			syslog('constructFromId was called with an invalid ID');
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

		$Agrm = my_getInfo( $G_DEBUG, $HDUP, $sql.$pub_constraint );
		if ( empty( $Agrm )) {
			if ( $PUBLIC_USER ) {
				if (attempt_login()) {
					# run the query again, without the constraint
					$Agrm = my_getInfo( $G_DEBUG, $HDUP, $sql );
				}
				else {
					return;
				}
			}
		}

		# if still empty... then punt
		if ( empty( $Agrm )) {
			return;
		}

		$entryDate->setDate( $Agrm[0]['date'] );
		$this->__construct(
			$Agrm[0]['id'],
			$Agrm[0]['title'],
			$Agrm[0]['summary'],
			$Agrm[0]['full'],
			$Agrm[0]['background'],
			$Agrm[0]['comments'],
			$Agrm[0]['processnotes'],
			$Agrm[0]['cid'],
			$entryDate,
			$Agrm[0]['surpassed_by'],
			$Agrm[0]['expired'],
			$Agrm[0]['world_public']
		);
	}

	# agreement
	function isValid( )
	{
		# don't look for an id
		if ( empty( $this->title ) || empty( $this->full )) {
			return false;
		}
		return true;
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

	# agreement
	function display( $type='document' )
	{
		global $Cmty;
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
				$condition = <<<EOHTML
				<p class="notice">Surpassed By: 
					<a href="?id=agreement&amp;num={$surpassed_by}">{$rep_title}</a>
					({$Replacement->Date->toString()})
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

				echo <<<EOHTML
				<p>
					Make this agreement public to the world:
					<input type="checkbox" name="world_public" {$pub} />
				</p>

				<h3>Title:</h3>
				<input type="text" name="title" value="{$title}" size="50" />

				<h3>Summary:</h3>
				<textarea name="summary" cols="85" rows="3">{$summary}</textarea>

				<h3>Background:</h3>
				<textarea name="background" cols="85" 
					rows="7">{$background}</textarea>

				<h3>Proposal:</h3>
				<textarea name="full" cols="85" rows="30">{$full}</textarea>

				<h3>Comments:</h3>
				<textarea name="comments" cols="85" rows="5">{$comments}</textarea>

				<h3>Process Comments:</h3>
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

			case 'summary':

				if ( empty( $short )) {
					$short = !empty($summary) ? $summary :
						substr( $full, 0, $sub_summary_length ) . '...';
				}

				$date = $this->Date->toString( );
				$cmty_name = $Cmty->getName( $this->cid );

				echo <<<EOHTML
					<div class="agreement">
						<h2 class="agrm">
							{$date} 
							<a href="?id=agreement&amp;num={$this->id}">{$this->title}</a>
							[{$cmty_name}]
						</h2>
						{$condition}
						<div class="item_topic">
							<img class="topic_img tango" src="/display/images/tango/32x32/mimetypes/application-certificate.png" alt="agreement">
							<div class="info">{$short}</div>
						</div>
					</div>
EOHTML;
				break;

			case 'document':
				global $PUBLIC_USER;
				global $print_version;

				$print_ver_label = '';
				$print_ver_dest = '';
				if ( !$PUBLIC_USER ) {
					$print_ver_label = <<<EOHTML
						<img class="tango" src="/display/images/tango/32x32/devices/printer.png" border="0" alt="print">
						format for printing
EOHTML;
					$print_ver_dest = $_SERVER['QUERY_STRING'] . '&amp;print=1';
					if ( $print_version ) {
						$print_ver_label = <<<EOHTML
							<img class="tango" src="/display/images/tango/32x32/mimetypes/text-html.png" border="0" alt="full page">
							return to full page
EOHTML;
						$print_ver_dest = str_replace( '&amp;print=1', '',
							$_SERVER['QUERY_STRING'] );
					}
				}

				$date = $this->Date->toString( );
				$cmty_name = $Cmty->getName( $this->cid );
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
EOHTML;

				break;
		}

		return 1;
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
						<img class="tango" src="/display/images/tango/32x32/apps/accessories-text-editor.png" border="0" alt="edit" />
						edit
					</a>
					&nbsp;&nbsp;
					<a href="?id=admin&amp;doctype=agreement&amp;delete={$this->id}">
						<img class="tango" src="/display/images/tango/32x32/actions/edit-delete.png" border="0" alt="delete" />
						delete
					</a>
				</div>
EOHTML;
		}
		return $link;
	}

	# agreement
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
			echo '<div class="error">Missing content! <a href="javascript: ' .
				'history.go(-1)">Back</a></div>' . "\n";
			return;
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
		}
		else {
			$type = 'new';
			// this is a new document
			$Info = array( $this->id,
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
			return 0;
		}

		# grab the newly inserted document's ID number
		if ( !is_int( $this->id )) {
			$sql = 'select max( id ) as max from agreements';
			$Max = my_getInfo( $G_DEBUG, $HDUP, $sql );
			$this->id = $Max[0]['max'];
		}

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
			SITE_NAME . " BOA: {$type} {$this->title}",
			$msg,
			'From: Book of Agreements <' . FROM_ADDRESS . ">\r\n"
		);

		if (!$ret) {
			echo '<p class="error">Could not send mail</p>' . "\n";
		}

		$this->display( 'document' );
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
				SELECT '', NOW(), {$cur_sub_id}, agreements.* from agreements
				WHERE id={$this->id};
EOSQL;
		return (!is_null($this->mysql->query($sql)));
	}

	# agreement
	function delete( $confirm )
	{
		global $Cmtys;
		global $HDUP;
		global $G_DEBUG;

		if ( !$confirm ) {
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
						<img class="tango" src="/display/images/tango/32x32/actions/edit-delete.png" border="0" alt="delete" />
						confirm delete</a>
				</div>
				</form>
EOHTML;
		}
		else {
			$HDUP['table'] = 'agreements';
			$success = my_delete( $G_DEBUG, $HDUP, 'id', $this->id );
			if ( $success ) {
				echo "<p>Item deleted\n";
			}
			else {
				echo '<div class="error">Error: Item was not deleted</div>' . "\n";
			}
		}
	}
}

/**
 * Minutes
 */
class Minutes extends BOADoc {
	public $doc_type = 'minutes';
	public $m_id = 0;
	public $notes = null;
	public $agenda = null;
	public $content = null;
	public $cid = 0;
	public $Date;
	public $search_points = 0;
	public $found = '';
	public $found_agenda = false;

	# minutes
	function __construct( $m='', $n='', $a='', $c='', $c_id='', $D='' )
	{
		parent::__construct();

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
			{ $this->constructFromId( $this->m_id ); }
		}
	}

	# minutes
	function constructFromId( $id='' )
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

		$this->__construct( $Min[0]['m_id'], $Min[0]['notes'], 
			$Min[0]['agenda'], $Min[0]['content'], $Min[0]['cid'], $entryDate );
	}

	# minutes
	function display( $type='document' ) 
	{
		global $Cmty;
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
					"\t<td>" . $Cmty->getName( $this->cid ) . "</td>\n" .
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
				$cmty_name = $Cmty->getName( $this->cid );
				echo <<<EOHTML
					<div class="minutes">
						<h2 class="mins">
							<a href="?id=minutes&num={$this->m_id}">{$date_string} 
								{$cmty_name}</a> minutes
						</h2>
						<div class="item_topic">
							<img class="topic_img tango" src="/display/images/tango/32x32/mimetypes/text-x-generic.png" alt="minutes">
							<div class="info">{$short}</div>
						</div>
					</div>
EOHTML;
				break;

			case 'document':
				echo '<div class="minutes">' . "\n" .
					'<h1 class="mins">' . $Cmty->getName( $this->cid ) .
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
						<img class="tango" src="/display/images/tango/32x32/apps/accessories-text-editor.png" border="0" alt="edit" />
						edit
					</a>
					&nbsp;&nbsp;
					<a href="?id=admin&amp;doctype=minutes&amp;delete={$this->m_id}">
						<img class="tango" src="/display/images/tango/32x32/actions/edit-delete.png" border="0" alt="delete">
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
			return;
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
			return 0;
		}

		if ( !is_int( $this->m_id )) {
			$sql = 'select max( m_id ) as max from minutes';
			$Max = my_getInfo( $G_DEBUG, $HDUP, $sql );
			$this->m_id = $Max[0]['max'];
		}

		$this->display( 'document' );
	}

	# minutes
	function delete( $confirm )
	{
		global $Cmty;
		global $HDUP;
		global $G_DEBUG;

		if ( !$confirm )
		{
			$date_string = $this->Date->toString( );
			$cmty_name = $Cmty->getName( $this->cid );
			echo <<<EOHTML
			<div class="minutes">
				<h2>Are you sure you want to delete these minutes?</h2>
				<h1 class="mins">{$cmty_name}: {$date_string}</h1>
			</div>
			<div class="actions"
				<a href="?id=admin&amp;doctype=minutes&amp;delete={$this->m_id}&confirm_del=1">
					<img class="tango" src="/display/images/tango/32x32/actions/edit-delete.png" border="0" alt="delete">
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
