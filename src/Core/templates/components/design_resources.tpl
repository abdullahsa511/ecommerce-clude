#home-design-resources | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$design_resources = $current_component = $this->_component['designresources']?? [];

	// echo '<pre>';
	// print_r($design_resources['items']);
	// echo '</pre>';
	$is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;

?>
[data-v-component-designresources] [data-v-designresources-*]|innerText = $design_resources['@@__data-v-designresources-(*)__@@']

[data-v-designresources-component-link] a | href = <?php echo isset($design_resources['component_link']) ? $design_resources['component_link'] : ''; ?>

.design-resource-list > div.design-resource-item | deleteAllButFirst
.design-resource-list | prepend = <?php if(isset($design_resources['items'])){ foreach ($design_resources['items'] as $resource) { ?>
[data-v-designresourcesitem-*] | innertHTML = $resource['@@__data-v-designresourcesitem-(*)__@@']
img[data-v-designresourcesitem-img] | src = <?php echo isset($resource['img']) ? $resource['img'] : ''; ?>
img[data-v-designresourcesitem-img] | alt = <?php echo isset($resource['img']) && isset( $resource['title']) ? $resource['title'] : ''; ?>
h6[data-v-designresourcesitem-title] | innerHTML = <?php echo isset($resource['title']) ? $resource['title'] : ''; ?>
div[data-v-designresourcesitem-description] | innerHTML = <?php echo isset($resource['description']) ? $resource['description'] : ''; ?>
a[data-v-designresourcesitem-link] | href = <?php echo isset($resource['link']) ? $resource['link'] : ''; ?>
.design-resource-list | append = <?php }} ?>

div[data-v-designresources-component-link] | if_exists = $is_admin