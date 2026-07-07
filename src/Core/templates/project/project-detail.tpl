import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])


import(components/hero-project.tpl, [data-v-component-heroproject])

#project-detail-under-hero | before = <?php

use function App\Core\System\utils\echo_content;

    if(isset($current_component)) $previous_component = $current_component;
	$project_detail_under_hero = $current_component = $this->_component['projectdetailunderhero']?? [];
	$hideProjectDetailUnderHero = 0;
	$totalCountUnderHero = 0;
	if(isset($project_detail_under_hero['items'])){
		foreach ($project_detail_under_hero['items'] as $item) {
			if($item['subtitle'] && !$hideProjectDetailUnderHero){
				$hideProjectDetailUnderHero = true;
			}
			if($item['subtitle']){
				$totalCountUnderHero++;
			}
		}
	}
	
	// echo '<pre>';
	// print_r($hideProjectDetailUnderHero);
	// echo '</pre>';

?>

[data-v-component-projectdetailunderhero] [data-v-projectunderhero-component-link] a | href = <?php echo isset($project_detail_under_hero['component_link']) ? $project_detail_under_hero['component_link'] : ''; ?>
[data-v-projectunderhero-component-link] | if_exists = $is_admin

[data-v-component-projectdetailunderhero] [data-v-projectdetailunderhero-*]|innerText = $project_detail_under_hero['@@__data-v-projectdetailunderhero-(*)__@@']

[data-v-component-projectdetailunderhero] .under-hero-list > .under-hero-item | deleteAllButFirst
[data-v-component-projectdetailunderhero] .under-hero-list | prepend = <?php if(isset($project_detail_under_hero['items'])){ foreach ($project_detail_under_hero['items'] as $item) { ?>
div[data-v-projectdetailunderheroitem-class] | class = <?php echo isset($item["class"]) ? $item["class"] : ''; ?>
h5[data-v-projectdetailunderheroitem-title] | innerHTML = <?php echo isset($item["title"]) ? $item["title"] : ''; ?>
h6[data-v-projectdetailunderheroitem-subtitle] | innerHTML = <?php echo isset($item["subtitle"]) ? $item["subtitle"] : ''; ?>
div[data-v-projectdetailunderheroitem-class] | if_exists = $item["subtitle"]
[data-v-component-projectdetailunderhero] .under-hero-list | append = <?php }} ?>
[data-v-component-projectdetailunderhero] .under-hero-list | class = <?php echo 'row under-hero-list d-flex under-hero-count-' . $totalCountUnderHero; ?>


section[data-v-projectdetailunderhero-container] | if_exists = $hideProjectDetailUnderHero


#project-brief| before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$project_details = $current_component = $this->_component['projectdetails']?? [];
	// echo '<pre>';
	// print_r($project_details);
	// echo '</pre>';

?>

[data-v-component-projectdetails] [data-v-projectdetails-component-link] a | href = <?php echo isset($project_details['component_link']) ? $project_details['component_link'] : ''; ?>
[data-v-projectdetails-component-link] | if_exists = $is_admin

[data-v-component-projectdetails] [data-v-projectdetails-*]|innerHTML = $project_details['@@__data-v-projectdetails-(*)__@@']
[data-v-component-projectdetails] [data-v-projectdetails-description]|innerHTML = <?php isset($project_details['description']) ? echo_content($project_details['description']) : ''; ?>
[data-v-component-projectdetails] [data-v-projectdetails-description-two]|innerHTML = <?php echo isset($project_details['description-two']) ? echo_content($project_details['description-two']) : ''; ?>
h2[data-v-projectdetails-section_title]|innerHTML = <?php echo isset($project_details['keyline_quote']) ? $project_details['keyline_quote'] : ''; ?>

#photo-gallery| before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$project_gallery = $current_component = $this->_component['projectgallery']?? [];

	$isShowGallery = is_array($project_gallery['galleryThumb'] ?? null)
    && count($project_gallery['galleryThumb']) > 0;
	
	// echo '<pre>';
	// print_r($project_gallery);
	// echo '</pre>';
?>


[data-v-component-projectgallery] [data-v-projectgallery-component-link] a | href = <?php echo isset($project_gallery['component_link']) ? $project_gallery['component_link'] : ''; ?>
[data-v-projectgallery-component-link] | if_exists = $is_admin


#gallery-tabs > button | deleteAllButFirst
#gallery-tabs | prepend = <?php if(isset($project_gallery['galleryThumb'])){ foreach ($project_gallery['galleryThumb'] as $item) { ?>
	button[data-v-projectgalleryitem-thumb-class] | class = <?php echo isset($item["thumb_class"]) ? $item["thumb_class"] : ''; ?>
	img[data-v-projectgalleryitem-thumb_image] | src = <?php echo isset($item["thumb_image"]) ? $item["thumb_image"] : ''; ?>
	img[data-v-projectgalleryitem-thumb_image] | alt = <?php echo isset($item["thumb_image"]) ? $item["alt"] : ''; ?>
	button[data-v-projectgalleryitem-thumb_id] | id = <?php echo isset($item["thumb_id"]) ? $item["thumb_id"] : ''; ?>
	button[data-v-projectgalleryitem-thumb_id] | data-bs-target = <?php echo isset($item["target"]) ? $item["target"] : ''; ?>
