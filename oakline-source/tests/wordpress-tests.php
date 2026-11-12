<?php
require '/wordpress/wp-load.php';
ob_start();
$passed=0;function check($ok,$message){global $passed;if(!$ok){throw new Exception('FAILED: '.$message);}echo 'PASS: '.$message."\n";$passed++;}
wp_set_current_user(1);
check(wp_get_theme()->is_block_theme(),'Theme is a WordPress block theme');
check(count(get_posts(array('post_type'=>'page','numberposts'=>20)))>=5,'Five pages created');
check(get_option('blog_public')==='0'||get_option('blog_public')===0,'Demo indexing disabled');
foreach(array('home','about','services','projects','contact') as $slug){$p=get_page_by_path($slug);check($p&&has_blocks($p->post_content),'Editable blocks: '.$slug);check(strpos(do_blocks($p->post_content),'{{')===false,'Resolved links: '.$slug);}
$home=get_page_by_path('home');$before=$home->post_content;check(is_wp_error(oakline_seed())&&get_post($home->ID)->post_content===$before,'Setup never overwrites existing content');
check(strpos(do_shortcode('[oakline_estimate_form]'),'data-demo="true"')!==false,'Demo form does not send');
$base=array('full_name'=>'Fictional Test','email'=>'test@example.test','phone'=>'+1 512 555 0100','project_type'=>'kitchen','budget'=>'Let’s discuss','start_date'=>'','description'=>'A fictional kitchen project used only for testing.','consent'=>'yes','website'=>'');
check(!is_wp_error(oe_validate($base)),'Valid form input');
foreach(array('email'=>'invalid','consent'=>'no','project_type'=>'injected','budget'=>'fake','start_date'=>'2020-02-31','website'=>'spam','full_name'=>array('invalid'),'description'=>'short') as $key=>$value){$test=$base;$test[$key]=$value;check(is_wp_error(oe_validate($test)),'Reject invalid '.$key);}
$r=new WP_REST_Request('POST','/oakline/v1/submit');$r->set_header('Origin','https://evil.example');check(is_wp_error(oe_same_origin($r)),'Reject cross-origin request');
check(is_wp_error(oe_submit($r)),'Reject live collection in demo mode');
update_option('oakline_estimates',array('demo'=>0,'recipient'=>'owner@example.test','privacy_url'=>'https://example.test/privacy/','retention'=>30));
add_filter('pre_wp_mail',function(){return true;});
function request_for($base,$age=5){$stamp=time()-$age;$token=wp_generate_password(32,false,false);$r=new WP_REST_Request('POST','/oakline/v1/submit');$r->set_header('Content-Type','application/json');$r->set_body(wp_json_encode(array_merge($base,array('stamp'=>$stamp,'challenge'=>$token,'signature'=>hash_hmac('sha256',$stamp.'.'.$token,wp_salt('nonce'))))));return $r;}
check(is_wp_error(oe_submit(request_for($base,1300))),'Reject expired token');
check(is_wp_error(oe_submit(request_for($base,0))),'Reject instant bot submission');
$r=request_for($base);$result=oe_submit($r);check($result instanceof WP_REST_Response&&$result->get_status()===201,'Valid inquiry saved with mocked mail transport');
check(is_wp_error(oe_submit($r)),'Reject duplicate token replay');
$inquiries=get_posts(array('post_type'=>'oakline_inquiry','post_status'=>'private','numberposts'=>10));check(count($inquiries)===1,'Single private inquiry stored');
check(get_post_meta($inquiries[0]->ID,'_oe_delivery',true)==='accepted','Record mocked mail acceptance');
wp_set_current_user(0);check(!current_user_can('read_post',$inquiries[0]->ID),'Anonymous cannot read private inquiry');
wp_set_current_user(1);check(current_user_can('read_post',$inquiries[0]->ID),'Administrator can read inquiry');
remove_all_filters('pre_wp_mail');add_filter('pre_wp_mail',function(){return false;});
$base['email']='other@example.test';$failed=oe_submit(request_for($base));check($failed instanceof WP_REST_Response,'Failed email still retains inquiry');
$records=get_posts(array('post_type'=>'oakline_inquiry','post_status'=>'private','numberposts'=>10));$id=0;foreach($records as $record){if(get_post_meta($record->ID,'_oe_delivery',true)==='failed'){$id=$record->ID;}}
check($id&&wp_next_scheduled('oe_retry',array($id)),'Failed transport schedules bounded retry');oe_deliver($id);oe_deliver($id);oe_deliver($id);check((int)get_post_meta($id,'_oe_attempts',true)===3,'Email retries capped at three');
echo "TOTAL: $passed checks passed. Mail transport mocked; no external messages sent.\n";

file_put_contents('/oakline-tests/runtime-results.txt','WordPress '.get_bloginfo('version').' / PHP '.PHP_VERSION."\n".ob_get_contents());
