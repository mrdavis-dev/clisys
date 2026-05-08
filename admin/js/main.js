(function($) {

	"use strict";

	/* ── Full height ──────────────────────────────────────── */
	var fullHeight = function() {
		$('.js-fullheight').css('height', $(window).height());
		$(window).resize(function(){
			$('.js-fullheight').css('height', $(window).height());
		});
	};
	fullHeight();

	/* ── Sidebar toggle ───────────────────────────────────── */
	$('#sidebarCollapse').on('click', function () {
		$('#sidebar').toggleClass('active');
	});

	/* ── Flash toast ──────────────────────────────────────── */
	var $toast = $('#flash-toast');
	if ($toast.length) {
		// Auto-dismiss after 4 s
		setTimeout(function () { $toast.fadeOut(300); }, 4000);
		$toast.find('.flash-toast__close').on('click', function () {
			$toast.fadeOut(200);
		});
	}

	/* ── Confirmation modal ───────────────────────────────── */
	// Usage: add data-confirm="true" to any button/submit.
	// Optionally add data-confirm-form="#formId" and data-confirm-msg="…"
	$(document).on('click', '[data-confirm]', function (e) {
		e.preventDefault();
		var $btn    = $(this);
		var formId  = $btn.data('confirm-form');
		var msg     = $btn.data('confirm-msg') || '¿Seguro que deseas borrar este registro? Esta acción no se puede deshacer.';

		$('#modalConfirmBody').text(msg);
		$('#modalConfirmOk').off('click').on('click', function () {
			$('#modalConfirm').modal('hide');
			if (formId) {
				$(formId).submit();
			} else {
				// Re-trigger the original element without data-confirm
				$btn.removeAttr('data-confirm').trigger('click');
			}
		});
		$('#modalConfirm').modal('show');
	});

	/* ── AJAX search helper ───────────────────────────────── */
	// Usage: ajaxSearch({ url, inputId, resultId, spinId })
	window.ajaxSearch = function (opts) {
		var $input  = $('#' + opts.inputId);
		var $result = $('#' + opts.resultId);
		var $spin   = opts.spinId ? $('#' + opts.spinId) : $([]);

		function load(query) {
			$spin.removeClass('d-none');
			$.ajax({
				url:    opts.url,
				method: 'POST',
				data:   { query: query || '' },
				success: function (data) {
					$result.html(data);
				},
				error: function () {
					$result.html(
						'<div class="empty-state text-danger">' +
						'<i class="fa fa-exclamation-triangle fa-2x mb-3 d-block"></i>' +
						'<p>Error al cargar los datos. Intenta de nuevo.</p></div>'
					);
				},
				complete: function () {
					$spin.addClass('d-none');
				}
			});
		}

		load();
		$input.on('keyup input', function () { load($(this).val()); });
	};

	/* ── Bootstrap 4 client-side validation ──────────────── */
	$(document).on('submit', 'form[data-validate]', function (e) {
		var form = this;
		if (!form.checkValidity()) {
			e.preventDefault();
			e.stopPropagation();
		}
		$(form).addClass('was-validated');
	});

})(jQuery);
