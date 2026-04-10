<?php
/**
 * Translate imported English storefront/demo content to Brazilian Portuguese on the frontend.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the string replacement map used in the frontend HTML output.
 *
 * Longer strings must appear before shorter ones so replacements stay stable.
 *
 * @return array<string, string>
 */
function lojaweb_ptbr_replacements() {
	static $replacements = null;

	if ( null !== $replacements ) {
		return $replacements;
	}

	$replacements = array(
		'There are no shipping options available. Please ensure that your address has been entered correctly, or contact us if you need any help.' => 'Nao ha opcoes de entrega disponiveis. Confira se o seu endereco foi preenchido corretamente ou fale conosco se precisar de ajuda.',
		'Coupon code applied successfully.' => 'Cupom aplicado com sucesso.',
		'Buy This T-shirt At 20% Discount, Use Code OFF20' => 'Compre esta camiseta com 20% de desconto usando o cupom OFF20',
		'Buy This T-shirt At 20% Discount,  Use Code OFF20' => 'Compre esta camiseta com 20% de desconto usando o cupom OFF20',
		'Few days back you left {{cart.product.names}} in your cart.' => 'Ha alguns dias voce deixou {{cart.product.names}} no seu carrinho.',
		'To help make up your mind, we have added an exclusive 10% discount coupon {{cart.coupon_code}} to your cart.' => 'Para ajudar na sua decisao, adicionamos um cupom exclusivo de 10% de desconto {{cart.coupon_code}} ao seu carrinho.',
		'Complete Your Purchase Now >>' => 'Conclua sua compra agora >>',
		'Hurry! This is a onetime offer and will expire in 24 Hours.' => 'Aproveite! Esta e uma oferta unica e expira em 24 horas.',
		'In case you couldn\'t finish your order due to technical difficulties or because you need some help, just reply to this email we will be happy to help.' => 'Se voce nao conseguiu finalizar o pedido por dificuldades tecnicas ou porque precisa de ajuda, responda este e-mail e teremos prazer em ajudar.',
		'Raining Offers For Hot Summer!' => 'Ofertas imperdiveis para o verao!',
		'25% Off On All Products' => '25% de desconto em todos os produtos',
		'Featured Products' => 'Produtos em destaque',
		'Limited Time Offer' => 'Oferta por tempo limitado',
		'Special Edition' => 'Edicao especial',
		'Worldwide Shipping' => 'Entrega para todo o Brasil',
		'Best Quality' => 'Melhor qualidade',
		'Best Offers' => 'Melhores ofertas',
		'Secure Payments' => 'Pagamentos seguros',
		'Have any queries?' => 'Tem alguma duvida?',
		'We\'re here to help.' => 'Estamos aqui para ajudar.',
		'Don\'t be a stranger!' => 'Fale com a gente!',
		'You tell us. We listen.' => 'Voce fala, nos ouvimos.',
		'Get In Touch' => 'Entre em contato',
		'Contact Us' => 'Contato',
		'Billing details' => 'Dados de cobranca',
		'Shipping details' => 'Dados de entrega',
		'Your order' => 'Seu pedido',
		'Place order' => 'Finalizar pedido',
		'Proceed to checkout' => 'Ir para o checkout',
		'Apply coupon' => 'Aplicar cupom',
		'Coupon code' => 'Codigo do cupom',
		'Update cart' => 'Atualizar carrinho',
		'Return to shop' => 'Voltar para a loja',
		'View cart' => 'Ver carrinho',
		'No products in the cart.' => 'Nao ha produtos no carrinho.',
		'Shopping Cart' => 'Carrinho',
		'Cart totals' => 'Totais do carrinho',
		'Home' => 'Inicio',
		'Shop' => 'Loja',
		'Checkout' => 'Checkout',
		'Store Checkout' => 'Checkout da loja',
		'Thank You' => 'Obrigado',
		'Flow' => 'Fluxo',
		'Contact' => 'Contato',
		'Find More' => 'Saiba mais',
		'Shop Now' => 'Comprar agora',
		'Sales' => 'Vendas',
		'Complaints' => 'Reclamacoes',
		'Returns' => 'Devolucoes',
		'Marketing' => 'Marketing',
		'Product description' => 'Descricao do produto',
		'Additional information' => 'Informacoes adicionais',
		'More about the product' => 'Mais sobre o produto',
		'Description' => 'Descricao',
		'Reviews' => 'Avaliacoes',
		'Related products' => 'Produtos relacionados',
		'Bright Red Bag' => 'Bolsa Vermelha Vibrante',
		'Black Over-the-shoulder Handbag' => 'Bolsa Preta de Ombro',
		'Light Brown Purse' => 'Bolsa Marrom Clara',
		'Anchor Bracelet' => 'Pulseira Ancora',
		'Boho Bangle Bracelet' => 'Pulseira Boho',
		'Basic Gray Jeans' => 'Jeans Cinza Basico',
		'Blue Denim Jeans' => 'Jeans Azul',
		'Dark Brown Jeans' => 'Jeans Marrom Escuro',
		'DNK Blue Shoes' => 'Tenis Azul DNK',
		'DNK Red Shoes' => 'Tenis Vermelho DNK',
		'DNK Yellow Shoes' => 'Tenis Amarelo DNK',
		'DNK Black Shoes' => 'Tenis Preto DNK',
		'Men' => 'Masculino',
		'Women' => 'Feminino',
		'Accessories' => 'Acessorios',
		'Search Results for:' => 'Resultados da busca por:',
		'Here are the search results for your search.' => 'Aqui estao os resultados da sua busca.',
		'Sorry, but we could not find anything related to your search terms. Please try again.' => 'Nao encontramos nada relacionado aos termos pesquisados. Tente novamente.',
		'No results found' => 'Nenhum resultado encontrado',
		'Search' => 'Buscar',
		'Read more' => 'Leia mais',
		'Sale!' => 'Promocao!',
		'Add to cart' => 'Adicionar ao carrinho',
		'Select options' => 'Selecionar opcoes',
	);

	return $replacements;
}

/**
 * Translate a frontend HTML/text chunk.
 *
 * @param string $buffer HTML or text buffer.
 * @return string
 */
function lojaweb_translate_frontend_buffer( $buffer ) {
	if ( ! is_string( $buffer ) || '' === $buffer ) {
		return $buffer;
	}

	return strtr( $buffer, lojaweb_ptbr_replacements() );
}

/**
 * Start an output buffer for normal frontend HTML responses.
 */
function lojaweb_start_ptbr_output_buffer() {
	if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	if ( is_feed() || is_trackback() ) {
		return;
	}

	ob_start( 'lojaweb_translate_frontend_buffer' );
}
add_action( 'template_redirect', 'lojaweb_start_ptbr_output_buffer', 0 );

/**
 * Translate a subset of runtime strings before they hit the template.
 *
 * @param string $translated_text Already translated text.
 * @param string $text            Original text.
 * @return string
 */
function lojaweb_translate_runtime_strings( $translated_text, $text ) {
	$replacements = lojaweb_ptbr_replacements();

	if ( isset( $replacements[ $translated_text ] ) ) {
		return $replacements[ $translated_text ];
	}

	if ( isset( $replacements[ $text ] ) ) {
		return $replacements[ $text ];
	}

	return $translated_text;
}
add_filter( 'gettext', 'lojaweb_translate_runtime_strings', 20, 2 );
add_filter( 'ngettext', 'lojaweb_translate_runtime_strings', 20, 2 );
