<?php

/**
 * Epos_crm_webs FontEnd Form
 *
 *
 */

namespace EPOS_CRM\Src\Web\Elements;

defined('ABSPATH') or die();

use Elementor\Widget_Base;
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use EPOS_CRM\Utils\Woo_Session_Handler;





class Epos_Crm_Elements  extends Widget_Base
{
  public function get_name()
  {
    return 'icon-box';
  }

  public function get_title()
  {
    return __('EPOS Login', 'epos-crm-widget');
  }

  public function get_icon()
  {
    return 'eicon-preferences'; //https://elementor.github.io/elementor-icons
  }

  public function get_categories()
  {
    return ['epos-crm'];
  }

  public function get_style_depends()
  {
    return ['epos_crm_web-css-css'];
  }

  protected function register_controls()
  {

    // Content Setting
    $this->start_controls_section(
      'content_section',
      [
        'label' => __('Content', 'epos-crm-widget'),
        'tab'   => Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'title_text',
      [
        'label' => esc_html__('User Name', 'elementor'),
        'type' => Controls_Manager::TEXT,
        'dynamic' => [
          'active' => true,
        ],
        'default' => esc_html__('This is User name', 'elementor'),
        'placeholder' => esc_html__('Short code [epos_crm_login]', 'elementor'),
        'label_block' => true,
      ]
    );

    $this->add_control(
      'selected_icon',
      [
        'label' => esc_html__('Icon', 'elementor'),
        'type' => Controls_Manager::ICONS,
        'fa4compatibility' => 'icon',
        'default' => [
          'value' => 'fas fa-user-circle',
          'library' => 'fa-solid',
        ],
      ]
    );

    $this->add_control(
      'title_size',
      [
        'label' => esc_html__('User name HTML Tag', 'elementor'),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'h1' => 'H1',
          'h2' => 'H2',
          'h3' => 'H3',
          'h4' => 'H4',
          'h5' => 'H5',
          'h6' => 'H6',
          'div' => 'div',
          'span' => 'span',
          'p' => 'p',
        ],
        'default' => 'span',
      ]
    );

    $this->end_controls_section();

    // Style Setting

    // Icon Box Setting
    $this->start_controls_section(
      'section_style_box',
      [
        'label' => esc_html__('Box', 'elementor'),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_responsive_control(
      'position',
      [
        'label' => esc_html__('Icon Position', 'elementor'),
        'type' => Controls_Manager::CHOOSE,
        'default' => 'top',
        'mobile_default' => 'top',
        'options' => [
          'left' => [
            'title' => esc_html__('Left', 'elementor'),
            'icon' => 'eicon-h-align-left',
          ],
          'top' => [
            'title' => esc_html__('Top', 'elementor'),
            'icon' => 'eicon-v-align-top',
          ],
          'right' => [
            'title' => esc_html__('Right', 'elementor'),
            'icon' => 'eicon-h-align-right',
          ],
        ],
        'prefix_class' => 'elementor%s-position-',
        'condition' => [
          'selected_icon[value]!' => '',
        ],
      ]
    );

    $this->add_responsive_control(
      'content_vertical_alignment',
      [
        'label' => esc_html__('Vertical Alignment', 'elementor'),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'top' => [
            'title' => esc_html__('Top', 'elementor'),
            'icon' => 'eicon-v-align-top',
          ],
          'middle' => [
            'title' => esc_html__('Middle', 'elementor'),
            'icon' => 'eicon-v-align-middle',
          ],
          'bottom' => [
            'title' => esc_html__('Bottom', 'elementor'),
            'icon' => 'eicon-v-align-bottom',
          ],
        ],
        'default' => 'top',
        'toggle' => false,
        'prefix_class' => 'elementor-vertical-align-',
        'condition' => [
          'position!' => 'top',
        ],
      ]
    );

    $this->add_responsive_control(
      'text_align',
      [
        'label' => esc_html__('Alignment', 'elementor'),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'left' => [
            'title' => esc_html__('Left', 'elementor'),
            'icon' => 'eicon-text-align-left',
          ],
          'center' => [
            'title' => esc_html__('Center', 'elementor'),
            'icon' => 'eicon-text-align-center',
          ],
          'right' => [
            'title' => esc_html__('Right', 'elementor'),
            'icon' => 'eicon-text-align-right',
          ],
          'justify' => [
            'title' => esc_html__('Justified', 'elementor'),
            'icon' => 'eicon-text-align-justify',
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-icon-box-wrapper' => 'text-align: {{VALUE}};',
        ],
        'separator' => 'after',
      ]
    );

    $this->add_responsive_control(
      'icon_space',
      [
        'label' => esc_html__('Icon Spacing', 'elementor'),
        'type' => Controls_Manager::SLIDER,
        'size_units' => ['px', '%', 'em', 'rem', 'vw', 'custom'],
        'default' => [
          'size' => 15,
        ],
        'range' => [
          'px' => [
            'max' => 100,
          ],
          'em' => [
            'max' => 10,
          ],
          'rem' => [
            'max' => 10,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}}' => '--icon-box-icon-margin: {{SIZE}}{{UNIT}}',
        ],
        'condition' => [
          'selected_icon[value]!' => '',
        ],
      ]
    );

    $this->add_responsive_control(
      'title_bottom_space',
      [
        'label' => esc_html__('User Name Spacing', 'elementor'),
        'type' => Controls_Manager::SLIDER,
        'size_units' => ['px', 'em', 'rem', 'custom'],
        'range' => [
          'px' => [
            'max' => 100,
          ],
          'em' => [
            'min' => 0,
            'max' => 10,
          ],
          'rem' => [
            'min' => 0,
            'max' => 10,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-icon-box-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();
    // End Icon Box Setting

    // Icon Setting

    $this->start_controls_section(
      'section_style_icon',
      [
        'label' => esc_html__('User Icon', 'elementor'),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => [
          'selected_icon[value]!' => '',
        ],
      ]
    );
    // Color Setting
    $this->start_controls_tabs('icon_colors');

    // Tab normal
    $this->start_controls_tab(
      'icon_colors_normal',
      [
        'label' => esc_html__('Normal', 'elementor'),
      ]
    );

    $this->add_control(
      'primary_color',
      [
        'label' => esc_html__('Primary Color', 'elementor'),
        'type' => Controls_Manager::COLOR,
        'global' => [
          'default' => Global_Colors::COLOR_PRIMARY,
        ],
        'default' => '',
        'selectors' => [
          '{{WRAPPER}}.elementor-view-stacked .elementor-icon' => 'background-color: {{VALUE}};',
          '{{WRAPPER}}.elementor-view-framed .elementor-icon, {{WRAPPER}}.elementor-view-default .elementor-icon' => 'fill: {{VALUE}}; color: {{VALUE}}; border-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'secondary_color',
      [
        'label' => esc_html__('Secondary Color', 'elementor'),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'condition' => [
          'view!' => 'default',
        ],
        'selectors' => [
          '{{WRAPPER}}.elementor-view-framed .elementor-icon' => 'background-color: {{VALUE}};',
          '{{WRAPPER}}.elementor-view-stacked .elementor-icon' => 'fill: {{VALUE}}; color: {{VALUE}};',
        ],
      ]
    );

    // Size
    $this->add_responsive_control(
      'icon_size',
      [
        'label' => esc_html__('Size', 'elementor'),
        'type' => Controls_Manager::SLIDER,
        'size_units' => ['px', '%', 'em', 'rem', 'vw', 'custom'],
        'range' => [
          'px' => [
            'min' => 6,
            'max' => 300,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
        ],
      ]
    );
    $this->end_controls_tab();

    // End Normal Tab

    // Hover Setting
    $this->start_controls_tab(
      'icon_colors_hover',
      [
        'label' => esc_html__('Hover', 'elementor'),
      ]
    );

    $this->add_control(
      'hover_primary_color',
      [
        'label' => esc_html__('Primary Color', 'elementor'),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}}.elementor-view-stacked .elementor-icon:hover' => 'background-color: {{VALUE}};',
          '{{WRAPPER}}.elementor-view-framed .elementor-icon:hover, {{WRAPPER}}.elementor-view-default .elementor-icon:hover' => 'fill: {{VALUE}}; color: {{VALUE}}; border-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'hover_secondary_color',
      [
        'label' => esc_html__('Secondary Color', 'elementor'),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'condition' => [
          'view!' => 'default',
        ],
        'selectors' => [
          '{{WRAPPER}}.elementor-view-framed .elementor-icon:hover' => 'background-color: {{VALUE}};',
          '{{WRAPPER}}.elementor-view-stacked .elementor-icon:hover' => 'fill: {{VALUE}}; color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'hover_animation',
      [
        'label' => esc_html__('Hover Animation', 'elementor'),
        'type' => Controls_Manager::HOVER_ANIMATION,
      ]
    );

    $this->end_controls_tab();

    $this->end_controls_section();

    $this->start_controls_section(
      'section_style_content',
      [
        'label' => esc_html__('Content', 'elementor'),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control(
      'heading_title',
      [
        'label' => esc_html__('Title', 'elementor'),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'title_color',
      [
        'label' => esc_html__('Color', 'elementor'),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-icon-box-title' => 'color: {{VALUE}};',
        ],
        'global' => [
          'default' => Global_Colors::COLOR_PRIMARY,
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'title_typography',
        'selector' => '{{WRAPPER}} .elementor-icon-box-title, {{WRAPPER}} .elementor-icon-box-title a',
        'global' => [
          'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Text_Stroke::get_type(),
      [
        'name' => 'text_stroke',
        'selector' => '{{WRAPPER}} .elementor-icon-box-title',
      ]
    );

    $this->add_group_control(
      Group_Control_Text_Shadow::get_type(),
      [
        'name' => 'title_shadow',
        'selector' => '{{WRAPPER}} .elementor-icon-box-title',
      ]
    );

    $this->end_controls_section();
  }

  protected function render()
  {
    $settings = $this->get_settings_for_display();
    $has_link = ! empty($settings['link']['url']);
    $this->add_render_attribute('icon', 'class', ['elementor-icon', 'elementor-animation-' . $settings['hover_animation']]);
    $this->add_render_attribute('_wrapper', 'id', ['epos_crm_login']);
    $this->add_render_attribute('_wrapper', 'class', ['elementor-widget-icon-box']);

    $html_tag = $has_link ? 'a' : 'span';
    $migrated = isset($settings['__fa4_migrated']['selected_icon']);
    $is_new = ! isset($settings['icon']) && Icons_Manager::is_migration_allowed();
    $has_content = ! Utils::is_empty($settings['title_text']);
    $session = new Woo_Session_Handler;

    $is_login = !empty($session->get('epos_customer_data')) ? true : false;


?>
    <!-- Icon  -->
    <div class="elementor-icon-box-wrapper">
      <div class="elementor-icon-box-icon">
        <<?php Utils::print_validated_html_tag($html_tag); ?>
          <?php $this->print_render_attribute_string('link'); ?>
          <?php $this->print_render_attribute_string('icon'); ?>>
          <?php
          if ($is_new || $migrated) {
            Icons_Manager::render_icon($settings['selected_icon'], ['aria-hidden' => 'true']);
          } elseif (! empty($settings['icon'])) {
          ?><i <?php $this->print_render_attribute_string('i'); ?>> </i>

          <?php
          }
          ?>
        </<?php Utils::print_validated_html_tag($html_tag); ?>>
      </div>
      <!-- Username  -->
      <?php if ($has_content) : ?>
        <div class="elementor-icon-box-content">

          <?php if (! Utils::is_empty($settings['title_text'])) : ?>
            <<?php Utils::print_validated_html_tag($settings['title_size']); ?> class="elementor-icon-box-title">
              <<?php Utils::print_validated_html_tag($html_tag); ?> <?php $this->print_render_attribute_string('link'); ?> <?php $this->print_render_attribute_string('title_text'); ?>>
                <?php $this->print_unescaped_setting('title_text'); ?>
              </<?php Utils::print_validated_html_tag($html_tag); ?>>
            </<?php Utils::print_validated_html_tag($settings['title_size']); ?>>
          <?php endif; ?>

        </div>
      <?php endif; ?>
    </div>
    <?php if ($is_login ): ?>
      <div id="epos_crm_login_dropdown">
        <ul class="epos_crm_dropdown sub-menu">
          <li class="dropdown_item">
            <a href="<?php echo wp_logout_url(get_permalink()); ?>" class="elementor-item ">Logout</a>
          </li>
        </ul>
      </div>
    <?php endif; ?>
  <?php
  }

  protected function content_template()
  {
  ?>
    <#
      // For older version `settings.icon` is needed.
      var hasIcon=settings.icon || settings.selected_icon.value;
      var hasContent=settings.title_text;

      if ( ! hasIcon && ! hasContent ) {
      return;
      }

      var hasLink=settings.link,
      htmlTag=hasLink ? 'a' : 'span' ,
      iconHTML=elementor.helpers.renderIcon( view, settings.selected_icon, { 'aria-hidden' : true }, 'i' , 'object' ),
      migrated=elementor.helpers.isIconMigrated( settings, 'selected_icon' ),
      titleSizeTag=elementor.helpers.validateHTMLTag( settings.title_size );

      view.addRenderAttribute( 'icon' , 'class' , 'elementor-icon elementor-animation-' + settings.hover_animation );

      if ( hasLink ) {
      view.addRenderAttribute( 'link' , 'href' , settings.link );
      view.addRenderAttribute( 'icon' , 'tabindex' , '-1' );
      }

      view.addInlineEditingAttributes( 'title_text' , 'none' );
      #>
      <div class="elementor-icon-box-wrapper">

        <# if ( hasIcon ) { #>
          <div class="elementor-icon-box-icon">
            <{{{ htmlTag }}} {{{ view.getRenderAttributeString( 'link' ) }}} {{{ view.getRenderAttributeString( 'icon' ) }}}>
              <# if ( iconHTML && iconHTML.rendered && ( ! settings.icon || migrated ) ) { #>
                {{{ elementor.helpers.sanitize( iconHTML.value ) }}}
                <# } else { #>
                  <i class="{{ _.escape( settings.icon ) }}" aria-hidden="true"></i>
                  <# } #>
            </{{{ htmlTag }}}>
          </div>
          <# } #>

            <# if ( hasContent ) { #>
              <div class="elementor-icon-box-content">

                <# if ( settings.title_text ) { #>
                  <{{{ titleSizeTag }}} class="elementor-icon-box-title">
                    <{{{ htmlTag }}} {{{ view.getRenderAttributeString( 'link' ) }}} {{{ view.getRenderAttributeString( 'title_text' ) }}}>
                      {{{ elementor.helpers.sanitize( settings.title_text ) }}}
                    </{{{ htmlTag }}}>
                  </{{{ titleSizeTag }}}>
                  <# } #>

              </div>
              <# } #>
                <div id="epos_crm_login_dropdown">
                  <ul class="epos_crm_dropdown sub-menu">
                    <li class="dropdown_item">
                      <a href="<?php echo wp_logout_url(get_permalink()); ?>" class="elementor-item ">Logout</a>
                    </li>
                  </ul>
                </div>
      </div>
  <?php
  }

  public function on_import($element)
  {
    return Icons_Manager::on_import_migration($element, 'icon', 'selected_icon', true);
  }
}
