// Replace the original card skin with skin that uses the primary term set by TSF.
add_action(
	'elementor/widget/posts/skins_init',
	function ( $widget ) {

		if ( ! function_exists( 'tsf' ) ) return;

		class CardSkinWithPrimaryTerm extends \ElementorPro\Modules\Posts\Skins\Skin_Cards {

			protected function render_badge() {

				$taxonomy = $this->get_instance_value( 'badge_taxonomy' );

				if ( ! taxonomy_exists( $taxonomy ) )
					return;

				$primary_term = tsf()->get_primary_term( get_the_ID(), $taxonomy );

				if ( empty( $primary_term->name ) )
					return parent::render_badge();

				printf(
					'<div class=elementor-post__badge>%s</div>',
					esc_html( $primary_term->name )
				);
			}
		}

		// unregister the original cards skin, including all hooks
		$original = $widget->get_skin( 'cards' );
		remove_action( 'elementor/element/posts/section_layout/before_section_end', [ $original, 'register_controls' ] );
		remove_action( 'elementor/element/posts/section_query/after_section_end', [ $original, 'register_style_sections' ] );
		remove_action( 'elementor/element/posts/cards_section_design_image/before_section_end', [ $original, 'register_additional_design_image_controls' ] );
		$widget->remove_skin( 'cards' );

		//reregister skin
		$widget->add_skin( new CardSkinWithPrimaryTerm( $widget ) );
	}
);
