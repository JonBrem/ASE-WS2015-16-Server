<?php
require_once('lib/simple_html_dom.php');
require_once("dbConnection.php");


class LokalreporterCrawler {

	public function crawlLokalreporterSites($baseUrl = "http://lokalreporter.idvl.de/mediathek/page/", $siteNum = 1) {
		echo "Crawling: " .$baseUrl . $siteNum .  "<br>";
		$site = file_get_html($baseUrl . $siteNum);

		foreach($site->find('.video') as $video) {
			$this->retrieveMediaInfoFromSite($video->href);
			sleep(2);
		}

		$nextButtonContent = $site->find('.fa-step-forward');
		if(sizeof($nextButtonContent) > 0) {
			ob_flush();
			sleep(5);
			$this->crawlLokalreporterSites($baseUrl, $siteNum + 1);
		} else {
			ob_end_flush();
		}
	}

	private function retrieveMediaInfoFromSite($link) {
		
		try {
			$videoSite = file_get_html($link);
			if(!$videoSite) return;

			$title = "";

			$titleElement = $videoSite->find('.entry-title', 0);
			if($titleElement) {
				$title = $titleElement->innertext;
			}

			$image = $videoSite->find('meta[property=og:image]', 0);
			$video = $videoSite->find('meta[property=og:video]', 0);
			
			$mediaObject = array(
				"url" => $link,
				"title" => $title,
				"image" => $image->content,
				"video" => $video->content
			);

			echo "<pre>";
			var_dump($mediaObject);
			echo "</pre>";

			$conn = getDBConnection();
			if(!$conn->connect_error) {
				$sql = "INSERT INTO media (title, url, preview_image, video_url, status) VALUES (\"" .
					$mediaObject["title"] ."\",\"" . $mediaObject["url"] . "\", \"" . $mediaObject["image"] . "\",\"" . $mediaObject["video"] . "\",\"crawled\");";
				if($conn->query($sql) === TRUE) {
					echo "Successfully crawled " . $link . "<br>";
				} else {
					echo "Error inserting into db!!: $sql<br>";
				}
			} else {
				echo "Error connecting to database!!<br>";
			}

			$conn->close();
		} catch (Exception $e) {
			var_dump($e);
		}
	}

}

ob_start();
// todo: this could be in a "fancier" call from another file.
$lokalreporterCrawler = new LokalreporterCrawler();
$lokalreporterCrawler->crawlLokalreporterSites();
ob_end_flush();

?>