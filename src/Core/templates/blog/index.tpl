import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])


#whats-happening | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$whats_happening = $current_component = $this->_component['whatishappening']?? [];

	// echo '<pre>';
	// print_r($whats_happening);
	// echo '</pre>';
	$is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;

?>

[data-v-whatishappening-section_title] | innerText = <?php echo isset($whats_happening['section_title']) ? $whats_happening['section_title'] : ''; ?>
[data-v-whatishappening-image] | src = <?php echo isset($whats_happening['image']) ? $whats_happening['image'] : ''; ?>
[data-v-whatishappening-image] | style = <?php echo isset($whats_happening['image']) ? "background-image: url('".$whats_happening['image']."');" : "background-image: url('/img/blog-page/whats-happening-left.png');"; ?>
[data-v-whatishappening-thumbnail-link] | href = <?php echo isset($whats_happening['title_link']) ? $whats_happening['title_link'] : ''; ?>

[data-v-whatishappening-thumbnail-link] | area-label = <?php echo isset($whats_happening['title_link']) ? $whats_happening['below_title'] : ''; ?>
[data-v-whatishappening-thumbnail-link] | title = <?php echo isset($whats_happening['title_link']) ? $whats_happening['below_title'] : ''; ?>


<!-- [data-v-whatishappening-link] a | href = <?php echo isset($whats_happening['title_link']) ? $whats_happening['title_link'] : ''; ?> -->
[data-v-whatishappening-below_title] h2 | innerText = <?php echo isset($whats_happening['below_title']) ? $whats_happening['below_title'] : ''; ?>
[data-v-whatishappening-below_title] | href = <?php echo isset($whats_happening['title_link']) ? $whats_happening['title_link'] : ''; ?>
[data-v-whatishappening-below_description] | innerText = <?php echo isset($whats_happening['below_description']) ? $whats_happening['below_description'] : ''; ?>


[data-v-component-whatishappening] [data-v-whatishappening-component-link] | prepend = <?php if(isset($whats_happening['component_link'])){ ?>
	[data-v-component-whatishappening] [data-v-whatishappening-component-link] a | href = <?php echo isset($whats_happening['component_link']) ? $whats_happening['component_link'] : ''; ?>
[data-v-component-whatishappening] [data-v-whatishappening-component-link] | append = <?php } ?>

div[data-v-whatishappening-items] > div.right-item | deleteAllButFirst
div[data-v-whatishappening-items] | prepend = <?php if(isset($whats_happening['items'])){ foreach ($whats_happening['items'] as $item) { ?>
	[data-v-whatishappening-item-date] | innerText = <?php echo isset($item['created']) ? $item['created'] : ''; ?>
a[data-v-whatishappeningitem-link] | href = <?php echo isset($item['link']) ? $item['link'] : ''; ?>
	[data-v-whatishappening-item-thumbnail] a | href = <?php echo isset($item['link']) ? $item['link'] : ''; ?>
	img[data-v-whatishappeningitem-image] | src = <?php echo isset($item['image']) ? $item['image'] : ''; ?>
	img[data-v-whatishappeningitem-image] | alt = <?php echo isset($item['image']) ? $item['title'] : ''; ?>
	p[data-v-whatishappeningitem-title] | innerHTML = <?php echo isset($item['title']) ? $item['title'] : ''; ?>
div[data-v-whatishappening-items] | append = <?php }} ?>
div[data-v-whatishappening-component-link] | if_exists = $is_admin


#blog-list | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$latest_news = $current_component = $this->_component['latestnews']?? [];
	$per_page = isset($latest_news['per_page']) ? $latest_news['per_page'] : 21;
	$total_data = isset($latest_news['total']) ? $latest_news['total'] : 0;
	$show_data = isset($latest_news['items']) ? count($latest_news['items']) : 0;
	$is_show_load_more_btn = $total_data == $show_data ? true : false;

	// echo '<pre>';
	// print_r($latest_news);
	// echo '</pre>';
	$is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>
[data-v-component-latestnews] [data-v-latestnews-*]|innerText = $latest_news['@@__data-v-latestnews-(*)__@@']

[data-v-component-latestnews] [data-v-latestnews-component-link] | prepend = <?php if(isset($latest_news['component_link'])){ ?>
	[data-v-component-latestnews] [data-v-latestnews-component-link] a | href = <?php echo isset($latest_news['component_link']) ? $latest_news['component_link'] : ''; ?>
[data-v-component-latestnews] [data-v-latestnews-component-link] | append = <?php } ?>

#total-posts-count | innerHTML = <?php echo isset($latest_news['total']) ? $latest_news['total'] : 0; ?>

[data-v-latestnews-items] > div.project-list-item | deleteAllButFirst
[data-v-latestnews-items] | prepend = <?php if(isset($latest_news['items'])){ foreach ($latest_news['items'] as $item) { ?>
	div[data-v-latestnewsitem-add-to-pinboard] | data-id = <?php echo isset($item['post_id']) ? $item['post_id'] : ''; ?>
	div[data-v-latestnewsitem-add-to-pinboard] | data-model = post
	div[data-v-latestnewsitem-add-to-pinboard] | data-title = <?php echo isset($item['title']) ? $item['title'] : ''; ?>
	div[data-v-latestnewsitem-add-to-pinboard] | data-description = <?php echo isset($item['excerpt']) ? $item['excerpt'] : ''; ?>
	div[data-v-latestnewsitem-add-to-pinboard] | data-image = <?php echo isset($item['image']) ? $item['image'] : ''; ?>
	div[data-v-latestnewsitem-add-to-pinboard] | data-product-url = <?php echo isset($item['link']) ? $item['link'] : ''; ?>
	[data-v-latestnewsitem-edit-link] | prepend = <?php if(isset($item['edit_link'])){ ?>
	a[data-v-latestnewsitem-edit-link] | href = <?php echo isset($item['edit_link']) ? $item['edit_link'] : ''; ?>
	[data-v-latestnewsitem-edit-link] | append = <?php } ?>
	img[data-v-latestnewsitem-image] | src = <?php echo isset($item['image']) ? $item['image'] : ''; ?>
	img[data-v-latestnewsitem-image] | alt = <?php echo isset($item['image']) ? $item['title'] : ''; ?>
	h6[data-v-latestnewsitem-title] | innerHTML = <?php echo isset($item['title']) ? $item['title'] : ''; ?>
	div[data-v-latestnewsitem-excerpt] | innerHTML = <?php echo isset($item['excerpt']) ? $item['excerpt'] : ''; ?>
	a[data-v-latestnewsitem-link] | href = <?php echo isset($item['link']) ? $item['link'] : ''; ?>
[data-v-latestnews-items] | append = <?php }} ?>
#all-blog-pagination | data-total-blogs-count= <?php echo $total_data; ?>
#all-blog-pagination | data-show-count = <?php echo isset($show_data) ? $show_data : 0; ?>
#all-blog-pagination | data-current-page= <?php echo isset($latest_news['current_page']) ? $latest_news['current_page'] : 1; ?>
#all-blog-pagination | data-per-page= <?php echo isset($latest_news['per_page']) ? $latest_news['per_page'] : 21; ?>
div[data-v-load-more-container] | class = <?php echo $is_show_load_more_btn ? 'hidden_load' : 'row justify-content-center th-pt-80'; ?>
div[data-v-latestnews-component-link] | if_exists = $is_admin
a[data-v-latestnewsitem-edit-link] | if_exists = $is_admin

import(components/footer.tpl, [data-v-component-footer])