import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])

import(showroom-hero-section.tpl, [data-v-component-showroomherosection])

#th-showroom-interactive-tour-section | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $showroom = $current_component = $this->_component['showroominteractivetoursection'] ?? [];
    $sections = $showroom['sections'] ?? null;
    // echo '<pre>';
    // print_r($showroom);
    // echo '</pre>';
?>

h2[data-v-section-tour-title] | innerText = <?php echo isset($showroom['title']) ? $showroom['title'] : ''; ?>
img[data-v-section-tour-map-image] | src = <?php echo isset($showroom['overview_image']) ? $showroom['overview_image'] : ''; ?>
img[data-v-section-tour-map-image] | alt = <?php echo isset($showroom['overview_alt']) ? $showroom['overview_alt'] : ''; ?>
div[data-v-section-tour-showroom-slug] | innerText = <?php echo isset($showroom['slug']) ? $showroom['slug'] : ''; ?>

div[data-v-section-tour-items] div[data-v-section-tour-row] | deleteAllButFirst
div[data-v-section-tour-items] | prepend = <?php if(isset($sections)){ foreach ($sections as $row) { ?> 
    div[data-v-section-tour-row] div[data-v-section-item] | deleteAllButFirst
    div[data-v-section-tour-row] | prepend = <?php if(is_array($row)){ foreach($row as $idx => $item) { ?>
        div[data-v-section-item] | addClass = <?php echo $idx % 2 == 0 ? 'even' : 'odd'; ?>
        div[data-v-section-item] | item-number = <?php echo $idx + 1; ?>
        div[data-v-section-item] | item-id = <?php echo $item['project_sections_id']; ?>
        img[data-v-section-item-card-image] | src = <?php echo isset($item['image']) ? $item['image'] : ''; ?>
        img[data-v-section-item-card-image] | alt = <?php echo isset($item['alt']) ? $item['alt'] : ''; ?>
        div[data-v-section-item-label] | innerHTML = <?php echo isset($item['title']) ? $item['title'] : ''; ?>
    div[data-v-section-tour-row] | append = <?php }} ?>
div[data-v-section-tour-items] | append = <?php }} ?>




