<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id="content-wrapper" div and all content
 * after Footer Widgets
 *
 * @package rtPanel
 * @since rtPanel Theme 2.0
 */

global $rtp_general;
?>
                <div class="clear"></div>
                 <?php rtp_hook_end_content_wrapper(); /* rtpanel_hook for adding content before #content-wrapper ends */ ?>
            </div><!-- end content-wrapper -->
           
            <div id="footer-wrapper"> <!-- footer-wrapper begins -->
                    <?php
                        if ( $rtp_general['footer_sidebar'] ) {

                        // Widgetized sidebar, if you have the plugin installed.
                            if ( function_exists('dynamic_sidebar') && is_active_sidebar('footer-widgets') ) {
                                echo '<div id="footerbar">';
                                    dynamic_sidebar('footer-widgets');
                                echo '</div><div class="clear"></div>';
                            } else { ?>
                                <!-- ========== [ Fall-Back Default Widgets ] ========== -->
                                    <div id="footerbar"> <!-- footerbar begins -->
                                        <div class="widget footerbar-widget"><h3 class="widgettitle"><?php _e( 'Archives', 'rtPanel' ); ?></h3><ul><?php wp_get_archives( array( 'type' => 'monthly' ) ); ?></ul></div>
                                        <div class="widget footerbar-widget"><h3 class="widgettitle"><?php _e( 'Tag Cloud', 'rtPanel' ); ?></h3> <?php wp_tag_cloud(); ?> </div>
                                        <div class="widget footerbar-widget"><h3 class="widgettitle"><?php _e( 'Meta', 'rtPanel' ); ?></h3><ul><?php wp_register(); ?><li><?php wp_loginout(); ?></li><?php wp_meta(); ?></ul></div>
                                    </div> <!-- end footerbar -->
                                <!-- ========== [ End of Default Widgets ] ========== --><?php
                            }
                        } ?>
                <?php 
                // rtpanel_hook for adding content before #footer
                    rtp_hook_before_footer();
                ?>
                <div id="footer"> <!-- footer begins -->
                    <!-- If you'd like to support WordPress, having the "powered by" link somewhere on your blog is the best way; it's our only promotion or advertising. -->
                    <p>&copy; <?php echo date( 'Y' ); echo ' - '; bloginfo( 'name' ); ?></p>
                    <p><em><?php printf( __( '<a href="%s" title="%s" target="_blank">%s</a> Theme by <a href="%s" title="This wordpress theme is designed by %s">%s</a>', 'rtPanel' ), 'http://rtpanel.com/', 'Click here for rtPanel documentation and free support.', 'rtPanel', 'http://rtcamp.com', 'rtCamp', 'rtCamp' ); ?></em></p>
                </div> <!-- end footer -->

                <?php 
                // rtpanel_hook for adding content after #footer
                    rtp_hook_after_footer();
                ?>
            </div><!-- end footer-wrapper-->
	</div><!-- end main-wrapper -->

        <?php wp_footer(); ?>
<!--        <script type="text/javascript">
            jQuery(window).load(function(){var last = jQuery('.sidebar-widget:nth-child(n+1)').attr('id');
            var tlast = jQuery('.sidebar-widget:nth-child(n+2)').attr('id');
            sticky_widgets('#'+last);
            sticky_widgets('#'+tlast);
            });
            </script>-->
    </body><!-- end body -->
</html><!-- end html -->