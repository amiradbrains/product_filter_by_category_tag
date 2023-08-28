<?php
//mockexam filter
function secops_mockexam_shortcode($atts) {
    ob_start();
    // Query for product categories
    $categories = get_terms('product_cat');
    // HTML for Categories
    echo '
    <div class="products-section">
    <div class="product-filters" id="product-tag-filter">
		 	 <ul class="cats" id="category-filter">
				<div class="ctechD text-center">
								 <p class="ctech mb-1"><strong>#format</strong></p> 
					</div>';
    foreach ($categories as $category) {
        echo '<li><a href="#" data-category="' . $category->slug . '">' . $category->name . '</a></li>';
    }
	// Placeholder for Tags (Populated by JavaScript)
    
    echo '</ul>';
	echo '<ul class="tags" id="tag-filter" style="display:none;"></ul></div>';
   
    // Query for products
    $args = [
        'post_type' => 'product',
        'posts_per_page' => -1,
    ];
    $query = new WP_Query($args);

    // HTML for Products
    echo '<div id="product-list" class="product-list mockExam">';
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            global $product;
            
            $cat_slugs = [];
            $tag_slugs = [];
            $categories = get_the_terms(get_the_ID(), 'product_cat');
            $tags = get_the_terms(get_the_ID(), 'product_tag');

            if (!empty($categories) && !is_wp_error($categories)) {
                foreach ($categories as $cat) {
                    $cat_slugs[] = $cat->slug;
                }
            }

            if (!empty($tags) && !is_wp_error($tags)) {
                foreach ($tags as $tag) {
                    $tag_slugs[] = $tag->slug;
                }
            }

            echo '<div class="product-item product single-pricing-card" data-categories="' . implode(' ', $cat_slugs) . '" data-tags="' . implode(' ', $tag_slugs) . '">';
            $ribbon = get_post_meta( get_the_ID(), 'ribbon', true );
			$sub_title = get_post_meta(get_the_ID(), "sub_title", true);
			$currency = get_post_meta(get_the_ID(), "currency", true);
			$sale_price = get_post_meta( get_the_ID(), '_sale_price', true );
			$regular_price = get_post_meta( get_the_ID(), '_regular_price', true );
			$r_price = ""; 
                if(!empty($sale_price) && !empty($regular_price)){
                    $r_price = $regular_price ;
                    $s_price = $sale_price ;  
                }
                else if( empty($sale_price) && !empty($regular_price) ){
                    $s_price =  $regular_price;
                }else{
                    $s_price = $r_price = 'TBA';    
                }
			$sup = get_post_meta(get_the_ID(), "sup", true);
			$format = get_post_meta(get_the_ID(), "format", true);
			 echo '<div class="ribbonWrapper">';
             echo '<div class="ribbon ' . $ribbon . '">' . $ribbon . '</div></div>';
			?> 
				<div class="productImg">
                     <?php the_post_thumbnail(); ?>
                </div>
		<h4 class="__content__subtitle">Mock Exams</h4>
		<?php	echo '<div class="pricing-header">';?>
                <h3> <?php the_title();?> </h3>
              <?php  echo '<p>' . $sub_title . '</p>';
                echo '</div>';
				echo '<div class="price">';
//                 echo '<h4>' .$currency . $s_price . '<sup>' . $sup . '</sup></h4>';
                echo '<h4> FREE</h4> <br>';
//                 if(!empty($r_price))
//                     echo '<p>' .$currency . $r_price . '</p>';
//                 else if($s_price==2000)
//                     echo '<p>'.$currency.'2000</p>';
                echo '</div>';
                echo '<a href="#" class="link-btn">Take the Exam now</a>';
			    echo '<div class ="features-list">' . $format . '</div>';
			echo '</div>';
        }
    }
    echo '</div>';

    ?>
    <script>
        jQuery(document).ready(function($) {
    var selectedCategory = '';  // Variable to hold the selected category

    // Function to populate tags
    function populateTags(category) {
        var uniqueTags = [];
        $('#product-list .product-item[data-categories*="' + category + '"]').each(function() {
            var tags = $(this).data('tags').split(' ');
            uniqueTags = [...new Set([...uniqueTags, ...tags])];
        });

        // Populate Tag Filter
        $('#tag-filter').empty().show();
    // Add static div
    $('#tag-filter').prepend('<div class="ctechD text-center"><p class="ctech mb-1"><strong>#tech</strong></p></div>');
    // Populate Tag Filter
    uniqueTags.forEach(function(tag) {
        $('#tag-filter').append('<li><a href="#" data-tag="' + tag + '">' + tag + '</a></li>');
    });
    }

    // Default selection
    $('a[data-category="all"]').addClass('active');
    $('a[data-category="all"]').parent().addClass('active');
	selectedCategory = 'all';
    populateTags('all');  // populate tags for 'all' category

    $('#category-filter').on('click', 'a', function(e) {
        e.preventDefault();
        
        // Remove active class from all and add to the clicked one
        $('#category-filter a').removeClass('active');
        $('#category-filter li').removeClass('active');
        $(this).addClass('active');
        $(this).parent().addClass('active');

        selectedCategory = $(this).data('category');  // Update the selected category

        // Filter Products by Category
        $('#product-list .product-item').hide();
        $('#product-list .product-item[data-categories*="' + selectedCategory + '"]').show();

        populateTags(selectedCategory);

        // Remove active class from tags as category changed
        $('#tag-filter a').removeClass('active');
        $('#tag-filter li').removeClass('active');
    });

    $('#tag-filter').on('click', 'a', function(e) {
        e.preventDefault();
        
        // Remove active class from all and add to the clicked one
        $('#tag-filter a').removeClass('active');
        $('#tag-filter li').removeClass('active');
        $(this).addClass('active');
        $(this).parent().addClass('active');

        var selectedTag = $(this).data('tag');

        // Further Filter Products by Tag AND Category
        $('#product-list .product-item').hide();
        $('#product-list .product-item[data-tags*="' + selectedTag + '"][data-categories*="' + selectedCategory + '"]').show();
    });
});

    </script>
    <?php

    return ob_get_clean();
}

add_shortcode('secops_mockexam', 'secops_mockexam_shortcode');
//End mockexam filter

?>
