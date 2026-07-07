import(components/head.tpl, [data-v-component-header])
import(components/header.tpl, [data-v-component-header])


#searchresulthero | before = <?php 
    $searchresulthero = $current_component = $this->_component['searchresulthero']?? [];
    // echo '<pre>';
    // print_r($searchresulthero);
    // echo '</pre>';

    $image = $searchresulthero['image'] ?? $searchresulthero['image'] ?? "";
    $bannerClass = $image?'':'th-no-hero-image';
    $showHeroSection = !!($image || $image);
    $showProductImage = !!($image && !$image);

?>

span[data-v-searchresulthero-heading-title] | innerText = <?php echo (isset($searchresulthero['title']) && $searchresulthero['title'] != '') ? 'Search results for ' : 'All Results'; ?>
[data-v-searchresulthero-title] | innerText = <?php echo $searchresulthero['title']??$searchresulthero['name']; ?>
[data-v-searchresulthero-subtitle] | innerText = <?php echo $searchresulthero['subtitle']?? 'Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.'; ?>
[data-v-searchresulthero-image] | data-bg-src = <?php echo str_replace(' ', '%20', $image??""); ?>
[data-v-searchresulthero-image] | data-class = <?php echo $bannerClass??""; ?>
[data-v-searchresulthero-image] | data-bg = <?php echo $banner??""; ?>

import(components/footer.tpl, [data-v-component-footer])