<?php if ( ! is_single() ) {?>

	</div><!-- #content -->

<?php } ?>
<?php if ( ! is_front_page() ) {?>

	<?php get_sidebar(); ?>

	</div><!-- #wrap -->
	<div class="push"></div>

	</div><!-- .wrapper for sitting footer at the bottom of the page -->
<?php } ?>


<div class="footer">
  <div class="inner">
    <div class="omni-left"> <!--left hand column of footer-->
	<?php if ( pb_is_public() ) : ?>
	  <p class="omni-bookinfo">
        <span class="key"><?php _e( 'Book Name', 'pressbooks-book' ); ?>: </span>
		<span class="info"><?php bloginfo( 'name' ); ?></span></p>
		
		<?php global $metakeys;
		  $metadata = pb_get_book_information();
          foreach ( $metadata as $key => $val ) :
			if ( isset( $metakeys[ $key ] ) && ! empty( $val ) ) : ?>
			  <p class="omni-bookinfo">
			  <span class="key"><?php echo $metakeys[ $key ]; ?>:</span>
			  <span class="info"><?php if ( 'pb_publication_date' === $key ) {
						  $val = date_i18n( 'F j, Y', absint( $val ) );
                        }
						echo $val; ?>
			  </span>
			  </p>
			<?php endif;
		  endforeach; ?>
        <?php
				// Copyright
		  echo '<p class="omni-bookinfo">';
		  echo '<span class="key">' . __( 'Copyright', 'pressbooks-book' ) . ':</span>';
		  echo '<span class="info">'.( ! empty( $metadata['pb_copyright_year'] ) ) ? $metadata['pb_copyright_year'] : date( 'Y' );
		  if ( ! empty( $metadata['pb_copyright_holder'] ) ) {
			 echo ' ' . __( 'by', 'pressbooks-book' ) . ' ' . $metadata['pb_copyright_holder'];
		  }
		  echo "</span></p>\n"; 
		?>
		<p class="cie-name"><a href="https://pressbooks.com">Pressbooks: <?php _e( 'Simple Book Production', 'pressbooks-book' ); ?></a></p>

     </div> <!--left hand column of footer-->
     <div class="omni-right">
		<?php echo pressbooks_copyright_license(); ?>
     </div>

		<?php endif; ?>
	</div><!-- #inner -->
</div><!-- #footer -->
<?php wp_footer(); ?>
</body>
</html>
