(function ($) {
if(typeof vc === 'undefined' || typeof vc.shortcode_view === 'undefined')
            return false;
var Shortcodes = vc.shortcodes;

	window.KnowhereVcBackendTtaViewInterface = vc.shortcode_view.extend({
		sortableSelector: !1,
		$sortable: !1,
		$navigation: !1,
		defaultSectionTitle: window.i18nLocale.tab,
		sortableUpdateModelIdSelector: "data-vc-target-model-id",
		activeClass: "vc_active",
		sortingPlaceholder: "vc_placeholder",
		events: {
			"click > .vc_controls .vc_control-btn-delete": "deleteShortcode",
			"click > .vc_controls .vc_control-btn-edit": "editElement",
			"click > .vc_controls .vc_control-btn-clone": "clone",
			"click > .vc_controls .vc_control-btn-prepend": "clickPrependSection",
			"click .vc_tta-section-append": "clickAppendSection"
		},
		initialize: function(params) {
			window.KnowhereVcBackendTtaViewInterface.__super__.initialize.call(this, params), _.bindAll(this, "updateSorting")
		},
		render: function() {
			return window.KnowhereVcBackendTtaViewInterface.__super__.render.call(this), this.$el.addClass("vc_tta-container vc_tta-o-non-responsive"), this
		},
		setContent: function() {
			this.$content = this.$el.find("> .wpb_element_wrapper .vc_tta-panels")
		},
		clickAppendSection: function(e) {
			e.preventDefault(), this.addSection()
		},
		clickPrependSection: function(e) {
			e.preventDefault(), this.addSection(!0)
		},
		addSection: function(prepend) {
			var newTabTitle, params, shortcode;
			return newTabTitle = this.defaultSectionTitle, params = {
				shortcode: "vc_mad_tta_section",
				params: {
					title: newTabTitle
				},
				parent_id: this.model.get("id"),
				order: _.isBoolean(prepend) && prepend ? vc.add_element_block_view.getFirstPositionIndex() : vc.shortcodes.getNextOrder(),
				prepend: prepend
			}, shortcode = vc.shortcodes.create(params)
		},
		findSection: function(modelId) {
			return this.$content.children('[data-model-id="' + modelId + '"]')
		},
		getIndex: function($element) {
			return $element.index()
		},
		buildSortable: function($element) {
			return "edit" !== vc_user_access().getState("shortcodes") && vc_user_access().shortcodeAll("vc_mad_tta_section") ? $element.sortable({
				forcePlaceholderSize: !0,
				placeholder: this.sortingPlaceholder,
				helper: this.renderSortingPlaceholder,
				scroll: !0,
				cursor: "move",
				cursorAt: {
					top: 20,
					left: 16
				},
				start: function(event, ui) {},
				over: function(event, ui) {},
				stop: function(event, ui) {
					ui.item.attr("style", "")
				},
				update: this.updateSorting,
				items: this.sortableSelector
			}) : !1
		},
		updateSorting: function(event, ui) {
			var self;
			return vc_user_access().shortcodeAll("vc_mad_tta_section") ? (self = this, this.$sortable.find(this.sortableSelector).each(function() {
				var shortcode, modelId, $this;
				$this = $(this), modelId = $this.attr(self.sortableUpdateModelIdSelector), shortcode = vc.shortcodes.get(modelId), vc.storage.lock(), shortcode.save({
					order: self.getIndex($this)
				})
			}), vc.storage.unlock(), void vc.storage.save()) : !1
		},
		makeFirstSectionActive: function() {
			this.$content.children(":first-child").addClass(this.activeClass)
		},
		checkForActiveSection: function() {
			var $currentActive;
			$currentActive = this.$content.children("." + this.activeClass), $currentActive.length || this.makeFirstSectionActive()
		},
		changeActiveSection: function(modelId) {
			this.$content.children(".vc_tta-panel." + this.activeClass).removeClass(this.activeClass), this.findSection(modelId).addClass(this.activeClass)
		},
		changedContent: function(view) {
			var changedContent;
			return changedContent = window.KnowhereVcBackendTtaViewInterface.__super__.changedContent.call(this, view), this.checkForActiveSection(), this.buildSortable(this.$sortable), changedContent
		},
		notifySectionChanged: function(model) {
			var view, title;
			view = model.get("view"), _.isObject(view) && (title = model.getParam("title"), _.isString(title) && title.length || (title = this.defaultSectionTitle), view.$el.find(".vc_tta-panel-title a .vc_tta-title-text").text(title))
		},
		notifySectionRendered: function(model) {},
		getNextTab: function($viewTab) {
			var lastIndex, viewTabIndex, $nextTab, $navigationSections;
			return $navigationSections = this.$navigation.children(), lastIndex = $navigationSections.length - 2, viewTabIndex = $viewTab.index(), $nextTab = viewTabIndex !== lastIndex ? $navigationSections.eq(viewTabIndex + 1) : $navigationSections.eq(viewTabIndex - 1)
		},
		renderSortingPlaceholder: function(event, element) {
			return vc.app.renderPlaceholder(event, element)
		}
	});

	window.KnowhereVcBackendTtaAccordionView = KnowhereVcBackendTtaViewInterface.extend({
		sortableSelector: "> .vc_tta-panel:not(.vc_tta-section-append)",
		sortableSelectorCancel: ".vc-non-draggable",
		sortableUpdateModelIdSelector: "data-model-id",
		defaultSectionTitle: window.i18nLocale.section,
		render: function() {
			return window.VcBackendTtaTabsView.__super__.render.call(this), this.$navigation = this.$content, this.$sortable = this.$content, vc_user_access().shortcodeAll("vc_mad_tta_section") || this.$content.find(".vc_tta-section-append").hide(), this
		},
		removeSection: function(model) {
			var $viewTab, $nextTab, tabIsActive;
			$viewTab = this.findSection(model.get("id")), tabIsActive = $viewTab.hasClass(this.activeClass), tabIsActive && ($nextTab = this.getNextTab($viewTab), $nextTab.addClass(this.activeClass))
		},
		addShortcode: function(view) {
			var beforeShortcode;
			beforeShortcode = _.last(vc.shortcodes.filter(function(shortcode) {
				return shortcode.get("parent_id") === this.get("parent_id") && parseFloat(shortcode.get("order")) < parseFloat(this.get("order"))
			}, view.model)), beforeShortcode ? view.render().$el.insertAfter("[data-model-id=" + beforeShortcode.id + "]") : this.$content.prepend(view.render().el)
		}
	});

})(window.jQuery);
