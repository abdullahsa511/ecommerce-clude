import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])
#textiles | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $design_resource_textiles = $current_component = $this->_component['designresourcetextiles']?? [];

    // echo '<pre>';
	// print_r($design_resource_textiles);
	// echo '</pre>';

?>  

[data-v-component-designresourcetextiles] [data-v-designresourcetextiles-*]|innerText = $design_resource_textiles['@@__data-v-designresourcetextiles-(*)__@@']

.textiles-list > div.textiles-item | deleteAllButFirst
.textiles-list | prepend = <?php if(isset($design_resource_textiles['items'])){ foreach ($design_resource_textiles['items'] as $textile) { ?>
	img[data-v-designresourcetextilesitem-image] | src = <?php echo isset($textile["image"]) ? $textile["image"] : ''; ?>
	h6[data-v-designresourcetextilesitem-title] | innerHTML = <?php echo isset($textile["title"]) ? $textile["title"] : ''; ?>
	span[data-v-designresourcetextilesitem-grade] | innerHTML = <?php echo isset($textile["grade"]) ? $textile["grade"] : ''; ?>
	div[data-v-designresourcetextilesitem-link_text] | innerHTML = <?php echo isset($textile["link_text"]) ? $textile["link_text"] : ''; ?>
	div[data-v-designresourcetextiles-total_result] | innerHTML = <?php echo isset($design_resource_textiles['total_result']) ? $design_resource_textiles['total_result'] : ''; ?>
.textiles-list | append = <?php }} ?>


import(components/footer.tpl, [data-v-component-footer])