import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])

#th-project-hero | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$project_hero = $current_component = $this->_component['projecthero']?? [];

	// echo '<pre>';
	// print_r($project_hero);
	// echo '</pre>';
	$is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;

?>
section[data-v-projecthero-hero_image] | data-bg-src = <?php echo isset($project_hero['image_banner']) ? $project_hero['image_banner'] : ''; ?>
span[data-v-projecthero-subtitle] | innerHTML = <?php echo isset($project_hero['subtitle']) ? $project_hero['subtitle'] : ''; ?>
h1[data-v-projecthero-title] | innerHTML = <?php echo isset($project_hero['title']) ? $project_hero['title'] : ''; ?>
[data-v-project-hero-component-link] a | href = <?php echo isset($project_hero['component_link']) ? $project_hero['component_link'] : ''; ?>

div[data-v-projecthero-button_group] | deleteAllButFirst
[data-v-projecthero-button_group] | prepend = <?php if(isset($project_hero['buttons'])){ foreach ($project_hero['buttons'] as $key => $button) { ?>
	a[data-v-projecthero-button_link] | href = <?php echo isset($button['url']) ? $button['url'] : ''; ?>
	a[data-v-projecthero-button_link] | target = <?php echo isset($button['target']) ? $button['target'] : ''; ?>
	a[data-v-projecthero-button_link] | class = <?php echo $key == 0 ? 'th-btn text-capitalize' : 'th-btn-outline text-capitalize'; ?>
	a[data-v-projecthero-button_link] > span | innerText = <?php echo isset($button['title']) ? $button['title'] : ''; ?>
[data-v-projecthero-button_group] | append = <?php }} ?>


div[data-v-project-hero-component-link] | if_exists = $is_admin


#th-feature-project | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$feature_project = $current_component = $this->_component['featureprojectsmasonry']?? [];

	// echo '<pre>';
	// print_r($feature_project);
	// echo '</pre>';
	$is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;

?>
[data-v-component-featureprojectsmasonry] [data-v-featureprojectsmasonry-*]|innerText = $feature_project['@@__data-v-featureprojectsmasonry-(*)__@@']

[data-v-component-featureprojectsmasonry] [data-v-featureproject-component-link] | prepend = <?php if(isset($feature_project['component_link'])){ ?>
	[data-v-component-featureprojectsmasonry] [data-v-featureproject-component-link] a | href = <?php echo isset($feature_project['component_link']) ? $feature_project['component_link'] : ''; ?>
[data-v-component-featureprojectsmasonry] [data-v-featureproject-component-link] | append = <?php } ?>

[data-v-component-featureprojectsmasonry] div.th-masonry-grid > div.th-masonry-grid-item | deleteAllButFirst
[data-v-component-featureprojectsmasonry] div.th-masonry-grid | prepend = <?php if(isset($feature_project['items'])){ foreach ($feature_project['items'] as $item) { ?>
	div[data-v-featureprojectsmasonryitem-class] | class = <?php echo isset($item["class"]) ? $item["class"] : ''; ?>
	img[data-v-featureprojectsmasonryitem-img] | src = <?php echo isset($item["img"]) ? $item["img"] : ''; ?>
	img[data-v-featureprojectsmasonryitem-img] | alt = <?php echo isset($item["img"]) ? $item["heading"] : ''; ?>
	span[data-v-featureprojectsmasonryitem-designer-by] | innerHTML = <?php echo isset($item["credit_label"]) ? $item["credit_label"] . ' ' : ''; ?>
	span[data-v-featureprojectsmasonryitem-designer] | innerHTML = <?php echo isset($item["designer"]) ? $item["designer"] : ''; ?>
	div[data-v-featureprojectsmasonryitem-heading] | innerHTML = <?php echo isset($item["heading"]) ? $item["heading"] : ''; ?>
	div[data-v-featureprojectsmasonryitem-des] | innerHTML = <?php echo isset($item["preview_text"]) ? $item["preview_text"] : ''; ?>
	a[data-v-featureprojectsmasonryitem-link] | href = <?php echo isset($item["link"]) ? $item["link"] : ''; ?>
[data-v-component-featureprojectsmasonry] div.th-masonry-grid | append = <?php }} ?>

div[data-v-featureproject-component-link] | if_exists = $is_admin


