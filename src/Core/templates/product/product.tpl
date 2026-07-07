import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])


#hero-product | before = <?php 
    $heroproduct = $current_component = $this->_component['heroproduct']?? [];
    // echo '<pre>';
    // print_r($heroproduct);
    // echo '</pre>';
// Banner & Product Image
$productName = isset($heroproduct['title']) ? $heroproduct['title'] : '';
$is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;

$bannerRaw = $heroproduct['banner_image'] ?? null;
if (is_array($bannerRaw)) {
    $banner = $bannerRaw[0]['objectURL'] ?? null;
} elseif (is_string($bannerRaw) && $bannerRaw !== '') {
    $banner = $bannerRaw;
} else {
    $banner = null;
}

$productImageRaw = $heroproduct['image'] ?? [];
if (is_string($productImageRaw)) { $productImageRaw = json_decode($productImageRaw, true); }
if (!is_array($productImageRaw)) { $productImageRaw = []; }
$productImage = $productImageRaw[0]['objectURL'] ?? null;

// Banner videos for video collage
$bannerVideos = $heroproduct['banner_videos'] ?? [];
try{
    if (is_string($bannerVideos)) {
        $bannerVideos = json_decode($bannerVideos, true);
        $bannerVideos = is_array($bannerVideos) ? $bannerVideos : [];
    }
}catch (Exception $e){
    $bannerVideos = [];
}

$showVideoBanner = !empty($bannerVideos);



// Hero visibility (priority: 1. banner_videos → 2. banner_image → 3. image)
$showBannerImage = !$showVideoBanner && !!$banner;
$showProductImage = !$showVideoBanner && !$showBannerImage && !!$productImage;


// $bannerClass = $banner ? '' : 'th-no-hero-image';
// $showHeroSection = !!($banner || $productImage || $showVideoBanner);
// $showProductImage = (!!$productImage && !!!$banner && !$showVideoBanner);
// Hero visibility & classes
$bannerClass = $showBannerImage ? '' : 'th-no-hero-image';
$showHeroSection = $showVideoBanner || $showBannerImage || $showProductImage;

// Button logic: store_link empty → Contact Sales; store_link set → Order Online. View Catalogue always shown. $heroproduct['store_link'] = null
$storeLinkTrimmed = isset($heroproduct['store_link']) && $heroproduct['store_link'] !== null ? trim((string) $heroproduct['store_link']) : '';
$productAvailableOnStore = $storeLinkTrimmed !== '';


$buttonContactSales  = !$productAvailableOnStore;
$buttonOrderOnline   = $productAvailableOnStore;

$catalogueLinkTrimmed = isset($heroproduct['catalogue_link']) ? trim((string) $heroproduct['catalogue_link']) : '';
$buttonViewCatalogue = $catalogueLinkTrimmed !== '';

// Breadcrumbs & Waypoints
$breadcrumbs = $heroproduct['breadcrumbs'] ?? [];
$waypoints = $heroproduct['way_points'] ?? [];

// echo '<pre>';
// print_r($heroproduct);
// echo '</pre>';

?>

<!-- Breadcrumbs -->
ol[data-v-product-breadcrumbs] > li[data-v-product-breadcrumb-list] | deleteAllButFirst
ol[data-v-product-breadcrumbs] | prepend = <?php if(is_array($breadcrumbs) && $breadcrumbs){ foreach ($breadcrumbs as $key => $breadcrumb){ if(!is_array($breadcrumb) || empty($breadcrumb['name']) || empty($breadcrumb['link'])) { continue; } if($key == count($breadcrumbs) - 1) { echo "&nbsp;/ ".$breadcrumb['name'] ?? ''; continue; } ?>
    li[data-v-product-breadcrumb-list] > a[data-v-product-breadcrumb-link] | innerText = <?php echo isset($breadcrumb['name']) ? $breadcrumb['name'] : ''; ?>
    li[data-v-product-breadcrumb-list] > a[data-v-product-breadcrumb-link] | href = <?php echo isset($breadcrumb['link']) ? $breadcrumb['link'] : ''; ?>
ol[data-v-product-breadcrumbs] | append = <?php }} ?>

<!-- Hero Product -->
[data-v-component-heroproduct] [data-v-product-edit-link] a | href = <?php echo isset($heroproduct['edit_link']) ? $heroproduct['edit_link'] : ''; ?>

[data-v-component-heroproduct] [data-v-heroproduct-component-link] a | href = <?php echo isset($heroproduct['component_link']) ? $heroproduct['component_link'] : ''; ?>
[data-v-heroproduct-component-link] | if_exists = $is_admin

[data-v-heroproduct-hero_image] | data-bg-src = <?php echo ($showBannerImage ? str_replace(' ', '%20', $banner??"") : ''); ?>
[data-v-heroproduct-hero_image] | data-class = <?php echo $bannerClass??""; ?>
[data-v-heroproduct-hero_image] | data-bg = <?php echo ($showBannerImage ? ($banner??"") : ''); ?>
[data-v-heroproduct-hero_title] | innerText = <?php echo $heroproduct['title'] ?? $heroproduct['name'] ?? ''; ?>
[data-v-heroproduct-hero_title] | style = <?php echo isset($heroproduct['title']) && is_string($heroproduct['title']) && $heroproduct['title'] !== '' && $heroproduct['title'][0] == 'J'?  "text-indent: 5px;": "text-indent: 0px;"; ?>
[data-v-heroproduct-hero_description] | innerText = <?php echo $heroproduct['tag_line'] ?? $heroproduct['description'] ?? ''; ?>
img[data-v-heroproduct-hero-img-src] | src = <?php echo $productImage ?? ''; ?>
img[data-v-heroproduct-hero-img-src] | alt = <?php echo $productName ?? ''; ?>

<!-- Buttons -->
[data-v-product-btn-group] | prepend = <?php if(isset($heroproduct['buttons']) && is_array($heroproduct['buttons'])){ foreach ($heroproduct['buttons'] as $key => $button) {
    if(!is_array($button)) continue;
    $buttonTitle = $button['title'] ?? '';
    $buttonUrl = $button['url'] ?? '#';
    if($productAvailableOnStore && stripos($buttonTitle, 'Order') !== false) $link = $storeLinkTrimmed;
    else if ($buttonViewCatalogue && stripos($buttonTitle, 'Catalogue') !== false) $link = $catalogueLinkTrimmed;
    else $link = $buttonUrl;
    $k = count($heroproduct['buttons']) - 1;
    if($showProductImage){
        $class = $key == $k ? 'th-btn th-btn-outline-black th-btn-text-preserve' : 'th-btn th-btn-primary th-btn-text-preserve';
    }else{
        $class = $key == $k ? 'th-btn-outline th-btn-text-preserve th-btn-text' :'th-btn-gray th-btn-text-preserve th-btn-text';
    }

    if($productAvailableOnStore && stripos($buttonTitle, 'Book') !== false) continue;
    else if(!$productAvailableOnStore && stripos($buttonTitle, 'Order') !== false) continue;
    else if (!$buttonViewCatalogue && stripos($buttonTitle, 'Catalogue') !== false) continue;
?>
a[data-v-product-btn-link] | href = <?php echo $link; ?>
a[data-v-product-btn-link] | class = <?php echo $class; ?>
a[data-v-product-btn-link] > span | innerText = <?php echo $buttonTitle; ?>
[data-v-product-btn-group] | append = <?php }} ?>



