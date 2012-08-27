<?php
require('../../../wp-blog-header.php');
header("HTTP/1.1 200 OK");
global $wpdb;
$main_table_name = $wpdb->prefix.'lddbusinessdirectory';
$cat_table_name = $wpdb->prefix.'lddbusinessdirectory_cats';
$doc_table_name = $wpdb->prefix.'lddbusinessdirectory_docs';


global $lddbd_state_dropdown;
$lddbd_state_dropdown= "<select id='lddbd_address_state' name='lddbd_address_state'>
						<option value='AK'>AK</option>
						<option value='AL'>AL</option>
						<option value='AR'>AR</option>
						<option value='AZ'>AZ</option>
						<option value='CA'>CA</option>
						<option value='CO'>CO</option>
						<option value='CT'>CT</option>
						<option value='DE'>DE</option>
						<option value='FL'>FL</option>
						<option value='GA'>GA</option>
						<option value='HI'>HI</option>
						<option value='IA'>IA</option>
						<option value='ID'>ID</option>
						<option value='IL'>IL</option>
						<option value='IN'>IN</option>
						<option value='KS'>KS</option>
						<option value='KY'>KY</option>
						<option value='LA'>LA</option>
						<option value='MA'>MA</option>
						<option value='MD'>MD</option>
						<option value='ME'>ME</option>
						<option value='MI'>MI</option>
						<option value='MN'>MN</option>
						<option value='MO'>MO</option>
						<option value='MS'>MS</option>
						<option value='MT'>MT</option>
						<option value='NC'>NC</option>
						<option value='ND'>ND</option>
						<option value='NE'>NE</option>
						<option value='NH'>NH</option>
						<option value='NJ'>NJ</option>
						<option value='NM'>NM</option>
						<option value='NV'>NV</option>
						<option value='NY'>NY</option>
						<option value='OH'>OH</option>
						<option value='OK'>OK</option>
						<option value='OR'>OR</option>
						<option value='PA'>PA</option>
						<option value='RI'>RI</option>
						<option value='SC'>SC</option>
						<option value='SD'>SD</option>
						<option value='TN'>TN</option>
						<option value='TX'>TX</option>
						<option value='UT'>UT</option>
						<option value='VA'>VA</option>
						<option value='VT'>VT</option>
						<option value='WA'>WA</option>
						<option value='WI'>WI</option>
						<option value='WV'>WV</option>
						<option value='WY'>WY</option>
					</select>";


$action = $_POST['action'];


