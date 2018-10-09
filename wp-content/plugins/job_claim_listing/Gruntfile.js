'use strict';
module.exports = function(grunt) {

	grunt.initConfig({

		pkg : grunt.file.readJSON( 'package.json' ),

		makepot: {
			claimListing: {
				options: {
					type: 'wp-plugin',
					exclude: [
						'vendor/',
					],
				}
			}
		},

		glotpress_download: {
			theme: {
				options: {
					url: 'https://astoundify.com/glotpress',
					domainPath: 'languages',
					slug: 'claim-listing',
					textdomain: 'wp-job-manager-claim-listing',
					formats: [ 'mo', 'po' ],
					file_format: '%domainPath%/%wp_locale%.%format%',
					filter: {
						translation_sets: false,
						minimum_percentage: 50,
						waiting_strings: false
					}
				}
			}
		},

		checktextdomain: {
			standard: {
				options:{
					force: true,
					text_domain: 'wp-job-manager-claim-listing',
					create_report_file: false,
					correct_domain: true,
					keywords: [
						'__:1,2d',
						'_e:1,2d',
						'_x:1,2c,3d',
						'esc_html__:1,2d',
						'esc_html_e:1,2d',
						'esc_html_x:1,2c,3d',
						'esc_attr__:1,2d', 
						'esc_attr_e:1,2d', 
						'esc_attr_x:1,2c,3d', 
						'_ex:1,2c,3d',
						'_n:1,2,4d', 
						'_nx:1,2,4c,5d',
						'_n_noop:1,2,3d',
						'_nx_noop:1,2,3c,4d'
					]
				},
				files: [{
					src: [
						'**/*.php',
						'!node_modules/**',
						'!vendor/'
					],
					expand: true,
				}],
			},
			zip: {
				options:{
					force: true,
					text_domain: 'wp-job-manager-claim-listing',
					create_report_file: false,
					correct_domain: true,
					keywords: [
						'__:1,2d',
						'_e:1,2d',
						'_x:1,2c,3d',
						'esc_html__:1,2d',
						'esc_html_e:1,2d',
						'esc_html_x:1,2c,3d',
						'esc_attr__:1,2d', 
						'esc_attr_e:1,2d', 
						'esc_attr_x:1,2c,3d', 
						'_ex:1,2c,3d',
						'_n:1,2,4d', 
						'_nx:1,2,4c,5d',
						'_n_noop:1,2,3d',
						'_nx_noop:1,2,3c,4d'
					]
				},
				files: [{
					src: ['wp-job-manager-claim-listing/*.php','wp-job-manager-claim-listing/**/*.php'],
					expand: true,
				}],
			},
		},

		copy: {
			build: {
				src: [
					'**',
					'**/**',
					'!node_modules/**',
					'!**/node_modules/**',
				],
				dest: 'wp-job-manager-claim-listing/',
			},
		},

		zip: {
			'<%= pkg.name %>-<%= pkg.version %>.zip': ['wp-job-manager-claim-listing/**'],
		},

		clean: {
			dist: {
				src: [
					'wp-job-manager-claim-listing',
				]
			}
		},

	});

	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.loadNpmTasks( 'grunt-glotpress' );
	grunt.loadNpmTasks( 'grunt-checktextdomain' );
	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-zip' );
	grunt.loadNpmTasks( 'grunt-contrib-clean' );

	grunt.registerTask( 'i18n', [ 'checktextdomain:standard', 'makepot', 'glotpress_download' ] );
	grunt.registerTask( 'build_zip', [ 'copy', 'checktextdomain:zip', 'zip', 'clean' ] );
};
