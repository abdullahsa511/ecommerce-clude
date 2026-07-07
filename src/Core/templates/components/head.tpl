
head | before = <?php
use function App\Core\System\utils\env;

    if(isset($current_component)) $previous_component = $current_component;
	$head = $current_component = isset($this->_component['head']) ? $this->_component['head']??[] : [];

    // echo "<pre>";
    // print_r($head);
    // echo "</pre>";
    // exit;
    $metaTitle = isset($head['meta_title'])
    ? $head['meta_title']
    : (isset($head['metaData']) && isset($head['metaData']['meta_title'])
        ? $head['metaData']['meta_title']
        : (isset($head['title']) ? $head['title'] : ''));

    $metaDescription = isset($head['meta_description'])
        ? $head['meta_description']
        : (isset($head['metaData']) && isset($head['metaData']['meta_description'])
            ? $head['metaData']['meta_description']
            : '');

    $metaKeyword = isset($head['meta_keywords'])
        ? $head['meta_keywords']
        : (isset($head['metaData']) && isset($head['metaData']['meta_keywords'])
            ? $head['metaData']['meta_keywords']
            : '');

    $ogTitle = isset($head['og_title'])
        ? $head['og_title']
        : $metaTitle ?? '';

    $robots = isset($head['robots']) ? true : false;

        // echo "<pre>";
        // echo $ogTitle;
?>

[data-v-head-meta-title] | content = <?php echo $metaTitle; ?>
[data-v-head-meta-description] | content = <?php echo $metaDescription; ?>
[data-v-head-meta-keywords] | content = <?php echo $metaKeyword; ?>
[data-v-head-title] | innerText = <?php echo isset($head['title']) ? $head['title'] : ''; ?>
[data-v-head-canonical] | href = <?php echo isset($head['canonical']) ? $head['canonical'] : ''; ?>

[data-v-og-title] | content = <?php echo isset($ogTitle) ? $ogTitle : ''; ?>
[data-v-og-type] | content = <?php echo isset($head['type']) ? $head['type'] : ''; ?>
[data-v-og-url] | content = <?php echo isset($head['url']) ? $head['url'] : $head['canonical']??""; ?>
[data-v-og-description] | content = <?php echo isset($head['metaData']['meta_description']) ? $head['metaData']['meta_description'] : ''; ?>
[data-v-og-image] | content = <?php echo isset($head['og_image']) ? $head['og_image'] : '/media/Projects/Krost_Business_Furniture_2026.webp'; ?>
[data-v-twitter-title] | content = <?php echo $metaTitle ?? ''; ?>
[data-v-twitter-description] | content = <?php echo isset($head['metaData']['meta_description']) ? $head['metaData']['meta_description'] : ''; ?>
[data-v-twitter-image] | content = <?php echo isset($head['og_image']) ? $head['og_image'] : '/media/Projects/Krost_Business_Furniture_2026.webp'; ?>
<!-- Product Schema -->
#product-schema | innerText = <?php echo isset($head['product_schema']) ? $head['product_schema'] : ''; ?>
[data-v-head-meta-author] | content = <?php echo isset($head['author']) ? $head['author'] : 'Krost Business Furniture';?>



#stylesheet-min | before = <?php $version = env('APP_VERSION', '1.0.0'); ?>
#stylesheet-min | href = <?php echo "/css/stylesheet.min.css?v=" . $version; ?>

#data-v-product-ld-json | before = <?php 
    $metaData = isset($this->parameters['metaData']) ? $this->parameters['metaData'] : [];
    $ldJson = isset($metaData['ld_json']) ? $metaData['ld_json'] : '';
?>
#data-v-product-ld-json | innerHTML = <?php echo $ldJson ?? ''; ?>
#data-v-product-ld-json | if_exists = $ldJson
[data-v-robots] | if_exists = $robots