#th-featured-projects-slider | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$featured_projects = $current_component = $this->_component['featuredprojectslider']?? [];

	// echo '<pre>';
	// print_r($featured_projects);
	// echo '</pre>';
$is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
	// echo '<pre>';
	// print_r($is_admin);
	// echo '</pre>';
?>

[data-v-featuredprojectslider-component-link] a | href = <?php echo isset($featured_projects['component_link']) ? $featured_projects['component_link'] : ''; ?>
[data-v-featuredprojectslider-section_subtitle] | innerHTML = <?php echo isset($featured_projects['section_subtitle']) ? $featured_projects['section_subtitle'] : ''; ?>

.th-featured-projects-slider > div.swiper-wrapper > div.swiper-slide | deleteAllButFirst
.th-featured-projects-slider > div.swiper-wrapper | prepend = <?php if(isset($featured_projects['items'])){ foreach ($featured_projects['items'] as $key => $project) { ?>
div[data-v-featuredprojectslideritem-label] | innerHTML = <?php echo isset($project["label"]) ? $project["label"] : ''; ?>
h3[data-v-featuredprojectslideritem-title] > a | innerHTML = <?php echo isset($project["title"]) ? $project["title"] : ''; ?>
div[data-v-featuredprojectslideritem-description] | innerHTML = <?php echo isset($project["preview_text"]) ? $project["preview_text"] : ''; ?>
img[data-v-featuredprojectslideritem-image] | src = <?php echo isset($project["image"]) ? $project["image"] : ''; ?>
img[data-v-featuredprojectslideritem-image] | alt = <?php echo isset($project["title"]) ? htmlspecialchars($project['title']) : ''; ?>
div[data-v-featuredprojectslideritem-add-to-pinboard] | data-id = <?php echo isset($project["project_id"]) ? $project["project_id"] : ''; ?>
div[data-v-featuredprojectslideritem-add-to-pinboard] | data-model = project
div[data-v-featuredprojectslideritem-add-to-pinboard] | data-title = <?php echo isset($project["title"]) ? $project["title"] : ''; ?>
div[data-v-featuredprojectslideritem-add-to-pinboard] | data-description = <?php echo isset($project["preview_text"]) ? $project["preview_text"] : ''; ?>
div[data-v-featuredprojectslideritem-add-to-pinboard] | data-image = <?php echo isset($project["image"]) ? $project["image"] : ''; ?>
div[data-v-featuredprojectslideritem-add-to-pinboard] | data-product-url = <?php echo isset($project["slug"]) ? "/projects"."/".$project["slug"] : '';  ?>
a[data-v-featuredprojectslideritem-link] | href = <?php echo isset($project["slug"]) ? "/projects"."/".$project["slug"] : ''; ?>
.th-featured-projects-slider > div.swiper-wrapper | append = <?php }} ?>

div[data-v-featuredprojectslider-component-link] | if_exists = $is_admin