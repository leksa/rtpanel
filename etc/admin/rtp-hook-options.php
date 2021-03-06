<?php
/**
 * rtPanel Hooks Tab.
 *
 * @package rtPanel
 * @since rtPanel 2.0
 */


/**
 * Displays the Hooks Options tab
 *
 * @uses $screen_layout_columns int
 * @param srting $pagehook The page hook
 */
function rtpa_hooks_options_page( $pagehook ) {
    global $screen_layout_columns; ?>

    <div class="options-main-container">
        <?php rtp_get_error_or_update_messages(); ?>
        <div class="options-container">

            <form name="rt_hooks_form" id="rt_hooks_form" action="options.php" method="post" enctype="multipart/form-data">
                <?php
                //Display the required metaboxes declared in rt-settings-metaboxes.php
                add_meta_box( 'hook_options', __( 'Hook Options', 'rtPanel' ), 'rtp_hooks_metabox', $pagehook, 'normal', 'core' );
                
                //nonce for security purpose
                wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
                wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
                ?>
                <input type="hidden" name="action" value="save_rt_metaboxes_general" />

                <div id="poststuff" class="metabox-holder alignleft <?php echo 2 == $screen_layout_columns ? ' has-right-sidebar' : ''; ?>">
                    <div id="side-info-column" class="inner-sidebar">
                        <?php do_meta_boxes( $pagehook, 'side', '' ); ?>
                    </div>
                    <div id="post-body" class="has-sidebar">
                        <div id="post-body-content" class="has-sidebar-content">
                            <?php settings_fields( 'hooks_settings' ); ?>
                            <?php do_meta_boxes( $pagehook, 'normal', '' ); ?>
                        </div>
                    </div>
                    <br class="clear"/>
                    <input class="button-primary" value="<?php _e( 'Save', 'rtPanel' ); ?>" name="rtp_submit" type="submit" />
                    <input class="button-secondary" value="<?php _e( 'Reset', 'rtPanel' ); ?>" name="rtp_reset" type="submit" />
                </div>

                <script type="text/javascript">
                    //<![CDATA[
                    jQuery(document).ready( function($) {
                        // close postboxes that should be closed
                        $('.if-js-closed').removeClass('if-js-closed').addClass('closed');
                        // postboxes setup
                        postboxes.add_postbox_toggles('<?php echo $pagehook; ?>');
                    });
                    //]]>
                </script>
            </form>
        </div>
    </div><?php
} ?>