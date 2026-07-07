import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])
#hero-about | before = <?php 
use function App\Core\System\utils\htmlToPlainText;
use function App\Core\System\utils\normalizeUrl;
$hero = $current_component = $this->_component['heroabout']?? [];

// echo '<pre>';
// print_r($hero);
// echo '</pre>';

$way_points = $hero['way_points']??[];
$is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>
[data-v-component-heroabout] [data-v-heroabout-*]|innerText = $hero['@@__data-v-heroabout-(*)__@@']

[data-v-heroabout-hero_image] | data-bg-src = <?php echo isset($hero['image']) ? $hero['image'] : '/img/bg/home/hero_home.jpg'; ?>

[data-v-component-heroabout] [data-v-heroabout-hero_title]|innerHTML = <?php echo $hero['hero_title']; ?>

[data-v-component-heroabout] [data-v-heroabout-component-link] a | href = <?php echo isset($hero['component_link']) ? $hero['component_link'] : ''; ?>
div[data-v-heroabout-component-link] | if_exists = $is_admin

<!-- heroabout buttons -->
[data-v-heroabout-button_group] [data-v-heroabout-button_div] | deleteAllButFirst
[data-v-heroabout-button_group] | prepend = <?php if(isset($hero['buttons']) && count($hero['buttons']) > 0){ foreach ($hero['buttons'] as $key => $button) { ?>
    a[data-v-heroabout-button_link] | class = <?php echo isset($button['anchor_class']) ? $button['anchor_class'] : ''; ?>
    a[data-v-heroabout-button_link] | href = <?php echo isset($button['link']) ? $button['link'] : ''; ?>
    span[data-v-heroabout-button_label] | innerText = <?php echo isset($button['title']) ? $button['title'] : ''; ?>
[data-v-heroabout-button_group] | append = <?php }} ?>

[data-v-about-hero-waypoints] | deleteAllButFirst
[data-v-about-hero-waypoints] | prepend = <?php if(isset($way_points)){ foreach ($way_points as $item) { ?>
div[data-v-about-hero-waypoint] | id = <?php echo isset($item["id"]) ? 'way-point-'. $item["id"] : ''; ?>
div[data-v-about-hero-waypoint] | style = <?php echo isset($item["leftPercent"]) && isset($item["topPercent"]) ? "left: ".$item["leftPercent"]."%; top: ".$item["topPercent"]."%;" : ''; ?>
a[data-v-about-hero-waypoint-link] | innerText = <?php echo isset($item["label"]) ? $item["label"] : ''; ?>
a[data-v-about-hero-waypoint-link] | href = <?php echo isset($item["href"]) ? trim($item["href"]) : ''; ?>
a[data-v-about-hero-waypoint-link] | id = <?php echo isset($item["id"]) ? $item["id"] : ''; ?>
[data-v-about-hero-waypoints] | append = <?php }} ?>


#about-established | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$aboutestablished = $current_component = $this->_component['aboutestablished']?? [];
	// echo '<pre>';
	// print_r($aboutestablished);
	// echo '</pre>';
	$is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>
	
[data-v-aboutestablished-component-link] a | href = <?php echo isset($aboutestablished['component_link']) ? $aboutestablished['component_link'] : ''; ?>
[data-v-aboutestablisheditem-title] | innerHTML = <?php echo isset($aboutestablished['section_title']) ? $aboutestablished['section_title'] : ''; ?>
[data-v-aboutestablisheditem-description] | innerHTML = <?php echo isset($aboutestablished['description']) ? $aboutestablished['description'] : ''; ?>


[data-v-aboutestablisheditem-button-link] | deleteAllButFirst
[data-v-aboutestablisheditem-button-link] | prepend = <?php if(isset($aboutestablished['buttons'])){ foreach ($aboutestablished['buttons'] as $button) { ?> 
	div[data-v-aboutestablisheditem-button-link] a | href = <?php echo isset($button['url']) ? $button['url'] : ''; ?>
	div[data-v-aboutestablisheditem-button-link] a | innerText = <?php echo isset($button['title']) ? $button['title'] : ''; ?>
[data-v-aboutestablisheditem-button-link] | append = <?php }} ?>

