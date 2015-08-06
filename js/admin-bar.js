;var GFEF;

(function($) {

	GFEF = {
		$target: false,
		counter: 0,
		schemas: false,
		schema: false,

		add_menu: function( form ){
			if( ! this.schemas )
				this.schemas = JSON.parse( gfef_vars.schemas );

			if( ! this.schemas[ form.schema ] )
				return;

			this.schema = this.schemas[ form.schema ];
			this.counter++;
			this.build_form_menu( form );
		}, // add_menu

		build_form_menu: function( form ){
			var $parent = this.create_top_menu(),
				context = 'gfef-form-' + form.form_id,
				edit_url = this.get_edit_url( form ),
				title = this.get_title( form, context );

			if( Object.keys( this.schema.menus ).length > 1 ){
				var $submenu = this.create_sub_menu( context, edit_url, title );
				$parent.append( $submenu );

				for ( var property in this.schema.menus ) {
					var url = this.schema.menus[property].url.format( form.form_id );
					var title = this.schema.menus[property].label;
					$submenu.find('ul').append( this.create_menu_item( context + property, url, title ) );
				}
			} else {
				$parent.append( this.create_menu_item( 'gfef', edit_url, title ) );
			}
		}, // build_form_menu

		get_title: function( form, context ){
			if( ! this.schema.callback )
				return 'Edit Form ' + this.counter;

			$.ajax({
				url: gfef_vars.ajax_url,
				data: { action: this.schema.callback, form_id: form.form_id },
				type: 'get',
				dataType: 'json',
				success: function( response ){
					if( response.form_title )
						$('#wp-admin-bar-' + context ).find('a:first').text( response.form_title )
				}
			});
			return 'Edit Form ' + this.counter;
		}, // get_title

		get_edit_url: function( form ){
			if( ! this.schema.menus.edit )
				return '#';

			return this.schema.menus.edit.url.format( form.form_id );
		}, // get_edit_url

		create_top_menu: function(){
			var context = 'gfef',
				$top = $('#wp-admin-bar-' + context + '-default');

			if( $top.length )
				return $top;

			var $li = this.create_sub_menu( context, '#', 'Edit Forms' );

			this.$target = $('#wp-admin-bar-edit');
			this.$target.after( $li );

			return $('#wp-admin-bar-' + context + '-default');
		}, // create_top_menu

		create_sub_menu: function( context, url, label ){
			var $li = $('<li id="wp-admin-bar-' + context + '" class="menupop" />'),
				$a = $('<a class="ab-item" aria-haspopup="true" href="' + url + '" />').html( label ),
				$div = $('<div class="ab-sub-wrapper" />'),
				$ul = $('<ul id="wp-admin-bar-' + context + '-default" class="ab-submenu" />');

			$div.append( $ul );
			$li.append( $a );
			$li.append( $div );

			return $li;
		}, // create_sub_menu

		create_menu_item: function( context, url, label ){
			var $li = $('<li id="wp-admin-bar-' + context +'" />'),
				$a = $('<a class="ab-item" href="' + url + '" />').text( label );

			$li.append( $a );

			return $li;
		}
	};

	/**
	 * Gravity Forms Implementation
	 */
	$(document).bind( 'gform_post_render', function( e, form_id, current_page ){
		GFEF.add_menu( { form_id: form_id, schema: 'gravityforms' } );
	});

})(jQuery);

if ( ! String.prototype.format ) {
	String.prototype.format = function() {
		var args = arguments;
		return this.replace(/{(\d+)}/g, function(match, number) {
			return typeof args[number] != 'undefined' ? args[number] : match;
		});
	};
}