if($action == 'approve'){
	global $main_table_name, $doc_table_name, $cat_table_name;
	$id = $_POST['id'];
	$wpdb->update(
		$main_table_name,
		array(
			'approved'=>'true'
		),
		array('id'=>$id),
		array('%s'),
		array('%d')
	);
}
else if($action == 'revoke'){
	global $main_table_name, $doc_table_name, $cat_table_name;
	$id = $_POST['id'];
	$wpdb->update(
		$main_table_name,
		array(
			'approved'=>'false'
		),
		array('id'=>$id),
		array('%s'),
		array('%d')
	);
	
}
else if($action == 'delete'){
	global $main_table_name, $doc_table_name, $cat_table_name;
	$id = $_POST['id'];
	$wpdb->query(
		"
		DELETE FROM $main_table_name
		WHERE id = $id
		"
	);
}
else if($action == 'add'){
	global $main_table_name, $doc_table_name, $cat_table_name;
	$options = get_option('lddbd_options');
	$section_array = unserialize($options['information_sections']);
	$save_additional_sections = array();
	foreach($section_array as $section){
		$name = $section['name'];
		$value = $_POST[$name];
		$save_additional_sections[$name]=$value;
	}

	$name = $_POST['name'];
	$description = $_POST['description'];
	$address_street = $_POST['address_street'];
	$address_city = $_POST['address_city'];
	$address_state = $_POST['lddbd_address_state'];
	$address_zip = $_POST['address_zip'];
	$categories = $_POST['categories'];
	$phone = $_POST['phone'];
	$fax = $_POST['fax'];
	$email = $_POST['email'];
	$contact = $_POST['contact'];
	$url = $_POST['url'];
	$facebook = $_POST['facebook'];
	$twitter = $_POST['twitter'];
	$linkedin = $_POST['linkedin'];
	$promo = $_POST['promo'];
		if($promo=='true'){$promo='true';}
		else{$promo='false';}
	$promoDescription = $_POST['promo_description'];
	$logo = $_FILES['logo'];
	$login = $_POST['login'];
	$password = $_POST['password'];
	$approved = $_POST['approved'];
		if($approved=='true'){$approved='true';}
		else{$approved='false';}
	
	$allowedExtensions = array('jpg', 'jpeg', 'gif', 'png', 'xls', 'xslx', 'doc', 'docx', 'pdf');
	preg_match('/\.('.implode($allowedExtensions, '|').')$/', $_FILES['logo']['name'], $fileExt);
	$logo_path = 'logos/'.$login.'_logo.'.$fileExt[1];
	while (file_exists($logo_path)) {
		$modifier = rand(0, 1000);
		$logo_path = 'logos/'.$login.'_logo'.$modifier.'.'.$fileExt[1];
	}
	
	if(move_uploaded_file($_FILES['logo']['tmp_name'], $logo_path)) {
    	//echo 'file uploaded';
   	}
   
	
	//$wpdb->show_errors();
	
	$row_added = $wpdb->insert(
		$main_table_name,
		array(
			'createDate' => current_time('mysql'),
			'name' => $name,
			'description' => $description,
			'address_street' => $address_street,
			'address_city' => $address_city,
			'address_state' => $address_state,
			'address_zip' => $address_zip,
			'categories' => $categories,
			'phone' => $phone,
			'fax' => $fax,
			'email' => $email,
			'contact' => $contact,
			'url' => $url,
			'facebook' => $facebook,
			'twitter' => $twitter,
			'linkedin' => $linkedin,
			'promo' => $promo,
			'promoDescription' => $promoDescription,
			'logo' => $logo_path,
			'login' => $login,
			'password' => $password,
			'approved' => $approved,
			'other_info' => serialize($save_additional_sections)
		)
	);
	
	header('Location: '.get_bloginfo('url').'/wp-admin/admin.php?page=business_directory');
	//echo $wpdb->insert_id;
}
else if($action == 'edit'){
	global $main_table_name, $doc_table_name, $cat_table_name;
	$options = get_option('lddbd_options');
	$section_array = unserialize($options['information_sections']);
	$save_additional_sections = array();
	foreach($section_array as $section){
		$name = $section['name'];
		$value = $_POST[$name];
		$save_additional_sections[$name]=$value;
	}

	$update_array = array();
	if(!empty($_POST['name'])){$update_array['name'] = $_POST['name'];}
	if(!empty($_POST['description'])){$update_array['description'] = $_POST['description'];}
	if(!empty($_POST['address_street'])){$update_array['address_street'] = $_POST['address_street'];}
	if(!empty($_POST['address_city'])){$update_array['address_city'] = $_POST['address_city'];}
	if(!empty($_POST['lddbd_address_state'])){$update_array['address_state'] = $_POST['lddbd_address_state'];}
	if(!empty($_POST['address_zip'])){$update_array['address_zip'] = $_POST['address_zip'];}
	if(!empty($_POST['phone'])){$update_array['phone'] = $_POST['phone'];}
	if(!empty($_POST['fax'])){$update_array['fax'] = $_POST['fax'];}
	if(!empty($_POST['email'])){$update_array['email'] = $_POST['email'];}
	if(!empty($_POST['contact'])){$update_array['contact'] = $_POST['contact'];}
	if(!empty($_POST['url'])){$update_array['url'] = $_POST['url'];}
	if(!empty($_POST['facebook'])){$update_array['facebook'] = $_POST['facebook'];}
	if(!empty($_POST['twitter'])){$update_array['twitter'] = $_POST['twitter'];}
	if(!empty($_POST['linkedin'])){$update_array['linkedin'] = $_POST['linkedin'];}
	if(isset($_POST['promo']) && $_POST['promo']=='true'){
		$update_array['promo'] ='true';
	}
	else{$update_array['promo'] = 'false';}
	if(!empty($_POST['promo_description'])){$update_array['promoDescription'] = $_POST['promo_description'];}
	if(!empty($_POST['current_logo'])){$update_array['logo'] = $_POST['current_logo'];}
	if(!empty($_POST['login'])){$update_array['login'] = $_POST['login'];}
	if(!empty($_POST['password'])){$update_array['password'] = $_POST['password'];}
	if(!empty($_POST['approved'])){
		if($_POST['approved']=='true'){$update_array['approved']='true';}
		else{$update_array['approved']='false';}
	}
	if(!empty($save_additional_sections)){$update_array['other_info'] = serialize($save_additional_sections);}
	if(!empty($_POST['categories'])){$update_array['categories'] = $_POST['categories'];}
	
	
	
	if(!empty($_FILES['logo']['name'])){
		$allowedExtensions = array('jpg', 'jpeg', 'gif', 'png', 'xls', 'xslx', 'doc', 'docx', 'pdf');
		preg_match('/\.('.implode($allowedExtensions, '|').')$/', $_FILES['logo']['name'], $fileExt);
		$logo_path = 'logos/'.$_POST['login'].'_logo.'.$fileExt[1];
		while (file_exists($logo_path)) {
			$modifier = rand(0, 1000);
			$logo_path = 'logos/'.$_POST['login'].'_logo'.$modifier.'.'.$fileExt[1];
		}
		
		if(move_uploaded_file($_FILES['logo']['tmp_name'], $logo_path)) {
	    	$update_array['logo'] = $logo_path;
	   	}
	}
	
	for($i=1; $i<6; $i++){
		if(!empty($_FILES['file'.$i]['name'])){
			$allowedExtensions = array('jpg', 'jpeg', 'pdf', 'xls', 'xslx', 'doc', 'docx');
			preg_match('/\.('.implode($allowedExtensions, '|').')$/', $_FILES['file'.$i]['name'], $fileExt);
			$file_path = 'files/'.$_POST['login'].'_'.$i.'.'.$fileExt[1];
			while (file_exists($logo_path)) {
				$modifier = rand(0, 1000);
				$file_path = 'files/'.$_POST['login'].'_'.$i.'_'.$modifier.'.'.$fileExt[1];
			}
			
			if(move_uploaded_file($_FILES['file'.$i]['tmp_name'], $file_path)) {
		    	//echo 'file uploaded';
		   	}
		   	
		   	$row_added = $wpdb->insert(
				$doc_table_name,
				array(
					'bus_id' => $_POST['id'],
					'doc_path' => $file_path,
					'doc_description' => $_POST['file'.$i.'_description']
				)
			);
		}
		
	}
	
	//$wpdb->show_errors();
	$row_updated = $wpdb->update(
		$main_table_name,
		$update_array,
		array('id'=>$_POST['id']),
		array('%s'),
		array('%d')
	);
	
	if($_POST['from']=='frontend'){
		echo "Your information has been updated.";
	} else {
		header('Location: '.get_bloginfo('url').'/wp-admin/admin.php?page=business_directory');
	}	
}
else if($action == 'quick_edit'){
	global $main_table_name, $doc_table_name, $cat_table_name;
	$update_array = array();
	if(!empty($_POST['name'])){$update_array['name'] = $_POST['name'];}
	if(!empty($_POST['description'])){$update_array['description'] = $_POST['description'];}
	if(!empty($_POST['phone'])){$update_array['phone'] = $_POST['phone'];}
	if(!empty($_POST['fax'])){$update_array['fax'] = $_POST['fax'];}
	if(!empty($_POST['email'])){$update_array['email'] = $_POST['email'];}
	if(!empty($_POST['contact'])){$update_array['contact'] = $_POST['contact'];}
	if(!empty($_POST['url'])){$update_array['url'] = $_POST['url'];}
	if(!empty($_POST['facebook'])){$update_array['facebook'] = $_POST['facebook'];}
	if(!empty($_POST['twitter'])){$update_array['twitter'] = $_POST['twitter'];}
	if(!empty($_POST['linkedin'])){$update_array['linkedin'] = $_POST['linkedin'];}
	if(isset($_POST['promo']) && $_POST['promo']=='true'){
		$update_array['promo'] ='true';
	}
	else{$update_array['promo'] = 'false';}	
	if(!empty($_POST['promo_description'])){$update_array['promoDescription'] = $_POST['promo_description'];}
	if(!empty($_POST['logo'])){$update_array['logo'] = $_FILES['logo'];}
	else if(!empty($_POST['current_logo'])){$update_array['logo'] = $_POST['current_logo'];}
	if(!empty($_POST['login'])){$update_array['login'] = $_POST['login'];}
	if(!empty($_POST['approved'])){
		if($_POST['approved']=='true'){$update_array['approved']='true';}
		else{$update_array['approved']='false';}
	}
	
	
	if(!empty($_FILES['logo']['name'])){
		$allowedExtensions = array('jpg', 'jpeg', 'gif', 'png', 'xls', 'xslx', 'doc', 'docx', 'pdf');
		preg_match('/\.('.implode($allowedExtensions, '|').')$/', $_FILES['logo']['name'], $fileExt);
		$logo_path = 'logos/'.$login.'_logo.'.$fileExt[1];
		while (file_exists($logo_path)) {
			$modifier = rand(0, 1000);
			$logo_path = 'logos/'.$login.'_logo'.$modifier.'.'.$fileExt[1];
		}
		
		$update_array['logo'] = $logo_path;
		if(move_uploaded_file($_FILES['logo']['tmp_name'], $logo_path)) {
	    	//echo 'file uploaded';
	   	}
	} else if(!empty($_POST['current_logo'])){
		$update_array['logo'] = $_POST['current_logo'];
	}
	
	//print_r($update_array);	
	//$wpdb->show_errors();
	$row_updated = $wpdb->update(
		$main_table_name,
		$update_array,
		array('id'=>$_POST['id']),
		array('%s'),
		array('%d')
	);
}