[data-v-aboutestablished-images] > div.th-about-established-image | deleteAllButFirst
[data-v-aboutestablished-images] | prepend = <?php if(isset($aboutestablished['images'])){ foreach ($aboutestablished['images'] as $establishImage) { ?>
[data-v-aboutestablished-image] > img | src = <?php echo $establishImage['objectURL']; ?>
[data-v-aboutestablished-image] > img | alt = <?php echo isset($aboutestablished['section_title']) ? $aboutestablished['section_title'] : ''; ?>
[data-v-aboutestablished-images] | append = <?php }} ?> 

div[data-v-aboutestablished-component-link] | if_exists = $is_admin

#about-pinciple | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$ourprinciple = $current_component = $this->_component['ourprinciple']?? [];

	// echo '<pre>';
	// print_r($ourprinciple['items']);
	// echo '</pre>';
	$is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;

?>
[data-v-ourprinciple-component-link] a | href = <?php echo isset($ourprinciple['component_link']) ? $ourprinciple['component_link'] : ''; ?>
[data-v-component-ourprinciple] [data-v-ourprinciple-*]|innerText = $ourprinciple['@@__data-v-ourprinciple-(*)__@@']
[data-v-component-ourprinciple] [data-v-ourprinciple-section_subtitle]|innerHTML = <?php echo $ourprinciple['section_subtitle']; ?>

[data-v-component-ourprinciple] .principle-list .principle-item | deleteAllButFirst
[data-v-component-ourprinciple] .principle-list | prepend = <?php if(isset($ourprinciple['items'])){ foreach ($ourprinciple['items'] as $item) { ?>
[data-v-ourprincipleitem-*] | innerHTML = $item['@@__data-v-ourprincipleitem-(*)__@@']
h3[data-v-ourprincipleitem-number] | innerHTML = <?php echo $item['number']; ?>
h5[data-v-ourprincipleitem-title] | innerHTML = <?php echo $item['title']; ?>
div[data-v-ourprincipleitem-description] | innerHTML = <?php echo $item['description']; ?>
[data-v-component-ourprinciple] .principle-list | append = <?php }} ?>

div[data-v-ourprinciple-component-link] | if_exists = $is_admin

#about-who-you | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $videogallerywhoweare = $current_component = $this->_component['videogallerywhoweare']?? [];
    // echo '<pre>';
    // print_r($videogallerywhoweare);
    // echo '</pre>';
    $is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>
[data-v-videogallerywhoweare-component-link] a | href = <?php echo isset($videogallerywhoweare['component_link']) ? $videogallerywhoweare['component_link'] : ''; ?>
h2[data-v-aboutwhoweare-section_title] | innerHTML = <?php echo $videogallerywhoweare['section_title']; ?>
div[data-v-aboutwhoweare-section_subtitle] | innerHTML = <?php echo $videogallerywhoweare['section_subtitle']; ?>

[data-v-gallery-whoweare] | prepend = <?php echo 'let whoWeAreData = ' . json_encode($videogallerywhoweare['items']??'[]'); ?>

div[data-v-videogallerywhoweare-component-link] | if_exists = $is_admin




.our-history-section | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $ourhistory = $current_component = $this->_component['ourhistory']?? [];
    // echo '<pre>';
    // print_r($ourhistory);
    // echo '</pre>';

?>
[data-v-component-ourhistory] [data-v-ourhistory-*]|innerText = $ourhistory['@@__data-v-ourhistory-(*)__@@']
[data-v-component-ourhistory] [data-v-ourhistory-image]|src = <?php echo $ourhistory['image']; ?>

[data-v-component-ourhistory] .history-list .history-item | deleteAllButFirst
[data-v-component-ourhistory] .history-list | prepend = <?php if(isset($ourhistory['items'])){ foreach ($ourhistory['items'] as $item) { ?>
[data-v-ourhistoryitem-*] | innerHTML = $item['@@__data-v-ourhistoryitem-(*)__@@']
h2[data-v-ourhistoryitem-title] | innerHTML = <?php echo $item['title']; ?>
h6[data-v-ourhistoryitem-description] | innerHTML = <?php echo $item['description']; ?>
[data-v-component-ourhistory] .history-list | append = <?php }} ?>

#our-history-masonary-section | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $ourhistorymasonry = $current_component = $this->_component['ourhistorymasonry']?? [];

    // echo '<pre>';
    // print_r($ourhistorymasonry);
    // echo '</pre>';
    $is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>
[data-v-ourhistorymasonry-component-link] a | href = <?php echo isset($ourhistorymasonry['component_link']) ? $ourhistorymasonry['component_link'] : ''; ?>
[data-v-component-ourhistorymasonry] [data-v-ourhistorymasonry-*]|innerText = $ourhistorymasonry['@@__data-v-ourhistorymasonry-(*)__@@']