<!-- Pinboard -->
div[data-v-heroproduct-add-to-pinboard] | data-id = <?php echo isset($heroproduct['product_id']) ? $heroproduct['product_id'] : ''; ?>
div[data-v-heroproduct-add-to-pinboard] | data-model = product
<!-- div[data-v-heroproduct-add-to-pinboard] | data-title = <?php echo isset($heroproduct['title']) ? ucwords(str_replace(['_', '-'], ' ', strtolower($heroproduct['title']))) : ''; ?> -->
div[data-v-heroproduct-add-to-pinboard] | data-title = <?php echo isset($heroproduct['title']) ? $heroproduct['title'] : ''; ?>
div[data-v-heroproduct-add-to-pinboard] | data-description = <?php echo isset($heroproduct['tag_line']) ? $heroproduct['tag_line'] : ''; ?>
div[data-v-heroproduct-add-to-pinboard] | data-image = <?php echo isset($productImage) ? $productImage : ''; ?>
div[data-v-heroproduct-add-to-pinboard] | data-product-url = <?php echo isset($heroproduct['product_url']) ? $heroproduct['product_url'] : ''; ?>
<!-- Hero Section Classes -->
section[data-v-heroproduct-hero_image] | class = <?php 
echo ($showVideoBanner?'th-hero-wrapper th-hero th-hero-transparent bg-black th-breadcrumb-wrapper th-hero-product-centered th-way-points ' : 
    ($showProductImage?'th-hero-wrapper th-hero th-hero-primary-color th-breadcrumb-wrapper th-hero-product-centered th-way-points ' : 
'th-hero-wrapper th-hero th-hero-transparent gr-bg4 th-breadcrumb-wrapper th-hero-product-centered th-way-points')) ; ?>

div[data-v-heroproduct-banner-style] | class = <?php 
   echo ($showProductImage?"th-hero-container th-hero-primary-color":"th-hero-container gr-bg4");
?>


<!-- Video Banner - full-screen, sequential playback (video1 → video2 → video3 → loop) -->
[data-v-heroproduct-video-collage] | if_exists = $showVideoBanner
[data-v-heroproduct-video-collage] | data-videos = <?php echo $showVideoBanner && !empty($bannerVideos) ? htmlspecialchars(json_encode($bannerVideos), ENT_QUOTES, 'UTF-8') : '[]'; ?>

img[data-v-heroproduct-hero-img-src] | if_exists = $showProductImage
section[data-v-component-heroproduct] | if_exists = $showHeroSection

<!-- Waypoints -->
[data-v-heroproduct-waypoints] | deleteAllButFirst
[data-v-heroproduct-waypoints] | prepend = <?php if(is_array($waypoints) && $waypoints){ foreach ($waypoints as $item){ if(!is_array($item)) continue; ?>
div[data-v-heroproduct-waypoint] | id = <?php echo isset($item["id"]) ? 'way-point-'. $item["id"] : ''; ?>
div[data-v-heroproduct-waypoint] | style = <?php echo isset($item["leftPercent"]) && isset($item["topPercent"]) ? "left: ".$item["leftPercent"]."%; top: ".$item["topPercent"]."%;" : ''; ?>
a[data-v-heroproduct-waypoint-link] | innerText = <?php echo isset($item["label"]) ? $item["label"] : ''; ?>
a[data-v-heroproduct-waypoint-link] | href = <?php echo isset($item["href"]) ? trim($item["href"]) : ''; ?>
a[data-v-heroproduct-waypoint-link] | id = <?php echo isset($item["id"]) ? $item["id"] : ''; ?>
[data-v-heroproduct-waypoints] | append = <?php }} ?>

[data-v-product-edit-link] | if_exists = $is_admin

#product-story-masonry | before = <?php 
    $productstorymasonry = $current_component = $this->_component['productstorymasonry']?? [];
    // echo '<pre>';
    // print_r($productstorymasonry);
    // echo '</pre>';
    $is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>

[data-v-productstorymasonry-component-link] a | href = <?php echo isset($productstorymasonry['component_link']) ? $productstorymasonry['component_link'] : ''; ?>
[data-v-component-productstorymasonry] .th-masonry-grid-item | deleteAllButFirst
[data-v-component-productstorymasonry] .th-masonry-grid | prepend = <?php 
    if(isset($productstorymasonry['items']) && is_array($productstorymasonry['items'])) {
        foreach ($productstorymasonry['items'] as $item) {
            if(!is_array($item)) continue;
?>

[data-v-productstorymasonry-item] img[data-v-productstorymasonry-img] | src = <?php echo isset($item['img']) ? $item['img'] : ''; ?>
[data-v-productstorymasonry-item] | class = <?php echo isset($item['class']) ? $item['class'] : ''; ?>
[data-v-productstorymasonry-item] img[data-v-productstorymasonry-img] | alt = <?php echo isset($item['heading']) ? htmlspecialchars($item['heading']) : ''; ?>
[data-v-productstorymasonry-item] h6[data-v-productstorymasonry-heading] | innerText = <?php echo isset($item['heading']) ? $item['heading'] : ''; ?>
[data-v-productstorymasonry-item] p[data-v-productstorymasonry-des] | innerText = <?php echo isset($item['des']) ? $item['des'] : ''; ?>
[data-v-productstorymasonry-item] a[data-v-productstorymasonry-link] | href = <?php echo isset($item['link']) ? $item['link'] : '#'; ?>

[data-v-component-productstorymasonry] .th-masonry-grid | append = <?php 
        } 
    } 
?>

[data-v-productstorymasonry-component-link] | if_exists = $is_admin


#th-product-features | before = <?php 
    $productfeature = $current_component = $this->_component['productfeature']?? [];
    // echo '<pre>';
    // print_r($productfeature);
    // echo '</pre>';
    $is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>

