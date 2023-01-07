/**
 * FPCustomCode v1.0.0
 * https://github.com/flexpik/ps-custom-code
 * Copyright (c) 2022 FlexPik.com
 * Released under the MIT license
 * Date: 2022-11-06
 */

$(document).ready(function () {
  let codeJs = document.getElementById("code_js");
  let codeCss = document.getElementById("code_css");
  let globalJs = document.getElementById("global_js");
  let globalCss = document.getElementById("global_css");

  if (codeJs) {
    CodeMirror.fromTextArea(codeJs, {
      lineNumbers: true,
      mode: 'javascript',
      indentUnit: 4,
      indentWithTabs: true,
    });
  }

  if (codeCss) {
    CodeMirror.fromTextArea(codeCss, {
      lineNumbers: true,
      mode: 'css',
    });
  }

  if (globalJs) {
    CodeMirror.fromTextArea(globalJs, {
      lineNumbers: true,
      mode: 'javascript',
      indentUnit: 4,
      indentWithTabs: true,
    });
  }

  if (globalCss) {
    CodeMirror.fromTextArea(globalCss, {
      lineNumbers: true,
      mode: 'css',
    });
  }
});
