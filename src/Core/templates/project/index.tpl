import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])


.whats-happening-container | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$whats_happening = $current_component = $this->_component['whatishappening']?? [];

	// echo '<pre>';
	// print_r($design_resources['items']);
	// echo '</pre>';

?>
[data-v-component-whatishappening] [data-v-whatishappening-*]|innerText = $whats_happening['@@__data-v-whatishappening-(*)__@@']

[data-v-component-whatishappening] .whats-happening-right > div.right-item | deleteAllButFirst
[data-v-component-whatishappening]  .whats-happening-right | prepend = <?php if(isset($whats_happening['items'])){ foreach ($whats_happening['items'] as $item) { ?>
[data-v-whatishappeningitem-*] | innertHTML = $item['@@__data-v-whatishappeningitem-(*)__@@']
img[data-v-whatishappeningitem-image] | src = <?php echo isset($item['image']) ? $item['image'] : ''; ?>
p[data-v-whatishappeningitem-title] | innerHTML = <?php echo isset($item['title']) ? $item['title'] : ''; ?>
[data-v-component-whatishappening] .whats-happening-right | append = <?php }} ?>


#projects-list | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$latest_news = $current_component = $this->_component['latestnews']?? [];

	// echo '<pre>';
	// print_r($design_resources['items']);
	// echo '</pre>';

?>
[data-v-component-latestnews] [data-v-latestnews-*]|innerText = $latest_news['@@__data-v-latestnews-(*)__@@']

[data-v-component-latestnews] .project-list-row > div.project-list-item | deleteAllButFirst
[data-v-component-latestnews]  .project-list-row | prepend = <?php if(isset($latest_news['items'])){ foreach ($latest_news['items'] as $item) { ?>
[data-v-latestnewsitem-*] | innertHTML = $item['@@__data-v-latestnewsitem-(*)__@@']
img[data-v-latestnewsitem-image] | src = <?php echo isset($item['image']) ? $item['image'] : ''; ?>
h6[data-v-latestnewsitem-title] | innerHTML = <?php echo isset($item['title']) ? $item['title'] : ''; ?>
[data-v-component-latestnews] .project-list-row | append = <?php }} ?>



import(components/footer.tpl, [data-v-component-footer])