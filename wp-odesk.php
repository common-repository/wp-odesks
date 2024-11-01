<?php
/*
Plugin Name: WP Odesk Plugin
Plugin URI: http://www.reybornwebservices.com/plugins/wp-odesk-plugin/
Description: This plugin uses Odesk API to display odesk profile, provider affiliate, and job affiliate in your website. This is a good tool in promoting your odesk profile as well as monetizing your website through odes affiliate program.
Version: 0.1
Author: Reyborn Webservices
Author URI: http://www.reybornwebservices.com/
License: GPL2

Copyright 2011 WP Odesk Plugin  (email : reybornwebservices@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
*/


if (isset($_POST['section']) && $_POST['section'] == 'option') {	
	update_option('wp_odesk_link', $_POST['footer_'], ' ', 'yes');
	update_option('wp_odesk_proxies', $_POST['txtProxy'], ' ', 'yes');
	update_option('wp_odesk_redirect', $_POST['txtRedirect'], ' ', 'yes');
}
if (isset($_POST['section']) && $_POST['section'] == 'profile') {
	update_option('wp_odesk_profilekey', $_POST['txtprofileKey'], ' ', 'yes');
}
if (isset($_POST['section']) && $_POST['section'] == 'affiliate') {
	update_option("wp_showonprovider", $_POST['txtshowonprovider'], ' ', 'yes');
	update_option('wp_odesk_cjid', $_POST['txtcjid'], ' ', 'yes');
	update_option('wp_odesk_category', $_POST['cbocategory'], ' ', 'yes');
	update_option('wp_odesk_numresult', $_POST['txtnumresult'], ' ', 'yes');
	update_option('wp_odesk_keyword', $_POST['txtkeyword'], ' ', 'yes');
}

add_action('admin_menu', 'odesk_admin_page');
//add_action('wp_print_scripts', 'add_odesk_scripts');
//add_action('wp_print_styles', 'add_odesk_style');

function odesk_admin_page() {
	add_menu_page('WP-Odesk', 'WP-Odesk', 'manage_options', 'wp-odesk-main', 'wp_odesk_optionpage');	
	add_submenu_page( 'wp-odesk-main', 'Odesk Profile', 'Odesk Profile', 'manage_options', 'wp-odesk-profile', 'wp_odesk_profilepage');
	add_submenu_page( 'wp-odesk-main', 'Odesk Affiliates', 'Odesk Affiliates', 'manage_options', 'wp-odesk-affiliates', 'wp_odesk_affiliatepage');	
}

//display the admin options page
function wp_odesk_optionpage() { ?>
    <div><h2>WP Odesk Option Page</h2>Please Input proxies if you want to use proxy in connecting to odesk api.<br /><br />
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>&updated=true" method="post">
        <table>
        <tr>
          <td valign="top"><p>Proxies:</p></td>
          <td><span style="font-size:11px;">
            <textarea name="txtProxy" id="txtProxy" rows="4" style="width:350px;"><?php echo get_option("wp_odesk_proxies"); ?></textarea>
          <br />
          Separated by comma e.g 190.121.135.178:8080</span></td>
        </tr>
        <tr>
          <td valign="top">Recirect URL:</td>
          <td><input name="txtRedirect" type="text" id="txtRedirect" style="width:350px;" value="<?php echo get_option('wp_odesk_redirect'); ?>"/><br />
          <span style="font-size:11px;">Url to show if odesk fails to load.</span></td>
        </tr>
        <tr><td></td><td><input name="footer_" type="checkbox" value="1" <?php if (get_option("wp_odesk_link") != '') { echo "checked=\"checked\"";} ?>/> Display developer link at the footer?</td></tr>
       <tr><td></td><td><input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" /></td></tr>
       
        </table>
        <input type="hidden" name="section" value="option" />
    </form>
    </div>
<?php
}
function wp_odesk_profilepage() { ?>
<div><h2>Odesk Profile Fetcher Options</h2>
    This plugin requires Odesk.com account. If you do not have one, please signup <a href="https://www.odesk.com/w/signup.php?" target="_blank" rel="nofollow">here</a>.<br />
    Please input your profile key, you can found it at your Odesk public profile page somewhere below your photo at the last part of the permalink.<br />Example: https://www.odesk.com/users/~~xxxxxxxxxxxxxxx, the last part including the tilde is your profile number.<br />
    After saving this options, create a post or page and put this shortcode <strong>[odesk_profile]</strong>.<br /><br />
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>&updated=true" method="post">
        <table>
        <tr>
          <td>Odesk Profile ID:</td><td><input name="txtprofileKey" type="text" id="txtprofileKey" style="width:350px;" value="<?php echo get_option('wp_odesk_profilekey'); ?>"/></td></tr>
       <tr><td></td><td><input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" /></td></tr>
       
        </table>
        <input type="hidden" name="section" value="profile" />
  </form>
    </div>
<?php
}

