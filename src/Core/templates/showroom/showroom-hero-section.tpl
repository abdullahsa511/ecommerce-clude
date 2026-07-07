#showroom-hero-section | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $data = $current_component = $this->_component['showroomherosection'] ?? [];
    $way_points = $data['way_points']??[]; 
    // echo '<pre>';
    // print_r($data);
    // echo '</pre>';
    $is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;

?>
[data-v-showroom-edit-link] a | href = <?php echo isset($data['edit_link']) ? $data['edit_link'] : ''; ?>
[data-v-showroom-edit-link] | if_exists = $is_admin

[data-v-component-showroomherosection] [data-v-showroomherosetion-component-link] a | href = <?php echo isset($data['component_link']) ? $data['component_link'] : ''; ?>
[data-v-showroomherosetion-component-link] | if_exists = $is_admin

[data-v-showroom-hero_image] | data-bg-src = <?php echo isset($data['banner_image']) ? $data['banner_image'] : '/img/bg/home/hero_home.jpg'; ?>

span[data-v-showroom-breadcrumb] | innerText = <?php echo isset($data['title']) ? $data['title'] : ''; ?>
h1[data-v-showroom-title] > span | innerText = <?php echo isset($data['title']) ? $data['title'] : ''; ?>
span[data-v-showroom-openinfo] | innerText = <?php echo isset($data['opening_hours']) ? $data['opening_hours'] : ''; ?>
span[data-v-showroom-address] | innerText = <?php echo isset($data['address']) ? $data['address'] : ''; ?>
span[data-v-showroom-phone] | innerText = <?php echo isset($data['phone']) ? $data['phone'] : ''; ?>


[data-v-hero-waypoints] | deleteAllButFirst
[data-v-hero-waypoints] | prepend = <?php if(isset($way_points)){ foreach ($way_points as $item) { ?>
div[data-v-hero-waypoint] | id = <?php echo isset($item["id"]) ? 'way-point-'. $item["id"] : ''; ?>
div[data-v-hero-waypoint] | style = <?php echo isset($item["leftPercent"]) && isset($item["topPercent"]) ? "left: ".$item["leftPercent"]."%; top: ".$item["topPercent"]."%;" : ''; ?>
a[data-v-hero-waypoint-link] | innerText = <?php echo isset($item["label"]) ? $item["label"] : ''; ?>
a[data-v-hero-waypoint-link] | href = <?php echo isset($item["href"]) ? trim($item["href"]) : ''; ?>
a[data-v-hero-waypoint-link] | id = <?php echo isset($item["id"]) ? $item["id"] : ''; ?>
[data-v-hero-waypoints] | append = <?php }} ?>


div[data-v-showroom-hero_button_group] > div.button | deleteAllButFirst
div[data-v-showroom-hero_button_group] | prepend = <?php if(isset($data['buttons'])){ foreach ($data['buttons'] as $key => $button) { ?>
    span[data-v-showroom-hero_button_label] | innerText = <?php echo $button['title']??''; ?>
    a[data-v-showroom-hero_button_link] | href = <?php echo $button['url']??''; ?>
    a[data-v-showroom-hero_button_link] | class = <?php echo $key == 0 ? 'th-btn text-capitalize' : 'th-btn-outline text-capitalize'; ?>
div[data-v-showroom-hero_button_group] | append = <?php }} ?>



