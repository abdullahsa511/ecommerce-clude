#home-categories | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $categories = $current_component = $this->_component['categoriesslidernav']?? [];
	// echo '<pre>';
	// print_r($categories);
	// echo '</pre>';
?>

// Section header data binding
[data-v-categoriesslidernav-section_title] | innerHTML = <?php echo isset($categories["section_title"]) ? $categories["section_title"] : ''; ?>
[data-v-categoriesslidernav-section_subtitle] | innerHTML = <?php echo isset($categories["section_subtitle"]) ? $categories["section_subtitle"] : ''; ?>
[data-v-categoriesslidernav-section_link_text] | innerHTML = <?php echo isset($categories["section_link_text"]) ? $categories["section_link_text"] : ''; ?>

// Navigation menu items
.th-menu-container > li | deleteAllButFirst
.th-menu-container | prepend = <?php if(isset($categories['collapseItems'])){ foreach ($categories['collapseItems'] as $menuItem) { ?>
[data-v-categoriesslidernav-collapseItems-menuTitle] | innerHTML = <?php echo isset($menuItem["menuTitle"]) ? $menuItem["menuTitle"] : ''; ?>

// Sub-menu items
.th-menu-container > li > ul.sub-menu > li | deleteAllButFirst
.th-menu-container > li > ul.sub-menu | prepend = <?php if(isset($menuItem['subMenuItems'])){ foreach ($menuItem['subMenuItems'] as $subItem) { ?>
[data-v-categoriesslidernav-collapseItems-subMenuItems-title] | innerHTML = <?php echo isset($subItem["title"]) ? $subItem["title"] : ''; ?>
[data-v-categoriesslidernav-collapseItems-subMenuItems-link] | href = <?php echo isset($subItem["link"]) ? $subItem["link"] : ''; ?>
[data-v-categoriesslidernav-collapseItems-subMenuItems-readMoreText] | innerHTML = <?php echo isset($subItem["readMoreText"]) ? $subItem["readMoreText"] : ''; ?>
.th-menu-container > li > ul.sub-menu | append = <?php }} ?>

.th-menu-container | append = <?php }} ?>

// Slider items
.slider-container .swiper-wrapper > div.swiper-slide | deleteAllButFirst
.slider-container .swiper-wrapper | prepend = <?php if(isset($categories['items'])){ foreach ($categories['items'] as $item) { ?>
[data-v-categoriesslidernav-items-image] | src = <?php echo isset($item["image"]) ? $item["image"] : ''; ?>
[data-v-categoriesslidernav-items-title] | innerHTML = <?php echo isset($item["title"]) ? $item["title"] : ''; ?>
[data-v-categoriesslidernav-items-subTitle] | innerHTML = <?php echo isset($item["subTitle"]) ? $item["subTitle"] : ''; ?>
[data-v-categoriesslidernav-items-link] | href = <?php echo isset($item["link"]) ? $item["link"] : ''; ?>
[data-v-categoriesslidernav-items-buttonText] | innerHTML = <?php echo isset($item["buttonText"]) ? $item["buttonText"] : ''; ?>
.slider-container .swiper-wrapper | append = <?php }} ?> 