function wp_odesk_affiliatepage() {
	$meta = odesk_curl_data("http://www.odesk.com/api/profiles/v1/metadata/categories.xml");
	//print_r($meta);
?>
<div>
  <h2>Odesk Affiliate Options</h2>
    This plugin requires Commision Junction Account (Publisher) to earn commision when someone got hired through your listing link.<br />If you do not have one, please signup <a href="http://www.cj.com/get-started-publisher?x" target="_blank" rel="nofollow">here</a>.<br />
    After saving this options, create a post or page and put this shortcode <strong>[odesk_affiliate_job]</strong> for job affiliate and <strong>[odesk_affiliate_provider]</strong> for contractor affiliate.<br /><span style="color:#F00; font-size:18px;">*</span> indicates required field.
    <br /> <br />
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>&updated=true" method="post">
        <table>
        <tr>
          <td>CJ ID <span style="color:#F00;">*</span>:</td><td><input name="txtcjid" type="text" id="txtcjid" style="width:350px;" value="<?php echo get_option('wp_odesk_cjid'); ?>"/></td></tr>
        <tr>
          <td>Keyword:</td>
          <td><input name="txtkeyword" type="text" id="txtkeyword" style="width:350px;" value="<?php echo get_option('wp_odesk_keyword'); ?>"/></td>
        </tr>
        <tr>
          <td>Category:</td>
          <?php $jobcategory = get_option("wp_odesk_category"); ?>
          <td><select class="widefat" name="cbocategory" id="cbocategory">
            <option value="" <?php echo ($jobcategory == "") ? "selected=selected" : ""; ?>></option>
            	<?php				
				//asort($meta);
				foreach ($meta->categories->category as $c) { ?>
            		<option value="<?php echo $c->title; ?>" <?php echo ($jobcategory == $c->title) ? "selected=selected" : ""; ?>><?php echo $c->title; ?></option>
            	<?php		
				}
				?>
          </select></td>
        </tr>
        <tr>
          <td>Number of results:</td>
          <td><input name="txtnumresult" type="text" id="txtnumresult" style="width:100px;" value="<?php echo get_option('wp_odesk_numresult'); ?>"/></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><input name="txtshowonprovider" type="checkbox" value="1" <?php if (get_option("wp_showonprovider") != '') { echo "checked=\"checked\"";} ?> />&nbsp; Show my profile at the top of the provider lists</td>
        </tr>
       <tr><td></td><td><input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" /></td></tr>
       
        </table>
        <input type="hidden" name="section" value="affiliate" />
  </form>
</div>
<?php
}

add_shortcode('odesk_profile', 'odesk_profile_generator');
add_shortcode('odesk_affiliate_job', 'odesk_job_generator');
add_shortcode('odesk_affiliate_provider', 'odesk_provider_generator');

