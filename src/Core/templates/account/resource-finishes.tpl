import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])
#finishes | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $design_resource_finishes = $current_component = $this->_component['designresourcefinishes']?? [];

    // echo '<pre>';
	// print_r($design_resource_finishes);
	// echo '</pre>';

?>  

[data-v-component-designresourcefinishes] [data-v-designresourcefinishes-*]|innerText = $design_resource_finishes['@@__data-v-designresourcefinishes-(*)__@@']
div[data-v-designresourcefinishes-total_result] | innerHTML = <?php echo isset($design_resource_finishes['total_result']) ? $design_resource_finishes['total_result'] : ''; ?>

div.finishes-list > div.finishes-item | deleteAllButFirst
div.finishes-list | prepend = <?php if(isset($design_resource_finishes['items'])){ foreach ($design_resource_finishes['items'] as $finish) { ?>
	img[data-v-designresourcefinishesitem-image] | src = <?php echo isset($finish["image"]) ? $finish["image"] : ''; ?>
	h6[data-v-designresourcefinishesitem-title] | innerHTML = <?php echo isset($finish["title"]) ? $finish["title"] : ''; ?>
	span[data-v-designresourcefinishesitem-grade] | innerHTML = <?php echo isset($finish["grade"]) ? $finish["grade"] : ''; ?>

	a[data-v-designresourcefinishesitem-link_text] | innerHTML = <?php echo isset($finish["link_text"]) ? $finish["link_text"] : ''; ?>
	a[data-v-designresourcefinishesitem-link_text] | href = <?php echo isset($finish["link_text"]) ? $finish["link_text"] : ''; ?>
div.finishes-list | append = <?php }} ?>


import(components/footer.tpl, [data-v-component-footer])