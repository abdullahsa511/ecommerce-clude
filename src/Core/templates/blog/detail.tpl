import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])

#th-blog-hero | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$blogdetailshero = $current_component = $this->_component['blogdetailshero']?? [];
	$blogName = isset($blogdetailshero['name']) ? $blogdetailshero['name'] : '';
	// echo '<pre>';
	// print_r($blogdetailshero);
	// echo '</pre>';
	$is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>

<!-- dynamic breakcrumbs  -->

[data-v-component-blogdetailshero] [data-v-blogdetails-*]|innerText = $blogdetailshero['@@__data-v-blogdetails-(*)__@@']
[data-v-blogdetails-edit-link] a | href = <?php echo isset($blogdetailshero['edit_link']) ? $blogdetailshero['edit_link'] : ''; ?>
div[data-v-blogdetails-edit-link] | if_exists = $is_admin
[data-v-component-blogdetailshero] [data-v-blog-hercomponent-link] | prepend = <?php if(isset($blogdetailshero['component_link'])){ ?>
	[data-v-component-blogdetailshero] [data-v-blog-hercomponent-link] a | href = <?php echo isset($blogdetailshero['component_link']) ? $blogdetailshero['component_link'] : ''; ?>
[data-v-component-blogdetailshero] [data-v-blog-hercomponent-link] | append = <?php } ?>
div[data-v-blog-hercomponent-link] | if_exists = $is_admin

span[data-v-breadcrumb-name] | innerText = <?php echo isset($blogdetailshero['name']) ? $blogdetailshero['name'] : ''; ?>


[data-v-blogdetails-hero-img]|data-bg-src = <?php echo htmlentities($blogdetailshero['image_banner'])??'/img/project-detail/hero.png'; ?>

div[data-v-blogdetailshero-add-to-pinboard] | data-id = <?php echo isset($blogdetailshero['post_id']) ? $blogdetailshero['post_id'] : ''; ?>
div[data-v-blogdetailshero-add-to-pinboard] | data-model = post
div[data-v-blogdetailshero-add-to-pinboard] | data-title = <?php echo isset($blogdetailshero['name']) ? $blogdetailshero['name'] : ''; ?>
div[data-v-blogdetailshero-add-to-pinboard] | data-description = <?php echo  ''; ?>
div[data-v-blogdetailshero-add-to-pinboard] | data-image = <?php echo isset($blogdetailshero['image_banner']) ? $blogdetailshero['image_banner'] : ''; ?>
div[data-v-blogdetailshero-add-to-pinboard] | data-product-url = <?php echo isset($blogdetailshero['slug']) ? "/blog"."/".$blogdetailshero['slug'] : ''; ?>


[data-v-blogdetails-waypoints] | deleteAllButFirst
[data-v-blogdetails-waypoints] | prepend = <?php if(isset($blogdetailshero['way_points'])){ foreach ($blogdetailshero['way_points'] as $item) { ?>
div[data-v-blogdetails-waypoint] | id = <?php echo isset($item["id"]) ? 'way-point-'. $item["id"] : ''; ?>
div[data-v-blogdetails-waypoint] | style = <?php echo isset($item["leftPercent"]) && isset($item["topPercent"]) ? "left: ".$item["leftPercent"]."%; top: ".$item["topPercent"]."%;" : ''; ?>
a[data-v-blogdetails-waypoint-link] | innerText = <?php echo isset($item["label"]) ? $item["label"] : ''; ?>
a[data-v-blogdetails-waypoint-link] | href = <?php echo isset($item["href"]) ? trim($item["href"]) : ''; ?>
a[data-v-blogdetails-waypoint-link] | id = <?php echo isset($item["id"]) ? $item["id"] : ''; ?>
[data-v-blogdetails-waypoints] | append = <?php }} ?>

span[data-v-blogdetails-date]|innerText = <?php echo isset($blogdetailshero['created_at']) ? $blogdetailshero['created_at'] : ''; ?>
h1[data-v-blogdetails-heading]|innerText = <?php echo isset($blogdetailshero['name']) ? $blogdetailshero['name'] : ''; ?>


#th-blog-detail | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$blogdetailsexcerpt = $current_component = $this->_component['blogdetailsexcerpt']?? [];
	
	// echo '<pre>';
	// print_r($blogdetailsexcerpt);
	// echo '</pre>';
?>
[data-v-component-blogdetail] [data-v-blogdetail-*]|innerText = $blogdetailsexcerpt['@@__data-v-blogdetail-(*)__@@']

[data-v-component-blogdetailsexcerpt] [data-v-blog-excerpt-component-link] | prepend = <?php if(isset($blogdetailsexcerpt['component_link'])){ ?>
	[data-v-component-blogdetailsexcerpt] [data-v-blog-excerpt-component-link] a | href = <?php echo isset($blogdetailsexcerpt['component_link']) ? $blogdetailsexcerpt['component_link'] : ''; ?>
[data-v-component-blogdetailsexcerpt] [data-v-blog-excerpt-component-link] | append = <?php } ?>
div[data-v-blog-excerpt-component-link] | if_exists = $is_admin

