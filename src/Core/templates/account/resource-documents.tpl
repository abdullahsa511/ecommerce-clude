import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])
#documents | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $design_resource_documents = $current_component = $this->_component['designresourcedocuments']?? [];

    // echo '<pre>';
	// print_r($design_resource_documents);
	// echo '</pre>';

?>

[data-v-component-designresourcedocuments] [data-v-designresourcedocuments-*]|innerText = $design_resource_documents['@@__data-v-designresourcedocuments-(*)__@@']
div[data-v-designresourcedocuments-total_result] | innerHTML = <?php echo isset($design_resource_documents['total_result']) ? $design_resource_documents['total_result'] : ''; ?>
div[data-v-designresourcedocuments-total_result] | innerHTML = <?php echo isset($design_resource_documents['total_result']) ? $design_resource_documents['total_result'] : ''; ?>
div[data-v-designresourcedocuments-pagination] > div#current-page | innerHTML = <?php echo isset($design_resource_documents['pagination']['current_page']) ? $design_resource_documents['pagination']['current_page'] : ''; ?>
div[data-v-designresourcedocuments-pagination] > div#per-page | innerHTML = <?php echo isset($design_resource_documents['pagination']['per_page']) ? $design_resource_documents['pagination']['per_page'] : ''; ?>
div[data-v-designresourcedocuments-pagination] > div#offset | innerHTML = <?php echo isset($design_resource_documents['pagination']['offset']) ? $design_resource_documents['pagination']['offset'] : ''; ?>
div[data-v-designresourcedocuments-pagination] > div#context | innerHTML = <?php echo isset($design_resource_documents['pagination']['context']) ? $design_resource_documents['pagination']['context'] : ''; ?>
div[data-v-designresourcedocuments-pagination] > div#category | innerHTML = <?php echo isset($design_resource_documents['pagination']['category']) ? $design_resource_documents['pagination']['category'] : ''; ?>
div[data-v-designresourcedocuments-pagination] > div#model_id | innerHTML = <?php echo isset($design_resource_documents['pagination']['model_id']) ? $design_resource_documents['pagination']['model_id'] : ''; ?>
div[data-v-designresourcedocuments-pagination] > div#model_name | innerHTML = <?php echo isset($design_resource_documents['pagination']['model_name']) ? $design_resource_documents['pagination']['model_name'] : ''; ?>
div[data-v-designresourcedocuments-pagination] > div#total | innerHTML = <?php echo isset($design_resource_documents['total']) ? $design_resource_documents['total'] : ''; ?>
[data-v-component-designresourcedocuments] .document-list > div.document-item | deleteAllButFirst
[data-v-component-designresourcedocuments] .document-list | prepend = <?php if(isset($design_resource_documents['items'])){ foreach ($design_resource_documents['items'] as $model) { ?>
[data-v-designresourcedocumentsitem-*] | innerHTML = $model['@@__data-v-designresourcedocumentsitem-(*)__@@']
img[data-v-designresourcedocumentsitem-image] | src = <?php echo isset($model["image"]) ? $model["image"] : ''; ?>
h4[data-v-designresourcedocumentsitem-title] | innerHTML = <?php echo isset($model["title"]) ? $model["title"] : ''; ?>

	div[data-v-component-designresourcedocuments] .design-resource-formats > .design-resource-tag | deleteAllButFirst
	div[data-v-component-designresourcedocuments] .design-resource-formats | prepend = <?php if(isset($model["design_resource_documents"])){ foreach ($model["design_resource_documents"] as $tag) { ?>
		a[data-v-designresourcedocumentsitem-name] | innerHTML = <?php echo isset($tag["name"]) ? $tag["name"] : ''; ?>
		a[data-v-designresourcedocumentsitem-link] | href = <?php echo isset($tag["name"]) ? $tag["name"] : ''; ?>
	div[data-v-component-designresourcedocuments] .design-resource-formats | append = <?php }} ?>

a[data-v-designresourcedocumentsitem-link_text] | href = <?php echo isset($model["link_text"]) ? $model["link_text"] : ''; ?>
a[data-v-designresourcedocumentsitem-link_text] | innerHTML = <?php echo isset($model["link_text"]) ? $model["link_text"] : ''; ?>
	
	
[data-v-component-designresourcedocuments] .document-list | append = <?php }} ?>


import(components/footer.tpl, [data-v-component-footer])