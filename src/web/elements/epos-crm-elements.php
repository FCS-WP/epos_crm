<?php

/**
 * Epos_crm_webs FontEnd Form
 *
 *
 */

namespace EPOS_CRM\Src\Web\Elements;

defined('ABSPATH') or die();

use Elementor\Widget_Base;
use Elementor\Controls_Manager;



class Epos_Crm_Elements  extends Widget_Base
{
  public function get_name()
  {
    return 'my_custom_widget'; // Internal widget ID
  }

  public function get_title()
  {
    return __('My Custom Widget', 'my-elementor-widget');
  }

  public function get_icon()
  {
    return 'eicon-text'; // Elementor icon
  }

  public function get_categories()
  {
    return ['basic']; // Elementor category (basic, general, etc.)
  }

  protected function register_controls()
  {
    $this->start_controls_section(
      'content_section',
      [
        'label' => __('Content', 'my-elementor-widget'),
        'tab'   => Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'title',
      [
        'label'       => __('Title', 'my-elementor-widget'),
        'type'        => Controls_Manager::TEXT,
        'default'     => __('Hello World', 'my-elementor-widget'),
        'placeholder' => __('Enter your title', 'my-elementor-widget'),
      ]
    );

    $this->end_controls_section();
  }

  protected function render()
  {
    $settings = $this->get_settings_for_display();
    echo '<h2>' . esc_html($settings['title']) . '</h2>';
  }

  protected function _content_template()
  {
?>
    <#
      var title=settings.title;
      #>
      <h2>{{{ title }}}</h2>
  <?php
  }
}
