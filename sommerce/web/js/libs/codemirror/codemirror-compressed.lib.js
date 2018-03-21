"use strict";

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/* CodeMirror - Minified & Bundled
 Generated on 6/28/2016 with http://codemirror.net/doc/compress.html
 Version: HEAD

 Modes:
 - css.js
 - htmlembedded.js
 - htmlmixed.js
 - javascript.js
 - php.js
 Add-ons:
 - html-hint.js
 - simple.js
 Keymaps:
 - sublime.js
 */

!function (a) {
  "object" == (typeof exports === "undefined" ? "undefined" : _typeof(exports)) && "object" == (typeof module === "undefined" ? "undefined" : _typeof(module)) ? a(require("../../lib/codemirror")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror"], a) : a(CodeMirror);
}(function (a) {
  "use strict";
  function b(a) {
    for (var b = {}, c = 0; c < a.length; ++c) {
      b[a[c]] = !0;
    }return b;
  }function x(a, b) {
    for (var d, c = !1; null != (d = a.next());) {
      if (c && "/" == d) {
        b.tokenize = null;break;
      }c = "*" == d;
    }return ["comment", "comment"];
  }a.defineMode("css", function (b, c) {
    function u(a, b) {
      return s = b, a;
    }function v(a, b) {
      var c = a.next();if (f[c]) {
        var d = f[c](a, b);if (d !== !1) return d;
      }return "@" == c ? (a.eatWhile(/[\w\\\-]/), u("def", a.current())) : "=" == c || ("~" == c || "|" == c) && a.eat("=") ? u(null, "compare") : '"' == c || "'" == c ? (b.tokenize = w(c), b.tokenize(a, b)) : "#" == c ? (a.eatWhile(/[\w\\\-]/), u("atom", "hash")) : "!" == c ? (a.match(/^\s*\w*/), u("keyword", "important")) : /\d/.test(c) || "." == c && a.eat(/\d/) ? (a.eatWhile(/[\w.%]/), u("number", "unit")) : "-" !== c ? /[,+>*\/]/.test(c) ? u(null, "select-op") : "." == c && a.match(/^-?[_a-z][_a-z0-9-]*/i) ? u("qualifier", "qualifier") : /[:;{}\[\]\(\)]/.test(c) ? u(null, c) : "u" == c && a.match(/rl(-prefix)?\(/) || "d" == c && a.match("omain(") || "r" == c && a.match("egexp(") ? (a.backUp(1), b.tokenize = x, u("property", "word")) : /[\w\\\-]/.test(c) ? (a.eatWhile(/[\w\\\-]/), u("property", "word")) : u(null, null) : /[\d.]/.test(a.peek()) ? (a.eatWhile(/[\w.%]/), u("number", "unit")) : a.match(/^-[\w\\\-]+/) ? (a.eatWhile(/[\w\\\-]/), a.match(/^\s*:/, !1) ? u("variable-2", "variable-definition") : u("variable-2", "variable")) : a.match(/^\w+-/) ? u("meta", "meta") : void 0;
    }function w(a) {
      return function (b, c) {
        for (var e, d = !1; null != (e = b.next());) {
          if (e == a && !d) {
            ")" == a && b.backUp(1);break;
          }d = !d && "\\" == e;
        }return (e == a || !d && ")" != a) && (c.tokenize = null), u("string", "string");
      };
    }function x(a, b) {
      return a.next(), a.match(/\s*[\"\')]/, !1) ? b.tokenize = null : b.tokenize = w(")"), u(null, "(");
    }function y(a, b, c) {
      this.type = a, this.indent = b, this.prev = c;
    }function z(a, b, c, d) {
      return a.context = new y(c, b.indentation() + (d === !1 ? 0 : e), a.context), c;
    }function A(a) {
      return a.context.prev && (a.context = a.context.prev), a.context.type;
    }function B(a, b, c) {
      return E[c.context.type](a, b, c);
    }function C(a, b, c, d) {
      for (var e = d || 1; e > 0; e--) {
        c.context = c.context.prev;
      }return B(a, b, c);
    }function D(a) {
      var b = a.current().toLowerCase();t = p.hasOwnProperty(b) ? "atom" : o.hasOwnProperty(b) ? "keyword" : "variable";
    }var d = c.inline;c.propertyKeywords || (c = a.resolveMode("text/css"));var s,
        t,
        e = b.indentUnit,
        f = c.tokenHooks,
        g = c.documentTypes || {},
        h = c.mediaTypes || {},
        i = c.mediaFeatures || {},
        j = c.mediaValueKeywords || {},
        k = c.propertyKeywords || {},
        l = c.nonStandardPropertyKeywords || {},
        m = c.fontProperties || {},
        n = c.counterDescriptors || {},
        o = c.colorKeywords || {},
        p = c.valueKeywords || {},
        q = c.allowNested,
        r = c.supportsAtComponent === !0,
        E = {};return E.top = function (a, b, c) {
      if ("{" == a) return z(c, b, "block");if ("}" == a && c.context.prev) return A(c);if (r && /@component/.test(a)) return z(c, b, "atComponentBlock");if (/^@(-moz-)?document$/.test(a)) return z(c, b, "documentTypes");if (/^@(media|supports|(-moz-)?document|import)$/.test(a)) return z(c, b, "atBlock");if (/^@(font-face|counter-style)/.test(a)) return c.stateArg = a, "restricted_atBlock_before";if (/^@(-(moz|ms|o|webkit)-)?keyframes$/.test(a)) return "keyframes";if (a && "@" == a.charAt(0)) return z(c, b, "at");if ("hash" == a) t = "builtin";else if ("word" == a) t = "tag";else {
        if ("variable-definition" == a) return "maybeprop";if ("interpolation" == a) return z(c, b, "interpolation");if (":" == a) return "pseudo";if (q && "(" == a) return z(c, b, "parens");
      }return c.context.type;
    }, E.block = function (a, b, c) {
      if ("word" == a) {
        var d = b.current().toLowerCase();return k.hasOwnProperty(d) ? (t = "property", "maybeprop") : l.hasOwnProperty(d) ? (t = "string-2", "maybeprop") : q ? (t = b.match(/^\s*:(?:\s|$)/, !1) ? "property" : "tag", "block") : (t += " error", "maybeprop");
      }return "meta" == a ? "block" : q || "hash" != a && "qualifier" != a ? E.top(a, b, c) : (t = "error", "block");
    }, E.maybeprop = function (a, b, c) {
      return ":" == a ? z(c, b, "prop") : B(a, b, c);
    }, E.prop = function (a, b, c) {
      if (";" == a) return A(c);if ("{" == a && q) return z(c, b, "propBlock");if ("}" == a || "{" == a) return C(a, b, c);if ("(" == a) return z(c, b, "parens");if ("hash" != a || /^#([0-9a-fA-f]{3,4}|[0-9a-fA-f]{6}|[0-9a-fA-f]{8})$/.test(b.current())) {
        if ("word" == a) D(b);else if ("interpolation" == a) return z(c, b, "interpolation");
      } else t += " error";return "prop";
    }, E.propBlock = function (a, b, c) {
      return "}" == a ? A(c) : "word" == a ? (t = "property", "maybeprop") : c.context.type;
    }, E.parens = function (a, b, c) {
      return "{" == a || "}" == a ? C(a, b, c) : ")" == a ? A(c) : "(" == a ? z(c, b, "parens") : "interpolation" == a ? z(c, b, "interpolation") : ("word" == a && D(b), "parens");
    }, E.pseudo = function (a, b, c) {
      return "word" == a ? (t = "variable-3", c.context.type) : B(a, b, c);
    }, E.documentTypes = function (a, b, c) {
      return "word" == a && g.hasOwnProperty(b.current()) ? (t = "tag", c.context.type) : E.atBlock(a, b, c);
    }, E.atBlock = function (a, b, c) {
      if ("(" == a) return z(c, b, "atBlock_parens");if ("}" == a || ";" == a) return C(a, b, c);if ("{" == a) return A(c) && z(c, b, q ? "block" : "top");if ("interpolation" == a) return z(c, b, "interpolation");if ("word" == a) {
        var d = b.current().toLowerCase();t = "only" == d || "not" == d || "and" == d || "or" == d ? "keyword" : h.hasOwnProperty(d) ? "attribute" : i.hasOwnProperty(d) ? "property" : j.hasOwnProperty(d) ? "keyword" : k.hasOwnProperty(d) ? "property" : l.hasOwnProperty(d) ? "string-2" : p.hasOwnProperty(d) ? "atom" : o.hasOwnProperty(d) ? "keyword" : "error";
      }return c.context.type;
    }, E.atComponentBlock = function (a, b, c) {
      return "}" == a ? C(a, b, c) : "{" == a ? A(c) && z(c, b, q ? "block" : "top", !1) : ("word" == a && (t = "error"), c.context.type);
    }, E.atBlock_parens = function (a, b, c) {
      return ")" == a ? A(c) : "{" == a || "}" == a ? C(a, b, c, 2) : E.atBlock(a, b, c);
    }, E.restricted_atBlock_before = function (a, b, c) {
      return "{" == a ? z(c, b, "restricted_atBlock") : "word" == a && "@counter-style" == c.stateArg ? (t = "variable", "restricted_atBlock_before") : B(a, b, c);
    }, E.restricted_atBlock = function (a, b, c) {
      return "}" == a ? (c.stateArg = null, A(c)) : "word" == a ? (t = "@font-face" == c.stateArg && !m.hasOwnProperty(b.current().toLowerCase()) || "@counter-style" == c.stateArg && !n.hasOwnProperty(b.current().toLowerCase()) ? "error" : "property", "maybeprop") : "restricted_atBlock";
    }, E.keyframes = function (a, b, c) {
      return "word" == a ? (t = "variable", "keyframes") : "{" == a ? z(c, b, "top") : B(a, b, c);
    }, E.at = function (a, b, c) {
      return ";" == a ? A(c) : "{" == a || "}" == a ? C(a, b, c) : ("word" == a ? t = "tag" : "hash" == a && (t = "builtin"), "at");
    }, E.interpolation = function (a, b, c) {
      return "}" == a ? A(c) : "{" == a || ";" == a ? C(a, b, c) : ("word" == a ? t = "variable" : "variable" != a && "(" != a && ")" != a && (t = "error"), "interpolation");
    }, { startState: function startState(a) {
        return { tokenize: null, state: d ? "block" : "top", stateArg: null, context: new y(d ? "block" : "top", a || 0, null) };
      }, token: function token(a, b) {
        if (!b.tokenize && a.eatSpace()) return null;var c = (b.tokenize || v)(a, b);return c && "object" == (typeof c === "undefined" ? "undefined" : _typeof(c)) && (s = c[1], c = c[0]), t = c, b.state = E[b.state](s, a, b), t;
      }, indent: function indent(a, b) {
        var c = a.context,
            d = b && b.charAt(0),
            f = c.indent;return "prop" != c.type || "}" != d && ")" != d || (c = c.prev), c.prev && ("}" != d || "block" != c.type && "top" != c.type && "interpolation" != c.type && "restricted_atBlock" != c.type ? (")" == d && ("parens" == c.type || "atBlock_parens" == c.type) || "{" == d && ("at" == c.type || "atBlock" == c.type)) && (f = Math.max(0, c.indent - e), c = c.prev) : (c = c.prev, f = c.indent)), f;
      }, electricChars: "}", blockCommentStart: "/*", blockCommentEnd: "*/", fold: "brace" };
  });var c = ["domain", "regexp", "url", "url-prefix"],
      d = b(c),
      e = ["all", "aural", "braille", "handheld", "print", "projection", "screen", "tty", "tv", "embossed"],
      f = b(e),
      g = ["width", "min-width", "max-width", "height", "min-height", "max-height", "device-width", "min-device-width", "max-device-width", "device-height", "min-device-height", "max-device-height", "aspect-ratio", "min-aspect-ratio", "max-aspect-ratio", "device-aspect-ratio", "min-device-aspect-ratio", "max-device-aspect-ratio", "color", "min-color", "max-color", "color-index", "min-color-index", "max-color-index", "monochrome", "min-monochrome", "max-monochrome", "resolution", "min-resolution", "max-resolution", "scan", "grid", "orientation", "device-pixel-ratio", "min-device-pixel-ratio", "max-device-pixel-ratio", "pointer", "any-pointer", "hover", "any-hover"],
      h = b(g),
      i = ["landscape", "portrait", "none", "coarse", "fine", "on-demand", "hover", "interlace", "progressive"],
      j = b(i),
      k = ["align-content", "align-items", "align-self", "alignment-adjust", "alignment-baseline", "anchor-point", "animation", "animation-delay", "animation-direction", "animation-duration", "animation-fill-mode", "animation-iteration-count", "animation-name", "animation-play-state", "animation-timing-function", "appearance", "azimuth", "backface-visibility", "background", "background-attachment", "background-blend-mode", "background-clip", "background-color", "background-image", "background-origin", "background-position", "background-repeat", "background-size", "baseline-shift", "binding", "bleed", "bookmark-label", "bookmark-level", "bookmark-state", "bookmark-target", "border", "border-bottom", "border-bottom-color", "border-bottom-left-radius", "border-bottom-right-radius", "border-bottom-style", "border-bottom-width", "border-collapse", "border-color", "border-image", "border-image-outset", "border-image-repeat", "border-image-slice", "border-image-source", "border-image-width", "border-left", "border-left-color", "border-left-style", "border-left-width", "border-radius", "border-right", "border-right-color", "border-right-style", "border-right-width", "border-spacing", "border-style", "border-top", "border-top-color", "border-top-left-radius", "border-top-right-radius", "border-top-style", "border-top-width", "border-width", "bottom", "box-decoration-break", "box-shadow", "box-sizing", "break-after", "break-before", "break-inside", "caption-side", "clear", "clip", "color", "color-profile", "column-count", "column-fill", "column-gap", "column-rule", "column-rule-color", "column-rule-style", "column-rule-width", "column-span", "column-width", "columns", "content", "counter-increment", "counter-reset", "crop", "cue", "cue-after", "cue-before", "cursor", "direction", "display", "dominant-baseline", "drop-initial-after-adjust", "drop-initial-after-align", "drop-initial-before-adjust", "drop-initial-before-align", "drop-initial-size", "drop-initial-value", "elevation", "empty-cells", "fit", "fit-position", "flex", "flex-basis", "flex-direction", "flex-flow", "flex-grow", "flex-shrink", "flex-wrap", "float", "float-offset", "flow-from", "flow-into", "font", "font-feature-settings", "font-family", "font-kerning", "font-language-override", "font-size", "font-size-adjust", "font-stretch", "font-style", "font-synthesis", "font-variant", "font-variant-alternates", "font-variant-caps", "font-variant-east-asian", "font-variant-ligatures", "font-variant-numeric", "font-variant-position", "font-weight", "grid", "grid-area", "grid-auto-columns", "grid-auto-flow", "grid-auto-rows", "grid-column", "grid-column-end", "grid-column-gap", "grid-column-start", "grid-gap", "grid-row", "grid-row-end", "grid-row-gap", "grid-row-start", "grid-template", "grid-template-areas", "grid-template-columns", "grid-template-rows", "hanging-punctuation", "height", "hyphens", "icon", "image-orientation", "image-rendering", "image-resolution", "inline-box-align", "justify-content", "left", "letter-spacing", "line-break", "line-height", "line-stacking", "line-stacking-ruby", "line-stacking-shift", "line-stacking-strategy", "list-style", "list-style-image", "list-style-position", "list-style-type", "margin", "margin-bottom", "margin-left", "margin-right", "margin-top", "marker-offset", "marks", "marquee-direction", "marquee-loop", "marquee-play-count", "marquee-speed", "marquee-style", "max-height", "max-width", "min-height", "min-width", "move-to", "nav-down", "nav-index", "nav-left", "nav-right", "nav-up", "object-fit", "object-position", "opacity", "order", "orphans", "outline", "outline-color", "outline-offset", "outline-style", "outline-width", "overflow", "overflow-style", "overflow-wrap", "overflow-x", "overflow-y", "padding", "padding-bottom", "padding-left", "padding-right", "padding-top", "page", "page-break-after", "page-break-before", "page-break-inside", "page-policy", "pause", "pause-after", "pause-before", "perspective", "perspective-origin", "pitch", "pitch-range", "play-during", "position", "presentation-level", "punctuation-trim", "quotes", "region-break-after", "region-break-before", "region-break-inside", "region-fragment", "rendering-intent", "resize", "rest", "rest-after", "rest-before", "richness", "right", "rotation", "rotation-point", "ruby-align", "ruby-overhang", "ruby-position", "ruby-span", "shape-image-threshold", "shape-inside", "shape-margin", "shape-outside", "size", "speak", "speak-as", "speak-header", "speak-numeral", "speak-punctuation", "speech-rate", "stress", "string-set", "tab-size", "table-layout", "target", "target-name", "target-new", "target-position", "text-align", "text-align-last", "text-decoration", "text-decoration-color", "text-decoration-line", "text-decoration-skip", "text-decoration-style", "text-emphasis", "text-emphasis-color", "text-emphasis-position", "text-emphasis-style", "text-height", "text-indent", "text-justify", "text-outline", "text-overflow", "text-shadow", "text-size-adjust", "text-space-collapse", "text-transform", "text-underline-position", "text-wrap", "top", "transform", "transform-origin", "transform-style", "transition", "transition-delay", "transition-duration", "transition-property", "transition-timing-function", "unicode-bidi", "vertical-align", "visibility", "voice-balance", "voice-duration", "voice-family", "voice-pitch", "voice-range", "voice-rate", "voice-stress", "voice-volume", "volume", "white-space", "widows", "width", "word-break", "word-spacing", "word-wrap", "z-index", "clip-path", "clip-rule", "mask", "enable-background", "filter", "flood-color", "flood-opacity", "lighting-color", "stop-color", "stop-opacity", "pointer-events", "color-interpolation", "color-interpolation-filters", "color-rendering", "fill", "fill-opacity", "fill-rule", "image-rendering", "marker", "marker-end", "marker-mid", "marker-start", "shape-rendering", "stroke", "stroke-dasharray", "stroke-dashoffset", "stroke-linecap", "stroke-linejoin", "stroke-miterlimit", "stroke-opacity", "stroke-width", "text-rendering", "baseline-shift", "dominant-baseline", "glyph-orientation-horizontal", "glyph-orientation-vertical", "text-anchor", "writing-mode"],
      l = b(k),
      m = ["scrollbar-arrow-color", "scrollbar-base-color", "scrollbar-dark-shadow-color", "scrollbar-face-color", "scrollbar-highlight-color", "scrollbar-shadow-color", "scrollbar-3d-light-color", "scrollbar-track-color", "shape-inside", "searchfield-cancel-button", "searchfield-decoration", "searchfield-results-button", "searchfield-results-decoration", "zoom"],
      n = b(m),
      o = ["font-family", "src", "unicode-range", "font-variant", "font-feature-settings", "font-stretch", "font-weight", "font-style"],
      p = b(o),
      q = ["additive-symbols", "fallback", "negative", "pad", "prefix", "range", "speak-as", "suffix", "symbols", "system"],
      r = b(q),
      s = ["aliceblue", "antiquewhite", "aqua", "aquamarine", "azure", "beige", "bisque", "black", "blanchedalmond", "blue", "blueviolet", "brown", "burlywood", "cadetblue", "chartreuse", "chocolate", "coral", "cornflowerblue", "cornsilk", "crimson", "cyan", "darkblue", "darkcyan", "darkgoldenrod", "darkgray", "darkgreen", "darkkhaki", "darkmagenta", "darkolivegreen", "darkorange", "darkorchid", "darkred", "darksalmon", "darkseagreen", "darkslateblue", "darkslategray", "darkturquoise", "darkviolet", "deeppink", "deepskyblue", "dimgray", "dodgerblue", "firebrick", "floralwhite", "forestgreen", "fuchsia", "gainsboro", "ghostwhite", "gold", "goldenrod", "gray", "grey", "green", "greenyellow", "honeydew", "hotpink", "indianred", "indigo", "ivory", "khaki", "lavender", "lavenderblush", "lawngreen", "lemonchiffon", "lightblue", "lightcoral", "lightcyan", "lightgoldenrodyellow", "lightgray", "lightgreen", "lightpink", "lightsalmon", "lightseagreen", "lightskyblue", "lightslategray", "lightsteelblue", "lightyellow", "lime", "limegreen", "linen", "magenta", "maroon", "mediumaquamarine", "mediumblue", "mediumorchid", "mediumpurple", "mediumseagreen", "mediumslateblue", "mediumspringgreen", "mediumturquoise", "mediumvioletred", "midnightblue", "mintcream", "mistyrose", "moccasin", "navajowhite", "navy", "oldlace", "olive", "olivedrab", "orange", "orangered", "orchid", "palegoldenrod", "palegreen", "paleturquoise", "palevioletred", "papayawhip", "peachpuff", "peru", "pink", "plum", "powderblue", "purple", "rebeccapurple", "red", "rosybrown", "royalblue", "saddlebrown", "salmon", "sandybrown", "seagreen", "seashell", "sienna", "silver", "skyblue", "slateblue", "slategray", "snow", "springgreen", "steelblue", "tan", "teal", "thistle", "tomato", "turquoise", "violet", "wheat", "white", "whitesmoke", "yellow", "yellowgreen"],
      t = b(s),
      u = ["above", "absolute", "activeborder", "additive", "activecaption", "afar", "after-white-space", "ahead", "alias", "all", "all-scroll", "alphabetic", "alternate", "always", "amharic", "amharic-abegede", "antialiased", "appworkspace", "arabic-indic", "armenian", "asterisks", "attr", "auto", "avoid", "avoid-column", "avoid-page", "avoid-region", "background", "backwards", "baseline", "below", "bidi-override", "binary", "bengali", "blink", "block", "block-axis", "bold", "bolder", "border", "border-box", "both", "bottom", "break", "break-all", "break-word", "bullets", "button", "button-bevel", "buttonface", "buttonhighlight", "buttonshadow", "buttontext", "calc", "cambodian", "capitalize", "caps-lock-indicator", "caption", "captiontext", "caret", "cell", "center", "checkbox", "circle", "cjk-decimal", "cjk-earthly-branch", "cjk-heavenly-stem", "cjk-ideographic", "clear", "clip", "close-quote", "col-resize", "collapse", "color", "color-burn", "color-dodge", "column", "column-reverse", "compact", "condensed", "contain", "content", "content-box", "context-menu", "continuous", "copy", "counter", "counters", "cover", "crop", "cross", "crosshair", "currentcolor", "cursive", "cyclic", "darken", "dashed", "decimal", "decimal-leading-zero", "default", "default-button", "dense", "destination-atop", "destination-in", "destination-out", "destination-over", "devanagari", "difference", "disc", "discard", "disclosure-closed", "disclosure-open", "document", "dot-dash", "dot-dot-dash", "dotted", "double", "down", "e-resize", "ease", "ease-in", "ease-in-out", "ease-out", "element", "ellipse", "ellipsis", "embed", "end", "ethiopic", "ethiopic-abegede", "ethiopic-abegede-am-et", "ethiopic-abegede-gez", "ethiopic-abegede-ti-er", "ethiopic-abegede-ti-et", "ethiopic-halehame-aa-er", "ethiopic-halehame-aa-et", "ethiopic-halehame-am-et", "ethiopic-halehame-gez", "ethiopic-halehame-om-et", "ethiopic-halehame-sid-et", "ethiopic-halehame-so-et", "ethiopic-halehame-ti-er", "ethiopic-halehame-ti-et", "ethiopic-halehame-tig", "ethiopic-numeric", "ew-resize", "exclusion", "expanded", "extends", "extra-condensed", "extra-expanded", "fantasy", "fast", "fill", "fixed", "flat", "flex", "flex-end", "flex-start", "footnotes", "forwards", "from", "geometricPrecision", "georgian", "graytext", "grid", "groove", "gujarati", "gurmukhi", "hand", "hangul", "hangul-consonant", "hard-light", "hebrew", "help", "hidden", "hide", "higher", "highlight", "highlighttext", "hiragana", "hiragana-iroha", "horizontal", "hsl", "hsla", "hue", "icon", "ignore", "inactiveborder", "inactivecaption", "inactivecaptiontext", "infinite", "infobackground", "infotext", "inherit", "initial", "inline", "inline-axis", "inline-block", "inline-flex", "inline-grid", "inline-table", "inset", "inside", "intrinsic", "invert", "italic", "japanese-formal", "japanese-informal", "justify", "kannada", "katakana", "katakana-iroha", "keep-all", "khmer", "korean-hangul-formal", "korean-hanja-formal", "korean-hanja-informal", "landscape", "lao", "large", "larger", "left", "level", "lighter", "lighten", "line-through", "linear", "linear-gradient", "lines", "list-item", "listbox", "listitem", "local", "logical", "loud", "lower", "lower-alpha", "lower-armenian", "lower-greek", "lower-hexadecimal", "lower-latin", "lower-norwegian", "lower-roman", "lowercase", "ltr", "luminosity", "malayalam", "match", "matrix", "matrix3d", "media-controls-background", "media-current-time-display", "media-fullscreen-button", "media-mute-button", "media-play-button", "media-return-to-realtime-button", "media-rewind-button", "media-seek-back-button", "media-seek-forward-button", "media-slider", "media-sliderthumb", "media-time-remaining-display", "media-volume-slider", "media-volume-slider-container", "media-volume-sliderthumb", "medium", "menu", "menulist", "menulist-button", "menulist-text", "menulist-textfield", "menutext", "message-box", "middle", "min-intrinsic", "mix", "mongolian", "monospace", "move", "multiple", "multiply", "myanmar", "n-resize", "narrower", "ne-resize", "nesw-resize", "no-close-quote", "no-drop", "no-open-quote", "no-repeat", "none", "normal", "not-allowed", "nowrap", "ns-resize", "numbers", "numeric", "nw-resize", "nwse-resize", "oblique", "octal", "open-quote", "optimizeLegibility", "optimizeSpeed", "oriya", "oromo", "outset", "outside", "outside-shape", "overlay", "overline", "padding", "padding-box", "painted", "page", "paused", "persian", "perspective", "plus-darker", "plus-lighter", "pointer", "polygon", "portrait", "pre", "pre-line", "pre-wrap", "preserve-3d", "progress", "push-button", "radial-gradient", "radio", "read-only", "read-write", "read-write-plaintext-only", "rectangle", "region", "relative", "repeat", "repeating-linear-gradient", "repeating-radial-gradient", "repeat-x", "repeat-y", "reset", "reverse", "rgb", "rgba", "ridge", "right", "rotate", "rotate3d", "rotateX", "rotateY", "rotateZ", "round", "row", "row-resize", "row-reverse", "rtl", "run-in", "running", "s-resize", "sans-serif", "saturation", "scale", "scale3d", "scaleX", "scaleY", "scaleZ", "screen", "scroll", "scrollbar", "se-resize", "searchfield", "searchfield-cancel-button", "searchfield-decoration", "searchfield-results-button", "searchfield-results-decoration", "semi-condensed", "semi-expanded", "separate", "serif", "show", "sidama", "simp-chinese-formal", "simp-chinese-informal", "single", "skew", "skewX", "skewY", "skip-white-space", "slide", "slider-horizontal", "slider-vertical", "sliderthumb-horizontal", "sliderthumb-vertical", "slow", "small", "small-caps", "small-caption", "smaller", "soft-light", "solid", "somali", "source-atop", "source-in", "source-out", "source-over", "space", "space-around", "space-between", "spell-out", "square", "square-button", "start", "static", "status-bar", "stretch", "stroke", "sub", "subpixel-antialiased", "super", "sw-resize", "symbolic", "symbols", "table", "table-caption", "table-cell", "table-column", "table-column-group", "table-footer-group", "table-header-group", "table-row", "table-row-group", "tamil", "telugu", "text", "text-bottom", "text-top", "textarea", "textfield", "thai", "thick", "thin", "threeddarkshadow", "threedface", "threedhighlight", "threedlightshadow", "threedshadow", "tibetan", "tigre", "tigrinya-er", "tigrinya-er-abegede", "tigrinya-et", "tigrinya-et-abegede", "to", "top", "trad-chinese-formal", "trad-chinese-informal", "translate", "translate3d", "translateX", "translateY", "translateZ", "transparent", "ultra-condensed", "ultra-expanded", "underline", "up", "upper-alpha", "upper-armenian", "upper-greek", "upper-hexadecimal", "upper-latin", "upper-norwegian", "upper-roman", "uppercase", "urdu", "url", "var", "vertical", "vertical-text", "visible", "visibleFill", "visiblePainted", "visibleStroke", "visual", "w-resize", "wait", "wave", "wider", "window", "windowframe", "windowtext", "words", "wrap", "wrap-reverse", "x-large", "x-small", "xor", "xx-large", "xx-small"],
      v = b(u),
      w = c.concat(e).concat(g).concat(i).concat(k).concat(m).concat(s).concat(u);a.registerHelper("hintWords", "css", w), a.defineMIME("text/css", { documentTypes: d, mediaTypes: f, mediaFeatures: h, mediaValueKeywords: j, propertyKeywords: l, nonStandardPropertyKeywords: n, fontProperties: p, counterDescriptors: r, colorKeywords: t, valueKeywords: v, tokenHooks: { "/": function _(a, b) {
        return a.eat("*") ? (b.tokenize = x, x(a, b)) : !1;
      } }, name: "css" }), a.defineMIME("text/x-scss", { mediaTypes: f, mediaFeatures: h, mediaValueKeywords: j, propertyKeywords: l, nonStandardPropertyKeywords: n, colorKeywords: t, valueKeywords: v, fontProperties: p, allowNested: !0, tokenHooks: { "/": function _(a, b) {
        return a.eat("/") ? (a.skipToEnd(), ["comment", "comment"]) : a.eat("*") ? (b.tokenize = x, x(a, b)) : ["operator", "operator"];
      }, ":": function _(a) {
        return a.match(/\s*\{/) ? [null, "{"] : !1;
      }, $: function $(a) {
        return a.match(/^[\w-]+/), a.match(/^\s*:/, !1) ? ["variable-2", "variable-definition"] : ["variable-2", "variable"];
      }, "#": function _(a) {
        return a.eat("{") ? [null, "interpolation"] : !1;
      } }, name: "css", helperType: "scss" }), a.defineMIME("text/x-less", { mediaTypes: f, mediaFeatures: h, mediaValueKeywords: j, propertyKeywords: l, nonStandardPropertyKeywords: n, colorKeywords: t, valueKeywords: v, fontProperties: p, allowNested: !0, tokenHooks: { "/": function _(a, b) {
        return a.eat("/") ? (a.skipToEnd(), ["comment", "comment"]) : a.eat("*") ? (b.tokenize = x, x(a, b)) : ["operator", "operator"];
      }, "@": function _(a) {
        return a.eat("{") ? [null, "interpolation"] : a.match(/^(charset|document|font-face|import|(-(moz|ms|o|webkit)-)?keyframes|media|namespace|page|supports)\b/, !1) ? !1 : (a.eatWhile(/[\w\\\-]/), a.match(/^\s*:/, !1) ? ["variable-2", "variable-definition"] : ["variable-2", "variable"]);
      }, "&": function _() {
        return ["atom", "atom"];
      } }, name: "css", helperType: "less" }), a.defineMIME("text/x-gss", { documentTypes: d, mediaTypes: f, mediaFeatures: h, propertyKeywords: l, nonStandardPropertyKeywords: n, fontProperties: p, counterDescriptors: r, colorKeywords: t, valueKeywords: v, supportsAtComponent: !0, tokenHooks: { "/": function _(a, b) {
        return a.eat("*") ? (b.tokenize = x, x(a, b)) : !1;
      } }, name: "css", helperType: "gss" });
}), function (a) {
  "object" == (typeof exports === "undefined" ? "undefined" : _typeof(exports)) && "object" == (typeof module === "undefined" ? "undefined" : _typeof(module)) ? a(require("../../lib/codemirror"), require("../htmlmixed/htmlmixed"), require("../../addon/mode/multiplex")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror", "../htmlmixed/htmlmixed", "../../addon/mode/multiplex"], a) : a(CodeMirror);
}(function (a) {
  "use strict";
  a.defineMode("htmlembedded", function (b, c) {
    return a.multiplexingMode(a.getMode(b, "htmlmixed"), { open: c.open || c.scriptStartRegex || "<%", close: c.close || c.scriptEndRegex || "%>", mode: a.getMode(b, c.scriptingModeSpec) });
  }, "htmlmixed"), a.defineMIME("application/x-ejs", { name: "htmlembedded", scriptingModeSpec: "javascript" }), a.defineMIME("application/x-aspx", { name: "htmlembedded", scriptingModeSpec: "text/x-csharp" }), a.defineMIME("application/x-jsp", { name: "htmlembedded", scriptingModeSpec: "text/x-java" }), a.defineMIME("application/x-erb", { name: "htmlembedded", scriptingModeSpec: "ruby" });
}), function (a) {
  "object" == (typeof exports === "undefined" ? "undefined" : _typeof(exports)) && "object" == (typeof module === "undefined" ? "undefined" : _typeof(module)) ? a(require("../../lib/codemirror"), require("../xml/xml"), require("../javascript/javascript"), require("../scss/scss")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror", "../xml/xml", "../javascript/javascript", "../scss/scss"], a) : a(CodeMirror);
}(function (a) {
  "use strict";
  function c(a, b, c) {
    var d = a.current(),
        e = d.search(b);return e > -1 ? a.backUp(d.length - e) : d.match(/<\/?$/) && (a.backUp(d.length), a.match(b, !1) || a.match(d)), c;
  }function e(a) {
    var b = d[a];return b ? b : d[a] = new RegExp("\\s+" + a + "\\s*=\\s*('|\")?([^'\"]+)('|\")?\\s*");
  }function f(a, b) {
    var c = a.match(e(b));return c ? c[2] : "";
  }function g(a, b) {
    return new RegExp((b ? "^" : "") + "</s*" + a + "s*>", "i");
  }function h(a, b) {
    for (var c in a) {
      for (var d = b[c] || (b[c] = []), e = a[c], f = e.length - 1; f >= 0; f--) {
        d.unshift(e[f]);
      }
    }
  }function i(a, b) {
    for (var c = 0; c < a.length; c++) {
      var d = a[c];if (!d[0] || d[1].test(f(b, d[0]))) return d[2];
    }
  }var b = { script: [["lang", /(javascript|babel)/i, "javascript"], ["type", /^(?:text|application)\/(?:x-)?(?:java|ecma)script$|^$/i, "javascript"], ["type", /./, "text/plain"], [null, null, "javascript"]], style: [["lang", /^css$/i, "css"], ["type", /^(text\/)?(x-)?(stylesheet|css)$/i, "css"], ["type", /./, "text/plain"], [null, null, "css"]] },
      d = {};a.defineMode("htmlmixed", function (d, e) {
    function n(b, e) {
      var l,
          h = f.token(b, e.htmlState),
          k = /\btag\b/.test(h);if (k && !/[<>\s\/]/.test(b.current()) && (l = e.htmlState.tagName && e.htmlState.tagName.toLowerCase()) && j.hasOwnProperty(l)) e.inTag = l + " ";else if (e.inTag && k && />$/.test(b.current())) {
        var m = /^([\S]+) (.*)/.exec(e.inTag);e.inTag = null;var o = ">" == b.current() && i(j[m[1]], m[2]),
            p = a.getMode(d, o),
            q = g(m[1], !0),
            r = g(m[1], !1);e.token = function (a, b) {
          return a.match(q, !1) ? (b.token = n, b.localState = b.localMode = null, null) : c(a, r, b.localMode.token(a, b.localState));
        }, e.localMode = p, e.localState = a.startState(p, f.indent(e.htmlState, ""));
      } else e.inTag && (e.inTag += b.current(), b.eol() && (e.inTag += " "));return h;
    }var f = a.getMode(d, { name: "xml", htmlMode: !0, multilineTagIndentFactor: e.multilineTagIndentFactor, multilineTagIndentPastTag: e.multilineTagIndentPastTag }),
        j = {},
        k = e && e.tags,
        l = e && e.scriptTypes;if (h(b, j), k && h(k, j), l) for (var m = l.length - 1; m >= 0; m--) {
      j.script.unshift(["type", l[m].matches, l[m].mode]);
    }return { startState: function startState() {
        var b = a.startState(f);return { token: n, inTag: null, localMode: null, localState: null, htmlState: b };
      }, copyState: function copyState(b) {
        var c;return b.localState && (c = a.copyState(b.localMode, b.localState)), { token: b.token, inTag: b.inTag, localMode: b.localMode, localState: c, htmlState: a.copyState(f, b.htmlState) };
      }, token: function token(a, b) {
        return b.token(a, b);
      }, indent: function indent(b, c) {
        return !b.localMode || /^\s*<\//.test(c) ? f.indent(b.htmlState, c) : b.localMode.indent ? b.localMode.indent(b.localState, c) : a.Pass;
      }, innerMode: function innerMode(a) {
        return { state: a.localState || a.htmlState, mode: a.localMode || f };
      } };
  }, "xml", "javascript", "css"), a.defineMIME("text/html", "htmlmixed");
}), function (a) {
  "object" == (typeof exports === "undefined" ? "undefined" : _typeof(exports)) && "object" == (typeof module === "undefined" ? "undefined" : _typeof(module)) ? a(require("../../lib/codemirror")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror"], a) : a(CodeMirror);
}(function (a) {
  "use strict";
  function b(a, b, c) {
    return (/^(?:operator|sof|keyword c|case|new|[\[{}\(,;:]|=>)$/.test(b.lastType) || "quasi" == b.lastType && /\{\s*$/.test(a.string.slice(0, a.pos - (c || 0)))
    );
  }a.defineMode("javascript", function (c, d) {
    function n(a) {
      for (var c, b = !1, d = !1; null != (c = a.next());) {
        if (!b) {
          if ("/" == c && !d) return;"[" == c ? d = !0 : d && "]" == c && (d = !1);
        }b = !b && "\\" == c;
      }
    }function q(a, b, c) {
      return o = a, p = c, b;
    }function r(a, c) {
      var d = a.next();if ('"' == d || "'" == d) return c.tokenize = s(d), c.tokenize(a, c);if ("." == d && a.match(/^\d+(?:[eE][+\-]?\d+)?/)) return q("number", "number");if ("." == d && a.match("..")) return q("spread", "meta");if (/[\[\]{}\(\),;\:\.]/.test(d)) return q(d);if ("=" == d && a.eat(">")) return q("=>", "operator");if ("0" == d && a.eat(/x/i)) return a.eatWhile(/[\da-f]/i), q("number", "number");if ("0" == d && a.eat(/o/i)) return a.eatWhile(/[0-7]/i), q("number", "number");if ("0" == d && a.eat(/b/i)) return a.eatWhile(/[01]/i), q("number", "number");if (/\d/.test(d)) return a.match(/^\d*(?:\.\d*)?(?:[eE][+\-]?\d+)?/), q("number", "number");if ("/" == d) return a.eat("*") ? (c.tokenize = t, t(a, c)) : a.eat("/") ? (a.skipToEnd(), q("comment", "comment")) : b(a, c, 1) ? (n(a), a.match(/^\b(([gimyu])(?![gimyu]*\2))+\b/), q("regexp", "string-2")) : (a.eatWhile(l), q("operator", "operator", a.current()));if ("`" == d) return c.tokenize = u, u(a, c);if ("#" == d) return a.skipToEnd(), q("error", "error");if (l.test(d)) return a.eatWhile(l), q("operator", "operator", a.current());if (j.test(d)) {
        a.eatWhile(j);var e = a.current(),
            f = k.propertyIsEnumerable(e) && k[e];return f && "." != c.lastType ? q(f.type, f.style, e) : q("variable", "variable", e);
      }
    }function s(a) {
      return function (b, c) {
        var e,
            d = !1;if (g && "@" == b.peek() && b.match(m)) return c.tokenize = r, q("jsonld-keyword", "meta");for (; null != (e = b.next()) && (e != a || d);) {
          d = !d && "\\" == e;
        }return d || (c.tokenize = r), q("string", "string");
      };
    }function t(a, b) {
      for (var d, c = !1; d = a.next();) {
        if ("/" == d && c) {
          b.tokenize = r;break;
        }c = "*" == d;
      }return q("comment", "comment");
    }function u(a, b) {
      for (var d, c = !1; null != (d = a.next());) {
        if (!c && ("`" == d || "$" == d && a.eat("{"))) {
          b.tokenize = r;break;
        }c = !c && "\\" == d;
      }return q("quasi", "string-2", a.current());
    }function w(a, b) {
      b.fatArrowAt && (b.fatArrowAt = null);var c = a.string.indexOf("=>", a.start);if (!(0 > c)) {
        for (var d = 0, e = !1, f = c - 1; f >= 0; --f) {
          var g = a.string.charAt(f),
              h = v.indexOf(g);if (h >= 0 && 3 > h) {
            if (!d) {
              ++f;break;
            }if (0 == --d) break;
          } else if (h >= 3 && 6 > h) ++d;else if (j.test(g)) e = !0;else {
            if (/["'\/]/.test(g)) return;if (e && !d) {
              ++f;break;
            }
          }
        }e && !d && (b.fatArrowAt = f);
      }
    }function y(a, b, c, d, e, f) {
      this.indented = a, this.column = b, this.type = c, this.prev = e, this.info = f, null != d && (this.align = d);
    }function z(a, b) {
      for (var c = a.localVars; c; c = c.next) {
        if (c.name == b) return !0;
      }for (var d = a.context; d; d = d.prev) {
        for (var c = d.vars; c; c = c.next) {
          if (c.name == b) return !0;
        }
      }
    }function A(a, b, c, d, e) {
      var f = a.cc;for (B.state = a, B.stream = e, B.marked = null, B.cc = f, B.style = b, a.lexical.hasOwnProperty("align") || (a.lexical.align = !0);;) {
        var g = f.length ? f.pop() : h ? M : L;if (g(c, d)) {
          for (; f.length && f[f.length - 1].lex;) {
            f.pop()();
          }return B.marked ? B.marked : "variable" == c && z(a, d) ? "variable-2" : b;
        }
      }
    }function C() {
      for (var a = arguments.length - 1; a >= 0; a--) {
        B.cc.push(arguments[a]);
      }
    }function D() {
      return C.apply(null, arguments), !0;
    }function E(a) {
      function b(b) {
        for (var c = b; c; c = c.next) {
          if (c.name == a) return !0;
        }return !1;
      }var c = B.state;if (B.marked = "def", c.context) {
        if (b(c.localVars)) return;c.localVars = { name: a, next: c.localVars };
      } else {
        if (b(c.globalVars)) return;d.globalVars && (c.globalVars = { name: a, next: c.globalVars });
      }
    }function G() {
      B.state.context = { prev: B.state.context, vars: B.state.localVars }, B.state.localVars = F;
    }function H() {
      B.state.localVars = B.state.context.vars, B.state.context = B.state.context.prev;
    }function I(a, b) {
      var c = function c() {
        var c = B.state,
            d = c.indented;if ("stat" == c.lexical.type) d = c.lexical.indented;else for (var e = c.lexical; e && ")" == e.type && e.align; e = e.prev) {
          d = e.indented;
        }c.lexical = new y(d, B.stream.column(), a, null, c.lexical, b);
      };return c.lex = !0, c;
    }function J() {
      var a = B.state;a.lexical.prev && (")" == a.lexical.type && (a.indented = a.lexical.indented), a.lexical = a.lexical.prev);
    }function K(a) {
      function b(c) {
        return c == a ? D() : ";" == a ? C() : D(b);
      }return b;
    }function L(a, b) {
      return "var" == a ? D(I("vardef", b.length), ka, K(";"), J) : "keyword a" == a ? D(I("form"), M, L, J) : "keyword b" == a ? D(I("form"), L, J) : "{" == a ? D(I("}"), fa, J) : ";" == a ? D() : "if" == a ? ("else" == B.state.lexical.info && B.state.cc[B.state.cc.length - 1] == J && B.state.cc.pop()(), D(I("form"), M, L, J, pa)) : "function" == a ? D(va) : "for" == a ? D(I("form"), qa, L, J) : "variable" == a ? D(I("stat"), $) : "switch" == a ? D(I("form"), M, I("}", "switch"), K("{"), fa, J, J) : "case" == a ? D(M, K(":")) : "default" == a ? D(K(":")) : "catch" == a ? D(I("form"), G, K("("), wa, K(")"), L, J, H) : "class" == a ? D(I("form"), xa, J) : "export" == a ? D(I("stat"), Ba, J) : "import" == a ? D(I("stat"), Ca, J) : "module" == a ? D(I("form"), la, I("}"), K("{"), fa, J, J) : "async" == a ? D(L) : C(I("stat"), M, K(";"), J);
    }function M(a) {
      return O(a, !1);
    }function N(a) {
      return O(a, !0);
    }function O(a, b) {
      if (B.state.fatArrowAt == B.stream.start) {
        var c = b ? W : V;if ("(" == a) return D(G, I(")"), da(la, ")"), J, K("=>"), c, H);if ("variable" == a) return C(G, la, K("=>"), c, H);
      }var d = b ? S : R;return x.hasOwnProperty(a) ? D(d) : "function" == a ? D(va, d) : "keyword c" == a || "async" == a ? D(b ? Q : P) : "(" == a ? D(I(")"), P, Ia, K(")"), J, d) : "operator" == a || "spread" == a ? D(b ? N : M) : "[" == a ? D(I("]"), Ga, J, d) : "{" == a ? ea(aa, "}", null, d) : "quasi" == a ? C(T, d) : "new" == a ? D(X(b)) : D();
    }function P(a) {
      return a.match(/[;\}\)\],]/) ? C() : C(M);
    }function Q(a) {
      return a.match(/[;\}\)\],]/) ? C() : C(N);
    }function R(a, b) {
      return "," == a ? D(M) : S(a, b, !1);
    }function S(a, b, c) {
      var d = 0 == c ? R : S,
          e = 0 == c ? M : N;return "=>" == a ? D(G, c ? W : V, H) : "operator" == a ? /\+\+|--/.test(b) ? D(d) : "?" == b ? D(M, K(":"), e) : D(e) : "quasi" == a ? C(T, d) : ";" != a ? "(" == a ? ea(N, ")", "call", d) : "." == a ? D(_, d) : "[" == a ? D(I("]"), P, K("]"), J, d) : void 0 : void 0;
    }function T(a, b) {
      return "quasi" != a ? C() : "${" != b.slice(b.length - 2) ? D(T) : D(M, U);
    }function U(a) {
      return "}" == a ? (B.marked = "string-2", B.state.tokenize = u, D(T)) : void 0;
    }function V(a) {
      return w(B.stream, B.state), C("{" == a ? L : M);
    }function W(a) {
      return w(B.stream, B.state), C("{" == a ? L : N);
    }function X(a) {
      return function (b) {
        return "." == b ? D(a ? Z : Y) : C(a ? N : M);
      };
    }function Y(a, b) {
      return "target" == b ? (B.marked = "keyword", D(R)) : void 0;
    }function Z(a, b) {
      return "target" == b ? (B.marked = "keyword", D(S)) : void 0;
    }function $(a) {
      return ":" == a ? D(J, L) : C(R, K(";"), J);
    }function _(a) {
      return "variable" == a ? (B.marked = "property", D()) : void 0;
    }function aa(a, b) {
      return "async" == a ? D(aa) : "variable" == a || "keyword" == B.style ? (B.marked = "property", D("get" == b || "set" == b ? ba : ca)) : "number" == a || "string" == a ? (B.marked = g ? "property" : B.style + " property", D(ca)) : "jsonld-keyword" == a ? D(ca) : "modifier" == a ? D(aa) : "[" == a ? D(M, K("]"), ca) : "spread" == a ? D(M) : void 0;
    }function ba(a) {
      return "variable" != a ? C(ca) : (B.marked = "property", D(va));
    }function ca(a) {
      return ":" == a ? D(N) : "(" == a ? C(va) : void 0;
    }function da(a, b) {
      function c(d, e) {
        if ("," == d) {
          var f = B.state.lexical;return "call" == f.info && (f.pos = (f.pos || 0) + 1), D(a, c);
        }return d == b || e == b ? D() : D(K(b));
      }return function (d, e) {
        return d == b || e == b ? D() : C(a, c);
      };
    }function ea(a, b, c) {
      for (var d = 3; d < arguments.length; d++) {
        B.cc.push(arguments[d]);
      }return D(I(b, c), da(a, b), J);
    }function fa(a) {
      return "}" == a ? D() : C(L, fa);
    }function ga(a) {
      return i && ":" == a ? D(ia) : void 0;
    }function ha(a, b) {
      return "=" == b ? D(N) : void 0;
    }function ia(a) {
      return "variable" == a ? (B.marked = "variable-3", D(ja)) : void 0;
    }function ja(a, b) {
      return "<" == b ? D(da(ia, ">"), ja) : "[" == a ? D(K("]"), ja) : void 0;
    }function ka() {
      return C(la, ga, na, oa);
    }function la(a, b) {
      return "modifier" == a ? D(la) : "variable" == a ? (E(b), D()) : "spread" == a ? D(la) : "[" == a ? ea(la, "]") : "{" == a ? ea(ma, "}") : void 0;
    }function ma(a, b) {
      return "variable" != a || B.stream.match(/^\s*:/, !1) ? ("variable" == a && (B.marked = "property"), "spread" == a ? D(la) : "}" == a ? C() : D(K(":"), la, na)) : (E(b), D(na));
    }function na(a, b) {
      return "=" == b ? D(N) : void 0;
    }function oa(a) {
      return "," == a ? D(ka) : void 0;
    }function pa(a, b) {
      return "keyword b" == a && "else" == b ? D(I("form", "else"), L, J) : void 0;
    }function qa(a) {
      return "(" == a ? D(I(")"), ra, K(")"), J) : void 0;
    }function ra(a) {
      return "var" == a ? D(ka, K(";"), ta) : ";" == a ? D(ta) : "variable" == a ? D(sa) : C(M, K(";"), ta);
    }function sa(a, b) {
      return "in" == b || "of" == b ? (B.marked = "keyword", D(M)) : D(R, ta);
    }function ta(a, b) {
      return ";" == a ? D(ua) : "in" == b || "of" == b ? (B.marked = "keyword", D(M)) : C(M, K(";"), ua);
    }function ua(a) {
      ")" != a && D(M);
    }function va(a, b) {
      return "*" == b ? (B.marked = "keyword", D(va)) : "variable" == a ? (E(b), D(va)) : "(" == a ? D(G, I(")"), da(wa, ")"), J, ga, L, H) : void 0;
    }function wa(a) {
      return "spread" == a ? D(wa) : C(la, ga, ha);
    }function xa(a, b) {
      return "variable" == a ? (E(b), D(ya)) : void 0;
    }function ya(a, b) {
      return "extends" == b ? D(M, ya) : "{" == a ? D(I("}"), za, J) : void 0;
    }function za(a, b) {
      return "variable" == a || "keyword" == B.style ? "static" == b ? (B.marked = "keyword", D(za)) : (B.marked = "property", "get" == b || "set" == b ? D(Aa, va, za) : D(va, za)) : "*" == b ? (B.marked = "keyword", D(za)) : ";" == a ? D(za) : "}" == a ? D() : void 0;
    }function Aa(a) {
      return "variable" != a ? C() : (B.marked = "property", D());
    }function Ba(a, b) {
      return "*" == b ? (B.marked = "keyword", D(Fa, K(";"))) : "default" == b ? (B.marked = "keyword", D(M, K(";"))) : C(L);
    }function Ca(a) {
      return "string" == a ? D() : C(Da, Fa);
    }function Da(a, b) {
      return "{" == a ? ea(Da, "}") : ("variable" == a && E(b), "*" == b && (B.marked = "keyword"), D(Ea));
    }function Ea(a, b) {
      return "as" == b ? (B.marked = "keyword", D(Da)) : void 0;
    }function Fa(a, b) {
      return "from" == b ? (B.marked = "keyword", D(M)) : void 0;
    }function Ga(a) {
      return "]" == a ? D() : C(N, Ha);
    }function Ha(a) {
      return "for" == a ? C(Ia, K("]")) : "," == a ? D(da(Q, "]")) : C(da(N, "]"));
    }function Ia(a) {
      return "for" == a ? D(qa, Ia) : "if" == a ? D(M, Ia) : void 0;
    }function Ja(a, b) {
      return "operator" == a.lastType || "," == a.lastType || l.test(b.charAt(0)) || /[,.]/.test(b.charAt(0));
    }var o,
        p,
        e = c.indentUnit,
        f = d.statementIndent,
        g = d.jsonld,
        h = d.json || g,
        i = d.typescript,
        j = d.wordCharacters || /[\w$\xa1-\uffff]/,
        k = function () {
      function a(a) {
        return { type: a, style: "keyword" };
      }var b = a("keyword a"),
          c = a("keyword b"),
          d = a("keyword c"),
          e = a("operator"),
          f = { type: "atom", style: "atom" },
          g = { "if": a("if"), "while": b, "with": b, "else": c, "do": c, "try": c, "finally": c, "return": d, "break": d, "continue": d, "new": a("new"), "delete": d, "throw": d, "debugger": d, "var": a("var"), "const": a("var"), let: a("var"), "function": a("function"), "catch": a("catch"), "for": a("for"), "switch": a("switch"), "case": a("case"), "default": a("default"), "in": e, "typeof": e, "instanceof": e, "true": f, "false": f, "null": f, undefined: f, NaN: f, Infinity: f, "this": a("this"), "class": a("class"), "super": a("atom"), "yield": d, "export": a("export"), "import": a("import"), "extends": d, await: d, async: a("async") };if (i) {
        var h = { type: "variable", style: "variable-3" },
            j = { "interface": a("class"), "implements": d, namespace: d, module: a("module"), "enum": a("module"), "public": a("modifier"), "private": a("modifier"), "protected": a("modifier"), "abstract": a("modifier"), as: e, string: h, number: h, "boolean": h, any: h };for (var k in j) {
          g[k] = j[k];
        }
      }return g;
    }(),
        l = /[+\-*&%=<>!?|~^]/,
        m = /^@(context|id|value|language|type|container|list|set|reverse|index|base|vocab|graph)"/,
        v = "([{}])",
        x = { atom: !0, number: !0, variable: !0, string: !0, regexp: !0, "this": !0, "jsonld-keyword": !0 },
        B = { state: null, column: null, marked: null, cc: null },
        F = { name: "this", next: { name: "arguments" } };return J.lex = !0, { startState: function startState(a) {
        var b = { tokenize: r, lastType: "sof", cc: [], lexical: new y((a || 0) - e, 0, "block", !1), localVars: d.localVars, context: d.localVars && { vars: d.localVars }, indented: a || 0 };return d.globalVars && "object" == _typeof(d.globalVars) && (b.globalVars = d.globalVars), b;
      }, token: function token(a, b) {
        if (a.sol() && (b.lexical.hasOwnProperty("align") || (b.lexical.align = !1), b.indented = a.indentation(), w(a, b)), b.tokenize != t && a.eatSpace()) return null;var c = b.tokenize(a, b);return "comment" == o ? c : (b.lastType = "operator" != o || "++" != p && "--" != p ? o : "incdec", A(b, c, o, p, a));
      }, indent: function indent(b, c) {
        if (b.tokenize == t) return a.Pass;if (b.tokenize != r) return 0;var g = c && c.charAt(0),
            h = b.lexical;if (!/^\s*else\b/.test(c)) for (var i = b.cc.length - 1; i >= 0; --i) {
          var j = b.cc[i];if (j == J) h = h.prev;else if (j != pa) break;
        }"stat" == h.type && "}" == g && (h = h.prev), f && ")" == h.type && "stat" == h.prev.type && (h = h.prev);var k = h.type,
            l = g == k;return "vardef" == k ? h.indented + ("operator" == b.lastType || "," == b.lastType ? h.info + 1 : 0) : "form" == k && "{" == g ? h.indented : "form" == k ? h.indented + e : "stat" == k ? h.indented + (Ja(b, c) ? f || e : 0) : "switch" != h.info || l || 0 == d.doubleIndentSwitch ? h.align ? h.column + (l ? 0 : 1) : h.indented + (l ? 0 : e) : h.indented + (/^(?:case|default)\b/.test(c) ? e : 2 * e);
      }, electricInput: /^\s*(?:case .*?:|default:|\{|\})$/, blockCommentStart: h ? null : "/*", blockCommentEnd: h ? null : "*/", lineComment: h ? null : "//", fold: "brace", closeBrackets: "()[]{}''\"\"``", helperType: h ? "json" : "javascript", jsonldMode: g, jsonMode: h, expressionAllowed: b, skipExpression: function skipExpression(a) {
        var b = a.cc[a.cc.length - 1];(b == M || b == N) && a.cc.pop();
      } };
  }), a.registerHelper("wordChars", "javascript", /[\w$]/), a.defineMIME("text/javascript", "javascript"), a.defineMIME("text/ecmascript", "javascript"), a.defineMIME("application/javascript", "javascript"), a.defineMIME("application/x-javascript", "javascript"), a.defineMIME("application/ecmascript", "javascript"), a.defineMIME("application/json", { name: "javascript", json: !0 }), a.defineMIME("application/x-json", { name: "javascript", json: !0 }), a.defineMIME("application/ld+json", { name: "javascript", jsonld: !0 }), a.defineMIME("text/typescript", { name: "javascript", typescript: !0 }), a.defineMIME("application/typescript", { name: "javascript", typescript: !0 });
}), function (a) {
  "object" == (typeof exports === "undefined" ? "undefined" : _typeof(exports)) && "object" == (typeof module === "undefined" ? "undefined" : _typeof(module)) ? a(require("../../lib/codemirror"), require("../htmlmixed/htmlmixed"), require("../clike/clike")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror", "../htmlmixed/htmlmixed", "../clike/clike"], a) : a(CodeMirror);
}(function (a) {
  "use strict";
  function b(a) {
    for (var b = {}, c = a.split(" "), d = 0; d < c.length; ++d) {
      b[c[d]] = !0;
    }return b;
  }function c(a, b, e) {
    return 0 == a.length ? d(b) : function (f, g) {
      for (var h = a[0], i = 0; i < h.length; i++) {
        if (f.match(h[i][0])) return g.tokenize = c(a.slice(1), b), h[i][1];
      }return g.tokenize = d(b, e), "string";
    };
  }function d(a, b) {
    return function (c, d) {
      return e(c, d, a, b);
    };
  }function e(a, b, d, e) {
    if (e !== !1 && a.match("${", !1) || a.match("{$", !1)) return b.tokenize = null, "string";if (e !== !1 && a.match(/^\$[a-zA-Z_][a-zA-Z0-9_]*/)) return a.match("[", !1) && (b.tokenize = c([[["[", null]], [[/\d[\w\.]*/, "number"], [/\$[a-zA-Z_][a-zA-Z0-9_]*/, "variable-2"], [/[\w\$]+/, "variable"]], [["]", null]]], d, e)), a.match(/\-\>\w/, !1) && (b.tokenize = c([[["->", null]], [[/[\w]+/, "variable"]]], d, e)), "variable-2";for (var f = !1; !a.eol() && (f || e === !1 || !a.match("{$", !1) && !a.match(/^(\$[a-zA-Z_][a-zA-Z0-9_]*|\$\{)/, !1));) {
      if (!f && a.match(d)) {
        b.tokenize = null, b.tokStack.pop(), b.tokStack.pop();break;
      }f = "\\" == a.next() && !f;
    }return "string";
  }var f = "abstract and array as break case catch class clone const continue declare default do else elseif enddeclare endfor endforeach endif endswitch endwhile extends final for foreach function global goto if implements interface instanceof namespace new or private protected public static switch throw trait try use var while xor die echo empty exit eval include include_once isset list require require_once return print unset __halt_compiler self static parent yield insteadof finally",
      g = "true false null TRUE FALSE NULL __CLASS__ __DIR__ __FILE__ __LINE__ __METHOD__ __FUNCTION__ __NAMESPACE__ __TRAIT__",
      h = "func_num_args func_get_arg func_get_args strlen strcmp strncmp strcasecmp strncasecmp each error_reporting define defined trigger_error user_error set_error_handler restore_error_handler get_declared_classes get_loaded_extensions extension_loaded get_extension_funcs debug_backtrace constant bin2hex hex2bin sleep usleep time mktime gmmktime strftime gmstrftime strtotime date gmdate getdate localtime checkdate flush wordwrap htmlspecialchars htmlentities html_entity_decode md5 md5_file crc32 getimagesize image_type_to_mime_type phpinfo phpversion phpcredits strnatcmp strnatcasecmp substr_count strspn strcspn strtok strtoupper strtolower strpos strrpos strrev hebrev hebrevc nl2br basename dirname pathinfo stripslashes stripcslashes strstr stristr strrchr str_shuffle str_word_count strcoll substr substr_replace quotemeta ucfirst ucwords strtr addslashes addcslashes rtrim str_replace str_repeat count_chars chunk_split trim ltrim strip_tags similar_text explode implode setlocale localeconv parse_str str_pad chop strchr sprintf printf vprintf vsprintf sscanf fscanf parse_url urlencode urldecode rawurlencode rawurldecode readlink linkinfo link unlink exec system escapeshellcmd escapeshellarg passthru shell_exec proc_open proc_close rand srand getrandmax mt_rand mt_srand mt_getrandmax base64_decode base64_encode abs ceil floor round is_finite is_nan is_infinite bindec hexdec octdec decbin decoct dechex base_convert number_format fmod ip2long long2ip getenv putenv getopt microtime gettimeofday getrusage uniqid quoted_printable_decode set_time_limit get_cfg_var magic_quotes_runtime set_magic_quotes_runtime get_magic_quotes_gpc get_magic_quotes_runtime import_request_variables error_log serialize unserialize memory_get_usage var_dump var_export debug_zval_dump print_r highlight_file show_source highlight_string ini_get ini_get_all ini_set ini_alter ini_restore get_include_path set_include_path restore_include_path setcookie header headers_sent connection_aborted connection_status ignore_user_abort parse_ini_file is_uploaded_file move_uploaded_file intval floatval doubleval strval gettype settype is_null is_resource is_bool is_long is_float is_int is_integer is_double is_real is_numeric is_string is_array is_object is_scalar ereg ereg_replace eregi eregi_replace split spliti join sql_regcase dl pclose popen readfile rewind rmdir umask fclose feof fgetc fgets fgetss fread fopen fpassthru ftruncate fstat fseek ftell fflush fwrite fputs mkdir rename copy tempnam tmpfile file file_get_contents file_put_contents stream_select stream_context_create stream_context_set_params stream_context_set_option stream_context_get_options stream_filter_prepend stream_filter_append fgetcsv flock get_meta_tags stream_set_write_buffer set_file_buffer set_socket_blocking stream_set_blocking socket_set_blocking stream_get_meta_data stream_register_wrapper stream_wrapper_register stream_set_timeout socket_set_timeout socket_get_status realpath fnmatch fsockopen pfsockopen pack unpack get_browser crypt opendir closedir chdir getcwd rewinddir readdir dir glob fileatime filectime filegroup fileinode filemtime fileowner fileperms filesize filetype file_exists is_writable is_writeable is_readable is_executable is_file is_dir is_link stat lstat chown touch clearstatcache mail ob_start ob_flush ob_clean ob_end_flush ob_end_clean ob_get_flush ob_get_clean ob_get_length ob_get_level ob_get_status ob_get_contents ob_implicit_flush ob_list_handlers ksort krsort natsort natcasesort asort arsort sort rsort usort uasort uksort shuffle array_walk count end prev next reset current key min max in_array array_search extract compact array_fill range array_multisort array_push array_pop array_shift array_unshift array_splice array_slice array_merge array_merge_recursive array_keys array_values array_count_values array_reverse array_reduce array_pad array_flip array_change_key_case array_rand array_unique array_intersect array_intersect_assoc array_diff array_diff_assoc array_sum array_filter array_map array_chunk array_key_exists array_intersect_key array_combine array_column pos sizeof key_exists assert assert_options version_compare ftok str_rot13 aggregate session_name session_module_name session_save_path session_id session_regenerate_id session_decode session_register session_unregister session_is_registered session_encode session_start session_destroy session_unset session_set_save_handler session_cache_limiter session_cache_expire session_set_cookie_params session_get_cookie_params session_write_close preg_match preg_match_all preg_replace preg_replace_callback preg_split preg_quote preg_grep overload ctype_alnum ctype_alpha ctype_cntrl ctype_digit ctype_lower ctype_graph ctype_print ctype_punct ctype_space ctype_upper ctype_xdigit virtual apache_request_headers apache_note apache_lookup_uri apache_child_terminate apache_setenv apache_response_headers apache_get_version getallheaders mysql_connect mysql_pconnect mysql_close mysql_select_db mysql_create_db mysql_drop_db mysql_query mysql_unbuffered_query mysql_db_query mysql_list_dbs mysql_list_tables mysql_list_fields mysql_list_processes mysql_error mysql_errno mysql_affected_rows mysql_insert_id mysql_result mysql_num_rows mysql_num_fields mysql_fetch_row mysql_fetch_array mysql_fetch_assoc mysql_fetch_object mysql_data_seek mysql_fetch_lengths mysql_fetch_field mysql_field_seek mysql_free_result mysql_field_name mysql_field_table mysql_field_len mysql_field_type mysql_field_flags mysql_escape_string mysql_real_escape_string mysql_stat mysql_thread_id mysql_client_encoding mysql_get_client_info mysql_get_host_info mysql_get_proto_info mysql_get_server_info mysql_info mysql mysql_fieldname mysql_fieldtable mysql_fieldlen mysql_fieldtype mysql_fieldflags mysql_selectdb mysql_createdb mysql_dropdb mysql_freeresult mysql_numfields mysql_numrows mysql_listdbs mysql_listtables mysql_listfields mysql_db_name mysql_dbname mysql_tablename mysql_table_name pg_connect pg_pconnect pg_close pg_connection_status pg_connection_busy pg_connection_reset pg_host pg_dbname pg_port pg_tty pg_options pg_ping pg_query pg_send_query pg_cancel_query pg_fetch_result pg_fetch_row pg_fetch_assoc pg_fetch_array pg_fetch_object pg_fetch_all pg_affected_rows pg_get_result pg_result_seek pg_result_status pg_free_result pg_last_oid pg_num_rows pg_num_fields pg_field_name pg_field_num pg_field_size pg_field_type pg_field_prtlen pg_field_is_null pg_get_notify pg_get_pid pg_result_error pg_last_error pg_last_notice pg_put_line pg_end_copy pg_copy_to pg_copy_from pg_trace pg_untrace pg_lo_create pg_lo_unlink pg_lo_open pg_lo_close pg_lo_read pg_lo_write pg_lo_read_all pg_lo_import pg_lo_export pg_lo_seek pg_lo_tell pg_escape_string pg_escape_bytea pg_unescape_bytea pg_client_encoding pg_set_client_encoding pg_meta_data pg_convert pg_insert pg_update pg_delete pg_select pg_exec pg_getlastoid pg_cmdtuples pg_errormessage pg_numrows pg_numfields pg_fieldname pg_fieldsize pg_fieldtype pg_fieldnum pg_fieldprtlen pg_fieldisnull pg_freeresult pg_result pg_loreadall pg_locreate pg_lounlink pg_loopen pg_loclose pg_loread pg_lowrite pg_loimport pg_loexport http_response_code get_declared_traits getimagesizefromstring socket_import_stream stream_set_chunk_size trait_exists header_register_callback class_uses session_status session_register_shutdown echo print global static exit array empty eval isset unset die include require include_once require_once json_decode json_encode json_last_error json_last_error_msg curl_close curl_copy_handle curl_errno curl_error curl_escape curl_exec curl_file_create curl_getinfo curl_init curl_multi_add_handle curl_multi_close curl_multi_exec curl_multi_getcontent curl_multi_info_read curl_multi_init curl_multi_remove_handle curl_multi_select curl_multi_setopt curl_multi_strerror curl_pause curl_reset curl_setopt_array curl_setopt curl_share_close curl_share_init curl_share_setopt curl_strerror curl_unescape curl_version mysqli_affected_rows mysqli_autocommit mysqli_change_user mysqli_character_set_name mysqli_close mysqli_commit mysqli_connect_errno mysqli_connect_error mysqli_connect mysqli_data_seek mysqli_debug mysqli_dump_debug_info mysqli_errno mysqli_error_list mysqli_error mysqli_fetch_all mysqli_fetch_array mysqli_fetch_assoc mysqli_fetch_field_direct mysqli_fetch_field mysqli_fetch_fields mysqli_fetch_lengths mysqli_fetch_object mysqli_fetch_row mysqli_field_count mysqli_field_seek mysqli_field_tell mysqli_free_result mysqli_get_charset mysqli_get_client_info mysqli_get_client_stats mysqli_get_client_version mysqli_get_connection_stats mysqli_get_host_info mysqli_get_proto_info mysqli_get_server_info mysqli_get_server_version mysqli_info mysqli_init mysqli_insert_id mysqli_kill mysqli_more_results mysqli_multi_query mysqli_next_result mysqli_num_fields mysqli_num_rows mysqli_options mysqli_ping mysqli_prepare mysqli_query mysqli_real_connect mysqli_real_escape_string mysqli_real_query mysqli_reap_async_query mysqli_refresh mysqli_rollback mysqli_select_db mysqli_set_charset mysqli_set_local_infile_default mysqli_set_local_infile_handler mysqli_sqlstate mysqli_ssl_set mysqli_stat mysqli_stmt_init mysqli_store_result mysqli_thread_id mysqli_thread_safe mysqli_use_result mysqli_warning_count";a.registerHelper("hintWords", "php", [f, g, h].join(" ").split(" ")), a.registerHelper("wordChars", "php", /[\w$]/);var i = { name: "clike", helperType: "php", keywords: b(f), blockKeywords: b("catch do else elseif for foreach if switch try while finally"), defKeywords: b("class function interface namespace trait"), atoms: b(g), builtin: b(h), multiLineStrings: !0, hooks: { $: function $(a) {
        return a.eatWhile(/[\w\$_]/), "variable-2";
      }, "<": function _(a, b) {
        var c;if (c = a.match(/<<\s*/)) {
          var e = a.eat(/['"]/);a.eatWhile(/[\w\.]/);var f = a.current().slice(c[0].length + (e ? 2 : 1));if (e && a.eat(e), f) return (b.tokStack || (b.tokStack = [])).push(f, 0), b.tokenize = d(f, "'" != e), "string";
        }return !1;
      }, "#": function _(a) {
        for (; !a.eol() && !a.match("?>", !1);) {
          a.next();
        }return "comment";
      }, "/": function _(a) {
        if (a.eat("/")) {
          for (; !a.eol() && !a.match("?>", !1);) {
            a.next();
          }return "comment";
        }return !1;
      }, '"': function _(a, b) {
        return (b.tokStack || (b.tokStack = [])).push('"', 0), b.tokenize = d('"'), "string";
      }, "{": function _(a, b) {
        return b.tokStack && b.tokStack.length && b.tokStack[b.tokStack.length - 1]++, !1;
      }, "}": function _(a, b) {
        return b.tokStack && b.tokStack.length > 0 && ! --b.tokStack[b.tokStack.length - 1] && (b.tokenize = d(b.tokStack[b.tokStack.length - 2])), !1;
      } } };a.defineMode("php", function (b, c) {
    function f(b, c) {
      var f = c.curMode == e;if (b.sol() && c.pending && '"' != c.pending && "'" != c.pending && (c.pending = null), f) return f && null == c.php.tokenize && b.match("?>") ? (c.curMode = d, c.curState = c.html, c.php.context.prev || (c.php = null), "meta") : e.token(b, c.curState);if (b.match(/^<\?\w*/)) return c.curMode = e, c.php || (c.php = a.startState(e, d.indent(c.html, ""))), c.curState = c.php, "meta";if ('"' == c.pending || "'" == c.pending) {
        for (; !b.eol() && b.next() != c.pending;) {}var g = "string";
      } else if (c.pending && b.pos < c.pending.end) {
        b.pos = c.pending.end;var g = c.pending.style;
      } else var g = d.token(b, c.curState);c.pending && (c.pending = null);var j,
          h = b.current(),
          i = h.search(/<\?/);return -1 != i && ("string" == g && (j = h.match(/[\'\"]$/)) && !/\?>/.test(h) ? c.pending = j[0] : c.pending = { end: b.pos, style: g }, b.backUp(h.length - i)), g;
    }var d = a.getMode(b, "text/html"),
        e = a.getMode(b, i);return { startState: function startState() {
        var b = a.startState(d),
            f = c.startOpen ? a.startState(e) : null;return { html: b, php: f, curMode: c.startOpen ? e : d, curState: c.startOpen ? f : b, pending: null };
      }, copyState: function copyState(b) {
        var i,
            c = b.html,
            f = a.copyState(d, c),
            g = b.php,
            h = g && a.copyState(e, g);return i = b.curMode == d ? f : h, { html: f, php: h, curMode: b.curMode, curState: i, pending: b.pending };
      }, token: f, indent: function indent(a, b) {
        return a.curMode != e && /^\s*<\//.test(b) || a.curMode == e && /^\?>/.test(b) ? d.indent(a.html, b) : a.curMode.indent(a.curState, b);
      }, blockCommentStart: "/*", blockCommentEnd: "*/", lineComment: "//", innerMode: function innerMode(a) {
        return { state: a.curState, mode: a.curMode };
      } };
  }, "htmlmixed", "clike"), a.defineMIME("application/x-httpd-php", "php"), a.defineMIME("application/x-httpd-php-open", { name: "php", startOpen: !0 }), a.defineMIME("text/x-php", i);
}), function (a) {
  "object" == (typeof exports === "undefined" ? "undefined" : _typeof(exports)) && "object" == (typeof module === "undefined" ? "undefined" : _typeof(module)) ? a(require("../../lib/codemirror"), require("./xml-hint")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror", "./xml-hint"], a) : a(CodeMirror);
}(function (a) {
  "use strict";
  function k(a) {
    for (var b in j) {
      j.hasOwnProperty(b) && (a.attrs[b] = j[b]);
    }
  }function m(b, c) {
    var d = { schemaInfo: i };if (c) for (var e in c) {
      d[e] = c[e];
    }return a.hint.xml(b, d);
  }var b = "ab aa af ak sq am ar an hy as av ae ay az bm ba eu be bn bh bi bs br bg my ca ch ce ny zh cv kw co cr hr cs da dv nl dz en eo et ee fo fj fi fr ff gl ka de el gn gu ht ha he hz hi ho hu ia id ie ga ig ik io is it iu ja jv kl kn kr ks kk km ki rw ky kv kg ko ku kj la lb lg li ln lo lt lu lv gv mk mg ms ml mt mi mr mh mn na nv nb nd ne ng nn no ii nr oc oj cu om or os pa pi fa pl ps pt qu rm rn ro ru sa sc sd se sm sg sr gd sn si sk sl so st es su sw ss sv ta te tg th ti bo tk tl tn to tr ts tt tw ty ug uk ur uz ve vi vo wa cy wo fy xh yi yo za zu".split(" "),
      c = ["_blank", "_self", "_top", "_parent"],
      d = ["ascii", "utf-8", "utf-16", "latin1", "latin1"],
      e = ["get", "post", "put", "delete"],
      f = ["application/x-www-form-urlencoded", "multipart/form-data", "text/plain"],
      g = ["all", "screen", "print", "embossed", "braille", "handheld", "print", "projection", "screen", "tty", "tv", "speech", "3d-glasses", "resolution [>][<][=] [X]", "device-aspect-ratio: X/Y", "orientation:portrait", "orientation:landscape", "device-height: [X]", "device-width: [X]"],
      h = { attrs: {} },
      i = { a: { attrs: { href: null, ping: null, type: null, media: g, target: c, hreflang: b } }, abbr: h, acronym: h, address: h, applet: h, area: { attrs: { alt: null, coords: null, href: null, target: null, ping: null, media: g, hreflang: b, type: null, shape: ["default", "rect", "circle", "poly"] } }, article: h, aside: h, audio: { attrs: { src: null, mediagroup: null, crossorigin: ["anonymous", "use-credentials"], preload: ["none", "metadata", "auto"], autoplay: ["", "autoplay"], loop: ["", "loop"], controls: ["", "controls"] } }, b: h, base: { attrs: { href: null, target: c } }, basefont: h, bdi: h, bdo: h, big: h, blockquote: { attrs: { cite: null } }, body: h, br: h, button: { attrs: { form: null, formaction: null, name: null, value: null, autofocus: ["", "autofocus"], disabled: ["", "autofocus"], formenctype: f, formmethod: e, formnovalidate: ["", "novalidate"], formtarget: c, type: ["submit", "reset", "button"] } }, canvas: { attrs: { width: null, height: null } }, caption: h, center: h, cite: h, code: h, col: { attrs: { span: null } }, colgroup: { attrs: { span: null } }, command: { attrs: { type: ["command", "checkbox", "radio"], label: null, icon: null, radiogroup: null, command: null, title: null, disabled: ["", "disabled"], checked: ["", "checked"] } }, data: { attrs: { value: null } }, datagrid: { attrs: { disabled: ["", "disabled"], multiple: ["", "multiple"] } }, datalist: { attrs: { data: null } }, dd: h, del: { attrs: { cite: null, datetime: null } }, details: { attrs: { open: ["", "open"] } }, dfn: h, dir: h, div: h, dl: h, dt: h, em: h, embed: { attrs: { src: null, type: null, width: null, height: null } }, eventsource: { attrs: { src: null } }, fieldset: { attrs: { disabled: ["", "disabled"], form: null, name: null } }, figcaption: h, figure: h, font: h, footer: h, form: { attrs: { action: null, name: null, "accept-charset": d, autocomplete: ["on", "off"], enctype: f, method: e, novalidate: ["", "novalidate"], target: c } }, frame: h, frameset: h, h1: h, h2: h, h3: h, h4: h, h5: h, h6: h, head: { attrs: {}, children: ["title", "base", "link", "style", "meta", "script", "noscript", "command"] }, header: h, hgroup: h, hr: h, html: { attrs: { manifest: null }, children: ["head", "body"] }, i: h, iframe: { attrs: { src: null, srcdoc: null, name: null, width: null, height: null, sandbox: ["allow-top-navigation", "allow-same-origin", "allow-forms", "allow-main"], seamless: ["", "seamless"] } }, img: { attrs: { alt: null, src: null, ismap: null, usemap: null, width: null, height: null, crossorigin: ["anonymous", "use-credentials"] } }, input: { attrs: { alt: null, dirname: null, form: null, formaction: null, height: null, list: null, max: null, maxlength: null, min: null, name: null, pattern: null, placeholder: null, size: null, src: null, step: null, value: null, width: null, accept: ["audio/*", "video/*", "image/*"], autocomplete: ["on", "off"], autofocus: ["", "autofocus"], checked: ["", "checked"], disabled: ["", "disabled"], formenctype: f, formmethod: e, formnovalidate: ["", "novalidate"], formtarget: c, multiple: ["", "multiple"], readonly: ["", "readonly"], required: ["", "required"], type: ["hidden", "text", "search", "tel", "url", "email", "password", "datetime", "date", "month", "week", "time", "datetime-local", "number", "range", "color", "checkbox", "radio", "file", "submit", "image", "reset", "button"] } }, ins: { attrs: { cite: null, datetime: null } }, kbd: h, keygen: { attrs: { challenge: null, form: null, name: null, autofocus: ["", "autofocus"], disabled: ["", "disabled"], keytype: ["RSA"] } }, label: { attrs: { "for": null, form: null } }, legend: h, li: { attrs: { value: null } }, link: { attrs: { href: null, type: null, hreflang: b, media: g, sizes: ["all", "16x16", "16x16 32x32", "16x16 32x32 64x64"] } }, map: { attrs: { name: null } }, mark: h, menu: { attrs: { label: null, type: ["list", "context", "toolbar"] } }, meta: { attrs: { content: null, charset: d, name: ["viewport", "application-name", "author", "description", "generator", "keywords"], "http-equiv": ["content-language", "content-type", "default-style", "refresh"] } }, meter: { attrs: { value: null, min: null, low: null, high: null, max: null, optimum: null } }, nav: h, noframes: h, noscript: h, object: { attrs: { data: null, type: null, name: null, usemap: null, form: null, width: null, height: null, typemustmatch: ["", "typemustmatch"] } }, ol: { attrs: { reversed: ["", "reversed"], start: null, type: ["1", "a", "A", "i", "I"] } }, optgroup: { attrs: { disabled: ["", "disabled"], label: null } }, option: { attrs: { disabled: ["", "disabled"], label: null, selected: ["", "selected"], value: null } }, output: { attrs: { "for": null, form: null, name: null } }, p: h, param: { attrs: { name: null, value: null } }, pre: h, progress: { attrs: { value: null, max: null } }, q: { attrs: { cite: null } }, rp: h, rt: h, ruby: h, s: h, samp: h, script: { attrs: { type: ["text/javascript"], src: null, async: ["", "async"], defer: ["", "defer"], charset: d } }, section: h, select: { attrs: { form: null, name: null, size: null, autofocus: ["", "autofocus"], disabled: ["", "disabled"], multiple: ["", "multiple"] } }, small: h, source: { attrs: { src: null, type: null, media: null } }, span: h, strike: h, strong: h, style: { attrs: { type: ["text/css"], media: g, scoped: null } }, sub: h, summary: h, sup: h, table: h, tbody: h, td: { attrs: { colspan: null, rowspan: null, headers: null } }, textarea: { attrs: { dirname: null, form: null, maxlength: null, name: null, placeholder: null, rows: null, cols: null, autofocus: ["", "autofocus"], disabled: ["", "disabled"], readonly: ["", "readonly"], required: ["", "required"], wrap: ["soft", "hard"] } }, tfoot: h, th: { attrs: { colspan: null, rowspan: null, headers: null, scope: ["row", "col", "rowgroup", "colgroup"] } }, thead: h, time: { attrs: { datetime: null } }, title: h, tr: h, track: { attrs: { src: null, label: null, "default": null, kind: ["subtitles", "captions", "descriptions", "chapters", "metadata"], srclang: b } }, tt: h, u: h, ul: h, "var": h, video: { attrs: { src: null, poster: null, width: null, height: null, crossorigin: ["anonymous", "use-credentials"], preload: ["auto", "metadata", "none"], autoplay: ["", "autoplay"], mediagroup: ["movie"], muted: ["", "muted"], controls: ["", "controls"] } }, wbr: h },
      j = { accesskey: ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9"], "class": null, contenteditable: ["true", "false"], contextmenu: null, dir: ["ltr", "rtl", "auto"], draggable: ["true", "false", "auto"], dropzone: ["copy", "move", "link", "string:", "file:"], hidden: ["hidden"], id: null, inert: ["inert"], itemid: null, itemprop: null, itemref: null, itemscope: ["itemscope"], itemtype: null, lang: ["en", "es"], spellcheck: ["true", "false"], style: null, tabindex: ["1", "2", "3", "4", "5", "6", "7", "8", "9"], title: null, translate: ["yes", "no"], onclick: null, rel: ["stylesheet", "alternate", "author", "bookmark", "help", "license", "next", "nofollow", "noreferrer", "prefetch", "prev", "search", "tag"] };k(h);for (var l in i) {
    i.hasOwnProperty(l) && i[l] != h && k(i[l]);
  }a.htmlSchema = i, a.registerHelper("hint", "html", m);
}), function (a) {
  "object" == (typeof exports === "undefined" ? "undefined" : _typeof(exports)) && "object" == (typeof module === "undefined" ? "undefined" : _typeof(module)) ? a(require("../../lib/codemirror")) : "function" == typeof define && define.amd ? define(["../../lib/codemirror"], a) : a(CodeMirror);
}(function (a) {
  "use strict";
  function b(a, b) {
    if (!a.hasOwnProperty(b)) throw new Error("Undefined state " + b + " in simple mode");
  }function c(a, b) {
    if (!a) return (/(?:)/
    );var c = "";return a instanceof RegExp ? (a.ignoreCase && (c = "i"), a = a.source) : a = String(a), new RegExp((b === !1 ? "" : "^") + "(?:" + a + ")", c);
  }function d(a) {
    if (!a) return null;if ("string" == typeof a) return a.replace(/\./g, " ");for (var b = [], c = 0; c < a.length; c++) {
      b.push(a[c] && a[c].replace(/\./g, " "));
    }return b;
  }function e(a, e) {
    (a.next || a.push) && b(e, a.next || a.push), this.regex = c(a.regex), this.token = d(a.token), this.data = a;
  }function f(a, b) {
    return function (c, d) {
      if (d.pending) {
        var e = d.pending.shift();return 0 == d.pending.length && (d.pending = null), c.pos += e.text.length, e.token;
      }if (d.local) {
        if (d.local.end && c.match(d.local.end)) {
          var f = d.local.endToken || null;return d.local = d.localState = null, f;
        }var g,
            f = d.local.mode.token(c, d.localState);return d.local.endScan && (g = d.local.endScan.exec(c.current())) && (c.pos = c.start + g.index), f;
      }for (var i = a[d.state], j = 0; j < i.length; j++) {
        var k = i[j],
            l = (!k.data.sol || c.sol()) && c.match(k.regex);if (l) {
          if (k.data.next ? d.state = k.data.next : k.data.push ? ((d.stack || (d.stack = [])).push(d.state), d.state = k.data.push) : k.data.pop && d.stack && d.stack.length && (d.state = d.stack.pop()), k.data.mode && h(b, d, k.data.mode, k.token), k.data.indent && d.indent.push(c.indentation() + b.indentUnit), k.data.dedent && d.indent.pop(), l.length > 2) {
            d.pending = [];for (var m = 2; m < l.length; m++) {
              l[m] && d.pending.push({ text: l[m], token: k.token[m - 1] });
            }return c.backUp(l[0].length - (l[1] ? l[1].length : 0)), k.token[0];
          }return k.token && k.token.join ? k.token[0] : k.token;
        }
      }return c.next(), null;
    };
  }function g(a, b) {
    if (a === b) return !0;if (!a || "object" != (typeof a === "undefined" ? "undefined" : _typeof(a)) || !b || "object" != (typeof b === "undefined" ? "undefined" : _typeof(b))) return !1;var c = 0;for (var d in a) {
      if (a.hasOwnProperty(d)) {
        if (!b.hasOwnProperty(d) || !g(a[d], b[d])) return !1;c++;
      }
    }for (var d in b) {
      b.hasOwnProperty(d) && c--;
    }return 0 == c;
  }function h(b, d, e, f) {
    var h;if (e.persistent) for (var i = d.persistentStates; i && !h; i = i.next) {
      (e.spec ? g(e.spec, i.spec) : e.mode == i.mode) && (h = i);
    }var j = h ? h.mode : e.mode || a.getMode(b, e.spec),
        k = h ? h.state : a.startState(j);e.persistent && !h && (d.persistentStates = { mode: j, spec: e.spec, state: k, next: d.persistentStates }), d.localState = k, d.local = { mode: j, end: e.end && c(e.end), endScan: e.end && e.forceEnd !== !1 && c(e.end, !1), endToken: f && f.join ? f[f.length - 1] : f };
  }function i(a, b) {
    for (var c = 0; c < b.length; c++) {
      if (b[c] === a) return !0;
    }
  }function j(b, c) {
    return function (d, e, f) {
      if (d.local && d.local.mode.indent) return d.local.mode.indent(d.localState, e, f);if (null == d.indent || d.local || c.dontIndentStates && i(d.state, c.dontIndentStates) > -1) return a.Pass;var g = d.indent.length - 1,
          h = b[d.state];a: for (;;) {
        for (var j = 0; j < h.length; j++) {
          var k = h[j];if (k.data.dedent && k.data.dedentIfLineStart !== !1) {
            var l = k.regex.exec(e);if (l && l[0]) {
              g--, (k.next || k.push) && (h = b[k.next || k.push]), e = e.slice(l[0].length);continue a;
            }
          }
        }break;
      }return 0 > g ? 0 : d.indent[g];
    };
  }a.defineSimpleMode = function (b, c) {
    a.defineMode(b, function (b) {
      return a.simpleMode(b, c);
    });
  }, a.simpleMode = function (c, d) {
    b(d, "start");var g = {},
        h = d.meta || {},
        i = !1;for (var k in d) {
      if (k != h && d.hasOwnProperty(k)) for (var l = g[k] = [], m = d[k], n = 0; n < m.length; n++) {
        var o = m[n];l.push(new e(o, d)), (o.indent || o.dedent) && (i = !0);
      }
    }var p = { startState: function startState() {
        return { state: "start", pending: null, local: null, localState: null, indent: i ? [] : null };
      }, copyState: function copyState(b) {
        var c = { state: b.state, pending: b.pending, local: b.local, localState: null, indent: b.indent && b.indent.slice(0) };b.localState && (c.localState = a.copyState(b.local.mode, b.localState)), b.stack && (c.stack = b.stack.slice(0));for (var d = b.persistentStates; d; d = d.next) {
          c.persistentStates = { mode: d.mode, spec: d.spec, state: d.state == b.localState ? c.localState : a.copyState(d.mode, d.state), next: c.persistentStates };
        }return c;
      }, token: f(g, c), innerMode: function innerMode(a) {
        return a.local && { mode: a.local.mode, state: a.localState };
      }, indent: j(g, h) };if (h) for (var q in h) {
      h.hasOwnProperty(q) && (p[q] = h[q]);
    }return p;
  };
}), function (a) {
  "object" == (typeof exports === "undefined" ? "undefined" : _typeof(exports)) && "object" == (typeof module === "undefined" ? "undefined" : _typeof(module)) ? a(require("../lib/codemirror"), require("../addon/search/searchcursor"), require("../addon/edit/matchbrackets")) : "function" == typeof define && define.amd ? define(["../lib/codemirror", "../addon/search/searchcursor", "../addon/edit/matchbrackets"], a) : a(CodeMirror);
}(function (a) {
  "use strict";
  function g(b, c, e) {
    if (0 > e && 0 == c.ch) return b.clipPos(d(c.line - 1));var f = b.getLine(c.line);if (e > 0 && c.ch >= f.length) return b.clipPos(d(c.line + 1, 0));for (var h, g = "start", i = c.ch, j = 0 > e ? 0 : f.length, k = 0; i != j; i += e, k++) {
      var l = f.charAt(0 > e ? i - 1 : i),
          m = "_" != l && a.isWordChar(l) ? "w" : "o";if ("w" == m && l.toUpperCase() == l && (m = "W"), "start" == g) "o" != m && (g = "in", h = m);else if ("in" == g && h != m) {
        if ("w" == h && "W" == m && 0 > e && i--, "W" == h && "w" == m && e > 0) {
          h = "w";continue;
        }break;
      }
    }return d(c.line, i);
  }function h(a, b) {
    a.extendSelectionsBy(function (c) {
      return a.display.shift || a.doc.extend || c.empty() ? g(a.doc, c.head, b) : 0 > b ? c.from() : c.to();
    });
  }function j(b, c) {
    return b.isReadOnly() ? a.Pass : (b.operation(function () {
      for (var a = b.listSelections().length, e = [], f = -1, g = 0; a > g; g++) {
        var h = b.listSelections()[g].head;if (!(h.line <= f)) {
          var i = d(h.line + (c ? 0 : 1), 0);b.replaceRange("\n", i, null, "+insertLine"), b.indentLine(i.line, null, !0), e.push({ head: i, anchor: i }), f = h.line + 1;
        }
      }b.setSelections(e);
    }), void b.execCommand("indentAuto"));
  }function k(b, c) {
    for (var e = c.ch, f = e, g = b.getLine(c.line); e && a.isWordChar(g.charAt(e - 1));) {
      --e;
    }for (; f < g.length && a.isWordChar(g.charAt(f));) {
      ++f;
    }return { from: d(c.line, e), to: d(c.line, f), word: g.slice(e, f) };
  }function m(a) {
    var b = a.getCursor(),
        c = a.scanForBracket(b, -1);if (c) for (;;) {
      var e = a.scanForBracket(b, 1);if (!e) return;if (e.ch == l.charAt(l.indexOf(c.ch) + 1)) return a.setSelection(d(c.pos.line, c.pos.ch + 1), e.pos, !1), !0;b = d(e.pos.line, e.pos.ch + 1);
    }
  }function o(b, c) {
    if (b.isReadOnly()) return a.Pass;for (var g, e = b.listSelections(), f = [], h = 0; h < e.length; h++) {
      var i = e[h];if (!i.empty()) {
        for (var j = i.from().line, k = i.to().line; h < e.length - 1 && e[h + 1].from().line == k;) {
          k = i[++h].to().line;
        }f.push(j, k);
      }
    }f.length ? g = !0 : f.push(b.firstLine(), b.lastLine()), b.operation(function () {
      for (var a = [], e = 0; e < f.length; e += 2) {
        var h = f[e],
            i = f[e + 1],
            j = d(h, 0),
            k = d(i),
            l = b.getRange(j, k, !1);c ? l.sort() : l.sort(function (a, b) {
          var c = a.toUpperCase(),
              d = b.toUpperCase();return c != d && (a = c, b = d), b > a ? -1 : a == b ? 0 : 1;
        }), b.replaceRange(l, j, k), g && a.push({ anchor: j, head: k });
      }g && b.setSelections(a, 0);
    });
  }function q(b, c) {
    b.operation(function () {
      for (var d = b.listSelections(), e = [], f = [], g = 0; g < d.length; g++) {
        var h = d[g];h.empty() ? (e.push(g), f.push("")) : f.push(c(b.getRange(h.from(), h.to())));
      }b.replaceSelections(f, "around", "case");for (var i, g = e.length - 1; g >= 0; g--) {
        var h = d[e[g]];if (!(i && a.cmpPos(h.head, i) > 0)) {
          var j = k(b, h.head);i = j.from, b.replaceRange(c(j.word), j.from, j.to);
        }
      }
    });
  }function s(b) {
    var c = b.getCursor("from"),
        d = b.getCursor("to");if (0 == a.cmpPos(c, d)) {
      var e = k(b, c);if (!e.word) return;c = e.from, d = e.to;
    }return { from: c, to: d, query: b.getRange(c, d), word: e };
  }function t(a, b) {
    var c = s(a);if (c) {
      var e = c.query,
          f = a.getSearchCursor(e, b ? c.to : c.from);(b ? f.findNext() : f.findPrevious()) ? a.setSelection(f.from(), f.to()) : (f = a.getSearchCursor(e, b ? d(a.firstLine(), 0) : a.clipPos(d(a.lastLine()))), (b ? f.findNext() : f.findPrevious()) ? a.setSelection(f.from(), f.to()) : c.word && a.setSelection(c.from, c.to));
    }
  }var b = a.keyMap.sublime = { fallthrough: "default" },
      c = a.commands,
      d = a.Pos,
      e = a.keyMap["default"] == a.keyMap.macDefault,
      f = e ? "Cmd-" : "Ctrl-";c[b["Alt-Left"] = "goSubwordLeft"] = function (a) {
    h(a, -1);
  }, c[b["Alt-Right"] = "goSubwordRight"] = function (a) {
    h(a, 1);
  }, e && (b["Cmd-Left"] = "goLineStartSmart");var i = e ? "Ctrl-Alt-" : "Ctrl-";c[b[i + "Up"] = "scrollLineUp"] = function (a) {
    var b = a.getScrollInfo();if (!a.somethingSelected()) {
      var c = a.lineAtHeight(b.top + b.clientHeight, "local");a.getCursor().line >= c && a.execCommand("goLineUp");
    }a.scrollTo(null, b.top - a.defaultTextHeight());
  }, c[b[i + "Down"] = "scrollLineDown"] = function (a) {
    var b = a.getScrollInfo();if (!a.somethingSelected()) {
      var c = a.lineAtHeight(b.top, "local") + 1;a.getCursor().line <= c && a.execCommand("goLineDown");
    }a.scrollTo(null, b.top + a.defaultTextHeight());
  }, c[b["Shift-" + f + "L"] = "splitSelectionByLine"] = function (a) {
    for (var b = a.listSelections(), c = [], e = 0; e < b.length; e++) {
      for (var f = b[e].from(), g = b[e].to(), h = f.line; h <= g.line; ++h) {
        g.line > f.line && h == g.line && 0 == g.ch || c.push({ anchor: h == f.line ? f : d(h, 0), head: h == g.line ? g : d(h) });
      }
    }a.setSelections(c, 0);
  }, b["Shift-Tab"] = "indentLess", c[b.Esc = "singleSelectionTop"] = function (a) {
    var b = a.listSelections()[0];a.setSelection(b.anchor, b.head, { scroll: !1 });
  }, c[b[f + "L"] = "selectLine"] = function (a) {
    for (var b = a.listSelections(), c = [], e = 0; e < b.length; e++) {
      var f = b[e];c.push({ anchor: d(f.from().line, 0), head: d(f.to().line + 1, 0) });
    }a.setSelections(c);
  }, b["Shift-Ctrl-K"] = "deleteLine", c[b[f + "Enter"] = "insertLineAfter"] = function (a) {
    return j(a, !1);
  }, c[b["Shift-" + f + "Enter"] = "insertLineBefore"] = function (a) {
    return j(a, !0);
  }, c[b[f + "D"] = "selectNextOccurrence"] = function (b) {
    var c = b.getCursor("from"),
        e = b.getCursor("to"),
        f = b.state.sublimeFindFullWord == b.doc.sel;if (0 == a.cmpPos(c, e)) {
      var g = k(b, c);if (!g.word) return;b.setSelection(g.from, g.to), f = !0;
    } else {
      var h = b.getRange(c, e),
          i = f ? new RegExp("\\b" + h + "\\b") : h,
          j = b.getSearchCursor(i, e);j.findNext() ? b.addSelection(j.from(), j.to()) : (j = b.getSearchCursor(i, d(b.firstLine(), 0)), j.findNext() && b.addSelection(j.from(), j.to()));
    }f && (b.state.sublimeFindFullWord = b.doc.sel);
  };var l = "(){}[]";c[b["Shift-" + f + "Space"] = "selectScope"] = function (a) {
    m(a) || a.execCommand("selectAll");
  }, c[b["Shift-" + f + "M"] = "selectBetweenBrackets"] = function (b) {
    return m(b) ? void 0 : a.Pass;
  }, c[b[f + "M"] = "goToBracket"] = function (b) {
    b.extendSelectionsBy(function (c) {
      var e = b.scanForBracket(c.head, 1);if (e && 0 != a.cmpPos(e.pos, c.head)) return e.pos;var f = b.scanForBracket(c.head, -1);return f && d(f.pos.line, f.pos.ch + 1) || c.head;
    });
  };var n = e ? "Cmd-Ctrl-" : "Shift-Ctrl-";c[b[n + "Up"] = "swapLineUp"] = function (b) {
    if (b.isReadOnly()) return a.Pass;for (var c = b.listSelections(), e = [], f = b.firstLine() - 1, g = [], h = 0; h < c.length; h++) {
      var i = c[h],
          j = i.from().line - 1,
          k = i.to().line;g.push({ anchor: d(i.anchor.line - 1, i.anchor.ch), head: d(i.head.line - 1, i.head.ch) }), 0 != i.to().ch || i.empty() || --k, j > f ? e.push(j, k) : e.length && (e[e.length - 1] = k), f = k;
    }b.operation(function () {
      for (var a = 0; a < e.length; a += 2) {
        var c = e[a],
            f = e[a + 1],
            h = b.getLine(c);b.replaceRange("", d(c, 0), d(c + 1, 0), "+swapLine"), f > b.lastLine() ? b.replaceRange("\n" + h, d(b.lastLine()), null, "+swapLine") : b.replaceRange(h + "\n", d(f, 0), null, "+swapLine");
      }b.setSelections(g), b.scrollIntoView();
    });
  }, c[b[n + "Down"] = "swapLineDown"] = function (b) {
    if (b.isReadOnly()) return a.Pass;for (var c = b.listSelections(), e = [], f = b.lastLine() + 1, g = c.length - 1; g >= 0; g--) {
      var h = c[g],
          i = h.to().line + 1,
          j = h.from().line;0 != h.to().ch || h.empty() || i--, f > i ? e.push(i, j) : e.length && (e[e.length - 1] = j), f = j;
    }b.operation(function () {
      for (var a = e.length - 2; a >= 0; a -= 2) {
        var c = e[a],
            f = e[a + 1],
            g = b.getLine(c);c == b.lastLine() ? b.replaceRange("", d(c - 1), d(c), "+swapLine") : b.replaceRange("", d(c, 0), d(c + 1, 0), "+swapLine"), b.replaceRange(g + "\n", d(f, 0), null, "+swapLine");
      }b.scrollIntoView();
    });
  }, c[b[f + "/"] = "toggleCommentIndented"] = function (a) {
    a.toggleComment({ indent: !0 });
  }, c[b[f + "J"] = "joinLines"] = function (a) {
    for (var b = a.listSelections(), c = [], e = 0; e < b.length; e++) {
      for (var f = b[e], g = f.from(), h = g.line, i = f.to().line; e < b.length - 1 && b[e + 1].from().line == i;) {
        i = b[++e].to().line;
      }c.push({ start: h, end: i, anchor: !f.empty() && g });
    }a.operation(function () {
      for (var b = 0, e = [], f = 0; f < c.length; f++) {
        for (var i, g = c[f], h = g.anchor && d(g.anchor.line - b, g.anchor.ch), j = g.start; j <= g.end; j++) {
          var k = j - b;j == g.end && (i = d(k, a.getLine(k).length + 1)), k < a.lastLine() && (a.replaceRange(" ", d(k), d(k + 1, /^\s*/.exec(a.getLine(k + 1))[0].length)), ++b);
        }e.push({ anchor: h || i, head: i });
      }a.setSelections(e, 0);
    });
  }, c[b["Shift-" + f + "D"] = "duplicateLine"] = function (a) {
    a.operation(function () {
      for (var b = a.listSelections().length, c = 0; b > c; c++) {
        var e = a.listSelections()[c];e.empty() ? a.replaceRange(a.getLine(e.head.line) + "\n", d(e.head.line, 0)) : a.replaceRange(a.getRange(e.from(), e.to()), e.from());
      }a.scrollIntoView();
    });
  }, b[f + "T"] = "transposeChars", c[b.F9 = "sortLines"] = function (a) {
    o(a, !0);
  }, c[b[f + "F9"] = "sortLinesInsensitive"] = function (a) {
    o(a, !1);
  }, c[b.F2 = "nextBookmark"] = function (a) {
    var b = a.state.sublimeBookmarks;if (b) for (; b.length;) {
      var c = b.shift(),
          d = c.find();if (d) return b.push(c), a.setSelection(d.from, d.to);
    }
  }, c[b["Shift-F2"] = "prevBookmark"] = function (a) {
    var b = a.state.sublimeBookmarks;if (b) for (; b.length;) {
      b.unshift(b.pop());var c = b[b.length - 1].find();if (c) return a.setSelection(c.from, c.to);b.pop();
    }
  }, c[b[f + "F2"] = "toggleBookmark"] = function (a) {
    for (var b = a.listSelections(), c = a.state.sublimeBookmarks || (a.state.sublimeBookmarks = []), d = 0; d < b.length; d++) {
      for (var e = b[d].from(), f = b[d].to(), g = a.findMarks(e, f), h = 0; h < g.length; h++) {
        if (g[h].sublimeBookmark) {
          g[h].clear();for (var i = 0; i < c.length; i++) {
            c[i] == g[h] && c.splice(i--, 1);
          }break;
        }
      }h == g.length && c.push(a.markText(e, f, { sublimeBookmark: !0, clearWhenEmpty: !1 }));
    }
  }, c[b["Shift-" + f + "F2"] = "clearBookmarks"] = function (a) {
    var b = a.state.sublimeBookmarks;if (b) for (var c = 0; c < b.length; c++) {
      b[c].clear();
    }b.length = 0;
  }, c[b["Alt-F2"] = "selectBookmarks"] = function (a) {
    var b = a.state.sublimeBookmarks,
        c = [];if (b) for (var d = 0; d < b.length; d++) {
      var e = b[d].find();e ? c.push({ anchor: e.from, head: e.to }) : b.splice(d--, 0);
    }c.length && a.setSelections(c, 0);
  }, b["Alt-Q"] = "wrapLines";var p = f + "K ";b[p + f + "Backspace"] = "delLineLeft", c[b.Backspace = "smartBackspace"] = function (b) {
    return b.somethingSelected() ? a.Pass : void b.operation(function () {
      for (var c = b.listSelections(), e = b.getOption("indentUnit"), f = c.length - 1; f >= 0; f--) {
        var g = c[f].head,
            h = b.getRange({ line: g.line, ch: 0 }, g),
            i = a.countColumn(h, null, b.getOption("tabSize")),
            j = b.findPosH(g, -1, "char", !1);if (h && !/\S/.test(h) && i % e == 0) {
          var k = new d(g.line, a.findColumn(h, i - e, e));k.ch != g.ch && (j = k);
        }b.replaceRange("", j, g, "+delete");
      }
    });
  }, c[b[p + f + "K"] = "delLineRight"] = function (a) {
    a.operation(function () {
      for (var b = a.listSelections(), c = b.length - 1; c >= 0; c--) {
        a.replaceRange("", b[c].anchor, d(b[c].to().line), "+delete");
      }a.scrollIntoView();
    });
  }, c[b[p + f + "U"] = "upcaseAtCursor"] = function (a) {
    q(a, function (a) {
      return a.toUpperCase();
    });
  }, c[b[p + f + "L"] = "downcaseAtCursor"] = function (a) {
    q(a, function (a) {
      return a.toLowerCase();
    });
  }, c[b[p + f + "Space"] = "setSublimeMark"] = function (a) {
    a.state.sublimeMark && a.state.sublimeMark.clear(), a.state.sublimeMark = a.setBookmark(a.getCursor());
  }, c[b[p + f + "A"] = "selectToSublimeMark"] = function (a) {
    var b = a.state.sublimeMark && a.state.sublimeMark.find();b && a.setSelection(a.getCursor(), b);
  }, c[b[p + f + "W"] = "deleteToSublimeMark"] = function (b) {
    var c = b.state.sublimeMark && b.state.sublimeMark.find();if (c) {
      var d = b.getCursor(),
          e = c;if (a.cmpPos(d, e) > 0) {
        var f = e;e = d, d = f;
      }b.state.sublimeKilled = b.getRange(d, e), b.replaceRange("", d, e);
    }
  }, c[b[p + f + "X"] = "swapWithSublimeMark"] = function (a) {
    var b = a.state.sublimeMark && a.state.sublimeMark.find();b && (a.state.sublimeMark.clear(), a.state.sublimeMark = a.setBookmark(a.getCursor()), a.setCursor(b));
  }, c[b[p + f + "Y"] = "sublimeYank"] = function (a) {
    null != a.state.sublimeKilled && a.replaceSelection(a.state.sublimeKilled, null, "paste");
  }, b[p + f + "G"] = "clearBookmarks", c[b[p + f + "C"] = "showInCenter"] = function (a) {
    var b = a.cursorCoords(null, "local");a.scrollTo(null, (b.top + b.bottom) / 2 - a.getScrollInfo().clientHeight / 2);
  };var r = e ? "Ctrl-Shift-" : "Ctrl-Alt-";c[b[r + "Up"] = "selectLinesUpward"] = function (a) {
    a.operation(function () {
      for (var b = a.listSelections(), c = 0; c < b.length; c++) {
        var e = b[c];e.head.line > a.firstLine() && a.addSelection(d(e.head.line - 1, e.head.ch));
      }
    });
  }, c[b[r + "Down"] = "selectLinesDownward"] = function (a) {
    a.operation(function () {
      for (var b = a.listSelections(), c = 0; c < b.length; c++) {
        var e = b[c];e.head.line < a.lastLine() && a.addSelection(d(e.head.line + 1, e.head.ch));
      }
    });
  }, c[b[f + "F3"] = "findUnder"] = function (a) {
    t(a, !0);
  }, c[b["Shift-" + f + "F3"] = "findUnderPrevious"] = function (a) {
    t(a, !1);
  }, c[b["Alt-F3"] = "findAllUnder"] = function (a) {
    var b = s(a);if (b) {
      for (var c = a.getSearchCursor(b.query), d = [], e = -1; c.findNext();) {
        d.push({ anchor: c.from(), head: c.to() }), c.from().line <= b.from.line && c.from().ch <= b.from.ch && e++;
      }a.setSelections(d, e);
    }
  }, b["Shift-" + f + "["] = "fold", b["Shift-" + f + "]"] = "unfold", b[p + f + "0"] = b[p + f + "j"] = "unfoldAll", b[f + "I"] = "findIncremental", b["Shift-" + f + "I"] = "findIncrementalReverse", b[f + "H"] = "replace", b.F3 = "findNext", b["Shift-F3"] = "findPrev", a.normalizeKeyMap(b);
});