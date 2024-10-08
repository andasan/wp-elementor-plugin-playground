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

            // Card Styling
            $this->start_controls_section(
                'card_style_section',
                [
                    'label' => 'Card Styling',
                    'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_control(
                'card_background_color',
                [
                    'label' => 'Background Color',
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ciccc-event-card' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                \Elementor\Group_Control_Border::get_type(),
                [
                    'name' => 'card_border',
                    'label' => 'Border',
                    'selector' => '{{WRAPPER}} .ciccc-event-card',
                ]
            );

            $this->add_control(
                'card_border_radius',
                [
                    'label' => 'Border Radius',
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .ciccc-event-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'card_padding',
                [
                    'label' => 'Padding',
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .ciccc-event-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->end_controls_section();

            // Title Styling
            $this->start_controls_section(
                'title_style_section',
                [
                    'label' => 'Title Styling',
                    'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_control(
                'title_color',
                [
                    'label' => 'Color',
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ciccc-event-card h3' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'title_typography',
                    'label' => 'Typography',
                    'selector' => '{{WRAPPER}} .ciccc-event-card h3',
                ]
            );

            $this->end_controls_section();

            // Details Styling
            $this->start_controls_section(
                'details_style_section',
                [
                    'label' => 'Details Styling',
                    'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_control(
                'details_color',
                [
                    'label' => 'Text Color',
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ciccc-event-card-details span' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'details_typography',
                    'label' => 'Typography',
                    'selector' => '{{WRAPPER}} .ciccc-event-card-details span',
                ]
            );

            $this->add_control(
                'icon_color',
                [
                    'label' => 'Icon Color',
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ciccc-event-card-details i' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_section();

            // Slider Arrow Styling
            $this->start_controls_section(
                'arrow_style_section',
                [
                    'label' => 'Slider Arrow Styling',
                    'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                    'condition' => [
                        'layout' => 'slider',
                        'show_arrows' => 'yes',
                    ],
                ]
            );

            $this->start_controls_tabs('arrow_style_tabs');

            // Normal state
            $this->start_controls_tab(
                'arrow_style_normal',
                ['label' => 'Normal']
            );

            $this->add_control(
                'arrow_color',
                [
                    'label' => 'Color',
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .slick-prev:before, {{WRAPPER}} .slick-next:before' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();

            // Hover state
            $this->start_controls_tab(
                'arrow_style_hover',
                ['label' => 'Hover']
            );

            $this->add_control(
                'arrow_color_hover',
                [
                    'label' => 'Color',
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .slick-prev:hover:before, {{WRAPPER}} .slick-next:hover:before' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->add_control(
                'arrow_size',
                [
                    'label' => 'Size',
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['px'],
                    'range' => [
                        'px' => [
                            'min' => 10,
                            'max' => 50,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .slick-prev:before, {{WRAPPER}} .slick-next:before' => 'font-size: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->end_controls_section();

            // Slider Pagination Styling
            $this->start_controls_section(
                'pagination_style_section',
                [
                    'label' => 'Slider Pagination Styling',
                    'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                    'condition' => [
                        'layout' => 'slider',
                    ],
                ]
            );

            $this->start_controls_tabs('dot_style_tabs');

            // Normal state
            $this->start_controls_tab(
                'dot_style_normal',
                ['label' => 'Normal']
            );

            $this->add_control(
                'dot_color',
                [
                    'label' => 'Color',
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .slick-dots li button:before' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();

            // Hover state
            $this->start_controls_tab(
                'dot_style_hover',
                ['label' => 'Hover']
            );

            $this->add_control(
                'dot_color_hover',
                [
                    'label' => 'Color',
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .slick-dots li button:hover:before, {{WRAPPER}} .slick-dots li button:focus:before' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();

            // Active state
            $this->start_controls_tab(
                'dot_style_active',
                ['label' => 'Active']
            );

            $this->add_control(
                'active_dot_color',
                [
                    'label' => 'Color',
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .slick-dots li.slick-active button:before' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->add_control(
                'dot_size',
                [
                    'label' => 'Size',
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['px'],
                    'range' => [
                        'px' => [
                            'min' => 5,
                            'max' => 20,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .slick-dots li button:before' => 'font-size: {{SIZE}}{{UNIT}};',
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
                if (($settings['show_arrows'] ?? 'yes') === 'yes') {
                    $this->add_render_attribute('wrapper', 'data-arrow-position', $settings['arrow_position'] ?? 'outside');
                    
                    if (($settings['arrow_position'] ?? 'outside') === 'custom') {
                        $this->add_render_attribute('wrapper', 'data-left-arrow-offset-x', $settings['left_arrow_offset_x']['size'] ?? 0 . ($settings['left_arrow_offset_x']['unit'] ?? 'px'));
                        $this->add_render_attribute('wrapper', 'data-left-arrow-offset-y', $settings['left_arrow_offset_y']['size'] ?? 0 . ($settings['left_arrow_offset_y']['unit'] ?? 'px'));
                        $this->add_render_attribute('wrapper', 'data-right-arrow-offset-x', $settings['right_arrow_offset_x']['size'] ?? 0 . ($settings['right_arrow_offset_x']['unit'] ?? 'px'));
                        $this->add_render_attribute('wrapper', 'data-right-arrow-offset-y', $settings['right_arrow_offset_y']['size'] ?? 0 . ($settings['right_arrow_offset_y']['unit'] ?? 'px'));
                    }
                }

                $this->add_render_attribute('wrapper', 'data-arrow-color', $settings['arrow_color'] ?? '#000000');
                $this->add_render_attribute('wrapper', 'data-arrow-color-hover', $settings['arrow_color_hover'] ?? '#666666');
                $this->add_render_attribute('wrapper', 'data-arrow-size', $settings['arrow_size']['size'] ?? 20);
                $this->add_render_attribute('wrapper', 'data-dot-color', $settings['dot_color'] ?? '#000000');
                $this->add_render_attribute('wrapper', 'data-dot-color-hover', $settings['dot_color_hover'] ?? '#666666');
                $this->add_render_attribute('wrapper', 'data-active-dot-color', $settings['active_dot_color'] ?? '#000000');
                $this->add_render_attribute('wrapper', 'data-dot-size', $settings['dot_size']['size'] ?? 10);
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