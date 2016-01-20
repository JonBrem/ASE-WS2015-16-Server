<?php
require_once('lib/simple_html_dom.php');
require_once("util/db_connection.php");
require_once("util/status_codes.php");

/**
 * <p>The LokalreporterCrawler was used to crawl the <a href="http://lokalreporter.idvl.de/mediathek/page/">lokalreporter site</a>
 * and get links to all the videos on that site.</p>
 * <p>Several criteria of a "usual" crawler are not implemented because it was clear from the start
 * that this only needs to run once, does not need to scale up etc.</p>
 */
class LokalreporterCrawler {

	/**
	 * Basic program routine. Call once, this will start crawling until it finds no more videos.
	 * @param $baseUrl
	 *			URL of the lokalreporter site.
	 * @param $siteNum
	 *			What page number to start on (so when the crawler crashes, it won't have to start over)
	 */
	public function crawlLokalreporterSites($baseUrl = "http://lokalreporter.idvl.de/mediathek/page/", $siteNum = 1) {
		echo "Crawling: " .$baseUrl . $siteNum .  "<br>";
		$site = file_get_html($baseUrl . $siteNum);

		foreach($site->find('.video') as $video) {
			$this->retrieveMediaInfoFromSite($video->href);
			sleep(2); // for niceness (crawlers shouldn't make too many requests a short amount of time)
		}

		$nextButtonContent = $this->findNextPageButton($site);
		if(sizeof($nextButtonContent) > 0) {
			ob_flush();
			sleep(5);// for niceness (crawlers shouldn't make too many requests a short amount of time)
			$this->crawlLokalreporterSites($baseUrl, $siteNum + 1);
			// recursive call, which isn't great but good enough because there really aren't that many pages.
		} else {
			ob_end_flush();
		}
	}

	private function findNextPageButton($site) {
		return $site->find('.fa-step-forward');
	}

	/**
	 * Reads the title, url, image url and video url of a video
	 * on the lokalreporter site and stores it in the db.
	 * @param link
	 *		URL of a video on the lokalreporter site
	 */
	private function retrieveMediaInfoFromSite($link) {
		try {
			$videoSite = file_get_html($link);
			if(!$videoSite) return;

			$mediaObject = $this->getVideoSiteData($videoSite);
			$this->storeVideoDataInDB($mediaObject, $link);

		} catch (Exception $e) {
			var_dump($e);
		}
	}

	/**
	 * retrieves the data (url, title, image and video) from a video site.
	 * @param $videoSite simple_html_dom library representation of that video site
	 * @return a php array containing the data
	 */
	private function getVideoSiteData($videoSite) {
		$title = $this->getTitle($videoSite);
		$image = $videoSite->find('meta[property=og:image]', 0);
		$video = $videoSite->find('meta[property=og:video]', 0);
		
		$mediaObject = array(
			"url" => $link,
			"title" => $title,
			"image" => $image->content,
			"video" => $video->content
		);

		return $mediaObject;
	}

	/**
	 * Finding the title is just a bit more complicated than finding the rest and
	 * more prone to error, so for decomposition purposes, it gets its own function.
	 * @param simple_html_dom library representation of a video site
	 * @return the title as a string / empty string if there is no title
	 */
	private function getTitle($videoSite) {
		$title = "";

		$titleElement = $videoSite->find('.entry-title', 0);
		if($titleElement) {
			$title = $titleElement->innertext;
		}
		return $title;
	}

	/**
	 * @param mediaObject
	 *		the values in that array will be stored in the database.
	 * @param link
	 *		in order to print and have some feedback that the crawler is still up and running
	 */
	private function storeVideoDataInDB($mediaObject, $link) {
		$conn = getDBConnection();
		if(!$conn->connect_error) {
			$sql = "INSERT INTO media (title, url, preview_image, video_url, status) VALUES (\"" .
				$mediaObject["title"] ."\",\"" . $mediaObject["url"] . "\", \"" . $mediaObject["image"] . "\",\"" . $mediaObject["video"] . "\",\"".STATUS_CRAWLED."\");";
			if($conn->query($sql) === TRUE) {
				echo "Successfully crawled " . $link . "<br>";
			} else {
				echo "Error inserting into db!!: $sql<br>";
			}
		} else {
			echo "Error connecting to database!!<br>";
		}

		$conn->close();
	}

}

ob_start();
$lokalreporterCrawler = new LokalreporterCrawler();
$lokalreporterCrawler->crawlLokalreporterSites();
ob_end_flush();

?>