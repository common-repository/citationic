<?php
/**
 * Plugin Name: Citationic
 * Description: Simple, lighweight multi-styles citation plugin. Only CSS, no JS required! Help your visitors cite your texts: APA, MLA, Harvard, Vancouver, Chicago, IEEE citation guides.
 * Author:      Evolink
 * Version:     1.0
 */


$cion_styles = [
	"apa"       => '<div id="content-1">{sitename} ({date}) <b>{title}</b>. Retrieved from {permalink}.</div>',
	"mla"       => '<div id="content-2">"<b>{title}.</b>" {sitename} - {date}, {permalink}</div>',
	"harvard"   => '<div id="content-3">{sitename} {publication_date} <b>{title}.</b>, viewed {date},<{permalink}></div>',
	"vancouver" => '<div id="content-4">{sitename} - <b>{title}.</b> [Internet]. [Accessed {date}]. Available from: {permalink}</div>',
	"chicago"   => '<div id="content-5">"<b>{title}.</b>" {sitename} - Accessed {date}. {permalink}</div>',
	"ieee"      => '<div id="content-6">"<b>{title}.</b>" {sitename} [Online]. Available: {permalink}. [Accessed: {date}]</div>',
];

$cion_tabs = [
	"apa" => '<input type="radio" name="tab-btn" id="tab-btn-1" checked><label for="tab-btn-1">APA</label>',
	"mla" => '<input type="radio" name="tab-btn" id="tab-btn-2"><label for="tab-btn-2">MLA</label>',
	"harvard" => '<input type="radio" name="tab-btn" id="tab-btn-3"><label for="tab-btn-3">Harvard</label>',
	"vancouver" => '<input type="radio" name="tab-btn" id="tab-btn-4"><label for="tab-btn-4">Vancouver</label>',
	"chicago" => '<input type="radio" name="tab-btn" id="tab-btn-5"><label for="tab-btn-5">Chicago</label>',
	"ieee" => '<input type="radio" name="tab-btn" id="tab-btn-6"><label for="tab-btn-6">IEEE</label>',
];


function citationic_register_setting() {
    register_setting('citationic_setting', 'citationic_setting');
}

add_action('admin_menu', 'citationic_setting_menu');

function citationic_setting_menu() {
    add_submenu_page( 'tools.php', 'CITATIONIC', 'Citationic', 'manage_options', 'wp-citationic', 'citationic_setting_page' ); 

}

function citationic_setting_page() {
    echo '<div class="wrap">';
    citationic_admin();
    echo '</div>';
}

function citationic_admin() {
echo <<<TXT

<h1>Citationic usage</h1>

<p>Simple, lighweight citation plugin. Only CSS, no JS required!</p>

<p>Insert shortcode <code>[citationic]</code> in your texts.<br/>
To write Citationic in widgets try something like 
<a href='https://wordpress.org/plugins/shortcode-widget/' target='_blank'>Shortcode Widget</a><br/>
or just add <code>add_filter('widget_text', 'do_shortcode');</code> in your functions.php of current theme.</p>

<p>If you want hide/show citationic on specific post types try pludin Widget Context.</p>

<p>You can change list of styles and its order using attribute <b>styles</b>:<br/>
<code>[citationic styles="apa,mla,harvard,vancouver,chicago,ieee"]</code> (here is default output)</p>

TXT;
}

// Registering shortcode [citationic]
add_shortcode('citationic', 'cion_shortcode');

function cion_shortcode( $atts ) {
	global $cion_tabs;
	global $cion_styles;

	if(!function_exists('displayTodaysDate')){
    function displayTodaysDate() {
        return date_i18n(get_option('date_format'));
	}
	}
	
	$edited_tabs = '';
	$edited_styles = '';
	$find_string = array('{author}','{sitename}', '{title}', '{date}', '{publication_date}', '{permalink}');
    $replace_string = array(get_the_author(), get_bloginfo('name'), get_the_title(), displayTodaysDate(), get_the_date(), 
	'<a href="' . get_permalink() . '">' . get_permalink() . '</a>');
	
	$styles_list = ['apa','mla','harvard','vancouver','chicago','ieee'];
	$styles_user = array_intersect(explode(',',$atts['styles']),$styles_list);
	if(empty($styles_user)) $styles_user=$styles_list;
	
	foreach($styles_user as $k)
	{
		$edited_tabs   = $edited_tabs   . str_replace($find_string, $replace_string, $cion_tabs[$k]);
		$edited_styles = $edited_styles . str_replace($find_string, $replace_string, $cion_styles[$k]);
	}
	
    return '<div id="citationic" class="tabs">' . $edited_tabs . $edited_styles . '</div>';
}

// Adding some makeup
add_action('wp_head', 'cion_head');

function cion_head() {
?>
<style>
.tabs{font-size:0;margin-left:auto;margin-right:auto}.tabs>input[type=radio]{display:none}.tabs>div{display:none;border:1px solid #e0e0e0;padding:10px 15px;font-size:16px;overflow:hidden;text-overflow:ellipsis}#tab-btn-1:checked~#content-1,#tab-btn-2:checked~#content-2,#tab-btn-3:checked~#content-3,#tab-btn-4:checked~#content-4,#tab-btn-5:checked~#content-5,#tab-btn-6:checked~#content-6{display:block}.tabs>label{display:inline-block;text-align:center;vertical-align:middle;user-select:none;background-color:#f5f5f5;border:1px solid #e0e0e0;padding:2px 8px;font-size:16px;line-height:1.5;transition:color .15s ease-in-out,background-color .15s ease-in-out;cursor:pointer;position:relative;top:1px}.tabs>label:not(:first-of-type){border-left:none}.tabs>input[type=radio]:checked+label{background-color:#fff;border-bottom:1px solid #fff}
</style>
<?php
}