[data-v-productfeature-component-link] a | href = <?php echo isset($productfeature['component_link']) ? $productfeature['component_link'] : ''; ?>
[data-v-component-productfeature] h2[data-v-productfeature-sectionTitle] | innerText = <?php echo isset($productfeature['sectionTitle']) ? $productfeature['sectionTitle'] : 'Features'; ?>
div[data-v-productfeature-section-subtitle] | innerHTML = <?php echo isset($productfeature['sectionSubtitle']) ? $productfeature['sectionSubtitle'] : ''; ?>
[data-v-component-productfeature] div[data-v-productfeature-sectionLinkText] | innerText = <?php echo isset($productfeature['sectionLinkText']) ? $productfeature['sectionLinkText'] : 'Order Online'; ?>
#online-order-link | href = <?php echo $storeLinkTrimmed ?? ''; ?>
div[data-v-productfeature-section] | if_exists = $buttonOrderOnline

[data-v-productfeature-items] [data-v-productfeature-item] | deleteAllButFirst
[data-v-productfeature-items] | prepend = <?php 
    if(isset($productfeature['items']) && is_array($productfeature['items'])) {
        foreach ($productfeature['items'] as $item) {
            if(!is_array($item)) continue;
?>

[data-v-productfeature-item] img[data-v-productfeature-img] | src = <?php echo isset($item['img']) ? $item['img'] : ''; ?>
[data-v-productfeature-item] img[data-v-productfeature-img] | alt = <?php echo isset($item['title']) ? htmlspecialchars($item['title']) : ''; ?>
[data-v-productfeature-item] h6[data-v-productfeature-title] | innerText = <?php echo isset($item['title']) ? $item['title'] : ''; ?>
[data-v-productfeature-item] .th-description[data-v-productfeature-description] | innerText = <?php echo isset($item['description']) ? $item['description'] : ''; ?>

[data-v-productfeature-items] | append = <?php 
        } 
    } 
?>
[data-v-productfeature-component-link] | if_exists = $is_admin

#th-product-configurator | before = <?php 
    $productconfigurator = $current_component = $this->_component['productconfigurator']?? [];
    $showConfigurator = isset($productconfigurator['show_configurator']) && $productconfigurator['show_configurator'] == 1 ? true : false;
    $configuratorVariants = (isset($productconfigurator['variants']) && is_array($productconfigurator['variants'])) ? $productconfigurator['variants'] : [];
    $firstVariantName = $configuratorVariants[0]['variant_name'] ?? '';
?>

[data-v-component-productconfigurator] h2[data-v-productconfigurator-section-title] | innerText = <?php echo isset($productconfigurator['sectionTitle']) ? $productconfigurator['sectionTitle'] : 'Product Configurator'; ?>
[data-v-component-productconfigurator] .th-section-subtitle[data-v-productconfigurator-section-subtitle] | innerHTML = <?php echo isset($productconfigurator['sectionSubtitle']) ? $productconfigurator['sectionSubtitle'] : 'Tailor this product to your project. Select your preferred finishes and configurations to suit your space.'; ?>

[data-v-productconfigurator-variant-card-lists] [data-v-productconfigurator-variant-card] | deleteAllButFirst
[data-v-productconfigurator-variant-card-lists] | prepend = <?php if(!empty($configuratorVariants)){ foreach ($configuratorVariants as $item) {
    if(!is_array($item)) continue;
    $itemVariantName = $item['variant_name'] ?? 'Demo Variant';
    $variantImage = 'https://dummyimage.com/120x120/444/fff&text=F';
    if (!empty($item['variant_image'])) {
        $variantImage = $item['variant_image'];
    } elseif (!empty($item['image'])) {
        $imgRaw = $item['image'];
        if (is_string($imgRaw)) {
            $imgDecoded = json_decode($imgRaw, true);
            if (is_array($imgDecoded) && isset($imgDecoded[0]['objectURL'])) {
                $variantImage = $imgDecoded[0]['objectURL'];
            } elseif ($imgRaw !== '' && ($imgRaw[0] ?? '') !== '[' && ($imgRaw[0] ?? '') !== '{') {
                $variantImage = $imgRaw;
            }
        } elseif (is_array($imgRaw) && isset($imgRaw[0]['objectURL'])) {
            $variantImage = $imgRaw[0]['objectURL'];
        }
    }
?>
    
    [data-v-productconfigurator-variant-card-lists] [data-v-productconfigurator-variant-card] .variant-selected-badge[data-v-productconfigurator-variant-selected-badge] | deleteAllButFirst
    [data-v-productconfigurator-variant-card] | addClass = <?php echo $firstVariantName === $itemVariantName ? 'selected' : ''; ?>

    [data-v-productconfigurator-variant-card] img[data-v-productconfigurator-variant-image] | src = <?php echo $variantImage; ?>
    [data-v-productconfigurator-variant-card] .variant-label[data-v-productconfigurator-variant-label] | innerText = <?php echo $itemVariantName; ?>
    [data-v-productconfigurator-variant-card] [data-v-productconfigurator-variant-selected-badge] | innerText = <?php echo $firstVariantName === $itemVariantName ? 'Selected' : ''; ?>
    
[data-v-productconfigurator-variant-card-lists] | append = <?php }}; ?>

[data-v-productconfigurator-script] | append = <?php echo "window.configuration = " . json_encode($productconfigurator['variants']??[]) . ";"; ?>
[data-v-productconfigurator-script] | append = <?php echo "window.modelData = " . json_encode($productconfigurator['modelData']??[]) . ";"; ?>
[data-v-productconfigurator-script] | append = <?php echo "window.accessories = " . json_encode($productconfigurator['accessories']??[]) . ";"; ?>
[data-v-productconfigurator-script] | append = <?php echo "window.product = " . json_encode(['product_id' => $productconfigurator['product_id'] ?? null, 'product_code' => $productconfigurator['product_code'] ?? '', 'description' => $productconfigurator['description'] ?? '', 'image' => $productconfigurator['image'] ?? '']) . ";"; ?>

#th-product-configurator | if_exists = $showConfigurator


#product-detail-tabs | before = <?php 
$specs = $current_component = $this->_component['productspecifications']?? [];  
$specificationImage = isset($specs['img']) ? $specs['img'] : ($productImage ?? '');

$showSpecifications = isset($specs['specifications']) && is_array($specs['specifications']) && count($specs['specifications']) > 0 ? 1 : 0;

$dimensions = $current_component = $this->_component['productdimenssions']?? [];
if (!is_array($dimensions)) { $dimensions = []; }
$showDimensionsImage = (isset($dimensions['dimensions_image']) && !empty($dimensions['dimensions_image'])) ? true : false;
$dimensionsImage = isset($dimensions['dimensions_image']) ? $dimensions['dimensions_image'] : $specificationImage;

// echo '<pre>';
// print_r($dimensions);
// echo '</pre>';