else if($action == 'search'){
	global $main_table_name, $doc_table_name, $cat_table_name;
	$search = $_POST['query'];
	$promo = '';
	if(isset($_POST['promo_filter']) && $_POST['promo_filter']=='promo'){
		$promo = "AND (promo = true)";
	}
	$search_results = $wpdb->get_results(
		"
		SELECT *
		FROM $main_table_name
		WHERE (approved = true)
		{$promo}
		AND (id like '%{$search}%'
		OR name like '%{$search}%'
		OR description like '%{$search}%'
		OR phone like '%{$search}%'
		OR fax like '%{$search}%'
		OR email like '%{$search}%'
		OR contact like '%{$search}%'
		OR url like '%{$search}%'
		OR facebook like '%{$search}%'
		OR twitter like '%{$search}%'
		OR linkedin like '%{$search}%'
		OR promoDescription like '%{$search}%'
		OR login like '%{$search}%')	
		ORDER BY name ASC
		"
	);
	echo "<h3><a href='javascript:void(0);' id='lddbd_back_to_categories' onclick='javascript: backToCategories();'>&larr; Categories</a>Search Results</h3>";
	if($search_results){
		foreach($search_results as $business){
			$contact = '';
			$logo_html = '';
			$contact_right = '';
			
			
			if(!empty($business->phone)){ $contact.="<li>Phone: {$business->phone}</li>"; }
			if(!empty($business->fax)){ $contact.="<li>Fax: {$business->fax}</li>"; }
			
			if(!empty($business->url)){ 
				if(strstr($business->url, 'http://')){$business_url = $business->url;}
				else{$business_url = 'http://'.$business->url;}
				$contact_right.="<a class='lddbd_contact_icon' target='_blank' href='{$business_url}'><img src='".plugins_url()."/lddbd/images/website.png' /</a>"; 
			}
			if(!empty($business->facebook)){ 
				if(strstr($business->facebook, 'http://')){$business_facebook = $business->facebook;}
				else{$business_facebook = 'http://'.$business->facebook;}
				$contact_right.="<a class='lddbd_contact_icon' target='_blank' href='{$business_facebook}'><img src='".plugins_url()."/lddbd/images/facebook.png' /></a>"; 
			}
			if(!empty($business->twitter)){ 
				if(strstr($business->twitter, 'http://')){$business_twitter = $business->twitter;}
				else{$business_twitter = 'http://'.$business->twitter;}
				$contact_right.="<a class='lddbd_contact_icon' target='_blank' href='{$business_twitter}'><img src='".plugins_url()."/lddbd/images/twitter.png' /></a>"; 
			}
			if(!empty($business->linkedin)){ 
				if(strstr($business->linkedin, 'http://')){$business_linkedin = $business->linkedin;}
				else{$business_linkedin = 'http://'.$business->linkedin;}
				$contact_right.="<a class='lddbd_contact_icon' target='_blank' href='{$business_linkedin}'><img src='".plugins_url()."/lddbd/images/linkedin.png' /></a>"; 
			}
			if(!empty($business->email)){ $contact_right.="<a class='lddbd_contact_icon' href='javascript:void(0);' onclick=\"javascript:mailToBusiness('{$business->email}', this, '{$business->name}');\"><img src='".plugins_url()."/lddbd/images/email.png' /></a>"; }
			if($business->promo=='true'){ $contact_right.="<a class='lddbd_contact_icon' href='javascript:void(0);' onclick=\"javascript:singleBusinessListing({$business->id});\"><img src='".plugins_url()."/lddbd/images/special-offer.png' /></a>"; }
			
			if(!empty($business->logo)){$logo_html = "<div class='lddbd_logo_holder' onclick='javascript:singleBusinessListing({$business->id});'><img src='".plugins_url()."/lddbd/{$business->logo}'/></div>"; }
			
			if(strstr($business->url, 'http://')){$business_url = $business->url;}
			else{$business_url = 'http://'.$business->url;}
		
			echo "<div class='lddbd_business_listing'>
						{$logo_html}
						<a href='javascript:void(0);' id='{$business->id}_business_detail' class='business_detail_link' onclick='javascript:singleBusinessListing({$business->id});'>{$business->name}</a>
						<ul class='lddbd_business_contact'>
							{$contact}
						</ul>
						<div class='lddbd_business_contact'>
							{$contact_right}
						</div>
					</div>";
		}
	}	
}