function odesk_provider_generator(){
	$cjid = "3216638";
		
	if (get_option("wp_odesk_cjid") != '') {
		$cjid = get_option("wp_odesk_cjid");
	}
		
	if (get_option("wp_showonprovider") == 1) {	
		//Get profile
		$output = "";
		
		$url = "http://www.odesk.com/api/profiles/v1/providers/".trim(get_option("wp_odesk_profilekey")).".xml";
		$profile = odesk_curl_data($url);
		$p = $profile->profile;
		$output .= "<div style=\"padding-top:10px;\"><div style=\"float:left;width:50px;\"><img src=\"".$p->dev_portrait_50."\" /><br /><span><a href=\"http://www.jdoqocy.com/click-".$cjid."-10718312?url=http://www.odesk.com/users/".$p->ciphertext."?utm_source=cj\" rel=\"nofollow\" target=\"_blank\">Hire Me!</a></span></div><div style=\"float:right; width:90%;\"><strong><a href=\"http://www.jdoqocy.com/click-".$cjid."-10718312?url=http://www.odesk.com/users/".$p->ciphertext."?utm_source=cj\" rel=\"nofollow\" target=\"_blank\">".$p->dev_full_name."</a><br />- </strong>";
		$output .= "<span style=\"font-size:12px;\"><i>".$p->profile_title_full."</i></span><br />";
		$output .= "<span style=\"font-size:12px;\"><strong>$".$p->dev_bill_rate."/hr - Hours: </strong> ".$p->dev_total_hours."<strong> - ".$p->dev_country."</strong> - Last active: ".$p->dev_last_worked."</span><br />";
		$output .= "<span style=\"font-size:12px;\">".strip_tags(substr($p->dev_blurb,0,200))."...</span>";
		$output .= "<br style=\"clear:both;\" />";
		$output .= "</div><div style=\"clear:both;float:none;\"></div></div>";
		$output .= "<div style=\"border-bottom:1px dotted; padding-bottom:10px;\"></div>";
	}
	//Get provider listing
	$baseurl = "https://www.odesk.com/api/profiles/v1/search/providers.xml?";
	if (get_option("wp_odesk_keyword") != ''){
		$param['q']  = get_option("wp_odesk_keyword");
	}
	if (get_option("wp_odesk_category") != ''){
		$param['c1']  = get_option("wp_odesk_category");
	}
	$param['page'] = "0;10";
	if (get_option("wp_odesk_numresult") != ''){
		$param['page']  = "0;".get_option("wp_odesk_numresult");
	}
	
	$param['pt'] = "Individual";
	$param['rdy'] = "1";
	
	$parameter = http_build_query($param);
	$url = $baseurl.$parameter;
	//return urldecode($url);
	$providers = odesk_curl_data($url);
	
	if (!$providers) {
		return "Error retrieving provider list.<br /><br />Kindly <strong>refresh</strong> this page";
	}else{
		$cnt = 1;
		foreach ($providers->providers->provider as $job) {		
			$output .= "<div style=\"padding-top:10px;\"><div style=\"float:left;width:50px;\"><img src=\"".$job->dev_portrait_50."\" /><br /><span><a href=\"http://www.jdoqocy.com/click-".$cjid."-10718312?url=http://www.odesk.com/users/".$job->ciphertext."?utm_source=cj\" rel=\"nofollow\" target=\"_blank\">Hire Me!</a></span></div><div style=\"float:right; width:90%;\"><strong><a href=\"http://www.jdoqocy.com/click-".$cjid."-10718312?url=http://www.odesk.com/users/".$job->ciphertext."?utm_source=cj\" rel=\"nofollow\" target=\"_blank\">".$job->contact_name."</a><br />- </strong>";
			$output .= "<span style=\"font-size:12px;\"><i>".$job->ui_profile_title."</i></span><br />";
			$output .= "<span style=\"font-size:12px;\"><strong>$".$job->dev_bill_rate."/hr - Hours: </strong> ".$job->dev_total_hours."<strong> - ".$job->dev_country."</strong> - Last active: ".$job->dev_last_activity."</span><br />";
			$output .= "<span style=\"font-size:12px;\">".strip_tags(substr($job->dev_blurb,0,200))."...</span>";
			$output .= "<br style=\"clear:both;\" />";
			$output .= "</div><div style=\"clear:both;float:none;\"></div></div>";
			$output .= "<div style=\"border-bottom:1px dotted; padding-bottom:10px;\"></div>";
			$cnt++;
		}
			if (get_option('wp_odesk_link') == 1) {
			$output .= "<div style=\"text-align:right; font-size:10px;\"><i><a target\"_blank\" href=\"http://www.reygcalantaol.com/wp-odesk-plugin.html\">WP Odesk Plugin</a> by Rey G. Calanta-ol</i></div>";
			}else{
			$output .= "<div style=\"text-align:right; font-size:10px;\"><i>WP Odesk Plugin by Rey G. Calanta-ol</i></div>";				
			}
			
			return $output;
		
	}
	
}

