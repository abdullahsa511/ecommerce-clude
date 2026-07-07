#th-showrooms | before = <?php 
	$showrooms = $current_component = $this->_component['showrooms']?? [];
	$items = isset($showrooms['items']) ? $showrooms['items'] : [];

	// echo '<pre>';
	// print_r($showrooms);
	// echo '</pre>';

	$is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;


?>
[data-v-showrooms-component-link] a | href = <?php echo isset($showrooms['component_link']) ? $showrooms['component_link'] : ''; ?>

[data-v-component-showrooms] [data-v-showrooms-*]|innerText = $showrooms['@@__data-v-showrooms-(*)__@@']


h2[data-v-showrooms-section_title] | innerText = <?php echo isset($showrooms['section_title']) ? $showrooms['section_title'] : ''; ?>

[data-v-component-showrooms] .showroom-list .showroom-item | deleteAllButFirst
[data-v-component-showrooms] .showroom-list | prepend = <?php foreach ($items as $showroom) { 
	$is_explore_link = $showroom['explore_link']?? false;
	$is_section_active = $showroom['is_section_active']?? false;
	?>
	[data-v-showroomitem-*] | innerHTML = $showroom['@@__data-v-showroomitem-(*)__@@']
	div[data-v-showroomitem-view_btn] | if_exists = $is_explore_link
	img[data-v-showroomitem-image] | src = <?php echo isset($showroom["image"]) ? $showroom["image"] : ''; ?>
	h3[data-v-showroomitem-title] | innerHTML = <?php echo isset($showroom["title"]) ? $showroom["title"] : ''; ?>
	p[data-v-showroomitem-opening_time] | innerHTML = <?php echo isset($showroom["opening_time"]) ? $showroom["opening_time"] : ''; ?>
	p[data-v-showroomitem-address] | innerHTML = <?php echo isset($showroom["address"]) ? $showroom["address"] : ''; ?>
	div[data-v-showroomitem-book_btn] | innerHTML = <?php echo isset($showroom["book_btn"]) ? $showroom["book_btn"] : ''; ?>
	div[data-v-showroomitem-view_btn] | innerHTML = <?php echo isset($showroom["view_btn"]) ? $showroom["view_btn"] : ''; ?>
	a[data-v-showroomitem-map_link] | href = <?php echo isset($showroom["map_link"]) ? $showroom["map_link"] : ''; ?>

	<!-- a[data-v-showroomitem-book_link] | href = <?php echo isset($showroom["book_link"] ) && $is_section_active == true ? $showroom["book_link"] : ''; ?> -->
	a[data-v-showroomitem-book_link] | href = <?php echo isset($showroom["book_link"] ) ? $showroom["book_link"] : ''; ?>

	<!-- a[data-v-showroomitem-view_link] | href = <?php 
        echo isset($showroom["view_link"]) && $is_section_active == 0 ? '/contact-sales#book-now' : $showroom["view_link"];
    ?> -->

	a[data-v-showroomitem-view_link] | if_exists = $is_section_active
	a[data-v-showroomitem-view_link] | href = <?php echo isset($showroom["view_link"]) ? $showroom["view_link"] : ''; ?>

	div[data-v-showroomitem-inactive_media] | hide = $is_section_active
	div[data-v-showroomitem-inactive_title] | hide = $is_section_active

	a[data-v-showroomitem-explore_link] | href = <?php echo isset($showroom["explore_link"]) ? $showroom["explore_link"] : ''; ?>
	
[data-v-component-showrooms] .showroom-list | append = <?php } ?>

div[data-v-showrooms-component-link] | if_exists = $is_admin