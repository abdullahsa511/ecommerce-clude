#home-blogs | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$blogs = $current_component = $this->_component['blogslider']?? [];

	// echo '<pre>';
	// print_r($blogs['component_link']);
	// echo '</pre>';

  $is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>
[data-v-component-blogslider] [data-v-blogslider-*]|innerText = $blogs['@@__data-v-blogslider-(*)__@@']

[data-v-blogslider-component-link] a | href = <?php echo isset($blogs['component_link']) ? $blogs['component_link'] : ''; ?>
[data-v-component-blogslider] [data-v-blogslider-section_title] | innerHTML = <?php echo isset($blogs["section_title"]) ? $blogs["section_title"] : ''; ?>
[data-v-component-blogslider] [data-v-blogslider-section_subtitle] | innerHTML = <?php echo isset($blogs["section_subtitle"]) ? $blogs["section_subtitle"] : ''; ?>

[data-v-component-blogslider] [data-v-blogslider-section_link] | href = <?php echo isset($blogs["section_link"]) ? $blogs["section_link"] : ''; ?>
[data-v-component-blogslider] [data-v-blogslider-section_link_text] | innerHTML = <?php echo isset($blogs["section_link_text"]) ? $blogs["section_link_text"] : 'View All Articles'; ?>

[data-v-component-blogslider] div.swiper-wrapper > div.swiper-slide | deleteAllButFirst
[data-v-component-blogslider] div.swiper-wrapper | prepend = <?php if(isset($blogs['items'])){ foreach ($blogs['items'] as $img =>$blog) { ?>
[data-v-blogslideritem-*] | innerHTML = $blog['@@__data-v-blogslideritem-(*)__@@']
img[data-v-blogslideritem-image] | src = <?php echo isset($blog["image"]) ? (is_array($blog['image']) && isset($blog['image'][0]['objectUrl']) ? $blog['image'][0]['objectUrl'] : $blog["image"]) : ''; ?>
img[data-v-blogslideritem-image] | alt = <?php echo isset($blog["name"]) ? htmlspecialchars($blog['name']) : ''; ?>
h6[data-v-blogslideritem-name] | innerHTML = <?php echo isset($blog["name"]) ? $blog["name"] : ''; ?>
p[data-v-blogslideritem-excerpt] | innerHTML = <?php echo isset($blog["excerpt"]) ? $blog["excerpt"] : ''; ?>
a[data-v-blogslideritem-slug] | href = <?php echo isset($blog["slug"]) ? "/blog/". $blog["slug"] : ''; ?>
div[data-v-blogslideritem-add-to-pinboard] | data-id = <?php echo isset($blog["post_id"]) ? $blog["post_id"] : ''; ?>
div[data-v-blogslideritem-add-to-pinboard] | data-product-url = <?php echo isset($blog["slug"]) ? "/blog/". $blog["slug"] : ''; ?>
div[data-v-blogslideritem-add-to-pinboard] | data-title = <?php echo isset($blog["name"]) ? $blog["name"] : ''; ?>
div[data-v-blogslideritem-add-to-pinboard] | data-description = <?php echo isset($blog["description"]) ? $blog["description"] : ''; ?>
div[data-v-blogslideritem-add-to-pinboard] | data-image = <?php 
if(isset($blog['feature_image_thumb'])){
  $img = json_decode($blog['feature_image_thumb'], true);	
    $img = $img[0]??null;
    $img = $img['objectURL']??null;
    echo $img;
  }
?>
[data-v-component-blogslider] div.swiper-wrapper | append = <?php }} ?>

div[data-v-blogslider-component-link] | if_exists = $is_admin