function odesk_job_generator(){
	$baseurl = "https://www.odesk.com/api/profiles/v1/search/jobs.xml?";
	if (get_option("wp_odesk_keyword") != ''){
		$param['q']  = get_option("wp_odesk_keyword");
	}
	if (get_option("wp_odesk_category") != ''){
		$param['c1']  = get_option("wp_odesk_category");
	}
	$param['page'] = "0;10";
	if (get_option("wp_odesk_numresult") != ''){
		$param['page']  = "0;".get_option("wp_odesk_numresult");
	}
	
	$parameter = http_build_query($param);
	$url = $baseurl.$parameter;
	//return urldecode($url);
	$jobs = odesk_curl_data($url);
	
	if (!$jobs) {
		return "Error retrieving job list.<br /><br />Kindly <strong>refresh</strong> this page";
	}else{

		$cjid = "3216638";
		
		if (get_option("wp_odesk_cjid") != '') {
			$cjid = get_option("wp_odesk_cjid");
		}
		
		$output = "";
		$cnt = 1;
		foreach ($jobs->jobs->job as $job) {
			$output .= "<div style=\"padding-top:10px;\"><strong><a href=\"http://www.jdoqocy.com/click-".$cjid."-10718312?url=https://www.odesk.com/jobs/".$job->ciphertext."?source=rss\" rel=\"nofollow\" target=\"_blank\">".$job->op_title."</a><br />".$job->job_type." - </strong>";
			$output .= "<span style=\"font-size:11px;\"><i>";
			if ($job->job_type == 'Hourly') {
				$output .= "Est. Time: ". $job->op_est_duration." week(s),";
			}else{
				$output .= "Est. Budget: $".number_format((double)$job->amount,2);
			}
			$output .= " - Posted: ".$job->date_posted." " .$job->op_time_posted;
			$output .= "</i></span><br />";
			$output .= "<span>".strip_tags(substr($job->op_description,0,200))."...</span><br />";
			$output .= "<span style=\"font-size:11px;\"><i>Skills: ";
			if ($job->op_required_skills == '') {
				$output .= "None";
			}else{
				$output .= trim($job->op_required_skills,",");
			}
			$output .= " - Category: ".$job->job_category_level_one.">".$job->job_category_level_two."</i></span><br style=\"clear:both;\" />";
			$output .= "<span>[<strong><a href=\"http://www.jdoqocy.com/click-".$cjid."-10718312?url=https://www.odesk.com/jobs/".$job->ciphertext."?source=rss rel=\"nofollow\" target=\"_blank\">Apply Now!</a></strong>]</span>";
			$output .= "<div style=\"clear:both;\"></div>";
			$output .= "</div>";
			$output .= "<div style=\"border-bottom:1px dotted; padding-bottom:10px;\"></div>";
			$cnt++;
		}
			if (get_option('wp_odesk_link') == 1) {
			$output .= "<div style=\"text-align:right; font-size:10px;\"><i><a target\"_blank\" href=\"http://www.reygcalantaol.com/wp-odesk-plugin.html\">WP Odesk Plugin</a> by Rey G. Calanta-ol</i></div>";
			}else{
			$output .= "<div style=\"text-align:right; font-size:10px;\"><i>WP Odesk Plugin by Rey G. Calanta-ol</i></div>";			
			}
			
			return $output;
		
	}
	
}

