<?php
/* 
 * rtPanel default functions
 *
 * @package rtPanel
 *
 * @since rtPanel 2.0
 */

/**
 * Checks whether the post meta div needs to be displayed or not
 *
 * @uses $rtp_post_comments Post Comments DB array
 * @uses $post Post Data
 * @param string $position Specify the position of the post meta (u/l)
 *
 * @since rtPanel 2.1
 */
function rtp_has_postmeta( $position = 'u' ) {
    global $post, $rtp_post_comments;
    $can_edit = ( get_edit_post_link() ) ? 1 : 0;
    $flag = 0;
    // Show Author?
    if ( $rtp_post_comments['post_author_'.$position] ) {
        $flag = 1;
    }
    // Comments ?
    elseif ( @comments_open() && has_action( 'rtp_hook_post_meta_top_comment' ) && $position == 'u' ){
        $flag = 1;
    } 
    // Show Date?
    elseif ( $rtp_post_comments['post_date_'.$position] )  {
        $flag = 1;
    }
     // Show Category?
    elseif ( get_the_category_list() && $rtp_post_comments['post_category_'.$position] ) {
        $flag = 1;
    }
    // Show Tags?
    elseif ( get_the_tag_list() && $rtp_post_comments['post_tags_'.$position] ) {
        $flag = 1;
    } 
    // Checked if logged in and post meta top
    else if ( $can_edit && $position == 'u' ) {
        $flag = 1;
    } 
    elseif ( ( has_action( 'rtp_hook_begin_post_meta_top' ) || ( has_action( 'rtp_hook_end_post_meta_top' ) && $can_edit ) ) && $position == 'u' ) {
        $flag = 1;
    }
    elseif ( ( has_action( 'rtp_hook_begin_post_meta_bottom' ) || has_action( 'rtp_hook_end_post_meta_bottom' ) ) && $position == 'l' ) {
        $flag = 1;
    }
    else {
        // Show Custom Taxonomies?
        $args = array( '_builtin' => false );
        $taxonomies = get_taxonomies( $args, 'names' );
        foreach ( $taxonomies as $taxonomy ) {
            if ( get_the_terms( $post->ID, $taxonomy ) && isset( $rtp_post_comments['post_'.$taxonomy.'_'.$position] ) && $rtp_post_comments['post_'.$taxonomy.'_'.$position] ) {
                $flag = 1;
            }
        }
    }
    
    return $flag;
        
}

/**
 * Default post meta
 *
 * @uses $rtp_post_comments Post Comments DB array
 * @uses $post Post Data
 * @param string $placement Specify the position of the post meta (top/bottom)
 *
 * @since rtPanel 2.0
 */
