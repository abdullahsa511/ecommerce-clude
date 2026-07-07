#hero | before = <?php 
  if(isset($current_component)) $previous_component = $current_component;
  $hero = $current_component = $this->_component['heroproject']?? [];
  $way_points = $hero['way_points']??[];
	// echo '<pre>';
	// print_r($hero);
	// // print_r($way_points);
	// echo '</pre>';

	$is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>

[data-v-project-edit-link] a | href = <?php echo isset($hero['edit_link']) ? $hero['edit_link'] : ''; ?>
[data-v-heroproject-component-link] a | href = <?php echo isset($hero['component_link']) ? $hero['component_link'] : ''; ?>

[data-v-heroproject-hero_image] | data-bg-src = <?php echo isset($hero['image'][0]) ? $hero['image'][0]['objectURL'] : '/img/bg/home/hero_home.jpg'; ?>
[data-v-component-heroproject] [data-v-heroproject-section_title]|innerHTML = <?php echo isset($hero['section_title']) ? $hero['section_title'] : ''; ?>
span[data-v-heroproject-name]|innerHTML = <?php echo isset($hero['section_title']) ? $hero['section_title'] : ''; ?>
[data-v-component-heroproject] [data-v-heroproject-section_description]|innerHTML = <?php echo isset($hero['section_description']) ? $hero['section_description'] : ''; ?>
[data-v-heroproject-add-to-pinboard] | data-id = <?php echo isset($hero['project_id']) ? $hero['project_id'] : ''; ?>
[data-v-heroproject-add-to-pinboard] | data-model = project
[data-v-heroproject-add-to-pinboard] | data-title = <?php echo isset($hero['section_title']) ? $hero['section_title'] : ''; ?>
[data-v-heroproject-add-to-pinboard] | data-description = <?php echo isset($hero['keyline_quote']) ? $hero['keyline_quote'] : ''; ?>
[data-v-heroproject-add-to-pinboard] | data-image = <?php echo isset($hero['image'][0]) ? $hero['image'][0]['objectURL'] : ''; ?>
[data-v-heroproject-add-to-pinboard] | data-product-url = <?php echo isset($hero['project_link']) ? $hero['project_link'] : ''; ?>
[data-v-heroproject-hero_button_group] > div.button | deleteAllButFirst

[data-v-heroproject-waypoints] | deleteAllButFirst
[data-v-heroproject-waypoints] | prepend = <?php if(isset($way_points)){ foreach ($way_points as $item) { ?>
div[data-v-heroproject-waypoint] | id = <?php echo isset($item["id"]) ? 'way-point-'. $item["id"] : ''; ?>
div[data-v-heroproject-waypoint] | style = <?php echo isset($item["leftPercent"]) && isset($item["topPercent"]) ? "left: ".$item["leftPercent"]."%; top: ".$item["topPercent"]."%;" : ''; ?>
a[data-v-heroproject-waypoint-link] | innerText = <?php echo isset($item["label"]) ? $item["label"] : ''; ?>
a[data-v-heroproject-waypoint-link] | href = <?php echo isset($item["href"]) ? trim($item["href"]) : ''; ?>
a[data-v-heroproject-waypoint-link] | id = <?php echo isset($item["id"]) ? $item["id"] : ''; ?>
[data-v-heroproject-waypoints] | append = <?php }} ?>

[data-v-heroproject-hero_button_group] | prepend = <?php if(isset($hero['buttons'])){ foreach ($hero['buttons'] as $key => $button) { ?>
[data-v-heroproject-hero_button_class] | class = <?php echo $key == 0 ? 'th-btn text-capitalize' : 'th-btn-outline text-capitalize'; ?>
[data-v-heroproject-hero_button_label] | innerText = <?php echo $button['title']; ?>
[data-v-heroproject-hero_button_icon] | class = <?php echo $button['icon']; ?>
[data-v-heroproject-hero_button_link] | href = <?php echo $button['link']; ?>
[data-v-heroproject-hero_button_group] | append = <?php }} ?>

div[data-v-heroproject-component-link] | if_exists = $is_admin
div[data-v-project-edit-link] | if_exists = $is_admin
