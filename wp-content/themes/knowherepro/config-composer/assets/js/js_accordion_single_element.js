(function ($) {
if(typeof vc === 'undefined' || typeof vc.shortcode_view === 'undefined')
            return false;
var Shortcodes = vc.shortcodes;

	window.KnowhereVcBackendTtaSectionView = window.VcColumnView.extend({
		parentObj: null,
		events: {
			"click > .wpb_element_wrapper > .vc_tta-panel-body > .vc_controls .vc_control-btn-delete": "deleteShortcode",
			"click > .wpb_element_wrapper > .vc_tta-panel-body > .vc_controls .vc_control-btn-prepend": "addElement",
			"click > .wpb_element_wrapper > .vc_tta-panel-body > .vc_controls .vc_control-btn-edit": "editElement",
			"click > .wpb_element_wrapper > .vc_tta-panel-body > .vc_controls .vc_control-btn-clone": "clone",
			"click > .wpb_element_wrapper > .vc_tta-panel-body > .vc_empty-container": "addToEmpty"
		},
		setContent: function() {
			this.$content = this.$el.find("> .wpb_element_wrapper > .vc_tta-panel-body > .vc_container_for_children")
		},
		render: function() {
			var parentObj;
			return window.KnowhereVcBackendTtaSectionView.__super__.render.call(this), parentObj = vc.shortcodes.get(this.model.get("parent_id")), _.isObject(parentObj) && !_.isUndefined(parentObj.view) && (this.parentObj = parentObj), this.$el.addClass("vc_tta-panel"), this.$el.attr("style", ""), this.$el.attr("data-vc-toggle", "tab"), this.replaceTemplateVars(), this
		},
		replaceTemplateVars: function() {
			var title, $panelHeading;
			title = this.model.getParam("title"), _.isEmpty(title) && (title = this.parentObj && this.parentObj.defaultSectionTitle && this.parentObj.defaultSectionTitle.length ? this.parentObj.defaultSectionTitle : window.i18nLocale.section), $panelHeading = this.$el.find(".vc_tta-panel-heading");
			var template = vc.template($panelHeading.html(), vc.templateOptions.custom);
			$panelHeading.html(template({
				model_id: this.model.get("id"),
				section_title: title
			}))
		},
		getIndex: function() {
			return this.$el.index()
		},
		ready: function() {
			this.updateParentNavigation(), window.KnowhereVcBackendTtaSectionView.__super__.ready.call(this)
		},
		updateParentNavigation: function() {
			_.isObject(this.parentObj) && this.parentObj.view && this.parentObj.view.notifySectionRendered && this.parentObj.view.notifySectionRendered(this.model)
		},
		deleteShortcode: function(e) {
			var answer;
			return _.isObject(e) && e.preventDefault(), answer = confirm(window.i18nLocale.press_ok_to_delete_section), !0 !== answer ? !1 : (1 === vc.shortcodes.where({
				parent_id: this.model.get("parent_id")
			}).length ? (this.model.destroy(), this.parentObj && this.parentObj.destroy()) : (this.parentObj && this.parentObj.view && this.parentObj.view.removeSection && this.parentObj.view.removeSection(this.model), this.model.destroy()), !0)
		},
		changeShortcodeParams: function(model) {
			window.KnowhereVcBackendTtaSectionView.__super__.changeShortcodeParams.call(this, model), _.isObject(this.parentObj) && this.parentObj.view && this.parentObj.view.notifySectionChanged && this.parentObj.view.notifySectionChanged(model)
		}
	});

})(window.jQuery);