else if($action == 'categories_list'){
	global $main_table_name, $doc_table_name, $cat_table_name;
	$business_list = $wpdb->get_results(
		"
		SELECT *
		FROM $main_table_name WHERE approved = true
		"
	);
	$category_list = $wpdb->get_results(
		"
		SELECT * FROM $cat_table_name
		ORDER BY name ASC
		"
	);
	
	$category_number = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $cat_table_name" ) );
	
	if($category_list){
		$i = 0;
		foreach($category_list as $category){
			$cat_count = 0;
			foreach($business_list as $business){
				$cat_array = explode(',', str_replace('x', '', substr($business->categories, 1)));
				if(in_array($category->id, $cat_array)){
					$cat_count++;
				}
			}
			
			
			$row_updated = $wpdb->update(
				$cat_table_name,
				array('count'=>$cat_count),
				array('id'=>$category->id),
				array('%d'),
				array('%d')
			);
			$categories.="<a class='category_link' href='javascript:void(0);' onclick='javascript:categoryListing({$category->id});'>{$category->name} ({$cat_count}) </a>";
			if($i >= $category_number/2){
				$categories.="</div><div id='lddbd_categories_right'>";
			}
			$i++;
		}
	}
	echo "<div id='lddbd_categories_left'>
 			{$categories}
 		</div>";
}

