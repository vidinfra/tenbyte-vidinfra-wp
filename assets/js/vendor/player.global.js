"use strict";
var Vidinfra = (() => {
  var __defProp = Object.defineProperty;
  var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
  var __getOwnPropNames = Object.getOwnPropertyNames;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
  var __export = (target, all) => {
    for (var name in all)
      __defProp(target, name, { get: all[name], enumerable: true });
  };
  var __copyProps = (to, from, except, desc) => {
    if (from && typeof from === "object" || typeof from === "function") {
      for (let key of __getOwnPropNames(from))
        if (!__hasOwnProp.call(to, key) && key !== except)
          __defProp(to, key, { get: () => from[key], enumerable: !(desc = __getOwnPropDesc(from, key)) || desc.enumerable });
    }
    return to;
  };
  var __toCommonJS = (mod) => __copyProps(__defProp({}, "__esModule", { value: true }), mod);

  // src/player.ts
  var player_exports = {};
  __export(player_exports, {
    Player: () => Player
  });
  var Player = class {
    constructor(elementOrOptions, options) {
      this.wrapper = null;
      this.embedOptions = null;
      this.listeners = /* @__PURE__ */ new Map();
      this.pendingRequests = /* @__PURE__ */ new Map();
      this.requestCounter = 0;
      this.ready = false;
      this.destroyed = false;
      this.allowedOrigin = null;
      if (options && (options.id || options.url || options.libraryId)) {
        this.embedOptions = options;
        const container = this.resolveContainer(elementOrOptions);
        const result = this.createEmbed(container, options);
        this.iframe = result.iframe;
        this.wrapper = result.wrapper;
      } else if (elementOrOptions instanceof HTMLIFrameElement) {
        this.iframe = elementOrOptions;
      } else if (typeof elementOrOptions === "string") {
        const element = document.querySelector(elementOrOptions);
        if (!element) {
          throw new Error(
            `Player: No element found matching selector "${elementOrOptions}"`
          );
        }
        if (element instanceof HTMLIFrameElement) {
          this.iframe = element;
        } else {
          throw new Error(
            `Player: Element "${elementOrOptions}" is not an iframe. To create an embed, provide options as the second argument.`
          );
        }
      } else {
        throw new Error(
          "Player: First argument must be an iframe element, selector, or container element (with options)"
        );
      }
      try {
        const url = new URL(this.iframe.src);
        this.allowedOrigin = url.origin;
      } catch (e) {
        console.warn(
          "Could not determine iframe origin, accepting messages from any origin"
        );
      }
      this.messageHandler = this.handleMessage.bind(this);
      window.addEventListener("message", this.messageHandler);
      let readyListener = null;
      this.readyPromise = new Promise((resolve) => {
        readyListener = (event) => {
          if (this.isValidMessage(event)) {
            const message = event.data;
            if (message.type === "event" && message.event === "ready") {
              if (!this.ready) {
                this.ready = true;
                console.log("Player: Received ready signal from iframe");
                if (readyListener) {
                  window.removeEventListener("message", readyListener);
                }
                resolve();
              }
            }
          }
        };
        window.addEventListener("message", readyListener);
      });
    }
    resolveContainer(elementOrOptions) {
      if (typeof elementOrOptions === "string") {
        const element = document.getElementById(elementOrOptions) || document.querySelector(elementOrOptions);
        if (!element || element instanceof HTMLIFrameElement) {
          throw new Error(
            `Player: Container "${elementOrOptions}" not found or is an iframe`
          );
        }
        return element;
      } else if (elementOrOptions instanceof HTMLElement) {
        return elementOrOptions;
      }
      throw new Error("Player: Invalid container");
    }
    createEmbed(container, options) {
      const paddingPercent = this.parseAspectRatio(options.aspectRatio);
      const wrapper = document.createElement("div");
      if (options.className) wrapper.className = options.className;
      wrapper.style.position = "relative";
      wrapper.style.paddingBottom = `${paddingPercent}%`;
      wrapper.style.height = "0";
      wrapper.style.overflow = "hidden";
      const iframe = document.createElement("iframe");
      iframe.src = this.buildEmbedSrc(options);
      iframe.loading = options.loading || "lazy";
      iframe.allow = options.allow || "accelerometer; gyroscope; autoplay; encrypted-media; picture-in-picture;";
      iframe.setAttribute("allowfullscreen", "true");
      if (options.width || options.height) {
        wrapper.style.paddingBottom = "0";
        wrapper.style.height = "auto";
        if (options.width) {
          iframe.style.width = typeof options.width === "number" ? `${options.width}px` : options.width;
        }
        if (options.height) {
          iframe.style.height = typeof options.height === "number" ? `${options.height}px` : options.height;
        }
      } else {
        Object.assign(iframe.style, {
          border: "none",
          position: "absolute",
          top: "0",
          height: "100%",
          width: "100%"
        });
      }
      wrapper.appendChild(iframe);
      container.appendChild(wrapper);
      return { iframe, wrapper };
    }
    parseAspectRatio(ratio) {
      if (!ratio) return 56.25;
      const parts = ratio.split(":").map(Number);
      if (parts.length === 2 && parts.every((v) => Number.isFinite(v) && v > 0)) {
        return parts[1] / parts[0] * 100;
      }
      return 56.25;
    }
    buildEmbedSrc(options) {
      if (options.id || options.url) {
        const baseUrl = options.baseUrl || "https://player.vidinfra.com";
        const videoRef = options.url || options.id;
        const params = new URLSearchParams();
        if (options.autoplay) params.set("autoplay", "true");
        if (options.loop) params.set("loop", "true");
        if (options.muted) params.set("muted", "true");
        if (options.controls !== void 0)
          params.set("controls", String(options.controls));
        if (options.preload !== void 0)
          params.set("preload", String(options.preload));
        return `${baseUrl.replace(/\/$/, "")}/${videoRef}?${params.toString()}`;
      }
      if (options.libraryId && options.videoId) {
        const baseUrl = options.baseUrl || "https://player.vidinfra.com";
        const pathPlayerSegment = options.playerId ? String(options.playerId) : "default";
        const params = new URLSearchParams({
          autoplay: String(options.autoplay || false),
          loop: String(options.loop || false),
          muted: String(options.muted || false),
          controls: String(options.controls !== false),
          preload: String(options.preload !== false)
        });
        return `${baseUrl.replace(/\/$/, "")}/${options.libraryId}/${pathPlayerSegment}/${options.videoId}?${params.toString()}`;
      }
      throw new Error(
        "Player: Must provide either 'id'/'url' or 'libraryId'/'videoId' in options"
      );
    }
    /**
     * Validate that a message came from our iframe
     */
    isValidMessage(event) {
      if (this.iframe.contentWindow !== event.source) {
        return false;
      }
      if (this.allowedOrigin && event.origin !== this.allowedOrigin) {
        console.warn(
          `Rejected message from unauthorized origin: ${event.origin}`
        );
        return false;
      }
      return true;
    }
    /**
     * Handle incoming messages from iframe
     */
    handleMessage(event) {
      if (!this.isValidMessage(event)) {
        return;
      }
      const message = event.data;
      if (message.type === "event") {
        this.emit(message.event, message.data);
      } else if (message.type === "response" && message.id) {
        const pending = this.pendingRequests.get(message.id);
        if (pending) {
          clearTimeout(pending.timeoutId);
          this.pendingRequests.delete(message.id);
          if (message.error) {
            pending.reject(new Error(message.error));
          } else {
            pending.resolve(message.data);
          }
        }
      }
    }
    /**
     * Send a command to the iframe and optionally wait for response
     */
    sendCommand(action, data, waitForResponse = false) {
      if (this.destroyed) {
        return Promise.reject(new Error("Player has been destroyed"));
      }
      const id = waitForResponse ? `req_${++this.requestCounter}` : void 0;
      const message = {
        type: "command",
        id,
        action,
        data
      };
      return this.readyPromise.then(() => {
        if (!this.iframe.contentWindow) {
          throw new Error("Iframe contentWindow is not available");
        }
        const targetOrigin = this.allowedOrigin || "*";
        this.iframe.contentWindow.postMessage(message, targetOrigin);
        if (waitForResponse && id) {
          return new Promise((resolve, reject) => {
            const timeoutId = window.setTimeout(() => {
              this.pendingRequests.delete(id);
              reject(new Error(`Command '${action}' timed out after 5000ms`));
            }, 5e3);
            this.pendingRequests.set(id, { resolve, reject, timeoutId });
          });
        }
        return Promise.resolve(void 0);
      });
    }
    /**
     * Emit an event to all registered listeners
     */
    emit(event, data) {
      const callbacks = this.listeners.get(event);
      if (callbacks) {
        callbacks.forEach((callback) => {
          try {
            callback(data);
          } catch (e) {
            console.error(`Error in event listener for '${event}':`, e);
          }
        });
      }
    }
    // Public API
    /**
     * Register an event listener
     */
    on(event, callback) {
      if (!this.listeners.has(event)) {
        this.listeners.set(event, /* @__PURE__ */ new Set());
      }
      this.listeners.get(event).add(callback);
      return this;
    }
    /**
     * Unregister an event listener
     */
    off(event, callback) {
      if (!callback) {
        this.listeners.delete(event);
      } else {
        const callbacks = this.listeners.get(event);
        if (callbacks) {
          callbacks.delete(callback);
          if (callbacks.size === 0) {
            this.listeners.delete(event);
          }
        }
      }
      return this;
    }
    /**
     * Register a one-time event listener
     */
    once(event, callback) {
      const onceWrapper = (...args) => {
        this.off(event, onceWrapper);
        callback(...args);
      };
      return this.on(event, onceWrapper);
    }
    // Player control methods
    /**
     * Start playback
     */
    play() {
      return this.sendCommand("play");
    }
    /**
     * Pause playback
     */
    pause() {
      return this.sendCommand("pause");
    }
    /**
     * Toggle play/pause
     */
    togglePlay() {
      return this.sendCommand("togglePlay");
    }
    /**
     * Seek to a specific time (in seconds)
     */
    seek(time) {
      return this.sendCommand("seek", { time });
    }
    /**
     * Set volume (0-1)
     */
    setVolume(volume) {
      return this.sendCommand("setVolume", { volume });
    }
    /**
     * Get current volume
     */
    getVolume() {
      return this.sendCommand("getVolume", void 0, true);
    }
    /**
     * Mute audio
     */
    mute() {
      return this.sendCommand("mute");
    }
    /**
     * Unmute audio
     */
    unmute() {
      return this.sendCommand("unmute");
    }
    /**
     * Set muted state
     */
    setMuted(muted) {
      return this.sendCommand("setMuted", { muted });
    }
    /**
     * Get muted state
     */
    getMuted() {
      return this.sendCommand("getMuted", void 0, true);
    }
    /**
     * Get current playback time
     */
    getCurrentTime() {
      return this.sendCommand("getCurrentTime", void 0, true);
    }
    /**
     * Get video duration
     */
    getDuration() {
      return this.sendCommand("getDuration", void 0, true);
    }
    /**
     * Get paused state
     */
    getPaused() {
      return this.sendCommand("getPaused", void 0, true);
    }
    /**
     * Toggle fullscreen
     */
    toggleFullscreen() {
      return this.sendCommand("toggleFullscreen");
    }
    /**
     * Request fullscreen
     */
    requestFullscreen() {
      return this.sendCommand("requestFullscreen");
    }
    /**
     * Exit fullscreen
     */
    exitFullscreen() {
      return this.sendCommand("exitFullscreen");
    }
    /**
     * Set playback rate
     */
    setPlaybackRate(rate) {
      return this.sendCommand("setPlaybackRate", { rate });
    }
    /**
     * Get playback rate
     */
    getPlaybackRate() {
      return this.sendCommand("getPlaybackRate", void 0, true);
    }
    /**
     * Show controls
     */
    showControls() {
      return this.sendCommand("showControls");
    }
    /**
     * Hide controls
     */
    hideControls() {
      return this.sendCommand("hideControls");
    }
    /**
     * Set controls visibility
     */
    setControlsVisible(visible) {
      return this.sendCommand("setControlsVisible", { visible });
    }
    /**
     * Add watermark to the player
     */
    addWatermark(watermark) {
      return this.sendCommand("addWatermark", { watermark });
    }
    /**
     * Check if ready
     */
    isReady() {
      return this.ready;
    }
    /**
     * Wait for ready state
     */
    whenReady() {
      return this.readyPromise;
    }
    /**
     * Get the iframe element
     */
    getIframe() {
      return this.iframe;
    }
    /**
     * Get the wrapper element (only available when using embed mode)
     */
    getWrapper() {
      return this.wrapper;
    }
    /**
     * Get the iframe src URL
     */
    getSrc() {
      return this.iframe.src;
    }
    /**
     * Update embed options and reload (only available when using embed mode)
     */
    update(partial) {
      if (!this.embedOptions) {
        console.warn(
          "Player.update() is only available when player was created with embed options"
        );
        return;
      }
      this.embedOptions = { ...this.embedOptions, ...partial };
      this.iframe.src = this.buildEmbedSrc(this.embedOptions);
    }
    /**
     * Cleanup and remove event listeners
     */
    destroy() {
      if (this.destroyed) {
        return;
      }
      this.destroyed = true;
      this.pendingRequests.forEach((pending) => {
        clearTimeout(pending.timeoutId);
        pending.reject(new Error("Player destroyed"));
      });
      this.pendingRequests.clear();
      window.removeEventListener("message", this.messageHandler);
      this.listeners.clear();
      if (this.wrapper && this.wrapper.parentElement) {
        this.wrapper.parentElement.removeChild(this.wrapper);
      }
    }
  };
  try {
    if (typeof window !== "undefined") {
      const w = window;
      w.Player = w.Player || Player;
    }
  } catch {
  }
  return __toCommonJS(player_exports);
})();
//# sourceMappingURL=player.global.js.map