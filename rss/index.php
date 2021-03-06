<?php  
	define('URL_BASE','http://'.$_SERVER['SERVER_NAME']);
	header('Content-type: application/rss+xml');
	$pub_time = date('D, d M Y H:i:s +0000');

	$feed_title = 'Shizzeeps';
	
	require_once "../bin/db.php";
	
	function FindShizzeepsRss() {
		
		// get stored data out of db
		try{
			$db = new db();
			$sql = "SELECT Data FROM Storage WHERE City='rss' LIMIT 1;";
			$result = $db->query($sql);
			$rows = $result->fetchRow();
			$db = null;
		}
		catch (DatabaseException $e) {
		  $e->HandleError();
		}
		catch (ResultException $e) {
		  $e->HandleError();
		}
		
		$stripped = stripslashes($rows['Data']);
		$unserialized = unserialize($stripped); // now it's a php object
		$json = json_encode($unserialized); // now it's json
		$places = $unserialized->results->places;
		
		return $places;
	
	}//FindShizzeepsRss

	
	//needs to be populated
	$places = FindShizzeepsRss();
	
	/*

	echo "<pre>";
	var_dump( $places );
	echo "</pre>";
	
*/
	
	/*
	
	Request from @donpdonp
	
	<item>
	<title>4 people are checked in at the Urban Grind Northwest</title>
	<description></description>
	<geo:lat>45.513122</geo:lat>
	<geo:long>-122.644189</geo:long>
	<georss:point>45.513122 -122.644189</georss:point>
	<link>http://www.shizzeeps.com/thingy/17883</link>
	<guid>http://www.shizzeeps.com/thingy/17883</guid>
	<pubDate>Tue, 03 Mar 2009 21:23:37 PST</pubDate>
	</item>

	*/
	
?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:wfw="http://wellformedweb.org/CommentAPI/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:atom="http://www.w3.org/2005/Atom"
    xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
    xmlns:georss="http://www.georss.org/georss"
    xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#"
>

	<channel>
		<title><?=$feed_title?></title>
		<atom:link href="<?=URL_BASE?><?=$_SERVER['REQUEST_URI']?>" rel="self" type="application/rss+xml" />
		<link><?=URL_BASE?></link>
		<description>Feed of trending places from Shizzeeps</description>
		<pubDate><?=$pub_time ?></pubDate>
		<language>en</language>
		<sy:updatePeriod>hourly</sy:updatePeriod>
		<sy:updateFrequency>1</sy:updateFrequency>
		

		
		<?php foreach ($places as $place): ?>
			
			<item>
				<title>
				<?php if ($place->population > 1) { ?>
					<?=$place->population?> shizzeeps are congregating at <?=$place->places_name?>
				<?php } else { ?>
					<?=$place->population?> shizzeep is congregating at <?=$place->places_name?>
				<?php } ?>
				in <?=$place->city?>, <?=$place->state_iso?>
				</title>
				<description></description>
				<geo:lat><?=$place->latitude?></geo:lat>
				<geo:long><?=$place->longitude?></geo:long>
				<georss:point><?=$place->latitude?> <?=$place->longitude?></georss:point>
				<?php if ($place->state_iso == "OR") { ?>
					<link>http://www.shizzeeps.com/pdx/</link>
				<?php } else if ($place->state_iso == "TX") { ?>
					<link>http://www.shizzeeps.com/aus/</link>
				<?php } else { ?>
					<link>http://www.shizzeeps.com/</link>
				<?php } ?>
				<guid><?=$place->places_key?></guid>
				<pubDate><?=$pub_time?></pubDate>
			</item>
		
		<?php endforeach ?>
		
	</channel>

</rss>