<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * e.g., it puts together the home page when no home.php file exists.
 *
 * Learn more: {@link https://codex.wordpress.org/Template_Hierarchy}
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */
?>

<html>
<head>
    <link rel="stylesheet" href="/wp-admin/css/common.css?ver=4.5.9">
    <link rel="stylesheet" href="<?php echo get_stylesheet_uri() ?>">
    <?php wp_head(); ?>
</head>

<body class="root-page <?php echo (is_admin_bar_showing() ? 'with-admin-bar' : '') ?>">

<div id="wpbody" role="main">

    <div id="wpbody-content" aria-label="Main content" tabindex="0" style="overflow: hidden;">
        <div class="wrap">
            <h1>Welcome to Rooftop CMS</h1>


            <div>
                <div>
                    <p>
                        Since Rooftop is an API-first CMS, there's not much to see on this page.
                        <br>
                        Looking for your admin area? Head over <a href="/wp-admin">here</a>...
                    </p>

                    <p>
                        If you need any help, feel free to <a href="mailto:hello@rooftopcms.com">get in touch</a>.
                    </p>
                </div>
            </div>

            <div id="dashboard-widgets-wrap">
                <div id="dashboard-widgets" class="metabox-holder">
                    <div id="postbox-container-1" class="postbox-container">
                        <div>
                            <h3>
                                The Rooftop API
                            </h3>

                            <div class="inside">
                                <div class="main">
                                    <p>
                                        Add an API key <a href="/wp-admin/admin.php?page=rooftop-overview">here</a> or add some
                                        <a href="/wp-admin/admin.php?page=rooftop-custom-content-setup-overview">content types</a>.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="postbox-container-1" class="postbox-container">
                        <div>
                            <h3>
                                Docs &amp; code
                            </h3>

                            <div class="inside">
                                <div class="main">
                                    <p>
                                        Check out the docs at <a href="https://rooftopcms.readme.io">https://rooftopcms.readme.io</a>.
                                    </p>

                                    <p>
                                        If you'd like to be hands on and help us develop Rooftop, head over to
                                        <a href="https://github.com/rooftopcms">GitHub</a>.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="closedpostboxesnonce" name="closedpostboxesnonce" value="48a1ff72f9"><input type="hidden" id="meta-box-order-nonce" name="meta-box-order-nonce" value="e514a4af16">	</div><!-- dashboard-widgets-wrap -->

        </div><!-- wrap -->


        <div class="clear"></div></div><!-- wpbody-content -->
    <div class="clear"></div></div>

    <?php wp_footer(); ?>
</body>
</html>

