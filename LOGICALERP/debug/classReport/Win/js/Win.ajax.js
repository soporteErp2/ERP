/**
 * win - Libreria para web aplicaciones RIA
 * @version v0.0.1
 * @link    https://github.com/jhon3rick/Win.js#readme
 * @author  Jhon Marroquin (Twitter @jhon3rick || email jhon3rick@gmail.com)
 * @license (MIT)
 */

/*
 * Request XHR
 */

(function() {
  $W.Ajax = (function() {
    return {
      request: function(obj) {
        var bodyXhr, method, obj_loading, parametros, value, xhr;
        parametros = '';
        if (typeof obj.id_ventana === 'undefined') {
          console.warn("Debe enviar el parametro id_ventana, para poder mostrar el loading");
          return;
        }
        if (typeof obj.params !== 'undefined') {
          for (value in obj.params) {
            parametros += parametros === '' ? value + "=" + obj.params[value] : "&" + value + "=" + obj.params[value];
          }
        }
        if (obj.modal === true || obj.modal === '') {
          obj_loading = {
            id_ventana: obj.id_ventana,
            text: obj.text,
            loader: obj.loader,
            icono: obj.iconFinish,
            icono: obj.iconFinish,
            texto: obj.textFinish,
            duracion: obj.duracionFinish,
            estado: 'on'
          };
          Win.loading(obj_loading);
        }
        xhr = new XMLHttpRequest;
        bodyXhr = obj.url + '?' + parametros;
        method = obj.method || 'POST';
        xhr.open(method, bodyXhr, true);
        xhr.onreadystatechange = function() {
          var response;
          if (xhr.readyState === 4) {
            response = xhr.responseText;
            if ((obj.modal === true || obj.modal === '') && (obj.autoClose === true || obj.autoClose === '')) {
              obj_loading.estado = 'off';
              Win.loading(obj_loading);
            }
            return obj.success(response, xhr);
          } else {
            if ((obj.modal === true || obj.modal === '') && (obj.autoClose === true || obj.autoClose === '')) {
              obj_loading.estado = 'off';
              Win.loading(obj_loading);
            }
            return obj.failure(xhr);
          }
        };
        return xhr.send(null);
      },
      load: function(dom_element, obj) {
        var bodyXhr, eval_script, extract_script, method, parametros, tagScript, text, value, xhr;
        parametros = '';
        tagScript = '(?:<script.*?>)((\n|\r|.)*?)(?:<\/script>)';
        if (typeof obj.params !== 'undefined') {
          for (value in obj.params) {
            parametros += parametros === '' ? value + "=" + obj.params[value] : "&" + value + "=" + obj.params[value];
          }
        }
        text = obj.text || 'cargando...';
        dom_element.innerHTML = "<div class=\"win-content-min-load\" > <div class=\"win-content-min-load-img\"> <div class=\"win-min-load-ajax\"></div> </div> <div class=\"win-content-min-load-label\">" + text + "</div> </div>";
        xhr = new XMLHttpRequest;
        bodyXhr = obj.url + '?' + parametros;
        method = obj.method || 'POST';
        xhr.open(method, bodyXhr, true);
        xhr.onreadystatechange = function() {
          var i, j, len, responseHtml, results, script;
          if (xhr.readyState === 4) {
            responseHtml = xhr.responseText;
            dom_element.innerHTML = responseHtml;
            script = dom_element.getElementsByTagName('script');
            results = [];
            for (j = 0, len = script.length; j < len; j++) {
              i = script[j];
              tagScript = document.createElement('script');
              i.parentNode.replaceChild(tagScript, i);
              results.push(tagScript.innerHTML = i.innerHTML);
            }
            return results;
          }
        };
        xhr.send(null);
        extract_script = function(string) {
          var SearchExp;
          SearchExp = new RegExp(tagScript, 'img');
          return string.replace(SearchExp, '');
        };
        return eval_script = function(string) {
          var script, scripts;
          scripts = string.match(new RegExp(tagScript, 'img')) || [];
          script = '';
          scripts.map(function(script_map) {
            return script += (script_map.match(new RegExp(tagScript, 'im')) || ['', ''])[1];
          });
          return eval(script);
        };
      }
    };
  })();

}).call(this);
