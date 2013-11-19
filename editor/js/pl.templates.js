!function ($) {

$.plTemplates = {

	init: function(){
		this.bindUIActions()
	}

	, bindUIActions: function(){
		var that = this

		// fix issue with drop down falling behind
		$('.actions-toggle').on('click', function(){
			$('.x-templates').css('z-index', 7);
			$(this).closest('.x-templates').css('z-index', 8)
		})

		$('.tpl-tag').tooltip({placement: 'top'})


		$(".load-template").on("click.loadTemplate", function(e) {

			e.preventDefault()

			var args = {
					mode: 'set_template'
				,	run: 'load'
				,	confirm: true
				,	confirmText: $.pl.lang("<h3>Are you sure?</h3><p>Loading a new template will overwrite the current page's configuration.</p>")
				,	savingText: $.pl.lang("Loading Template")
				,	refresh: true
				,	refreshText: $.pl.lang("Successfully Loaded. Refreshing page")
				, 	log: true
				,	key: $(this).closest('.pl-template-row').data('key')
				,	templateMode: $.pl.config.templateMode
			}

			var response = $.plAJAX.run( args )
			
		
		})

		$(".delete-template").on("click.deleteTemplate", function(e) {

			e.preventDefault()

			var key = $(this).closest('.pl-template-row').data('key')
			,	theIsotope = $(this).closest('.isotope')
			,	args = {
						mode: 'set_template'
					,	run: 'delete'
					,	confirm: true
					,	confirmText: $.pl.lang("<h3>Are you sure?</h3><p>This will delete this template. All pages using this template will be reverted to their default page configuration.</p>")
					,	savingText: $.pl.lang("Deleting Template")
					,	refresh: false
					, 	log: true
					,	key: key
					, 	beforeSend: function(){
							$( '.template_key_'+key ).fadeOut(300, function() {
								$(this).remove()

							})

					}
					,	postSuccess: function(){
						theIsotope.isotope( 'reLayout' )
					}
				}

			var response = $.plAJAX.run( args )

		})


		$(".form-save-template").on("submit.saveTemplate", function(e) {

			e.preventDefault()

			var form = $(this).formParams()
			,	theRegion = $('[data-region="template"]')
			,	args = {
						mode: 'set_template'
					,	run: 'save'
					,	savingText: $.pl.lang("Saving Template")
					,	refreshText: $.pl.lang("Successfully Saved. Refreshing page")
					, 	log: true
					,	map: $.plMapping.getCurrentMap()
					,	settings: $.pl.data.local
					,	postSuccess: function( response ){
							if( !response )
								return
								
							theRegion
								.addClass('custom-template editing-locked')	
								.data('custom-template', response.key)
								.attr('data-custom-template', response.key)
								
							// Reload page with custom sections tab open
							$.pageBuilder.reloadConfig( {location: 'new custom section', refresh: true, refreshArgs: '?tablink=add-new&tabsublink=custom'} )
						}
				}
			,	args = $.extend({}, args, form) // add form fields to post


			var response = $.plAJAX.run( args )


		})


		$(".update-template").on("click", function(e) {

			e.preventDefault()

			var that = this
			,	key = $(this).closest('.pl-template-row').data('key')
			,	args = {
						mode: 'set_template'
					,	run: 'update'
					,	confirm: true
					,	confirmText: $.pl.lang("<h3>Are you sure?</h3><p>This action will overwrite this template and its configuration. All pages using this template will be updated with the new config as well.</p>")
					,	savingText: $.pl.lang("Updating Template")
					,	successNote: true
					,	successText: $.pl.lang("Template successfully updated!")
					,	refresh: false
					, 	log: true
					,	key: key
					,	map: $.plMapping.getCurrentMap()
					,	settings: $.pl.data.local
				}

			var response = $.plAJAX.run( args )



		})


		$(".set-tpl").on("click.defaultTemplate", function(e) {

			e.preventDefault()

			var that = this
			,	value = $(this).closest('.pl-template-row').data('key')
			,	run = $(this).data('run')
			,	args = {
						mode: 'set_template'
					,	run: 'set_'+run
					,	confirm: false
					,	refresh: false
					, 	log: true
					, 	field: $(this).data('field')
					,	value: value
					, 	postSuccess: function( response ){

							// console.log("caller is " + arguments.callee.caller.toString());


							// $.Ajax parses argument values and calles this thing, probably supposed to do that a different way
							if(!response)
								return

							var theList = $(that).closest('.pl-list-contain')

								theList
									.find('.set-tpl[data-run="'+run+'"]')
									.removeClass('active')

								theList
									.find('.active-'+run)
									.removeClass('active-'+run)


							if(response.result && response.result != false){

								$(that)
									.addClass('active')
									.closest('.x-item-actions')
									.addClass('active-'+run)

							}else {
								plPrint('Response was false.')
								plPrint( response )
							}
						}
				}
			var response = $.plAJAX.run( args )
		})
	}
}
}(window.jQuery);