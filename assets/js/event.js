import $ from 'jquery';

let $country = $('select[name="event[country]"]'),
    $city = $('select[name="event[city]"]');

$country.on('change', function() {
    let xhr = $.getJSON('/city/choices', {
        countryId: $country.val(),
    });

    $city.prop('disabled', true);

    xhr.done(function(cities) {
        $city.empty();

        $.each(cities, function (index, city) {
            let $option = $('<option/>');
            $option.prop('value', city.id);
            $option.text(city.name);
            $city.append($option);
        });

        $city.prop('disabled', false);
    });
});