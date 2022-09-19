<?php

function sa_customizer_section_register($wp_customize)
{
	// mudar esse noma para o nome do tema ativo
	$theme_name = 'ascorsan';

	$wp_customize->add_section('sa_customize_section', array(
		'title'    => __('Simulador Aposentadoria', $theme_name),
		'description' => 'Configurações do simulador de aposentadoria',
		'priority' => 5,
		'capability'     => 'edit_theme_options',
		'theme_supports' => '',
	));


	//  =============================
	//  = Checkbox                  =
	//  =============================
	$wp_customize->add_setting('is_bootstrap', array(
		'default' => _x('', $theme_name),
		'type' => 'theme_mod'

	));

	$wp_customize->add_control('is_bootstrap', array(
		'type' => 'checkbox',
		'label' => __('O site usa Bootstrap?', $theme_name),
		'section' => 'sa_customize_section',
		'priority' => 1,
	));


	//  =============================
	//  = Color Picker              =
	//  =============================
	$wp_customize->add_setting('primary_color', array(
		'default'           => '#020381',
	));

	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'primary_color_control', array(
		'label'    => __('Cor de destaque', $theme_name),
		'section'  => 'sa_customize_section',
		'settings' => 'primary_color',
		'priority' => 2,
	)));

	$wp_customize->add_setting('secondary_color', array(
		'default'           => '#000',
	));

	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'secondary_color_control', array(
		'label'    => __('Cor dos titulos', $theme_name),
		'section'  => 'sa_customize_section',
		'settings' => 'secondary_color',
		'priority' => 3,
	)));

	//  =============================
	//  = Text Input                =
	//  =============================
	$wp_customize->add_setting('email_simulador', array(
		'default' => _x('', $theme_name),
		'type' => 'theme_mod'

	));

	$wp_customize->add_control('email_simulador', array(
		'label' => __('Email do formulário', $theme_name),
		'section' => 'sa_customize_section',
		'priority' => 4,
	));
	
	//  =============================
	//  = Text Input                =
	//  =============================
	$wp_customize->add_setting('escritorio_simulador', array(
		'default' => _x('', $theme_name),
		'type' => 'theme_mod'

	));

	$wp_customize->add_control('escritorio_simulador', array(
		'label' => __('Escritório', $theme_name),
		'description' => __('Digite o nome do escritorio', $theme_name),
		'section' => 'sa_customize_section',
		'priority' => 5,
	));

	//  =============================
	//  = Text Input                =
	//  =============================
	$wp_customize->add_setting('whatsapp_simulador', array(
		'default' => _x('', $theme_name),
		'type' => 'theme_mod'

	));

	$wp_customize->add_control('whatsapp_simulador', array(
		'label' => __('Whatsapp', $theme_name),
		'description' => __('Digite o apenas numeros começando com DDI e DDD + telefone Ex:. 5551999999999', $theme_name),
		'section' => 'sa_customize_section',
		'priority' => 6,
	));
}

add_action('customize_register', 'sa_customizer_section_register');
