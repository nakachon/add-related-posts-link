<?php
/*
Plugin Name: Add Related Posts Link
Plugin URI:  
Description: 関連記事のリストを表示
Author:  nakachon
Version: 0.1
Author URI: http://blog.nakachon.com/
*/

class ShowText {
    function __construct() {
      	add_action('admin_menu', array($this, 'add_pages'));
    	add_filter ('the_content',array($this,'add_related_posts_link'));
     }
    function add_pages() {
      add_menu_page('関連リンク追加','関連リンク追加',  'level_8', __FILE__, array($this,'show_text_option_page'), '', 26);
    }
    
    function add_related_posts_link($content) {
    	
    	    	
    	if ( is_single() && is_main_query()) {
    	 	
    	 	//記事が属するカテゴリを取得
    	 	$categories = get_the_category();
    	   
    	   //カテゴリの数量をカウント　$cat_numberにいれる
    	   //$cat_number = count($categories);
    	   
    	   // 管理画面から入力した関連リンクのIDとhtmlを取得
    	   
    	   $opts = get_option('showtext_options');
    	   
    	   foreach ($opts as $key => $opt) {
    	   
    	   	$show_CATID[$key]= isset($opt['CATID']) ? $opt['CATID']: null;
    	   	$show_text[$key] = isset($opt['text']) ? $opt['text']: null;
    	   	
    	   	$show_MENUID[$key]= isset($opt['MENUID']) ? $opt['MENUID']: null;
    	   	$show_MENUID[$key]= esc_html($show_MENUID[$key]);
    	   	
    	   	
    	   }
    	   
    	  // $related_category_IDと同じものが$show_CATIDにあれば、$contentに追加する
    	  
			foreach ($categories as $key => $category) {
				
				foreach ($show_CATID as $key2 => $value) {
					$CATID = intval($show_CATID[$key2]);
					
				if ( $category->cat_ID == $CATID ) {
				
					// そのままだと \でエスケープされているので、stripslashesで取り除く
					$show_text[$key2] = stripslashes($show_text[$key2]);
					$content .= $show_text[$key2];
					
					// カスタムメニューを取得して追加
					$nav = null;
					$nav = wp_nav_menu( array('menu'=>$show_MENUID[$key2], 'echo' => false));
					
					$content .= $nav;
					
				} // if 
				
				} // foreach show_CATID
				
			} // foreach
	 	   
    	} // if is_single
    	 
    	return $content;
    	
    }
    
    
    function show_text_option_page() {
        //$_POST['showtext_options'])があったら保存
        //オブション画面に表示する内容
       
        
        if ( isset($_POST['showtext_options'])) {
            check_admin_referer('shoptions');
            $opts = $_POST['showtext_options'];
            
            
            
            foreach ($opts as $key => $opt) {
            // Deleteがonになっているのと、CATIDに記入がない$optを削除	
            	if ($opt['Delete'] == "on" || $opt['CATID'] == "" ) { unset($opts[$key]); }
            }
         	//　削除して$keyが抜けたところを再配置
         	$opts = array_values($opts);
         	
            
             
            update_option('showtext_options', $opts);
            ?><div class="updated fade"><p><strong><?php _e('Options saved.'); ?></strong></p></div><?php
        }
        ?>
        <div class="wrap">
     
        <div id="icon-options-general" class="icon32"><br /></div><h2>関連リンク追加設定</h2>
        
        <form action="" method="post">
            <?php
            wp_nonce_field('shoptions');
            $opts = get_option('showtext_options');
            
            foreach ($opts as $key => $opt) {
            
            	$show_CATID[$key]= isset($opt['CATID']) ? $opt['CATID']: null;
            	$show_CATID[$key]= esc_html($show_CATID[$key]);
            	
            	$show_text[$key] = isset($opt['text']) ? $opt['text']: null;
            	$show_text[$key] = stripslashes($show_text[$key]);
            	$show_text[$key] = esc_html($show_text[$key] );
            	
            	$show_MENUID[$key]= isset($opt['MENUID']) ? $opt['MENUID']: null;
            	$show_MENUID[$key]= esc_html($show_MENUID[$key]);
            	
            }
            
            ?> 
        
         <table class="widefat fixed" cellspacing="0">
                <thead>
                <tr>
                	<th scope="col" style="width: 50px;">Key</th>
                	 <th scope="col" style="width: 50px;">削除</th>
                    <th scope="col" style="width: 180px;"><label for="inputCATID">カテゴリID</label></th>
                    <th scope="col" style="width: 300px;"><label for="inputtext">テキスト</label></th>
                    <th scope="col" style="width: 180px;"><label for="inputMENUID">カスタムメニューID</label></th>
                </tr>
                </thead>
        
                <tfoot>
                <tr>
               		<th scope="col" style="width: 50px;">Key</th>
      		    	<th scope="col" style="width: 50px;">削除</th>
					<th scope="col" style="width: 180px;"><label for="inputCATID">カテゴリID</label></th>
					<th scope="col" style="width: 300px;"><label for="inputtext">テキスト</label></th>
					<th scope="col" style="width: 180px;"><label for="inputMENUID">カスタムメニューID</label></th>
                </tr>
                </tfoot>
        
                <tbody>
                <?php foreach ($opts as $key => $opt) { ?>
                
                <tr>
                	<td><?php echo $key; ?></td>
                	<td scope="col" class="check-column"><input name="showtext_options[<?php echo $key; ?>][Delete]" type="checkbox" id="inputDelete" /></td>
                	<td><input name="showtext_options[<?php echo $key; ?>][CATID]" type="text" id="inputCATID" value="<?php  echo $show_CATID[$key]; ?>" /></td>
                	<td><textarea style='width: 80%;' rows="8" name="showtext_options[<?php echo $key; ?>][text]" id="inputtext" class="regular-text"><?php  echo $show_text[$key]; ?></textarea></td>
                	<td><input name="showtext_options[<?php echo $key; ?>][MENUID]" type="text" id="inputMENUID" value="<?php  echo $show_MENUID[$key]; ?>" /></td>
                	
         
                </tr>
                
                <?php } ?>
                
                <?php $key= $key+1; ?>
                
					<tr>
						<td><?php echo $key; ?></td>
						<td scope="col" class="check-column"><input name="showtext_options[<?php echo $key; ?>][Delete]" type="checkbox" id="inputDelete" /></td>
						<td><input name="showtext_options[<?php echo $key; ?>][CATID]" type="text" id="inputCATID" value="<?php  echo $show_CATID[$key]; ?>" /></td>
						<td><textarea style='width: 80%;' rows="8" name="showtext_options[<?php echo $key; ?>][text]" id="inputtext" class="regular-text"></textarea></td>
						<td><input name="showtext_options[<?php echo $key; ?>][MENUID]" type="text" id="inputMENUID" value="<?php  echo $show_MENUID[$key]; ?>" /></td>
						
					</tr>
                	
                </tbody>
                </table>
                 <p class="submit"><input type="submit" name="Submit" class="button-primary" value="変更を保存" /></p>
                  </form>
        <?php
    }
    
}

$showtext = new ShowText;