[data-v-component-projectgallery] #gallery-tabs | append = <?php }} ?>

#gallery-tabs-content > div | deleteAllButFirst
#gallery-tabs-content | prepend = <?php if(isset($project_gallery['galleryThumb'])){ foreach ($project_gallery['galleryThumb'] as $item) { ?>
div[data-v-projectgalleryitem-class] | class = <?php echo isset($item["class"]) ? $item["class"] : ''; ?>
img[data-v-projectgalleryitem-image] | src = <?php echo isset($item["image"]) ? $item["image"] : ''; ?>
[data-v-projectgalleryitem-image] | alt = <?php echo isset($item["image"]) ? $item["alt"] : ''; ?>
div[data-v-projectgalleryitem-tabcontent] | id = <?php echo isset($item["id"]) ? $item["id"] : ''; ?>
div[data-v-projectgalleryitem-tabcontent] | aria-labelledby = <?php echo isset($item["thumb_id"]) ? $item["thumb_id"] : ''; ?>
[data-v-component-projectgallery] #gallery-tabs-content | append = <?php }} ?>
#photo-gallery | if_exists = $isShowGallery

#lorem-penetrating| before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$project_detail_penetrating = $current_component = $this->_component['projectdetailpenetrating']?? [];
	// echo '<pre>';
	// print_r($project_detail_penetrating);
	// echo '</pre>';

?>


[data-v-component-projectdetailpenetrating] [data-v-projectdetailpenetrating-component-link] a | href = <?php echo isset($project_detail_penetrating['component_link']) ? $project_detail_penetrating['component_link'] : ''; ?>
[data-v-projectdetailpenetrating-component-link] | if_exists = $is_admin



span[data-v-projectdetailpenetrating-main_description_one] | innerHTML = <?php isset($project_detail_penetrating['main_description_one']) ? echo_content($project_detail_penetrating['main_description_one']) : ''; ?>
img[data-v-projectdetailpenetrating-main_image_one] | src = <?php echo isset($project_detail_penetrating['main_image_one']) ? $project_detail_penetrating['main_image_one'] : ''; ?>
img[data-v-projectdetailpenetrating-main_image_one] | alt = <?php echo isset($project_detail_penetrating['project_title']) ? $project_detail_penetrating['project_title'] : ''; ?>
h2[data-v-projectdetailpenetrating-title] | innerHTML = <?php isset($project_detail_penetrating['keyline_quote']) ? echo_content($project_detail_penetrating['keyline_quote']) : 'Title Here'; ?>
div[data-v-projectdetailpenetrating-main_description_two] | innerHTML = <?php isset($project_detail_penetrating['main_description_two']) ? echo_content($project_detail_penetrating['main_description_two']) : ''; ?>
span[data-v-projectdetailpenetrating-main_description_three] | innerHTML = <?php isset($project_detail_penetrating['main_description_three']) ? echo_content($project_detail_penetrating['main_description_three']) : ''; ?>
img[data-v-projectdetailpenetrating-main_image_two] | src = <?php echo isset($project_detail_penetrating['main_image_two']) ? $project_detail_penetrating['main_image_two'] : ''; ?>
img[data-v-projectdetailpenetrating-main_image_two] | alt = <?php echo isset($project_detail_penetrating['project_title']) ? $project_detail_penetrating['project_title'] : ''; ?>
div[data-v-projectdetailpenetrating-main_description_three_style] | style = <?php isset($project_detail_penetrating['main_description_three']) && strlen($project_detail_penetrating['main_description_three']) > 20 ? "margin-top: 40px" : ''; ?>


#th-product-slider| before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $featuredproductslider = $current_component = $this->_component['featuredproductslider']?? [];
    // echo '<pre>';
    // print_r($featuredproductslider);
    // echo '</pre>';
	$isShowFeaturedProductSlider = isset($featuredproductslider['items']) && count($featuredproductslider['items']) > 0;
?>

[data-v-component-featuredproductslider] [data-v-featuredproductslider-component-link] a | href = <?php echo isset($featuredproductslider['component_link']) ? $featuredproductslider['component_link'] : ''; ?>
[data-v-featuredproductslider-component-link] | if_exists = $is_admin

