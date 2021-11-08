<?php
defined( 'ABSPATH' ) || exit();

/**
 *
 * @since 3.0.0
 * @package Stripe/Gateways
 * @author User
 *
 */
class WC_Payment_Gateway_Stripe_CC extends WC_Payment_Gateway_Stripe {

	use WC_Stripe_Payment_Intent_Trait;

	protected $payment_method_type = 'card';

	public function __construct() {
		$this->id                 = 'stripe_cc';
		$this->tab_title          = __( 'Credit Cards', 'woo-stripe-payment' );
		$this->template_name      = 'credit-card.php';
		$this->token_type         = 'Stripe_CC';
		$this->method_title       = __( 'Stripe Credit Cards', 'woo-stripe-payment' );
		$this->method_description = __( 'Credit card gateway that integrates with your Stripe account.', 'woo-stripe-payment' );
		parent::__construct();
	}

	public function get_icon() {
		$cards = $this->get_option( 'cards', array() );
		$icons = array();
		foreach ( $cards as $card ) {
			$icons[ $card ] = stripe_wc()->assets_url( "img/cards/{$card}.svg" );
		}

		return wc_stripe_get_template_html(
			'card-icons.php',
			apply_filters( 'wc_stripe_cc_icon_template_args', array(
				'cards'      => $cards,
				'icons'      => $icons,
				'assets_url' => stripe_wc()->assets_url()
			), $this )
		);
	}

	public function enqueue_checkout_scripts( $scripts ) {
		$scripts->enqueue_script(
			'credit-card',
			$scripts->assets_url( 'js/frontend/credit-card.js' ),
			array(
				$scripts->prefix . 'external',
				$scripts->prefix . 'wc-stripe',
			)
		);
		$scripts->localize_script( 'credit-card', $this->get_localized_params() );
	}

	public function get_localized_params() {
		$data = parent::get_localized_params();

		return array_merge(
			$data,
			array(
				'cardOptions'        => $this->get_card_form_options(),
				'customFieldOptions' => $this->get_card_custom_field_options(),
				'custom_form'        => $this->is_custom_form_active(),
				'html'               => array( 'card_brand' => sprintf( '<img id="wc-stripe-card" src="%s" />', $this->get_custom_form()['cardBrand'] ) ),
				'cards'              => array(
					'visa'       => stripe_wc()->assets_url( 'img/cards/visa.svg' ),
					'amex'       => stripe_wc()->assets_url( 'img/cards/amex.svg' ),
					'mastercard' => stripe_wc()->assets_url( 'img/cards/mastercard.svg' ),
					'discover'   => stripe_wc()->assets_url( 'img/cards/discover.svg' ),
					'diners'     => stripe_wc()->assets_url( 'img/cards/diners.svg' ),
					'jcb'        => stripe_wc()->assets_url( 'img/cards/jcb.svg' ),
					'unionpay'   => stripe_wc()->assets_url( 'img/cards/china_union_pay.svg' ),
					'unknown'    => $this->get_custom_form()['cardBrand'],
				),
				'postal_regex'       => $this->get_postal_code_regex(),
			)
		);
	}

	/**
	 * @since 3.3.0
	 */
	public function get_card_form_options() {
		$options = array(
			'style' => $this->get_form_style()
		);

		return apply_filters( 'wc_stripe_cc_form_options', $options, $this );
	}

	/**
	 * @return mixed|void
	 * @since 3.3.0
	 */
	public function get_card_custom_field_options() {
		$style   = $this->get_form_style();
		$options = array();
		foreach ( [ 'cardNumber', 'cardExpiry', 'cardCvc' ] as $key ) {
			$options[ $key ] = array( 'style' => $style );
		}

		return apply_filters( 'wc_stripe_get_card_custom_field_options', $options, $this );
	}

