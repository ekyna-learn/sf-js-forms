$(document).ready(function() {
    let $country = $('select[name="event[country]"]'),
        $city = $('select[name="event[city]"]');

    $country.on('change', function() {
        let xhr = $.getJSON('/city/choices', {
            countryId: $country.val(),
        });

        xhr.done(function(cities) {
            $city.empty();

            $.each(cities, function (index, city) {
                let $option = $('<option/>');
                $option.prop('value', city.id);
                $option.text(city.name);
                $city.append($option);
            });
        });
    });
});