function odesk_profile_generator() {
	$url = "http://www.odesk.com/api/profiles/v1/providers/".trim(get_option("wp_odesk_profilekey")).".xml";
	$profile = odesk_curl_data($url);
	$output = "";
	//echo $url;
	//print_r($profile);
	//exit;
	if (!$profile) {
		$output = "Error retreiving Odesk profile.<br /><br />Kindly <strong>refresh</strong> this page";
		if (trim(get_option("odesk_profile_redirect")) != "") {
			$output .= "<script type='text/javascript'>location.href='".get_option("wp_odesk_redirect")."'</script>";
		}
		//return $output;
	}else{
		$output .= getProfileHeader($profile->profile);
		$output .= "<div class=\"clear_both\"></div>"; //begin tabber
		$output .= "<div class=\"tabber\">"; //begin tabber
		$output .= "<div class=\"tabbertab\">"; //First tab
	  	$output .= "<h2> Overview </h2>"; //Title
		$output .= getOverview($profile->profile); //Content
     	$output .= "</div>"; //End Tab
		
		$output .= "<div class=\"tabbertab\">"; //First tab
	  	$output .= "<h2>Resume</h2>"; //Title
	  	$output .= getSkills($profile->profile->skills->skill); //Content
		$output .= getCertification($profile->profile->certification->certificate); //Content
		$output .= getEmployment($profile->profile->experiences->experience); //Content
		$output .= getOther($profile->profile->experiences->oth_experience); //Content
		$output .= getEducation($profile->profile->education->institution); //Content		
     	$output .= "</div>"; //End Tab
		
		$output .= "<div class=\"tabbertab\">"; //First tab
	  	$output .= "<h2>Work History and Feedback (".$profile->profile->assignments_count.")</h2>"; //Title
		$output .= getWorkHistory($profile->profile->assignments->hr->job);
		$output .= getWorkHistoryFP($profile->profile->assignments->fp->job);		
		$output .= "</div>"; //End Tab

		$output .= "<div class=\"tabbertab\">"; //First tab
	  	$output .= "<h2>Tests (".$profile->profile->tsexams_count.")</h2>"; //Title
	  	$output .= getTest($profile->profile->tsexams->tsexam); //Content
     	$output .= "</div>"; //End Tab
		
		$output .= "<div class=\"tabbertab\">"; //First tab
	  	$output .= "<h2>Portfolio (".$profile->profile->dev_portfolio_items_count.")</h2>"; //Title
	  	$output .= getPortfolio($profile->profile->portfolio_items->portfolio_item); //Content
     	$output .= "</div>"; //End Tab		
		
		$output .= "</div>";	//End tabber	
		$output .= "<div style=\"text-align:right; font-size:10px;\">";
		if (get_option("odesk_profile_link") != '') {
		$output .= "<i><a href=\"http://www.reygcalantaol.com/wp-odesk-plugin.html\">WP Odesk Plugin</a> by <a href=\"http://www.reygcalantaol.com\">Rey G. Calanta-ol</a></i>";
		}else{
		$output .= "<i>WP Odesk Plugin  by  Rey G. Calanta-ol</i>";
		}
		
		$output .= "</div><div class=\"clear_both\"></div>";	//Footer			
	}
	return $output;
}


function odesk_curl_data($url) {
	$proxy = "";
	if (trim(get_option("wp_odesk_proxies")) != '') {
		$proxies = explode(",",get_option("wp_odesk_proxies"));
		$rand = rand(0,count($proxies)-1);
		$proxy = trim($proxies[$rand]);
	}
	//return $proxy;
	$referer = "http://www.odesk.com";
	$agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.8) Gecko/2009032609 Firefox/3.0.8";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	if (trim($proxy) != '') {
		curl_setopt($ch, CURLOPT_PROXY, $proxy);
	}
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_REFERER, $referer);
	curl_setopt($ch, CURLOPT_USERAGENT, $agent);
	$data = curl_exec($ch);

	$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);	
	$error = curl_error($ch);
	curl_close($ch);
	
	if ($status >= 200 && $status < 300) {	
		$doc = new SimpleXmlElement($data, LIBXML_NOCDATA);
		//print_r($doc);
		return $doc;
	}else{
		return false;
		//return $error;
	}
}

function getOverview($p) {
	//print_r($p);
	$ready = ($p->dev_is_ready == 1) ? 'Yes' : 'No';
	$overview .= "<div class=\"odesk_overview\">";	
	$overview .= "<span>".$p->dev_blurb."</span><br /><br />";
	$overview .= "<div class=\"odesk_overview_list\"><ul>";
	$overview .= "<li><span class=\"caption\">Total Hours: </span><span class=\"value\">".$p->dev_total_hours."</span></li>";
	$overview .= "<li><span class=\"caption\">Total Contracts: </span><span class=\"value\">".$p->dev_billed_assignments."</span></li>";
	$overview .= "<li><span class=\"caption\">Location: </span><span class=\"value\">".$p->dev_location."</span></li>";
	$overview .= "<li><span class=\"caption\">English Skills: </span><span class=\"value\">".$p->dev_eng_skill."</span></li>";
	$overview .= "<li><span class=\"caption\">Member Since: </span><span class=\"value\">".$p->dev_member_since."</span></li>";
	$overview .= "<li><span class=\"caption\">Last Worked: </span><span class=\"value\">".$p->dev_last_worked."</span></li>";
	$overview .= "<li><span class=\"caption\">Odesk Ready: </span><span class=\"value\">".$ready."</span></li>";
	$overview .= "</ul></div>";
	$overview .= "<div class=\"clear_both\"></div>";
	$overview .= "</div>";
	
	return $overview;
}

