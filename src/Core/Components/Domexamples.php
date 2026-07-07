<?php

namespace App\Core\Components;

use App\Core\System\Component\ComponentBase;

class Domexamples extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds

    public function __construct(
        array $options = []
    ) {
        parent::__construct($options);
    }
    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta(): array
    {
        return [
            'name' => 'domexamples',
            'class' => self::class,
            'validOptions' => [
                'component_id'
            ],
            'filePath' => __FILE__,
            'cacheKey' => null,
            'data' => [],
            'designOnly' => false
        ];
    }

    public function results($parameters = []): array
    {
        return [
            'title' => 'Modern PHP DOM API Examples',
            'description' => 'Comprehensive demonstration of PHP 8.4+ Dom\ classes and modern DOM manipulation',
            'examples' => [
                'adjacent_position' => [
                    'title' => 'AdjacentPosition Examples',
                    'description' => 'Demonstrating DOM element positioning with AdjacentPosition enum',
                    'examples' => [
                        'before_begin' => 'Before Begin',
                        'after_begin' => 'After Begin', 
                        'before_end' => 'Before End',
                        'after_end' => 'After End'
                    ]
                ],
                'dom_attr' => [
                    'title' => 'DOM Attribute Examples',
                    'description' => 'Working with DOM attributes and properties',
                    'attributes' => [
                        'id' => 'test-element',
                        'class' => 'container active',
                        'data_custom' => 'custom-value'
                    ]
                ],
                'dom_cdata' => [
                    'title' => 'CDATA Section Examples',
                    'description' => 'Handling CDATA sections in XML documents',
                    'content' => 'This is CDATA content with &lt;tags&gt; and &amp; symbols'
                ],
                'dom_character_data' => [
                    'title' => 'Character Data Examples',
                    'description' => 'Working with text nodes and character data',
                    'original_text' => 'Hello World',
                    'modified_text' => 'Hi Beautiful World - Extended'
                ],
                'dom_child_node' => [
                    'title' => 'Child Node Examples',
                    'description' => 'Managing child nodes and parent-child relationships',
                    'children' => [
                        'First div',
                        'Second div', 
                        'Third div'
                    ]
                ],
                'dom_parent_node' => [
                    'title' => 'Parent Node Examples',
                    'description' => 'ParentNode interface operations and child management',
                    'child_count' => 3,
                    'children' => [
                        ['tag' => 'header', 'content' => 'Header Section'],
                        ['tag' => 'main', 'content' => 'Main Content'],
                        ['tag' => 'footer', 'content' => 'Footer Section']
                    ]
                ],
                'dom_comment' => [
                    'title' => 'DOM Comment Examples',
                    'description' => 'Creating and managing HTML comments',
                    'comment_text' => 'This is an HTML comment with special characters: &lt;&gt;&amp;&quot;&#39;',
                    'updated_comment' => 'Updated comment content'
                ],
                'dom_document' => [
                    'title' => 'Document Examples',
                    'description' => 'Working with DOM documents and document structure',
                    'doctype' => 'html',
                    'document_element' => 'html',
                    'implementation' => 'Dom\\Implementation'
                ],
                'dom_document_fragment' => [
                    'title' => 'Document Fragment Examples',
                    'description' => 'Using document fragments for efficient DOM manipulation',
                    'fragment_elements' => [
                        'article' => 'Article content',
                        'section' => 'Section content',
                        'aside' => 'Aside content'
                    ]
                ],
                'dom_element' => [
                    'title' => 'Element Examples',
                    'description' => 'Creating and manipulating DOM elements',
                    'video_element' => [
                        'controls' => '',
                        'width' => '640',
                        'height' => '360',
                        'preload' => 'metadata'
                    ],
                    'source_element' => [
                        'src' => 'video.mp4',
                        'type' => 'video/mp4'
                    ]
                ],
                'dom_html_collection' => [
                    'title' => 'HTML Collection Examples',
                    'description' => 'Working with HTML collections and node lists',
                    'paragraphs' => [
                        'Paragraph 1',
                        'Paragraph 2',
                        'Paragraph 3',
                        'Paragraph 4'
                    ]
                ],
                'dom_html_document' => [
                    'title' => 'HTML Document Examples',
                    'description' => 'Modern HTML document creation and manipulation',
                    'document_properties' => [
                        'doctype' => 'html',
                        'document_element' => 'html',
                        'title' => 'Test HTML Document',
                        'body_content' => 'Hello World This is a test paragraph.'
                    ]
                ],
                'dom_html_element' => [
                    'title' => 'HTML Element Examples',
                    'description' => 'Working with HTML elements and their properties',
                    'container' => [
                        'tag_name' => 'div',
                        'id' => 'container',
                        'class' => 'main',
                        'data_custom' => 'value',
                        'inner_html' => 'Hello World This is a paragraph.'
                    ]
                ],
                'dom_implementation' => [
                    'title' => 'DOM Implementation Examples',
                    'description' => 'Using DOM implementation for document creation',
                    'doctype' => 'html',
                    'html5_support' => true,
                    'implementation_class' => 'Dom\\Implementation'
                ],
                'dom_named_node_map' => [
                    'title' => 'Named Node Map Examples',
                    'description' => 'Working with attribute collections',
                    'attributes' => [
                        'id' => '123',
                        'name' => 'Product Name',
                        'price' => '29.99',
                        'category' => 'electronics'
                    ]
                ],
                'dom_namespace_info' => [
                    'title' => 'Namespace Info Examples',
                    'description' => 'Handling XML namespaces and namespace information',
                    'namespace_uri' => 'http://example.com/namespace',
                    'prefix' => 'ns',
                    'local_name' => 'element',
                    'has_namespace' => true
                ],
                'dom_node_list' => [
                    'title' => 'Node List Examples',
                    'description' => 'Working with node lists and collections',
                    'articles' => [
                        'Article 1',
                        'Article 2',
                        'Article 3',
                        'Article 4'
                    ],
                    'sidebar_content' => 'Sidebar content'
                ],
                'dom_processing_instruction' => [
                    'title' => 'Processing Instruction Examples',
                    'description' => 'Creating and managing XML processing instructions',
                    'target' => 'xml-stylesheet',
                    'data' => 'type="text/xsl" href="style.xsl"'
                ],
                'dom_text' => [
                    'title' => 'Text Node Examples',
                    'description' => 'Working with text nodes and text manipulation',
                    'original_text' => 'Hello World',
                    'split_text' => 'Hello',
                    'remaining_text' => ' World'
                ],
                'dom_token_list' => [
                    'title' => 'Token List Examples',
                    'description' => 'Managing class lists and token collections',
                    'classes' => ['container', 'active', 'visible'],
                    'contains_active' => true,
                    'contains_visible' => true
                ],
                'dom_xml_document' => [
                    'title' => 'XML Document Examples',
                    'description' => 'Working with XML documents and XML properties',
                    'xml_properties' => [
                        'version' => '1.0',
                        'encoding' => 'UTF-8',
                        'standalone' => true
                    ],
                    'root_element' => 'root',
                    'namespace_elements' => [
                        'ns:element' => 'Namespace content',
                        'ns:another' => 'Another namespace element'
                    ]
                ],
                'dom_xpath' => [
                    'title' => 'XPath Examples',
                    'description' => 'Advanced XPath queries and node selection',
                    'basic_queries' => [
                        'all_elements' => 25,
                        'paragraphs' => 6,
                        'headings' => 4,
                        'links' => 3
                    ],
                    'attribute_queries' => [
                        'post_elements' => 2,
                        'data_id_elements' => 2,
                        'meta_elements' => 2,
                        'hash_links' => 3
                    ],
                    'complex_queries' => [
                        'articles_with_author' => 2,
                        'article_paragraphs' => 4,
                        'multi_attr_elements' => 2
                    ]
                ]
            ]
        ];
    }
} 