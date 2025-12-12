/**
 * Tenbyte VidInfra Admin JavaScript
 *
 * @package VidinfraPlayer
 */

(function ($) {
  "use strict";

  $(document).ready(function () {
    // Initialize WordPress Color Picker
    if (typeof $.fn.wpColorPicker !== "undefined") {
      $(".vidinfra-color-picker").wpColorPicker({
        defaultColor: "#ffffff",
        change: function (event, ui) {
          // Optional: Add any custom change handler here
        },
        clear: function () {
          // Optional: Add any custom clear handler here
        },
      });
    }

    // Copy shortcode to clipboard using modern Clipboard API
    $(".vidinfra-player-sidebar-box code").on("click", function () {
      var text = $(this).text();
      var $this = $(this);

      // Use modern Clipboard API if available
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard
          .writeText(text)
          .then(function () {
            showCopyFeedback($this);
          })
          .catch(function () {
            fallbackCopyToClipboard(text, $this);
          });
      } else {
        fallbackCopyToClipboard(text, $this);
      }
    });

    function showCopyFeedback($element) {
      var $feedback = $('<span class="copy-feedback"> ✓ Copied!</span>');
      $element.append($feedback);

      setTimeout(function () {
        $feedback.fadeOut(function () {
          $(this).remove();
        });
      }, 2000);
    }

    function fallbackCopyToClipboard(text, $element) {
      var $temp = $("<textarea>");
      $("body").append($temp);
      $temp.val(text).select();

      try {
        var successful = document.execCommand("copy");
        if (successful) {
          showCopyFeedback($element);
        }
      } catch (err) {
        // Silently fail
      }

      $temp.remove();
    }

    // Watermark settings toggle functionality
    function initWatermarkFields() {
      // Add CSS classes to rows for better control - do this immediately
      var $line1TextRow = $("#watermark_text_field").closest("tr");
      var $line1SizeRow = $("#watermark_font_size").closest("tr");
      var $line1ColorRow = $("#watermark_font_color").closest("tr");
      var $line1OpacityRow = $("#watermark_font_opacity").closest("tr");
      var $line1HeadingRow = $('tr:has(th:contains("Line 1:"))');
      var $addLine2Row = $('tr:has(th:contains("Add Second Line"))');

      var $line2TextRow = $("#watermark_text_field_line2").closest("tr");
      var $line2SizeRow = $("#watermark_font_size_line2").closest("tr");
      var $line2ColorRow = $("#watermark_font_color_line2").closest("tr");
      var $line2OpacityRow = $("#watermark_font_opacity_line2").closest("tr");
      var $line2HeadingRow = $('tr:has(th:contains("Line 2:"))');
      var $dividerRow = $("tr:has(td):has(hr)");

      // Add classes immediately
      $line1TextRow
        .add($line1SizeRow)
        .add($line1ColorRow)
        .add($line1OpacityRow)
        .add($line1HeadingRow)
        .add($addLine2Row)
        .addClass("vidinfra-watermark-field-line1");

      $line2TextRow
        .add($line2SizeRow)
        .add($line2ColorRow)
        .add($line2OpacityRow)
        .add($line2HeadingRow)
        .addClass("vidinfra-watermark-field-line2");

      $dividerRow.addClass("vidinfra-watermark-divider");
    }

    function toggleWatermarkFields() {
      var watermarkEnabled = $("#watermark_enable").is(":checked");

      if (watermarkEnabled) {
        $(".vidinfra-watermark-field-line1").css("display", "table-row");
        toggleLine2Fields();
      } else {
        $(".vidinfra-watermark-field-line1").css("display", "none");
        $(".vidinfra-watermark-field-line2, .vidinfra-watermark-divider").css(
          "display",
          "none"
        );
      }
    }

    function toggleLine2Fields() {
      var watermarkEnabled = $("#watermark_enable").is(":checked");
      var line2Enabled = $("#watermark_enable_line2").is(":checked");

      if (watermarkEnabled && line2Enabled) {
        $(".vidinfra-watermark-divider, .vidinfra-watermark-field-line2").css(
          "display",
          "table-row"
        );
      } else {
        $(".vidinfra-watermark-field-line2").css("display", "none");
        // Only show divider if Line 2 is enabled
        if (!line2Enabled) {
          $(".vidinfra-watermark-divider").css("display", "none");
        }
      }
    }

    // Initialize classes and visibility immediately on page load
    initWatermarkFields();
    toggleWatermarkFields();

    // Handle watermark enable checkbox change
    $("#watermark_enable").on("change", function () {
      toggleWatermarkFields();
    });

    // Handle Line 2 enable checkbox change
    $("#watermark_enable_line2").on("change", function () {
      toggleLine2Fields();
    });
  });
})(jQuery);
