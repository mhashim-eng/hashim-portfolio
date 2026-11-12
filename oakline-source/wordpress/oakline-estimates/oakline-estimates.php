<?php
/**
 * Plugin Name: Oakline Estimates
 * Description: Demo-safe estimate form, private inquiry inbox, rate limiting and WordPress email delivery with bounded retries.
 * Version: 1.0.0
 * Requires at least: 6.6
 * Requires PHP: 8.0
 * Author: Muhammad Hashim
 * License: GPL-2.0-or-later
 */
if (!defined('ABSPATH')) { exit; }
function oe_settings() { return wp_parse_args(get_option('oakline_estimates',array()),array('demo'=>1,'recipient'=>'','privacy_url'=>'','retention'=>30)); }
add_action('init',function () {
    register_post_type('oakline_inquiry',array('label'=>'Estimate inquiries','public'=>false,'publicly_queryable'=>false,'show_ui'=>true,'show_in_rest'=>false,'exclude_from_search'=>true,'supports'=>array('title'),'map_meta_cap'=>false,'capabilities'=>array('edit_post'=>'manage_options','read_post'=>'manage_options','delete_post'=>'manage_options','edit_posts'=>'manage_options','edit_others_posts'=>'manage_options','publish_posts'=>'manage_options','read_private_posts'=>'manage_options','delete_posts'=>'manage_options','delete_private_posts'=>'manage_options','delete_published_posts'=>'manage_options','delete_others_posts'=>'manage_options','edit_private_posts'=>'manage_options','edit_published_posts'=>'manage_options','create_posts'=>'do_not_allow')));
});
add_action('admin_init',function () {
    register_setting('oakline_estimates','oakline_estimates',array('sanitize_callback'=>function($raw){
        if (!is_array($raw)) { return oe_settings(); }
        $recipient=sanitize_email(is_string($raw['recipient']??null)?$raw['recipient']:'');
        $privacy=esc_url_raw(is_string($raw['privacy_url']??null)?$raw['privacy_url']:'');
        $demo=empty($raw['demo'])?0:1;
        if (!$demo&&(!is_email($recipient)||!wp_http_validate_url($privacy))) {add_settings_error('oakline_estimates','missing','A valid approved recipient and public privacy-policy URL are required. Demo mode remains enabled.');$demo=1;}
        return array('demo'=>$demo,'recipient'=>$recipient,'privacy_url'=>$privacy,'retention'=>max(7,min(365,absint($raw['retention']??30))));
    }));
});
add_action('admin_menu',function(){add_options_page('Oakline Estimates','Oakline Estimates','manage_options','oakline-estimates','oe_settings_screen');});
function oe_settings_screen(){
    if(!current_user_can('manage_options')){return;}$s=oe_settings();
    echo '<div class="wrap"><h1>Oakline Estimates</h1><p>Keep demo mode enabled until business details, privacy policy and mail transport are approved. Use a trusted SMTP provider or your host’s authenticated mail service. A successful wp_mail call means accepted for transport, not confirmed inbox delivery.</p><form action="options.php" method="post">';settings_fields('oakline_estimates');
    echo '<table class="form-table"><tr><th>Demo mode</th><td><label><input type="checkbox" name="oakline_estimates[demo]" value="1" '.checked($s['demo'],1,false).'> Do not store or send inquiries</label></td></tr><tr><th>Approved business recipient</th><td><input class="regular-text" type="email" name="oakline_estimates[recipient]" value="'.esc_attr($s['recipient']).'"></td></tr><tr><th>Privacy policy URL</th><td><input class="regular-text" type="url" name="oakline_estimates[privacy_url]" value="'.esc_attr($s['privacy_url']).'"></td></tr><tr><th>Inquiry retention (days)</th><td><input type="number" min="7" max="365" name="oakline_estimates[retention]" value="'.esc_attr($s['retention']).'"><p>Daily cleanup permanently removes expired inquiries. Backups and mailbox copies need their own retention policy.</p></td></tr></table>';submit_button();echo '</form><h2>Delivery acceptance</h2><ol><li>Configure authenticated SMTP with SPF/DKIM for the sending domain.</li><li>Turn demo mode off only on the intended live site.</li><li>Submit a fictional test through the contact page.</li><li>Verify the record in Estimate inquiries and verify actual receipt in the approved mailbox.</li><li>Test a failed transport and inspect the delivery state. Retries stop after three attempts.</li></ol><p>No email has been tested against your business mailbox by this source package.</p></div>';
}
add_shortcode('oakline_estimate_form',function(){
    $s=oe_settings();$form=file_get_contents(__DIR__.'/form.html');
    if(!$s['demo']){
        $form=str_replace('data-demo="true"','data-demo="false" data-endpoint="'.esc_url(rest_url('oakline/v1')).'"',$form);
        $form=str_replace('Demo only. Nothing is emailed or stored. You can download your completed project brief.','Fields marked * are required. Your information is used to respond to this project inquiry.',$form);
        $form=str_replace('I understand this is a fictional demo and no estimate request will be sent.','I agree to be contacted about my project and have read the <a href="'.esc_url($s['privacy_url']).'">privacy policy</a>.',$form);
        $form=str_replace('Preview My Request','Submit Request',$form);
        $form=str_replace('</form>','<div class="honeypot" aria-hidden="true"><label>Leave this field empty<input name="website" tabindex="-1" autocomplete="off"></label></div></form>',$form);
    }
    return $form;
});
function oe_same_origin($request){
    $origin=$request->get_header('origin');$allowed=wp_parse_url(home_url());
    if($origin){$p=wp_parse_url($origin);if(!$p||strtolower($p['host']??'')!==strtolower($allowed['host']??'')||($p['scheme']??'')!==($allowed['scheme']??'')||($p['port']??null)!==($allowed['port']??null)){return new WP_Error('origin','Request origin is not allowed.',array('status'=>403));}}
    return true;
}
function oe_challenge(){
    $stamp=time();$random=wp_generate_password(32,false,false);$payload=$stamp.'.'.$random;
    $r=new WP_REST_Response(array('stamp'=>$stamp,'challenge'=>$random,'signature'=>hash_hmac('sha256',$payload,wp_salt('nonce'))));$r->header('Cache-Control','no-store, private');return $r;
}
function oe_validate($raw){
    if(!is_array($raw)){return new WP_Error('invalid','Invalid request.',array('status'=>400));}
    $data=array();foreach(array('full_name'=>120,'email'=>180,'phone'=>32,'project_type'=>30,'budget'=>40,'start_date'=>10,'description'=>3000,'consent'=>10,'website'=>200) as $key=>$max){$v=$raw[$key]??'';if(!is_string($v)||strlen($v)>$max*4){return new WP_Error('invalid','Invalid '.$key.'.',array('status'=>400));}$data[$key]=trim($key==='description'?sanitize_textarea_field($v):sanitize_text_field($v));if(strlen($data[$key])>$max*4){return new WP_Error('invalid','Field is too long.',array('status'=>400));}}
    if(strlen($data['full_name'])<2||!is_email($data['email'])||strlen($data['description'])<20||$data['consent']!=='yes'){return new WP_Error('invalid','Enter your name, valid email, project description (at least 20 characters) and consent.',array('status'=>400));}
    if($data['phone']&&!preg_match('/^[0-9+().\s-]{7,32}$/',$data['phone'])){return new WP_Error('invalid','Please enter a valid phone number.',array('status'=>400));}
    if(!in_array($data['project_type'],array('kitchen','bathroom','flooring','whole-home'),true)||!in_array($data['budget'],array('Under $10,000','$10,000–$25,000','$25,000–$50,000','$50,000–$100,000','$100,000+','Let’s discuss'),true)){return new WP_Error('invalid','Choose a valid service and budget range.',array('status'=>400));}
    if($data['start_date']){$date=DateTimeImmutable::createFromFormat('!Y-m-d',$data['start_date'],wp_timezone());if(!$date||$date->format('Y-m-d')!==$data['start_date']||$data['start_date']<current_time('Y-m-d')){return new WP_Error('invalid','Choose a valid date today or later.',array('status'=>400));}}
    if($data['website']){return new WP_Error('spam','Unable to accept this request.',array('status'=>400));}unset($data['website']);return $data;
}
function oe_submit($request){
    $s=oe_settings();if($s['demo']||!is_email($s['recipient'])||!$s['privacy_url']){return new WP_Error('demo','This demonstration does not accept real inquiries.',array('status'=>503));}
    if(strlen($request->get_body())>20000){return new WP_Error('large','Request is too large.',array('status'=>413));}
    $raw=$request->get_json_params();$data=oe_validate($raw);if(is_wp_error($data)){return $data;}
    $stamp=$raw['stamp']??0;$challenge=$raw['challenge']??'';$signature=$raw['signature']??'';
    if(!is_int($stamp)||!is_string($challenge)||!preg_match('/^[a-zA-Z0-9]{32}$/',$challenge)||!is_string($signature)||time()-$stamp<3||time()-$stamp>1200||!hash_equals(hash_hmac('sha256',$stamp.'.'.$challenge,wp_salt('nonce')),$signature)){return new WP_Error('expired','Please wait a moment, refresh the form and try again.',array('status'=>403));}
    $ip=$_SERVER['REMOTE_ADDR']??'unknown';$key='oe_rate_'.hash_hmac('sha256',$ip,wp_salt());$emailkey='oe_rate_'.hash_hmac('sha256',strtolower($data['email']),wp_salt());
    if((int)get_transient($key)>=5||(int)get_transient($emailkey)>=3){return new WP_Error('rate','Too many requests. Please try again in an hour.',array('status'=>429));}
    $lock='oe_once_'.hash('sha256',$challenge);if(!add_option($lock,time(),'','no')){return new WP_Error('duplicate','This form has already been submitted. Refresh before trying again.',array('status'=>409));}
    set_transient($key,(int)get_transient($key)+1,HOUR_IN_SECONDS);set_transient($emailkey,(int)get_transient($emailkey)+1,HOUR_IN_SECONDS);
    $reference='OAK-'.strtoupper(substr(wp_generate_uuid4(),0,8));
    $id=wp_insert_post(array('post_type'=>'oakline_inquiry','post_status'=>'private','post_title'=>$reference,'post_content'=>wp_slash(wp_json_encode($data))),true);
    if(is_wp_error($id)){delete_option($lock);return new WP_Error('storage','We could not save your request. Please try again.',array('status'=>503));}
    update_post_meta($id,'_oe_consent_at',gmdate('c'));update_post_meta($id,'_oe_delivery','pending');update_post_meta($id,'_oe_attempts',0);oe_deliver($id);
    $response=new WP_REST_Response(array('message'=>'Your request has been received. Reference: '.$reference.'. This is not a confirmed appointment or a final quotation.','reference'=>$reference),201);$response->header('Cache-Control','no-store, private');return $response;
}
add_action('rest_api_init',function(){register_rest_route('oakline/v1','/challenge',array('methods'=>'GET','callback'=>'oe_challenge','permission_callback'=>'oe_same_origin'));register_rest_route('oakline/v1','/submit',array('methods'=>'POST','callback'=>'oe_submit','permission_callback'=>'oe_same_origin'));});
function oe_deliver($id){
    $post=get_post($id);$s=oe_settings();if(!$post||$post->post_type!=='oakline_inquiry'||$s['demo']||!is_email($s['recipient'])||get_post_meta($id,'_oe_delivery',true)==='accepted'){return;}
    $attempts=(int)get_post_meta($id,'_oe_attempts',true);if($attempts>=3){return;}$data=json_decode($post->post_content,true);if(!is_array($data)){return;}
    $body="New renovation estimate inquiry\nReference: ".$post->post_title."\n\n";foreach($data as $k=>$v){$body.=ucwords(str_replace('_',' ',$k)).': '.$v."\n";}
    $sent=wp_mail($s['recipient'],'Oakline estimate request '.$post->post_title,$body,array('Content-Type: text/plain; charset=UTF-8','Reply-To: '.sanitize_email($data['email'])));
    update_post_meta($id,'_oe_attempts',$attempts+1);update_post_meta($id,'_oe_delivery',$sent?'accepted':'failed');
    if(!$sent&&$attempts<2&&!wp_next_scheduled('oe_retry',array($id))){wp_schedule_single_event(time()+300*($attempts+1),'oe_retry',array($id));}
}
add_action('oe_retry','oe_deliver');
add_action('add_meta_boxes',function(){add_meta_box('oe-details','Inquiry details & delivery','oe_details','oakline_inquiry');});
function oe_details($post){
    if(!current_user_can('manage_options')){return;}$data=json_decode($post->post_content,true);echo '<table class="widefat striped">';foreach((array)$data as $key=>$value){echo '<tr><th>'.esc_html(ucwords(str_replace('_',' ',$key))).'</th><td>'.nl2br(esc_html($value)).'</td></tr>';}echo '</table><p>Transport: <strong>'.esc_html(get_post_meta($post->ID,'_oe_delivery',true)).'</strong> · Attempts: '.esc_html(get_post_meta($post->ID,'_oe_attempts',true)).' · Consent recorded: '.esc_html(get_post_meta($post->ID,'_oe_consent_at',true)).'</p><p>Accepted means the mail transport accepted the message, not confirmed inbox delivery. After fixing mail configuration, use the retry action below.</p><a class="button" href="'.esc_url(wp_nonce_url(admin_url('admin-post.php?action=oe_retry_manual&id='.$post->ID),'oe_retry_'.$post->ID)).'">Retry failed delivery</a>';
}
add_action('admin_post_oe_retry_manual',function(){if(!current_user_can('manage_options')){wp_die('Not authorized.',403);}$id=absint($_GET['id']??0);check_admin_referer('oe_retry_'.$id);$p=get_post($id);if($p&&$p->post_type==='oakline_inquiry'&&get_post_meta($id,'_oe_delivery',true)!=='accepted'){update_post_meta($id,'_oe_attempts',0);oe_deliver($id);}wp_safe_redirect(admin_url('post.php?post='.$id.'&action=edit'));exit;});
function oe_cleanup(){
    $days=oe_settings()['retention'];$ids=get_posts(array('post_type'=>'oakline_inquiry','post_status'=>'any','fields'=>'ids','numberposts'=>100,'date_query'=>array(array('before'=>gmdate('Y-m-d H:i:s',time()-$days*DAY_IN_SECONDS)))));foreach($ids as $id){wp_delete_post($id,true);}
    global $wpdb;$prefix=$wpdb->esc_like('oe_once_').'%';$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name LIKE %s AND CAST(option_value AS UNSIGNED) < %d",$prefix,time()-DAY_IN_SECONDS));
}
add_action('oe_cleanup','oe_cleanup');register_activation_hook(__FILE__,function(){if(!wp_next_scheduled('oe_cleanup')){wp_schedule_event(time()+HOUR_IN_SECONDS,'daily','oe_cleanup');}});register_deactivation_hook(__FILE__,function(){wp_clear_scheduled_hook('oe_cleanup');wp_clear_scheduled_hook('oe_retry');});