[data-v-component-featuredproductslider] [data-v-featuredproductslider-]|innerText = $featuredproductslider['@@__data-v-featuredproductslider-()__@@']
[data-v-featuredproductslider-section_subtitle] | innerHTML = <?php echo isset($featuredproductslider['section_subtitle']) ? $featuredproductslider['section_subtitle'] : ''; ?>
a[data-v-featuredproductslider-link_url] | href = <?php echo isset($featuredproductslider['link_url']) ? $featuredproductslider['link_url'] : ''; ?>
[data-v-component-featuredproductslider] .swiper-wrapper > .swiper-slide | deleteAllButFirst
[data-v-component-featuredproductslider] .swiper-wrapper | prepend = <?php if(isset($featuredproductslider['items'])){ foreach ($featuredproductslider['items'] as $item) { ?>
[data-v-featuredproductslideritem-] | innertHTML = $item['@@__data-v-featuredproductslideritem-()__@@']
img[data-v-featuredproductslideritem-image] | src = <?php echo isset($item["image"]) ? $item["image"] : ''; ?>
img[data-v-featuredproductslideritem-image] | alt = <?php echo isset($item["title"]) ? $item["title"] : ''; ?>
a[data-v-featuredproductslideritem-url] | href = <?php echo isset($item["slug"]) ? "/products"."/".$item["category_slug"]."/".$item["slug"] : ''; ?>
h3[data-v-featuredproductslideritem-name] | innerHTML = <?php echo isset($item["title"]) ? $item["title"] : ''; ?>
p[data-v-featuredproductslideritem-category] | innerHTML = <?php echo isset($item["category"]) ? $item["category"] : ''; ?>
div[data-v-featuredproductslideritem-add-to-pinboard] | data-id = <?php echo isset($item["id"]) ? $item["id"] : '55'; ?>
div[data-v-featuredproductslideritem-add-to-pinboard] | data-model = <?php echo isset($item["model"]) ? $item["model"] : 'product'; ?>
div[data-v-featuredproductslideritem-add-to-pinboard] | data-title = <?php echo isset($item["title"]) ? $item["title"] : ''; ?>
div[data-v-featuredproductslideritem-add-to-pinboard] | data-description = <?php echo isset($item["description"]) ? $item["description"] : ''; ?>
div[data-v-featuredproductslideritem-add-to-pinboard] | data-image = <?php echo isset($item["image"]) ? $item["image"] : ''; ?>
div[data-v-featuredproductslideritem-add-to-pinboard] | data-product-url = <?php echo isset($item["slug"]) ? "/products"."/".$item["category_slug"]."/".$item["slug"] : ''; ?>

div[data-v-featuredproductslideritem-tags] > .th-tag | deleteAllButFirst
div[data-v-featuredproductslideritem-tags] | prepend = <?php if(isset($item['tags'])){ foreach ($item['tags'] as $tag) { ?>
	div[data-v-featuredproductslideritem-tags] > .th-tag | innerText = <?php echo $tag??""; ?>
div[data-v-featuredproductslideritem-tags] | append = <?php }} ?>

[data-v-component-featuredproductslider] .swiper-wrapper | append = <?php }} ?>
[data-v-component-featuredproductslider] | if_exists = $isShowFeaturedProductSlider

import(components/project_slider.tpl, [data-v-component-featuredprojectslider])

import(components/needhelp.tpl, [data-v-component-needhelp])


#th-featured-material| before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$featuredmaterialslider = $current_component = $this->_component['featuredmaterialslider']?? [];
	// echo '<pre>';
	// print_r($featuredmaterialslider);
	// echo '</pre>';
?>
h2[data-v-featuredmaterialslider-section_title] | innerHTML = <?php echo isset($featuredmaterialslider['section_title']) ? $featuredmaterialslider['section_title'] : ''; ?>
div[data-v-featuredmaterialslider-section_subtitle] | innerHTML = <?php echo isset($featuredmaterialslider['section_subtitle']) ? $featuredmaterialslider['section_subtitle'] : ''; ?>

[data-v-component-featuredmaterialslider] [data-v-featuredmaterialslider-section_link] | href = <?php echo isset($featuredmaterialslider['section_link']) ? $featuredmaterialslider['section_link'] : ''; ?>
[data-v-component-featuredmaterialslider] [data-v-featuredmaterialslider-section_link_text] | innerHTML = <?php echo isset($featuredmaterialslider['section_link_text']) ? $featuredmaterialslider['section_link_text'] : ''; ?>


[data-v-component-featuredmaterialslider] [data-v-featuredmaterialslider-*]|innerText = $featuredmaterialslider['@@__data-v-featuredmaterialslider-(*)__@@']

[data-v-component-featuredmaterialslider] .featured-material-list > .featured-material-item | deleteAllButFirst
[data-v-component-featuredmaterialslider] .featured-material-list | prepend = <?php if(isset($featuredmaterialslider['items'])){ foreach ($featuredmaterialslider['items'] as $item) { ?>
img[data-v-featuredmaterialslideritem-image] | src = <?php echo isset($item["image"]) ? $item["image"] : ''; ?>
p[data-v-featuredmaterialslideritem-category] | innerHTML = <?php echo isset($item["category"]) ? $item["category"] : ''; ?>
h3[data-v-featuredmaterialslideritem-name] | innerHTML = <?php echo isset($item["name"]) ? $item["name"] : ''; ?>
span[data-v-featuredmaterialslideritem-description] | innerHTML = <?php echo isset($item["description"]) ? $item["description"] : ''; ?>
[data-v-component-featuredmaterialslider] .featured-material-list | append = <?php }} ?>


import(components/footer.tpl, [data-v-component-footer])