// $showDimensions = (isset($dimensions) && ($dimensions['display_width']??$dimensions['display_height']??$dimensions['display_depth']??false)) ? true : false;
$showDimensions = (isset($dimensions['display_width']) && !empty($dimensions['display_width'])) 
|| (isset($dimensions['display_height']) && !empty($dimensions['display_height']))
|| (isset($dimensions['display_depth']) && !empty($dimensions['display_depth']));

// echo '<pre>';
// print_r($dimensions);
// echo '</pre>';

$downloads = $current_component = $this->_component['productdownloads']?? [];
if (!is_array($downloads)) { $downloads = []; }
// echo '<pre>';
// print_r($downloads);
// echo '</pre>';

$showDownloads = count($downloads) > 0 ? true : false;

$certifications = $current_component = $this->_component['productcertifications']?? [];

// echo '<pre>';
// print_r($certifications);
// echo '</pre>';

if (!is_array($certifications)) { $certifications = []; }
$showProductCertifications = count($certifications) > 0 ? true : false;

$media = $current_component = $this->_component['productmedia']?? [];
$showProductMedia = isset($media['items']) && count($media['items']) > 0 ? true : false;

$class = "nav-link th-tab-title th-tabs-title-p";
$activeClass = "active nav-link th-tab-title th-tabs-title-p";
$isSpecTabActive = $showSpecifications == 1 ? true : false;
$isDimensionsTabActive = $showDimensions && !$showSpecifications == 1 ? true : false;
$isDownloadsTabActive = $showDownloads && !$showSpecifications && !$showDimensions ? true : false;
$isCertificationsTabActive = $showProductCertifications && !$showSpecifications && !$showDimensions && !$showDownloads ? true : false;
$isMediaTabActive = $showProductMedia && !$showProductCertifications && !$showSpecifications && !$showDimensions && !$showDownloads ? true : false;

?>

button[data-v-productspecifications-tab-button] | class = <?php echo $isSpecTabActive ? $activeClass : $class; ?>
button[data-v-productdimensions-tab-button] | class = <?php echo $isDimensionsTabActive ? $activeClass : $class; ?>
button[data-v-productdownloads-tab-button] | class = <?php echo $isDownloadsTabActive ? $activeClass : $class; ?>
button[data-v-productcertifications-tab-button] | class = <?php echo $isCertificationsTabActive ? $activeClass : $class; ?>
button[data-v-productmedia-tab-button] | class = <?php echo $isMediaTabActive ? $activeClass : $class; ?>

div[data-v-component-productspecifications] | class = <?php echo $isSpecTabActive ? 'tab-pane fade show active' : 'tab-pane fade'; ?>
div[data-v-component-productdimensions] | class = <?php echo $isDimensionsTabActive ? 'tab-pane fade show active' : 'tab-pane fade'; ?>
div[data-v-component-productdownloads] | class = <?php echo $isDownloadsTabActive ? 'tab-pane fade show active' : 'tab-pane fade'; ?>
div[data-v-component-productcertifications] | class = <?php echo $isCertificationsTabActive ? 'tab-pane fade show active' : 'tab-pane fade'; ?>
div[data-v-component-productmedia] | class = <?php echo $isMediaTabActive ? 'tab-pane fade show active' : 'tab-pane fade'; ?>

#specs | before = <?php 
// $showSpecs = isset($specs) && count($specs) > 0 ? true : false;
// echo '<pre>';
// print_r($specs);
// echo '</pre>';
// echo '<pre>';
// print_r("Show Specifications: ".$showSpecifications);
// echo '</pre>';

?>

[data-v-productspecifications-image-container] img[data-v-productspecifications-image] | src = <?php echo !empty($specificationImage) ? $specificationImage : ($productImage ?? ''); ?>
[data-v-productspecifications-image-container] img[data-v-productspecifications-image] | alt = <?php echo !empty($productName) ? $productName : ($productName ?? ''); ?>
a[data-v-productspecifications-link] | href = <?php echo $heroproduct['catalogue_link'] ?? '#'; ?>
a[data-v-productspecifications-link] | innerText = <?php echo 'View in Catalogue'; ?>
a[data-v-productspecifications-link] | target = <?php echo '_blank'; ?>
a[data-v-productspecifications-link] | if_exists = $buttonViewCatalogue
[data-v-productspecifications-items] [data-v-productspecifications-item] | deleteAllButFirst
[data-v-productspecifications-items] | prepend = <?php if(isset($specs['specifications']) && is_array($specs['specifications'])) { foreach ($specs['specifications'] as $item) { ?>
        [data-v-productspecifications-item] | innerText = <?php echo ($item ?? ''); ?>
[data-v-productspecifications-items] | append = <?php } } ?>

button[data-v-productspecifications-tab-button] | if_exists = $showSpecifications
div[data-v-component-productspecifications] | if_exists = $showSpecifications




[data-v-productspecifications-image-container-mobile] img[data-v-productspecifications-image-mobile] | src = <?php echo !empty($specificationImage) ? $specificationImage : ($productImage ?? ''); ?>
[data-v-productspecifications-image-container-mobile] img[data-v-productspecifications-image-mobile] | alt = <?php echo !empty($productName) ? $productName : ($productName ?? ''); ?>
a[data-v-productspecifications-link-mobile] | href = <?php echo $heroproduct['catalogue_link'] ?? '#'; ?>
a[data-v-productspecifications-link-mobile] | innerText = <?php echo 'View in Catalogue'; ?>
a[data-v-productspecifications-link-mobile] | target = <?php echo '_blank'; ?>
a[data-v-productspecifications-link-mobile] | if_exists = $buttonViewCatalogue
[data-v-productspecifications-items-mobile] [data-v-productspecifications-item-mobile] | deleteAllButFirst
[data-v-productspecifications-items-mobile] | prepend = <?php if(isset($specs['specifications']) && is_array($specs['specifications'])) { foreach ($specs['specifications'] as $item) { ?>
        [data-v-productspecifications-item-mobile] | innerText = <?php echo $item ?? ''; ?>
[data-v-productspecifications-items-mobile] | append = <?php } } ?>

button[data-v-productspecifications-tab-button-mobile] | if_exists = $showSpecifications
div[data-v-productspecifications-mobile] | if_exists = $showSpecifications


<!-- For Product Dimensions -->
#dimensions | before = <?php 

// echo '<pre>';
// print_r($dimensions);
// echo '</pre>';