function getTest($profile) {
	
	$work = "<div>";
	$work .= "<table class=\"odesk_table\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">";
	$work .= "<tr>";
	$work .= "<td colspan=\"5\"><span><strong>oDesk Tests Taken</strong></span></td>";
	$work .= "</tr>";	
	$work .= "<tr>";
	$work .= "<th width=\"45%\">Name of Test</th>";
	$work .= "<th width=\"10%\">Score</th>";
	$work .= "<th width=\"15%\">Percentile</th>";
	$work .= "<th width=\"20%\">Date Taken</th>";
	$work .= "<th width=\"10%\">Duration</th>";
	$work .= "</tr>";
	
	foreach ($profile as $hr) {

	$work .= "<tr>";
	$work .= "<td>".$hr->ts_name."</td>";
	$work .= "<td>".$hr->ts_score."</td>";
	$work .= "<td>".$hr->ts_percentile."</td>";
	$work .= "<td>".$hr->ts_when."</td>";
	$work .= "<td>".$hr->ts_duration." min</td>";	
	$work .= "</tr>";
	
	}
	
	$work .= "</table></div>";		
	
	return $work;
	
}


function getPortfolio($folio) {
	
	$portfolio = "<div>";
	$portfolio .= "<table class=\"odesk_table\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">";
	$portfolio .= "<tr>";
	$portfolio .= "<td colspan=\"2\"><span><strong>Portfolio</strong></span></td>";
	$portfolio .= "</tr>";	
	
	foreach ($folio as $hr) {
		$date = (int)$hr->pi_completed;
		$portfolio .= "<tr>";
		$portfolio .= "<td valign=\"top\"><img src=\"".$hr->pi_thumbnail."\" alt=\"\" /></td>";
		$portfolio .= "<td><ul>";
		$portfolio .= "<li><strong>Project Title:</strong> ".$hr->pi_title."</li>";
		$portfolio .= "<li><strong>Completed:</strong> ".date('M d, Y',$date)."</li>";
		$portfolio .= "<li><strong>Category:</strong> ".$hr->pi_category->pi_category_level1.">".$hr->pi_category->pi_category_level2."</li>";
		$portfolio .= "<li><strong>URL:</strong> <a href=\"".$hr->pi_url."\" rel=\"nofollow\" target=\"_blank\">".$hr->pi_url."</a></li>";
		$portfolio .= "<li><strong>Description:</strong> ".$hr->pi_description."</li>";
		$portfolio .= "</ul></td>";
		$portfolio .= "</tr>";	
	}
	
	$portfolio .= "</table></div>";		
	
	return $portfolio;
	
}


function getWorkHistory($profile) {
	
	$work = "<div>";
	$work .= "<table class=\"odesk_table\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
	$work .= "<tr>";
	$work .= "<td colspan=\"5\"><span><strong>Hourly Job History</strong></span></td>";
	$work .= "</tr>";	
	$work .= "<tr>";
	$work .= "<th width=\"13%\">Emp ID</th>";
	$work .= "<th width=\"15%\">From/To</th>";
	$work .= "<th width=\"20%\">Job Title</th>";
	$work .= "<th width=\"15%\">Paid</th>";
	$work .= "<th width=\"37%\">Feedback</th>";
	$work .= "</tr>";

	foreach ($profile as $hr) {
	if ($hr->as_client != 318878) {	
	$charge = number_format((double)$hr->as_total_charge,2);
	$feedback = ($hr->as_status == 'Closed') ? $hr->feedback->comment : '<i>Job in progress</i>';
	$work .= "<tr>";
	$work .= "<td>".$hr->as_client."</td>";
	$work .= "<td>".$hr->as_from."-".$hr->as_to."</td>";
	$work .= "<td>".$hr->as_opening_title."</td>";
	$work .= "<td>$".$charge. " (".$hr->as_total_hours." hrs @ ".$hr->as_rate."/hr)</td>";
	$work .= "<td>".$feedback."</td>";	
	$work .= "</tr>";
	}
	}
	
	$work .= "</table>";		
	
	return $work;
	
}

