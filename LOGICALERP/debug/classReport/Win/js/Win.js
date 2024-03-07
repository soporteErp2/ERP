/**
 * win - Libreria para web aplicaciones RIA
 * @version v0.0.1
 * @link    https://github.com/jhon3rick/Win.js#readme
 * @author  Jhon Marroquin (Twitter @jhon3rick || email jhon3rick@gmail.com)
 * @license (MIT)
 */

/*
 * Win.js
 * @namespeace Win
 *
 * @version 0.1
 * @author Jhon Marroquin || @jhon3rick
 * @author Jonatan Herran || @jonatan2874
 *
 */

(function() {
  "use strict";
  var Win;

  Win = (function() {
    var $W, CLASS_SELECTOR, ELEMENT_TYPES, EMPTY_ARRAY, ID_SELECTOR, IS_HTML_FRAGMENT, OBJECT_PROTOTYPE, TAG_SELECTOR, _getDOMObject, _instance;
    EMPTY_ARRAY = [];
    OBJECT_PROTOTYPE = Object.prototype;
    IS_HTML_FRAGMENT = /^\s*<(\w+|!)[^>]*>/;
    ELEMENT_TYPES = [1, 9, 11];
    CLASS_SELECTOR = /^\.([\w-]+)$/;
    ID_SELECTOR = /^#[\w\d-]+$/;
    TAG_SELECTOR = /^[\w-]+$/;

    /*
    		Basic Instance of WinJs
    		@method $W
    		@param  {string/instance} [OPTIONAL] Selector for handler
    		@param  {string} [OPTIONAL] Children in selector
     */
    $W = function(selector, children) {
      var dom;
      if (!selector) {
        return _instance();
      } else if ($W.toType(selector) === "function") {
        return $W(document).ready(selector);
      } else {
        dom = _getDOMObject(selector, children);
        return _instance(dom, selector);
      }
    };
    $W.query = function(domain, selector) {
      var elements;
      if (CLASS_SELECTOR.test(selector)) {
        elements = domain.getElementsByClassName(selector.replace(".", ""));
      } else if (TAG_SELECTOR.test(selector)) {
        elements = domain.getElementsByTagName(selector);
      } else if (ID_SELECTOR.test(selector) && domain === document) {
        elements = domain.getElementById(selector.replace("#", ""));
        if (!elements) {
          elements = [];
        }
      } else {
        elements = domain.querySelectorAll(selector);
      }
      if (elements.nodeType) {
        return [elements];
      } else {
        return Array.prototype.slice.call(elements);
      }
    };
    $W.toType = function(obj) {
      var match;
      match = OBJECT_PROTOTYPE.toString.call(obj).match(/\s([a-z|A-Z]+)/);
      if (match.length > 1) {
        return match[1].toLowerCase();
      } else {
        return "object";
      }
    };
    $W.each = function(elements, callback) {
      var element, i, j, key, len;
      i = void 0;
      key = void 0;
      if ($W.toType(elements) === "array") {
        for (i = j = 0, len = elements.length; j < len; i = ++j) {
          element = elements[i];
          if (callback.call(element, i, element) === false) {
            elements;
          }
        }
      } else {
        for (key in elements) {
          if (callback.call(elements[key], key, elements[key]) === false) {
            elements;
          }
        }
      }
      return elements;
    };
    _instance = function(dom, selector) {
      if (selector == null) {
        selector = "";
      }
      dom = dom || EMPTY_ARRAY;
      dom.selector = selector;
      dom.__proto__ = _instance.prototype;
      return dom;
    };
    _getDOMObject = function(selector, children) {
      var domain, type;
      domain = null;
      type = $W.toType(selector);
      if (type === "array") {
        domain = _compact(selector);
      } else if (type === "string" && IS_HTML_FRAGMENT.test(selector)) {
        domain = _fragment(selector.trim(), RegExp.$1);
        selector = null;
      } else if (type === "string") {
        domain = $W.query(document, selector);
        if (children) {
          if (domain.length === 1) {
            domain = $W.query(domain[0], children);
          } else {
            domain = $W.map(function() {
              return $W.query(domain, children);
            });
          }
        }
      } else if (ELEMENT_TYPES.indexOf(selector.nodeType) >= 0 || selector === window) {
        domain = [selector];
        selector = null;
      }
      return domain;
    };
    _instance.prototype = $W.fn = {};
    $W.fn.each = function(callback) {
      this.forEach(function(element, index) {
        return callback.call(element, index, element);
      });
      return this;
    };
    $W.fn.forEach = EMPTY_ARRAY.forEach;
    $W.version = "0.0.1";
    return $W;
  })();

  this.Win = this.$W = Win;

  if (typeof module !== "undefined" && module !== null) {
    module.exports = Win;
  }

}).call(this);
