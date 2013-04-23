<?php
 	/*
	Section: Flipper
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A great way to flip through posts. Simply select a post type and done.
	Class Name: PageLinesFlipper
	Cloning: true
	Workswith: main, templates, sidebar_wrap
	Filter: format
*/

class PageLinesFlipper extends PageLinesSection {

	
	var $default_limit = 3;

	function section_persistent(){
		
	}

	function section_styles(){
		wp_enqueue_script( 'caroufredsel', $this->base_url.'/min.caroufredsel.js', array( 'jquery' ), PL_CORE_VERSION, true );
		wp_enqueue_script( 'flipper', $this->base_url.'/flipper.js', array( 'jquery' ), PL_CORE_VERSION, true );
	}
	
	function section_opts(){
		
		$pt_objects = get_post_types( array(), 'objects');
		
		$pts = array();
		
		foreach($pt_objects as $key => $pt){
			
			if(post_type_supports( $key, 'thumbnail' ) && $pt->public){
				$pts[ $key ] = array(
					'name' => $pt->label
				);
			}
				
		}
		//plprint($pt_objects); 
		
		$options = array(); 

		$options[] = array(
			
			'title' => __( 'Flipper Setup', 'pagelines' ),
			'type'	=> 'multi',
			'opts'	=> array(
				array(
					'key'			=> 'flipper_post_type',
					'type' 			=> 'select',
					'opts'			=> $pts, 
					'default'		=> 4,
					'label' 	=> __( 'Which WordPress post type should Flipper use?', 'pagelines' ),
					'help'		=> __( 'Note: Post types for this section must have "featured images" enabled and be public.<br/>Tip: Use a plugin to create custom post types for use with Flipper.', 'pagelines' ),
				),
				
			
			)

		);
		$options[] = array(
			'key'			=> 'flipper_shown',
			'type' 			=> 'count_select',
			'count_start'	=> 1,
			'count_number'	=> 6,
			'default'		=> 3,
			'label' 		=> __( 'Max Number of Posts Shown', 'pagelines' ),
			'help'		=> __( 'This controls the maximum number of posts shown. A smaller amount may be shown based on layout width.', 'pagelines' ),
		);
		
		$options[] = array(
			'key'			=> 'flipper_title',
			'type' 			=> 'text',
			'label' 		=> __( 'Flipper Title', 'pagelines' ),
		);
		
		$options[] = array(
			'key'			=> 'flipper_meta',
			'type' 			=> 'text',
			'label' 		=> __( 'Flipper Meta', 'pagelines' ),
			'ref'			=> __( 'Use shortcodes to control the dynamic meta info. Example shortcodes you can use are: <ul><li><strong>[post_categories]</strong> - List of categories</li><li><strong>[post_edit]</strong> - Link for admins to edit the post</li><li><strong>[post_tags]</strong> - List of post tags</li><li><strong>[post_comments]</strong> - Link to post comments</li><li><strong>[post_author_posts_link]</strong> - Author and link to archive</li><li><strong>[post_author_link]</strong> - Link to author URL</li><li><strong>[post_author]</strong> - Post author with no link</li><li><strong>[post_time]</strong> - Time of post</li><li><strong>[post_date]</strong> - Date of post</li><li><strong>[post_type]</strong> - Type of post</li></ul>', 'pagelines' ),
		);
		$options[] = array(
			'key'			=> 'flipper_total',
			'type' 			=> 'count_select',
			'count_start'	=> 5,
			'count_number'	=> 20,
			'default'		=> 10,
			'label' 		=> __( 'Total Posts Loaded', 'pagelines' ),
			
		);

	
		return $options;
	}
	
	function section_template(  ) { 
				
		global $post;
		$post_type = ($this->opt('flipper_post_type')) ? $this->opt('flipper_post_type') : 'post'; 
		
		$pt = get_post_type_object($post_type);

		$shown = ($this->opt('flipper_shown')) ? $this->opt('flipper_shown') : '3'; 
		
		$total = ($this->opt('flipper_total')) ? $this->opt('flipper_total') : '10'; 
		
		$title = ($this->opt('flipper_title')) ? $this->opt('flipper_title') : $pt->label; 
		
		$meta = ($this->opt('flipper_meta')) ? $this->opt('flipper_meta') : '[post_date] [post_edit]'; 
		
		
		
		$the_query = array(
			'posts_per_page' 	=> $total,
			'post_type' 		=> $post_type
		);
		query_posts( $the_query ); 
		
		if(have_posts()) { ?>
				
				<div class="flipper-heading">
					<div class="flipper-title">
						
						<?php 
							echo $title;
							
							
							$archive_link = get_post_type_archive_link( $post_type );
							
							if( $archive_link )
								printf( '<a href="%s" > %s</a>', 
									$archive_link, 
									__(' / View All', 'pagelines')
								); 
							?>
						
					</div>
					<a class="flipper-prev" href="#"><i class="icon-arrow-left"></i></a>
			    	<a class="flipper-next" href="#"><i class="icon-arrow-right"></i></a>
				</div>
	
				<div class="flipper-wrap">
				
				<ul class="row flipper-items text-align-center flipper" data-scroll-speed="800" data-easing="easeInOutQuart" data-shown="<?php echo $shown;?>">
		<?php } ?>
			
			<?php if(have_posts()) : while(have_posts()) : the_post(); ?>
			
				
			<li style="">
				
				<div class="flipper-item fix">
					<?php 
					if ( has_post_thumbnail() ) { 
						echo get_the_post_thumbnail( $post->ID, 'aspect-thumb', array('title' => '')); 
					} else { 
						echo '<img height="400" width="600" src="'.$this->base_url.'/missing-thumb.jpg" alt="no image added yet." />'; 				
						}
						 ?>
					
					<div class="flipper-info-bg"></div>
					<div class="flipper-info pl-center-inside">
						
						<div class="pl-center">

						<?php 
						
							$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );  
				
							printf('<a href="%s">%s</a>', get_permalink(), __("View", 'pagelines'));
						
						
						?>
							
							
						</div>
						
					</div>
				</div><!--work-item-->
				
				<div class="flipper-meta">
					<h4 class="flipper-post-title"><?php the_title(); ?></h4>
					<div class="flipper-metabar"><?php echo do_shortcode( $meta ); ?></div>
				</div>
			
				
				<div class="clear"></div>
				
			</li>
			
			<?php endwhile; endif; 
			
			
			if(have_posts())
		 		echo '</ul></div>'; 
	
	}

	function do_defaults(){

		?>
		<h2>StarBar</h2>
		<ul class="starbars">
			<li>
				<p>Jack</p>
				<div class="bar-wrap">
					<span data-width="30%"><strong>30<i class="icon-star"></i></strong></span><strong>100<i class="icon-star"></i></strong>
				</div>
			</li>
			<li>
				<p>Jill</p>
				<div class="bar-wrap">
					<span data-width="60%"><strong>60<i class="icon-star"></i></strong></span>
				</div>
			</li>
		</ul>
		<?php
	}


}