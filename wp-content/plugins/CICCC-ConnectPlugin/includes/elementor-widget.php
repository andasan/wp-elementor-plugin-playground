<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Wait for Elementor to be fully loaded before defining and registering the widget
add_action('elementor/widgets/register', function($widgets_manager) {
    class CICCC_Event_List_Widget extends \Elementor\Widget_Base {
        public function get_name() {
            return 'ciccc_event_list';
        }

        public function get_title() {
            return 'CICCC Event List';
        }

        public function get_icon() {
            return 'eicon-post-list';
        }

        public function get_categories() {
            return ['general'];
        }

        public function get_script_depends() {
            return ['slick'];
        }

        public function get_style_depends() {
            return ['slick', 'slick-theme'];
        }

        public function __construct($data = [], $args = null) {
            parent::__construct($data, $args);

            wp_register_style('slick', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');
            wp_register_style('slick-theme', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css');
            wp_register_script('slick', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', ['jquery'], null, true);
        }

        protected function register_controls() {
            $this->start_controls_section(
                'content_section',
                [
                    'label' => 'Content',
                    'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                ]
            );

            $this->add_control(
                'layout',
                [
                    'label' => 'Layout',
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'grid',
                    'options' => [
                        'grid' => 'Grid',
                        'list' => 'List',
                        'slider' => 'Slider',
                    ],
                ]
            );

            $this->add_responsive_control(
                'columns',
                [
                    'label' => 'Columns',
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => '3',
                    'options' => [
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                        '5' => '5',
                        '6' => '6',
                    ],
                    'condition' => [
                        'layout' => 'grid',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ciccc-event-list[data-layout="grid"]' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                    ],
                ]
            );

            $this->add_control(
                'number_of_events',
                [
                    'label' => 'Number of Events',
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 1,
                    'max' => 100,
                    'step' => 1,
                    'default' => 10,
                ]
            );

            $this->add_control(
                'order_by',
                [
                    'label' => 'Order By',
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'date',
                    'options' => [
                        'date' => 'Date',
                        'title' => 'Title',
                        'price' => 'Price',
                    ],
                ]
            );

            $this->add_control(
                'order',
                [
                    'label' => 'Order',
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'DESC',
                    'options' => [
                        'ASC' => 'Ascending',
                        'DESC' => 'Descending',
                    ],
                ]
            );

            $this->add_control(
                'api_url',
                [
                    'label' => 'API URL',
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => defined('CICCC_DEFAULT_API_URL') ? CICCC_DEFAULT_API_URL : '',
                    'placeholder' => 'Enter your API URL',
                ]
            );

            $this->end_controls_section();

            // Slider settings section
            $this->start_controls_section(
                'slider_settings',
                [
                    'label' => 'Slider Settings',
                    'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                    'condition' => [
                        'layout' => 'slider',
                    ],
                ]
            );

            $this->add_control(
                'slides_to_show',
                [
                    'label' => 'Slides to Show',
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 1,
                    'max' => 10,
                    'step' => 1,
                    'default' => 3,
                ]
            );

            $this->add_control(
                'slides_to_scroll',
                [
                    'label' => 'Slides to Scroll',
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 1,
                    'max' => 10,
                    'step' => 1,
                    'default' => 1,
                ]
            );

            $this->add_control(
                'autoplay',
                [
                    'label' => 'Autoplay',
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => 'Yes',
                    'label_off' => 'No',
                    'return_value' => 'yes',
                    'default' => 'yes',
                ]
            );

            $this->add_control(
                'autoplay_speed',
                [
                    'label' => 'Autoplay Speed',
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 1000,
                    'max' => 10000,
                    'step' => 500,
                    'default' => 3000,
                    'condition' => [
                        'autoplay' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'pause_on_hover',
                [
                    'label' => 'Pause on Hover',
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => 'Yes',
                    'label_off' => 'No',
                    'return_value' => 'yes',
                    'default' => 'yes',
                    'condition' => [
                        'autoplay' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'show_arrows',
                [
                    'label' => 'Show Arrows',
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => 'Yes',
                    'label_off' => 'No',
                    'return_value' => 'yes',
                    'default' => 'yes',
                    'condition' => [
                        'layout' => 'slider',
                    ],
                ]
            );

            $this->add_control(
                'arrow_position',
                [
                    'label' => 'Arrow Position',
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'custom',
                    'options' => [
                        'outside' => 'Outside',
                        'inside' => 'Inside',
                        'custom' => 'Custom',
                    ],
                    'condition' => [
                        'layout' => 'slider',
                        'show_arrows' => 'yes',
                    ],
                ]
            );

            $this->add_responsive_control(
                'left_arrow_offset_x',
                [
                    'label' => 'Left Arrow Offset X',
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['px', '%'],
                    'range' => [
                        'px' => ['min' => -100, 'max' => 100],
                        '%' => ['min' => -50, 'max' => 50],
                    ],
                    'default' => ['unit' => 'px', 'size' => -25],
                    'condition' => [
                        'layout' => 'slider',
                        'show_arrows' => 'yes',
                        'arrow_position' => 'custom',
                    ],
                ]
            );

            $this->add_responsive_control(
                'left_arrow_offset_y',
                [
                    'label' => 'Left Arrow Offset Y',
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['px', '%'],
                    'range' => [
                        'px' => ['min' => -100, 'max' => 100],
                        '%' => ['min' => -50, 'max' => 50],
                    ],
                    'default' => ['unit' => '%', 'size' => 50],
                    'condition' => [
                        'layout' => 'slider',
                        'show_arrows' => 'yes',
                        'arrow_position' => 'custom',
                    ],
                ]
            );

            $this->add_responsive_control(
                'right_arrow_offset_x',
                [
                    'label' => 'Right Arrow Offset X',
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['px', '%'],
                    'range' => [
                        'px' => ['min' => -100, 'max' => 100],
                        '%' => ['min' => -50, 'max' => 50],
                    ],
                    'default' => ['unit' => 'px', 'size' => -25],
                    'condition' => [
                        'layout' => 'slider',
                        'show_arrows' => 'yes',
                        'arrow_position' => 'custom',
                    ],
                ]
            );

            $this->add_responsive_control(
                'right_arrow_offset_y',
                [
                    'label' => 'Right Arrow Offset Y',
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['px', '%'],
                    'range' => [
                        'px' => ['min' => -100, 'max' => 100],
                        '%' => ['min' => -50, 'max' => 50],
                    ],
                    'default' => ['unit' => '%', 'size' => 50],
                    'condition' => [
                        'layout' => 'slider',
                        'show_arrows' => 'yes',
                        'arrow_position' => 'custom',
                    ],
                ]
            );

            $this->end_controls_section();
        }

        protected function render() {
            $settings = $this->get_settings_for_display();
            
            $this->add_render_attribute('wrapper', 'class', 'ciccc-event-list');
            $this->add_render_attribute('wrapper', 'data-layout', $settings['layout']);
            $this->add_render_attribute('wrapper', 'data-number-of-events', $settings['number_of_events'] ?? 10);
            $this->add_render_attribute('wrapper', 'data-order-by', $settings['order_by'] ?? 'date');
            $this->add_render_attribute('wrapper', 'data-order', $settings['order'] ?? 'DESC');
            $this->add_render_attribute('wrapper', 'data-api-url', $settings['api_url']);

            if ($settings['layout'] === 'grid') {
                $columns = $settings['columns'] ?? '3';
                $this->add_render_attribute('wrapper', 'data-columns', $columns);
            } elseif ($settings['layout'] === 'slider') {
                $this->add_render_attribute('wrapper', 'data-slides-to-show', $settings['slides_to_show'] ?? 3);
                $this->add_render_attribute('wrapper', 'data-slides-to-scroll', $settings['slides_to_scroll'] ?? 1);
                $this->add_render_attribute('wrapper', 'data-autoplay', $settings['autoplay'] ?? 'yes');
                $this->add_render_attribute('wrapper', 'data-autoplay-speed', $settings['autoplay_speed'] ?? 3000);
                $this->add_render_attribute('wrapper', 'data-pause-on-hover', $settings['pause_on_hover'] ?? 'yes');
                $this->add_render_attribute('wrapper', 'data-show-arrows', $settings['show_arrows'] ?? 'yes');
                
                // Only add arrow-related attributes if arrows are enabled
                if ($settings['show_arrows'] === 'yes') {
                    $this->add_render_attribute('wrapper', 'data-arrow-position', $settings['arrow_position']);
                    
                    if ($settings['arrow_position'] === 'custom') {
                        $this->add_render_attribute('wrapper', 'data-left-arrow-offset-x', $settings['left_arrow_offset_x']['size'] . $settings['left_arrow_offset_x']['unit']);
                        $this->add_render_attribute('wrapper', 'data-left-arrow-offset-y', $settings['left_arrow_offset_y']['size'] . $settings['left_arrow_offset_y']['unit']);
                        $this->add_render_attribute('wrapper', 'data-right-arrow-offset-x', $settings['right_arrow_offset_x']['size'] . $settings['right_arrow_offset_x']['unit']);
                        $this->add_render_attribute('wrapper', 'data-right-arrow-offset-y', $settings['right_arrow_offset_y']['size'] . $settings['right_arrow_offset_y']['unit']);
                    }
                }
            }

            ?>
            <div <?php echo $this->get_render_attribute_string('wrapper'); ?>>
                <!-- Events will be loaded here via JavaScript -->
            </div>
            <?php
        }
        
    }

    $widgets_manager->register(new CICCC_Event_List_Widget());
});