// echo '<pre>';
// print_r("Show Dimensions: ".$showDimensions);
// echo '</pre>';
?>
a[data-v-productdimensions-link] | href = <?php echo $heroproduct['catalogue_link'] ?? '#'; ?>
[data-v-component-productdimenssions] #width-dimension | innerText = <?php echo $dimensions['display_width'] ?? ''; ?>
[data-v-component-productdimenssions] #height-dimension | innerText = <?php echo $dimensions['display_height'] ?? ''; ?>
[data-v-component-productdimenssions] #depth-dimension | innerText = <?php echo $dimensions['display_depth'] ?? ''; ?>

div[data-v-productdimensions-image-container] > img[data-v-productdimensions-image] | src = <?php echo $dimensionsImage; ?>
div[data-v-productdimensions-image-container] > img[data-v-productdimensions-image] | alt = <?php echo $productName; ?>

div[data-v-productdimensions-image-container] > img[data-v-productdimensions-image] | if_exists = $showDimensionsImage

button[data-v-productdimensions-tab-button] | class = <?php echo !$showSpecifications && $showDimensions ? $activeClass : $class . ($showDimensions == 0 ? ' d-none' : ''); ?>
div[data-v-component-productdimenssions] | class = <?php echo $isDimensionsTabActive ? 'tab-pane fade show active' : 'tab-pane fade'; ?>

a[data-v-productdimensions-link-mobile] | href = <?php echo $heroproduct['catalogue_link'] ?? '#'; ?>
[data-v-component-productdimenssions-mobile] #width-dimension-mobile | innerText = <?php echo $dimensions['display_width'] ?? ''; ?>
[data-v-component-productdimenssions-mobile] #height-dimension-mobile | innerText = <?php echo $dimensions['display_height'] ?? ''; ?>
[data-v-component-productdimenssions-mobile] #depth-dimension-mobile | innerText = <?php echo $dimensions['display_depth'] ?? ''; ?>

img[data-v-productdimensions-image-mobile] | src = <?php echo $dimensionsImage; ?>
img[data-v-productdimensions-image-mobile] | alt = <?php echo $productName; ?>

img[data-v-productdimensions-image-mobile] | if_exists = $showDimensionsImage
img[data-v-productdimensions-image] | if_exists = $showDimensionsImage
img[data-v-component-productdimenssions] | if_exists = $showDimensions

<!-- button[data-v-productdimensions-tab-button] | class = <?php echo !$showSpecifications && $showDimensions ? $activeClass : $class . ($showDimensions == 0 ? ' d-none' : ''); ?> -->
<!-- div[data-v-component-productdimenssions-mobile] | class = <?php echo $isDimensionsTabActive ? 'tab-pane fade show active' : 'tab-pane fade d-none'; ?> -->
div[data-v-component-productdimenssions-mobile] | if_exists = $showDimensions



<!-- For Product Downloads -->
#downloads | before = <?php 
// echo '<pre>';
// print_r($downloads);
// echo '</pre>';

// echo '<pre>';
// print_r($d);
// echo '</pre>';
?>

[data-v-component-productdownloads] h3[data-v-downloads-title] | innerText = <?php echo !empty($downloads['title']) ? $downloads['title'] : 'Available Downloads'; ?>
img[data-v-productdownloads-image] | src = <?php echo isset($specs['img']) ? $specs['img'] : ''; ?>
img[data-v-productdownloads-image] | alt = <?php echo isset($productName) ? $productName : ''; ?>
[data-v-productdownloads-link] | if_exists = $showDownloads
ul[data-v-downloads-items] li[data-v-downloads-item] | deleteAllButFirst

ul[data-v-downloads-items] li | prepend = <?php if(is_array($downloads)) { foreach ($downloads as $item) { if(!is_array($item)) continue; ?>
    li[data-v-downloads-item] a[data-v-downloads-item-name] | innerHTML = <?php echo $item['name'] ?? ''; ?>
    li[data-v-downloads-item] img[data-v-downloads-item-icon-src] | src = <?php echo $item['objectURL'] ?? ($specificationImage ?? ''); ?>
    li[data-v-downloads-item] img[data-v-downloads-item-icon-src] | alt = <?php echo $productName ?? ($productName ?? ''); ?>
    li[data-v-downloads-item] a[data-v-downloads-item-link] | href = <?php echo $item['url'] ?? '#'; ?>
ul[data-v-downloads-items] li | append = <?php }}?>

button[data-v-productdownloads-tab-button] | if_exists = $showDownloads
div[data-v-component-productdownloads] | if_exists = $showDownloads


[data-v-productdownloads-mobile] h3[data-v-downloads-title-mobile] | innerText = <?php echo !empty($downloads['title']) ? $downloads['title'] : 'Available Downloads'; ?>
img[data-v-productdownloads-image-mobile] | src = <?php echo isset($specs['img']) ? $specs['img'] : ''; ?>
img[data-v-productdownloads-image-mobile] | alt = <?php echo isset($productName) ? $productName : ''; ?>
[data-v-productdownloads-link-mobile] | if_exists = $showDownloads
ul[data-v-downloads-items-mobile] li[data-v-downloads-item-mobile] | deleteAllButFirst

ul[data-v-downloads-items-mobile] li | prepend = <?php if(is_array($downloads)) { foreach ($downloads as $item) { if(!is_array($item)) continue; ?>
    li[data-v-downloads-item-mobile] a[data-v-downloads-item-name-mobile] | innerHTML = <?php echo $item['name'] ?? ''; ?>
    li[data-v-downloads-item-mobile] img[data-v-downloads-item-icon-src-mobile] | src = <?php echo $item['objectURL'] ?? ($specificationImage ?? ''); ?>
    li[data-v-downloads-item-mobile] img[data-v-downloads-item-icon-src-mobile] | alt = <?php echo $productName ?? ($productName ?? ''); ?>
    li[data-v-downloads-item-mobile] a[data-v-downloads-item-link-mobile] | href = <?php echo $item['url'] ?? '#'; ?>
ul[data-v-downloads-items-mobile] li | append = <?php }}?>

button[data-v-productdownloads-tab-button-mobile] | if_exists = $showDownloads
div[data-v-productdownloads-mobile] | if_exists = $showDownloads


<!-- for product certifications  -->
#certifications | before = <?php 
// echo '<pre>';
// print_r($certifications);
// echo '</pre>';

?>

ul[data-v-productcertifications-items] > li[data-v-productcertifications-item] | deleteAllButFirst
img[data-v-productcertifications-image] | src = <?php echo isset($specs['img']) ? $specs['img'] : ''; ?>
img[data-v-productcertifications-image] | alt = <?php echo isset($productName) ? $productName : ''; ?>
ul[data-v-productcertifications-items] | prepend = <?php if(is_array($certifications)) { foreach ($certifications as $item) { if(!is_array($item)) continue; ?>
    img[data-v-productcertifications-icon-src] | src = <?php echo $item['logo'] ?? ($specificationImage ?? ''); ?> 
    a[data-v-productcertifications-item-link] | href = <?php echo $item['certificateDownloadLink'] ?? '#'; ?>
    a[data-v-productcertifications-item-link] | innerText = <?php echo $item['title'] ?? ''; ?>
ul[data-v-productcertifications-items] | append = <?php }}?>