function rtp_default_post_meta( $placement = 'top' ) { 
    
        if ( is_page() ) {
            if ( get_edit_post_link() && ( 'top' == $placement ) ) { ?>
                <div class="post-meta post-meta-top"><?php rtp_hook_end_post_meta_top(); ?></div><?php
            }
        } else {
            global $post, $rtp_post_comments;
            $position = ( 'bottom' == $placement ) ? 'l' : 'u'; // l = Lower/Bottom , u = Upper/Top
            ?>
            
            <?php if ( rtp_has_postmeta( $position ) ) {
                    if ( $position == 'l' ) { echo '<footer class="post-footer">'; } ?>
                    <div class="post-meta post-meta-<?php echo $placement; ?>">

                        <?php   
                        if( 'bottom' == $placement )
                            rtp_hook_begin_post_meta_bottom();
                        else
                            rtp_hook_begin_post_meta_top();
                        // Author Link
                                if ( $rtp_post_comments['post_author_'.$position] || $rtp_post_comments['post_date_'.$position] ) { ?>
                                    <p class="alignleft post-publish"><?php
                                        if ( $rtp_post_comments['post_author_'.$position] ) {
                                            printf( __( 'by <span class="author vcard">%s</span>', 'rtPanel' ), ( !$rtp_post_comments['author_link_'.$position] ? get_the_author() . ( $rtp_post_comments['author_count_'.$position] ? '(' . get_the_author_posts() . ')' : '' ) : sprintf( __( '<a href="%1$s" title="%2$s">%3$s</a>', 'rtPanel' ), get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ) ), esc_attr( sprintf( __( 'Posts by %s', 'rtPanel' ), get_the_author() ) ), get_the_author() ) . ( $rtp_post_comments['author_count_'.$position] ? '(' . get_the_author_posts() . ')' : '' ) ) );
                                        }
                                        echo ( $rtp_post_comments['post_author_'.$position] && $rtp_post_comments['post_date_'.$position] ) ? ' ' : '';
                                        if ( $rtp_post_comments['post_date_'.$position] ) {
                                            printf( __( 'on <time class="published" datetime="%s">%s</time>', 'rtPanel' ), get_the_time( 'Y-m-d' ), get_the_time( $rtp_post_comments['post_date_format_'.$position] ) );
                                        } ?>
                                    </p><?php
                                } ?>

                        <?php   // Comment Count
                                if ( @comments_open() && $position == 'u' ) { // If post meta is set to top then only display the comment count. 
                                    add_filter( 'get_comments_number', 'rtp_only_comment_count', 11, 2 );
                                    rtp_hook_post_meta_top_comment();      
                                    remove_filter( 'get_comments_number', 'rtp_only_comment_count', 11, 2 );
                                } ?>

                        <?php   // Post Categories
                                echo ( get_the_category_list() && $rtp_post_comments['post_category_'.$position] ) ? '<p class="post-category alignleft">' . __( 'Category', 'rtPanel' ) . ': <span>' . get_the_category_list( ', ' ) . '</span></p>' : ''; ?>

                        <?php   // Post Tags
                                echo ( get_the_tag_list() && $rtp_post_comments['post_tags_'.$position] ) ? '<p class="post-tags alignleft">' . get_the_tag_list( __( 'Tagged in', 'rtPanel' ) . ': <span>', ', ', '</span>' ) . '</p>' : ''; ?>

                        <?php   // Post Custom Taxonomies
                                $args = array( '_builtin' => false );
                                $taxonomies = get_taxonomies( $args, 'objects' );
                                foreach ( $taxonomies as $key => $taxonomy ) {
                                    ( get_the_terms( $post->ID, $key ) && isset( $rtp_post_comments['post_'.$key.'_'.$position] ) && $rtp_post_comments['post_'.$key.'_'.$position] ) ? the_terms( $post->ID, $key, '<p class="post-custom-tax post-' . $key . ' alignleft">' . $taxonomy->labels->singular_name . ': ', ', ', '</p>' ) : '';
                                }

                        if ( 'bottom' == $placement )
                            rtp_hook_end_post_meta_bottom();
                        else
                            rtp_hook_end_post_meta_top(); ?>
                    </div><!-- .post-meta --><?php
                if ( $position == 'l' ) { echo '</footer>'; }
            }
        }
 }
add_action('rtp_hook_post_meta_top','rtp_default_post_meta'); // Post Meta Top
add_action('rtp_hook_post_meta_bottom','rtp_default_post_meta'); // Post Meta Bottom

/**
 * Default Navigation Menu
 *
 * @since rtPanel 2.0
 */
function rtp_default_nav_menu() {
     echo '<nav id="rtp-primary-menu" role="navigation">';
        /* Call wp_nav_menu() for Wordpress Navigaton with fallback wp_list_pages() if menu not set in admin panel */
        if ( function_exists( 'wp_nav_menu' ) && has_nav_menu( 'primary' ) ) {
            wp_nav_menu( array( 'container' => '', 'menu_id' => 'rtp-nav-menu', 'theme_location' => 'primary', 'depth' => apply_filters( 'rtp_nav_menu_depth', 4 ) ) );
        } else {
            echo '<ul class="menu" id="rtp-nav-menu">';
                wp_list_pages( array( 'title_li' => '', 'sort_column' => 'menu_order', 'number' => '5', 'depth' => apply_filters( 'rtp_nav_menu_depth', 4 ) ) );
            echo '</ul>';
        }
    echo '<div class="clear"></div></nav>';
}
add_action('rtp_hook_after_header','rtp_default_nav_menu'); // Adds default nav menu after #header

/**
 * 'Edit this post' link for post/page
 *
 * @since rtPanel 2.0
 */
function rtp_edit_link() {
    // Call Edit Link
    edit_post_link( __( 'Edit', 'rtPanel' ), '<p class="rtp-edit-link">[', ']</p>');
}
add_action('rtp_hook_end_post_meta_top', 'rtp_edit_link');

/**
 * Prepends and Appends Braces to Read More text
 *
 * @param string $text read more text
 * @return string
 *
 * @since rtPanel 2.0
 */
function rtp_readmore_braces( $text ) {
   return '<span class="rtp-courly-bracket">[ </span>'. $text .'<span class="rtp-courly-bracket"> ]</span>';
}
add_filter( 'rtp_readmore', 'rtp_readmore_braces' );

/**
 * Prepends and Appends Braces to Comment Number
 *
 * @param string $text comment count text
 * @return string
 *
 * @since rtPanel 2.0.9
 */
