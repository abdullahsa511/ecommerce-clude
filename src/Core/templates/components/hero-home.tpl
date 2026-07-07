#hero | before = <?php 
  if(isset($current_component)) $previous_component = $current_component;
  $hero = $current_component = $this->_component['herohome']?? [];
	// echo '<pre>';
	// print_r($hero);
	// echo '</pre>';
	$is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
	
	$backgroundImage = isset($hero['image'][0]) ? $hero['image'][0]['objectURL'] : '/img/bg/home/hero_home.jpg';
	$backgroundMobileBanner = isset($hero['mobile_banner'][0]) ? $hero['mobile_banner'][0]['objectURL'] : null;
	$isMobile = false;
	if (isset($_SERVER['HTTP_USER_AGENT'])) {
		$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
		if (preg_match('/(android|iphone|ipad|ipod|opera mini|iemobile|mobile)/i', $agent)) {
			$isMobile = true;
		}
	}
	// Show $backgroundMobileBanner if exists, else fallback to $backgroundImage
	if ($isMobile) {
		$bgImg = $backgroundMobileBanner ? $backgroundMobileBanner : $backgroundImage;
	} else {
		$bgImg = $backgroundImage;
	}
?>
#hero | style = <?php echo 'background-image: url(' . $bgImg . ');'; ?>
[data-v-herohome-component-link] > a | href = <?php echo isset($hero['component_link']) ? $hero['component_link'] : ''; ?>
[data-v-herohome-hero_title] | innerHTML = <?php echo isset($hero['section_title']) ? $hero['section_title'] : ''; ?>
[data-v-component-herohome] [data-v-herohome-hero_subtitle] | innerHTML = <?php echo isset($hero['section_subtitle']) ? $hero['section_subtitle'] : ''; ?>
[data-v-herohome-hero_button_group] > div.button | deleteAllButFirst


[data-v-herohome-waypoints] | deleteAllButFirst
[data-v-herohome-waypoints] | prepend = <?php if(isset($hero['banner_way_points'])){ foreach ($hero['banner_way_points'] as $item) { ?>
div[data-v-herohome-waypoint] | id = <?php echo isset($item["id"]) ? 'way-point-'. $item["id"] : ''; ?>
div[data-v-herohome-waypoint] | style = <?php echo isset($item["leftPercent"]) && isset($item["topPercent"]) ? "left: ".$item["leftPercent"]."%; top: ".$item["topPercent"]."%;" : ''; ?>
a[data-v-herohome-waypoint-link] | innerText = <?php echo isset($item["label"]) ? $item["label"] : ''; ?>
a[data-v-herohome-waypoint-link] | href = <?php echo isset($item["href"]) ? trim($item["href"]) : ''; ?>
a[data-v-herohome-waypoint-link] | id = <?php echo isset($item["id"]) ? $item["id"] : ''; ?>
[data-v-herohome-waypoints] | append = <?php }} ?>




[data-v-herohome-hero_button_group] | prepend = <?php if(isset($hero['buttons'])){ foreach ($hero['buttons'] as $key => $button) { ?>
[data-v-herohome-hero_button_label] | innerText = <?php echo $button['title']; ?>
[data-v-herohome-hero_button_icon] | class = <?php echo $button['icon']; ?>
[data-v-herohome-hero_button_link] | class = <?php echo $key == 0 ? 'th-btn text-capitalize th-btn-text' : 'th-btn-outline text-capitalize th-btn-text'; ?>
[data-v-herohome-hero_button_link] | href = <?php echo $button['url']; ?>
[data-v-herohome-hero_button_group] | append = <?php }} ?>


div[data-v-herohome-component-link] | if_exists = $is_admin