	public function get_form_style() {
		if ( $this->is_custom_form_active() ) {
			$style = $this->get_custom_form()['elementStyles'];
		} else {
			$style = array(
				'base'    => array(
					'color'         => '#32325d',
					'fontFamily'    => '"Helvetica Neue", Helvetica, sans-serif',
					'fontSmoothing' => 'antialiased',
					'fontSize'      => '18px',
					'::placeholder' => array( 'color' => '#aab7c4' ),
					':focus'        => array(),
				),
				'invalid' => array(
					'color'     => '#fa755a',
					'iconColor' => '#fa755a',
				),
			);
		}

		return apply_filters( 'wc_stripe_cc_element_style', $style, $this );
	}

	public function get_custom_form() {
		return wc_stripe_get_custom_forms()[ $this->get_option( 'custom_form' ) ];
	}

	public function get_element_options( $options = array() ) {
		if ( $this->is_custom_form_active() ) {
			return parent::get_element_options( $this->get_custom_form()['elementOptions'] );
		}

		return parent::get_element_options();
	}

	/**
	 * Returns true if custom forms are enabled.
	 *
	 * @return bool
	 */
	public function is_custom_form_active() {
		return $this->get_option( 'form_type' ) === 'custom';
	}

	public function get_custom_form_template() {
		$form = $this->get_option( 'custom_form' );

		return wc_stripe_get_custom_forms()[ $form ]['template'];
	}

	/**
	 * Returns true if the postal code field is enabled.
	 *
	 * @return bool
	 */
	public function postal_enabled() {
		if ( is_checkout() ) {
			return $this->is_active( 'postal_enabled' );
		}
		if ( is_add_payment_method_page() ) {
			return true;
		}
	}

	/**
	 * Returns true if the cvv field is enabled.
	 *
	 * @return bool
	 */
	public function cvv_enabled() {
		return $this->is_active( 'cvv_enabled' );
	}

