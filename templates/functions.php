<?php
/**
 * This file contains theme script, styles and other theme related functions.
 * This file can be overridden by creating a anspress directory in active theme folder.
 *
 * @license   https://www.gnu.org/licenses/gpl-2.0.txt GNU Public License
 * @author    Rahul Aryan <support@anspress.io>
 * @package   WordPress/AnsPress
 */

/**
 * Enqueue scripts.
 */
function ap_scripts_front() {
	if ( ! is_anspress() && ap_opt( 'load_assets_in_anspress_only' ) ) {
		return;
	}

	ap_enqueue_scripts();

	$custom_css = '
		#anspress .ap-q-cells{
				margin-' . ( is_rtl() ? 'right' : 'left' ).': '. ( ap_opt( 'avatar_size_qquestion' ) + 10 ) . 'px;
		}
		#anspress .ap-a-cells{
				margin-' . ( is_rtl() ? 'right' : 'left' ) . ': ' . ( ap_opt( 'avatar_size_qanswer' ) + 10 ) . 'px;
		}#anspress .ap-comment-content{
				margin-' . ( is_rtl() ? 'right' : 'left' ) . ': ' . ( ap_opt( 'avatar_size_qcomment' ) + 15 ) . 'px;
		}';

	wp_add_inline_style( 'anspress-main', $custom_css );
	do_action( 'ap_enqueue' );
	wp_enqueue_style( 'ap-overrides', ap_get_theme_url( 'css/overrides.css' ), array( 'anspress-main' ), AP_VERSION );

	$aplang = array(
		'password_field_not_macthing' => __( 'Password not matching', 'anspress-question-answer' ),
		'password_length_less' => __( 'Password length must be 6 or higher', 'anspress-question-answer' ),
		'not_valid_email' => __( 'Not a valid email', 'anspress-question-answer' ),
		'username_less' => __( 'Username length must be 4 or higher', 'anspress-question-answer' ),
		'username_not_avilable' => __( 'Username not available', 'anspress-question-answer' ),
		'email_already_in_use' => sprintf(
			'%1$s <a href="%2$s">%3$s</a>',
			__( 'Email already in use.', 'anspress-question-answer' ),
			wp_lostpassword_url(),
			__( 'Do you want to reset your password?', 'anspress-question-answer' )
		),
		'loading' => __( 'Loading', 'anspress-question-answer' ),
		'sending' => __( 'Sending request', 'anspress-question-answer' ),
		'uploading' => __( 'Uploading', 'anspress-question-answer' ),
		'not_valid_response' => __( 'Something went wrong in server side, not a valid response.', 'anspress-question-answer' ),
		'file_size_error' => sprintf( __( 'File size is bigger then %s MB', 'anspress-question-answer' ), round( ap_opt( 'max_upload_size' ) / ( 1024 * 1024 ), 2 ) ),
		'attached_max' => __( 'You have already attached maximum numbers of allowed attachments', 'anspress-question-answer' ),
		'commented' => __( 'commented', 'anspress-question-answer' ),
		'comment' => __( 'Comment', 'anspress-question-answer' ),
		'cancel' => __( 'Cancel', 'anspress-question-answer' ),
		'update' => __( 'Update', 'anspress-question-answer' ),
		'your_comment' => __( 'Write your comment...', 'anspress-question-answer' ),
		'notifications' => __( 'Notifications', 'anspress-question-answer' ),
		'mark_all_seen' => __( 'Mark all as seen', 'anspress-question-answer' ),
	);

	echo '<script type="text/javascript">';
		echo 'var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '",';
		echo 'ap_nonce 	= "' . wp_create_nonce( 'ap_ajax_nonce' ) . '",';
	  echo 'ap_max_tags = "' . ap_opt( 'max_tags' ) . '";';
	  echo 'apTemplateUrl = "' . ap_get_theme_url( 'js-template', false, false ) . '";';
	  echo 'apQuestionID = "' . get_question_id() . '";';
	  echo 'aplang = ' . wp_json_encode( $aplang ) . ';';
	  echo 'apShowComments = "' . (bool) ap_opt( 'show_comments_default' ) . '";';
	  echo 'disable_q_suggestion = "' . (bool) ap_opt( 'disable_q_suggestion' ) . '";';
	echo '</script>';

	ap_upload_js_init();
}
add_action( 'wp_enqueue_scripts', 'ap_scripts_front', 1 );

/**
 * Register widget positions.
 */
function ap_widgets_positions() {
	register_sidebar(array(
		'name' => __( '(AnsPress) Before', 'anspress-question-answer' ),
		'id' => 'ap-before',
		'before_widget' => '<div id="%1$s" class="ap-widget-pos %2$s">',
		'after_widget' => '</div>',
		'description' => __( 'Widgets in this area will be shown before anspress body.', 'anspress-question-answer' ),
		'before_title' => '<h3 class="ap-widget-title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => __( '(AnsPress) Question List Top', 'anspress-question-answer' ),
		'id' => 'ap-top',
		'before_widget' => '<div id="%1$s" class="ap-widget-pos %2$s">',
		'after_widget' => '</div>',
		'description' => __( 'Widgets in this area will be shown before questions list.', 'anspress-question-answer' ),
		'before_title' => '<h3 class="ap-widget-title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => __( '(AnsPress) Sidebar', 'anspress-question-answer' ),
		'id' => 'ap-sidebar',
		'before_widget' => '<div id="%1$s" class="ap-widget-pos %2$s">',
		'after_widget' => '</div>',
		'description' => __( 'Widgets in this area will be shown in AnsPress sidebar except single question page.', 'anspress-question-answer' ),
		'before_title' => '<h3 class="ap-widget-title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => __( '(AnsPress) Question Sidebar', 'anspress-question-answer' ),
		'id' => 'ap-qsidebar',
		'before_widget' => '<div id="%1$s" class="ap-widget-pos %2$s">',
		'after_widget' => '</div>',
		'description' => __( 'Widgets in this area will be shown in single question page sidebar.', 'anspress-question-answer' ),
		'before_title' => '<h3 class="ap-widget-title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => __( '(AnsPress) Category Page', 'anspress-question-answer' ),
		'id' => 'ap-category',
		'before_widget' => '<div id="%1$s" class="ap-widget-pos %2$s">',
		'after_widget' => '</div>',
		'description' => __( 'Widgets in this area will be shown in category listing page.', 'anspress-question-answer' ),
		'before_title' => '<h3 class="ap-widget-title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => __( '(AnsPress) Tag page', 'anspress-question-answer' ),
		'id' => 'ap-tag',
		'before_widget' => '<div id="%1$s" class="ap-widget-pos %2$s">',
		'after_widget' => '</div>',
		'description' => __( 'Widgets in this area will be shown in tag listing page.', 'anspress-question-answer' ),
		'before_title' => '<h3 class="ap-widget-title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => __( '(AnsPress) Author page', 'anspress-question-answer' ),
		'id' => 'ap-author',
		'before_widget' => '<div id="%1$s" class="ap-widget-pos %2$s">',
		'after_widget' => '</div>',
		'description' => __( 'Widgets in this area will be shown in authors page.', 'anspress-question-answer' ),
		'before_title' => '<h3 class="ap-widget-title">',
		'after_title' => '</h3>',
	));
}
add_action( 'widgets_init', 'ap_widgets_positions' );

