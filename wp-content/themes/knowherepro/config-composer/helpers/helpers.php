<?php

if ( ! function_exists('knowhere_vc_manager') ) {
	function knowhere_vc_manager() {
		return Knowhere_Vc_Config::getInstance();
	}
}

if ( ! function_exists('knowhere_vc_asset_url') ) {
	function knowhere_vc_asset_url( $file ) {
		return knowhere_vc_manager()->assetUrl( $file );
	}
}
