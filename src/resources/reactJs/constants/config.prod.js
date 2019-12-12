/* eslint-disable no-console */
// import React from "react";
// import { Field, ErrorMessage } from "formik";
import LanguageStrings from "./LanguageStrings";

const reactLanguage = window.reactLanguage ? window.reactLanguage : "en";

LanguageStrings.setLanguage(reactLanguage);
export const Lang = LanguageStrings;

let api_uri = "/cmsadmin";
let project_name = "Laravel CMS";
let project_name_plural = "Laravel CMS";
// if (window.location.href.indexOf(".test") != -1) {
//   api_uri = "http://react.js.test:9192/api";
// } else if (window.location.href.indexOf("127.0.0.1") != -1) {
//   api_uri = "/api";
// }

export const DATE_FORMATE = "DD/MM/YYYY";

export const API_URI = api_uri;
export const WWW_SITE = "...";
export const MEMBER_SITE = "...";

export const POINT_FUC = (num = 1) =>
  num > 1 || num < -1 ? Lang.points : Lang.point;
export const PROJECT_NAME_FUC = (num = 1) =>
  num > 1 ? project_name_plural : project_name;
export const PROJECT_NAME = project_name;
export const PROJECT_NAME_PLURAL = project_name_plural;

export const myLog = variable => {
  console.log(variable);
};

export const POINT_FUNC = (num = 1, lang = "english") => {
  if (lang == "english") {
    return num > 1 || num < -1 ? Lang.points : Lang.point;
  } else if (lang == "chinese") {
    return num > 1 || num < -1 ? Lang.points : Lang.point;
  }
};

// export const CustomInput2 = props => (
//   <div className="form-group row">
//     <label className="col-sm-4 col-form-label text-right">{props.label}</label>
//     <div className="col-sm">
//       {!props.children && (
//         <>
//           <Field
//             type={props.type || "text"}
//             name={props.name}
//             component={props.component || "input"}
//             className={`${
//               props.type == "checkbox" ? "align-middle" : "form-control"
//             } ${props.className || ""}`}
//           />
//           <ErrorMessage
//             name={props.name}
//             component="div"
//             className="text-danger"
//           />
//         </>
//       )}
//       {props.children && <>{props.children}</>}
//       {props.help && (
//         <small className="text-secondary text-help">{props.help}</small>
//       )}
//     </div>
//   </div>
// );

// export const CustomInput = props => {
//   let inputClass = "form-control";
//   if (props.type == "checkbox") {
//     inputClass = "align-middle";
//   } else if (props.component == "textarea") {
//     inputClass = "md-textarea md-textarea-auto2 form-control ";
//   }
//   return (
//     <div className="md-form md-outline">
//       {!props.children && (
//         <>
//           {props.iconClass && <i className={props.iconClass} />}
//           <label
//             htmlFor={`input-${props.name}`}
//             className="md-input-label active"
//           >
//             {props.label}
//           </label>

//           <Field
//             type={props.type || "text"}
//             name={props.name}
//             id={`${props.formName || "input"}-${props.name}`}
//             multiple={props.type == "file" ? true : false}
//             component={props.component || "input"}
//             className={`${inputClass} ${props.className || ""}`}
//           />

//           <ErrorMessage
//             name={props.name}
//             component="div"
//             className="text-danger small"
//           />
//         </>
//       )}
//       {props.children && <>{props.children}</>}
//       {props.help && (
//         <small className="form-text text-muted text-help">{props.help}</small>
//       )}
//     </div>
//   );
// };

// export const materialDesign = todo => {
//     if (todo == "form") {
//         (function($) {
//             var inputSelector = "".concat(
//                 [
//                     "text",
//                     "password",
//                     "email",
//                     "url",
//                     "tel",
//                     "number",
//                     "search",
//                     "search-md"
//                 ]
//                     .map(function(selector) {
//                         return "input[type=".concat(selector, "]");
//                     })
//                     .join(", "),
//                 ", textarea"
//             );
//             var textAreaSelector = ".materialize-textarea";

//             var updateTextFields = function updateTextFields($input) {
//                 var $labelAndIcon = $input.siblings("label, i");
//                 var hasValue = $input.val().length;
//                 var hasPlaceholder = $input.attr("placeholder");
//                 var addOrRemove = "".concat(
//                     hasValue || hasPlaceholder ? "add" : "remove",
//                     "Class"
//                 );
//                 $labelAndIcon[addOrRemove]("active");
//             };

//             var validateField = function validateField($input) {
//                 if ($input.hasClass("validate")) {
//                     var value = $input.val();
//                     var noValue = !value.length;
//                     var isValid = !$input[0].validity.badInput;

//                     if (noValue && isValid) {
//                         $input.removeClass("valid").removeClass("invalid");
//                     } else {
//                         var valid = $input.is(":valid");
//                         var length = Number($input.attr("length")) || 0;

//                         if (valid && (!length || length > value.length)) {
//                             $input.removeClass("invalid").addClass("valid");
//                         } else {
//                             $input.removeClass("valid").addClass("invalid");
//                         }
//                     }
//                 }
//             };

