import $ from 'jquery';

let addField = '[data-collection-role="add"]',
    removeField = '[data-collection-role="remove"]',
    moveUpField = '[data-collection-role="move-up"]',
    moveDownField = '[data-collection-role="move-down"]',
    CollectionAdd = function (el) {
        $(el).on('click', addField, this.addField);
    },
    CollectionRemove = function (el) {
        $(el).on('click', removeField, this.removeField);
    },
    CollectionMoveUp = function (el) {
        $(el).on('click', moveUpField, this.moveUpField);
    },
    CollectionMoveDown = function (el) {
        $(el).on('click', moveDownField, this.moveDownField);
    };

function collectionUpdatePositions($collection) {
    let selector = '[data-collection="' + $collection.attr('id') + '"]',
        $list = $collection.find('.app-collection-child-container').first().find('> .app-collection-child'),
        max = $list.length - 1;

    $list.each(function (index, li) {
        let $li = $(li);
        $li.find(selector + '[data-collection-role="position"]').first().val(index);
        if (index === 0) {
            $li.find(selector + moveUpField).prop('disabled', true);
        } else {
            $li.find(selector + moveUpField).prop('disabled', false);
        }
        if (index === max) {
            $li.find(selector + moveDownField).prop('disabled', true);
        } else {
            $li.find(selector + moveDownField).prop('disabled', false);
        }
    });
}

CollectionAdd.prototype.addField = function (e) {
    e && e.preventDefault();

    let $this = $(this),
        selector = $this.attr('data-collection'),
        $collection = $('#' + selector),
        $list = $collection.find('.app-collection-child-container').first(),
        prototypeName = $this.attr('data-prototype-name'),
        widget = $('#' + $collection.attr('data-prototype')).text(),
        index = $collection.data('child-index');

    // If child index is not available as a collection data
    if (index === undefined) {
        index = -1;

        // Determine the next index
        let indexRegex = new RegExp(widget.match(/id="(.*?)"/)[1].replace(prototypeName, '(\\d+)'));

        $list.find('> .app-collection-child').each(function (i, child) {
            let match = parseInt($(child).attr('id').match(indexRegex)[1]);
            if (match > index) {
                index = match;
            }
        });
    }

    // Stores the current child index
    index++;
    $collection.data('child-index', index);

    // Builds the child widget
    let name = widget.match(/id="(.*?)"/),
        widgetRegex = new RegExp(prototypeName, "g");

    widget = widget.replace(widgetRegex, index);
    widget = widget.replace(/__id__/g, name[1].replace(widgetRegex, index));
    let $element = $(widget);
    $list.append($element);

    collectionUpdatePositions($collection);
};

CollectionRemove.prototype.removeField = function (e) {
    let $this = $(this),
        selector = $this.attr('data-collection');

    e && e.preventDefault();

    if ($this.data('confirm')) {
        if (!confirm($this.data('confirm'))) {
            return;
        }
    }

    let $element = $this.closest('.app-collection-child');

    $element.remove();

    let $collection = $('#' + selector);
    collectionUpdatePositions($collection);
};

CollectionMoveUp.prototype.moveUpField = function (e) {
    let $this = $(this),
        selector = $this.attr('data-collection');

    e && e.preventDefault();

    let $element = $this.closest('.app-collection-child');
    if (!$element.is(':first-child')) {
        let $prev = $element.prev();

        $prev.before($element.detach());

        let $collection = $('#' + selector);
        collectionUpdatePositions($collection);
    }
};

CollectionMoveDown.prototype.moveDownField = function (e) {
    let $this = $(this),
        selector = $this.attr('data-collection');

    e && e.preventDefault();

    $this.trigger('app-collection-field-moved-down');
    let $element = $this.closest('.app-collection-child');
    if (!$element.is(':last-child')) {
        let $next = $element.next();
        $next.after($element.detach());

        let $collection = $('#' + selector);
        collectionUpdatePositions($collection);
    }
};

let oldAdd = $.fn.addField;
let oldRemove = $.fn.removeField;
let oldMoveUp = $.fn.moveUpField;
let oldMoveDown = $.fn.moveDownField;

$.fn.addField = function (option) {
    return this.each(function () {
        let $this = $(this),
            data = $this.data('addfield')
        ;
        if (!data) {
            $this.data('addfield', (data = new CollectionAdd(this)));
        }
        if (typeof option === 'string') {
            data[option].call($this);
        }
    });
};

$.fn.removeField = function (option) {
    return this.each(function () {
        let $this = $(this),
            data = $this.data('removefield')
        ;
        if (!data) {
            $this.data('removefield', (data = new CollectionRemove(this)));
        }
        if (typeof option === 'string') {
            data[option].call($this);
        }
    });
};

$.fn.moveUpField = function (option) {
    return this.each(function () {
        let $this = $(this),
            data = $this.data('moveupfield')
        ;
        if (!data) {
            $this.data('moveupfield', (data = new CollectionMoveUp(this)));
        }
        if (typeof option === 'string') {
            data[option].call($this);
        }
    });
};

$.fn.moveDownField = function (option) {
    return this.each(function () {
        let $this = $(this),
            data = $this.data('movedownfield')
        ;
        if (!data) {
            $this.data('movedownfield', (data = new CollectionMoveDown(this)));
        }
        if (typeof option === 'string') {
            data[option].call($this);
        }
    });
};

$.fn.addField.Constructor = CollectionAdd;
$.fn.removeField.Constructor = CollectionRemove;
$.fn.moveUpField.Constructor = CollectionMoveUp;
$.fn.moveDownField.Constructor = CollectionMoveDown;

$.fn.addField.noConflict = function () {
    $.fn.addField = oldAdd;
    return this;
};
$.fn.removeField.noConflict = function () {
    $.fn.removeField = oldRemove;
    return this;
};
$.fn.moveUpField.noConflict = function () {
    $.fn.moveUpField = oldMoveUp;
    return this;
};
$.fn.moveDownField.noConflict = function () {
    $.fn.moveDownField = oldMoveDown;
    return this;
};

$(document).on('click.addfield.data-api', addField, CollectionAdd.prototype.addField);
$(document).on('click.removefield.data-api', removeField, CollectionRemove.prototype.removeField);
$(document).on('click.moveupfield.data-api', moveUpField, CollectionMoveUp.prototype.moveUpField);
$(document).on('click.movedownfield.data-api', moveDownField, CollectionMoveDown.prototype.moveDownField);

$('.app-collection').each(function (index, collection) {
    collectionUpdatePositions($(collection));
    $(collection).attr('data-initialized', 1);
});