else if($action == 'category_filter'){
	global $main_table_name, $doc_table_name, $cat_table_name;
	$category_results = $wpdb->get_results(
		"
		SELECT *
		FROM $main_table_name
		WHERE categories like '%x".$_POST['cat_id']."x%'
		AND approved = true
		ORDER BY name ASC
		"
	);
	$category = $wpdb->get_row(
		"
		SELECT *
		FROM $cat_table_name
		WHERE id = '{$_POST['cat_id']}'
		"
	);
	echo "<h3><a href='javascript:void(0);' id='lddbd_back_to_categories' onclick='javascript: backToCategories();'>&larr; Categories</a>{$category->name}</h3>";
	if($category_results){
		foreach($category_results as $business){
			$contact = '';
			$logo_html = '';
			$contact_right = '';
			
			
			if(!empty($business->phone)){ $contact.="<li>Phone: {$business->phone}</li>"; }
			if(!empty($business->fax)){ $contact.="<li>Fax: {$business->fax}</li>"; }
			
			if(!empty($business->url)){ 
				if(strstr($business->url, 'http://')){$business_url = $business->url;}
				else{$business_url = 'http://'.$business->url;}
				$contact_right.="<a class='lddbd_contact_icon' target='_blank' href='{$business_url}'><img src='".plugins_url()."/lddbd/images/website.png' /</a>"; 
			}
			if(!empty($business->facebook)){ 
				if(strstr($business->facebook, 'http://')){$business_facebook = $business->facebook;}
				else{$business_facebook = 'http://'.$business->facebook;}
				$contact_right.="<a class='lddbd_contact_icon' target='_blank' href='{$business_facebook}'><img src='".plugins_url()."/lddbd/images/facebook.png' /></a>"; 
			}
			if(!empty($business->twitter)){ 
				if(strstr($business->twitter, 'http://')){$business_twitter = $business->twitter;}
				else{$business_twitter = 'http://'.$business->twitter;}
				$contact_right.="<a class='lddbd_contact_icon' target='_blank' href='{$business_twitter}'><img src='".plugins_url()."/lddbd/images/twitter.png' /></a>"; 
			}
			if(!empty($business->linkedin)){ 
				if(strstr($business->linkedin, 'http://')){$business_linkedin = $business->linkedin;}
				else{$business_linkedin = 'http://'.$business->linkedin;}
				$contact_right.="<a class='lddbd_contact_icon' target='_blank' href='{$business_linkedin}'><img src='".plugins_url()."/lddbd/images/linkedin.png' /></a>"; 
			}
			if(!empty($business->email)){ $contact_right.="<a class='lddbd_contact_icon' href='javascript:void(0);' onclick=\"javascript:mailToBusiness('{$business->email}', this, '{$business->name}');\"><img src='".plugins_url()."/lddbd/images/email.png' /></a>"; }
			if($business->promo=='true'){ $contact_right.="<a class='lddbd_contact_icon' href='javascript:void(0);' onclick=\"javascript:singleBusinessListing({$business->id});\"><img src='".plugins_url()."/lddbd/images/special-offer.png' /></a>"; }
			
			if(!empty($business->logo)){$logo_html = "<div class='lddbd_logo_holder' onclick='javascript:singleBusinessListing({$business->id});'><img src='".plugins_url()."/lddbd/{$business->logo}'/></div>"; }
			
			
		
			echo "<div class='lddbd_business_listing'>
						{$logo_html}
						<a href='javascript:void(0);' id='{$business->id}_business_detail' class='business_detail_link' onclick='javascript:singleBusinessListing({$business->id});'>{$business->name}</a>
						<ul class='lddbd_business_contact'>
							{$contact}
						</ul>
						<div class='lddbd_business_contact'>
							{$contact_right}
						</div>
					</div>";
		}
	}else{echo "<div class='lddbd_business_listing'>Sorry, this category is empty.</div>";}
}
else if($action == 'edit_category'){
	global $main_table_name, $doc_table_name, $cat_table_name;
	$id = $_POST['id'];
	//$wpdb->show_errors();
	$wpdb->update(
		$cat_table_name,
		array(
			'name'=>$_POST['name']
		),
		array('id'=>$id),
		array('%s'),
		array('%d')
	);

}
else if($action == 'add_category'){
	global $main_table_name, $doc_table_name, $cat_table_name;
	$row_added = $wpdb->insert(
		$cat_table_name,
		array(
			'name' => $_POST['name'],
			'count' => 0
		)
	);
	echo "<table><tr id='cat-{$wpdb->insert_id}'>".
			"<td>".
				"<strong>{$_POST['name']}</strong>".
				"<div class='row-actions'>".
					"<a class='delete_category' href='javascript:void(0);'>Delete</a>".
					"<a class='edit_category open' href='javascript:void(0);'>Edit</a>".
				"</div>".
			"</td>".
			"<td>0</td>".
			"<td>{$wpdb->insert_id}</td>".
		"</tr>".
		"<tr class='lddbd_edit_category_row'>".
			"<td colspan='3'>".
				"<form class='lddbd_edit_category_form' method='post' action='".plugins_url()."/lddbd/lddbd_ajax.php'>".
					"<input type='text' name='cat_name' value='{$_POST['name']}'>".
					"<input type='hidden' name='action' value='edit_category'/>".
					"<input type='hidden' name='id' value='{$wpdb->insert_id}'/>".
		   			"<p class='submit'>".
					    "<input type='submit' class='button-secondary' value='Save Category' />".
				    "</p>".
		   		"</form>".
	   		"</td>".
		"</tr></table>";
}
else if($action == 'delete_category'){
	global $main_table_name, $doc_table_name, $cat_table_name;
	$id = $_POST['id'];
	$wpdb->query(
		"
		DELETE FROM $cat_table_name
		WHERE id = $id
		"
	);
}
else if($action == 'login'){
	global $main_table_name, $doc_table_name, $cat_table_name;
	$login = $_POST['login'];
	//$wpdb->show_errors();
	$business = $wpdb->get_row("SELECT * FROM $main_table_name WHERE login = '{$login}'");

	if($business){
		if($business->password == $_POST['password']){
		
		$files = $wpdb->get_results("SELECT * FROM $doc_table_name WHERE bus_id = '{$business->id}'");
		$files_list = '';
		
		foreach($files as $file){
			$files_list .="<li><em>{$file->doc_description}</em><input type='button' value='delete' class='file_delete' id='{$file->doc_id}_delete'/></li>";
		}
		
		$promo = '';
		if($business->promo == 'true'){
			$promo = 'checked';
		}
		
		$options = get_option('lddbd_options');
		$user_categorization_query = $options['user_categorization'];
		if($user_categorization_query=='Yes'){
			$categories_list = $wpdb->get_results(
				"
				SELECT *
				FROM $cat_table_name
				"
			);
			
			$categories_array = explode(',', str_replace('x', '', substr($business->categories, 1)));
			if(empty($categories_array)){$categories_array=array();}
	
			$business_categories = "<div class='lddbd_input_holder'>";
			$business_categories .= "<strong>Categories</strong>";
			
			foreach($categories_list as $category){
				$checked = '';
				if(in_array($category->id, $categories_array)){$checked = 'checked';}
				$business_categories .= "<div class='lddbd_category_block'>";
				$business_categories .= "<input type='checkbox' class='category_box' name='category_{$category->id}' value='x{$category->id}x' {$checked}/>";
				$business_categories .= "<label for='category_{$category->id}'>{$category->name}</label>";
				$business_categories .= "</div>";
			}
			
			$business_categories .= "<input id='lddbd_categories' type='hidden' name='categories' value='{$business->categories}'/>";
			$business_categories .= "</div>";
		}
		
		$section_array = unserialize($options['information_sections']);
		if(!empty($section_array)){
			$other_sections = '';
			$business_section_array = unserialize($business->other_info);
			foreach($section_array as $number=>$attributes){
				$type = $attributes['type'];
				if($type=='bool'){
					$checked_yes = '';
					$checked_no = '';
					if($business_section_array[$attributes['name']]=='Yes'){
						$checked_yes = 'checked';
					} else {
						$checked_no = 'checked';
					}
					$input = "<div class='lddbd_radio_holder'><input type='radio' name='{$attributes['name']}' value='Yes' {$checked_yes}/>Yes&nbsp;<input type='radio' name='{$attributes['name']}' value='No' {$checked_no}/>No</div>";
				} else {
					$input = "<input type='{$type}' name='{$attributes['name']}' value='{$business_section_array[$attributes['name']]}'/>";
				}
				
				
				$other_sections.= "<div class='lddbd_input_holder'>
						<label for='{$attributes['name']}'>{$attributes['title']}</label>
						{$input}
					</div>
				";
				
			}
		}
	
		echo "<form id='lddbd_edit_business_form' action='".plugins_url()."/lddbd/lddbd_ajax.php' method='POST' enctype='multipart/form-data' target='lddbd_edit_submission_target'>
			<div class='lddbd_input_holder'>
				<label for='name'>Business Name</label>
				<input class='required' type='text' id='lddbd_name' name='name' value='{$business->name}'/>
			</div>
			
			<div class='lddbd_input_holder'>
				<label for='description'>Business Description</label>
				<textarea id='lddbd_description' name='description'>{$business->description}</textarea>
			</div>
			
			<div class='lddbd_input_holder'>
				<label for='address_street'>Street</label>
				<input type='text' id='lddbd_address_street' name='address_street' value='{$business->address_street}'>
			</div>
			
			<div class='lddbd_input_holder'>
				<label for='address_city'>City</label>
				<input type='text' id='lddbd_address_city' name='address_city' value='{$business->address_city}'>
			</div>
			
			<div class='lddbd_input_holder'>
				<label for='lddbd_address_state'>State</label>
				{$lddbd_state_dropdown}
			</div>
			
			<div class='lddbd_input_holder'>
				<label for='address'>Zip</label>
				<input type='text' id='lddbd_address_zip' name='address_zip' value='{$business->address_zip}'>
			</div>
			
			<div class='lddbd_input_holder'>
				<label for='phone'>Contact Phone</label>
				<input class='' type='text' id='lddbd_phone' name='phone' value='{$business->phone}'/>
			</div>
			
			<div class='lddbd_input_holder'>
				<label for='fax'>Contact Fax</label>
				<input type='text' id='lddbd_fax' name='fax' value='{$business->fax}'>
			</div>
			
			<div class='lddbd_input_holder'>
				<label for='email'>Contact Email</label>
				<input class='required' type='text' id='lddbd_email' name='email' value='{$business->email}'/>
			</div>
			
			<div class='lddbd_input_holder'>
				<label for='contact'>Contact Name</label>
				<input class='' type='text' id='lddbd_contact' name='contact' value='{$business->contact}'/>
			</div>
			
			<div class='lddbd_input_holder'>
				<label for='url'>Website</label>
				<input class='' type='text' id='lddbd_url' name='url' value='{$business->url}'/>
			</div>
			
			<div class='lddbd_input_holder'>
				<label for='facebook'>Facebook Page</label>
				<input type='text' id='lddbd_facebook' name='facebook' value='{$business->facebook}'/>
			</div>
			
			<div class='lddbd_input_holder'>
				<label for='twitter'>Twitter Handle</label>
				<input type='text' id='lddbd_twitter' name='twitter' value='{$business->twitter}'/>
			</div>
			
			<div class='lddbd_input_holder'>
				<label for='linkedin'>Linked In Profile</label>
				<input type='text' id='lddbd_linkedin' name='linkedin' value='{$business->linkedin}'/>
			</div>
			
			<div class='lddbd_input_holder'>
				<label for='promo'>Special Offer</label>
				<input type='checkbox' id='lddbd_promo' name='promo' value='true' {$promo}/>
			</div>
			
			<div class='lddbd_input_holder'>
				<label for='promo_description'>Special Offer Description</label>
				<input type='text' id='lddbd_promo_description' name='promo_description' value='{$business->promoDescription}'/>
			</div>
			
			<div class='lddbd_input_holder'>
				<label for='current_logo'>Current Logo</label>
				<input type='hidden' id='lddbd_current_logo' name='current_logo' value='{$business->logo}'/>
			</div>
			
			<div class='lddbd_input_holder'>
				<img src='".plugins_url()."/lddbd/{$business->logo}'/>
			</div>
			
			<div class='lddbd_input_holder'>
				<label for='logo'>Upload New Logo</label>
				<input class='' type='file' id='lddbd_logo' name='logo'/>
			</div>
			
			{$other_sections}
			
			{$business_categories}
						
			<div class='lddbd_input_holder'>
				<label for='password'>Password</label>
				<input class='required' type='text' id='lddbd_password' name='password' value='{$business->password}'/>
			</div>
			
			<div class='lddbd_input_holder'>
				<strong>Files</strong>
				<ul>
				{$files_list}
				</ul>
			</div>
			
			<div class='lddbd_input_holder file_input_holder'>
			<strong>File</strong>
			<strong>Description</strong>
			</div>
			
			<div class='lddbd_input_holder file_input_holder'>
				<input type='file' id='lddbd_file1' name='file1'/>
				<input type='text' id='lddbd_file1_description' name='file1_description'/>
			</div>
			
			<div class='lddbd_input_holder'>
				<input type='button' id='lddbd_add_file_upload' value='Add File Upload' />
			</div>
			
			<input type='hidden' id='lddbd_id' name='id' value='{$business->id}'/>
			<input type='hidden' id='lddbd_action' name='action' value='edit'/>
			<input type='hidden' id='lddbd_from' name='from' value='frontend'/>
			
			<p class='submit'>
				<input type='button' id='lddbd_login_cancel' value='Cancel' />
			    <input type='submit' class='button-primary' value='Submit Changes' />
		    </p>
			</form>
			<iframe id='lddbd_edit_submission_target' name='lddbd_edit_submission_target' src='".plugins_url()."/lddbd/lddbd_ajax.php' style='width:0px;height:0px;border:0px solid #fff;'></iframe>
			
			<script type='text/javascript'>
				jQuery(document).ready(function(){
					jQuery('#lddbd_address_state option[value=\"{$business->address_state}\"]').attr('selected', 'selected');
				});
				
				jQuery('.category_box').click(function(){
	 				var category_string = '';
	 				jQuery('.category_box').each(function(){
	 					if(jQuery(this).is(':checked')){
	 						category_string += ','+jQuery(this).val();
	 					}
	 				});
	 				jQuery('#lddbd_categories').val(category_string);
	 			});
 			
				jQuery('#lddbd_edit_business_form').submit(function(){
					//jQuery('#lddbd_edit_business_form').contents().fadeTo(200, 0.1);
					jQuery('.button-primary').attr('disabled', 'disabled');
					jQuery('#lddbd_edit_submission_target').load(function(){
						jQuery('#lddbd_edit_business_form').html('Your information has been updated.');
						alert('Your information has been updated.');
	 					var page = window.location.href.split('?')[0];
	 					window.location.href = page+'?business={$business->id}';
	 				});
 				});
 				
 				var file_input_count = 1;
 				jQuery('#lddbd_add_file_upload').click(function(){
 					if(file_input_count<5){
	 					file_input_count++;
	 					jQuery('.file_input_holder').last().after('<div class=\'lddbd_input_holder file_input_holder\'><input type=\'file\' id=\'lddbd_file'+file_input_count+'\' name=\'file'+file_input_count+'\'/><input type=\'text\' id=\'lddbd_file'+file_input_count+'_description\' name=\'file'+file_input_count+'_description\'/></div>');
	 				}	
 				});
 				
 				jQuery('input.file_delete').click(function(){
 					var this_placeholder = jQuery(this);
 					var doc_id = jQuery(this).attr('id');
 					doc_id = parseInt(doc_id);
 					jQuery.ajax({
						type: 'POST',
						url: '".plugins_url()."/lddbd/lddbd_ajax.php',
						data: {doc_id: doc_id, action: 'delete_doc'},
						success: function(data){
							this_placeholder.parent().slideUp('200');
						}
					});
 				});
 				
 				jQuery('#lddbd_login_cancel').click(function(){
 					window.location.reload();
 				});
			</script>
			";
		}
		else {
			echo "Sorry, the password you entered was incorrect, please try again.";
		}	
	}else{
		echo "Sorry, the login you entered was not on file, please try again.";
	}
}
else if($action == 'delete_doc'){
	global $main_table_name, $doc_table_name, $cat_table_name;
	$id = $_POST['doc_id'];
	$wpdb->query(
		"
		DELETE FROM $doc_table_name
		WHERE doc_id = $id
		"
	);
}
else if($action == 'email'){
	global $main_table_name, $doc_table_name, $cat_table_name;
	$email = $_POST['email'];
	$from = $_POST['from'];
	$name = $_POST['name'];
	$phone = $_POST['phone'];
	$message = $_POST['message'];
	$subject = 'Business Directory Email';
	
	if(!empty($name)){$subject.=' From '.$name;}
	if(!empty($phone)){$message.='\r\n Phone:'.$phone;}
	$headers = 'From: '.$from."\r\n".'X-Mailer: PHP/'.phpversion();
	
	$mail = mail($email, $subject, $message, $headers);
}

else if($action == 'recover_password'){
	global $main_table_name, $doc_table_name, $cat_table_name;
	$login = $_POST['login'];
	$business = $wpdb->get_row(
		"
		SELECT *
		FROM $main_table_name
		WHERE login = '{$login}'
		"
	);
	
	if(empty($business)){
		echo 'no login';
	} else {
		$charArray = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0');
		$password = '';
		for($i=0; $i<10; $i++){
			$password.=$charArray[rand(0, (count($charArray)-1))];
		}
		
		$id = $business->id;
		$wpdb->update(
			$main_table_name,
			array(
				'password'=>$password
			),
			array('id'=>$id),
			array('%s'),
			array('%d')
		);
		$message = "Your new business directory password is $password";
		$mail = mail($business->email, 'Business Directory Password', $message);
		echo 'success';
	}
}

?>