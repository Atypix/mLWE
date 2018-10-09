(function(){
	tinymce.PluginManager.add('ctsc_shortcodes', function( editor, url ){
		editor.addButton('ctsc_shortcodes_button', {
			title: 'CPO Shortcodes',
			type: 'menubutton',
			icon: 'icon ctsc-shortcodes-icon',
			menu: [
				{
					text: 'Design Elements',
					menu: [
						{ text: 'Button', onclick: function(){ editor.selection.setContent('[button color="red" url="http://www.url.com" size="medium"]' + editor.selection.getContent() + '[/button]'); } },
						{ text: 'Notice', onclick: function(){ editor.selection.setContent('[notice]' + editor.selection.getContent() + '[/notice]'); } },
						{ text: 'Message', onclick: function(){ editor.selection.setContent('[message type="info"]' + editor.selection.getContent() + '[/message]'); } },
						{ text: 'Progress', onclick: function(){ editor.selection.setContent('[progress color="red" icon="" size="" value="" title="Progress Bar"]'); } },
						{ text: 'Mini Feature', onclick: function(){ editor.selection.setContent('[feature icon="flag" style="horizontal" title="Title"]' + editor.selection.getContent() + '[/feature]'); } },
						{ text: 'Team Member', onclick: function(){ editor.selection.setContent('[team image="" name="Member Name" title="Job Title" web="" facebook="" twitter="" gplus=""]' + editor.selection.getContent() + '[/team]'); } },
						{ text: 'Testimonial', onclick: function(){ editor.selection.setContent('[testimonial image="" name="Testimonial Name" title="Job Title"]' + editor.selection.getContent() + '[/testimonial]'); } },
						{ text: 'Counter', onclick: function(){ editor.selection.setContent('[counter icon="ok" Title="Counter Title" number="999"]' + editor.selection.getContent() + '[/counter]'); } }
					]
				},{
					text: 'Interactive',
					menu: [		
						{ text: 'Accordion', onclick: function(){ editor.selection.setContent('[accordion title="Title" state="open"]' + editor.selection.getContent() + '[/accordion]'); } },
						{ text: 'Tabbed Content', onclick: function(){ editor.selection.setContent('[tabs][tab title="Title"]' + editor.selection.getContent() + '[/tab][/tabs]'); } },
						{ text: 'Slideshow', onclick: function(){ editor.selection.setContent('[slideshow][slide]' + editor.selection.getContent() + '[/slide][/slideshow]'); } },
						{ text: 'Map', onclick: function(){ editor.selection.setContent('[map latitude="" longitude="" color="" height="400"]'); } },
						{ text: 'Pricing Table', onclick: function(){ editor.selection.setContent('[pricing_table][pricing_item title="Title" price="100" coin="$" url="" urltitle="Read More"]' + editor.selection.getContent() + '[/pricing_item][/pricing_table]'); } },
					]
				},{
					text: 'Content',
					menu: [			
						{ text: 'Dropcap', onclick: function(){ editor.selection.setContent('[dropcap style="square"]' + editor.selection.getContent() + '[/dropcap]'); } },
						{ text: 'Leading', onclick: function(){ editor.selection.setContent('[leading]' + editor.selection.getContent() + '[/leading]'); } },
						{ text: 'List', onclick: function(){ editor.selection.setContent('[list icon="ok" style="round"]' + editor.selection.getContent() + '[/list]'); } },
						{ text: 'Post List', onclick: function(){ editor.selection.setContent('[posts type="post" number="5" columns="1"]'); } },
					]
				},{
					text: 'Social',
					menu: [			
						{ text: 'Like Button', onclick: function(){ editor.selection.setContent('[fb_like url="" style="standard" font="arial" action="like" width="450" height="30" position="none"]'); } },
						{ text: 'Tweet Button', onclick: function(){ editor.selection.setContent('[tweet url="" style="none" font="arial" action="like" width="450" height="30" position="none"]'); } },
						{ text: '+1 Button', onclick: function(){ editor.selection.setContent('[gplus counter="" style="" width="450" height="30" position="none"]'); } },
					]
				},{
					text: 'Layout',
					menu: [			
						{ text: 'Two Columns', onclick: function(){ editor.selection.setContent('[column_half]<br><br>Column<br><br>[/column_half][column_half_last]<br><br>Column<br><br>[/column_half_last]'); } },
						{ text: 'Three Columns', onclick: function(){ editor.selection.setContent('[column_third]<br><br>Column<br><br>[/column_third][column_third]<br><br>Column<br><br>[/column_third][column_third_last]<br><br>Column<br><br>[/column_third_last]'); } },
						{ text: 'Four Columns', onclick: function(){ editor.selection.setContent('[column_fourth]<br><br>Column<br><br>[/column_fourth][column_fourth]<br><br>Column<br><br>[/column_fourth][column_fourth]<br><br>Column<br><br>[/column_fourth][column_fourth_last]<br><br>Column<br><br>[/column_fourth_last]'); } },
						{ text: 'Five Columns', onclick: function(){ editor.selection.setContent('[column_fifth]<br><br>Column<br><br>[/column_fifth][column_fifth]<br><br>Column<br><br>[/column_fifth][column_fifth]<br><br>Column<br><br>[/column_fifth][column_fifth]<br><br>Column<br><br>[/column_fifth][column_fifth_last]<br><br>Column<br><br>[/column_fifth_last]'); } },
						{ text: 'Separator', onclick: function(){ editor.selection.setContent('[separator type="top"]' + editor.selection.getContent() + '[/separator]'); } },
						{ text: 'Section', onclick: function(){ editor.selection.setContent('[section background="#666666" color="dark"]' + editor.selection.getContent() + '[/section]'); } },
					]
				}

			]
		});
	});
})();