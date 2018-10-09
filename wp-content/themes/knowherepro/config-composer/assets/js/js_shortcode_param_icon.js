(function ($) {

	$(function() {

		$(".mad-search").keyup(function () {
			var filter = $(this).val(), count = 0;
			$(".mad-icon-list li").each(function (){
				if ($(this).data('icon').search(new RegExp(filter, "i")) < 0) {
					$(this).fadeOut();
				} else {
					$(this).show();
					count++;
				}
			});
		});

		$("#mad-param-icon li").on('click', function (e) {
			e.preventDefault();

			var $this = $(this),
				icon = $this.attr("data-icon"),
				container = $this.parents('#mad-param-icon');

			$this.attr("class", "selected").siblings().removeAttr("class");
			container.children('#mad-trace').val(icon);
			container.children('.mad-icon-preview').html("<i class=\'"+ icon +"\'></i>");
		});

	});

})(jQuery);