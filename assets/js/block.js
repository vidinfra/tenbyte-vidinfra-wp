/**
 * Vidinfra Player Gutenberg Block
 *
 * @package VidinfraPlayer
 */

(function (blocks, element, blockEditor, components, i18n, data) {
  "use strict";

  // Ensure all dependencies are available
  if (
    !blocks ||
    !element ||
    !blockEditor ||
    !components ||
    !i18n ||
    !blocks.registerBlockType
  ) {
    return;
  }

  var el = element.createElement;
  var registerBlockType = blocks.registerBlockType;
  var InspectorControls = blockEditor.InspectorControls;
  var useBlockProps = blockEditor.useBlockProps;
  var TextControl = components.TextControl;
  var SelectControl = components.SelectControl;
  var ToggleControl = components.ToggleControl;
  var PanelBody = components.PanelBody;
  var PanelRow = components.PanelRow;
  var __ = i18n.__;

  registerBlockType("vidinfra/player", {
    apiVersion: 2,
    title: __("Tenbyte Vidinfra", "vidinfra-player"),
    description: __("Embed a Vidinfra video player", "vidinfra-player"),
    icon: el("img", {
      src: vidinfraPlayerData.iconUrl,
      alt: "Tenbyte Vidinfra",
      style: { width: "24px", height: "24px" },
    }),
    category: "embed",
    keywords: [
      __("video", "vidinfra-player"),
      __("player", "vidinfra-player"),
      __("vidinfra", "vidinfra-player"),
    ],
    supports: {
      html: false,
    },

    attributes: {
      video_id: {
        type: "string",
        default: "",
      },
      library_id: {
        type: "string",
        default: "",
      },
      player_id: {
        type: "string",
        default: "",
      },
      width: {
        type: "string",
        default: "",
      },
      height: {
        type: "string",
        default: "",
      },
      autoplay: {
        type: "boolean",
        default: false,
      },
      loop: {
        type: "boolean",
        default: false,
      },
      muted: {
        type: "boolean",
        default: false,
      },
      controls: {
        type: "boolean",
        default: true,
      },
      preload: {
        type: "boolean",
        default: true,
      },
      aspect_ratio: {
        type: "string",
        default: "16:9",
      },
      loading: {
        type: "string",
        default: "eager",
      },
      class_name: {
        type: "string",
        default: "",
      },
    },

    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;
      var blockProps = useBlockProps();

      function onChangeVideoId(value) {
        setAttributes({ video_id: value });
      }

      function onChangeLibraryId(value) {
        setAttributes({ library_id: value });
      }

      function onChangePlayerId(value) {
        setAttributes({ player_id: value });
      }

      function onChangeAutoplay(value) {
        setAttributes({ autoplay: value });
      }

      function onChangeLoop(value) {
        setAttributes({ loop: value });
      }

      function onChangeMuted(value) {
        setAttributes({ muted: value });
      }

      function onChangeControls(value) {
        setAttributes({ controls: value });
      }

      function onChangePreload(value) {
        setAttributes({ preload: value });
      }

      function onChangeAspectRatio(value) {
        setAttributes({ aspect_ratio: value });
      }

      function onChangeWidth(value) {
        setAttributes({ width: value });
      }

      function onChangeHeight(value) {
        setAttributes({ height: value });
      }

      function onChangeLoading(value) {
        setAttributes({ loading: value });
      }

      function onChangeClassName(value) {
        setAttributes({ class_name: value });
      }

      return [
        el(
          InspectorControls,
          { key: "inspector" },
          el(
            PanelBody,
            {
              title: __("Video Settings", "vidinfra-player"),
              initialOpen: true,
            },
            el(TextControl, {
              label: __("Video ID (Required)", "vidinfra-player"),
              value: attributes.video_id,
              onChange: onChangeVideoId,
              help: __("Enter the Vidinfra video ID", "vidinfra-player"),
            }),
            el(TextControl, {
              label: __("Library ID", "vidinfra-player"),
              value: attributes.library_id,
              onChange: onChangeLibraryId,
              help: __(
                "Optional: Override default library ID from settings",
                "vidinfra-player"
              ),
            }),
            el(TextControl, {
              label: __("Player ID", "vidinfra-player"),
              value: attributes.player_id,
              onChange: onChangePlayerId,
              help: __(
                'Optional: Enter the player ID (defaults to "default")',
                "vidinfra-player"
              ),
            }),
            el(SelectControl, {
              label: __("Aspect Ratio", "vidinfra-player"),
              value: attributes.aspect_ratio,
              options: [
                { label: "16:9", value: "16:9" },
                { label: "4:3", value: "4:3" },
                { label: "1:1", value: "1:1" },
                { label: "21:9", value: "21:9" },
                { label: "9:16", value: "9:16" },
              ],
              onChange: onChangeAspectRatio,
            }),
            el(TextControl, {
              label: __("Width", "vidinfra-player"),
              value: attributes.width,
              onChange: onChangeWidth,
              help: __(
                "Optional: Width (number or string with unit)",
                "vidinfra-player"
              ),
            }),
            el(TextControl, {
              label: __("Height", "vidinfra-player"),
              value: attributes.height,
              onChange: onChangeHeight,
              help: __(
                "Optional: Height (number or string with unit)",
                "vidinfra-player"
              ),
            })
          ),

          el(
            PanelBody,
            {
              title: __("Player Options", "vidinfra-player"),
              initialOpen: false,
            },
            el(ToggleControl, {
              label: __("Autoplay", "vidinfra-player"),
              checked: attributes.autoplay,
              onChange: onChangeAutoplay,
            }),
            el(ToggleControl, {
              label: __("Loop", "vidinfra-player"),
              checked: attributes.loop,
              onChange: onChangeLoop,
            }),
            el(ToggleControl, {
              label: __("Muted", "vidinfra-player"),
              checked: attributes.muted,
              onChange: onChangeMuted,
            }),
            el(ToggleControl, {
              label: __("Show Controls", "vidinfra-player"),
              checked: attributes.controls,
              onChange: onChangeControls,
            }),
            el(ToggleControl, {
              label: __("Preload", "vidinfra-player"),
              checked: attributes.preload,
              onChange: onChangePreload,
            }),
            el(SelectControl, {
              label: __("Loading", "vidinfra-player"),
              value: attributes.loading,
              options: [
                { label: __("Eager", "vidinfra-player"), value: "eager" },
                { label: __("Lazy", "vidinfra-player"), value: "lazy" },
              ],
              onChange: onChangeLoading,
            }),
            el(TextControl, {
              label: __("CSS Class Name", "vidinfra-player"),
              value: attributes.class_name,
              onChange: onChangeClassName,
              help: __("Optional: Additional CSS classes", "vidinfra-player"),
            })
          )
        ),

        el(
          "div",
          blockProps,
          attributes.video_id
            ? el(
                "div",
                {
                  className: "vidinfra-player-block-preview",
                  style: {
                    padding: "24px",
                    backgroundColor: "#fff",
                    border: "1px solid #e0e0e0",
                    borderRadius: "8px",
                    boxShadow: "0 2px 4px rgba(0,0,0,0.05)",
                  },
                },
                el(
                  "div",
                  {
                    style: {
                      display: "flex",
                      alignItems: "center",
                      marginBottom: "16px",
                      paddingBottom: "16px",
                      borderBottom: "1px solid #e0e0e0",
                    },
                  },
                  el("img", {
                    src: vidinfraPlayerData.iconUrl,
                    alt: "Tenbyte Vidinfra",
                    style: {
                      width: "24px",
                      height: "24px",
                      marginRight: "8px",
                    },
                  }),
                  el(
                    "span",
                    {
                      style: {
                        fontSize: "14px",
                        fontWeight: "600",
                        color: "#1e1e1e",
                      },
                    },
                    __("Tenbyte Vidinfra Player", "vidinfra-player")
                  )
                ),
                el(
                  "div",
                  {
                    style: {
                      position: "relative",
                      paddingTop: "56.25%",
                      backgroundColor: "#000",
                      borderRadius: "6px",
                      overflow: "hidden",
                      marginBottom: "16px",
                    },
                  },
                  el(
                    "div",
                    {
                      style: {
                        position: "absolute",
                        top: "50%",
                        left: "50%",
                        transform: "translate(-50%, -50%)",
                        textAlign: "center",
                      },
                    },
                    el("span", {
                      className: "dashicons dashicons-video-alt3",
                      style: {
                        fontSize: "64px",
                        width: "64px",
                        height: "64px",
                        color: "#fff",
                        opacity: "0.8",
                      },
                    }),
                    el(
                      "div",
                      {
                        style: {
                          color: "#fff",
                          fontSize: "12px",
                          marginTop: "12px",
                          opacity: "0.9",
                        },
                      },
                      __("Preview in editor", "vidinfra-player")
                    )
                  )
                ),
                el(
                  "div",
                  {
                    style: {
                      display: "flex",
                      flexDirection: "column",
                      gap: "8px",
                      fontSize: "13px",
                      color: "#757575",
                    },
                  },
                  el(
                    "div",
                    {
                      style: {
                        display: "flex",
                        justifyContent: "space-between",
                      },
                    },
                    el(
                      "span",
                      { style: { fontWeight: "500", color: "#1e1e1e" } },
                      __("Video ID:", "vidinfra-player")
                    ),
                    el(
                      "span",
                      {
                        style: {
                          fontFamily: "monospace",
                          backgroundColor: "#f5f5f5",
                          padding: "2px 8px",
                          borderRadius: "4px",
                        },
                      },
                      attributes.video_id
                    )
                  ),
                  attributes.library_id
                    ? el(
                        "div",
                        {
                          style: {
                            display: "flex",
                            justifyContent: "space-between",
                          },
                        },
                        el(
                          "span",
                          { style: { fontWeight: "500", color: "#1e1e1e" } },
                          __("Library ID:", "vidinfra-player")
                        ),
                        el(
                          "span",
                          {
                            style: {
                              fontFamily: "monospace",
                              backgroundColor: "#f5f5f5",
                              padding: "2px 8px",
                              borderRadius: "4px",
                            },
                          },
                          attributes.library_id
                        )
                      )
                    : null,
                  el(
                    "div",
                    {
                      style: {
                        display: "flex",
                        justifyContent: "space-between",
                      },
                    },
                    el(
                      "span",
                      { style: { fontWeight: "500", color: "#1e1e1e" } },
                      __("Aspect Ratio:", "vidinfra-player")
                    ),
                    el("span", null, attributes.aspect_ratio)
                  )
                )
              )
            : el(
                "div",
                {
                  className: "vidinfra-player-block-placeholder",
                  style: {
                    padding: "40px 20px",
                    backgroundColor: "#fff",
                    border: "1px solid #ddd",
                    borderRadius: "4px",
                    textAlign: "center",
                    maxWidth: "600px",
                    margin: "0 auto",
                  },
                },
                el(
                  "div",
                  {
                    style: {
                      display: "flex",
                      alignItems: "center",
                      justifyContent: "center",
                      marginBottom: "20px",
                    },
                  },
                  el("img", {
                    src: vidinfraPlayerData.iconUrl,
                    alt: "Tenbyte Vidinfra",
                    style: {
                      width: "22px",
                      height: "22px",
                      marginRight: "10px",
                    },
                  }),
                  el(
                    "h3",
                    {
                      style: {
                        margin: "0",
                        fontSize: "18px",
                        fontWeight: "600",
                        color: "#1e1e1e",
                      },
                    },
                    __("Tenbyte Vidinfra Embed", "vidinfra-player")
                  )
                ),
                el(
                  "p",
                  {
                    style: {
                      margin: "0 0 20px",
                      color: "#757575",
                      fontSize: "14px",
                    },
                  },
                  __(
                    "Enter video id to the content you want to display on your site",
                    "vidinfra-player"
                  )
                ),
                el(
                  "div",
                  {
                    style: {
                      display: "flex",
                      gap: "10px",
                      alignItems: "flex-end",
                    },
                  },
                  el("input", {
                    type: "text",
                    placeholder: __("Video ID", "vidinfra-player"),
                    value: attributes.video_id,
                    onChange: function (event) {
                      onChangeVideoId(event.target.value);
                    },
                    style: {
                      flex: "1",
                      padding: "8px 12px",
                      border: "1px solid #ddd",
                      borderRadius: "4px",
                      fontSize: "14px",
                    },
                  }),
                  el(
                    "button",
                    {
                      className: "button button-primary",
                      onClick: function () {
                        // Button triggers attribute update
                        if (attributes.video_id) {
                          setAttributes({ video_id: attributes.video_id });
                        }
                      },
                      style: {
                        padding: "8px 16px",
                        cursor: "pointer",
                      },
                    },
                    __("Embed", "vidinfra-player")
                  )
                )
              )
        ),
      ];
    },

    save: function () {
      // Rendering is handled server-side via PHP
      return null;
    },
  });
})(
  window.wp && window.wp.blocks,
  window.wp && window.wp.element,
  window.wp && window.wp.blockEditor,
  window.wp && window.wp.components,
  window.wp && window.wp.i18n,
  window.wp && window.wp.data
);