button[data-v-productcertifications-tab-button] | if_exists = $showProductCertifications
div[data-v-component-productcertifications] | if_exists = $showProductCertifications


ul[data-v-productcertifications-items-mobile] > li[data-v-productcertifications-item-mobile] | deleteAllButFirst
img[data-v-productcertifications-image-mobile] | src = <?php echo isset($specs['img']) ? $specs['img'] : ''; ?>
ul[data-v-productcertifications-items-mobile] li | prepend = <?php if(is_array($certifications)) { foreach ($certifications as $item) { if(!is_array($item)) continue; ?>
    li[data-v-productcertifications-item-mobile] img[data-v-productcertifications-icon-src-mobile] | src = <?php echo $item['logo'] ?? ($specificationImage ?? ''); ?>
    li[data-v-productcertifications-item-mobile] a[data-v-productcertifications-item-title-mobile] | innerHTML = <?php echo $item['title'] ?? ''; ?>
    li[data-v-productcertifications-item-mobile] a[data-v-productcertifications-item-link-mobile] | href = <?php echo $item['certificateDownloadLink'] ?? '#'; ?>
ul[data-v-productcertifications-items-mobile] li | append = <?php }}?>

button[data-v-productcertifications-tab-button-mobile] | if_exists = $showProductCertifications
div[data-v-productcertifications-mobile] | if_exists = $showProductCertifications





<!-- for product media  -->
#media | before = <?php 

// echo '<pre>';
// print_r($media);
// echo '</pre>';

?>




[data-v-designresourceimages-images] > div.th-masonry-img-item | deleteAllButFirst
[data-v-designresourceimages-images] | prepend = <?php if(isset($media['items']) && is_array($media['items'])){ foreach ($media['items'] as $image) { if(!is_array($image)) continue; ?>
img[data-v-designresourceimages-image-src] | src = <?php echo isset($image["dataSrc"]) ? $image["dataSrc"] : ''; ?>
img[data-v-designresourceimages-image-src] | alt = <?php echo isset($image["dataSrc"]) ? ucfirst($image["title"]) : 'Product'; ?>
<!-- div[data-v-designresourceimages-image-src] | class = <?php echo isset($image["class"]) ? $image["class"] : ''; ?> -->
div[data-v-designresourceimages-image-data-src] | data-src = <?php echo isset($image["dataSrc"]) ? $image["dataSrc"] : ''; ?>
div[data-v-designresourceimages-image-bg-src] p[data-v-designresourceimages-image-context] | innerHTML = <?php echo isset($image["context"]) ? $image["context"] : ''; ?>
div[data-v-designresourceimages-image-bg-src] p[data-v-designresourceimages-image-context-reference] | innerHTML = <?php echo isset($image["context_reference"]) ? $image["context_reference"] : ''; ?>	

h6[data-v-designresourceimages-image-title] | innerHTML = <?php echo isset($image["title"]) ? $image["title"] : ''; ?>
[data-v-designresourceimages-images] | append = <?php }} ?>

button[data-v-productmedia-tab-button] | if_exists = $showProductMedia
div[data-v-component-productmedia] | if_exists = $showProductMedia


[data-v-designresourceimages-images-mobile] > div.th-masonry-img-item | deleteAllButFirst
[data-v-designresourceimages-images-mobile] | prepend = <?php if(isset($media['items']) && is_array($media['items'])){ foreach ($media['items'] as $image) { if(!is_array($image)) continue; ?>
img[data-v-designresourceimages-image-src-mobile] | src = <?php echo isset($image["dataSrc"]) ? $image["dataSrc"] : ''; ?>
<!-- div[data-v-designresourceimages-image-src] | class = <?php echo isset($image["class"]) ? $image["class"] : ''; ?> -->
div[data-v-designresourceimages-image-data-src-mobile] | data-src = <?php echo isset($image["dataSrc"]) ? $image["dataSrc"] : ''; ?>
div[data-v-designresourceimages-image-bg-src-mobile] p[data-v-designresourceimages-image-context-mobile] | innerHTML = <?php echo isset($image["context"]) ? $image["context"] : ''; ?>
div[data-v-designresourceimages-image-bg-src-mobile] p[data-v-designresourceimages-image-context-reference-mobile] | innerHTML = <?php echo isset($image["context_reference"]) ? $image["context_reference"] : ''; ?>	

h6[data-v-designresourceimages-image-title-mobile] | innerHTML = <?php echo isset($image["title"]) ? $image["title"] : ''; ?>
[data-v-designresourceimages-images-mobile] | append = <?php }} ?>

button[data-v-productmedia-tab-button-mobile] | if_exists = $showProductMedia
div[data-v-productmedia-mobile] | if_exists = $showProductMedia





#ocean | before = <?php 
$productsustainablity = $current_component = $this->_component['productsustainablity']?? [];
$oceanPlasticUsed = $productsustainablity['ocean_plastic_used'] ?? 0;
// echo '<pre>';
// print_r($productsustainablity);
// echo '</pre>';
$is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
$catalogueLink = isset($this->parameters['catalogue_link']) ? $this->parameters['catalogue_link'] : '#';
?>
[data-v-ocean-plastic-used] | class = <?php echo $oceanPlasticUsed ? 'container-show bg-white' : 'container-hide'; ?>
[data-v-component-productsustainablity] h2[data-v-productsustainablity-title] | innerText = <?php echo $productsustainablity['title'] ?? ''; ?>
[data-v-component-productsustainablity] .th-section-subtitle[data-v-productsustainablity-subtitle] | innerText = <?php echo $productsustainablity['subtitle'] ?? ''; ?>
img[data-v-productsustainablity-img1] | src = <?php echo $productsustainablity['img'] ?? ''; ?>
img[data-v-productsustainablity-img2] | src = <?php echo $productsustainablity['img2'] ?? ''; ?>

[data-v-component-productsustainablity] | if_exists = $oceanPlasticUsed



[data-v-productsustainablity-link-text] span | innerText = <?php echo $productsustainablity['linkText'] ?? 'View Catalogue'; ?>
[data-v-productsustainablity-link-text] | href = <?php echo $catalogueLink ?? '#'; ?>
[data-v-productsustainablity-component-link] a | href = <?php echo isset($productsustainablity['edit_link']) ? $productsustainablity['edit_link'] : ''; ?>
[data-v-productsustainablity-component-link] | if_exists = $is_admin


