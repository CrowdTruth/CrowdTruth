/*
Copyright (C) 2014 Acquisio Inc. V0.1.1

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
of the Software, and to permit persons to whom the Software is furnished to do
so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

(function(root, factory) {
  // CommonJS support
  if (typeof exports === 'object') {
    module.exports = factory();
  }
  // AMD
  else if (typeof define === 'function' && define.amd) {
    define(['jquery'], factory);
  }
  // Browser globals
  else {
    factory(root.jQuery);
  }
}(this, function($) {
  'use strict';

  var defer = function defer(fn) {
    if (window.requestAnimationFrame) {
      window.requestAnimationFrame(fn);
    }
    else {
      setTimeout(fn, 0);
    }
  };

  // **********************************
  // Templates
  // **********************************
  var template = '\
    <button class="dropdown-checkbox-toggle" data-toggle="dropdown" href="#">Dropdown trigger </button>\
    <div class="dropdown-checkbox-content">\
      <div class="dropdown-checkbox-header">\
        <input class="checkbox-all" type="checkbox"><input type="text" placeholder="Search" class="search"/>\
      </div>\
      <ul class="dropdown-checkbox-menu"></ul>\
    </div>';

  var templateOption = '<li><div class="layout"><input type="checkbox"/><label></label></div></li>';
  var templateNoResult = '<li><div class="layout"><label>No results.</label></div></li>';
  var templateNbSelected = ' <span class="dropdown-checkbox-nbselected"></span>';

  // **********************************
  // Constructor
  // **********************************
  var DropdownCheckbox = function(element, options) {
    // Create dropdown-checkbox
    $(element).html(template);
    $(element).addClass('dropdown-checkbox dropdown');

    this.$element = $(element).find('.dropdown-checkbox-toggle');
    this.$parent = $(element);
    this.$list = this.$parent.find('ul');
    this.elements = [];
    this.hasChanges = false;

    this.showNbSelected = false;

    // Set options if exist
    if (typeof options === 'object') {
      this.$element.text(options.title);
      this.$element.addClass(options.btnClass);
      this.autosearch = options.autosearch;
      this.elements = options.data || [];
      this._sort = options.sort || this._sort;
      this.sortOptions = options.sortOptions;
      this.hideHeader = options.hideHeader || options.hideHeader === undefined ? true : false;
      this.templateButton = options.templateButton;
      this.showNbSelected = options.showNbSelected || false;

      this._query = options.query || this._query;
      this._queryMethod = options.httpMethod || 'GET';
      this._queryParse = options.queryParse ||  this._queryParse;
      this._queryError = options.queryError ||   function() {};
      this._queryUrl = options.queryUrl;
    }

    this.$element.append(templateNbSelected);

    if (this.templateButton) {
      this.$element.remove();
      this.$parent.prepend(this.templateButton);
      this.$element = this.$parent.find('.dropdown-checkbox-toggle');
    }

    // Add toggle for dropdown
    this.$element.attr('data-toggle', 'dropdown');

    // Hide searchbox if needs
    if (this.hideHeader) this.$parent.find('.dropdown-checkbox-header').remove();

    // Prevent clicks on content
    this.$parent.find('.dropdown-checkbox-content').on('click.dropdown-checkbox.data-api', function(e) {
      e.stopPropagation();
    });

    // Open panel when the link is clicked
    this.$element.on('click.dropdown-checkbox.data-api', $.proxy(function() {
      // Remember current state
      var isOpened = this.$parent.hasClass('open');

      // Close all dropdown (bootstrap include)
      $('.dropdown').removeClass('open');

      // Reset last state
      if (isOpened) this.$parent.addClass('open');

      // Switch to next state
      this.$parent.toggleClass('open');

      // Notify changes on close
      if (this.hasChanges) this.$parent.trigger('change:dropdown-checkbox');

      this.hasChanges = false;
      return false;
    }, this));

    // Check or uncheck all checkbox
    this.$parent.find('.checkbox-all').on('change.dropdown-checkbox.data-api', $.proxy(function(event) {
      this.onClickCheckboxAll(event);
      this._showNbSelected();
    }, this));

    // Events on document
    // - Close panel when click out
    // - Catch keyup events in search box
    // - Catch click on checkbox
    $(document).on('click.dropdown-checkbox.data-api', $.proxy(function() {
      this.$parent.removeClass('open');

      // Notify changes on close
      if (this.hasChanges) this.$parent.trigger('change:dropdown-checkbox');
      this.hasChanges = false;
    }, this));

    this.$parent.find('.dropdown-checkbox-header').on('keyup.dropdown-checkbox.data-api', $.proxy(DropdownCheckbox.prototype.onKeyup, this));
    this.$parent.find('ul').delegate('li input[type=checkbox]', 'click.dropdown-checkbox.data-api', $.proxy(function(event) {
      this.onClickCheckbox(event);
      this._showNbSelected();
    }, this));

    this._reset(this.elements);
    this._showNbSelected();
  };

  // **********************************
  // DropdownCheckbox object
  // **********************************
  DropdownCheckbox.prototype = {
    constructor: DropdownCheckbox,

    // ----------------------------------
    // Methods to override
    // ----------------------------------
    _sort: function(elements) {
      return elements;
    },

    _query: function(type, url, success, error) {
      return $.ajax({
        type: type,
        url: url + '?q=' + this.word,
        dataType: 'json',
        cache: false,
        contentType: 'application/json',
        success: $.proxy(success, this),
        error: error
      });
    },

    _querySuccess: function(data) {
      var results = this._queryParse(data);
      if (results.length  > 0) return this._reset(results);
      return this.$list.html(templateNoResult);
    },

    _queryParse: function(data) {
      return data;
    },

    // ----------------------------------
    // Internal methods
    // ----------------------------------
    _removeElements: function(ids) {
      this._isValidArray(ids);
      var tmp = [],
        toAdd = true;
      for (var i = 0; i < this.elements.length; i++) {
        for (var j = 0; j < ids.length; j++) {
          if (ids[j] === parseInt(this.elements[i].id, 10)) toAdd = false;
        }
        if (toAdd) tmp.push(this.elements[i]);
        toAdd = true;
      }
      this.elements = tmp;
    },

    _getCheckbox: function(isChecked, isAll) {
      var results = [];
      for (var i = 0; i < this.elements.length; i++) {
        if (isChecked === this.elements[i].isChecked || isAll)
          results.push(this.elements[i]);
      }
      return results;
    },

    _isValidArray: function(arr) {
      if (!$.isArray(arr)) throw '[DropdownCheckbox] Requires array.';
    },

    _findMatch: function(word, elements) {
      var results = [];
      for (var i = 0; i < elements.length; i++) {
        if (elements[i].label.toLowerCase().search(word.toLowerCase()) !== -1) results.push(elements[i]);
      }
      return results;
    },

    _setCheckbox: function(isChecked, id) {
      for (var i = 0; i < this.elements.length; i++) {
        if (id == this.elements[i].id) {
          this.elements[i].isChecked = isChecked;
          break;
        }
      }
    },

    _refreshCheckboxAll: function() {
      var $elements = this.$element.parents('.dropdown-checkbox').find('ul li input[type=checkbox]'),
        willChecked;
      $elements.each(function() {
        willChecked = willChecked || $(this).prop('checked');
      });
      this.$element.parents('.dropdown-checkbox').find('.checkbox-all').prop('checked', willChecked);
    },

    _resetSearch: function() {
      this.$parent.find('.search').val('');
      this._reset(this.elements);
    },

    _createListItem: function(item) {
      var id = item.id,
          label = item.label,
          isChecked = item.isChecked,
          uuid = new Date().getTime() * Math.random();

      var node = this.listItemPrototype.cloneNode(true);
      var container = node.firstChild;

      $(node).data('id', id);
      container.firstChild.id = uuid;
      container.firstChild.checked = isChecked;
      container.lastChild.textContent = label;
      container.lastChild.setAttribute('for', uuid);
      return node;
    },

    _appendOne: function(item) {
      this.$list.append(this._createListItem(item));
    },

    _append: function(elements) {
      // Create a list element we can clone
      if (!this.listItemPrototype) this.listItemPrototype = $(templateOption)[0];

      if (!$.isArray(elements)) elements = [elements];

      var len = elements.length;
      var batchsize = 100;
      var createListItem = this._createListItem.bind(this);
      var $list = this.$list;
      var i;

      elements = this._sort(elements, this.sortOptions);

      (function appendBatch(index) {
        var fragment = document.createDocumentFragment();
        for (i = index; i < Math.min(index + batchsize, len); i++) {
          fragment.appendChild(createListItem(elements[i]));
        }
        $list[0].appendChild(fragment);
        if (i < len) defer(appendBatch.bind(null, i));
      })(0);

      this._showNbSelected();
    },

    _reset: function(elements) {
      this._isValidArray(elements);
      this.$list.empty();
      this._append(this._sort(elements));
      this._refreshCheckboxAll();
    },

    _showNbSelected: function() {
      if (this.showNbSelected) {
        this.$element.find('.dropdown-checkbox-nbselected')
          .html('(' + this._getCheckbox(true, false).length + ')');
      }
    },
    // ----------------------------------
    // Event methods
    // ----------------------------------
    onKeyup: function(event) {
      var keyCode = event.keyCode,
        word = this.word = $(event.target).val();

      if (word.length < 1 && keyCode === 8) {
        return this._reset(this.elements);
      }

      if (keyCode === 27) {
        return this._resetSearch();
      }

      if (this.autosearch || keyCode === 13) {
        if (this._queryUrl) {
          this._query(this._queryMethod,
                      this._queryUrl,
                      this._querySuccess,
                      this._queryError);
        } else {
          var results = this._findMatch(word, this.elements);
          if (results.length  > 0) return this._reset(results);
          return this.$list.html(templateNoResult);
        }
      }
    },

    onClickCheckboxAll: function(event) {
      var isChecked = $(event.target).is(':checked'),
        $elements = this.$parent.find('ul li'),
        self = this;
      $elements.each(function() {
        $(this).find('input[type=checkbox]').prop('checked', isChecked);
        self._setCheckbox(isChecked, $(this).data('id'));
      });
      this.$parent.trigger('checked:all', isChecked);
      isChecked ? this.$parent.trigger('check:all') : this.$parent.trigger('uncheck:all');

      // Notify changes
      this.hasChanges = true;
    },

    onClickCheckbox: function(event) {
      this._setCheckbox($(event.target).prop('checked'),
                        $(event.target).parent().parent().data('id'));
      this._refreshCheckboxAll();
      this.$parent.trigger('checked', $(event.target).prop('checked'));
      $(event.target).prop('checked') ? this.$parent.trigger('check:checkbox') : this.$parent.trigger('uncheck:checkbox');

      // Notify changes
      this.hasChanges = true;
    },

    // ----------------------------------
    // External methods
    // ----------------------------------
    checked: function() {
      return this._getCheckbox(true);
    },

    unchecked: function() {
      return this._getCheckbox(false);
    },

    items: function() {
      return this._getCheckbox(undefined, true);
    },

    append: function(elements) {
      if (!$.isArray(elements)) {
        this.elements.push(elements);
      } else {
        for (var i = 0; i < elements.length; i++)
          this.elements.push(elements[i]);
      }

      elements = this._sort(elements);

      this._append(elements);

      // Notify changes
      this.hasChanges = true;
    },

    remove: function(ids) {
      if (!$.isArray(ids)) ids = [ids];
      this._isValidArray(ids);
      this._removeElements(ids);
      this._reset(this.elements);

      // Notify changes
      this.hasChanges = true;
    },

    reset: function(elements) {
      if (!$.isArray(elements)) {
        this.elements = [elements];
      } else {
        this.elements = elements;
      }

      this._reset(elements);

      // Notify changes
      this.hasChanges = true;
    }
  };

  // **********************************
  // Add DropdownCheckbox as plugin for JQuery
  // **********************************
  $.fn.dropdownCheckbox = function(option, more) {
    var $this = $(this),
      data = $this.data('dropdownCheckbox'),
      options = typeof option == 'object' && option;

    if (!data) $this.data('dropdownCheckbox', (data = new DropdownCheckbox(this, options)));
    if (typeof option == 'string') return data[option](more);
    return this;
  };

  $.fn.dropdownCheckbox.Constructor = DropdownCheckbox;

}));