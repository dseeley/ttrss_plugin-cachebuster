<?php
class cachebuster extends Plugin {

	private $host;
	private $filters = array();

	function about() {
		return array(1.0,
			"Forces date string on end of url to combat remote/cdn caching.",
			"Dougal Seeley");
	}

	function init($host) {
		$this->host = $host;

		$host->add_hook($host::HOOK_FETCH_FEED, $this);
    }

	function hook_fetch_feed($feed_data, $fetch_url, $owner_uid, $feed, $last_article_timestamp, $auth_login, $auth_pass) {
        if (strpos($fetch_url, '?') !== false) {
            $fetch_url_plus_epoch = $fetch_url . '&' . "epoch=" . date('U');
        } else {
            $fetch_url_plus_epoch = $fetch_url . '?' . "epoch=" . date('U');
        }

		Debug::log("fetching(cachebuster) {$fetch_url_plus_epoch}...", Debug::LOG_VERBOSE);

		$feed_data = UrlHelper::fetch([
			"url" => $fetch_url_plus_epoch,
			"login" => $auth_login,
			"pass" => $auth_pass
		]);

		$feed_data = trim($feed_data);

		Debug::log("fetch(cachebuster) done.", Debug::LOG_VERBOSE);
		Debug::log(sprintf("effective URL (after redirects): %s (IP: %s) ", UrlHelper::$fetch_effective_url, UrlHelper::$fetch_effective_ip_addr), Debug::LOG_VERBOSE);
		return $feed_data;
	}

	function api_version() {
		return 2;
	}
}
