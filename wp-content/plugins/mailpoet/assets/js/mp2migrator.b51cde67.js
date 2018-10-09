/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = __webpack_require__(1);


/***/ },
/* 1 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/* eslint-disable func-names */
	!(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(2), __webpack_require__(3)], __WEBPACK_AMD_DEFINE_RESULT__ = function (mp, jQuery) {
	  'use strict';

	  var MailPoet = mp;
	  MailPoet.MP2Migrator = {

	    fatal_error: '',
	    is_logging: false,

	    startLogger: function () {
	      MailPoet.MP2Migrator.is_logging = true;
	      clearTimeout(MailPoet.MP2Migrator.displayLogs_timeout);
	      clearTimeout(MailPoet.MP2Migrator.updateProgressbar_timeout);
	      clearTimeout(MailPoet.MP2Migrator.update_wordpress_info_timeout);
	      setTimeout(MailPoet.MP2Migrator.updateDisplay, 1000);
	    },

	    stopLogger: function () {
	      MailPoet.MP2Migrator.is_logging = false;
	    },

	    updateDisplay: function () {
	      MailPoet.MP2Migrator.displayLogs();
	      MailPoet.MP2Migrator.updateProgressbar();
	    },

	    displayLogs: function () {
	      jQuery.ajax({
	        url: window.mailpoet_mp2_migrator.log_file_url,
	        cache: false
	      }).done(function (result) {
	        jQuery('#logger').html('');
	        result.split('\n').forEach(function (resultRow) {
	          var row = resultRow;
	          if (row.substr(0, 7) === '[ERROR]' || row.substr(0, 9) === '[WARNING]' || row === MailPoet.I18n.t('import_stopped_by_user')) {
	            row = '<span class="error_msg">' + row + '</span>'; // Mark the errors in red
	          } else if (row === MailPoet.I18n.t('import_complete')) { // Test if the import is complete
	            jQuery('#import-actions').hide();
	            jQuery('#upgrade-completed').show();
	          }
	          jQuery('#logger').append(row + '<br />\n');
	        });
	        jQuery('#logger').append('<span class="error_msg">' + MailPoet.MP2Migrator.fatal_error + '</span><br />\n');
	      }).always(function () {
	        if (MailPoet.MP2Migrator.is_logging) {
	          MailPoet.MP2Migrator.displayLogs_timeout = setTimeout(
	            MailPoet.MP2Migrator.displayLogs,
	            1000
	          );
	        }
	      });
	    },

	    updateProgressbar: function () {
	      jQuery.ajax({
	        url: window.mailpoet_mp2_migrator.progress_url,
	        cache: false,
	        dataType: 'json'
	      }).always(function (result) {
	        // Move the progress bar
	        var progress = 0;
	        if ((result.total !== undefined) && (Number(result.total) !== 0)) {
	          progress = Math.round((Number(result.current) / Number(result.total)) * 100);
	        }
	        jQuery('#progressbar').progressbar('option', 'value', progress);
	        jQuery('#progresslabel').html(progress + '%');
	        if (Number(result.current) !== 0) {
	          jQuery('#skip-import').hide();
	          jQuery('#progressbar').show();
	          jQuery('#logger-container').show();
	        }
	        if (MailPoet.MP2Migrator.is_logging) {
	          MailPoet.MP2Migrator.updateProgressbar_timeout = setTimeout(
	            MailPoet.MP2Migrator.updateProgressbar,
	            1000
	          );
	        }
	      });
	    },

	    startImport: function () {
	      MailPoet.MP2Migrator.fatal_error = '';
	      // Start displaying the logs
	      MailPoet.MP2Migrator.startLogger();

	      // Disable the import button
	      MailPoet.MP2Migrator.import_button_label = jQuery('#import').val();
	      jQuery('#import').val(MailPoet.I18n.t('importing')).attr('disabled', 'disabled');
	      // Hide the Skip button
	      jQuery('#skip-import').hide();
	      // Show the stop button
	      jQuery('#stop-import').show();

	      // Run the import
	      MailPoet.Ajax.post({
	        api_version: window.mailpoet_api_version,
	        endpoint: 'MP2Migrator',
	        action: 'import',
	        data: {
	        }
	      }).always(function () {
	        MailPoet.MP2Migrator.stopLogger();
	        // Get the latest information after the import was stopped
	        MailPoet.MP2Migrator.updateDisplay();
	        MailPoet.MP2Migrator.reactivateImportButton();
	      }).done(function (response) {
	        if (response) {
	          MailPoet.MP2Migrator.fatal_error = response.data;
	        }
	      }).fail(function (response) {
	        if (response.errors.length > 0) {
	          MailPoet.Notice.error(
	            response.errors.map(function (error) {
	              return error.message;
	            }),
	            { scroll: true }
	          );
	        }
	      });
	      return false;
	    },

	    reactivateImportButton: function () {
	      jQuery('#import').val(MailPoet.MP2Migrator.import_button_label).removeAttr('disabled');
	      jQuery('#stop-import').hide();
	    },

	    stopImport: function () {
	      jQuery('#stop-import').attr('disabled', 'disabled');
	      // Stop the import
	      MailPoet.Ajax.post({
	        api_version: window.mailpoet_api_version,
	        endpoint: 'MP2Migrator',
	        action: 'stopImport',
	        data: {
	        }
	      }).always(function () {
	        jQuery('#stop-import').removeAttr('disabled'); // Enable the button
	        MailPoet.MP2Migrator.reactivateImportButton();
	        // Get the latest information after the import was stopped
	        MailPoet.MP2Migrator.updateDisplay();
	      }).fail(function (response) {
	        if (response.errors.length > 0) {
	          MailPoet.Notice.error(
	            response.errors.map(function (error) {
	              return error.message;
	            }),
	            { scroll: true }
	          );
	        }
	      });
	      MailPoet.MP2Migrator.stopLogger();
	      return false;
	    },

	    skipImport: function () {
	      MailPoet.Ajax.post({
	        api_version: window.mailpoet_api_version,
	        endpoint: 'MP2Migrator',
	        action: 'skipImport',
	        data: {
	        }
	      }).done(function () {
	        MailPoet.MP2Migrator.gotoWelcomePage();
	      }).fail(function (response) {
	        if (response.errors.length > 0) {
	          MailPoet.Notice.error(
	            response.errors.map(function (error) {
	              return error.message;
	            }),
	            { scroll: true }
	          );
	        }
	      });
	      return false;
	    },

	    gotoWelcomePage: function () {
	      window.location.href = 'admin.php?page=mailpoet-welcome';
	      return false;
	    }

	  };

	  /**
	   * Actions to run when the DOM is ready
	   */
	  jQuery(function () {
	    jQuery('#progressbar').progressbar({ value: 0 });

	    // Import button
	    jQuery('#import').click(function () {
	      MailPoet.MP2Migrator.startImport();
	    });

	    // Stop import button
	    jQuery('#stop-import').click(function () {
	      MailPoet.MP2Migrator.stopImport();
	    });

	    // Skip import link
	    jQuery('#skip-import').click(function () {
	      MailPoet.MP2Migrator.skipImport();
	    });

	    // Go to welcome page
	    jQuery('#goto-welcome').click(function () {
	      MailPoet.MP2Migrator.gotoWelcomePage();
	    });

	    // Update the display
	    MailPoet.MP2Migrator.updateDisplay();
	  });
	}.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));


/***/ },
/* 2 */
/***/ function(module, exports) {

	module.exports = MailPoet;

/***/ },
/* 3 */
/***/ function(module, exports) {

	module.exports = jQuery;

/***/ }
/******/ ]);