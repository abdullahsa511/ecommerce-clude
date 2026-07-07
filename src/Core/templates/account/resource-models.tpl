import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])
#model | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $design_resource_models = $current_component = $this->_component['designresourcemodels']?? [];

    // echo '<pre>';
	// print_r($design_resource_models);
	// echo '</pre>';

?>

[data-v-component-designresourcemodels] [data-v-designresourcemodels-*]|innerText = $design_resource_models['@@__data-v-designresourcemodels-(*)__@@']
div[data-v-designresourcemodels-total_result] | innerHTML = <?php echo isset($design_resource_models['total_result']) ? $design_resource_models['total_result'] : ''; ?>
div[data-v-designresourcemodels-pagination] > div#current-page | innerHTML = <?php echo isset($design_resource_models['pagination']['current_page']) ? $design_resource_models['pagination']['current_page'] : ''; ?>
div[data-v-designresourcemodels-pagination] > div#per-page | innerHTML = <?php echo isset($design_resource_models['pagination']['per_page']) ? $design_resource_models['pagination']['per_page'] : ''; ?>
div[data-v-designresourcemodels-pagination] > div#offset | innerHTML = <?php echo isset($design_resource_models['pagination']['offset']) ? $design_resource_models['pagination']['offset'] : ''; ?>
div[data-v-designresourcemodels-pagination] > div#context | innerHTML = <?php echo isset($design_resource_models['pagination']['context']) ? $design_resource_models['pagination']['context'] : ''; ?>
div[data-v-designresourcemodels-pagination] > div#category | innerHTML = <?php echo isset($design_resource_models['pagination']['category']) ? $design_resource_models['pagination']['category'] : ''; ?>
div[data-v-designresourcemodels-pagination] > div#model_id | innerHTML = <?php echo isset($design_resource_models['pagination']['model_id']) ? $design_resource_models['pagination']['model_id'] : ''; ?>
div[data-v-designresourcemodels-pagination] > div#model_name | innerHTML = <?php echo isset($design_resource_models['pagination']['model_name']) ? $design_resource_models['pagination']['model_name'] : ''; ?>
div[data-v-designresourcemodels-pagination] > div#total | innerHTML = <?php echo isset($design_resource_models['total']) ? $design_resource_models['total'] : ''; ?>
[data-v-component-designresourcemodels] .model-list > div.model-item | deleteAllButFirst
[data-v-component-designresourcemodels] .model-list | prepend = <?php if(isset($design_resource_models['items'])){ foreach ($design_resource_models['items'] as $model) { ?>
[data-v-designresourcemodelsitem-*] | innerHTML = $model['@@__data-v-designresourcemodelsitem-(*)__@@']
img[data-v-designresourcemodelsitem-image] | src = <?php echo isset($model["image"]) ? $model["image"] : ''; ?>
h4[data-v-designresourcemodelsitem-title] | innerHTML = <?php echo isset($model["title"]) ? $model["title"] : ''; ?>


div[data-v-component-designresourcemodels] .design-resource-tags > .design-resource-tag | deleteAllButFirst
div[data-v-component-designresourcemodels] .design-resource-tags | prepend = <?php if(isset($model["design_resource_documents"])){ foreach ($model["design_resource_documents"] as $tag) { ?>
	a[data-v-designresourcemodelsitemtag-name] | innerHTML = <?php echo isset($tag["name"]) ? $tag["name"] : ''; ?>
	a[data-v-designresourcemodelsitemtag-link] | href = <?php echo isset($tag["name"]) ? $tag["name"] : ''; ?>
div[data-v-component-designresourcemodels] .design-resource-tags | append = <?php }} ?>

a[data-v-designresourcemodelsitem-link_text] | href = <?php echo isset($model["link_text"]) ? $model["link_text"] : ''; ?>
a[data-v-designresourcemodelsitem-link_text] | innerHTML = <?php echo isset($model["link_text"]) ? $model["link_text"] : ''; ?>
[data-v-component-designresourcemodels] .model-list | append = <?php }} ?>

import(components/footer.tpl, [data-v-component-footer])