#projects-list | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$all_projects = $current_component = $this->_component['allprojects']?? [];
	$total_data_count = isset($all_projects['total']) ? $all_projects['total'] : 0;
	$show_total_pages = isset($all_projects['show_total_pages']) ? $all_projects['show_total_pages'] : 0;
	$current_page = isset($all_projects['current_page']) ? $all_projects['current_page'] : 1;	
	$per_page = isset($all_projects['per_page']) ? $all_projects['per_page'] : 0;

	$is_show_load_more_btn = $total_data_count < $show_total_pages ? true : false;
	// echo '<pre>';
	// print_r($is_show_load_more_btn);
	// echo '</pre>';

	// echo '<pre>';
	// print_r($all_projects);
	// echo '</pre>';
	$is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;

?>
[data-v-component-allprojects] [data-v-allprojects-*]|innerText = $all_projects['@@__data-v-allprojects-(*)__@@']
[data-v-component-allprojects] [data-v-allprojects-component-link] | prepend = <?php if(isset($all_projects['component_link'])){ ?>
	[data-v-component-allprojects] [data-v-allprojects-component-link] a | href = <?php echo isset($all_projects['component_link']) ? $all_projects['component_link'] : ''; ?>
[data-v-component-allprojects] [data-v-allprojects-component-link] | append = <?php } ?>
div[data-v-allprojects-component-link] | if_exists = $is_admin

#total-projects-count | innerHTML = <?php echo isset($all_projects['total']) ? $all_projects['total'] : 0; ?>
[data-v-component-allprojects] div.th-projects-list > div.project-item | deleteAllButFirst
[data-v-component-allprojects] div.th-projects-list | prepend = <?php if(isset($all_projects['items'])){ foreach ($all_projects['items'] as $item) { ?>
[data-v-allprojectsitem-*] | innerHTML = $item['@@__data-v-allprojectsitem-(*)__@@']

a[data-v-allprojectsitem-edit-link] | href = <?php echo isset($item['edit_link']) ? $item['edit_link'] : ''; ?>
a[data-v-allprojectsitem-edit-link] | if_exists = $is_admin

div[data-v-allprojectsitem-add-to-pinboard] | data-id = <?php echo isset($item['project_id']) ? $item['project_id'] : ''; ?>
div[data-v-allprojectsitem-add-to-pinboard] | data-model = project
div[data-v-allprojectsitem-add-to-pinboard] | data-title = <?php echo isset($item['title']) ? $item['title'] : ''; ?>
div[data-v-allprojectsitem-add-to-pinboard] | data-description = <?php echo isset($item['preview_text']) ? $item['preview_text'] : ''; ?>
div[data-v-allprojectsitem-add-to-pinboard] | data-image = <?php echo isset($item['image_thumb']) ? $item['image_thumb'] : ''; ?>
div[data-v-allprojectsitem-add-to-pinboard] | data-product-url = <?php echo isset($item["slug"]) ? "/projects"."/".$item["slug"] : ''; ?>
img[data-v-allprojectsitem-image] | src = <?php echo isset($item["image_thumb"]) ? $item["image_thumb"] : ''; ?>
img[data-v-allprojectsitem-image] | alt = <?php echo isset($item["title"]) ? $item["title"] : ''; ?>
div[data-v-allprojectsitem-label] | innerHTML = <?php echo isset($item["designer"]) ? $item["designer"] : ''; ?>
h3[data-v-allprojectsitem-title] | innerHTML = <?php echo isset($item["title"]) ? $item["title"] : ''; ?>
div[data-v-allprojectsitem-description] | innerHTML = <?php echo isset($item["preview_text"]) ? $item["preview_text"] : ''; ?>
div[data-v-allprojectsitem-link_text] | innerHTML = <?php echo isset($item["link_text"]) ? $item["link_text"] : ''; ?>
a[data-v-allprojectsitem-slug] | href = <?php echo isset($item["slug"]) ? "/projects"."/".$item["slug"] : ''; ?>
[data-v-component-allprojects] div.th-projects-list | append = <?php }} ?>

#all-project-pagination | data-total-projects-count = <?php echo $total_data_count; ?>
#all-project-pagination | data-show-data-count = <?php echo $show_total_pages; ?>
#all-project-pagination | data-current-page = <?php echo $current_page ; ?>
#all-project-pagination | data-per-page = <?php echo $per_page; ?>
div[data-v-load-more-container] | class = <?php echo $is_show_load_more_btn ? 'hidden_load' : 'row justify-content-center th-pt-80'; ?>
<!-- div[data-v-load-more-container] | if_exists = $is_show_load_more_btn -->



import(components/footer.tpl, [data-v-component-footer])