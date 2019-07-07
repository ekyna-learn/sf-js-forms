import $ from 'jquery';
import './collection';

let $country = $('select[name="event[country]"]'),
    $city = $('select[name="event[city]"]');

function onCountryChange() {
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
}

$country.on('change', onCountryChange);

onCountryChange();