	public function get_postal_code_regex() {
		return array(
			'AT' => '^([0-9]{4})$',
			'BR' => '^([0-9]{5})([-])?([0-9]{3})$',
			'CH' => '^([0-9]{4})$',
			'DE' => '^([0]{1}[1-9]{1}|[1-9]{1}[0-9]{1})[0-9]{3}$',
			'ES' => '^([0-9]{5})$',
			'FR' => '^([0-9]{5})$',
			'IT' => '^([0-9]{5})$/i',
			'IE' => '([AC-FHKNPRTV-Y]\d{2}|D6W)[0-9AC-FHKNPRTV-Y]{4}',
			'JP' => '^([0-9]{3})([-])([0-9]{4})$',
			'PT' => '^([0-9]{4})([-])([0-9]{3})$',
			'US' => '^([0-9]{5})(-[0-9]{4})?$',
			'CA' => '^([ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ])([\ ])?(\d[ABCEGHJKLMNPRSTVWXYZ]\d)$',
			'PL' => '^([0-9]{2})([-])([0-9]{3})',
			'CZ' => '^([0-9]{3})(\s?)([0-9]{2})$',
			'SK' => '^([0-9]{3})(\s?)([0-9]{2})$',
			'NL' => '^([1-9][0-9]{3})(\s?)(?!SA|SD|SS)[A-Z]{2}$',
		);
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see WC_Payment_Gateway_Stripe::add_stripe_order_args()
	 */
	public function add_stripe_order_args( &$args, $order ) {
		// if the merchant is forcing 3D secure for all intents then add the required args.
		if ( $this->is_active( 'force_3d_secure' ) && is_checkout() && ! doing_action( 'woocommerce_scheduled_subscription_payment_' . $this->id ) ) {
			$args['payment_method_options']['card']['request_three_d_secure'] = 'any';
		}
	}

	public function has_enqueued_scripts( $scripts ) {
		return wp_script_is( $scripts->get_handle( 'credit-card' ) );
	}

	/**
	 * Returns true if the save payment method checkbox can be displayed.
	 *
	 * @return boolean
	 */
	public function show_save_source() {
		$page = wc_stripe_get_current_page();

		if ( 'checkout' === $page ) {
			if ( wcs_stripe_active() ) {
				if ( WC_Subscriptions_Cart::cart_contains_subscription() ) {
					return false;
				}
				if ( wcs_cart_contains_renewal() ) {
					return false;
				}
			}
			// @since 3.1.5
			if ( wc_stripe_pre_orders_active() && WC_Pre_Orders_Cart::cart_contains_pre_order() ) {
				return ! WC_Pre_Orders_Product::product_is_charged_upon_release( WC_Pre_Orders_Cart::get_pre_order_product() );
			}

			return apply_filters( 'wc_stripe_cc_show_save_source', $this->is_active( 'save_card_enabled' ) );
		} elseif ( in_array( $page, array( 'add_payment_method', 'change_payment_method' ) ) ) {
			return false;
		} elseif ( 'order_pay' === $page ) {
			return is_user_logged_in() && $this->is_active( 'save_card_enabled' );
		}
	}

	/**
	 * @param WC_Order $order
	 *
	 * @return array|void
	 */
	public function process_zero_total_order( $order ) {
		if ( defined( WC_Stripe_Constants::PROCESSING_PAYMENT ) ) {
			$result = $this->save_payment_method( $this->get_new_source_token(), $order );
			if ( is_wp_error( $result ) ) {
				wc_add_notice( $result->get_error_message(), 'error' );

				return $this->get_order_error();
			}
			// The setup intent ID is no longer needed so remove it from the order
			$order->delete_meta_data( WC_Stripe_Constants::SETUP_INTENT_ID );
		} else {
			$setup_intent        = $this->get_payment_intent_id();
			$save_payment_method = $this->should_save_payment_method( $order );
			// if setup intent exists then it was created client side.
			// attempt to save the payment method
			if ( $setup_intent && $save_payment_method ) {
				$result = $this->save_payment_method( $this->get_new_source_token(), $order );
				if ( is_wp_error( $result ) ) {
					wc_add_notice( $result->get_error_message(), 'error' );

					return $this->get_order_error();
				}
			} elseif ( ! $setup_intent && $save_payment_method ) {
				// A new payment method is being used but there's no setup intent provided
				// by client. Create one here
				$result = $this->does_order_require_action( $order, $this->get_new_source_token() );
				if ( is_wp_error( $result ) ) {
					wc_add_notice( sprintf( __( 'Error processing payment. Reason: %s', 'woo-stripe-payment' ), $result->get_error_message() ), 'error' );

					return $this->get_order_error();
				} elseif ( $result ) {
					return $result;
				}
			}
		}

		return $this->payment_object->process_zero_total_order( $order, $this );
	}

	/**
	 * @param WC_Order $order
	 * @param string $payment_method
	 *
	 * @return array
	 */
	private function does_order_require_action( $order, $payment_method ) {
		$customer_id = wc_stripe_get_customer_id( $order->get_customer_id() );
		if ( ( $intent_id = $order->get_meta( WC_Stripe_Constants::SETUP_INTENT_ID ) ) ) {
			$intent = $this->gateway->setupIntents->update( $intent_id, array( 'payment_method' => $payment_method ) );
		} else {
			$params = array(
				'confirm'        => true,
				'customer'       => $customer_id,
				'payment_method' => $payment_method,
				'usage'          => 'off_session',
				'metadata'       => array(
					'gateway_id' => $this->id,
					'order_id'   => $order->get_id()
				)
			);
			$this->add_stripe_order_args( $params, $order );
			$intent = $this->payment_object->get_gateway()->setupIntents->create( apply_filters( 'wc_stripe_setup_intent_params', $params, $order, $this ) );

		}
		if ( is_wp_error( $intent ) ) {
			return $intent;
		}
		$order->update_meta_data( WC_Stripe_Constants::SETUP_INTENT_ID, $intent->id );
		$order->save();

		if ( in_array( $intent->status, array(
			'requires_action',
			'requires_payment_method',
			'requires_source_action',
			'requires_source',
			'requires_confirmation'
		), true ) ) {
			return array(
				'result'   => 'success',
				'redirect' => $this->get_payment_intent_checkout_url( $intent, $order, 'setup_intent' ),
			);
		} elseif ( $intent->status === 'succeeded' ) {
			$payment_method->payment_method_token = $intent->payment_method;
			// The setup intent ID is no longer needed so remove it from the order
			$order->delete_meta_data( WC_Stripe_Constants::SETUP_INTENT_ID );

			return false;
		}
	}
}