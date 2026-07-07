import(components/header.tpl, [data-v-component-header])

#model-3d | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $design_resource_models = $current_component = $this->_component['designresourcemodels']?? [];

    // echo '<pre>';
	// print_r($design_resource_models);
	// echo '</pre>';
?>

[data-v-component-designresourcemodels] [data-v-designresourcemodels-*]|innerText = $design_resource_models['@@__data-v-designresourcemodels-(*)__@@']

[data-v-component-designresourcemodels] .model-list > div.model-item | deleteAllButFirst
[data-v-component-designresourcemodels] .model-list | prepend = <?php if(isset($design_resource_models['items'])){ foreach ($design_resource_models['items'] as $model) { ?>
[data-v-designresourcemodelsitem-*] | innerHTML = $model['@@__data-v-designresourcemodelsitem-(*)__@@']
img[data-v-designresourcemodelsitem-image] | src = <?php echo isset($model["image"]) ? $model["image"] : ''; ?>
h4[data-v-designresourcemodelsitem-title] | innerHTML = <?php echo isset($model["title"]) ? $model["title"] : ''; ?>
div[data-v-designresourcemodelsitem-link_text] | innerHTML = <?php echo isset($model["link_text"]) ? $model["link_text"] : ''; ?>
div[data-v-component-designresourcemodels] .design-resource-tags > .design-resource-tag | deleteAllButFirst
div[data-v-component-designresourcemodels] .design-resource-tags | prepend = <?php if(isset($model["tags"])){ foreach ($model["tags"] as $tag) { ?>
p[data-v-designresourcemodelsitemtag-name] | innerHTML = <?php echo isset($tag["name"]) ? $tag["name"] : ''; ?>
div[data-v-component-designresourcemodels] .design-resource-tags | append = <?php }} ?>
[data-v-component-designresourcemodels] .model-list | append = <?php }} ?>




import(components/footer.tpl, [data-v-component-footer])