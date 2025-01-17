<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.
/**
 *
 * Field: button_set
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'PBSettings_Field_button_set' ) ) {
	class PBSettings_Field_button_set extends PBSettings_Fields {

		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}

		public function render() {

			$args = wp_parse_args( $this->field, array(
				'multiple'   => false,
				'options'    => array(),
				'query_args' => array(),
			) );

			$value = ( is_array( $this->value ) ) ? $this->value : array_filter( (array) $this->value );

			echo $this->field_before();

			if ( isset( $this->field['options'] ) ) {

				$options = $this->field['options'];
				$options = ( is_array( $options ) ) ? $options : array_filter( $this->field_data( $options, false, $args['query_args'] ) );

				if ( is_array( $options ) && ! empty( $options ) ) {

					echo '<div class="pbsettings-siblings pbsettings--button-group" data-multiple="' . esc_attr( $args['multiple'] ) . '">';

					foreach ( $options as $key => $option ) {

						$type         = ( $args['multiple'] ) ? 'checkbox' : 'radio';
						$extra        = ( $args['multiple'] ) ? '[]' : '';
						$active       = ( in_array( $key, $value ) || ( empty( $value ) && empty( $key ) ) ) ? ' pbsettings--active' : '';
						$checked      = ( in_array( $key, $value ) || ( empty( $value ) && empty( $key ) ) ) ? ' checked' : '';
						$availability = isset( $option['availability'] ) ? $option['availability'] : '';
						
						if ( false === $availability ) {
							$is_disabled = 'disabled';
						} elseif ( true === $availability ) {
							$is_disabled = '';
						} else {
							$is_disabled = empty( $availability ) ? '' : 'disabled';
						}

						echo '<div class="pbsettings--sibling pbsettings--button' . esc_attr( $active ) . ' ' . esc_attr( $availability ) . '" ' . esc_attr( $is_disabled ) . '>';
						echo '<input type="' . esc_attr( $type ) . '" name="' . esc_attr( $this->field_name( $extra ) ) . '" value="' . esc_attr( $key ) . '"' . $this->field_attributes() . esc_attr( $checked ) . '/>';
						echo '<div class="pbsettings--name">' . wp_kses_post( $option['label'] ) . '</div>';
						echo '</div>';
					}

					echo '</div>';

				} else {

					echo ( ! empty( $this->field['empty_message'] ) ) ? esc_attr( $this->field['empty_message'] ) : esc_html__( 'No data available.' );

				}

			}

			echo $this->field_after();

		}

	}
}