#circle | before = <?php 
$circle = $current_component = $this->_component['productcalltoaction']?? [];
// echo '<pre>';
// print_r($circle);
// echo '</pre>';
?>

[data-v-component-productcalltoaction] [data-v-circle-items] | deleteAllButFirst
[data-v-component-productcalltoaction] [data-v-circle-items] | prepend = <?php if(isset($circle['items']) && is_array($circle['items'])) { foreach ($circle['items'] as $item) {
    if(!is_array($item)) continue;
    $circleImage = '';
    if (isset($item['image'][0]['objectURL'])) {
        $circleImage = $item['image'][0]['objectURL'];
    } elseif (isset($item['image']) && is_string($item['image'])) {
        if (preg_match("/objectURL\\s*:\\s*'([^']+)'/", $item['image'], $m) || preg_match('/objectURL\\s*:\\s*"([^"]+)"/', $item['image'], $m)) {
            $circleImage = $m[1];
        } else {
            $circleImageDecoded = json_decode($item['image'], true);
            if (is_array($circleImageDecoded) && isset($circleImageDecoded[0]['objectURL'])) {
                $circleImage = $circleImageDecoded[0]['objectURL'];
            } elseif ($item['image'] !== '' && ($item['image'][0] ?? '') !== '[' && ($item['image'][0] ?? '') !== '{') {
                $circleImage = $item['image'];
            }
        }
    }
?>
    [data-v-circle-item] img[data-v-circle-image] | src = <?php echo $circleImage; ?>
    [data-v-circle-item] h6[data-v-circle-title] | innerText = <?php echo $item['title'] ?? ''; ?>
    [data-v-circle-item] .th-link[data-v-circle-link] | href = <?php echo $item['linkUrl'] ?? '#'; ?>
    [data-v-circle-item] .th-link[data-v-circle-link] .th-link-text[data-v-circle-link-text] | innerText = <?php echo $item['linkText'] ?? 'Buy Now'; ?>
[data-v-component-productcalltoaction] [data-v-circle-items] | append = <?php } } ?>


<!-- for product family  -->
#th-product-related-family | before = <?php 
$relatedFamilyProducts = $current_component = $this->_component['productrelatedfamily']?? [];
// echo '<pre>';
// print_r($relatedFamilyProducts);
// echo '</pre>';
$isRelatedFamilyProductsExist = isset($relatedFamilyProducts['items']) && count($relatedFamilyProducts['items']) > 0 ? true : false;
// echo '<pre>';
// print_r($relatedFamilyProducts['items']);
// echo '</pre>';
?>
[data-v-productrelatedfamily-component-link] a | href = <?php echo isset($relatedFamilyProducts['component_link']) ? $relatedFamilyProducts['component_link'] : ''; ?>
[data-v-productrelatedfamily-component-link] | if_exists = $is_admin
#th-product-related-family | addClass = <?php echo !$isRelatedFamilyProductsExist ? 'pb-0' : ''; ?>

h2[data-v-related-family-title] | innerText = <?php echo $relatedFamilyProducts['title'] ?? $relatedFamilyProducts['section_title'] ?? ''; ?>
div[data-v-related-family-items] > div.swiper-slide | deleteAllButFirst

[data-v-related-family-items] | prepend = <?php if(isset($relatedFamilyProducts['items']) && is_array($relatedFamilyProducts['items'])) { foreach ($relatedFamilyProducts['items'] as $item) { if(!is_array($item)) continue; ?>
    a[data-v-category-product-url] | href = <?php echo $item['link'] ?? ''; ?>
    img[data-v-related-family-imagesrc] | src = <?php echo $item['image'] ?? ''; ?>
    img[data-v-related-family-imagesrc] | alt = <?php echo isset($item["description"]) ? $item["title"] . ' - '.  $item["description"] : ''; ?>
    h3[data-v-related-family-title] | innerText = <?php echo $item['title'] ?? ''; ?>
    div[data-v-related-family-add-to-pinboard] | data-id = <?php echo isset($item["id"]) ? $item["id"] : ''; ?>
    div[data-v-related-family-add-to-pinboard] | data-model = product
    div[data-v-related-family-add-to-pinboard] | data-title = <?php echo isset($item["title"]) ? $item["title"] : ''; ?>
    div[data-v-related-family-add-to-pinboard] | data-description = <?php echo isset($item["description"]) ? $item["description"] : ''; ?>
    div[data-v-related-family-add-to-pinboard] | data-image = <?php echo isset($item["image"]) ? $item["image"] : ''; ?>
    div[data-v-related-family-tags] > .th-tag | deleteAllButFirst
    div[data-v-related-family-tags] | prepend = <?php if(isset($item['tags']) && is_array($item['tags'])) { foreach ($item['tags'] as $tag) { ?>
        div[data-v-related-family-tags] > .th-tag | innerText = <?php echo is_scalar($tag) ? $tag : ''; ?>
    div[data-v-related-family-tags] | append = <?php } } ?>
    div[data-v-related-family-finishes] > .th-circle | deleteAllButFirst
    div[data-v-related-family-finishes] | prepend = <?php if(isset($item['finishes']) && is_array($item['finishes'])) { foreach ($item['finishes'] as $finish) { if(!is_array($finish)) continue; ?>
        div[data-v-related-family-finishes] > .th-circle | innerText = <?php echo $finish['name'] ?? ''; ?>
    div[data-v-related-family-finishes] | append = <?php } } ?>
[data-v-related-family-items] | append = <?php } } ?>

#th-product-related-family | if_exists = $isRelatedFamilyProductsExist

import(components/needhelp.tpl, [data-v-component-needhelp])


#th-product-may-like | before = <?php 
$productsYouMayAlsoLike = $current_component = $this->_component['productalsolike']?? [];
// echo '<pre>';
// print_r($productsYouMayAlsoLike);
// echo '</pre>';
$showProductsYouMayAlsoLike = isset($productsYouMayAlsoLike['items']) && is_array($productsYouMayAlsoLike['items']) && count($productsYouMayAlsoLike['items']) > 0 ? true : false;
$is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>
section[data-v-component-productalsolike] > div[data-v-productalsolike-component-link] > a | href = <?php echo $productsYouMayAlsoLike['component_link'] ?? ''; ?>
[data-v-productalsolike-component-link] a | if_exists = $is_admin

h2[data-v-productalsolike-section_title] | innerText = <?php echo $productsYouMayAlsoLike['section_title'] ?? ''; ?>
div[data-v-productalsolike-section_subtitle] | innerText = <?php echo $productsYouMayAlsoLike['section_subtitle'] ?? ''; ?>

