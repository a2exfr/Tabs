$(document).ready(function () {

	$('.cp').each(function () {
		var col = $(this).val()
		$(this).parent().css('background-color', col)
	})

	$('.cp').colorpicker({ format: 'hex' })
		.on('changeColor', function (e) {

			var col = e.color.toHex()
			$(this).parent().css('background-color', col)

		})
})