//             var textAreaAutoResize = function textAreaAutoResize() {
//                 var $textarea = $(_this);

//                 if ($textarea.val().length) {
//                     var $hiddenDiv = $(".hiddendiv");
//                     var fontFamily = $textarea.css("font-family");
//                     var fontSize = $textarea.css("font-size");

//                     if (fontSize) {
//                         $hiddenDiv.css("font-size", fontSize);
//                     }

//                     if (fontFamily) {
//                         $hiddenDiv.css("font-family", fontFamily);
//                     }

//                     if ($textarea.attr("wrap") === "off") {
//                         $hiddenDiv
//                             .css("overflow-wrap", "normal")
//                             .css("white-space", "pre");
//                     }

//                     $hiddenDiv.text("".concat($textarea.val(), "\n"));
//                     var content = $hiddenDiv.html().replace(/\n/g, "<br>");
//                     $hiddenDiv.html(content); // When textarea is hidden, width goes crazy.
//                     // Approximate with half of window size

//                     $hiddenDiv.css(
//                         "width",
//                         $textarea.is(":visible")
//                             ? $textarea.width()
//                             : $(window).width() / 2
//                     );
//                     $textarea.css("height", $hiddenDiv.height());
//                 }
//             };

//             $(inputSelector).each(function(index, input) {
//                 var $this = $(input);
//                 var $labelAndIcon = $this.siblings("label, i");
//                 updateTextFields($this);
//                 var isValid = input.validity.badInput;

//                 if (isValid) {
//                     $labelAndIcon.addClass("active");
//                 } else {
//                     //console.log(input.validity)
//                     $labelAndIcon.addClass("inValid-test active");
//                 }
//             });
//             $(document).on("focus", inputSelector, function(e) {
//                 $(e.target)
//                     .siblings("label, i")
//                     .addClass("active");
//             });
//             $(document).on("blur", inputSelector, function(e) {
//                 var $this = $(e.target);
//                 var noValue = !$this.val();
//                 var invalid = !e.target.validity.badInput;
//                 var noPlaceholder = $this.attr("placeholder") === undefined;

//                 if (noValue && invalid && noPlaceholder) {
//                     $this.siblings("label, i").removeClass("active");
//                 }

//                 validateField($this);
//             });
//             $(document).on("change", inputSelector, function(e) {
//                 var $this = $(e.target);
//                 updateTextFields($this);
//                 validateField($this);
//             });
//             $("input[autofocus]")
//                 .siblings("label, i")
//                 .addClass("active");
//             $(document).on("reset", function(e) {
//                 var $formReset = $(e.target);

//                 if ($formReset.is("form")) {
//                     var $formInputs = $formReset.find(inputSelector);
//                     $formInputs
//                         .removeClass("valid")
//                         .removeClass("invalid")
//                         .each(function(index, input) {
//                             var $this = $(input);
//                             var noDefaultValue = !$this.val();
//                             var noPlaceholder = !$this.attr("placeholder");

//                             if (noDefaultValue && noPlaceholder) {
//                                 $this
//                                     .siblings("label, i")
//                                     .removeClass("active");
//                             }
//                         });
//                     $formReset
//                         .find("select.initialized")
//                         .each(function(index, select) {
//                             var $select = $(select);
//                             var $visibleInput = $select.siblings(
//                                 "input.select-dropdown"
//                             );
//                             var defaultValue = $select
//                                 .children("[selected]")
//                                 .val();
//                             $select.val(defaultValue);
//                             $visibleInput.val(defaultValue);
//                         });
//                 }
//             });

//             function init() {
//                 var $text = $(".md-textarea-auto");

//                 if ($text.length) {
//                     var observe;

//                     if (window.attachEvent) {
//                         observe = function observe(element, event, handler) {
//                             element.attachEvent("on".concat(event), handler);
//                         };
//                     } else {
//                         observe = function observe(element, event, handler) {
//                             element.addEventListener(event, handler, false);
//                         };
//                     }

//                     $text.each(function() {
//                         var self = this;

//                         function resize() {
//                             self.style.height = "auto";
//                             self.style.height = "".concat(
//                                 self.scrollHeight,
//                                 "px"
//                             );
//                         }

//                         function delayedResize() {
//                             window.setTimeout(resize, 0);
//                         }

//                         observe(self, "change", resize);
//                         observe(self, "cut", delayedResize);
//                         observe(self, "paste", delayedResize);
//                         observe(self, "drop", delayedResize);
//                         observe(self, "keydown", delayedResize);
//                         resize();
//                     });
//                 }
//             }

//             init();
//             var $body = $("body");

//             if (!$(".hiddendiv").first().length) {
//                 var $hiddenDiv = $('<div class="hiddendiv common"></div>');
//                 $body.append($hiddenDiv);
//             }

//             $(textAreaSelector).each(textAreaAutoResize);
//             $body.on("keyup keydown", textAreaSelector, textAreaAutoResize);
//         })(jQuery);
//     }
// };
