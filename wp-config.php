<?php
/**
 * As configurações básicas do WordPress
 *
 * O script de criação wp-config.php usa esse arquivo durante a instalação.
 * Você não precisa usar o site, você pode copiar este arquivo
 * para "wp-config.php" e preencher os valores.
 *
 * Este arquivo contém as seguintes configurações:
 *
 * * Configurações do MySQL
 * * Chaves secretas
 * * Prefixo do banco de dados
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Configurações do MySQL - Você pode pegar estas informações com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define( 'DB_NAME', 'wordpress' );

/** Usuário do banco de dados MySQL */
define( 'DB_USER', 'wordpress' );

/** Senha do banco de dados MySQL */
define( 'DB_PASSWORD', 'wordpress' );

/** Nome do host do MySQL */
define( 'DB_HOST', 'database' );

/** Charset do banco de dados a ser usado na criação das tabelas. */
define( 'DB_CHARSET', 'utf8mb4' );

/** O tipo de Collate do banco de dados. Não altere isso se tiver dúvidas. */
define('DB_COLLATE', '');

/**#@+
 * Chaves únicas de autenticação e salts.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las
 * usando o {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org
 * secret-key service}
 * Você pode alterá-las a qualquer momento para invalidar quaisquer
 * cookies existentes. Isto irá forçar todos os
 * usuários a fazerem login novamente.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY', 'J(#s2J@ulV h} :O+Xt~?$+iMsY*<~X[BhTy;RDAmVue!F)G8;UGsnL~A!ftsH+_' );
define( 'SECURE_AUTH_KEY', '>?8AsdX,tGv{4t<q2atiO7H.[!]m`y(tf4$Ff6{6vXKnrfFOrc~/Kq<CSsJGVn>V' );
define( 'LOGGED_IN_KEY', 'n-b1K]nXeI!Yr==+(G2dR=a?{:(: n$r+osoB>D32@8{p+4;4MmjUlqhyeUG7V:E' );
define( 'NONCE_KEY', 'x)WVWB-<T2wL1ZYn;I21q?n4x_Ilao??8>6iro&CYUoE_C5W>j%4T!C<<SDXup*V' );
define( 'AUTH_SALT', '.4qKo6fMeKox^}`b)6C`_Ru~A[e$G3dM9DmJmjX2k=C8AJ$llzdn(9qA<mN`_]Ia' );
define( 'SECURE_AUTH_SALT', 'JRX(%}c&M-ugIyw1VN|${)tUoQzO5wAaeJv]3kR=544bF;K^13qaaNQ:u5*Rx2>5' );
define( 'LOGGED_IN_SALT', '|dC-H.ES%hD=Y]R5ClY.(9e+>@lN*G}XHJ9*N8CyAnENm9[Xn`f0xJ9d+y)bpw,i' );
define( 'NONCE_SALT', 'N5)G!;r{#8YAcu5r$Si8Q.=6h0Wb~R=b[d)jU(vw@LWn^w5x,_g>n8^D6E1|7{Dj' );

/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der
 * um prefixo único para cada um. Somente números, letras e sublinhados!
 */
$table_prefix = 'wp_';

/**
 * Para desenvolvedores: Modo de debug do WordPress.
 *
 * Altere isto para true para ativar a exibição de avisos
 * durante o desenvolvimento. É altamente recomendável que os
 * desenvolvedores de plugins e temas usem o WP_DEBUG
 * em seus ambientes de desenvolvimento.
 *
 * Para informações sobre outras constantes que podem ser utilizadas
 * para depuração, visite o Codex.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define('WP_DEBUG', false);

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if (! defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

/** Configura as variáveis e arquivos do WordPress. */
require_once ABSPATH . 'wp-settings.php';

