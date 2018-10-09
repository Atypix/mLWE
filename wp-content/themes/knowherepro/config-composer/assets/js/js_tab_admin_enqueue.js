(function ($) {
if(typeof vc === 'undefined' || typeof vc.shortcode_view === 'undefined')
            return false;
var Shortcodes = vc.shortcodes;
window.KnowhereTabView = vc.shortcode_view.extend({
    new_tab_adding:false,
	events:{
            'click .add_tab':'addTab',
            'click > .vc_controls .vc_control-btn-delete':'deleteShortcode',
            'click > .vc_controls .vc_control-btn-edit':'editElement',
            'click > .vc_controls .vc_control-btn-clone':'clone'
        },
	initialize:function (params) {
            window.KnowhereTabView.__super__.initialize.call(this, params);
            _.bindAll(this, 'stopSorting');
           //console && console.log('KnowhereTabView: initialize method called.');
        },
	// Render method called after element is added( cloned ), and on first initialisation
	render: function () {
        //console && console.log('KnowhereTabView: render method called.');
		window.KnowhereTabView.__super__.render.call(this); //make sure to call __super__. To execute logic fron inherited view. That way you can extend original logic. Otherwise, you will fully rewrite what VC will do at this event
		this.$tabs = this.$el.find('.wpb_tabs_holder');
        this.createAddTabButton();
        //console &&  console.log('KnowhereTabView '+this.$tabs);
		return this;
	},
	ready: function (e) {
        //console && console.log('KnowhereTabView: ready method called.');
		window.KnowhereTabView.__super__.ready.call(this, e);

		return this;
	},
	
    createAddTabButton:function(){
    	var new_tab_button_id = (+new Date() + '-' + Math.floor(Math.random() * 11));
            this.$tabs.append('<div id="new-tab-' + new_tab_button_id + '" class="new_element_button"></div>');
            this.$add_button = $('<li class="add_tab_block"><a href="#new-tab-' + new_tab_button_id + '" class="add_tab" title="' + window.i18nLocale.add_tab + '"></a></li>').appendTo(this.$tabs.find(".tabs_controls"));
         //console &&  console.log(' KnowhereTabView : createAddTabButton');
        },
    addTab:function (e) {
    	//console &&  console.log('addTab');
            e.preventDefault();
            this.new_tab_adding = true;
            var tab_title = window.i18nLocale.tab,
                tabs_count = this.$tabs.find('[data-element_type=vc_mad_single_tab]').length,
                tab_id = (+new Date() + '-' + tabs_count + '-' + Math.floor(Math.random() * 11));
            vc.shortcodes.create({shortcode:'vc_mad_single_tab', params:{title:tab_title, tab_id:tab_id}, parent_id:this.model.id});
            //console &&  console.log(' KnowhereTabView :  addTab');
            return false;
        },
        stopSorting:function (event, ui) {
            var shortcode;
             //console &&  console.log(' KnowhereTabView :  stopSorting loading');
            this.$tabs.find('ul.tabs_controls li:not(.add_tab_block)').each(function (index) {
                var href = $(this).find('a').attr('href').replace("#", "");
                // $('#' + href).appendTo(this.$tabs);
                shortcode = vc.shortcodes.get($('[id=' + $(this).attr('aria-controls') + ']').data('model-id'));
                vc.storage.lock();
                shortcode.save({'order':$(this).index()}); // Optimize
            });
              //console &&  console.log(' KnowhereTabView :  shortcode save');
            shortcode.save();
        },
       changedContent:function (view) {
            var params = view.model.get('params');
            if (!this.$tabs.hasClass('ui-tabs')) {
                this.$tabs.tabs({
                    select:function (event, ui) {
                        if ($(ui.tab).hasClass('add_tab')) {
                            return false;
                        }
                        return true;
                    }
                });
                this.$tabs.find(".ui-tabs-nav").prependTo(this.$tabs);
                this.$tabs.find(".ui-tabs-nav").sortable({
                    axis:(this.$tabs.closest('[data-element_type]').data('element_type') == 'test_element' ? 'y' : 'x'),
                    update:this.stopSorting,
                    items:"> li:not(.add_tab_block)"
                });
            }
            if (view.model.get('cloned') === true) {
                var cloned_from = view.model.get('cloned_from'),
                    $tab_controls = $('.tabs_controls > .add_tab_block', this.$content),
	                $new_tab = $("<li><a href='#tab-" + params.tab_id + "'>" + params.title + "</a></li>").insertBefore($tab_controls);
                this.$tabs.tabs('refresh');
                this.$tabs.tabs("option", 'active', $new_tab.index());
            } else {
                $("<li><a href='#tab-" + params.tab_id + "'>" + params.title + "</a></li>")
                    .insertBefore(this.$add_button);
                this.$tabs.tabs('refresh');
                this.$tabs.tabs("option", "active", this.new_tab_adding ? $('.ui-tabs-nav li', this.$content).length - 2 : 0);

            }
            this.new_tab_adding = false;
            //console &&  console.log(' KnowhereTabView :  changedContent save');
        },
   cloneModel:function (model, parent_id, save_order) {
            var shortcodes_to_resort = [],
                new_order = _.isBoolean(save_order) && save_order === true ? model.get('order') : parseFloat(model.get('order')) + vc.clone_index,
                model_clone,
                new_params = _.extend({}, model.get('params'));
            if (model.get('shortcode') === 'vc_mad_single_tab') _.extend(new_params, {tab_id:+new Date() + '-' + this.$tabs.find('[data-element-type=vc_mad_single_tab]').length + '-' + Math.floor(Math.random() * 11)});
            model_clone = Shortcodes.create({shortcode:model.get('shortcode'), id:vc_guid(), parent_id:parent_id, order:new_order, cloned:(model.get('shortcode') === 'vc_mad_single_tab' ? false : true), cloned_from:model.toJSON(), params:new_params});
            _.each(Shortcodes.where({parent_id:model.id}), function (shortcode) {
                this.cloneModel(shortcode, model_clone.get('id'), true);
            }, this);
            return model_clone;
            //console &&  console.log(' js :  cloneModel save');
        }

});

// Come from vc_map -> 'js_view' => 'KnowhereTabView'
})(window.jQuery);