function getWorkHistoryFP($profile) {
	
	$work = "<table class=\"odesk_table\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
	$work .= "<tr>";
	$work .= "<td colspan=\"5\"><span><strong>Fixed-Price Job History</strong></span></td>";
	$work .= "</tr>";	
	$work .= "<tr>";
	$work .= "<th width=\"13%\">Emp ID</th>";
	$work .= "<th width=\"15%\">From/To</th>";
	$work .= "<th width=\"20%\">Job Title</th>";
	$work .= "<th width=\"15%\">Paid</th>";
	$work .= "<th width=\"37%\">Feedback</th>";
	$work .= "</tr>";
	
	foreach ($profile as $hr) {
	$charge = number_format((double)$hr->as_total_charge,2);
	$feedback = ($hr->as_status == 'Closed') ? $hr->feedback->comment : '<i>Job in progress</i>';
	$work .= "<tr>";
	$work .= "<td>".$hr->as_client."</td>";
	$work .= "<td>".$hr->as_from."-".$hr->as_to."</td>";
	$work .= "<td>".$hr->as_opening_title."</td>";
	$work .= "<td>$".$charge."</td>";
	$work .= "<td>".$feedback."</td>";	
	$work .= "</tr>";
	
	}
	
	$work .= "</table></div>";		
	
	return $work;
	
}

function getSkills($s) {
	
	$skills = "<div>";
	$skills .= "<table class=\"odesk_table\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">";
	$skills .= "<tr>";
	$skills .= "<td colspan=\"3\"><span><strong>Skills</strong></span></td>";
	$skills .= "</tr>";	
	$skills .= "<tr>";
	$skills .= "<th width=\"15%\">Skill</th>";
	$skills .= "<th width=\"15%\">Level</th>";
	$skills .= "<th width=\"70%\">Description</th>";
	$skills .= "</tr>";
	
	foreach ($s as $skill) {
		
	$skills .= "<tr>";
	$skills .= "<td>".$skill->skl_name."</td>";
	$skills .= "<td>".$skill->skl_level."</td>";
	$skills .= "<td>".$skill->skl_description."</td>";	
	$skills .= "</tr>";
	
	}
	
	$skills .= "</table>";		
	
	return $skills;
	
}

function getCertification($cer) {
	
	$certificate .= "<table class=\"odesk_table\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">";
	$certificate .= "<tr>";
	$certificate .= "<td colspan=\"4\"><span><strong>Certification</strong></span></td>";
	$certificate .= "</tr>";	
	$certificate .= "<tr>";
	$certificate .= "<th width=\"10%\">Date</th>";
	$certificate .= "<th width=\"25%\">Name</th>";
	$certificate .= "<th width=\"25%\">Organization</th>";
	$certificate .= "<th width=\"40%\">Description</th>";
	$certificate .= "</tr>";
	
	foreach ($cer as $c) {
		
	$certificate .= "<tr>";
	$certificate .= "<td>".$c->cer_earned."</td>";
	$certificate .= "<td>".$c->cer_name."</td>";
	$certificate .= "<td>".$c->cer_organisation."</td>";
	$certificate .= "<td>".$c->cer_comment."</td>";
	$certificate .= "</tr>";
	
	}
	
	$certificate .= "</table>";		
	
	return $certificate;
	
}


function getEmployment($profile) {
	
	$employ .= "<table class=\"odesk_table\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">";
	$employ .= "<tr>";
	$employ .= "<td colspan=\"5\"><span><strong>Employment History</strong></span></td>";
	$employ .= "</tr>";	
	$employ .= "<tr>";
	$employ .= "<th width=\"10%\">From</th>";
	$employ .= "<th width=\"10%\">To</th>";
	$employ .= "<th width=\"20%\">Company</th>";
	$employ .= "<th width=\"30%\">Title/Role</th>";
	$employ .= "<th width=\"30%\">Description</th>";
	$employ .= "</tr>";
	
	foreach ($profile as $c) {
		
	$employ .= "<tr>";
	$employ .= "<td>".$c->exp_from."</td>";
	$employ .= "<td>".$c->exp_to."</td>";
	$employ .= "<td>".$c->exp_company."</td>";
	$employ .= "<td>".$c->exp_title."</td>";
	$employ .= "<td>".$c->exp_comment."</td>";
	$employ .= "</tr>";
	
	}
	
	$employ .= "</table>";		
	
	return $employ;
	
}