h2[data-v-ourhistorymasonry-section-title] | innerHTML = <?php echo $ourhistorymasonry['section_title']; ?>
div[data-v-ourhistorymasonry-section-subtitle] | innerHTML = <?php echo $ourhistorymasonry['section_subtitle']; ?>
div[data-v-ourhistorymasonry-section-description] | innerHTML = <?php echo $ourhistorymasonry['description']; ?>


div[data-v-ourhistorymasonry-grid] > div.th-masonry-grid-item | deleteAllButFirst
div[data-v-ourhistorymasonry-grid] | prepend = <?php if(isset($ourhistorymasonry['items'])){ foreach ($ourhistorymasonry['items'] as $history) { ?>
    img[data-v-ourhistorymasonryitem-img] | src = <?php echo isset($history["img"]) ? normalizeUrl($history["img"]) : ''; ?>
    img[data-v-ourhistorymasonryitem-img] | alt = <?php echo isset($history["img"]) ? htmlToPlainText($history["heading"]) : ''; ?>
    div[data-v-ourhistorymasonryitem-class] | class = <?php echo isset($history["class"]) ? $history["class"] : ''; ?>
    div[data-v-ourhistorymasonryitem-style] | style = <?php echo isset($history["style"]) ? $history["style"] : ''; ?>
    h3[data-v-ourhistorymasonryitem-heading] | innerHTML = <?php echo isset($history["heading"]) ? $history["heading"] : ''; ?>
    div[data-v-ourhistorymasonryitem-des] | innerHTML = <?php echo isset($history["des"]) ? htmlToPlainText($history["des"]) : ''; ?>
    <!-- div[data-v-ourhistorymasonryitem-link_text] | innerHTML = <?php echo isset($history["link_text"]) ? $history["link_text"] : ''; ?> -->
    a[data-v-ourhistorymasonryitem-link] | href = <?php echo isset($history["link"]) ? '/about' . (strpos($history["link"], '/') === 0 ? $history["link"] : '/' . ltrim($history["link"], '/')) : ''; ?>
div[data-v-ourhistorymasonry-grid] | append = <?php }} ?>

div[data-v-ourhistorymasonry-component-link] | if_exists = $is_admin



#about-our-sustainable-value | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $aboutoursustainablevalue = $current_component = $this->_component['aboutoursustainablevalue']?? [];
    // echo '<pre>';
    // print_r($aboutoursustainablevalue);
    // echo '</pre>';
    $is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>
[data-v-aboutoursustainablevalue-component-link] a | href = <?php echo isset($aboutoursustainablevalue['component_link']) ? $aboutoursustainablevalue['component_link'] : ''; ?>
h2[data-v-aboutoursustainablevalue-section-title] | innerHTML = <?php echo isset($aboutoursustainablevalue['section_title']) ? $aboutoursustainablevalue['section_title'] : ''; ?>
div[data-v-aboutoursustainablevalue-section-subtitle] | innerHTML = <?php echo isset($aboutoursustainablevalue['section_subtitle']) ? $aboutoursustainablevalue['section_subtitle'] : ''; ?>

[data-v-component-aboutoursustainablevalue] [data-v-aboutoursustainablevalue-*]|innerText = $aboutoursustainablevalue['@@__data-v-aboutoursustainablevalue-(*)__@@']

div[data-v-aboutoursustainablevalue-left-content] [data-v-aboutoursustainablevalue-left-content-item] | deleteAllButFirst
div[data-v-aboutoursustainablevalue-left-content] | prepend = <?php if(isset($aboutoursustainablevalue['items'])){ foreach ($aboutoursustainablevalue['items'] as $index => $item) { ?>
    img[data-v-aboutoursustainablevalue-left-content-logo-img1] | src = <?php echo isset($item['logo']) ? normalizeUrl($item['logo']) : (isset($item['logo']) ? normalizeUrl($item['logo']) : ''); ?>
    img[data-v-aboutoursustainablevalue-left-content-logo-img1] | alt = <?php echo isset($item['logo']) ? $item['content_title'] : ''; ?>
    <!-- img[data-v-aboutoursustainablevalue-left-content-logo-img2] | src = <?php echo ($index === 0 && isset($item['logo2'])) ? $item['logo2'] : ''; ?>
    img[data-v-aboutoursustainablevalue-left-content-logo-img2] | style = <?php echo ($index === 0 && isset($item['logo2']) && !empty($item['logo2'])) ? '' : 'display: none;'; ?> -->

    h6[data-v-aboutoursustainablevalue-left-content-title] | innerHTML = <?php echo isset($item['content_title']) ? $item['content_title'] : ''; ?>
    p[data-v-aboutoursustainablevalue-left-content-description] | innerHTML = <?php echo isset($item['content_description']) ? $item['content_description'] : ''; ?>

div[data-v-aboutoursustainablevalue-left-content] | append = <?php }} ?>