div[data-v-alsolike-items] > div[data-v-alsolike-item] | deleteAllButFirst
div[data-v-alsolike-items] | prepend = <?php if(isset($productsYouMayAlsoLike['items']) && is_array($productsYouMayAlsoLike['items']) && count($productsYouMayAlsoLike['items'])) { foreach ($productsYouMayAlsoLike['items'] as $likeItem) { if(!is_array($likeItem)) continue; ?>
img[data-v-alsolike-item-image] | src = <?php echo $likeItem['image'] ?? ''; ?>
img[data-v-alsolike-item-image] | alt = <?php echo $likeItem['title'] ?? ''; ?>
h3[data-v-alsolike-item-title] | innerText = <?php echo $likeItem['title'] ?? ''; ?>
div[data-v-alsolike-item-description] | innerText = <?php echo $likeItem['description'] ?? ''; ?>
a[data-v-alsolike-item-link] | href = <?php echo $likeItem['link'] ?? '#'; ?>


div[data-v-productalsolike-item-add-to-pinboard] | data-id = <?php echo isset($likeItem["id"]) ? $likeItem["id"] : ''; ?>
div[data-v-productalsolike-item-add-to-pinboard] | data-title = <?php echo isset($likeItem["title"]) ? $likeItem["title"] : ''; ?>
div[data-v-productalsolike-item-add-to-pinboard] | data-description = <?php echo isset($likeItem["description"]) ? $likeItem["description"] : ''; ?>
div[data-v-productalsolike-item-add-to-pinboard] | data-image = <?php echo isset($likeItem["image"]) ? $likeItem["image"] : ''; ?>



[data-v-alsolike-items] | append = <?php } } ?>

section[data-v-component-productalsolike] | if_exists = $showProductsYouMayAlsoLike

[data-v-productalsolike-component-link] | if_exists = $is_admin


.product-detail-featured-projects-section | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$featured_projects = $current_component = $this->_component['productfeaturedprojectslider']?? [];
	// echo '<pre>';
	// print_r($featured_projects);
	// echo '</pre>';
    $product_name = $heroproduct['title'] ?? $heroproduct['name'] ?? '';
    $subtitle = isset($featured_projects['section_subtitle']) ? $featured_projects['section_subtitle'] . ' ' . $product_name. '.' : '';
    $showFeatureProjects = isset($featured_projects['items']) && is_array($featured_projects['items']) && count($featured_projects['items']) > 0 ? true : false;
?>
[data-v-productfeaturedprojectslider-component-link] a | href = <?php echo isset($featured_projects['component_link']) ? $featured_projects['component_link'] : ''; ?>
[data-v-productfeaturedprojectslider-component-link] | if_exists = $is_admin
.product-detail-featured-projects-section h2[data-v-productfeaturedprojectslider-section_title] | innerHTML = <?php echo isset($featured_projects['section_title']) ? $featured_projects['section_title'] : ''; ?>
.product-detail-featured-projects-section .th-section-subtitle[data-v-productfeaturedprojectslider-section_subtitle] | innerHTML = <?php echo $subtitle; ?>
.th-featured-projects-slider > div.swiper-wrapper > div.swiper-slide | deleteAllButFirst
.th-featured-projects-slider > div.swiper-wrapper | prepend = <?php if(isset($featured_projects['items']) && is_array($featured_projects['items'])){ foreach ($featured_projects['items'] as $key => $project) { if(!is_array($project)) continue; ?>
div[data-v-featuredprojectslideritem-location] | innerHTML = <?php echo isset($project["location"]) ? $project["location"] : ''; ?>
h3[data-v-featuredprojectslideritem-title] > a | innerHTML = <?php echo isset($project["title"]) ? $project["title"] : ''; ?>
div[data-v-featuredprojectslideritem-description] | innerHTML = <?php echo isset($project["preview_text"]) ? $project["preview_text"] : ''; ?>
img[data-v-featuredprojectslideritem-image] | src = <?php echo isset($project["image"]) ? $project["image"] : ''; ?>
img[data-v-featuredprojectslideritem-image] | alt = <?php echo isset($project["title"]) ? $project["title"] : ''; ?>
div[data-v-featuredproductslideritem-add-to-pinboard] | data-id = <?php echo isset($project["project_id"]) ? $project["project_id"] : ''; ?>
div[data-v-featuredproductslideritem-add-to-pinboard] | data-model = project
div[data-v-featuredproductslideritem-add-to-pinboard] | data-title = <?php echo isset($project["title"]) ? $project["title"] : ''; ?>
div[data-v-featuredproductslideritem-add-to-pinboard] | data-description = <?php echo isset($project["preview_text"]) ? $project["preview_text"] : ''; ?>
div[data-v-featuredproductslideritem-add-to-pinboard] | data-image = <?php echo isset($project["image"]) ? $project["image"] : ''; ?>
a[data-v-featuredprojectslideritem-link] | href = <?php echo isset($project["slug"]) ? "/projects"."/".$project["slug"] : ''; ?>
.th-featured-projects-slider > div.swiper-wrapper | append = <?php }} ?>

section[data-v-component-productfeaturedprojectslider] | if_exists = $showFeatureProjects

#th-product-slider | before = <?php 
$productInstagramSlider = $current_component = $this->_component['productinstagramslider']?? [];
// echo '<pre>';
// print_r($productInstagramSlider);
// echo '</pre>';
$showProductInstagramSlider = isset($productInstagramSlider['items']) && is_array($productInstagramSlider['items']) && count($productInstagramSlider['items']) > 0;
?>
[data-v-component-productinstagramslider] h2[data-v-productinstagramslider-title] | innerText = <?php echo isset($productInstagramSlider['title']) ? '# ' . ucfirst($productInstagramSlider['title']) : ''; ?>
div[data-v-productinstagramslider-items] > div.swiper-slide | deleteAllButFirst
[data-v-productinstagramslider-items] | prepend = <?php if (!empty($productInstagramSlider['items']) && is_array($productInstagramSlider['items'])) { foreach ($productInstagramSlider['items'] as $item) { if(!is_array($item)) continue; ?>
    div[data-v-productinstagramslider-item-image] | data-bg-src = <?php echo isset($item['img']) ? $item['img'] : ''; ?>
    a[data-v-productinstagramslider-item-link] | href = <?php echo !empty($item['link']) ? 'https://www.instagram.com'. htmlspecialchars($item['link'], ENT_QUOTES, 'UTF-8') : '#'; ?>
[data-v-productinstagramslider-items] | append = <?php } } ?>

section[data-v-component-productinstagramslider] | if_exists = $showProductInstagramSlider
?>

import(product/product-instagram.tpl, [data-v-component-productinstagram])

import(components/footer.tpl, [data-v-component-footer])