function getOther($o) {
	
	$other .= "<table class=\"odesk_table\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">";
	$other .= "<tr>";
	$other .= "<td colspan=\"2\"><span><strong>Other Experience</strong></span></td>";
	$other .= "</tr>";	
	
	foreach ($o as $c) {
		
	$other .= "<tr>";
	$other .= "<td>".$c->exp_subject."</td>";
	$other .= "<td>".$c->exp_description."</td>";
	$other .= "</tr>";
	
	}
	
	$other .= "</table>";		
	
	return $other;	
}

function getEducation($profile) {
	
	$education .= "<table class=\"odesk_table\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">";
	$education .= "<tr>";
	$education .= "<td colspan=\"6\"><span><strong>Education</strong></span></td>";
	$education .= "</tr>";
	$education .= "<tr>";
	$education .= "<th width=\"10%\">From</th>";
	$education .= "<th width=\"10%\">To</th>";
	$education .= "<th width=\"15%\">School</th>";
	$education .= "<th width=\"15%\">Degree</th>";
	$education .= "<th width=\"20%\">Major</th>";
	$education .= "<th width=\"30%\">Description</th>";
	$education .= "</tr>";	
	
	foreach ($profile as $c) {
		
	$education .= "<tr>";
	$education .= "<td>".$c->ed_from."</td>";
	$education .= "<td>".$c->ed_to."</td>";
	$education .= "<td>".$c->ed_school."</td>";
	$education .= "<td>".$c->ed_degree."</td>";
	$education .= "<td>".$c->ed_area."</td>";
	$education .= "<td>".$c->ed_comment."</td>";	
	$education .= "</tr>";
	
	}
	
	$education .= "</table></div>";		
	
	return $education;	
}

function getProfileHeader($profile) {
	$header = "<div class=\"odesk_header\">";
	//http://www.jdoqocy.com/click-3216638-10718312?url=http://www.odesk.com/users/~~d318b7972be0ed3c?utm_source=cj
	$header .= "<div class=\"img\"><img src=\"".str_replace("&","&amp;",$profile->dev_portrait)."\" alt=\"\" title=\"".$profile->dev_short_name."\" /><br />";
	$header .= "<a target=\"_blank\" href=\"http://www.jdoqocy.com/click-3216638-10718312?url=http://www.odesk.com/users/".urlencode($profile->ciphertext)."?utm_source=cj\" title=\"".$profile->dev_short_name."\">";
	$header .= "<img src=\"".getURL().'/hire_me_button.gif'."\" border=\"0\" width=\"106\" alt=\"\" title=\"".$profile->dev_short_name."\" /></a></div>";
	$header .= "<div class=\"header_profilename\"><strong><a target=\"_blank\" href=\"http://www.jdoqocy.com/click-3216638-10718312?url=http://www.odesk.com/users/".$profile->ciphertext."?utm_source=cj\">".$profile->dev_short_name."</a></strong>";
	$header .= "<div class=\"border_line\"></div>";
	$header .= "<span>".$profile->profile_title_full."</span><br />";
	$header .= "<span>Current hourly rate: $<strong>".$profile->dev_bill_rate."/hr</strong></span><br />";
	$header .= "<span>Member since ".$profile->dev_member_since."</span>";
	$header .= "</div><div style=\"float:none;clear:both;\"></div>";
	$header .= "</div>";
	
	return $header;
}


function add_odesk_scripts() {
	if (is_singular()){
    wp_enqueue_script('odesk_tabber', getURL().'tabber-minimized.js');
	}
}
function add_odesk_style() {
	if (is_singular()){
    wp_enqueue_style('odesk_tabber', getURL().'tabber.css');
	}
}
function getURL() {
	return WP_CONTENT_URL.'/plugins/'.basename(dirname(__FILE__)) . '/';
}

?>