div[data-v-aboutoursustainablevalue-right-content-image] img | src = <?php echo isset($aboutoursustainablevalue['image']) ? normalizeUrl($aboutoursustainablevalue['image']) : ''; ?>
div[data-v-aboutoursustainablevalue-right-content-image] img | alt = <?php echo isset($aboutoursustainablevalue['image']) ? "Our Sustainable Value" : ''; ?>
div[data-v-aboutoursustainablevalue-component-link] | if_exists = $is_admin

#our-design-process | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $designprocess = $current_component = $this->_component['ourdesignprocess']?? [];
    // echo '<pre>';
    // print_r($designprocess);
    // echo '</pre>';
    $is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>
[data-v-ourdesignprocess-component-link] a | href = <?php echo isset($designprocess['component_link']) ? $designprocess['component_link'] : ''; ?>
[data-v-component-ourdesignprocess] [data-v-ourdesignprocess-*]|innerText = $designprocess['@@__data-v-ourdesignprocess-(*)__@@']
h2[data-v-ourdesignprocess-section-title] | innerHTML = <?php echo isset($designprocess['section_title']) ? $designprocess['section_title'] : ''; ?>
div[data-v-ourdesignprocess-section-subtitle] | innerHTML = <?php echo isset($designprocess['section_subtitle']) ? $designprocess['section_subtitle'] : ''; ?>


div[data-v-ourdesignprocess-items] > div.th-design-process-items | deleteAllButFirst

div[data-v-ourdesignprocess-items] | prepend = <?php if(isset($designprocess['items'])){ foreach ($designprocess['items'] as $designItem) { ?>

[data-v-ourdesignprocessitem-step_number] | innerText = <?php echo $designItem['step_number'] ?? ''; ?>
[data-v-ourdesignprocessitem-step_title] | innerHTML = <?php echo $designItem['step_title'] ?? ''; ?>
[data-v-ourdesignprocessitem-step_description] | innerHTML = <?php echo $designItem['step_description'] ?? ''; ?>

div[data-v-ourdesignprocess-items] | append = <?php }} ?>

div[data-v-ourdesignprocess-component-link] | if_exists = $is_admin


#th-gallery-manufacturingprocess | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $manufacturingprocess = $current_component = $this->_component['videogallerymanufacturingprocess']?? [];
    $manufacturingprocessdata = $this->parameters['manufacturingprocessdata']?? [];
    // echo '<pre>';
    // print_r($manufacturingprocess);
    // echo '</pre>';
    $is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>
[data-v-videogallerymanufacturingprocess-component-link] a | href = <?php echo isset($manufacturingprocess['component_link']) ? $manufacturingprocess['component_link'] : ''; ?>
[data-v-component-videogallerymanufacturingprocess] [data-v-videogallerymanufacturingprocess-*]|innerText = $manufacturingprocess['@@__data-v-videogallerymanufacturingprocess-(*)__@@']
h2[data-v-videogallerymanufacturingprocess-section_title] | innerHTML = <?php echo $manufacturingprocess['section_title']; ?>
div[data-v-videogallerymanufacturingprocess-section_subtitle] | innerHTML = <?php echo $manufacturingprocess['section_subtitle']; ?>

[data-v-gallery-manufacturingprocess] | prepend = <?php echo 'let manufacturingProcessData = ' . json_encode($manufacturingprocess['items']); ?>


[data-v-videogallery-bakground-image] | data-bg-src = <?php echo isset($manufacturingprocess['background_image']) ? $manufacturingprocess['background_image'] : '/img/bg/home/hero_home.jpg'; ?>

div[data-v-videogallerymanufacturingprocess-component-link] | if_exists = $is_admin




import(components/needhelp.tpl, [data-v-component-needhelp])


import(components/footer.tpl, [data-v-component-footer])