h2[data-v-blogdetail-section_title]|innerText = <?php echo isset($blogdetailsexcerpt['section_title']) ? $blogdetailsexcerpt['section_title'] : ''; ?>
p[data-v-blogdetail-description]|innerText = <?php echo isset($blogdetailsexcerpt['description']) ? $blogdetailsexcerpt['description'] : ''; ?>


#th-blog-main | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$blogmain = $current_component = $this->_component['blogmain']?? [];
	$featureImage = isset($blogmain['img']) ? $blogmain['img'] : '';
	$isFeatureImageShow = $featureImage ? true:false;
	// echo '<pre>';
	// print_r($blogmain);
	// echo '</pre>';
	$mainImageAlt = !empty(trim($blogName))
    ? trim($blogName) . ' - ' . $featureImage . ' - Main Image'
    : $isFeatureImageShow . ' - Main Image';
?>
[data-v-component-blogmain] [data-v-blogmain-*]|innerText = $blogmain['@@__data-v-blogmain-(*)__@@']
[data-v-component-blogmain] [data-v-blog-main-component-link] | prepend = <?php if(isset($blogmain['component_link'])){ ?>
	[data-v-component-blogmain] [data-v-blog-main-component-link] a | href = <?php echo isset($blogmain['component_link']) ? $blogmain['component_link'] : ''; ?>
[data-v-component-blogmain] [data-v-blog-main-component-link] | append = <?php } ?>
div[data-v-blog-main-component-link] | if_exists = $is_admin

h2[data-v-blogmain-section_title]|innerHTML = <?php echo isset($blogmain['section_title']) ? $blogmain['section_title'] : ''; ?>
div[data-v-blogmain-section_subtitle]|innerHTML = <?php echo isset($blogmain['section_subtitle']) ? str_replace('&nbsp;', ' ', $blogmain['section_subtitle']) : ''; ?>
img[data-v-blogmain-img] | src = <?php echo isset($featureImage) ? $featureImage : ''; ?>
img[data-v-blogmain-img] | alt = <?php echo isset($mainImageAlt) ? $mainImageAlt : ''; ?>
#th-blog-main-content-text | class = <?php echo empty($featureImage) ? 'col-lg-12 th-blog-main-content th-blog-main-content-px' : 'col-lg-6 th-blog-main-content th-blog-main-content-px'; ?>
div[data-v-blogmain-section_subtitle2]|innerHTML = <?php echo isset($blogmain['section_subtitle2']) ? str_replace('&nbsp;', ' ', $blogmain['section_subtitle2']) : ''; ?>
div[data-v-blogmain-section_subtitle3]|innerHTML = <?php echo isset($blogmain['section_subtitle3']) ? str_replace('&nbsp;', ' ', $blogmain['section_subtitle3']) : ''; ?>
div[data-v-blogmain-section_subtitle4]|innerHTML = <?php echo isset($blogmain['section_subtitle4']) ? str_replace('&nbsp;', ' ', $blogmain['section_subtitle4']) : ''; ?>
#th-blog-main-content-image | if_exists = $isFeatureImageShow


#blog-detail-gallery| before = <?php 
	use function App\Core\System\utils\normalizeUrl;
    if(isset($current_component)) $previous_component = $current_component;
	$blog_gallery = $current_component = $this->_component['bloggallery']?? [];

	// echo '<pre>';
	// print_r($blog_gallery);
	// echo '</pre>';

	$showBlogGallery = isset($blog_gallery['items']) && count($blog_gallery['items']) > 0;

?>
[data-v-component-bloggallery] [data-v-bloggallery-component-link] | prepend = <?php if(isset($blog_gallery['component_link'])){ ?>
	[data-v-component-bloggallery] [data-v-bloggallery-component-link] a | href = <?php echo isset($blog_gallery['component_link']) ? $blog_gallery['component_link'] : ''; ?>
[data-v-component-bloggallery] [data-v-bloggallery-component-link] | append = <?php } ?>
div[data-v-bloggallery-component-link] | if_exists = $is_admin

div[data-v-projectgallery-thumbs] > button | deleteAllButFirst
div[data-v-projectgallery-thumbs] | prepend = <?php if(isset($blog_gallery['items'])){ foreach ($blog_gallery['items'] as $item) { ?>
button[data-v-projectgalleryitem-thumb] | class = <?php echo isset($item["thumb_class"]) ? $item["thumb_class"] : ''; ?>
img[data-v-projectgalleryitem-thumb_image] | src = <?php echo isset($item["thumb_image"]) ? $item["thumb_image"] : ''; ?>
[data-v-projectgalleryitem-thumb_image] | alt = <?php echo isset($item["alt_text"]) && !empty($item["alt_text"]) ? $item["alt_text"] : 'Gallery'; ?>
button[data-v-projectgalleryitem-thumb] | id = <?php echo isset($item["thumb_id"]) ? $item["thumb_id"] : ''; ?>
button[data-v-projectgalleryitem-thumb] | data-bs-target = <?php echo isset($item["target"]) ? $item["target"] : ''; ?>
div[data-v-projectgallery-thumbs] | append = <?php }} ?>

