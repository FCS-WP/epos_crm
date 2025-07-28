<div id="posttype-epos_crm_item" class="posttypediv">
  <p><?php _e('Add EPOS_CRM as a menu item.', 'epos_crm-for-woocommerce') ?></p>
  <div id="tabs-panel-epos_crm_item" class="tabs-panel tabs-panel-active">
    <ul id="epos_crm_item-checklist" class="categorychecklist form-no-clear">
      <li>
        <label class="menu-item-title">
          <input type="checkbox" class="menu-item-checkbox" name="menu-item[-1][menu-item-object-id]"
            value="-1" /> <?php echo __('EPOS Customer Portal', 'epos_crm-for-woocommerce'); ?>
        </label>
        <input type="hidden" class="menu-item-type" name="menu-item[-1][menu-item-type]" value="custom" />
        <input type="hidden" class="menu-item-title" name="menu-item[-1][menu-item-title]" value="<?php echo $post_name; ?>" />

        <input type="hidden" class="menu-item-classes" name="menu-item[-1][menu-item-classes]" />
      </li>
    </ul>
  </div>
  <p class="button-controls">
    <span class="add-to-menu">
      <button type="submit" class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to menu', 'woocommerce'); ?>"
        name="add-post-type-menu-item" id="submit-posttype-epos_crm_item"><?php esc_html_e('Add to menu', 'woocommerce'); ?></button>
      <span class="spinner"></span>
    </span>
  </p>
</div>
