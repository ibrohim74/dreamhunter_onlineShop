<?php

use WBCR\Factory_Processing_103\WP_Background_Process;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Класс для работы оптимизации в фоне
 *
 * @author        Artem Prikhodko <webtemyk@yandex.ru>
 * @copyright (c) 2021, Webcraftic
 * @version       1.0
 */
abstract class WRIO_Processing extends WP_Background_Process {

	/**
	 * @var string
	 */
	protected $prefix = 'wrio';

	/**
	 * @var string
	 */
	protected $action = 'optimize_process';

	/**
	 * @var string
	 */
	public $scope;

	/**
	 * Processing constructor.
	 *
	 * @param $scope
	 */
	public function __construct( $scope ) {
		$this->scope  = $scope;
		$this->action = "{$this->action}_{$scope}";
		parent::__construct();
	}

	abstract public function push_items();

	/**
	 * Fire before start handle the tasks
	 */
	protected function handle_before() {
		WRIO_Plugin::app()->logger->info( "START auto optimize process." );
	}

	/**
	 * Fire after end handle the tasks
	 */
	protected function handle_after() {
		WRIO_Plugin::app()->logger->info( "END auto optimize process." );
	}

	/**
	 * Fire after complete handle
	 */
	protected function handle_after_complete() {
		WRIO_Plugin::app()->updatePopulateOption( 'process_running', false );

	}
}
