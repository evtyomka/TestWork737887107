<?php

function wc_add_custom_fields() {

	global $product, $post;

	echo '<div class="options_group">'; 
	//создание полей
	woocommerce_wp_select(
		[
			'id'      => '_select_type',
			'label'   => 'Тип товара:',
			'options' => [
				'rare'   => __( 'Rare', 'woocommerce' ),
				'frequent'   => __( 'Frequent', 'woocommerce' ),
				'unusual' => __( 'Unusual', 'woocommerce' ),
			],
		]
	);

	woocommerce_wp_hidden_input(
		[
			'id'    => '_thumbnail_id_new',
			'class' => 'class_hidden',
		]
	);
	
	//поля для фото товара
	$thumbnail_id_new = get_post_meta( $post->ID, '_thumbnail_id_new' )[0];
	
	if( $thumbnail_id_new ) {

	echo '<div class="cf_img"><a href="#" class="_thumbnail"><img src="' . wp_get_attachment_image_src($thumbnail_id_new, 'thumbnail')[0] . '" /></a></div>
	      <a href="#" class="_thumbnail_del"><input type="button" class="button" value="Удалить изображение" /></a>';

	} else {

		echo '<a href="#" class="_thumbnail"><input type="button" class="button" value="Выбрать изображение" /></a>
			  <a href="#" class="_thumbnail_del" style="display:none"><input type="button" class="button" value="Удалить изображение" ></a>';

	} 
	//функциоанал формы для редактирования
	echo '</div>'; 
	?>	<div class="cf_edit">
			<input type="button" class="button" id="save_post" value="Сохранить" />
		</div>
		<div class="cf_edit">
			<input type="button" class="button" id="cf_clear" value="Очистить" />
		</div>
	<?
	//подключение скрипта добавления фото
	wp_enqueue_script( 'myuploadscript', get_stylesheet_directory_uri() . '/assets/admin.js', array('jquery'), null, false );
}

add_action( 'woocommerce_product_options_general_product_data', 'wc_add_custom_fields' );

add_action( 'woocommerce_process_product_meta', 'wc_custom_fields_save', 10 );
function wc_custom_fields_save( $post_id ) {
	//сохранение метаданных кастомных полей
	$product = wc_get_product( $post_id );
	
	$select_field = isset( $_POST['_select_type'] ) ? sanitize_text_field( $_POST['_select_type'] ) : '';
	$product->update_meta_data( '_select_type', $select_field );

	$hidden_field = isset( $_POST['_thumbnail_id_new'] ) ? sanitize_text_field( $_POST['_thumbnail_id_new'] ) : '';
	$product->update_meta_data( '_thumbnail_id_new', $hidden_field );
	
	//дата созания товара 
	if( !get_post_meta( $post_id, 'cf_date_add' )[0] )
		$product->update_meta_data( 'cf_date_add', date("Y-m-d H:i:s") );
	

	$product->save();

}
//работа с таблицей товаров
add_filter( 'manage_product_posts_columns', 'new_column');
function new_column( $columns ) {
	
	$my_column ['new_thumb'] = 'Img';

    return array_slice( $columns, 0, 1 ) + $my_column + $columns;
	
}

add_filter( 'manage_edit-product_columns', 'show_product_order',15);
function show_product_order($columns){

   unset( $columns['thumb'] );

   return $columns;
}


function product_custom_column_values( $column, $post_id ) {
 //форимрование нового столбца данных
   switch($column) {
		case 'new_thumb' :
			$thumbnail_id_new = get_post_meta( $post_id, '_thumbnail_id_new' )[0];
			$thumbnail_id = get_post_meta( $post_id, '_thumbnail_id' )[0];
			if($thumbnail_id_new)
				$src = wp_get_attachment_image_src($thumbnail_id_new, array(32,32))[0];
			else
				$src = wp_get_attachment_image_src($thumbnail_id, array(32,32))[0];
			
			echo' <img src="' . $src . '" width="32px" />';
			
		break;
	}
}

add_action( 'manage_product_posts_custom_column' , 'product_custom_column_values', 10, 2 );
//подключене стилей дочерней темы
add_action( 'admin_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
   
    wp_enqueue_style( 'storefrontchild',get_stylesheet_directory_uri() . '/style.css',
        array('storefront')
    );
	 wp_enqueue_style( 'storefront', get_template_directory_uri() . '/style.scss' );
}