div[data-v-bloggallery-content] > div | deleteAllButFirst
div[data-v-bloggallery-content] | prepend = <?php if(isset($blog_gallery['items'])){ foreach ($blog_gallery['items'] as $tabItem) {
	//  echo '<pre>';
	//  print_r($tabItem);
	//  echo '</pre>';
	?>
div[data-v-bloggallery-content-item] | class = <?php echo isset($tabItem["class"]) ? $tabItem["class"] : ''; ?>
img[data-v-bloggallery-content-item-img] | src = <?php echo isset($tabItem["image"]) ? normalizeUrl($tabItem["image"]) : ''; ?>
[data-v-bloggallery-content-item-img] | alt = <?php echo isset($tabItem["alt_text"]) ? normalizeUrl($tabItem["alt_text"]) : ''; ?>
div[data-v-bloggallery-content-item] | id = <?php echo isset($tabItem["id"]) ? $tabItem["id"] : ''; ?>
div[data-v-bloggallery-content-item] | aria-labelledby = <?php echo isset($tabItem["thumb_id"]) ? $tabItem["thumb_id"] : ''; ?>
div[data-v-bloggallery-content] | append = <?php }} ?>

[data-v-component-bloggallery] | if_exists = $showBlogGallery


#th-blog-related-article-slider| before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $blogrelatedarticleslider = $current_component = $this->_component['blogrelatedarticleslider']?? [];
    // echo '<pre>';
    // print_r($blogrelatedarticleslider);
    // echo '</pre>';
	$isShowRelatedArticlesSlider = isset($blogrelatedarticleslider['items']) && count($blogrelatedarticleslider['items']) > 0;
?>

div[data-v-blogrelatedarticleslider-component-link] a | href = <?php echo isset($blogrelatedarticleslider['component_link']) ? $blogrelatedarticleslider['component_link'] : ''; ?>
div[data-v-blogrelatedarticleslider-component-link] | if_exists = $is_admin

[data-v-blogrelatedarticleslider-section_title] | innerHTML = <?php echo isset($blogrelatedarticleslider['section_title']) ? $blogrelatedarticleslider['section_title'] : ''; ?>
[data-v-blogrelatedarticleslider-section_subtitle] | innerHTML = <?php echo isset($blogrelatedarticleslider['section_subtitle']) ? $blogrelatedarticleslider['section_subtitle'] : ''; ?>
a[data-v-blogrelatedarticleslider-link_url] | href = <?php echo isset($blogrelatedarticleslider['link_url']) ? $blogrelatedarticleslider['link_url'] : ''; ?>

.th-related-articles-slider > .swiper-wrapper > .swiper-slide | deleteAllButFirst

.th-related-articles-slider > .swiper-wrapper | prepend = <?php if(isset($blogrelatedarticleslider['items'])){ foreach ($blogrelatedarticleslider['items'] as $item) { ?>
	
img[data-v-blogrelatedarticleslideritem-image] | src = <?php echo isset($item["image"]) ? $item["image"] : ''; ?>
img[data-v-blogrelatedarticleslideritem-image] | alt = <?php echo isset($item["title"]) ? $item["title"] : ''; ?>
a[data-v-blogrelatedarticleslideritem-url] | href = <?php echo isset($item["slug"]) ? "/blog"."/".$item["slug"] : ''; ?>
h3[data-v-blogrelatedarticleslideritem-name] | innerHTML = <?php echo isset($item["title"]) ? $item["title"] : ''; ?>
div[data-v-blogrelatedarticleslideritem-description] | innerHTML = <?php echo isset($item["description"]) ? $item["description"] : ''; ?>
div[data-v-blogrelatedarticleslideritem-add-to-pinboard] | data-id = <?php echo isset($item["id"]) ? $item["id"] : '55'; ?>
div[data-v-blogrelatedarticleslideritem-add-to-pinboard] | data-model = <?php echo isset($item["model"]) ? $item["model"] : 'blog'; ?>
div[data-v-blogrelatedarticleslideritem-add-to-pinboard] | data-title = <?php echo isset($item["title"]) ? $item["title"] : ''; ?>
div[data-v-blogrelatedarticleslideritem-add-to-pinboard] | data-description = <?php echo isset($item["description"]) ? $item["description"] : ''; ?>
div[data-v-blogrelatedarticleslideritem-add-to-pinboard] | data-image = <?php echo isset($item["image"]) ? $item["image"] : ''; ?>
div[data-v-blogrelatedarticleslideritem-add-to-pinboard] | data-blog-url = <?php echo isset($item["slug"]) ? "/blog"."/".$item["slug"] : ''; ?>

.th-related-articles-slider .swiper-wrapper | append = <?php }} ?>


import(components/needhelp.tpl, [data-v-component-needhelp])
import(components/footer.tpl, [data-v-component-footer])
