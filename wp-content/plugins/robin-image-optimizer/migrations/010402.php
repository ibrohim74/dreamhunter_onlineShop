<?php #comp-page builds: premium
	
	/**
	 * Updates for altering the table used to store statistics data.
	 * Adds new columns and renames existing ones in order to add support for the new social buttons.
	 */
	class WIOUpdate010402 extends Wbcr_Factory458_Update {
		
		/**
		 * {inherit}
		 *
		 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
		 * @since  1.3.9
		 */
		public function install() {

			$db_version             = RIO_Process_Queue::get_db_version();
			$plugin_version_in_db   = $this->get_plugin_version_in_db();
			$current_plugin_version = $this->plugin->getPluginVersion();

			$init_log_message = "Start plugin migration < %s.\r\n";
			$init_log_message .= "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t-DB Version: {$db_version}\r\n";
			$init_log_message .= "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t-Plugin Version in DB: {$plugin_version_in_db}\r\n";
			$init_log_message .= "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t-Current Plugin Version: {$current_plugin_version}";

			WRIO_Plugin::app()->logger->info( sprintf( $init_log_message, '1.4.2' ) );

			if ( 'server_4' === WRIO_Plugin::app()->getOption( 'image_optimization_server' ) ) {
				WRIO_Plugin::app()->updateOption( 'image_optimization_server', 'server_1' );
			}

			WRIO_Plugin::app()->logger->info( 'Plugin migration was successfull!' );
		}
		
		/**
		 * Get previous plugin version
		 *
		 * @return number
		 * @since  1.3.9
		 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
		 */
		public function get_plugin_version_in_db() {
			if ( WRIO_Plugin::app()->isNetworkActive() ) {
				return get_site_option( WRIO_Plugin::app()->getOptionName( 'plugin_version' ), 0 );
			}
			
			return get_option( WRIO_Plugin::app()->getOptionName( 'plugin_version' ), 0 );
		}
	}