function rtp_comment_braces( $text ) {
   return '<span class="rtp-courly-bracket">{ </span>'. $text .'<span class="rtp-courly-bracket"> }</span>';
}
add_filter( 'rtp_comment_count', 'rtp_comment_braces' );

/**
 * Prepends and Appends Braces to Read More text
 *
 * @param string $text comment count text
 * @return string
 *
 * @since rtPanel 2.0.9
 */
function rtp_comment_count() { ?>
   <p class="alignright rtp-post-comment-count"><span class="rtp-courly-bracket">{</span><?php comments_popup_link( __( '<span>0</span> Comments', 'rtPanel' ), __( '<span>1</span> Comment', 'rtPanel' ), __( '<span>%</span> Comments', 'rtPanel' ), 'rtp-post-comment' ); ?><span class="rtp-courly-bracket">}</span></p><?php
}
add_action( 'rtp_hook_post_meta_top_comment', 'rtp_comment_count' );

/**
 * Adds breadcrumb support to the theme.
 *
 * @since rtPanel 2.0.7
 */
function rtp_breadcrumb_support( $text ) { 
   // Breadcrumb Support
    if ( function_exists( 'bcn_display' ) ) {
        echo '<div class="breadcrumb">';
            bcn_display();
        echo '</div>';
    }
}
add_action( 'rtp_hook_begin_content', 'rtp_breadcrumb_support' );

/**
 * Adds Site Description
 *
 * @since rtPanel 2.0.7
 */
function rtp_blog_description(){
    if ( get_bloginfo( 'description' ) ) { ?>
        <h2 class="tagline"><?php bloginfo( 'description' ); ?></h2><?php
    }
}
add_action( 'rtp_hook_after_logo', 'rtp_blog_description' );

/**
 * Appends a "last-menu-item" class to the last item of the nav menu.
 *
 * @since rtPanel 2.0.9
 */
function rtp_add_markup_pages( $output ) {
    $output = substr_replace( $output, "last-menu-item menu-item", strripos( $output, "menu-item" ), strlen( "menu-item" ) );
    return $output;
}
add_filter('wp_nav_menu', 'rtp_add_markup_pages');

/**
 * Adds pagination to single
 *
 * @since rtPanel 2.1
 */
function rtp_default_single_pagination() {
    if ( is_single() && ( get_adjacent_post( '', '', true ) || get_adjacent_post( '', '', false ) ) ){ ?>
        <div class="rtp-navigation clearfix">
            <?php if ( get_adjacent_post( '', '', true ) ) { ?><div class="alignleft"><?php previous_post_link( '%link', __( '&larr; %title', 'rtPanel' ) ); ?></div><?php } ?>
            <?php if ( get_adjacent_post( '', '', false ) ) { ?><div class="alignright"><?php next_post_link( '%link', __( '%title &rarr;', 'rtPanel' ) ); ?></div><?php } ?>
        </div><!-- .rtp-navigation --><?php
    }
}
add_action( 'rtp_hook_single_pagination', 'rtp_default_single_pagination' );

/**
 * Adds pagination to archives
 *
 * @since rtPanel 2.1
 */
function rtp_default_archive_pagination() { 
    /* Page-Navi Plugin Support with WordPress Default Pagination */
    if ( !is_singular() ) {
        if ( function_exists( 'wp_pagenavi' ) ) {
            wp_pagenavi();
        } elseif ( get_next_posts_link() || get_previous_posts_link() ) { ?>
            <div class="rtp-navigation clearfix">
                <?php if ( get_next_posts_link() ) { ?><div class="alignleft"><?php next_posts_link( __( '&larr; Older Entries', 'rtPanel' ) ); ?></div><?php } ?>
                <?php if ( get_previous_posts_link() ) { ?><div class="alignright"><?php previous_posts_link( __( 'Newer Entries &rarr;', 'rtPanel' ) ); ?></div><?php } ?>
            </div><!-- .rtp-navigation --><?php
        }
    }
}
add_action( 'rtp_hook_archive_pagination', 'rtp_default_archive_pagination' );

/**
 * Displays the sidebar.
 *
 * @since rtPanel 2.1
 */
function rtp_default_sidebar() {
    get_sidebar();
}
add_action( 'rtp_hook_sidebar', 'rtp_default_sidebar' );

/**
 * Displays the sidebar.
 *
 * @since rtPanel 2.1
 */
function rtp_default_comments() {
    if ( is_singular() ) {
        comments_template( '', true );
    }
}
add_action( 'rtp_hook_comments', 'rtp_default_comments' );