import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])

//How to use selectors to replace html static content using tpl template to render dynamic content

//Vtpl selectors 
h1.title|innerText =  "About Us Dynamic"

div.header > div.header-right > a|innerHTML = "<span>Home Link</span>"

div.main-section > p:nth-child(2)|outerHTML = "<h5>This is dinamic tag with contents</h5>"

//The template engine expects PHP code for structural modifiers (after, before, append, prepend) 
//but can handle static strings for content modifiers (innerText, innerHTML, etc.).

.main-section | after = <?php echo "<div style='height:10px; background-color:red;'>Test</div>"; ?>

div.footer | before = <?php echo "<button>Click me</button>"; ?>
div.header | append = <?php echo " <h2>Appended Text</h2>"; ?>

.list | before = <?php 
    $array = [
        "1" => "One",
        "2" => "Two",
        "3" => "Three"
    ];
    echo " <h2>Prepended Text</h2>";
?>


.list | prepend = <?php foreach ($array as $key => $value) { ?>
.list-item | innerHTML = <?php echo $value; ?>
.list | append = <?php } ?>


.product-list | before = <?php 
$products = [
    [
        "title" => "Product One",
        "description" => "Description 1"
    ],
    [
        "title" => "Product Two",
        "description" => "Description 2"
    ],
    [
        "title" => "Product Three",
        "description" => "Description 3"
    ]
];


?>
.product-list > li | deleteAllButFirst
.product-list | prepend = <?php foreach ($products as $product) { ?>
.product-item > p[data-v-title] | innerHTML = <?php echo $product["title"]; ?>
.product-item > p[data-v-description] | innerHTML = <?php echo $product["description"]; ?>
.product-list | append = <?php } ?>


.hero | before = <?php 
$hero = $current_component = $this->_component['herohome']?? [];

?>
[data-v-component-herohome] [data-v-herohome-*]|innerText = $hero['@@__data-v-herohome-(*)__@@']
[data-v-component-herohome] [data-v-herohome-hero_title]|innerHTML = <?php echo $hero['hero_title']; ?>





