var AlpineBundle = (() => {
  var __create = Object.create;
  var __defProp = Object.defineProperty;
  var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
  var __getOwnPropNames = Object.getOwnPropertyNames;
  var __getProtoOf = Object.getPrototypeOf;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
  var __markAsModule = (target) => __defProp(target, "__esModule", { value: true });
  var __commonJS = (cb, mod) => function __require() {
    return mod || (0, cb[Object.keys(cb)[0]])((mod = { exports: {} }).exports, mod), mod.exports;
  };
  var __export = (target, all) => {
    __markAsModule(target);
    for (var name in all)
      __defProp(target, name, { get: all[name], enumerable: true });
  };
  var __reExport = (target, module, desc) => {
    if (module && typeof module === "object" || typeof module === "function") {
      for (let key of __getOwnPropNames(module))
        if (!__hasOwnProp.call(target, key) && key !== "default")
          __defProp(target, key, { get: () => module[key], enumerable: !(desc = __getOwnPropDesc(module, key)) || desc.enumerable });
    }
    return target;
  };
  var __toModule = (module) => {
    return __reExport(__markAsModule(__defProp(module != null ? __create(__getProtoOf(module)) : {}, "default", module && module.__esModule && "default" in module ? { get: () => module.default, enumerable: true } : { value: module, enumerable: true })), module);
  };

  // node_modules/property-expr/index.js
  var require_property_expr = __commonJS({
    "node_modules/property-expr/index.js"(exports, module) {
      "use strict";
      function Cache(maxSize) {
        this._maxSize = maxSize;
        this.clear();
      }
      Cache.prototype.clear = function() {
        this._size = 0;
        this._values = Object.create(null);
      };
      Cache.prototype.get = function(key) {
        return this._values[key];
      };
      Cache.prototype.set = function(key, value) {
        this._size >= this._maxSize && this.clear();
        if (!(key in this._values))
          this._size++;
        return this._values[key] = value;
      };
      var SPLIT_REGEX = /[^.^\]^[]+|(?=\[\]|\.\.)/g;
      var DIGIT_REGEX = /^\d+$/;
      var LEAD_DIGIT_REGEX = /^\d/;
      var SPEC_CHAR_REGEX = /[~`!#$%\^&*+=\-\[\]\\';,/{}|\\":<>\?]/g;
      var CLEAN_QUOTES_REGEX = /^\s*(['"]?)(.*?)(\1)\s*$/;
      var MAX_CACHE_SIZE = 512;
      var pathCache = new Cache(MAX_CACHE_SIZE);
      var setCache = new Cache(MAX_CACHE_SIZE);
      var getCache = new Cache(MAX_CACHE_SIZE);
      module.exports = {
        Cache,
        split: split2,
        normalizePath: normalizePath2,
        setter: function(path) {
          var parts = normalizePath2(path);
          return setCache.get(path) || setCache.set(path, function setter(obj, value) {
            var index = 0;
            var len = parts.length;
            var data2 = obj;
            while (index < len - 1) {
              var part = parts[index];
              if (part === "__proto__" || part === "constructor" || part === "prototype") {
                return obj;
              }
              data2 = data2[parts[index++]];
            }
            data2[parts[index]] = value;
          });
        },
        getter: function(path, safe) {
          var parts = normalizePath2(path);
          return getCache.get(path) || getCache.set(path, function getter2(data2) {
            var index = 0, len = parts.length;
            while (index < len) {
              if (data2 != null || !safe)
                data2 = data2[parts[index++]];
              else
                return;
            }
            return data2;
          });
        },
        join: function(segments) {
          return segments.reduce(function(path, part) {
            return path + (isQuoted(part) || DIGIT_REGEX.test(part) ? "[" + part + "]" : (path ? "." : "") + part);
          }, "");
        },
        forEach: function(path, cb, thisArg) {
          forEach2(Array.isArray(path) ? path : split2(path), cb, thisArg);
        }
      };
      function normalizePath2(path) {
        return pathCache.get(path) || pathCache.set(path, split2(path).map(function(part) {
          return part.replace(CLEAN_QUOTES_REGEX, "$2");
        }));
      }
      function split2(path) {
        return path.match(SPLIT_REGEX) || [""];
      }
      function forEach2(parts, iter, thisArg) {
        var len = parts.length, part, idx, isArray2, isBracket;
        for (idx = 0; idx < len; idx++) {
          part = parts[idx];
          if (part) {
            if (shouldBeQuoted(part)) {
              part = '"' + part + '"';
            }
            isBracket = isQuoted(part);
            isArray2 = !isBracket && /^\d+$/.test(part);
            iter.call(thisArg, part, isBracket, isArray2, idx, parts);
          }
        }
      }
      function isQuoted(str) {
        return typeof str === "string" && str && ["'", '"'].indexOf(str.charAt(0)) !== -1;
      }
      function hasLeadingNumber(part) {
        return part.match(LEAD_DIGIT_REGEX) && !part.match(DIGIT_REGEX);
      }
      function hasSpecialChars(part) {
        return SPEC_CHAR_REGEX.test(part);
      }
      function shouldBeQuoted(part) {
        return !isQuoted(part) && (hasLeadingNumber(part) || hasSpecialChars(part));
      }
    }
  });

  // node_modules/tiny-case/index.js
  var require_tiny_case = __commonJS({
    "node_modules/tiny-case/index.js"(exports, module) {
      var reWords = /[A-Z\xc0-\xd6\xd8-\xde]?[a-z\xdf-\xf6\xf8-\xff]+(?:['’](?:d|ll|m|re|s|t|ve))?(?=[\xac\xb1\xd7\xf7\x00-\x2f\x3a-\x40\x5b-\x60\x7b-\xbf\u2000-\u206f \t\x0b\f\xa0\ufeff\n\r\u2028\u2029\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000]|[A-Z\xc0-\xd6\xd8-\xde]|$)|(?:[A-Z\xc0-\xd6\xd8-\xde]|[^\ud800-\udfff\xac\xb1\xd7\xf7\x00-\x2f\x3a-\x40\x5b-\x60\x7b-\xbf\u2000-\u206f \t\x0b\f\xa0\ufeff\n\r\u2028\u2029\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\d+\u2700-\u27bfa-z\xdf-\xf6\xf8-\xffA-Z\xc0-\xd6\xd8-\xde])+(?:['’](?:D|LL|M|RE|S|T|VE))?(?=[\xac\xb1\xd7\xf7\x00-\x2f\x3a-\x40\x5b-\x60\x7b-\xbf\u2000-\u206f \t\x0b\f\xa0\ufeff\n\r\u2028\u2029\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000]|[A-Z\xc0-\xd6\xd8-\xde](?:[a-z\xdf-\xf6\xf8-\xff]|[^\ud800-\udfff\xac\xb1\xd7\xf7\x00-\x2f\x3a-\x40\x5b-\x60\x7b-\xbf\u2000-\u206f \t\x0b\f\xa0\ufeff\n\r\u2028\u2029\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\d+\u2700-\u27bfa-z\xdf-\xf6\xf8-\xffA-Z\xc0-\xd6\xd8-\xde])|$)|[A-Z\xc0-\xd6\xd8-\xde]?(?:[a-z\xdf-\xf6\xf8-\xff]|[^\ud800-\udfff\xac\xb1\xd7\xf7\x00-\x2f\x3a-\x40\x5b-\x60\x7b-\xbf\u2000-\u206f \t\x0b\f\xa0\ufeff\n\r\u2028\u2029\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\d+\u2700-\u27bfa-z\xdf-\xf6\xf8-\xffA-Z\xc0-\xd6\xd8-\xde])+(?:['’](?:d|ll|m|re|s|t|ve))?|[A-Z\xc0-\xd6\xd8-\xde]+(?:['’](?:D|LL|M|RE|S|T|VE))?|\d*(?:1ST|2ND|3RD|(?![123])\dTH)(?=\b|[a-z_])|\d*(?:1st|2nd|3rd|(?![123])\dth)(?=\b|[A-Z_])|\d+|(?:[\u2700-\u27bf]|(?:\ud83c[\udde6-\uddff]){2}|[\ud800-\udbff][\udc00-\udfff])[\ufe0e\ufe0f]?(?:[\u0300-\u036f\ufe20-\ufe2f\u20d0-\u20ff]|\ud83c[\udffb-\udfff])?(?:\u200d(?:[^\ud800-\udfff]|(?:\ud83c[\udde6-\uddff]){2}|[\ud800-\udbff][\udc00-\udfff])[\ufe0e\ufe0f]?(?:[\u0300-\u036f\ufe20-\ufe2f\u20d0-\u20ff]|\ud83c[\udffb-\udfff])?)*/g;
      var words = (str) => str.match(reWords) || [];
      var upperFirst = (str) => str[0].toUpperCase() + str.slice(1);
      var join2 = (str, d) => words(str).join(d).toLowerCase();
      var camelCase4 = (str) => words(str).reduce((acc, next) => `${acc}${!acc ? next.toLowerCase() : next[0].toUpperCase() + next.slice(1).toLowerCase()}`, "");
      var pascalCase = (str) => upperFirst(camelCase4(str));
      var snakeCase2 = (str) => join2(str, "_");
      var kebabCase3 = (str) => join2(str, "-");
      var sentenceCase = (str) => upperFirst(join2(str, " "));
      var titleCase = (str) => words(str).map(upperFirst).join(" ");
      module.exports = {
        words,
        upperFirst,
        camelCase: camelCase4,
        pascalCase,
        snakeCase: snakeCase2,
        kebabCase: kebabCase3,
        sentenceCase,
        titleCase
      };
    }
  });

  // node_modules/toposort/index.js
  var require_toposort = __commonJS({
    "node_modules/toposort/index.js"(exports, module) {
      module.exports = function(edges) {
        return toposort2(uniqueNodes(edges), edges);
      };
      module.exports.array = toposort2;
      function toposort2(nodes, edges) {
        var cursor = nodes.length, sorted = new Array(cursor), visited = {}, i = cursor, outgoingEdges = makeOutgoingEdges(edges), nodesHash = makeNodesHash(nodes);
        edges.forEach(function(edge) {
          if (!nodesHash.has(edge[0]) || !nodesHash.has(edge[1])) {
            throw new Error("Unknown node. There is an unknown node in the supplied edges.");
          }
        });
        while (i--) {
          if (!visited[i])
            visit(nodes[i], i, new Set());
        }
        return sorted;
        function visit(node, i2, predecessors) {
          if (predecessors.has(node)) {
            var nodeRep;
            try {
              nodeRep = ", node was:" + JSON.stringify(node);
            } catch (e) {
              nodeRep = "";
            }
            throw new Error("Cyclic dependency" + nodeRep);
          }
          if (!nodesHash.has(node)) {
            throw new Error("Found unknown node. Make sure to provided all involved nodes. Unknown node: " + JSON.stringify(node));
          }
          if (visited[i2])
            return;
          visited[i2] = true;
          var outgoing = outgoingEdges.get(node) || new Set();
          outgoing = Array.from(outgoing);
          if (i2 = outgoing.length) {
            predecessors.add(node);
            do {
              var child = outgoing[--i2];
              visit(child, nodesHash.get(child), predecessors);
            } while (i2);
            predecessors.delete(node);
          }
          sorted[--cursor] = node;
        }
      }
      function uniqueNodes(arr) {
        var res = new Set();
        for (var i = 0, len = arr.length; i < len; i++) {
          var edge = arr[i];
          res.add(edge[0]);
          res.add(edge[1]);
        }
        return Array.from(res);
      }
      function makeOutgoingEdges(arr) {
        var edges = new Map();
        for (var i = 0, len = arr.length; i < len; i++) {
          var edge = arr[i];
          if (!edges.has(edge[0]))
            edges.set(edge[0], new Set());
          if (!edges.has(edge[1]))
            edges.set(edge[1], new Set());
          edges.get(edge[0]).add(edge[1]);
        }
        return edges;
      }
      function makeNodesHash(arr) {
        var res = new Map();
        for (var i = 0, len = arr.length; i < len; i++) {
          res.set(arr[i], i);
        }
        return res;
      }
    }
  });

  // node_modules/alpinejs/dist/module.esm.js
  var flushPending = false;
  var flushing = false;
  var queue = [];
  var lastFlushedIndex = -1;
  var transactionActive = false;
  function scheduler(callback) {
    queueJob(callback);
  }
  function startTransaction() {
    transactionActive = true;
  }
  function commitTransaction() {
    transactionActive = false;
    queueFlush();
  }
  function queueJob(job) {
    if (!queue.includes(job))
      queue.push(job);
    queueFlush();
  }
  function dequeueJob(job) {
    let index = queue.indexOf(job);
    if (index !== -1 && index > lastFlushedIndex)
      queue.splice(index, 1);
  }
  function queueFlush() {
    if (!flushing && !flushPending) {
      if (transactionActive)
        return;
      flushPending = true;
      queueMicrotask(flushJobs);
    }
  }
  function flushJobs() {
    flushPending = false;
    flushing = true;
    for (let i = 0; i < queue.length; i++) {
      queue[i]();
      lastFlushedIndex = i;
    }
    queue.length = 0;
    lastFlushedIndex = -1;
    flushing = false;
  }
  var reactive;
  var effect;
  var release;
  var raw;
  var shouldSchedule = true;
  function disableEffectScheduling(callback) {
    shouldSchedule = false;
    callback();
    shouldSchedule = true;
  }
  function setReactivityEngine(engine) {
    reactive = engine.reactive;
    release = engine.release;
    effect = (callback) => engine.effect(callback, { scheduler: (task) => {
      if (shouldSchedule) {
        scheduler(task);
      } else {
        task();
      }
    } });
    raw = engine.raw;
  }
  function overrideEffect(override) {
    effect = override;
  }
  function elementBoundEffect(el) {
    let cleanup2 = () => {
    };
    let wrappedEffect = (callback) => {
      let effectReference = effect(callback);
      if (!el._x_effects) {
        el._x_effects = /* @__PURE__ */ new Set();
        el._x_runEffects = () => {
          el._x_effects.forEach((i) => i());
        };
      }
      el._x_effects.add(effectReference);
      cleanup2 = () => {
        if (effectReference === void 0)
          return;
        el._x_effects.delete(effectReference);
        release(effectReference);
      };
      return effectReference;
    };
    return [wrappedEffect, () => {
      cleanup2();
    }];
  }
  function watch(getter2, callback) {
    let firstTime = true;
    let oldValue;
    let effectReference = effect(() => {
      let value = getter2();
      JSON.stringify(value);
      if (!firstTime) {
        if (typeof value === "object" || value !== oldValue) {
          let previousValue = oldValue;
          queueMicrotask(() => {
            callback(value, previousValue);
          });
        }
      }
      oldValue = value;
      firstTime = false;
    });
    return () => release(effectReference);
  }
  async function transaction(callback) {
    startTransaction();
    try {
      await callback();
      await Promise.resolve();
    } finally {
      commitTransaction();
    }
  }
  var onAttributeAddeds = [];
  var onElRemoveds = [];
  var onElAddeds = [];
  function onElAdded(callback) {
    onElAddeds.push(callback);
  }
  function onElRemoved(el, callback) {
    if (typeof callback === "function") {
      if (!el._x_cleanups)
        el._x_cleanups = [];
      el._x_cleanups.push(callback);
    } else {
      callback = el;
      onElRemoveds.push(callback);
    }
  }
  function onAttributesAdded(callback) {
    onAttributeAddeds.push(callback);
  }
  function onAttributeRemoved(el, name, callback) {
    if (!el._x_attributeCleanups)
      el._x_attributeCleanups = {};
    if (!el._x_attributeCleanups[name])
      el._x_attributeCleanups[name] = [];
    el._x_attributeCleanups[name].push(callback);
  }
  function cleanupAttributes(el, names) {
    if (!el._x_attributeCleanups)
      return;
    Object.entries(el._x_attributeCleanups).forEach(([name, value]) => {
      if (names === void 0 || names.includes(name)) {
        value.forEach((i) => i());
        delete el._x_attributeCleanups[name];
      }
    });
  }
  function cleanupElement(el) {
    el._x_effects?.forEach(dequeueJob);
    while (el._x_cleanups?.length)
      el._x_cleanups.pop()();
  }
  var observer = new MutationObserver(onMutate);
  var currentlyObserving = false;
  function startObservingMutations() {
    observer.observe(document, { subtree: true, childList: true, attributes: true, attributeOldValue: true });
    currentlyObserving = true;
  }
  function stopObservingMutations() {
    flushObserver();
    observer.disconnect();
    currentlyObserving = false;
  }
  var queuedMutations = [];
  function flushObserver() {
    let records = observer.takeRecords();
    queuedMutations.push(() => records.length > 0 && onMutate(records));
    let queueLengthWhenTriggered = queuedMutations.length;
    queueMicrotask(() => {
      if (queuedMutations.length === queueLengthWhenTriggered) {
        while (queuedMutations.length > 0)
          queuedMutations.shift()();
      }
    });
  }
  function mutateDom(callback) {
    if (!currentlyObserving)
      return callback();
    stopObservingMutations();
    let result = callback();
    startObservingMutations();
    return result;
  }
  var isCollecting = false;
  var deferredMutations = [];
  function deferMutations() {
    isCollecting = true;
  }
  function flushAndStopDeferringMutations() {
    isCollecting = false;
    onMutate(deferredMutations);
    deferredMutations = [];
  }
  function onMutate(mutations) {
    if (isCollecting) {
      deferredMutations = deferredMutations.concat(mutations);
      return;
    }
    let addedNodes = [];
    let removedNodes = /* @__PURE__ */ new Set();
    let addedAttributes = /* @__PURE__ */ new Map();
    let removedAttributes = /* @__PURE__ */ new Map();
    for (let i = 0; i < mutations.length; i++) {
      if (mutations[i].target._x_ignoreMutationObserver)
        continue;
      if (mutations[i].type === "childList") {
        mutations[i].removedNodes.forEach((node) => {
          if (node.nodeType !== 1)
            return;
          if (!node._x_marker)
            return;
          removedNodes.add(node);
        });
        mutations[i].addedNodes.forEach((node) => {
          if (node.nodeType !== 1)
            return;
          if (removedNodes.has(node)) {
            removedNodes.delete(node);
            return;
          }
          if (node._x_marker)
            return;
          addedNodes.push(node);
        });
      }
      if (mutations[i].type === "attributes") {
        let el = mutations[i].target;
        let name = mutations[i].attributeName;
        let oldValue = mutations[i].oldValue;
        let add2 = () => {
          if (!addedAttributes.has(el))
            addedAttributes.set(el, []);
          addedAttributes.get(el).push({ name, value: el.getAttribute(name) });
        };
        let remove = () => {
          if (!removedAttributes.has(el))
            removedAttributes.set(el, []);
          removedAttributes.get(el).push(name);
        };
        if (el.hasAttribute(name) && oldValue === null) {
          add2();
        } else if (el.hasAttribute(name)) {
          remove();
          add2();
        } else {
          remove();
        }
      }
    }
    removedAttributes.forEach((attrs, el) => {
      cleanupAttributes(el, attrs);
    });
    addedAttributes.forEach((attrs, el) => {
      onAttributeAddeds.forEach((i) => i(el, attrs));
    });
    for (let node of removedNodes) {
      if (addedNodes.some((i) => i.contains(node)))
        continue;
      onElRemoveds.forEach((i) => i(node));
    }
    for (let node of addedNodes) {
      if (!node.isConnected)
        continue;
      onElAddeds.forEach((i) => i(node));
    }
    addedNodes = null;
    removedNodes = null;
    addedAttributes = null;
    removedAttributes = null;
  }
  function scope(node) {
    return mergeProxies(closestDataStack(node));
  }
  function addScopeToNode(node, data2, referenceNode) {
    node._x_dataStack = [data2, ...closestDataStack(referenceNode || node)];
    return () => {
      node._x_dataStack = node._x_dataStack.filter((i) => i !== data2);
    };
  }
  function closestDataStack(node) {
    if (node._x_dataStack)
      return node._x_dataStack;
    if (typeof ShadowRoot === "function" && node instanceof ShadowRoot) {
      return closestDataStack(node.host);
    }
    if (!node.parentNode) {
      return [];
    }
    return closestDataStack(node.parentNode);
  }
  function mergeProxies(objects) {
    return new Proxy({ objects }, mergeProxyTrap);
  }
  var mergeProxyTrap = {
    ownKeys({ objects }) {
      return Array.from(new Set(objects.flatMap((i) => Object.keys(i))));
    },
    has({ objects }, name) {
      if (name == Symbol.unscopables)
        return false;
      return objects.some((obj) => Object.prototype.hasOwnProperty.call(obj, name) || Reflect.has(obj, name));
    },
    get({ objects }, name, thisProxy) {
      if (name == "toJSON")
        return collapseProxies;
      return Reflect.get(objects.find((obj) => Reflect.has(obj, name)) || {}, name, thisProxy);
    },
    set({ objects }, name, value, thisProxy) {
      const target = objects.find((obj) => Object.prototype.hasOwnProperty.call(obj, name)) || objects[objects.length - 1];
      const descriptor = Object.getOwnPropertyDescriptor(target, name);
      if (descriptor?.set && descriptor?.get)
        return descriptor.set.call(thisProxy, value) || true;
      return Reflect.set(target, name, value);
    }
  };
  function collapseProxies() {
    let keys = Reflect.ownKeys(this);
    return keys.reduce((acc, key) => {
      acc[key] = Reflect.get(this, key);
      return acc;
    }, {});
  }
  function initInterceptors(data2) {
    let isObject22 = (val) => typeof val === "object" && !Array.isArray(val) && val !== null;
    let recurse = (obj, basePath = "") => {
      Object.entries(Object.getOwnPropertyDescriptors(obj)).forEach(([key, { value, enumerable }]) => {
        if (enumerable === false || value === void 0)
          return;
        if (typeof value === "object" && value !== null && value.__v_skip)
          return;
        let path = basePath === "" ? key : `${basePath}.${key}`;
        if (typeof value === "object" && value !== null && value._x_interceptor) {
          obj[key] = value.initialize(data2, path, key);
        } else {
          if (isObject22(value) && value !== obj && !(value instanceof Element)) {
            recurse(value, path);
          }
        }
      });
    };
    return recurse(data2);
  }
  function interceptor(callback, mutateObj = () => {
  }) {
    let obj = {
      initialValue: void 0,
      _x_interceptor: true,
      initialize(data2, path, key) {
        return callback(this.initialValue, () => get(data2, path), (value) => set(data2, path, value), path, key);
      }
    };
    mutateObj(obj);
    return (initialValue) => {
      if (typeof initialValue === "object" && initialValue !== null && initialValue._x_interceptor) {
        let initialize = obj.initialize.bind(obj);
        obj.initialize = (data2, path, key) => {
          let innerValue = initialValue.initialize(data2, path, key);
          obj.initialValue = innerValue;
          return initialize(data2, path, key);
        };
      } else {
        obj.initialValue = initialValue;
      }
      return obj;
    };
  }
  function get(obj, path) {
    return path.split(".").reduce((carry, segment) => carry[segment], obj);
  }
  function set(obj, path, value) {
    if (typeof path === "string")
      path = path.split(".");
    if (path.length === 1)
      obj[path[0]] = value;
    else if (path.length === 0)
      throw error;
    else {
      if (obj[path[0]])
        return set(obj[path[0]], path.slice(1), value);
      else {
        obj[path[0]] = {};
        return set(obj[path[0]], path.slice(1), value);
      }
    }
  }
  var magics = {};
  function magic(name, callback) {
    magics[name] = callback;
  }
  function injectMagics(obj, el) {
    let memoizedUtilities = getUtilities(el);
    Object.entries(magics).forEach(([name, callback]) => {
      Object.defineProperty(obj, `$${name}`, {
        get() {
          return callback(el, memoizedUtilities);
        },
        enumerable: false
      });
    });
    return obj;
  }
  function getUtilities(el) {
    let [utilities, cleanup2] = getElementBoundUtilities(el);
    let utils = { interceptor, ...utilities };
    onElRemoved(el, cleanup2);
    return utils;
  }
  function tryCatch(el, expression, callback, ...args) {
    try {
      return callback(...args);
    } catch (e) {
      handleError(e, el, expression);
    }
  }
  function handleError(...args) {
    return errorHandler(...args);
  }
  var errorHandler = normalErrorHandler;
  function setErrorHandler(handler4) {
    errorHandler = handler4;
  }
  function normalErrorHandler(error2, el, expression = void 0) {
    error2 = Object.assign(error2 ?? { message: "No error message given." }, { el, expression });
    console.warn(`Alpine Expression Error: ${error2.message}

${expression ? 'Expression: "' + expression + '"\n\n' : ""}`, el);
    setTimeout(() => {
      throw error2;
    }, 0);
  }
  var shouldAutoEvaluateFunctions = true;
  function dontAutoEvaluateFunctions(callback) {
    let cache = shouldAutoEvaluateFunctions;
    shouldAutoEvaluateFunctions = false;
    let result = callback();
    shouldAutoEvaluateFunctions = cache;
    return result;
  }
  function evaluate(el, expression, extras = {}) {
    let result;
    evaluateLater(el, expression)((value) => result = value, extras);
    return result;
  }
  function evaluateLater(...args) {
    return theEvaluatorFunction(...args);
  }
  var theEvaluatorFunction = normalEvaluator;
  function setEvaluator(newEvaluator) {
    theEvaluatorFunction = newEvaluator;
  }
  var theRawEvaluatorFunction;
  function setRawEvaluator(newEvaluator) {
    theRawEvaluatorFunction = newEvaluator;
  }
  function normalEvaluator(el, expression) {
    let overriddenMagics = {};
    injectMagics(overriddenMagics, el);
    let dataStack = [overriddenMagics, ...closestDataStack(el)];
    let evaluator = typeof expression === "function" ? generateEvaluatorFromFunction(dataStack, expression) : generateEvaluatorFromString(dataStack, expression, el);
    return tryCatch.bind(null, el, expression, evaluator);
  }
  function generateEvaluatorFromFunction(dataStack, func) {
    return (receiver = () => {
    }, { scope: scope2 = {}, params = [], context } = {}) => {
      if (!shouldAutoEvaluateFunctions) {
        runIfTypeOfFunction(receiver, func, mergeProxies([scope2, ...dataStack]), params);
        return;
      }
      let result = func.apply(mergeProxies([scope2, ...dataStack]), params);
      runIfTypeOfFunction(receiver, result);
    };
  }
  var evaluatorMemo = {};
  function generateFunctionFromString(expression, el) {
    if (evaluatorMemo[expression]) {
      return evaluatorMemo[expression];
    }
    let AsyncFunction = Object.getPrototypeOf(async function() {
    }).constructor;
    let rightSideSafeExpression = /^[\n\s]*if.*\(.*\)/.test(expression.trim()) || /^(let|const)\s/.test(expression.trim()) ? `(async()=>{ ${expression} })()` : expression;
    const safeAsyncFunction = () => {
      try {
        let func2 = new AsyncFunction(["__self", "scope"], `with (scope) { __self.result = ${rightSideSafeExpression} }; __self.finished = true; return __self.result;`);
        Object.defineProperty(func2, "name", {
          value: `[Alpine] ${expression}`
        });
        return func2;
      } catch (error2) {
        handleError(error2, el, expression);
        return Promise.resolve();
      }
    };
    let func = safeAsyncFunction();
    evaluatorMemo[expression] = func;
    return func;
  }
  function generateEvaluatorFromString(dataStack, expression, el) {
    let func = generateFunctionFromString(expression, el);
    return (receiver = () => {
    }, { scope: scope2 = {}, params = [], context } = {}) => {
      func.result = void 0;
      func.finished = false;
      let completeScope = mergeProxies([scope2, ...dataStack]);
      if (typeof func === "function") {
        let promise = func.call(context, func, completeScope).catch((error2) => handleError(error2, el, expression));
        if (func.finished) {
          runIfTypeOfFunction(receiver, func.result, completeScope, params, el);
          func.result = void 0;
        } else {
          promise.then((result) => {
            runIfTypeOfFunction(receiver, result, completeScope, params, el);
          }).catch((error2) => handleError(error2, el, expression)).finally(() => func.result = void 0);
        }
      }
    };
  }
  function runIfTypeOfFunction(receiver, value, scope2, params, el) {
    if (shouldAutoEvaluateFunctions && typeof value === "function") {
      let result = value.apply(scope2, params);
      if (result instanceof Promise) {
        result.then((i) => runIfTypeOfFunction(receiver, i, scope2, params)).catch((error2) => handleError(error2, el, value));
      } else {
        receiver(result);
      }
    } else if (typeof value === "object" && value instanceof Promise) {
      value.then((i) => receiver(i));
    } else {
      receiver(value);
    }
  }
  function evaluateRaw(...args) {
    return theRawEvaluatorFunction(...args);
  }
  function normalRawEvaluator(el, expression, extras = {}) {
    let overriddenMagics = {};
    injectMagics(overriddenMagics, el);
    let dataStack = [overriddenMagics, ...closestDataStack(el)];
    let scope2 = mergeProxies([extras.scope ?? {}, ...dataStack]);
    let params = extras.params ?? [];
    if (expression.includes("await")) {
      let AsyncFunction = Object.getPrototypeOf(async function() {
      }).constructor;
      let rightSideSafeExpression = /^[\n\s]*if.*\(.*\)/.test(expression.trim()) || /^(let|const)\s/.test(expression.trim()) ? `(async()=>{ ${expression} })()` : expression;
      let func = new AsyncFunction(["scope"], `with (scope) { let __result = ${rightSideSafeExpression}; return __result }`);
      let result = func.call(extras.context, scope2);
      return result;
    } else {
      let rightSideSafeExpression = /^[\n\s]*if.*\(.*\)/.test(expression.trim()) || /^(let|const)\s/.test(expression.trim()) ? `(()=>{ ${expression} })()` : expression;
      let func = new Function(["scope"], `with (scope) { let __result = ${rightSideSafeExpression}; return __result }`);
      let result = func.call(extras.context, scope2);
      if (typeof result === "function" && shouldAutoEvaluateFunctions) {
        return result.apply(scope2, params);
      }
      return result;
    }
  }
  var prefixAsString = "x-";
  function prefix(subject = "") {
    return prefixAsString + subject;
  }
  function setPrefix(newPrefix) {
    prefixAsString = newPrefix;
  }
  var directiveHandlers = {};
  function directive(name, callback) {
    directiveHandlers[name] = callback;
    return {
      before(directive2) {
        if (!directiveHandlers[directive2]) {
          console.warn(String.raw`Cannot find directive \`${directive2}\`. \`${name}\` will use the default order of execution`);
          return;
        }
        const pos = directiveOrder.indexOf(directive2);
        directiveOrder.splice(pos >= 0 ? pos : directiveOrder.indexOf("DEFAULT"), 0, name);
      }
    };
  }
  function directiveExists(name) {
    return Object.keys(directiveHandlers).includes(name);
  }
  function directives(el, attributes, originalAttributeOverride) {
    attributes = Array.from(attributes);
    if (el._x_virtualDirectives) {
      let vAttributes = Object.entries(el._x_virtualDirectives).map(([name, value]) => ({ name, value }));
      let staticAttributes = attributesOnly(vAttributes);
      vAttributes = vAttributes.map((attribute) => {
        if (staticAttributes.find((attr) => attr.name === attribute.name)) {
          return {
            name: `x-bind:${attribute.name}`,
            value: `"${attribute.value}"`
          };
        }
        return attribute;
      });
      attributes = attributes.concat(vAttributes);
    }
    let transformedAttributeMap = {};
    let directives2 = attributes.map(toTransformedAttributes((newName, oldName) => transformedAttributeMap[newName] = oldName)).filter(outNonAlpineAttributes).map(toParsedDirectives(transformedAttributeMap, originalAttributeOverride)).sort(byPriority);
    return directives2.map((directive2) => {
      return getDirectiveHandler(el, directive2);
    });
  }
  function attributesOnly(attributes) {
    return Array.from(attributes).map(toTransformedAttributes()).filter((attr) => !outNonAlpineAttributes(attr));
  }
  var isDeferringHandlers = false;
  var directiveHandlerStacks = /* @__PURE__ */ new Map();
  var currentHandlerStackKey = Symbol();
  function deferHandlingDirectives(callback) {
    isDeferringHandlers = true;
    let key = Symbol();
    currentHandlerStackKey = key;
    directiveHandlerStacks.set(key, []);
    let flushHandlers = () => {
      while (directiveHandlerStacks.get(key).length)
        directiveHandlerStacks.get(key).shift()();
      directiveHandlerStacks.delete(key);
    };
    let stopDeferring = () => {
      isDeferringHandlers = false;
      flushHandlers();
    };
    callback(flushHandlers);
    stopDeferring();
  }
  function getElementBoundUtilities(el) {
    let cleanups = [];
    let cleanup2 = (callback) => cleanups.push(callback);
    let [effect3, cleanupEffect] = elementBoundEffect(el);
    cleanups.push(cleanupEffect);
    let utilities = {
      Alpine: alpine_default,
      effect: effect3,
      cleanup: cleanup2,
      evaluateLater: evaluateLater.bind(evaluateLater, el),
      evaluate: evaluate.bind(evaluate, el)
    };
    let doCleanup = () => cleanups.forEach((i) => i());
    return [utilities, doCleanup];
  }
  function getDirectiveHandler(el, directive2) {
    let noop = () => {
    };
    let handler4 = directiveHandlers[directive2.type] || noop;
    let [utilities, cleanup2] = getElementBoundUtilities(el);
    onAttributeRemoved(el, directive2.original, cleanup2);
    let fullHandler = () => {
      if (el._x_ignore || el._x_ignoreSelf)
        return;
      handler4.inline && handler4.inline(el, directive2, utilities);
      handler4 = handler4.bind(handler4, el, directive2, utilities);
      isDeferringHandlers ? directiveHandlerStacks.get(currentHandlerStackKey).push(handler4) : handler4();
    };
    fullHandler.runCleanups = cleanup2;
    return fullHandler;
  }
  var startingWith = (subject, replacement) => ({ name, value }) => {
    if (name.startsWith(subject))
      name = name.replace(subject, replacement);
    return { name, value };
  };
  var into = (i) => i;
  function toTransformedAttributes(callback = () => {
  }) {
    return ({ name, value }) => {
      let { name: newName, value: newValue } = attributeTransformers.reduce((carry, transform) => {
        return transform(carry);
      }, { name, value });
      if (newName !== name)
        callback(newName, name);
      return { name: newName, value: newValue };
    };
  }
  var attributeTransformers = [];
  function mapAttributes(callback) {
    attributeTransformers.push(callback);
  }
  function outNonAlpineAttributes({ name }) {
    return alpineAttributeRegex().test(name);
  }
  var alpineAttributeRegex = () => new RegExp(`^${prefixAsString}([^:^.]+)\\b`);
  function toParsedDirectives(transformedAttributeMap, originalAttributeOverride) {
    return ({ name, value }) => {
      if (name === value)
        value = "";
      let typeMatch = name.match(alpineAttributeRegex());
      let valueMatch = name.match(/:([a-zA-Z0-9\-_:]+)/);
      let modifiers = name.match(/\.[^.\]]+(?=[^\]]*$)/g) || [];
      let original = originalAttributeOverride || transformedAttributeMap[name] || name;
      return {
        type: typeMatch ? typeMatch[1] : null,
        value: valueMatch ? valueMatch[1] : null,
        modifiers: modifiers.map((i) => i.replace(".", "")),
        expression: value,
        original
      };
    };
  }
  var DEFAULT = "DEFAULT";
  var directiveOrder = [
    "ignore",
    "ref",
    "data",
    "id",
    "anchor",
    "bind",
    "init",
    "for",
    "model",
    "modelable",
    "transition",
    "show",
    "if",
    DEFAULT,
    "teleport"
  ];
  function byPriority(a, b) {
    let typeA = directiveOrder.indexOf(a.type) === -1 ? DEFAULT : a.type;
    let typeB = directiveOrder.indexOf(b.type) === -1 ? DEFAULT : b.type;
    return directiveOrder.indexOf(typeA) - directiveOrder.indexOf(typeB);
  }
  function dispatch(el, name, detail = {}) {
    el.dispatchEvent(new CustomEvent(name, {
      detail,
      bubbles: true,
      composed: true,
      cancelable: true
    }));
  }
  function walk(el, callback) {
    if (typeof ShadowRoot === "function" && el instanceof ShadowRoot) {
      Array.from(el.children).forEach((el2) => walk(el2, callback));
      return;
    }
    let skip = false;
    callback(el, () => skip = true);
    if (skip)
      return;
    let node = el.firstElementChild;
    while (node) {
      walk(node, callback, false);
      node = node.nextElementSibling;
    }
  }
  function warn(message, ...args) {
    console.warn(`Alpine Warning: ${message}`, ...args);
  }
  var started = false;
  function start() {
    if (started)
      warn("Alpine has already been initialized on this page. Calling Alpine.start() more than once can cause problems.");
    started = true;
    if (!document.body)
      warn("Unable to initialize. Trying to load Alpine before `<body>` is available. Did you forget to add `defer` in Alpine's `<script>` tag?");
    dispatch(document, "alpine:init");
    dispatch(document, "alpine:initializing");
    startObservingMutations();
    onElAdded((el) => initTree(el, walk));
    onElRemoved((el) => destroyTree(el));
    onAttributesAdded((el, attrs) => {
      directives(el, attrs).forEach((handle) => handle());
    });
    let outNestedComponents = (el) => !closestRoot(el.parentElement, true);
    Array.from(document.querySelectorAll(allSelectors().join(","))).filter(outNestedComponents).forEach((el) => {
      initTree(el);
    });
    dispatch(document, "alpine:initialized");
    setTimeout(() => {
      warnAboutMissingPlugins();
    });
  }
  var rootSelectorCallbacks = [];
  var initSelectorCallbacks = [];
  function rootSelectors() {
    return rootSelectorCallbacks.map((fn) => fn());
  }
  function allSelectors() {
    return rootSelectorCallbacks.concat(initSelectorCallbacks).map((fn) => fn());
  }
  function addRootSelector(selectorCallback) {
    rootSelectorCallbacks.push(selectorCallback);
  }
  function addInitSelector(selectorCallback) {
    initSelectorCallbacks.push(selectorCallback);
  }
  function closestRoot(el, includeInitSelectors = false) {
    return findClosest(el, (element) => {
      const selectors = includeInitSelectors ? allSelectors() : rootSelectors();
      if (selectors.some((selector) => element.matches(selector)))
        return true;
    });
  }
  function findClosest(el, callback) {
    if (!el)
      return;
    if (callback(el))
      return el;
    if (el._x_teleportBack)
      el = el._x_teleportBack;
    if (el.parentNode instanceof ShadowRoot) {
      return findClosest(el.parentNode.host, callback);
    }
    if (!el.parentElement)
      return;
    return findClosest(el.parentElement, callback);
  }
  function isRoot(el) {
    return rootSelectors().some((selector) => el.matches(selector));
  }
  var initInterceptors2 = [];
  function interceptInit(callback) {
    initInterceptors2.push(callback);
  }
  var markerDispenser = 1;
  function initTree(el, walker = walk, intercept = () => {
  }) {
    if (findClosest(el, (i) => i._x_ignore))
      return;
    deferHandlingDirectives(() => {
      walker(el, (el2, skip) => {
        if (el2._x_marker)
          return;
        intercept(el2, skip);
        initInterceptors2.forEach((i) => i(el2, skip));
        directives(el2, el2.attributes).forEach((handle) => handle());
        if (!el2._x_ignore)
          el2._x_marker = markerDispenser++;
        el2._x_ignore && skip();
      });
    });
  }
  function destroyTree(root, walker = walk) {
    walker(root, (el) => {
      cleanupElement(el);
      cleanupAttributes(el);
      delete el._x_marker;
    });
  }
  function warnAboutMissingPlugins() {
    let pluginDirectives = [
      ["ui", "dialog", ["[x-dialog], [x-popover]"]],
      ["anchor", "anchor", ["[x-anchor]"]],
      ["sort", "sort", ["[x-sort]"]]
    ];
    pluginDirectives.forEach(([plugin2, directive2, selectors]) => {
      if (directiveExists(directive2))
        return;
      selectors.some((selector) => {
        if (document.querySelector(selector)) {
          warn(`found "${selector}", but missing ${plugin2} plugin`);
          return true;
        }
      });
    });
  }
  var tickStack = [];
  var isHolding = false;
  function nextTick(callback = () => {
  }) {
    queueMicrotask(() => {
      isHolding || setTimeout(() => {
        releaseNextTicks();
      });
    });
    return new Promise((res) => {
      tickStack.push(() => {
        callback();
        res();
      });
    });
  }
  function releaseNextTicks() {
    isHolding = false;
    while (tickStack.length)
      tickStack.shift()();
  }
  function holdNextTicks() {
    isHolding = true;
  }
  function setClasses(el, value) {
    if (Array.isArray(value)) {
      return setClassesFromString(el, value.join(" "));
    } else if (typeof value === "object" && value !== null) {
      return setClassesFromObject(el, value);
    } else if (typeof value === "function") {
      return setClasses(el, value());
    }
    return setClassesFromString(el, value);
  }
  function setClassesFromString(el, classString) {
    let split2 = (classString2) => classString2.split(" ").filter(Boolean);
    let missingClasses = (classString2) => classString2.split(" ").filter((i) => !el.classList.contains(i)).filter(Boolean);
    let addClassesAndReturnUndo = (classes) => {
      el.classList.add(...classes);
      return () => {
        el.classList.remove(...classes);
      };
    };
    classString = classString === true ? classString = "" : classString || "";
    return addClassesAndReturnUndo(missingClasses(classString));
  }
  function setClassesFromObject(el, classObject) {
    let split2 = (classString) => classString.split(" ").filter(Boolean);
    let forAdd = Object.entries(classObject).flatMap(([classString, bool]) => bool ? split2(classString) : false).filter(Boolean);
    let forRemove = Object.entries(classObject).flatMap(([classString, bool]) => !bool ? split2(classString) : false).filter(Boolean);
    let added = [];
    let removed = [];
    forRemove.forEach((i) => {
      if (el.classList.contains(i)) {
        el.classList.remove(i);
        removed.push(i);
      }
    });
    forAdd.forEach((i) => {
      if (!el.classList.contains(i)) {
        el.classList.add(i);
        added.push(i);
      }
    });
    return () => {
      removed.forEach((i) => el.classList.add(i));
      added.forEach((i) => el.classList.remove(i));
    };
  }
  function setStyles(el, value) {
    if (typeof value === "object" && value !== null) {
      return setStylesFromObject(el, value);
    }
    return setStylesFromString(el, value);
  }
  function setStylesFromObject(el, value) {
    let previousStyles = {};
    Object.entries(value).forEach(([key, value2]) => {
      previousStyles[key] = el.style[key];
      if (!key.startsWith("--")) {
        key = kebabCase(key);
      }
      el.style.setProperty(key, value2);
    });
    setTimeout(() => {
      if (el.style.length === 0) {
        el.removeAttribute("style");
      }
    });
    return () => {
      setStyles(el, previousStyles);
    };
  }
  function setStylesFromString(el, value) {
    let cache = el.getAttribute("style", value);
    el.setAttribute("style", value);
    return () => {
      el.setAttribute("style", cache || "");
    };
  }
  function kebabCase(subject) {
    return subject.replace(/([a-z])([A-Z])/g, "$1-$2").toLowerCase();
  }
  function once(callback, fallback = () => {
  }) {
    let called = false;
    return function() {
      if (!called) {
        called = true;
        callback.apply(this, arguments);
      } else {
        fallback.apply(this, arguments);
      }
    };
  }
  directive("transition", (el, { value, modifiers, expression }, { evaluate: evaluate2 }) => {
    if (typeof expression === "function")
      expression = evaluate2(expression);
    if (expression === false)
      return;
    if (!expression || typeof expression === "boolean") {
      registerTransitionsFromHelper(el, modifiers, value);
    } else {
      registerTransitionsFromClassString(el, expression, value);
    }
  });
  function registerTransitionsFromClassString(el, classString, stage) {
    registerTransitionObject(el, setClasses, "");
    let directiveStorageMap = {
      "enter": (classes) => {
        el._x_transition.enter.during = classes;
      },
      "enter-start": (classes) => {
        el._x_transition.enter.start = classes;
      },
      "enter-end": (classes) => {
        el._x_transition.enter.end = classes;
      },
      "leave": (classes) => {
        el._x_transition.leave.during = classes;
      },
      "leave-start": (classes) => {
        el._x_transition.leave.start = classes;
      },
      "leave-end": (classes) => {
        el._x_transition.leave.end = classes;
      }
    };
    directiveStorageMap[stage](classString);
  }
  function registerTransitionsFromHelper(el, modifiers, stage) {
    registerTransitionObject(el, setStyles);
    let doesntSpecify = !modifiers.includes("in") && !modifiers.includes("out") && !stage;
    let transitioningIn = doesntSpecify || modifiers.includes("in") || ["enter"].includes(stage);
    let transitioningOut = doesntSpecify || modifiers.includes("out") || ["leave"].includes(stage);
    if (modifiers.includes("in") && !doesntSpecify) {
      modifiers = modifiers.filter((i, index) => index < modifiers.indexOf("out"));
    }
    if (modifiers.includes("out") && !doesntSpecify) {
      modifiers = modifiers.filter((i, index) => index > modifiers.indexOf("out"));
    }
    let wantsAll = !modifiers.includes("opacity") && !modifiers.includes("scale");
    let wantsOpacity = wantsAll || modifiers.includes("opacity");
    let wantsScale = wantsAll || modifiers.includes("scale");
    let opacityValue = wantsOpacity ? 0 : 1;
    let scaleValue = wantsScale ? modifierValue(modifiers, "scale", 95) / 100 : 1;
    let delay = modifierValue(modifiers, "delay", 0) / 1e3;
    let origin = modifierValue(modifiers, "origin", "center");
    let property = "opacity, transform";
    let durationIn = modifierValue(modifiers, "duration", 150) / 1e3;
    let durationOut = modifierValue(modifiers, "duration", 75) / 1e3;
    let easing = `cubic-bezier(0.4, 0.0, 0.2, 1)`;
    if (transitioningIn) {
      el._x_transition.enter.during = {
        transformOrigin: origin,
        transitionDelay: `${delay}s`,
        transitionProperty: property,
        transitionDuration: `${durationIn}s`,
        transitionTimingFunction: easing
      };
      el._x_transition.enter.start = {
        opacity: opacityValue,
        transform: `scale(${scaleValue})`
      };
      el._x_transition.enter.end = {
        opacity: 1,
        transform: `scale(1)`
      };
    }
    if (transitioningOut) {
      el._x_transition.leave.during = {
        transformOrigin: origin,
        transitionDelay: `${delay}s`,
        transitionProperty: property,
        transitionDuration: `${durationOut}s`,
        transitionTimingFunction: easing
      };
      el._x_transition.leave.start = {
        opacity: 1,
        transform: `scale(1)`
      };
      el._x_transition.leave.end = {
        opacity: opacityValue,
        transform: `scale(${scaleValue})`
      };
    }
  }
  function registerTransitionObject(el, setFunction, defaultValue = {}) {
    if (!el._x_transition)
      el._x_transition = {
        enter: { during: defaultValue, start: defaultValue, end: defaultValue },
        leave: { during: defaultValue, start: defaultValue, end: defaultValue },
        in(before = () => {
        }, after = () => {
        }) {
          transition(el, setFunction, {
            during: this.enter.during,
            start: this.enter.start,
            end: this.enter.end
          }, before, after);
        },
        out(before = () => {
        }, after = () => {
        }) {
          transition(el, setFunction, {
            during: this.leave.during,
            start: this.leave.start,
            end: this.leave.end
          }, before, after);
        }
      };
  }
  window.Element.prototype._x_toggleAndCascadeWithTransitions = function(el, value, show, hide) {
    const nextTick2 = document.visibilityState === "visible" ? requestAnimationFrame : setTimeout;
    let clickAwayCompatibleShow = () => nextTick2(show);
    if (value) {
      if (el._x_transition && (el._x_transition.enter || el._x_transition.leave)) {
        el._x_transition.enter && (Object.entries(el._x_transition.enter.during).length || Object.entries(el._x_transition.enter.start).length || Object.entries(el._x_transition.enter.end).length) ? el._x_transition.in(show) : clickAwayCompatibleShow();
      } else {
        el._x_transition ? el._x_transition.in(show) : clickAwayCompatibleShow();
      }
      return;
    }
    el._x_hidePromise = el._x_transition ? new Promise((resolve, reject) => {
      el._x_transition.out(() => {
      }, () => resolve(hide));
      el._x_transitioning && el._x_transitioning.beforeCancel(() => reject({ isFromCancelledTransition: true }));
    }) : Promise.resolve(hide);
    queueMicrotask(() => {
      let closest = closestHide(el);
      if (closest) {
        if (!closest._x_hideChildren)
          closest._x_hideChildren = [];
        closest._x_hideChildren.push(el);
      } else {
        nextTick2(() => {
          let hideAfterChildren = (el2) => {
            let carry = Promise.all([
              el2._x_hidePromise,
              ...(el2._x_hideChildren || []).map(hideAfterChildren)
            ]).then(([i]) => i?.());
            delete el2._x_hidePromise;
            delete el2._x_hideChildren;
            return carry;
          };
          hideAfterChildren(el).catch((e) => {
            if (!e.isFromCancelledTransition)
              throw e;
          });
        });
      }
    });
  };
  function closestHide(el) {
    let parent = el.parentNode;
    if (!parent)
      return;
    return parent._x_hidePromise ? parent : closestHide(parent);
  }
  function transition(el, setFunction, { during, start: start2, end } = {}, before = () => {
  }, after = () => {
  }) {
    if (el._x_transitioning)
      el._x_transitioning.cancel();
    if (Object.keys(during).length === 0 && Object.keys(start2).length === 0 && Object.keys(end).length === 0) {
      before();
      after();
      return;
    }
    let undoStart, undoDuring, undoEnd;
    performTransition(el, {
      start() {
        undoStart = setFunction(el, start2);
      },
      during() {
        undoDuring = setFunction(el, during);
      },
      before,
      end() {
        undoStart();
        undoEnd = setFunction(el, end);
      },
      after,
      cleanup() {
        undoDuring();
        undoEnd();
      }
    });
  }
  function performTransition(el, stages) {
    let interrupted, reachedBefore, reachedEnd;
    let finish = once(() => {
      mutateDom(() => {
        interrupted = true;
        if (!reachedBefore)
          stages.before();
        if (!reachedEnd) {
          stages.end();
          releaseNextTicks();
        }
        stages.after();
        if (el.isConnected)
          stages.cleanup();
        delete el._x_transitioning;
      });
    });
    el._x_transitioning = {
      beforeCancels: [],
      beforeCancel(callback) {
        this.beforeCancels.push(callback);
      },
      cancel: once(function() {
        while (this.beforeCancels.length) {
          this.beforeCancels.shift()();
        }
        ;
        finish();
      }),
      finish
    };
    mutateDom(() => {
      stages.start();
      stages.during();
    });
    holdNextTicks();
    requestAnimationFrame(() => {
      if (interrupted)
        return;
      let duration = Number(getComputedStyle(el).transitionDuration.replace(/,.*/, "").replace("s", "")) * 1e3;
      let delay = Number(getComputedStyle(el).transitionDelay.replace(/,.*/, "").replace("s", "")) * 1e3;
      if (duration === 0)
        duration = Number(getComputedStyle(el).animationDuration.replace("s", "")) * 1e3;
      mutateDom(() => {
        stages.before();
      });
      reachedBefore = true;
      requestAnimationFrame(() => {
        if (interrupted)
          return;
        mutateDom(() => {
          stages.end();
        });
        releaseNextTicks();
        setTimeout(el._x_transitioning.finish, duration + delay);
        reachedEnd = true;
      });
    });
  }
  function modifierValue(modifiers, key, fallback) {
    if (modifiers.indexOf(key) === -1)
      return fallback;
    const rawValue = modifiers[modifiers.indexOf(key) + 1];
    if (!rawValue)
      return fallback;
    if (key === "scale") {
      if (isNaN(rawValue))
        return fallback;
    }
    if (key === "duration" || key === "delay") {
      let match = rawValue.match(/([0-9]+)ms/);
      if (match)
        return match[1];
    }
    if (key === "origin") {
      if (["top", "right", "left", "center", "bottom"].includes(modifiers[modifiers.indexOf(key) + 2])) {
        return [rawValue, modifiers[modifiers.indexOf(key) + 2]].join(" ");
      }
    }
    return rawValue;
  }
  var isCloning = false;
  function skipDuringClone(callback, fallback = () => {
  }) {
    return (...args) => isCloning ? fallback(...args) : callback(...args);
  }
  function onlyDuringClone(callback) {
    return (...args) => isCloning && callback(...args);
  }
  var interceptors = [];
  function interceptClone(callback) {
    interceptors.push(callback);
  }
  function cloneNode(from, to) {
    interceptors.forEach((i) => i(from, to));
    isCloning = true;
    dontRegisterReactiveSideEffects(() => {
      initTree(to, (el, callback) => {
        callback(el, () => {
        });
      });
    });
    isCloning = false;
  }
  var isCloningLegacy = false;
  function clone(oldEl, newEl) {
    if (!newEl._x_dataStack)
      newEl._x_dataStack = oldEl._x_dataStack;
    isCloning = true;
    isCloningLegacy = true;
    dontRegisterReactiveSideEffects(() => {
      cloneTree(newEl);
    });
    isCloning = false;
    isCloningLegacy = false;
  }
  function cloneTree(el) {
    let hasRunThroughFirstEl = false;
    let shallowWalker = (el2, callback) => {
      walk(el2, (el3, skip) => {
        if (hasRunThroughFirstEl && isRoot(el3))
          return skip();
        hasRunThroughFirstEl = true;
        callback(el3, skip);
      });
    };
    initTree(el, shallowWalker);
  }
  function dontRegisterReactiveSideEffects(callback) {
    let cache = effect;
    overrideEffect((callback2, el) => {
      let storedEffect = cache(callback2);
      release(storedEffect);
      return () => {
      };
    });
    callback();
    overrideEffect(cache);
  }
  function bind(el, name, value, modifiers = []) {
    if (!el._x_bindings)
      el._x_bindings = reactive({});
    el._x_bindings[name] = value;
    name = modifiers.includes("camel") ? camelCase(name) : name;
    switch (name) {
      case "value":
        bindInputValue(el, value);
        break;
      case "style":
        bindStyles(el, value);
        break;
      case "class":
        bindClasses(el, value);
        break;
      case "selected":
      case "checked":
        bindAttributeAndProperty(el, name, value);
        break;
      default:
        bindAttribute(el, name, value);
        break;
    }
  }
  function bindInputValue(el, value) {
    if (isRadio(el)) {
      if (el.attributes.value === void 0) {
        el.value = value;
      }
      if (window.fromModel) {
        if (typeof value === "boolean") {
          el.checked = safeParseBoolean(el.value) === value;
        } else {
          el.checked = checkedAttrLooseCompare(el.value, value);
        }
      }
    } else if (isCheckbox(el)) {
      if (Number.isInteger(value)) {
        el.value = value;
      } else if (!Array.isArray(value) && typeof value !== "boolean" && ![null, void 0].includes(value)) {
        el.value = String(value);
      } else {
        if (Array.isArray(value)) {
          el.checked = value.some((val) => checkedAttrLooseCompare(val, el.value));
        } else {
          el.checked = !!value;
        }
      }
    } else if (el.tagName === "SELECT") {
      updateSelect(el, value);
    } else {
      if (el.value === value)
        return;
      el.value = value === void 0 ? "" : value;
    }
  }
  function bindClasses(el, value) {
    if (el._x_undoAddedClasses)
      el._x_undoAddedClasses();
    el._x_undoAddedClasses = setClasses(el, value);
  }
  function bindStyles(el, value) {
    if (el._x_undoAddedStyles)
      el._x_undoAddedStyles();
    el._x_undoAddedStyles = setStyles(el, value);
  }
  function bindAttributeAndProperty(el, name, value) {
    bindAttribute(el, name, value);
    setPropertyIfChanged(el, name, value);
  }
  function bindAttribute(el, name, value) {
    if ([null, void 0, false].includes(value) && attributeShouldntBePreservedIfFalsy(name)) {
      el.removeAttribute(name);
    } else {
      if (isBooleanAttr(name))
        value = name;
      setIfChanged(el, name, value);
    }
  }
  function setIfChanged(el, attrName, value) {
    if (el.getAttribute(attrName) != value) {
      el.setAttribute(attrName, value);
    }
  }
  function setPropertyIfChanged(el, propName, value) {
    if (el[propName] !== value) {
      el[propName] = value;
    }
  }
  function updateSelect(el, value) {
    const arrayWrappedValue = [].concat(value).map((value2) => {
      return value2 + "";
    });
    Array.from(el.options).forEach((option) => {
      option.selected = arrayWrappedValue.includes(option.value);
    });
  }
  function camelCase(subject) {
    return subject.toLowerCase().replace(/-(\w)/g, (match, char) => char.toUpperCase());
  }
  function checkedAttrLooseCompare(valueA, valueB) {
    return valueA == valueB;
  }
  function safeParseBoolean(rawValue) {
    if ([1, "1", "true", "on", "yes", true].includes(rawValue)) {
      return true;
    }
    if ([0, "0", "false", "off", "no", false].includes(rawValue)) {
      return false;
    }
    return rawValue ? Boolean(rawValue) : null;
  }
  var booleanAttributes = /* @__PURE__ */ new Set([
    "allowfullscreen",
    "async",
    "autofocus",
    "autoplay",
    "checked",
    "controls",
    "default",
    "defer",
    "disabled",
    "formnovalidate",
    "inert",
    "ismap",
    "itemscope",
    "loop",
    "multiple",
    "muted",
    "nomodule",
    "novalidate",
    "open",
    "playsinline",
    "readonly",
    "required",
    "reversed",
    "selected",
    "shadowrootclonable",
    "shadowrootdelegatesfocus",
    "shadowrootserializable"
  ]);
  function isBooleanAttr(attrName) {
    return booleanAttributes.has(attrName);
  }
  function attributeShouldntBePreservedIfFalsy(name) {
    return !["aria-pressed", "aria-checked", "aria-expanded", "aria-selected"].includes(name);
  }
  function getBinding(el, name, fallback) {
    if (el._x_bindings && el._x_bindings[name] !== void 0)
      return el._x_bindings[name];
    return getAttributeBinding(el, name, fallback);
  }
  function extractProp(el, name, fallback, extract = true) {
    if (el._x_bindings && el._x_bindings[name] !== void 0)
      return el._x_bindings[name];
    if (el._x_inlineBindings && el._x_inlineBindings[name] !== void 0) {
      let binding = el._x_inlineBindings[name];
      binding.extract = extract;
      return dontAutoEvaluateFunctions(() => {
        return evaluate(el, binding.expression);
      });
    }
    return getAttributeBinding(el, name, fallback);
  }
  function getAttributeBinding(el, name, fallback) {
    let attr = el.getAttribute(name);
    if (attr === null)
      return typeof fallback === "function" ? fallback() : fallback;
    if (attr === "")
      return true;
    if (isBooleanAttr(name)) {
      return !![name, "true"].includes(attr);
    }
    return attr;
  }
  function isCheckbox(el) {
    return el.type === "checkbox" || el.localName === "ui-checkbox" || el.localName === "ui-switch";
  }
  function isRadio(el) {
    return el.type === "radio" || el.localName === "ui-radio";
  }
  function debounce(func, wait) {
    let timeout;
    return function() {
      const context = this, args = arguments;
      const later = function() {
        timeout = null;
        func.apply(context, args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }
  function throttle(func, limit) {
    let inThrottle;
    return function() {
      let context = this, args = arguments;
      if (!inThrottle) {
        func.apply(context, args);
        inThrottle = true;
        setTimeout(() => inThrottle = false, limit);
      }
    };
  }
  function entangle({ get: outerGet, set: outerSet }, { get: innerGet, set: innerSet }) {
    let firstRun = true;
    let outerHash;
    let innerHash;
    let reference = effect(() => {
      let outer = outerGet();
      let inner = innerGet();
      if (firstRun) {
        innerSet(cloneIfObject(outer));
        firstRun = false;
      } else {
        let outerHashLatest = JSON.stringify(outer);
        let innerHashLatest = JSON.stringify(inner);
        if (outerHashLatest !== outerHash) {
          innerSet(cloneIfObject(outer));
        } else if (outerHashLatest !== innerHashLatest) {
          outerSet(cloneIfObject(inner));
        } else {
        }
      }
      outerHash = JSON.stringify(outerGet());
      innerHash = JSON.stringify(innerGet());
    });
    return () => {
      release(reference);
    };
  }
  function cloneIfObject(value) {
    return typeof value === "object" ? JSON.parse(JSON.stringify(value)) : value;
  }
  function plugin(callback) {
    let callbacks = Array.isArray(callback) ? callback : [callback];
    callbacks.forEach((i) => i(alpine_default));
  }
  var stores = {};
  var isReactive = false;
  function store(name, value) {
    if (!isReactive) {
      stores = reactive(stores);
      isReactive = true;
    }
    if (value === void 0) {
      return stores[name];
    }
    stores[name] = value;
    initInterceptors(stores[name]);
    if (typeof value === "object" && value !== null && value.hasOwnProperty("init") && typeof value.init === "function") {
      stores[name].init();
    }
  }
  function getStores() {
    return stores;
  }
  var binds = {};
  function bind2(name, bindings) {
    let getBindings = typeof bindings !== "function" ? () => bindings : bindings;
    if (name instanceof Element) {
      return applyBindingsObject(name, getBindings());
    } else {
      binds[name] = getBindings;
    }
    return () => {
    };
  }
  function injectBindingProviders(obj) {
    Object.entries(binds).forEach(([name, callback]) => {
      Object.defineProperty(obj, name, {
        get() {
          return (...args) => {
            return callback(...args);
          };
        }
      });
    });
    return obj;
  }
  function applyBindingsObject(el, obj, original) {
    let cleanupRunners = [];
    while (cleanupRunners.length)
      cleanupRunners.pop()();
    let attributes = Object.entries(obj).map(([name, value]) => ({ name, value }));
    let staticAttributes = attributesOnly(attributes);
    attributes = attributes.map((attribute) => {
      if (staticAttributes.find((attr) => attr.name === attribute.name)) {
        return {
          name: `x-bind:${attribute.name}`,
          value: `"${attribute.value}"`
        };
      }
      return attribute;
    });
    directives(el, attributes, original).map((handle) => {
      cleanupRunners.push(handle.runCleanups);
      handle();
    });
    return () => {
      while (cleanupRunners.length)
        cleanupRunners.pop()();
    };
  }
  var datas = {};
  function data(name, callback) {
    datas[name] = callback;
  }
  function injectDataProviders(obj, context) {
    Object.entries(datas).forEach(([name, callback]) => {
      Object.defineProperty(obj, name, {
        get() {
          return (...args) => {
            return callback.bind(context)(...args);
          };
        },
        enumerable: false
      });
    });
    return obj;
  }
  var Alpine2 = {
    get reactive() {
      return reactive;
    },
    get release() {
      return release;
    },
    get effect() {
      return effect;
    },
    get raw() {
      return raw;
    },
    get transaction() {
      return transaction;
    },
    version: "3.15.8",
    flushAndStopDeferringMutations,
    dontAutoEvaluateFunctions,
    disableEffectScheduling,
    startObservingMutations,
    stopObservingMutations,
    setReactivityEngine,
    onAttributeRemoved,
    onAttributesAdded,
    closestDataStack,
    skipDuringClone,
    onlyDuringClone,
    addRootSelector,
    addInitSelector,
    setErrorHandler,
    interceptClone,
    addScopeToNode,
    deferMutations,
    mapAttributes,
    evaluateLater,
    interceptInit,
    initInterceptors,
    injectMagics,
    setEvaluator,
    setRawEvaluator,
    mergeProxies,
    extractProp,
    findClosest,
    onElRemoved,
    closestRoot,
    destroyTree,
    interceptor,
    transition,
    setStyles,
    mutateDom,
    directive,
    entangle,
    throttle,
    debounce,
    evaluate,
    evaluateRaw,
    initTree,
    nextTick,
    prefixed: prefix,
    prefix: setPrefix,
    plugin,
    magic,
    store,
    start,
    clone,
    cloneNode,
    bound: getBinding,
    $data: scope,
    watch,
    walk,
    data,
    bind: bind2
  };
  var alpine_default = Alpine2;
  function makeMap(str, expectsLowerCase) {
    const map = /* @__PURE__ */ Object.create(null);
    const list = str.split(",");
    for (let i = 0; i < list.length; i++) {
      map[list[i]] = true;
    }
    return expectsLowerCase ? (val) => !!map[val.toLowerCase()] : (val) => !!map[val];
  }
  var specialBooleanAttrs = `itemscope,allowfullscreen,formnovalidate,ismap,nomodule,novalidate,readonly`;
  var isBooleanAttr2 = /* @__PURE__ */ makeMap(specialBooleanAttrs + `,async,autofocus,autoplay,controls,default,defer,disabled,hidden,loop,open,required,reversed,scoped,seamless,checked,muted,multiple,selected`);
  var EMPTY_OBJ = true ? Object.freeze({}) : {};
  var EMPTY_ARR = true ? Object.freeze([]) : [];
  var hasOwnProperty = Object.prototype.hasOwnProperty;
  var hasOwn = (val, key) => hasOwnProperty.call(val, key);
  var isArray = Array.isArray;
  var isMap = (val) => toTypeString(val) === "[object Map]";
  var isString = (val) => typeof val === "string";
  var isSymbol = (val) => typeof val === "symbol";
  var isObject = (val) => val !== null && typeof val === "object";
  var objectToString = Object.prototype.toString;
  var toTypeString = (value) => objectToString.call(value);
  var toRawType = (value) => {
    return toTypeString(value).slice(8, -1);
  };
  var isIntegerKey = (key) => isString(key) && key !== "NaN" && key[0] !== "-" && "" + parseInt(key, 10) === key;
  var cacheStringFunction = (fn) => {
    const cache = /* @__PURE__ */ Object.create(null);
    return (str) => {
      const hit = cache[str];
      return hit || (cache[str] = fn(str));
    };
  };
  var camelizeRE = /-(\w)/g;
  var camelize = cacheStringFunction((str) => {
    return str.replace(camelizeRE, (_, c) => c ? c.toUpperCase() : "");
  });
  var hyphenateRE = /\B([A-Z])/g;
  var hyphenate = cacheStringFunction((str) => str.replace(hyphenateRE, "-$1").toLowerCase());
  var capitalize = cacheStringFunction((str) => str.charAt(0).toUpperCase() + str.slice(1));
  var toHandlerKey = cacheStringFunction((str) => str ? `on${capitalize(str)}` : ``);
  var hasChanged = (value, oldValue) => value !== oldValue && (value === value || oldValue === oldValue);
  var targetMap = /* @__PURE__ */ new WeakMap();
  var effectStack = [];
  var activeEffect;
  var ITERATE_KEY = Symbol(true ? "iterate" : "");
  var MAP_KEY_ITERATE_KEY = Symbol(true ? "Map key iterate" : "");
  function isEffect(fn) {
    return fn && fn._isEffect === true;
  }
  function effect2(fn, options = EMPTY_OBJ) {
    if (isEffect(fn)) {
      fn = fn.raw;
    }
    const effect3 = createReactiveEffect(fn, options);
    if (!options.lazy) {
      effect3();
    }
    return effect3;
  }
  function stop(effect3) {
    if (effect3.active) {
      cleanup(effect3);
      if (effect3.options.onStop) {
        effect3.options.onStop();
      }
      effect3.active = false;
    }
  }
  var uid = 0;
  function createReactiveEffect(fn, options) {
    const effect3 = function reactiveEffect() {
      if (!effect3.active) {
        return fn();
      }
      if (!effectStack.includes(effect3)) {
        cleanup(effect3);
        try {
          enableTracking();
          effectStack.push(effect3);
          activeEffect = effect3;
          return fn();
        } finally {
          effectStack.pop();
          resetTracking();
          activeEffect = effectStack[effectStack.length - 1];
        }
      }
    };
    effect3.id = uid++;
    effect3.allowRecurse = !!options.allowRecurse;
    effect3._isEffect = true;
    effect3.active = true;
    effect3.raw = fn;
    effect3.deps = [];
    effect3.options = options;
    return effect3;
  }
  function cleanup(effect3) {
    const { deps } = effect3;
    if (deps.length) {
      for (let i = 0; i < deps.length; i++) {
        deps[i].delete(effect3);
      }
      deps.length = 0;
    }
  }
  var shouldTrack = true;
  var trackStack = [];
  function pauseTracking() {
    trackStack.push(shouldTrack);
    shouldTrack = false;
  }
  function enableTracking() {
    trackStack.push(shouldTrack);
    shouldTrack = true;
  }
  function resetTracking() {
    const last = trackStack.pop();
    shouldTrack = last === void 0 ? true : last;
  }
  function track(target, type, key) {
    if (!shouldTrack || activeEffect === void 0) {
      return;
    }
    let depsMap = targetMap.get(target);
    if (!depsMap) {
      targetMap.set(target, depsMap = /* @__PURE__ */ new Map());
    }
    let dep = depsMap.get(key);
    if (!dep) {
      depsMap.set(key, dep = /* @__PURE__ */ new Set());
    }
    if (!dep.has(activeEffect)) {
      dep.add(activeEffect);
      activeEffect.deps.push(dep);
      if (activeEffect.options.onTrack) {
        activeEffect.options.onTrack({
          effect: activeEffect,
          target,
          type,
          key
        });
      }
    }
  }
  function trigger(target, type, key, newValue, oldValue, oldTarget) {
    const depsMap = targetMap.get(target);
    if (!depsMap) {
      return;
    }
    const effects = /* @__PURE__ */ new Set();
    const add2 = (effectsToAdd) => {
      if (effectsToAdd) {
        effectsToAdd.forEach((effect3) => {
          if (effect3 !== activeEffect || effect3.allowRecurse) {
            effects.add(effect3);
          }
        });
      }
    };
    if (type === "clear") {
      depsMap.forEach(add2);
    } else if (key === "length" && isArray(target)) {
      depsMap.forEach((dep, key2) => {
        if (key2 === "length" || key2 >= newValue) {
          add2(dep);
        }
      });
    } else {
      if (key !== void 0) {
        add2(depsMap.get(key));
      }
      switch (type) {
        case "add":
          if (!isArray(target)) {
            add2(depsMap.get(ITERATE_KEY));
            if (isMap(target)) {
              add2(depsMap.get(MAP_KEY_ITERATE_KEY));
            }
          } else if (isIntegerKey(key)) {
            add2(depsMap.get("length"));
          }
          break;
        case "delete":
          if (!isArray(target)) {
            add2(depsMap.get(ITERATE_KEY));
            if (isMap(target)) {
              add2(depsMap.get(MAP_KEY_ITERATE_KEY));
            }
          }
          break;
        case "set":
          if (isMap(target)) {
            add2(depsMap.get(ITERATE_KEY));
          }
          break;
      }
    }
    const run = (effect3) => {
      if (effect3.options.onTrigger) {
        effect3.options.onTrigger({
          effect: effect3,
          target,
          key,
          type,
          newValue,
          oldValue,
          oldTarget
        });
      }
      if (effect3.options.scheduler) {
        effect3.options.scheduler(effect3);
      } else {
        effect3();
      }
    };
    effects.forEach(run);
  }
  var isNonTrackableKeys = /* @__PURE__ */ makeMap(`__proto__,__v_isRef,__isVue`);
  var builtInSymbols = new Set(Object.getOwnPropertyNames(Symbol).map((key) => Symbol[key]).filter(isSymbol));
  var get2 = /* @__PURE__ */ createGetter();
  var readonlyGet = /* @__PURE__ */ createGetter(true);
  var arrayInstrumentations = /* @__PURE__ */ createArrayInstrumentations();
  function createArrayInstrumentations() {
    const instrumentations = {};
    ["includes", "indexOf", "lastIndexOf"].forEach((key) => {
      instrumentations[key] = function(...args) {
        const arr = toRaw(this);
        for (let i = 0, l = this.length; i < l; i++) {
          track(arr, "get", i + "");
        }
        const res = arr[key](...args);
        if (res === -1 || res === false) {
          return arr[key](...args.map(toRaw));
        } else {
          return res;
        }
      };
    });
    ["push", "pop", "shift", "unshift", "splice"].forEach((key) => {
      instrumentations[key] = function(...args) {
        pauseTracking();
        const res = toRaw(this)[key].apply(this, args);
        resetTracking();
        return res;
      };
    });
    return instrumentations;
  }
  function createGetter(isReadonly = false, shallow = false) {
    return function get3(target, key, receiver) {
      if (key === "__v_isReactive") {
        return !isReadonly;
      } else if (key === "__v_isReadonly") {
        return isReadonly;
      } else if (key === "__v_raw" && receiver === (isReadonly ? shallow ? shallowReadonlyMap : readonlyMap : shallow ? shallowReactiveMap : reactiveMap).get(target)) {
        return target;
      }
      const targetIsArray = isArray(target);
      if (!isReadonly && targetIsArray && hasOwn(arrayInstrumentations, key)) {
        return Reflect.get(arrayInstrumentations, key, receiver);
      }
      const res = Reflect.get(target, key, receiver);
      if (isSymbol(key) ? builtInSymbols.has(key) : isNonTrackableKeys(key)) {
        return res;
      }
      if (!isReadonly) {
        track(target, "get", key);
      }
      if (shallow) {
        return res;
      }
      if (isRef(res)) {
        const shouldUnwrap = !targetIsArray || !isIntegerKey(key);
        return shouldUnwrap ? res.value : res;
      }
      if (isObject(res)) {
        return isReadonly ? readonly(res) : reactive2(res);
      }
      return res;
    };
  }
  var set2 = /* @__PURE__ */ createSetter();
  function createSetter(shallow = false) {
    return function set3(target, key, value, receiver) {
      let oldValue = target[key];
      if (!shallow) {
        value = toRaw(value);
        oldValue = toRaw(oldValue);
        if (!isArray(target) && isRef(oldValue) && !isRef(value)) {
          oldValue.value = value;
          return true;
        }
      }
      const hadKey = isArray(target) && isIntegerKey(key) ? Number(key) < target.length : hasOwn(target, key);
      const result = Reflect.set(target, key, value, receiver);
      if (target === toRaw(receiver)) {
        if (!hadKey) {
          trigger(target, "add", key, value);
        } else if (hasChanged(value, oldValue)) {
          trigger(target, "set", key, value, oldValue);
        }
      }
      return result;
    };
  }
  function deleteProperty(target, key) {
    const hadKey = hasOwn(target, key);
    const oldValue = target[key];
    const result = Reflect.deleteProperty(target, key);
    if (result && hadKey) {
      trigger(target, "delete", key, void 0, oldValue);
    }
    return result;
  }
  function has(target, key) {
    const result = Reflect.has(target, key);
    if (!isSymbol(key) || !builtInSymbols.has(key)) {
      track(target, "has", key);
    }
    return result;
  }
  function ownKeys(target) {
    track(target, "iterate", isArray(target) ? "length" : ITERATE_KEY);
    return Reflect.ownKeys(target);
  }
  var mutableHandlers = {
    get: get2,
    set: set2,
    deleteProperty,
    has,
    ownKeys
  };
  var readonlyHandlers = {
    get: readonlyGet,
    set(target, key) {
      if (true) {
        console.warn(`Set operation on key "${String(key)}" failed: target is readonly.`, target);
      }
      return true;
    },
    deleteProperty(target, key) {
      if (true) {
        console.warn(`Delete operation on key "${String(key)}" failed: target is readonly.`, target);
      }
      return true;
    }
  };
  var toReactive = (value) => isObject(value) ? reactive2(value) : value;
  var toReadonly = (value) => isObject(value) ? readonly(value) : value;
  var toShallow = (value) => value;
  var getProto = (v) => Reflect.getPrototypeOf(v);
  function get$1(target, key, isReadonly = false, isShallow = false) {
    target = target["__v_raw"];
    const rawTarget = toRaw(target);
    const rawKey = toRaw(key);
    if (key !== rawKey) {
      !isReadonly && track(rawTarget, "get", key);
    }
    !isReadonly && track(rawTarget, "get", rawKey);
    const { has: has2 } = getProto(rawTarget);
    const wrap = isShallow ? toShallow : isReadonly ? toReadonly : toReactive;
    if (has2.call(rawTarget, key)) {
      return wrap(target.get(key));
    } else if (has2.call(rawTarget, rawKey)) {
      return wrap(target.get(rawKey));
    } else if (target !== rawTarget) {
      target.get(key);
    }
  }
  function has$1(key, isReadonly = false) {
    const target = this["__v_raw"];
    const rawTarget = toRaw(target);
    const rawKey = toRaw(key);
    if (key !== rawKey) {
      !isReadonly && track(rawTarget, "has", key);
    }
    !isReadonly && track(rawTarget, "has", rawKey);
    return key === rawKey ? target.has(key) : target.has(key) || target.has(rawKey);
  }
  function size(target, isReadonly = false) {
    target = target["__v_raw"];
    !isReadonly && track(toRaw(target), "iterate", ITERATE_KEY);
    return Reflect.get(target, "size", target);
  }
  function add(value) {
    value = toRaw(value);
    const target = toRaw(this);
    const proto = getProto(target);
    const hadKey = proto.has.call(target, value);
    if (!hadKey) {
      target.add(value);
      trigger(target, "add", value, value);
    }
    return this;
  }
  function set$1(key, value) {
    value = toRaw(value);
    const target = toRaw(this);
    const { has: has2, get: get3 } = getProto(target);
    let hadKey = has2.call(target, key);
    if (!hadKey) {
      key = toRaw(key);
      hadKey = has2.call(target, key);
    } else if (true) {
      checkIdentityKeys(target, has2, key);
    }
    const oldValue = get3.call(target, key);
    target.set(key, value);
    if (!hadKey) {
      trigger(target, "add", key, value);
    } else if (hasChanged(value, oldValue)) {
      trigger(target, "set", key, value, oldValue);
    }
    return this;
  }
  function deleteEntry(key) {
    const target = toRaw(this);
    const { has: has2, get: get3 } = getProto(target);
    let hadKey = has2.call(target, key);
    if (!hadKey) {
      key = toRaw(key);
      hadKey = has2.call(target, key);
    } else if (true) {
      checkIdentityKeys(target, has2, key);
    }
    const oldValue = get3 ? get3.call(target, key) : void 0;
    const result = target.delete(key);
    if (hadKey) {
      trigger(target, "delete", key, void 0, oldValue);
    }
    return result;
  }
  function clear() {
    const target = toRaw(this);
    const hadItems = target.size !== 0;
    const oldTarget = true ? isMap(target) ? new Map(target) : new Set(target) : void 0;
    const result = target.clear();
    if (hadItems) {
      trigger(target, "clear", void 0, void 0, oldTarget);
    }
    return result;
  }
  function createForEach(isReadonly, isShallow) {
    return function forEach2(callback, thisArg) {
      const observed = this;
      const target = observed["__v_raw"];
      const rawTarget = toRaw(target);
      const wrap = isShallow ? toShallow : isReadonly ? toReadonly : toReactive;
      !isReadonly && track(rawTarget, "iterate", ITERATE_KEY);
      return target.forEach((value, key) => {
        return callback.call(thisArg, wrap(value), wrap(key), observed);
      });
    };
  }
  function createIterableMethod(method, isReadonly, isShallow) {
    return function(...args) {
      const target = this["__v_raw"];
      const rawTarget = toRaw(target);
      const targetIsMap = isMap(rawTarget);
      const isPair = method === "entries" || method === Symbol.iterator && targetIsMap;
      const isKeyOnly = method === "keys" && targetIsMap;
      const innerIterator = target[method](...args);
      const wrap = isShallow ? toShallow : isReadonly ? toReadonly : toReactive;
      !isReadonly && track(rawTarget, "iterate", isKeyOnly ? MAP_KEY_ITERATE_KEY : ITERATE_KEY);
      return {
        next() {
          const { value, done } = innerIterator.next();
          return done ? { value, done } : {
            value: isPair ? [wrap(value[0]), wrap(value[1])] : wrap(value),
            done
          };
        },
        [Symbol.iterator]() {
          return this;
        }
      };
    };
  }
  function createReadonlyMethod(type) {
    return function(...args) {
      if (true) {
        const key = args[0] ? `on key "${args[0]}" ` : ``;
        console.warn(`${capitalize(type)} operation ${key}failed: target is readonly.`, toRaw(this));
      }
      return type === "delete" ? false : this;
    };
  }
  function createInstrumentations() {
    const mutableInstrumentations2 = {
      get(key) {
        return get$1(this, key);
      },
      get size() {
        return size(this);
      },
      has: has$1,
      add,
      set: set$1,
      delete: deleteEntry,
      clear,
      forEach: createForEach(false, false)
    };
    const shallowInstrumentations2 = {
      get(key) {
        return get$1(this, key, false, true);
      },
      get size() {
        return size(this);
      },
      has: has$1,
      add,
      set: set$1,
      delete: deleteEntry,
      clear,
      forEach: createForEach(false, true)
    };
    const readonlyInstrumentations2 = {
      get(key) {
        return get$1(this, key, true);
      },
      get size() {
        return size(this, true);
      },
      has(key) {
        return has$1.call(this, key, true);
      },
      add: createReadonlyMethod("add"),
      set: createReadonlyMethod("set"),
      delete: createReadonlyMethod("delete"),
      clear: createReadonlyMethod("clear"),
      forEach: createForEach(true, false)
    };
    const shallowReadonlyInstrumentations2 = {
      get(key) {
        return get$1(this, key, true, true);
      },
      get size() {
        return size(this, true);
      },
      has(key) {
        return has$1.call(this, key, true);
      },
      add: createReadonlyMethod("add"),
      set: createReadonlyMethod("set"),
      delete: createReadonlyMethod("delete"),
      clear: createReadonlyMethod("clear"),
      forEach: createForEach(true, true)
    };
    const iteratorMethods = ["keys", "values", "entries", Symbol.iterator];
    iteratorMethods.forEach((method) => {
      mutableInstrumentations2[method] = createIterableMethod(method, false, false);
      readonlyInstrumentations2[method] = createIterableMethod(method, true, false);
      shallowInstrumentations2[method] = createIterableMethod(method, false, true);
      shallowReadonlyInstrumentations2[method] = createIterableMethod(method, true, true);
    });
    return [
      mutableInstrumentations2,
      readonlyInstrumentations2,
      shallowInstrumentations2,
      shallowReadonlyInstrumentations2
    ];
  }
  var [mutableInstrumentations, readonlyInstrumentations, shallowInstrumentations, shallowReadonlyInstrumentations] = /* @__PURE__ */ createInstrumentations();
  function createInstrumentationGetter(isReadonly, shallow) {
    const instrumentations = shallow ? isReadonly ? shallowReadonlyInstrumentations : shallowInstrumentations : isReadonly ? readonlyInstrumentations : mutableInstrumentations;
    return (target, key, receiver) => {
      if (key === "__v_isReactive") {
        return !isReadonly;
      } else if (key === "__v_isReadonly") {
        return isReadonly;
      } else if (key === "__v_raw") {
        return target;
      }
      return Reflect.get(hasOwn(instrumentations, key) && key in target ? instrumentations : target, key, receiver);
    };
  }
  var mutableCollectionHandlers = {
    get: /* @__PURE__ */ createInstrumentationGetter(false, false)
  };
  var readonlyCollectionHandlers = {
    get: /* @__PURE__ */ createInstrumentationGetter(true, false)
  };
  function checkIdentityKeys(target, has2, key) {
    const rawKey = toRaw(key);
    if (rawKey !== key && has2.call(target, rawKey)) {
      const type = toRawType(target);
      console.warn(`Reactive ${type} contains both the raw and reactive versions of the same object${type === `Map` ? ` as keys` : ``}, which can lead to inconsistencies. Avoid differentiating between the raw and reactive versions of an object and only use the reactive version if possible.`);
    }
  }
  var reactiveMap = /* @__PURE__ */ new WeakMap();
  var shallowReactiveMap = /* @__PURE__ */ new WeakMap();
  var readonlyMap = /* @__PURE__ */ new WeakMap();
  var shallowReadonlyMap = /* @__PURE__ */ new WeakMap();
  function targetTypeMap(rawType) {
    switch (rawType) {
      case "Object":
      case "Array":
        return 1;
      case "Map":
      case "Set":
      case "WeakMap":
      case "WeakSet":
        return 2;
      default:
        return 0;
    }
  }
  function getTargetType(value) {
    return value["__v_skip"] || !Object.isExtensible(value) ? 0 : targetTypeMap(toRawType(value));
  }
  function reactive2(target) {
    if (target && target["__v_isReadonly"]) {
      return target;
    }
    return createReactiveObject(target, false, mutableHandlers, mutableCollectionHandlers, reactiveMap);
  }
  function readonly(target) {
    return createReactiveObject(target, true, readonlyHandlers, readonlyCollectionHandlers, readonlyMap);
  }
  function createReactiveObject(target, isReadonly, baseHandlers, collectionHandlers, proxyMap) {
    if (!isObject(target)) {
      if (true) {
        console.warn(`value cannot be made reactive: ${String(target)}`);
      }
      return target;
    }
    if (target["__v_raw"] && !(isReadonly && target["__v_isReactive"])) {
      return target;
    }
    const existingProxy = proxyMap.get(target);
    if (existingProxy) {
      return existingProxy;
    }
    const targetType = getTargetType(target);
    if (targetType === 0) {
      return target;
    }
    const proxy = new Proxy(target, targetType === 2 ? collectionHandlers : baseHandlers);
    proxyMap.set(target, proxy);
    return proxy;
  }
  function toRaw(observed) {
    return observed && toRaw(observed["__v_raw"]) || observed;
  }
  function isRef(r) {
    return Boolean(r && r.__v_isRef === true);
  }
  magic("nextTick", () => nextTick);
  magic("dispatch", (el) => dispatch.bind(dispatch, el));
  magic("watch", (el, { evaluateLater: evaluateLater2, cleanup: cleanup2 }) => (key, callback) => {
    let evaluate2 = evaluateLater2(key);
    let getter2 = () => {
      let value;
      evaluate2((i) => value = i);
      return value;
    };
    let unwatch = watch(getter2, callback);
    cleanup2(unwatch);
  });
  magic("store", getStores);
  magic("data", (el) => scope(el));
  magic("root", (el) => closestRoot(el));
  magic("refs", (el) => {
    if (el._x_refs_proxy)
      return el._x_refs_proxy;
    el._x_refs_proxy = mergeProxies(getArrayOfRefObject(el));
    return el._x_refs_proxy;
  });
  function getArrayOfRefObject(el) {
    let refObjects = [];
    findClosest(el, (i) => {
      if (i._x_refs)
        refObjects.push(i._x_refs);
    });
    return refObjects;
  }
  var globalIdMemo = {};
  function findAndIncrementId(name) {
    if (!globalIdMemo[name])
      globalIdMemo[name] = 0;
    return ++globalIdMemo[name];
  }
  function closestIdRoot(el, name) {
    return findClosest(el, (element) => {
      if (element._x_ids && element._x_ids[name])
        return true;
    });
  }
  function setIdRoot(el, name) {
    if (!el._x_ids)
      el._x_ids = {};
    if (!el._x_ids[name])
      el._x_ids[name] = findAndIncrementId(name);
  }
  magic("id", (el, { cleanup: cleanup2 }) => (name, key = null) => {
    let cacheKey = `${name}${key ? `-${key}` : ""}`;
    return cacheIdByNameOnElement(el, cacheKey, cleanup2, () => {
      let root = closestIdRoot(el, name);
      let id = root ? root._x_ids[name] : findAndIncrementId(name);
      return key ? `${name}-${id}-${key}` : `${name}-${id}`;
    });
  });
  interceptClone((from, to) => {
    if (from._x_id) {
      to._x_id = from._x_id;
    }
  });
  function cacheIdByNameOnElement(el, cacheKey, cleanup2, callback) {
    if (!el._x_id)
      el._x_id = {};
    if (el._x_id[cacheKey])
      return el._x_id[cacheKey];
    let output = callback();
    el._x_id[cacheKey] = output;
    cleanup2(() => {
      delete el._x_id[cacheKey];
    });
    return output;
  }
  magic("el", (el) => el);
  warnMissingPluginMagic("Focus", "focus", "focus");
  warnMissingPluginMagic("Persist", "persist", "persist");
  function warnMissingPluginMagic(name, magicName, slug) {
    magic(magicName, (el) => warn(`You can't use [$${magicName}] without first installing the "${name}" plugin here: https://alpinejs.dev/plugins/${slug}`, el));
  }
  directive("modelable", (el, { expression }, { effect: effect3, evaluateLater: evaluateLater2, cleanup: cleanup2 }) => {
    let func = evaluateLater2(expression);
    let innerGet = () => {
      let result;
      func((i) => result = i);
      return result;
    };
    let evaluateInnerSet = evaluateLater2(`${expression} = __placeholder`);
    let innerSet = (val) => evaluateInnerSet(() => {
    }, { scope: { "__placeholder": val } });
    let initialValue = innerGet();
    innerSet(initialValue);
    queueMicrotask(() => {
      if (!el._x_model)
        return;
      el._x_removeModelListeners["default"]();
      let outerGet = el._x_model.get;
      let outerSet = el._x_model.set;
      let releaseEntanglement = entangle({
        get() {
          return outerGet();
        },
        set(value) {
          outerSet(value);
        }
      }, {
        get() {
          return innerGet();
        },
        set(value) {
          innerSet(value);
        }
      });
      cleanup2(releaseEntanglement);
    });
  });
  directive("teleport", (el, { modifiers, expression }, { cleanup: cleanup2 }) => {
    if (el.tagName.toLowerCase() !== "template")
      warn("x-teleport can only be used on a <template> tag", el);
    let target = getTarget(expression);
    let clone22 = el.content.cloneNode(true).firstElementChild;
    el._x_teleport = clone22;
    clone22._x_teleportBack = el;
    el.setAttribute("data-teleport-template", true);
    clone22.setAttribute("data-teleport-target", true);
    if (el._x_forwardEvents) {
      el._x_forwardEvents.forEach((eventName) => {
        clone22.addEventListener(eventName, (e) => {
          e.stopPropagation();
          el.dispatchEvent(new e.constructor(e.type, e));
        });
      });
    }
    addScopeToNode(clone22, {}, el);
    let placeInDom = (clone3, target2, modifiers2) => {
      if (modifiers2.includes("prepend")) {
        target2.parentNode.insertBefore(clone3, target2);
      } else if (modifiers2.includes("append")) {
        target2.parentNode.insertBefore(clone3, target2.nextSibling);
      } else {
        target2.appendChild(clone3);
      }
    };
    mutateDom(() => {
      placeInDom(clone22, target, modifiers);
      skipDuringClone(() => {
        initTree(clone22);
      })();
    });
    el._x_teleportPutBack = () => {
      let target2 = getTarget(expression);
      mutateDom(() => {
        placeInDom(el._x_teleport, target2, modifiers);
      });
    };
    cleanup2(() => mutateDom(() => {
      clone22.remove();
      destroyTree(clone22);
    }));
  });
  var teleportContainerDuringClone = document.createElement("div");
  function getTarget(expression) {
    let target = skipDuringClone(() => {
      return document.querySelector(expression);
    }, () => {
      return teleportContainerDuringClone;
    })();
    if (!target)
      warn(`Cannot find x-teleport element for selector: "${expression}"`);
    return target;
  }
  var handler = () => {
  };
  handler.inline = (el, { modifiers }, { cleanup: cleanup2 }) => {
    modifiers.includes("self") ? el._x_ignoreSelf = true : el._x_ignore = true;
    cleanup2(() => {
      modifiers.includes("self") ? delete el._x_ignoreSelf : delete el._x_ignore;
    });
  };
  directive("ignore", handler);
  directive("effect", skipDuringClone((el, { expression }, { effect: effect3 }) => {
    effect3(evaluateLater(el, expression));
  }));
  function on(el, event, modifiers, callback) {
    let listenerTarget = el;
    let handler4 = (e) => callback(e);
    let options = {};
    let wrapHandler = (callback2, wrapper) => (e) => wrapper(callback2, e);
    if (modifiers.includes("dot"))
      event = dotSyntax(event);
    if (modifiers.includes("camel"))
      event = camelCase2(event);
    if (modifiers.includes("passive"))
      options.passive = true;
    if (modifiers.includes("capture"))
      options.capture = true;
    if (modifiers.includes("window"))
      listenerTarget = window;
    if (modifiers.includes("document"))
      listenerTarget = document;
    if (modifiers.includes("debounce")) {
      let nextModifier = modifiers[modifiers.indexOf("debounce") + 1] || "invalid-wait";
      let wait = isNumeric(nextModifier.split("ms")[0]) ? Number(nextModifier.split("ms")[0]) : 250;
      handler4 = debounce(handler4, wait);
    }
    if (modifiers.includes("throttle")) {
      let nextModifier = modifiers[modifiers.indexOf("throttle") + 1] || "invalid-wait";
      let wait = isNumeric(nextModifier.split("ms")[0]) ? Number(nextModifier.split("ms")[0]) : 250;
      handler4 = throttle(handler4, wait);
    }
    if (modifiers.includes("prevent"))
      handler4 = wrapHandler(handler4, (next, e) => {
        e.preventDefault();
        next(e);
      });
    if (modifiers.includes("stop"))
      handler4 = wrapHandler(handler4, (next, e) => {
        e.stopPropagation();
        next(e);
      });
    if (modifiers.includes("once")) {
      handler4 = wrapHandler(handler4, (next, e) => {
        next(e);
        listenerTarget.removeEventListener(event, handler4, options);
      });
    }
    if (modifiers.includes("away") || modifiers.includes("outside")) {
      listenerTarget = document;
      handler4 = wrapHandler(handler4, (next, e) => {
        if (el.contains(e.target))
          return;
        if (e.target.isConnected === false)
          return;
        if (el.offsetWidth < 1 && el.offsetHeight < 1)
          return;
        if (el._x_isShown === false)
          return;
        next(e);
      });
    }
    if (modifiers.includes("self"))
      handler4 = wrapHandler(handler4, (next, e) => {
        e.target === el && next(e);
      });
    if (event === "submit") {
      handler4 = wrapHandler(handler4, (next, e) => {
        if (e.target._x_pendingModelUpdates) {
          e.target._x_pendingModelUpdates.forEach((fn) => fn());
        }
        next(e);
      });
    }
    if (isKeyEvent(event) || isClickEvent(event)) {
      handler4 = wrapHandler(handler4, (next, e) => {
        if (isListeningForASpecificKeyThatHasntBeenPressed(e, modifiers)) {
          return;
        }
        next(e);
      });
    }
    listenerTarget.addEventListener(event, handler4, options);
    return () => {
      listenerTarget.removeEventListener(event, handler4, options);
    };
  }
  function dotSyntax(subject) {
    return subject.replace(/-/g, ".");
  }
  function camelCase2(subject) {
    return subject.toLowerCase().replace(/-(\w)/g, (match, char) => char.toUpperCase());
  }
  function isNumeric(subject) {
    return !Array.isArray(subject) && !isNaN(subject);
  }
  function kebabCase2(subject) {
    if ([" ", "_"].includes(subject))
      return subject;
    return subject.replace(/([a-z])([A-Z])/g, "$1-$2").replace(/[_\s]/, "-").toLowerCase();
  }
  function isKeyEvent(event) {
    return ["keydown", "keyup"].includes(event);
  }
  function isClickEvent(event) {
    return ["contextmenu", "click", "mouse"].some((i) => event.includes(i));
  }
  function isListeningForASpecificKeyThatHasntBeenPressed(e, modifiers) {
    let keyModifiers = modifiers.filter((i) => {
      return !["window", "document", "prevent", "stop", "once", "capture", "self", "away", "outside", "passive", "preserve-scroll", "blur", "change", "lazy"].includes(i);
    });
    if (keyModifiers.includes("debounce")) {
      let debounceIndex = keyModifiers.indexOf("debounce");
      keyModifiers.splice(debounceIndex, isNumeric((keyModifiers[debounceIndex + 1] || "invalid-wait").split("ms")[0]) ? 2 : 1);
    }
    if (keyModifiers.includes("throttle")) {
      let debounceIndex = keyModifiers.indexOf("throttle");
      keyModifiers.splice(debounceIndex, isNumeric((keyModifiers[debounceIndex + 1] || "invalid-wait").split("ms")[0]) ? 2 : 1);
    }
    if (keyModifiers.length === 0)
      return false;
    if (keyModifiers.length === 1 && keyToModifiers(e.key).includes(keyModifiers[0]))
      return false;
    const systemKeyModifiers = ["ctrl", "shift", "alt", "meta", "cmd", "super"];
    const selectedSystemKeyModifiers = systemKeyModifiers.filter((modifier) => keyModifiers.includes(modifier));
    keyModifiers = keyModifiers.filter((i) => !selectedSystemKeyModifiers.includes(i));
    if (selectedSystemKeyModifiers.length > 0) {
      const activelyPressedKeyModifiers = selectedSystemKeyModifiers.filter((modifier) => {
        if (modifier === "cmd" || modifier === "super")
          modifier = "meta";
        return e[`${modifier}Key`];
      });
      if (activelyPressedKeyModifiers.length === selectedSystemKeyModifiers.length) {
        if (isClickEvent(e.type))
          return false;
        if (keyToModifiers(e.key).includes(keyModifiers[0]))
          return false;
      }
    }
    return true;
  }
  function keyToModifiers(key) {
    if (!key)
      return [];
    key = kebabCase2(key);
    let modifierToKeyMap = {
      "ctrl": "control",
      "slash": "/",
      "space": " ",
      "spacebar": " ",
      "cmd": "meta",
      "esc": "escape",
      "up": "arrow-up",
      "down": "arrow-down",
      "left": "arrow-left",
      "right": "arrow-right",
      "period": ".",
      "comma": ",",
      "equal": "=",
      "minus": "-",
      "underscore": "_"
    };
    modifierToKeyMap[key] = key;
    return Object.keys(modifierToKeyMap).map((modifier) => {
      if (modifierToKeyMap[modifier] === key)
        return modifier;
    }).filter((modifier) => modifier);
  }
  directive("model", (el, { modifiers, expression }, { effect: effect3, cleanup: cleanup2 }) => {
    let scopeTarget = el;
    if (modifiers.includes("parent")) {
      scopeTarget = el.parentNode;
    }
    let evaluateGet = evaluateLater(scopeTarget, expression);
    let evaluateSet;
    if (typeof expression === "string") {
      evaluateSet = evaluateLater(scopeTarget, `${expression} = __placeholder`);
    } else if (typeof expression === "function" && typeof expression() === "string") {
      evaluateSet = evaluateLater(scopeTarget, `${expression()} = __placeholder`);
    } else {
      evaluateSet = () => {
      };
    }
    let getValue = () => {
      let result;
      evaluateGet((value) => result = value);
      return isGetterSetter(result) ? result.get() : result;
    };
    let setValue = (value) => {
      let result;
      evaluateGet((value2) => result = value2);
      if (isGetterSetter(result)) {
        result.set(value);
      } else {
        evaluateSet(() => {
        }, {
          scope: { "__placeholder": value }
        });
      }
    };
    if (typeof expression === "string" && el.type === "radio") {
      mutateDom(() => {
        if (!el.hasAttribute("name"))
          el.setAttribute("name", expression);
      });
    }
    let hasChangeModifier = modifiers.includes("change") || modifiers.includes("lazy");
    let hasBlurModifier = modifiers.includes("blur");
    let hasEnterModifier = modifiers.includes("enter");
    let hasExplicitEventModifiers = hasChangeModifier || hasBlurModifier || hasEnterModifier;
    let removeListener;
    if (isCloning) {
      removeListener = () => {
      };
    } else if (hasExplicitEventModifiers) {
      let listeners = [];
      let syncValue = (e) => setValue(getInputValue(el, modifiers, e, getValue()));
      if (hasChangeModifier) {
        listeners.push(on(el, "change", modifiers, syncValue));
      }
      if (hasBlurModifier) {
        listeners.push(on(el, "blur", modifiers, syncValue));
        if (el.form) {
          let syncCallback = () => syncValue({ target: el });
          if (!el.form._x_pendingModelUpdates)
            el.form._x_pendingModelUpdates = [];
          el.form._x_pendingModelUpdates.push(syncCallback);
          cleanup2(() => el.form._x_pendingModelUpdates.splice(el.form._x_pendingModelUpdates.indexOf(syncCallback), 1));
        }
      }
      if (hasEnterModifier) {
        listeners.push(on(el, "keydown", modifiers, (e) => {
          if (e.key === "Enter")
            syncValue(e);
        }));
      }
      removeListener = () => listeners.forEach((remove) => remove());
    } else {
      let event = el.tagName.toLowerCase() === "select" || ["checkbox", "radio"].includes(el.type) ? "change" : "input";
      removeListener = on(el, event, modifiers, (e) => {
        setValue(getInputValue(el, modifiers, e, getValue()));
      });
    }
    if (modifiers.includes("fill")) {
      if ([void 0, null, ""].includes(getValue()) || isCheckbox(el) && Array.isArray(getValue()) || el.tagName.toLowerCase() === "select" && el.multiple) {
        setValue(getInputValue(el, modifiers, { target: el }, getValue()));
      }
    }
    if (!el._x_removeModelListeners)
      el._x_removeModelListeners = {};
    el._x_removeModelListeners["default"] = removeListener;
    cleanup2(() => el._x_removeModelListeners["default"]());
    if (el.form) {
      let removeResetListener = on(el.form, "reset", [], (e) => {
        nextTick(() => el._x_model && el._x_model.set(getInputValue(el, modifiers, { target: el }, getValue())));
      });
      cleanup2(() => removeResetListener());
    }
    el._x_model = {
      get() {
        return getValue();
      },
      set(value) {
        setValue(value);
      }
    };
    el._x_forceModelUpdate = (value) => {
      if (value === void 0 && typeof expression === "string" && expression.match(/\./))
        value = "";
      window.fromModel = true;
      mutateDom(() => bind(el, "value", value));
      delete window.fromModel;
    };
    effect3(() => {
      let value = getValue();
      if (modifiers.includes("unintrusive") && document.activeElement.isSameNode(el))
        return;
      el._x_forceModelUpdate(value);
    });
  });
  function getInputValue(el, modifiers, event, currentValue) {
    return mutateDom(() => {
      if (event instanceof CustomEvent && event.detail !== void 0)
        return event.detail !== null && event.detail !== void 0 ? event.detail : event.target.value;
      else if (isCheckbox(el)) {
        if (Array.isArray(currentValue)) {
          let newValue = null;
          if (modifiers.includes("number")) {
            newValue = safeParseNumber(event.target.value);
          } else if (modifiers.includes("boolean")) {
            newValue = safeParseBoolean(event.target.value);
          } else {
            newValue = event.target.value;
          }
          return event.target.checked ? currentValue.includes(newValue) ? currentValue : currentValue.concat([newValue]) : currentValue.filter((el2) => !checkedAttrLooseCompare2(el2, newValue));
        } else {
          return event.target.checked;
        }
      } else if (el.tagName.toLowerCase() === "select" && el.multiple) {
        if (modifiers.includes("number")) {
          return Array.from(event.target.selectedOptions).map((option) => {
            let rawValue = option.value || option.text;
            return safeParseNumber(rawValue);
          });
        } else if (modifiers.includes("boolean")) {
          return Array.from(event.target.selectedOptions).map((option) => {
            let rawValue = option.value || option.text;
            return safeParseBoolean(rawValue);
          });
        }
        return Array.from(event.target.selectedOptions).map((option) => {
          return option.value || option.text;
        });
      } else {
        let newValue;
        if (isRadio(el)) {
          if (event.target.checked) {
            newValue = event.target.value;
          } else {
            newValue = currentValue;
          }
        } else {
          newValue = event.target.value;
        }
        if (modifiers.includes("number")) {
          return safeParseNumber(newValue);
        } else if (modifiers.includes("boolean")) {
          return safeParseBoolean(newValue);
        } else if (modifiers.includes("trim")) {
          return newValue.trim();
        } else {
          return newValue;
        }
      }
    });
  }
  function safeParseNumber(rawValue) {
    let number2 = rawValue ? parseFloat(rawValue) : null;
    return isNumeric2(number2) ? number2 : rawValue;
  }
  function checkedAttrLooseCompare2(valueA, valueB) {
    return valueA == valueB;
  }
  function isNumeric2(subject) {
    return !Array.isArray(subject) && !isNaN(subject);
  }
  function isGetterSetter(value) {
    return value !== null && typeof value === "object" && typeof value.get === "function" && typeof value.set === "function";
  }
  directive("cloak", (el) => queueMicrotask(() => mutateDom(() => el.removeAttribute(prefix("cloak")))));
  addInitSelector(() => `[${prefix("init")}]`);
  directive("init", skipDuringClone((el, { expression }, { evaluate: evaluate2 }) => {
    if (typeof expression === "string") {
      return !!expression.trim() && evaluate2(expression, {}, false);
    }
    return evaluate2(expression, {}, false);
  }));
  directive("text", (el, { expression }, { effect: effect3, evaluateLater: evaluateLater2 }) => {
    let evaluate2 = evaluateLater2(expression);
    effect3(() => {
      evaluate2((value) => {
        mutateDom(() => {
          el.textContent = value;
        });
      });
    });
  });
  directive("html", (el, { expression }, { effect: effect3, evaluateLater: evaluateLater2 }) => {
    let evaluate2 = evaluateLater2(expression);
    effect3(() => {
      evaluate2((value) => {
        mutateDom(() => {
          el.innerHTML = value;
          el._x_ignoreSelf = true;
          initTree(el);
          delete el._x_ignoreSelf;
        });
      });
    });
  });
  mapAttributes(startingWith(":", into(prefix("bind:"))));
  var handler2 = (el, { value, modifiers, expression, original }, { effect: effect3, cleanup: cleanup2 }) => {
    if (!value) {
      let bindingProviders = {};
      injectBindingProviders(bindingProviders);
      let getBindings = evaluateLater(el, expression);
      getBindings((bindings) => {
        applyBindingsObject(el, bindings, original);
      }, { scope: bindingProviders });
      return;
    }
    if (value === "key")
      return storeKeyForXFor(el, expression);
    if (el._x_inlineBindings && el._x_inlineBindings[value] && el._x_inlineBindings[value].extract) {
      return;
    }
    let evaluate2 = evaluateLater(el, expression);
    effect3(() => evaluate2((result) => {
      if (result === void 0 && typeof expression === "string" && expression.match(/\./)) {
        result = "";
      }
      mutateDom(() => bind(el, value, result, modifiers));
    }));
    cleanup2(() => {
      el._x_undoAddedClasses && el._x_undoAddedClasses();
      el._x_undoAddedStyles && el._x_undoAddedStyles();
    });
  };
  handler2.inline = (el, { value, modifiers, expression }) => {
    if (!value)
      return;
    if (!el._x_inlineBindings)
      el._x_inlineBindings = {};
    el._x_inlineBindings[value] = { expression, extract: false };
  };
  directive("bind", handler2);
  function storeKeyForXFor(el, expression) {
    el._x_keyExpression = expression;
  }
  addRootSelector(() => `[${prefix("data")}]`);
  directive("data", (el, { expression }, { cleanup: cleanup2 }) => {
    if (shouldSkipRegisteringDataDuringClone(el))
      return;
    expression = expression === "" ? "{}" : expression;
    let magicContext = {};
    injectMagics(magicContext, el);
    let dataProviderContext = {};
    injectDataProviders(dataProviderContext, magicContext);
    let data2 = evaluate(el, expression, { scope: dataProviderContext });
    if (data2 === void 0 || data2 === true)
      data2 = {};
    injectMagics(data2, el);
    let reactiveData = reactive(data2);
    initInterceptors(reactiveData);
    let undo = addScopeToNode(el, reactiveData);
    reactiveData["init"] && evaluate(el, reactiveData["init"]);
    cleanup2(() => {
      reactiveData["destroy"] && evaluate(el, reactiveData["destroy"]);
      undo();
    });
  });
  interceptClone((from, to) => {
    if (from._x_dataStack) {
      to._x_dataStack = from._x_dataStack;
      to.setAttribute("data-has-alpine-state", true);
    }
  });
  function shouldSkipRegisteringDataDuringClone(el) {
    if (!isCloning)
      return false;
    if (isCloningLegacy)
      return true;
    return el.hasAttribute("data-has-alpine-state");
  }
  directive("show", (el, { modifiers, expression }, { effect: effect3 }) => {
    let evaluate2 = evaluateLater(el, expression);
    if (!el._x_doHide)
      el._x_doHide = () => {
        mutateDom(() => {
          el.style.setProperty("display", "none", modifiers.includes("important") ? "important" : void 0);
        });
      };
    if (!el._x_doShow)
      el._x_doShow = () => {
        mutateDom(() => {
          if (el.style.length === 1 && el.style.display === "none") {
            el.removeAttribute("style");
          } else {
            el.style.removeProperty("display");
          }
        });
      };
    let hide = () => {
      el._x_doHide();
      el._x_isShown = false;
    };
    let show = () => {
      el._x_doShow();
      el._x_isShown = true;
    };
    let clickAwayCompatibleShow = () => setTimeout(show);
    let toggle = once((value) => value ? show() : hide(), (value) => {
      if (typeof el._x_toggleAndCascadeWithTransitions === "function") {
        el._x_toggleAndCascadeWithTransitions(el, value, show, hide);
      } else {
        value ? clickAwayCompatibleShow() : hide();
      }
    });
    let oldValue;
    let firstTime = true;
    effect3(() => evaluate2((value) => {
      if (!firstTime && value === oldValue)
        return;
      if (modifiers.includes("immediate"))
        value ? clickAwayCompatibleShow() : hide();
      toggle(value);
      oldValue = value;
      firstTime = false;
    }));
  });
  directive("for", (el, { expression }, { effect: effect3, cleanup: cleanup2 }) => {
    let iteratorNames = parseForExpression(expression);
    let evaluateItems = evaluateLater(el, iteratorNames.items);
    let evaluateKey = evaluateLater(el, el._x_keyExpression || "index");
    el._x_prevKeys = [];
    el._x_lookup = {};
    effect3(() => loop(el, iteratorNames, evaluateItems, evaluateKey));
    cleanup2(() => {
      Object.values(el._x_lookup).forEach((el2) => mutateDom(() => {
        destroyTree(el2);
        el2.remove();
      }));
      delete el._x_prevKeys;
      delete el._x_lookup;
    });
  });
  function loop(el, iteratorNames, evaluateItems, evaluateKey) {
    let isObject22 = (i) => typeof i === "object" && !Array.isArray(i);
    let templateEl = el;
    evaluateItems((items) => {
      if (isNumeric3(items) && items >= 0) {
        items = Array.from(Array(items).keys(), (i) => i + 1);
      }
      if (items === void 0)
        items = [];
      let lookup = el._x_lookup;
      let prevKeys = el._x_prevKeys;
      let scopes = [];
      let keys = [];
      if (isObject22(items)) {
        items = Object.entries(items).map(([key, value]) => {
          let scope2 = getIterationScopeVariables(iteratorNames, value, key, items);
          evaluateKey((value2) => {
            if (keys.includes(value2))
              warn("Duplicate key on x-for", el);
            keys.push(value2);
          }, { scope: { index: key, ...scope2 } });
          scopes.push(scope2);
        });
      } else {
        for (let i = 0; i < items.length; i++) {
          let scope2 = getIterationScopeVariables(iteratorNames, items[i], i, items);
          evaluateKey((value) => {
            if (keys.includes(value))
              warn("Duplicate key on x-for", el);
            keys.push(value);
          }, { scope: { index: i, ...scope2 } });
          scopes.push(scope2);
        }
      }
      let adds = [];
      let moves = [];
      let removes = [];
      let sames = [];
      for (let i = 0; i < prevKeys.length; i++) {
        let key = prevKeys[i];
        if (keys.indexOf(key) === -1)
          removes.push(key);
      }
      prevKeys = prevKeys.filter((key) => !removes.includes(key));
      let lastKey = "template";
      for (let i = 0; i < keys.length; i++) {
        let key = keys[i];
        let prevIndex = prevKeys.indexOf(key);
        if (prevIndex === -1) {
          prevKeys.splice(i, 0, key);
          adds.push([lastKey, i]);
        } else if (prevIndex !== i) {
          let keyInSpot = prevKeys.splice(i, 1)[0];
          let keyForSpot = prevKeys.splice(prevIndex - 1, 1)[0];
          prevKeys.splice(i, 0, keyForSpot);
          prevKeys.splice(prevIndex, 0, keyInSpot);
          moves.push([keyInSpot, keyForSpot]);
        } else {
          sames.push(key);
        }
        lastKey = key;
      }
      for (let i = 0; i < removes.length; i++) {
        let key = removes[i];
        if (!(key in lookup))
          continue;
        mutateDom(() => {
          destroyTree(lookup[key]);
          lookup[key].remove();
        });
        delete lookup[key];
      }
      for (let i = 0; i < moves.length; i++) {
        let [keyInSpot, keyForSpot] = moves[i];
        let elInSpot = lookup[keyInSpot];
        let elForSpot = lookup[keyForSpot];
        let marker = document.createElement("div");
        mutateDom(() => {
          if (!elForSpot)
            warn(`x-for ":key" is undefined or invalid`, templateEl, keyForSpot, lookup);
          elForSpot.after(marker);
          elInSpot.after(elForSpot);
          elForSpot._x_currentIfEl && elForSpot.after(elForSpot._x_currentIfEl);
          marker.before(elInSpot);
          elInSpot._x_currentIfEl && elInSpot.after(elInSpot._x_currentIfEl);
          marker.remove();
        });
        elForSpot._x_refreshXForScope(scopes[keys.indexOf(keyForSpot)]);
      }
      for (let i = 0; i < adds.length; i++) {
        let [lastKey2, index] = adds[i];
        let lastEl = lastKey2 === "template" ? templateEl : lookup[lastKey2];
        if (lastEl._x_currentIfEl)
          lastEl = lastEl._x_currentIfEl;
        let scope2 = scopes[index];
        let key = keys[index];
        let clone22 = document.importNode(templateEl.content, true).firstElementChild;
        let reactiveScope = reactive(scope2);
        addScopeToNode(clone22, reactiveScope, templateEl);
        clone22._x_refreshXForScope = (newScope) => {
          Object.entries(newScope).forEach(([key2, value]) => {
            reactiveScope[key2] = value;
          });
        };
        mutateDom(() => {
          lastEl.after(clone22);
          skipDuringClone(() => initTree(clone22))();
        });
        if (typeof key === "object") {
          warn("x-for key cannot be an object, it must be a string or an integer", templateEl);
        }
        lookup[key] = clone22;
      }
      for (let i = 0; i < sames.length; i++) {
        lookup[sames[i]]._x_refreshXForScope(scopes[keys.indexOf(sames[i])]);
      }
      templateEl._x_prevKeys = keys;
    });
  }
  function parseForExpression(expression) {
    let forIteratorRE = /,([^,\}\]]*)(?:,([^,\}\]]*))?$/;
    let stripParensRE = /^\s*\(|\)\s*$/g;
    let forAliasRE = /([\s\S]*?)\s+(?:in|of)\s+([\s\S]*)/;
    let inMatch = expression.match(forAliasRE);
    if (!inMatch)
      return;
    let res = {};
    res.items = inMatch[2].trim();
    let item = inMatch[1].replace(stripParensRE, "").trim();
    let iteratorMatch = item.match(forIteratorRE);
    if (iteratorMatch) {
      res.item = item.replace(forIteratorRE, "").trim();
      res.index = iteratorMatch[1].trim();
      if (iteratorMatch[2]) {
        res.collection = iteratorMatch[2].trim();
      }
    } else {
      res.item = item;
    }
    return res;
  }
  function getIterationScopeVariables(iteratorNames, item, index, items) {
    let scopeVariables = {};
    if (/^\[.*\]$/.test(iteratorNames.item) && Array.isArray(item)) {
      let names = iteratorNames.item.replace("[", "").replace("]", "").split(",").map((i) => i.trim());
      names.forEach((name, i) => {
        scopeVariables[name] = item[i];
      });
    } else if (/^\{.*\}$/.test(iteratorNames.item) && !Array.isArray(item) && typeof item === "object") {
      let names = iteratorNames.item.replace("{", "").replace("}", "").split(",").map((i) => i.trim());
      names.forEach((name) => {
        scopeVariables[name] = item[name];
      });
    } else {
      scopeVariables[iteratorNames.item] = item;
    }
    if (iteratorNames.index)
      scopeVariables[iteratorNames.index] = index;
    if (iteratorNames.collection)
      scopeVariables[iteratorNames.collection] = items;
    return scopeVariables;
  }
  function isNumeric3(subject) {
    return !Array.isArray(subject) && !isNaN(subject);
  }
  function handler3() {
  }
  handler3.inline = (el, { expression }, { cleanup: cleanup2 }) => {
    let root = closestRoot(el);
    if (!root._x_refs)
      root._x_refs = {};
    root._x_refs[expression] = el;
    cleanup2(() => delete root._x_refs[expression]);
  };
  directive("ref", handler3);
  directive("if", (el, { expression }, { effect: effect3, cleanup: cleanup2 }) => {
    if (el.tagName.toLowerCase() !== "template")
      warn("x-if can only be used on a <template> tag", el);
    let evaluate2 = evaluateLater(el, expression);
    let show = () => {
      if (el._x_currentIfEl)
        return el._x_currentIfEl;
      let clone22 = el.content.cloneNode(true).firstElementChild;
      addScopeToNode(clone22, {}, el);
      mutateDom(() => {
        el.after(clone22);
        skipDuringClone(() => initTree(clone22))();
      });
      el._x_currentIfEl = clone22;
      el._x_undoIf = () => {
        mutateDom(() => {
          destroyTree(clone22);
          clone22.remove();
        });
        delete el._x_currentIfEl;
      };
      return clone22;
    };
    let hide = () => {
      if (!el._x_undoIf)
        return;
      el._x_undoIf();
      delete el._x_undoIf;
    };
    effect3(() => evaluate2((value) => {
      value ? show() : hide();
    }));
    cleanup2(() => el._x_undoIf && el._x_undoIf());
  });
  directive("id", (el, { expression }, { evaluate: evaluate2 }) => {
    let names = evaluate2(expression);
    names.forEach((name) => setIdRoot(el, name));
  });
  interceptClone((from, to) => {
    if (from._x_ids) {
      to._x_ids = from._x_ids;
    }
  });
  mapAttributes(startingWith("@", into(prefix("on:"))));
  directive("on", skipDuringClone((el, { value, modifiers, expression }, { cleanup: cleanup2 }) => {
    let evaluate2 = expression ? evaluateLater(el, expression) : () => {
    };
    if (el.tagName.toLowerCase() === "template") {
      if (!el._x_forwardEvents)
        el._x_forwardEvents = [];
      if (!el._x_forwardEvents.includes(value))
        el._x_forwardEvents.push(value);
    }
    let removeListener = on(el, value, modifiers, (e) => {
      evaluate2(() => {
      }, { scope: { "$event": e }, params: [e] });
    });
    cleanup2(() => removeListener());
  }));
  warnMissingPluginDirective("Collapse", "collapse", "collapse");
  warnMissingPluginDirective("Intersect", "intersect", "intersect");
  warnMissingPluginDirective("Focus", "trap", "focus");
  warnMissingPluginDirective("Mask", "mask", "mask");
  function warnMissingPluginDirective(name, directiveName, slug) {
    directive(directiveName, (el) => warn(`You can't use [x-${directiveName}] without first installing the "${name}" plugin here: https://alpinejs.dev/plugins/${slug}`, el));
  }
  alpine_default.setEvaluator(normalEvaluator);
  alpine_default.setRawEvaluator(normalRawEvaluator);
  alpine_default.setReactivityEngine({ reactive: reactive2, effect: effect2, release: stop, raw: toRaw });
  var src_default = alpine_default;
  var module_default = src_default;

  // node_modules/yup/index.esm.js
  var index_esm_exports = {};
  __export(index_esm_exports, {
    ArraySchema: () => ArraySchema,
    BooleanSchema: () => BooleanSchema,
    DateSchema: () => DateSchema,
    MixedSchema: () => MixedSchema,
    NumberSchema: () => NumberSchema,
    ObjectSchema: () => ObjectSchema,
    Schema: () => Schema,
    StringSchema: () => StringSchema,
    TupleSchema: () => TupleSchema,
    ValidationError: () => ValidationError,
    addMethod: () => addMethod,
    array: () => create$2,
    bool: () => create$7,
    boolean: () => create$7,
    date: () => create$4,
    defaultLocale: () => locale,
    getIn: () => getIn,
    isSchema: () => isSchema,
    lazy: () => create,
    mixed: () => create$8,
    number: () => create$5,
    object: () => create$3,
    printValue: () => printValue,
    reach: () => reach,
    ref: () => create$9,
    setLocale: () => setLocale,
    string: () => create$6,
    tuple: () => create$1
  });
  var import_property_expr = __toModule(require_property_expr());
  var import_tiny_case = __toModule(require_tiny_case());
  var import_toposort = __toModule(require_toposort());
  var toString = Object.prototype.toString;
  var errorToString = Error.prototype.toString;
  var regExpToString = RegExp.prototype.toString;
  var symbolToString = typeof Symbol !== "undefined" ? Symbol.prototype.toString : () => "";
  var SYMBOL_REGEXP = /^Symbol\((.*)\)(.*)$/;
  function printNumber(val) {
    if (val != +val)
      return "NaN";
    const isNegativeZero = val === 0 && 1 / val < 0;
    return isNegativeZero ? "-0" : "" + val;
  }
  function printSimpleValue(val, quoteStrings = false) {
    if (val == null || val === true || val === false)
      return "" + val;
    const typeOf = typeof val;
    if (typeOf === "number")
      return printNumber(val);
    if (typeOf === "string")
      return quoteStrings ? `"${val}"` : val;
    if (typeOf === "function")
      return "[Function " + (val.name || "anonymous") + "]";
    if (typeOf === "symbol")
      return symbolToString.call(val).replace(SYMBOL_REGEXP, "Symbol($1)");
    const tag = toString.call(val).slice(8, -1);
    if (tag === "Date")
      return isNaN(val.getTime()) ? "" + val : val.toISOString(val);
    if (tag === "Error" || val instanceof Error)
      return "[" + errorToString.call(val) + "]";
    if (tag === "RegExp")
      return regExpToString.call(val);
    return null;
  }
  function printValue(value, quoteStrings) {
    let result = printSimpleValue(value, quoteStrings);
    if (result !== null)
      return result;
    return JSON.stringify(value, function(key, value2) {
      let result2 = printSimpleValue(this[key], quoteStrings);
      if (result2 !== null)
        return result2;
      return value2;
    }, 2);
  }
  function toArray(value) {
    return value == null ? [] : [].concat(value);
  }
  var _Symbol$toStringTag;
  var _Symbol$hasInstance;
  var _Symbol$toStringTag2;
  var strReg = /\$\{\s*(\w+)\s*\}/g;
  _Symbol$toStringTag = Symbol.toStringTag;
  var ValidationErrorNoStack = class {
    constructor(errorOrErrors, value, field, type) {
      this.name = void 0;
      this.message = void 0;
      this.value = void 0;
      this.path = void 0;
      this.type = void 0;
      this.params = void 0;
      this.errors = void 0;
      this.inner = void 0;
      this[_Symbol$toStringTag] = "Error";
      this.name = "ValidationError";
      this.value = value;
      this.path = field;
      this.type = type;
      this.errors = [];
      this.inner = [];
      toArray(errorOrErrors).forEach((err) => {
        if (ValidationError.isError(err)) {
          this.errors.push(...err.errors);
          const innerErrors = err.inner.length ? err.inner : [err];
          this.inner.push(...innerErrors);
        } else {
          this.errors.push(err);
        }
      });
      this.message = this.errors.length > 1 ? `${this.errors.length} errors occurred` : this.errors[0];
    }
  };
  _Symbol$hasInstance = Symbol.hasInstance;
  _Symbol$toStringTag2 = Symbol.toStringTag;
  var ValidationError = class extends Error {
    static formatError(message, params) {
      const path = params.label || params.path || "this";
      if (path !== params.path)
        params = Object.assign({}, params, {
          path
        });
      if (typeof message === "string")
        return message.replace(strReg, (_, key) => printValue(params[key]));
      if (typeof message === "function")
        return message(params);
      return message;
    }
    static isError(err) {
      return err && err.name === "ValidationError";
    }
    constructor(errorOrErrors, value, field, type, disableStack) {
      const errorNoStack = new ValidationErrorNoStack(errorOrErrors, value, field, type);
      if (disableStack) {
        return errorNoStack;
      }
      super();
      this.value = void 0;
      this.path = void 0;
      this.type = void 0;
      this.params = void 0;
      this.errors = [];
      this.inner = [];
      this[_Symbol$toStringTag2] = "Error";
      this.name = errorNoStack.name;
      this.message = errorNoStack.message;
      this.type = errorNoStack.type;
      this.value = errorNoStack.value;
      this.path = errorNoStack.path;
      this.errors = errorNoStack.errors;
      this.inner = errorNoStack.inner;
      if (Error.captureStackTrace) {
        Error.captureStackTrace(this, ValidationError);
      }
    }
    static [_Symbol$hasInstance](inst) {
      return ValidationErrorNoStack[Symbol.hasInstance](inst) || super[Symbol.hasInstance](inst);
    }
  };
  var mixed = {
    default: "${path} is invalid",
    required: "${path} is a required field",
    defined: "${path} must be defined",
    notNull: "${path} cannot be null",
    oneOf: "${path} must be one of the following values: ${values}",
    notOneOf: "${path} must not be one of the following values: ${values}",
    notType: ({
      path,
      type,
      value,
      originalValue
    }) => {
      const castMsg = originalValue != null && originalValue !== value ? ` (cast from the value \`${printValue(originalValue, true)}\`).` : ".";
      return type !== "mixed" ? `${path} must be a \`${type}\` type, but the final value was: \`${printValue(value, true)}\`` + castMsg : `${path} must match the configured type. The validated value was: \`${printValue(value, true)}\`` + castMsg;
    }
  };
  var string = {
    length: "${path} must be exactly ${length} characters",
    min: "${path} must be at least ${min} characters",
    max: "${path} must be at most ${max} characters",
    matches: '${path} must match the following: "${regex}"',
    email: "${path} must be a valid email",
    url: "${path} must be a valid URL",
    uuid: "${path} must be a valid UUID",
    datetime: "${path} must be a valid ISO date-time",
    datetime_precision: "${path} must be a valid ISO date-time with a sub-second precision of exactly ${precision} digits",
    datetime_offset: '${path} must be a valid ISO date-time with UTC "Z" timezone',
    trim: "${path} must be a trimmed string",
    lowercase: "${path} must be a lowercase string",
    uppercase: "${path} must be a upper case string"
  };
  var number = {
    min: "${path} must be greater than or equal to ${min}",
    max: "${path} must be less than or equal to ${max}",
    lessThan: "${path} must be less than ${less}",
    moreThan: "${path} must be greater than ${more}",
    positive: "${path} must be a positive number",
    negative: "${path} must be a negative number",
    integer: "${path} must be an integer"
  };
  var date = {
    min: "${path} field must be later than ${min}",
    max: "${path} field must be at earlier than ${max}"
  };
  var boolean = {
    isValue: "${path} field must be ${value}"
  };
  var object = {
    noUnknown: "${path} field has unspecified keys: ${unknown}"
  };
  var array = {
    min: "${path} field must have at least ${min} items",
    max: "${path} field must have less than or equal to ${max} items",
    length: "${path} must have ${length} items"
  };
  var tuple = {
    notType: (params) => {
      const {
        path,
        value,
        spec
      } = params;
      const typeLen = spec.types.length;
      if (Array.isArray(value)) {
        if (value.length < typeLen)
          return `${path} tuple value has too few items, expected a length of ${typeLen} but got ${value.length} for value: \`${printValue(value, true)}\``;
        if (value.length > typeLen)
          return `${path} tuple value has too many items, expected a length of ${typeLen} but got ${value.length} for value: \`${printValue(value, true)}\``;
      }
      return ValidationError.formatError(mixed.notType, params);
    }
  };
  var locale = Object.assign(Object.create(null), {
    mixed,
    string,
    number,
    date,
    object,
    array,
    boolean,
    tuple
  });
  var isSchema = (obj) => obj && obj.__isYupSchema__;
  var Condition = class {
    static fromOptions(refs, config) {
      if (!config.then && !config.otherwise)
        throw new TypeError("either `then:` or `otherwise:` is required for `when()` conditions");
      let {
        is,
        then,
        otherwise
      } = config;
      let check = typeof is === "function" ? is : (...values) => values.every((value) => value === is);
      return new Condition(refs, (values, schema) => {
        var _branch;
        let branch = check(...values) ? then : otherwise;
        return (_branch = branch == null ? void 0 : branch(schema)) != null ? _branch : schema;
      });
    }
    constructor(refs, builder) {
      this.fn = void 0;
      this.refs = refs;
      this.refs = refs;
      this.fn = builder;
    }
    resolve(base, options) {
      let values = this.refs.map((ref) => ref.getValue(options == null ? void 0 : options.value, options == null ? void 0 : options.parent, options == null ? void 0 : options.context));
      let schema = this.fn(values, base, options);
      if (schema === void 0 || schema === base) {
        return base;
      }
      if (!isSchema(schema))
        throw new TypeError("conditions must return a schema object");
      return schema.resolve(options);
    }
  };
  var prefixes = {
    context: "$",
    value: "."
  };
  function create$9(key, options) {
    return new Reference(key, options);
  }
  var Reference = class {
    constructor(key, options = {}) {
      this.key = void 0;
      this.isContext = void 0;
      this.isValue = void 0;
      this.isSibling = void 0;
      this.path = void 0;
      this.getter = void 0;
      this.map = void 0;
      if (typeof key !== "string")
        throw new TypeError("ref must be a string, got: " + key);
      this.key = key.trim();
      if (key === "")
        throw new TypeError("ref must be a non-empty string");
      this.isContext = this.key[0] === prefixes.context;
      this.isValue = this.key[0] === prefixes.value;
      this.isSibling = !this.isContext && !this.isValue;
      let prefix2 = this.isContext ? prefixes.context : this.isValue ? prefixes.value : "";
      this.path = this.key.slice(prefix2.length);
      this.getter = this.path && (0, import_property_expr.getter)(this.path, true);
      this.map = options.map;
    }
    getValue(value, parent, context) {
      let result = this.isContext ? context : this.isValue ? value : parent;
      if (this.getter)
        result = this.getter(result || {});
      if (this.map)
        result = this.map(result);
      return result;
    }
    cast(value, options) {
      return this.getValue(value, options == null ? void 0 : options.parent, options == null ? void 0 : options.context);
    }
    resolve() {
      return this;
    }
    describe() {
      return {
        type: "ref",
        key: this.key
      };
    }
    toString() {
      return `Ref(${this.key})`;
    }
    static isRef(value) {
      return value && value.__isYupRef;
    }
  };
  Reference.prototype.__isYupRef = true;
  var isAbsent = (value) => value == null;
  function createValidation(config) {
    function validate({
      value,
      path = "",
      options,
      originalValue,
      schema
    }, panic, next) {
      const {
        name,
        test,
        params,
        message,
        skipAbsent
      } = config;
      let {
        parent,
        context,
        abortEarly = schema.spec.abortEarly,
        disableStackTrace = schema.spec.disableStackTrace
      } = options;
      function resolve(item) {
        return Reference.isRef(item) ? item.getValue(value, parent, context) : item;
      }
      function createError(overrides = {}) {
        const nextParams = Object.assign({
          value,
          originalValue,
          label: schema.spec.label,
          path: overrides.path || path,
          spec: schema.spec,
          disableStackTrace: overrides.disableStackTrace || disableStackTrace
        }, params, overrides.params);
        for (const key of Object.keys(nextParams))
          nextParams[key] = resolve(nextParams[key]);
        const error2 = new ValidationError(ValidationError.formatError(overrides.message || message, nextParams), value, nextParams.path, overrides.type || name, nextParams.disableStackTrace);
        error2.params = nextParams;
        return error2;
      }
      const invalid = abortEarly ? panic : next;
      let ctx = {
        path,
        parent,
        type: name,
        from: options.from,
        createError,
        resolve,
        options,
        originalValue,
        schema
      };
      const handleResult = (validOrError) => {
        if (ValidationError.isError(validOrError))
          invalid(validOrError);
        else if (!validOrError)
          invalid(createError());
        else
          next(null);
      };
      const handleError2 = (err) => {
        if (ValidationError.isError(err))
          invalid(err);
        else
          panic(err);
      };
      const shouldSkip = skipAbsent && isAbsent(value);
      if (shouldSkip) {
        return handleResult(true);
      }
      let result;
      try {
        var _result;
        result = test.call(ctx, value, ctx);
        if (typeof ((_result = result) == null ? void 0 : _result.then) === "function") {
          if (options.sync) {
            throw new Error(`Validation test of type: "${ctx.type}" returned a Promise during a synchronous validate. This test will finish after the validate call has returned`);
          }
          return Promise.resolve(result).then(handleResult, handleError2);
        }
      } catch (err) {
        handleError2(err);
        return;
      }
      handleResult(result);
    }
    validate.OPTIONS = config;
    return validate;
  }
  function getIn(schema, path, value, context = value) {
    let parent, lastPart, lastPartDebug;
    if (!path)
      return {
        parent,
        parentPath: path,
        schema
      };
    (0, import_property_expr.forEach)(path, (_part, isBracket, isArray2) => {
      let part = isBracket ? _part.slice(1, _part.length - 1) : _part;
      schema = schema.resolve({
        context,
        parent,
        value
      });
      let isTuple = schema.type === "tuple";
      let idx = isArray2 ? parseInt(part, 10) : 0;
      if (schema.innerType || isTuple) {
        if (isTuple && !isArray2)
          throw new Error(`Yup.reach cannot implicitly index into a tuple type. the path part "${lastPartDebug}" must contain an index to the tuple element, e.g. "${lastPartDebug}[0]"`);
        if (value && idx >= value.length) {
          throw new Error(`Yup.reach cannot resolve an array item at index: ${_part}, in the path: ${path}. because there is no value at that index. `);
        }
        parent = value;
        value = value && value[idx];
        schema = isTuple ? schema.spec.types[idx] : schema.innerType;
      }
      if (!isArray2) {
        if (!schema.fields || !schema.fields[part])
          throw new Error(`The schema does not contain the path: ${path}. (failed at: ${lastPartDebug} which is a type: "${schema.type}")`);
        parent = value;
        value = value && value[part];
        schema = schema.fields[part];
      }
      lastPart = part;
      lastPartDebug = isBracket ? "[" + _part + "]" : "." + _part;
    });
    return {
      schema,
      parent,
      parentPath: lastPart
    };
  }
  function reach(obj, path, value, context) {
    return getIn(obj, path, value, context).schema;
  }
  var ReferenceSet = class extends Set {
    describe() {
      const description = [];
      for (const item of this.values()) {
        description.push(Reference.isRef(item) ? item.describe() : item);
      }
      return description;
    }
    resolveAll(resolve) {
      let result = [];
      for (const item of this.values()) {
        result.push(resolve(item));
      }
      return result;
    }
    clone() {
      return new ReferenceSet(this.values());
    }
    merge(newItems, removeItems) {
      const next = this.clone();
      newItems.forEach((value) => next.add(value));
      removeItems.forEach((value) => next.delete(value));
      return next;
    }
  };
  function clone2(src, seen = new Map()) {
    if (isSchema(src) || !src || typeof src !== "object")
      return src;
    if (seen.has(src))
      return seen.get(src);
    let copy;
    if (src instanceof Date) {
      copy = new Date(src.getTime());
      seen.set(src, copy);
    } else if (src instanceof RegExp) {
      copy = new RegExp(src);
      seen.set(src, copy);
    } else if (Array.isArray(src)) {
      copy = new Array(src.length);
      seen.set(src, copy);
      for (let i = 0; i < src.length; i++)
        copy[i] = clone2(src[i], seen);
    } else if (src instanceof Map) {
      copy = new Map();
      seen.set(src, copy);
      for (const [k, v] of src.entries())
        copy.set(k, clone2(v, seen));
    } else if (src instanceof Set) {
      copy = new Set();
      seen.set(src, copy);
      for (const v of src)
        copy.add(clone2(v, seen));
    } else if (src instanceof Object) {
      copy = {};
      seen.set(src, copy);
      for (const [k, v] of Object.entries(src))
        copy[k] = clone2(v, seen);
    } else {
      throw Error(`Unable to clone ${src}`);
    }
    return copy;
  }
  var Schema = class {
    constructor(options) {
      this.type = void 0;
      this.deps = [];
      this.tests = void 0;
      this.transforms = void 0;
      this.conditions = [];
      this._mutate = void 0;
      this.internalTests = {};
      this._whitelist = new ReferenceSet();
      this._blacklist = new ReferenceSet();
      this.exclusiveTests = Object.create(null);
      this._typeCheck = void 0;
      this.spec = void 0;
      this.tests = [];
      this.transforms = [];
      this.withMutation(() => {
        this.typeError(mixed.notType);
      });
      this.type = options.type;
      this._typeCheck = options.check;
      this.spec = Object.assign({
        strip: false,
        strict: false,
        abortEarly: true,
        recursive: true,
        disableStackTrace: false,
        nullable: false,
        optional: true,
        coerce: true
      }, options == null ? void 0 : options.spec);
      this.withMutation((s) => {
        s.nonNullable();
      });
    }
    get _type() {
      return this.type;
    }
    clone(spec) {
      if (this._mutate) {
        if (spec)
          Object.assign(this.spec, spec);
        return this;
      }
      const next = Object.create(Object.getPrototypeOf(this));
      next.type = this.type;
      next._typeCheck = this._typeCheck;
      next._whitelist = this._whitelist.clone();
      next._blacklist = this._blacklist.clone();
      next.internalTests = Object.assign({}, this.internalTests);
      next.exclusiveTests = Object.assign({}, this.exclusiveTests);
      next.deps = [...this.deps];
      next.conditions = [...this.conditions];
      next.tests = [...this.tests];
      next.transforms = [...this.transforms];
      next.spec = clone2(Object.assign({}, this.spec, spec));
      return next;
    }
    label(label) {
      let next = this.clone();
      next.spec.label = label;
      return next;
    }
    meta(...args) {
      if (args.length === 0)
        return this.spec.meta;
      let next = this.clone();
      next.spec.meta = Object.assign(next.spec.meta || {}, args[0]);
      return next;
    }
    withMutation(fn) {
      let before = this._mutate;
      this._mutate = true;
      let result = fn(this);
      this._mutate = before;
      return result;
    }
    concat(schema) {
      if (!schema || schema === this)
        return this;
      if (schema.type !== this.type && this.type !== "mixed")
        throw new TypeError(`You cannot \`concat()\` schema's of different types: ${this.type} and ${schema.type}`);
      let base = this;
      let combined = schema.clone();
      const mergedSpec = Object.assign({}, base.spec, combined.spec);
      combined.spec = mergedSpec;
      combined.internalTests = Object.assign({}, base.internalTests, combined.internalTests);
      combined._whitelist = base._whitelist.merge(schema._whitelist, schema._blacklist);
      combined._blacklist = base._blacklist.merge(schema._blacklist, schema._whitelist);
      combined.tests = base.tests;
      combined.exclusiveTests = base.exclusiveTests;
      combined.withMutation((next) => {
        schema.tests.forEach((fn) => {
          next.test(fn.OPTIONS);
        });
      });
      combined.transforms = [...base.transforms, ...combined.transforms];
      return combined;
    }
    isType(v) {
      if (v == null) {
        if (this.spec.nullable && v === null)
          return true;
        if (this.spec.optional && v === void 0)
          return true;
        return false;
      }
      return this._typeCheck(v);
    }
    resolve(options) {
      let schema = this;
      if (schema.conditions.length) {
        let conditions = schema.conditions;
        schema = schema.clone();
        schema.conditions = [];
        schema = conditions.reduce((prevSchema, condition) => condition.resolve(prevSchema, options), schema);
        schema = schema.resolve(options);
      }
      return schema;
    }
    resolveOptions(options) {
      var _options$strict, _options$abortEarly, _options$recursive, _options$disableStack;
      return Object.assign({}, options, {
        from: options.from || [],
        strict: (_options$strict = options.strict) != null ? _options$strict : this.spec.strict,
        abortEarly: (_options$abortEarly = options.abortEarly) != null ? _options$abortEarly : this.spec.abortEarly,
        recursive: (_options$recursive = options.recursive) != null ? _options$recursive : this.spec.recursive,
        disableStackTrace: (_options$disableStack = options.disableStackTrace) != null ? _options$disableStack : this.spec.disableStackTrace
      });
    }
    cast(value, options = {}) {
      let resolvedSchema = this.resolve(Object.assign({
        value
      }, options));
      let allowOptionality = options.assert === "ignore-optionality";
      let result = resolvedSchema._cast(value, options);
      if (options.assert !== false && !resolvedSchema.isType(result)) {
        if (allowOptionality && isAbsent(result)) {
          return result;
        }
        let formattedValue = printValue(value);
        let formattedResult = printValue(result);
        throw new TypeError(`The value of ${options.path || "field"} could not be cast to a value that satisfies the schema type: "${resolvedSchema.type}". 

attempted value: ${formattedValue} 
` + (formattedResult !== formattedValue ? `result of cast: ${formattedResult}` : ""));
      }
      return result;
    }
    _cast(rawValue, options) {
      let value = rawValue === void 0 ? rawValue : this.transforms.reduce((prevValue, fn) => fn.call(this, prevValue, rawValue, this), rawValue);
      if (value === void 0) {
        value = this.getDefault(options);
      }
      return value;
    }
    _validate(_value, options = {}, panic, next) {
      let {
        path,
        originalValue = _value,
        strict = this.spec.strict
      } = options;
      let value = _value;
      if (!strict) {
        value = this._cast(value, Object.assign({
          assert: false
        }, options));
      }
      let initialTests = [];
      for (let test of Object.values(this.internalTests)) {
        if (test)
          initialTests.push(test);
      }
      this.runTests({
        path,
        value,
        originalValue,
        options,
        tests: initialTests
      }, panic, (initialErrors) => {
        if (initialErrors.length) {
          return next(initialErrors, value);
        }
        this.runTests({
          path,
          value,
          originalValue,
          options,
          tests: this.tests
        }, panic, next);
      });
    }
    runTests(runOptions, panic, next) {
      let fired = false;
      let {
        tests,
        value,
        originalValue,
        path,
        options
      } = runOptions;
      let panicOnce = (arg) => {
        if (fired)
          return;
        fired = true;
        panic(arg, value);
      };
      let nextOnce = (arg) => {
        if (fired)
          return;
        fired = true;
        next(arg, value);
      };
      let count = tests.length;
      let nestedErrors = [];
      if (!count)
        return nextOnce([]);
      let args = {
        value,
        originalValue,
        path,
        options,
        schema: this
      };
      for (let i = 0; i < tests.length; i++) {
        const test = tests[i];
        test(args, panicOnce, function finishTestRun(err) {
          if (err) {
            Array.isArray(err) ? nestedErrors.push(...err) : nestedErrors.push(err);
          }
          if (--count <= 0) {
            nextOnce(nestedErrors);
          }
        });
      }
    }
    asNestedTest({
      key,
      index,
      parent,
      parentPath,
      originalParent,
      options
    }) {
      const k = key != null ? key : index;
      if (k == null) {
        throw TypeError("Must include `key` or `index` for nested validations");
      }
      const isIndex = typeof k === "number";
      let value = parent[k];
      const testOptions = Object.assign({}, options, {
        strict: true,
        parent,
        value,
        originalValue: originalParent[k],
        key: void 0,
        [isIndex ? "index" : "key"]: k,
        path: isIndex || k.includes(".") ? `${parentPath || ""}[${isIndex ? k : `"${k}"`}]` : (parentPath ? `${parentPath}.` : "") + key
      });
      return (_, panic, next) => this.resolve(testOptions)._validate(value, testOptions, panic, next);
    }
    validate(value, options) {
      var _options$disableStack2;
      let schema = this.resolve(Object.assign({}, options, {
        value
      }));
      let disableStackTrace = (_options$disableStack2 = options == null ? void 0 : options.disableStackTrace) != null ? _options$disableStack2 : schema.spec.disableStackTrace;
      return new Promise((resolve, reject) => schema._validate(value, options, (error2, parsed) => {
        if (ValidationError.isError(error2))
          error2.value = parsed;
        reject(error2);
      }, (errors, validated) => {
        if (errors.length)
          reject(new ValidationError(errors, validated, void 0, void 0, disableStackTrace));
        else
          resolve(validated);
      }));
    }
    validateSync(value, options) {
      var _options$disableStack3;
      let schema = this.resolve(Object.assign({}, options, {
        value
      }));
      let result;
      let disableStackTrace = (_options$disableStack3 = options == null ? void 0 : options.disableStackTrace) != null ? _options$disableStack3 : schema.spec.disableStackTrace;
      schema._validate(value, Object.assign({}, options, {
        sync: true
      }), (error2, parsed) => {
        if (ValidationError.isError(error2))
          error2.value = parsed;
        throw error2;
      }, (errors, validated) => {
        if (errors.length)
          throw new ValidationError(errors, value, void 0, void 0, disableStackTrace);
        result = validated;
      });
      return result;
    }
    isValid(value, options) {
      return this.validate(value, options).then(() => true, (err) => {
        if (ValidationError.isError(err))
          return false;
        throw err;
      });
    }
    isValidSync(value, options) {
      try {
        this.validateSync(value, options);
        return true;
      } catch (err) {
        if (ValidationError.isError(err))
          return false;
        throw err;
      }
    }
    _getDefault(options) {
      let defaultValue = this.spec.default;
      if (defaultValue == null) {
        return defaultValue;
      }
      return typeof defaultValue === "function" ? defaultValue.call(this, options) : clone2(defaultValue);
    }
    getDefault(options) {
      let schema = this.resolve(options || {});
      return schema._getDefault(options);
    }
    default(def) {
      if (arguments.length === 0) {
        return this._getDefault();
      }
      let next = this.clone({
        default: def
      });
      return next;
    }
    strict(isStrict = true) {
      return this.clone({
        strict: isStrict
      });
    }
    nullability(nullable, message) {
      const next = this.clone({
        nullable
      });
      next.internalTests.nullable = createValidation({
        message,
        name: "nullable",
        test(value) {
          return value === null ? this.schema.spec.nullable : true;
        }
      });
      return next;
    }
    optionality(optional, message) {
      const next = this.clone({
        optional
      });
      next.internalTests.optionality = createValidation({
        message,
        name: "optionality",
        test(value) {
          return value === void 0 ? this.schema.spec.optional : true;
        }
      });
      return next;
    }
    optional() {
      return this.optionality(true);
    }
    defined(message = mixed.defined) {
      return this.optionality(false, message);
    }
    nullable() {
      return this.nullability(true);
    }
    nonNullable(message = mixed.notNull) {
      return this.nullability(false, message);
    }
    required(message = mixed.required) {
      return this.clone().withMutation((next) => next.nonNullable(message).defined(message));
    }
    notRequired() {
      return this.clone().withMutation((next) => next.nullable().optional());
    }
    transform(fn) {
      let next = this.clone();
      next.transforms.push(fn);
      return next;
    }
    test(...args) {
      let opts;
      if (args.length === 1) {
        if (typeof args[0] === "function") {
          opts = {
            test: args[0]
          };
        } else {
          opts = args[0];
        }
      } else if (args.length === 2) {
        opts = {
          name: args[0],
          test: args[1]
        };
      } else {
        opts = {
          name: args[0],
          message: args[1],
          test: args[2]
        };
      }
      if (opts.message === void 0)
        opts.message = mixed.default;
      if (typeof opts.test !== "function")
        throw new TypeError("`test` is a required parameters");
      let next = this.clone();
      let validate = createValidation(opts);
      let isExclusive = opts.exclusive || opts.name && next.exclusiveTests[opts.name] === true;
      if (opts.exclusive) {
        if (!opts.name)
          throw new TypeError("Exclusive tests must provide a unique `name` identifying the test");
      }
      if (opts.name)
        next.exclusiveTests[opts.name] = !!opts.exclusive;
      next.tests = next.tests.filter((fn) => {
        if (fn.OPTIONS.name === opts.name) {
          if (isExclusive)
            return false;
          if (fn.OPTIONS.test === validate.OPTIONS.test)
            return false;
        }
        return true;
      });
      next.tests.push(validate);
      return next;
    }
    when(keys, options) {
      if (!Array.isArray(keys) && typeof keys !== "string") {
        options = keys;
        keys = ".";
      }
      let next = this.clone();
      let deps = toArray(keys).map((key) => new Reference(key));
      deps.forEach((dep) => {
        if (dep.isSibling)
          next.deps.push(dep.key);
      });
      next.conditions.push(typeof options === "function" ? new Condition(deps, options) : Condition.fromOptions(deps, options));
      return next;
    }
    typeError(message) {
      let next = this.clone();
      next.internalTests.typeError = createValidation({
        message,
        name: "typeError",
        skipAbsent: true,
        test(value) {
          if (!this.schema._typeCheck(value))
            return this.createError({
              params: {
                type: this.schema.type
              }
            });
          return true;
        }
      });
      return next;
    }
    oneOf(enums, message = mixed.oneOf) {
      let next = this.clone();
      enums.forEach((val) => {
        next._whitelist.add(val);
        next._blacklist.delete(val);
      });
      next.internalTests.whiteList = createValidation({
        message,
        name: "oneOf",
        skipAbsent: true,
        test(value) {
          let valids = this.schema._whitelist;
          let resolved = valids.resolveAll(this.resolve);
          return resolved.includes(value) ? true : this.createError({
            params: {
              values: Array.from(valids).join(", "),
              resolved
            }
          });
        }
      });
      return next;
    }
    notOneOf(enums, message = mixed.notOneOf) {
      let next = this.clone();
      enums.forEach((val) => {
        next._blacklist.add(val);
        next._whitelist.delete(val);
      });
      next.internalTests.blacklist = createValidation({
        message,
        name: "notOneOf",
        test(value) {
          let invalids = this.schema._blacklist;
          let resolved = invalids.resolveAll(this.resolve);
          if (resolved.includes(value))
            return this.createError({
              params: {
                values: Array.from(invalids).join(", "),
                resolved
              }
            });
          return true;
        }
      });
      return next;
    }
    strip(strip = true) {
      let next = this.clone();
      next.spec.strip = strip;
      return next;
    }
    describe(options) {
      const next = (options ? this.resolve(options) : this).clone();
      const {
        label,
        meta,
        optional,
        nullable
      } = next.spec;
      const description = {
        meta,
        label,
        optional,
        nullable,
        default: next.getDefault(options),
        type: next.type,
        oneOf: next._whitelist.describe(),
        notOneOf: next._blacklist.describe(),
        tests: next.tests.map((fn) => ({
          name: fn.OPTIONS.name,
          params: fn.OPTIONS.params
        })).filter((n, idx, list) => list.findIndex((c) => c.name === n.name) === idx)
      };
      return description;
    }
  };
  Schema.prototype.__isYupSchema__ = true;
  for (const method of ["validate", "validateSync"])
    Schema.prototype[`${method}At`] = function(path, value, options = {}) {
      const {
        parent,
        parentPath,
        schema
      } = getIn(this, path, value, options.context);
      return schema[method](parent && parent[parentPath], Object.assign({}, options, {
        parent,
        path
      }));
    };
  for (const alias of ["equals", "is"])
    Schema.prototype[alias] = Schema.prototype.oneOf;
  for (const alias of ["not", "nope"])
    Schema.prototype[alias] = Schema.prototype.notOneOf;
  var returnsTrue = () => true;
  function create$8(spec) {
    return new MixedSchema(spec);
  }
  var MixedSchema = class extends Schema {
    constructor(spec) {
      super(typeof spec === "function" ? {
        type: "mixed",
        check: spec
      } : Object.assign({
        type: "mixed",
        check: returnsTrue
      }, spec));
    }
  };
  create$8.prototype = MixedSchema.prototype;
  function create$7() {
    return new BooleanSchema();
  }
  var BooleanSchema = class extends Schema {
    constructor() {
      super({
        type: "boolean",
        check(v) {
          if (v instanceof Boolean)
            v = v.valueOf();
          return typeof v === "boolean";
        }
      });
      this.withMutation(() => {
        this.transform((value, _raw, ctx) => {
          if (ctx.spec.coerce && !ctx.isType(value)) {
            if (/^(true|1)$/i.test(String(value)))
              return true;
            if (/^(false|0)$/i.test(String(value)))
              return false;
          }
          return value;
        });
      });
    }
    isTrue(message = boolean.isValue) {
      return this.test({
        message,
        name: "is-value",
        exclusive: true,
        params: {
          value: "true"
        },
        test(value) {
          return isAbsent(value) || value === true;
        }
      });
    }
    isFalse(message = boolean.isValue) {
      return this.test({
        message,
        name: "is-value",
        exclusive: true,
        params: {
          value: "false"
        },
        test(value) {
          return isAbsent(value) || value === false;
        }
      });
    }
    default(def) {
      return super.default(def);
    }
    defined(msg) {
      return super.defined(msg);
    }
    optional() {
      return super.optional();
    }
    required(msg) {
      return super.required(msg);
    }
    notRequired() {
      return super.notRequired();
    }
    nullable() {
      return super.nullable();
    }
    nonNullable(msg) {
      return super.nonNullable(msg);
    }
    strip(v) {
      return super.strip(v);
    }
  };
  create$7.prototype = BooleanSchema.prototype;
  var isoReg = /^(\d{4}|[+-]\d{6})(?:-?(\d{2})(?:-?(\d{2}))?)?(?:[ T]?(\d{2}):?(\d{2})(?::?(\d{2})(?:[,.](\d{1,}))?)?(?:(Z)|([+-])(\d{2})(?::?(\d{2}))?)?)?$/;
  function parseIsoDate(date2) {
    const struct = parseDateStruct(date2);
    if (!struct)
      return Date.parse ? Date.parse(date2) : Number.NaN;
    if (struct.z === void 0 && struct.plusMinus === void 0) {
      return new Date(struct.year, struct.month, struct.day, struct.hour, struct.minute, struct.second, struct.millisecond).valueOf();
    }
    let totalMinutesOffset = 0;
    if (struct.z !== "Z" && struct.plusMinus !== void 0) {
      totalMinutesOffset = struct.hourOffset * 60 + struct.minuteOffset;
      if (struct.plusMinus === "+")
        totalMinutesOffset = 0 - totalMinutesOffset;
    }
    return Date.UTC(struct.year, struct.month, struct.day, struct.hour, struct.minute + totalMinutesOffset, struct.second, struct.millisecond);
  }
  function parseDateStruct(date2) {
    var _regexResult$7$length, _regexResult$;
    const regexResult = isoReg.exec(date2);
    if (!regexResult)
      return null;
    return {
      year: toNumber(regexResult[1]),
      month: toNumber(regexResult[2], 1) - 1,
      day: toNumber(regexResult[3], 1),
      hour: toNumber(regexResult[4]),
      minute: toNumber(regexResult[5]),
      second: toNumber(regexResult[6]),
      millisecond: regexResult[7] ? toNumber(regexResult[7].substring(0, 3)) : 0,
      precision: (_regexResult$7$length = (_regexResult$ = regexResult[7]) == null ? void 0 : _regexResult$.length) != null ? _regexResult$7$length : void 0,
      z: regexResult[8] || void 0,
      plusMinus: regexResult[9] || void 0,
      hourOffset: toNumber(regexResult[10]),
      minuteOffset: toNumber(regexResult[11])
    };
  }
  function toNumber(str, defaultValue = 0) {
    return Number(str) || defaultValue;
  }
  var rEmail = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
  var rUrl = /^((https?|ftp):)?\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i;
  var rUUID = /^(?:[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}|00000000-0000-0000-0000-000000000000)$/i;
  var yearMonthDay = "^\\d{4}-\\d{2}-\\d{2}";
  var hourMinuteSecond = "\\d{2}:\\d{2}:\\d{2}";
  var zOrOffset = "(([+-]\\d{2}(:?\\d{2})?)|Z)";
  var rIsoDateTime = new RegExp(`${yearMonthDay}T${hourMinuteSecond}(\\.\\d+)?${zOrOffset}$`);
  var isTrimmed = (value) => isAbsent(value) || value === value.trim();
  var objStringTag = {}.toString();
  function create$6() {
    return new StringSchema();
  }
  var StringSchema = class extends Schema {
    constructor() {
      super({
        type: "string",
        check(value) {
          if (value instanceof String)
            value = value.valueOf();
          return typeof value === "string";
        }
      });
      this.withMutation(() => {
        this.transform((value, _raw, ctx) => {
          if (!ctx.spec.coerce || ctx.isType(value))
            return value;
          if (Array.isArray(value))
            return value;
          const strValue = value != null && value.toString ? value.toString() : value;
          if (strValue === objStringTag)
            return value;
          return strValue;
        });
      });
    }
    required(message) {
      return super.required(message).withMutation((schema) => schema.test({
        message: message || mixed.required,
        name: "required",
        skipAbsent: true,
        test: (value) => !!value.length
      }));
    }
    notRequired() {
      return super.notRequired().withMutation((schema) => {
        schema.tests = schema.tests.filter((t) => t.OPTIONS.name !== "required");
        return schema;
      });
    }
    length(length, message = string.length) {
      return this.test({
        message,
        name: "length",
        exclusive: true,
        params: {
          length
        },
        skipAbsent: true,
        test(value) {
          return value.length === this.resolve(length);
        }
      });
    }
    min(min, message = string.min) {
      return this.test({
        message,
        name: "min",
        exclusive: true,
        params: {
          min
        },
        skipAbsent: true,
        test(value) {
          return value.length >= this.resolve(min);
        }
      });
    }
    max(max, message = string.max) {
      return this.test({
        name: "max",
        exclusive: true,
        message,
        params: {
          max
        },
        skipAbsent: true,
        test(value) {
          return value.length <= this.resolve(max);
        }
      });
    }
    matches(regex, options) {
      let excludeEmptyString = false;
      let message;
      let name;
      if (options) {
        if (typeof options === "object") {
          ({
            excludeEmptyString = false,
            message,
            name
          } = options);
        } else {
          message = options;
        }
      }
      return this.test({
        name: name || "matches",
        message: message || string.matches,
        params: {
          regex
        },
        skipAbsent: true,
        test: (value) => value === "" && excludeEmptyString || value.search(regex) !== -1
      });
    }
    email(message = string.email) {
      return this.matches(rEmail, {
        name: "email",
        message,
        excludeEmptyString: true
      });
    }
    url(message = string.url) {
      return this.matches(rUrl, {
        name: "url",
        message,
        excludeEmptyString: true
      });
    }
    uuid(message = string.uuid) {
      return this.matches(rUUID, {
        name: "uuid",
        message,
        excludeEmptyString: false
      });
    }
    datetime(options) {
      let message = "";
      let allowOffset;
      let precision;
      if (options) {
        if (typeof options === "object") {
          ({
            message = "",
            allowOffset = false,
            precision = void 0
          } = options);
        } else {
          message = options;
        }
      }
      return this.matches(rIsoDateTime, {
        name: "datetime",
        message: message || string.datetime,
        excludeEmptyString: true
      }).test({
        name: "datetime_offset",
        message: message || string.datetime_offset,
        params: {
          allowOffset
        },
        skipAbsent: true,
        test: (value) => {
          if (!value || allowOffset)
            return true;
          const struct = parseDateStruct(value);
          if (!struct)
            return false;
          return !!struct.z;
        }
      }).test({
        name: "datetime_precision",
        message: message || string.datetime_precision,
        params: {
          precision
        },
        skipAbsent: true,
        test: (value) => {
          if (!value || precision == void 0)
            return true;
          const struct = parseDateStruct(value);
          if (!struct)
            return false;
          return struct.precision === precision;
        }
      });
    }
    ensure() {
      return this.default("").transform((val) => val === null ? "" : val);
    }
    trim(message = string.trim) {
      return this.transform((val) => val != null ? val.trim() : val).test({
        message,
        name: "trim",
        test: isTrimmed
      });
    }
    lowercase(message = string.lowercase) {
      return this.transform((value) => !isAbsent(value) ? value.toLowerCase() : value).test({
        message,
        name: "string_case",
        exclusive: true,
        skipAbsent: true,
        test: (value) => isAbsent(value) || value === value.toLowerCase()
      });
    }
    uppercase(message = string.uppercase) {
      return this.transform((value) => !isAbsent(value) ? value.toUpperCase() : value).test({
        message,
        name: "string_case",
        exclusive: true,
        skipAbsent: true,
        test: (value) => isAbsent(value) || value === value.toUpperCase()
      });
    }
  };
  create$6.prototype = StringSchema.prototype;
  var isNaN$1 = (value) => value != +value;
  function create$5() {
    return new NumberSchema();
  }
  var NumberSchema = class extends Schema {
    constructor() {
      super({
        type: "number",
        check(value) {
          if (value instanceof Number)
            value = value.valueOf();
          return typeof value === "number" && !isNaN$1(value);
        }
      });
      this.withMutation(() => {
        this.transform((value, _raw, ctx) => {
          if (!ctx.spec.coerce)
            return value;
          let parsed = value;
          if (typeof parsed === "string") {
            parsed = parsed.replace(/\s/g, "");
            if (parsed === "")
              return NaN;
            parsed = +parsed;
          }
          if (ctx.isType(parsed) || parsed === null)
            return parsed;
          return parseFloat(parsed);
        });
      });
    }
    min(min, message = number.min) {
      return this.test({
        message,
        name: "min",
        exclusive: true,
        params: {
          min
        },
        skipAbsent: true,
        test(value) {
          return value >= this.resolve(min);
        }
      });
    }
    max(max, message = number.max) {
      return this.test({
        message,
        name: "max",
        exclusive: true,
        params: {
          max
        },
        skipAbsent: true,
        test(value) {
          return value <= this.resolve(max);
        }
      });
    }
    lessThan(less, message = number.lessThan) {
      return this.test({
        message,
        name: "max",
        exclusive: true,
        params: {
          less
        },
        skipAbsent: true,
        test(value) {
          return value < this.resolve(less);
        }
      });
    }
    moreThan(more, message = number.moreThan) {
      return this.test({
        message,
        name: "min",
        exclusive: true,
        params: {
          more
        },
        skipAbsent: true,
        test(value) {
          return value > this.resolve(more);
        }
      });
    }
    positive(msg = number.positive) {
      return this.moreThan(0, msg);
    }
    negative(msg = number.negative) {
      return this.lessThan(0, msg);
    }
    integer(message = number.integer) {
      return this.test({
        name: "integer",
        message,
        skipAbsent: true,
        test: (val) => Number.isInteger(val)
      });
    }
    truncate() {
      return this.transform((value) => !isAbsent(value) ? value | 0 : value);
    }
    round(method) {
      var _method;
      let avail = ["ceil", "floor", "round", "trunc"];
      method = ((_method = method) == null ? void 0 : _method.toLowerCase()) || "round";
      if (method === "trunc")
        return this.truncate();
      if (avail.indexOf(method.toLowerCase()) === -1)
        throw new TypeError("Only valid options for round() are: " + avail.join(", "));
      return this.transform((value) => !isAbsent(value) ? Math[method](value) : value);
    }
  };
  create$5.prototype = NumberSchema.prototype;
  var invalidDate = new Date("");
  var isDate = (obj) => Object.prototype.toString.call(obj) === "[object Date]";
  function create$4() {
    return new DateSchema();
  }
  var DateSchema = class extends Schema {
    constructor() {
      super({
        type: "date",
        check(v) {
          return isDate(v) && !isNaN(v.getTime());
        }
      });
      this.withMutation(() => {
        this.transform((value, _raw, ctx) => {
          if (!ctx.spec.coerce || ctx.isType(value) || value === null)
            return value;
          value = parseIsoDate(value);
          return !isNaN(value) ? new Date(value) : DateSchema.INVALID_DATE;
        });
      });
    }
    prepareParam(ref, name) {
      let param;
      if (!Reference.isRef(ref)) {
        let cast = this.cast(ref);
        if (!this._typeCheck(cast))
          throw new TypeError(`\`${name}\` must be a Date or a value that can be \`cast()\` to a Date`);
        param = cast;
      } else {
        param = ref;
      }
      return param;
    }
    min(min, message = date.min) {
      let limit = this.prepareParam(min, "min");
      return this.test({
        message,
        name: "min",
        exclusive: true,
        params: {
          min
        },
        skipAbsent: true,
        test(value) {
          return value >= this.resolve(limit);
        }
      });
    }
    max(max, message = date.max) {
      let limit = this.prepareParam(max, "max");
      return this.test({
        message,
        name: "max",
        exclusive: true,
        params: {
          max
        },
        skipAbsent: true,
        test(value) {
          return value <= this.resolve(limit);
        }
      });
    }
  };
  DateSchema.INVALID_DATE = invalidDate;
  create$4.prototype = DateSchema.prototype;
  create$4.INVALID_DATE = invalidDate;
  function sortFields(fields, excludedEdges = []) {
    let edges = [];
    let nodes = new Set();
    let excludes = new Set(excludedEdges.map(([a, b]) => `${a}-${b}`));
    function addNode(depPath, key) {
      let node = (0, import_property_expr.split)(depPath)[0];
      nodes.add(node);
      if (!excludes.has(`${key}-${node}`))
        edges.push([key, node]);
    }
    for (const key of Object.keys(fields)) {
      let value = fields[key];
      nodes.add(key);
      if (Reference.isRef(value) && value.isSibling)
        addNode(value.path, key);
      else if (isSchema(value) && "deps" in value)
        value.deps.forEach((path) => addNode(path, key));
    }
    return import_toposort.default.array(Array.from(nodes), edges).reverse();
  }
  function findIndex(arr, err) {
    let idx = Infinity;
    arr.some((key, ii) => {
      var _err$path;
      if ((_err$path = err.path) != null && _err$path.includes(key)) {
        idx = ii;
        return true;
      }
    });
    return idx;
  }
  function sortByKeyOrder(keys) {
    return (a, b) => {
      return findIndex(keys, a) - findIndex(keys, b);
    };
  }
  var parseJson = (value, _, ctx) => {
    if (typeof value !== "string") {
      return value;
    }
    let parsed = value;
    try {
      parsed = JSON.parse(value);
    } catch (err) {
    }
    return ctx.isType(parsed) ? parsed : value;
  };
  function deepPartial(schema) {
    if ("fields" in schema) {
      const partial = {};
      for (const [key, fieldSchema] of Object.entries(schema.fields)) {
        partial[key] = deepPartial(fieldSchema);
      }
      return schema.setFields(partial);
    }
    if (schema.type === "array") {
      const nextArray = schema.optional();
      if (nextArray.innerType)
        nextArray.innerType = deepPartial(nextArray.innerType);
      return nextArray;
    }
    if (schema.type === "tuple") {
      return schema.optional().clone({
        types: schema.spec.types.map(deepPartial)
      });
    }
    if ("optional" in schema) {
      return schema.optional();
    }
    return schema;
  }
  var deepHas = (obj, p) => {
    const path = [...(0, import_property_expr.normalizePath)(p)];
    if (path.length === 1)
      return path[0] in obj;
    let last = path.pop();
    let parent = (0, import_property_expr.getter)((0, import_property_expr.join)(path), true)(obj);
    return !!(parent && last in parent);
  };
  var isObject2 = (obj) => Object.prototype.toString.call(obj) === "[object Object]";
  function unknown(ctx, value) {
    let known = Object.keys(ctx.fields);
    return Object.keys(value).filter((key) => known.indexOf(key) === -1);
  }
  var defaultSort = sortByKeyOrder([]);
  function create$3(spec) {
    return new ObjectSchema(spec);
  }
  var ObjectSchema = class extends Schema {
    constructor(spec) {
      super({
        type: "object",
        check(value) {
          return isObject2(value) || typeof value === "function";
        }
      });
      this.fields = Object.create(null);
      this._sortErrors = defaultSort;
      this._nodes = [];
      this._excludedEdges = [];
      this.withMutation(() => {
        if (spec) {
          this.shape(spec);
        }
      });
    }
    _cast(_value, options = {}) {
      var _options$stripUnknown;
      let value = super._cast(_value, options);
      if (value === void 0)
        return this.getDefault(options);
      if (!this._typeCheck(value))
        return value;
      let fields = this.fields;
      let strip = (_options$stripUnknown = options.stripUnknown) != null ? _options$stripUnknown : this.spec.noUnknown;
      let props = [].concat(this._nodes, Object.keys(value).filter((v) => !this._nodes.includes(v)));
      let intermediateValue = {};
      let innerOptions = Object.assign({}, options, {
        parent: intermediateValue,
        __validating: options.__validating || false
      });
      let isChanged = false;
      for (const prop of props) {
        let field = fields[prop];
        let exists = prop in value;
        if (field) {
          let fieldValue;
          let inputValue = value[prop];
          innerOptions.path = (options.path ? `${options.path}.` : "") + prop;
          field = field.resolve({
            value: inputValue,
            context: options.context,
            parent: intermediateValue
          });
          let fieldSpec = field instanceof Schema ? field.spec : void 0;
          let strict = fieldSpec == null ? void 0 : fieldSpec.strict;
          if (fieldSpec != null && fieldSpec.strip) {
            isChanged = isChanged || prop in value;
            continue;
          }
          fieldValue = !options.__validating || !strict ? field.cast(value[prop], innerOptions) : value[prop];
          if (fieldValue !== void 0) {
            intermediateValue[prop] = fieldValue;
          }
        } else if (exists && !strip) {
          intermediateValue[prop] = value[prop];
        }
        if (exists !== prop in intermediateValue || intermediateValue[prop] !== value[prop]) {
          isChanged = true;
        }
      }
      return isChanged ? intermediateValue : value;
    }
    _validate(_value, options = {}, panic, next) {
      let {
        from = [],
        originalValue = _value,
        recursive = this.spec.recursive
      } = options;
      options.from = [{
        schema: this,
        value: originalValue
      }, ...from];
      options.__validating = true;
      options.originalValue = originalValue;
      super._validate(_value, options, panic, (objectErrors, value) => {
        if (!recursive || !isObject2(value)) {
          next(objectErrors, value);
          return;
        }
        originalValue = originalValue || value;
        let tests = [];
        for (let key of this._nodes) {
          let field = this.fields[key];
          if (!field || Reference.isRef(field)) {
            continue;
          }
          tests.push(field.asNestedTest({
            options,
            key,
            parent: value,
            parentPath: options.path,
            originalParent: originalValue
          }));
        }
        this.runTests({
          tests,
          value,
          originalValue,
          options
        }, panic, (fieldErrors) => {
          next(fieldErrors.sort(this._sortErrors).concat(objectErrors), value);
        });
      });
    }
    clone(spec) {
      const next = super.clone(spec);
      next.fields = Object.assign({}, this.fields);
      next._nodes = this._nodes;
      next._excludedEdges = this._excludedEdges;
      next._sortErrors = this._sortErrors;
      return next;
    }
    concat(schema) {
      let next = super.concat(schema);
      let nextFields = next.fields;
      for (let [field, schemaOrRef] of Object.entries(this.fields)) {
        const target = nextFields[field];
        nextFields[field] = target === void 0 ? schemaOrRef : target;
      }
      return next.withMutation((s) => s.setFields(nextFields, [...this._excludedEdges, ...schema._excludedEdges]));
    }
    _getDefault(options) {
      if ("default" in this.spec) {
        return super._getDefault(options);
      }
      if (!this._nodes.length) {
        return void 0;
      }
      let dft = {};
      this._nodes.forEach((key) => {
        var _innerOptions;
        const field = this.fields[key];
        let innerOptions = options;
        if ((_innerOptions = innerOptions) != null && _innerOptions.value) {
          innerOptions = Object.assign({}, innerOptions, {
            parent: innerOptions.value,
            value: innerOptions.value[key]
          });
        }
        dft[key] = field && "getDefault" in field ? field.getDefault(innerOptions) : void 0;
      });
      return dft;
    }
    setFields(shape, excludedEdges) {
      let next = this.clone();
      next.fields = shape;
      next._nodes = sortFields(shape, excludedEdges);
      next._sortErrors = sortByKeyOrder(Object.keys(shape));
      if (excludedEdges)
        next._excludedEdges = excludedEdges;
      return next;
    }
    shape(additions, excludes = []) {
      return this.clone().withMutation((next) => {
        let edges = next._excludedEdges;
        if (excludes.length) {
          if (!Array.isArray(excludes[0]))
            excludes = [excludes];
          edges = [...next._excludedEdges, ...excludes];
        }
        return next.setFields(Object.assign(next.fields, additions), edges);
      });
    }
    partial() {
      const partial = {};
      for (const [key, schema] of Object.entries(this.fields)) {
        partial[key] = "optional" in schema && schema.optional instanceof Function ? schema.optional() : schema;
      }
      return this.setFields(partial);
    }
    deepPartial() {
      const next = deepPartial(this);
      return next;
    }
    pick(keys) {
      const picked = {};
      for (const key of keys) {
        if (this.fields[key])
          picked[key] = this.fields[key];
      }
      return this.setFields(picked, this._excludedEdges.filter(([a, b]) => keys.includes(a) && keys.includes(b)));
    }
    omit(keys) {
      const remaining = [];
      for (const key of Object.keys(this.fields)) {
        if (keys.includes(key))
          continue;
        remaining.push(key);
      }
      return this.pick(remaining);
    }
    from(from, to, alias) {
      let fromGetter = (0, import_property_expr.getter)(from, true);
      return this.transform((obj) => {
        if (!obj)
          return obj;
        let newObj = obj;
        if (deepHas(obj, from)) {
          newObj = Object.assign({}, obj);
          if (!alias)
            delete newObj[from];
          newObj[to] = fromGetter(obj);
        }
        return newObj;
      });
    }
    json() {
      return this.transform(parseJson);
    }
    noUnknown(noAllow = true, message = object.noUnknown) {
      if (typeof noAllow !== "boolean") {
        message = noAllow;
        noAllow = true;
      }
      let next = this.test({
        name: "noUnknown",
        exclusive: true,
        message,
        test(value) {
          if (value == null)
            return true;
          const unknownKeys = unknown(this.schema, value);
          return !noAllow || unknownKeys.length === 0 || this.createError({
            params: {
              unknown: unknownKeys.join(", ")
            }
          });
        }
      });
      next.spec.noUnknown = noAllow;
      return next;
    }
    unknown(allow = true, message = object.noUnknown) {
      return this.noUnknown(!allow, message);
    }
    transformKeys(fn) {
      return this.transform((obj) => {
        if (!obj)
          return obj;
        const result = {};
        for (const key of Object.keys(obj))
          result[fn(key)] = obj[key];
        return result;
      });
    }
    camelCase() {
      return this.transformKeys(import_tiny_case.camelCase);
    }
    snakeCase() {
      return this.transformKeys(import_tiny_case.snakeCase);
    }
    constantCase() {
      return this.transformKeys((key) => (0, import_tiny_case.snakeCase)(key).toUpperCase());
    }
    describe(options) {
      const next = (options ? this.resolve(options) : this).clone();
      const base = super.describe(options);
      base.fields = {};
      for (const [key, value] of Object.entries(next.fields)) {
        var _innerOptions2;
        let innerOptions = options;
        if ((_innerOptions2 = innerOptions) != null && _innerOptions2.value) {
          innerOptions = Object.assign({}, innerOptions, {
            parent: innerOptions.value,
            value: innerOptions.value[key]
          });
        }
        base.fields[key] = value.describe(innerOptions);
      }
      return base;
    }
  };
  create$3.prototype = ObjectSchema.prototype;
  function create$2(type) {
    return new ArraySchema(type);
  }
  var ArraySchema = class extends Schema {
    constructor(type) {
      super({
        type: "array",
        spec: {
          types: type
        },
        check(v) {
          return Array.isArray(v);
        }
      });
      this.innerType = void 0;
      this.innerType = type;
    }
    _cast(_value, _opts) {
      const value = super._cast(_value, _opts);
      if (!this._typeCheck(value) || !this.innerType) {
        return value;
      }
      let isChanged = false;
      const castArray = value.map((v, idx) => {
        const castElement = this.innerType.cast(v, Object.assign({}, _opts, {
          path: `${_opts.path || ""}[${idx}]`
        }));
        if (castElement !== v) {
          isChanged = true;
        }
        return castElement;
      });
      return isChanged ? castArray : value;
    }
    _validate(_value, options = {}, panic, next) {
      var _options$recursive;
      let innerType = this.innerType;
      let recursive = (_options$recursive = options.recursive) != null ? _options$recursive : this.spec.recursive;
      options.originalValue != null ? options.originalValue : _value;
      super._validate(_value, options, panic, (arrayErrors, value) => {
        var _options$originalValu2;
        if (!recursive || !innerType || !this._typeCheck(value)) {
          next(arrayErrors, value);
          return;
        }
        let tests = new Array(value.length);
        for (let index = 0; index < value.length; index++) {
          var _options$originalValu;
          tests[index] = innerType.asNestedTest({
            options,
            index,
            parent: value,
            parentPath: options.path,
            originalParent: (_options$originalValu = options.originalValue) != null ? _options$originalValu : _value
          });
        }
        this.runTests({
          value,
          tests,
          originalValue: (_options$originalValu2 = options.originalValue) != null ? _options$originalValu2 : _value,
          options
        }, panic, (innerTypeErrors) => next(innerTypeErrors.concat(arrayErrors), value));
      });
    }
    clone(spec) {
      const next = super.clone(spec);
      next.innerType = this.innerType;
      return next;
    }
    json() {
      return this.transform(parseJson);
    }
    concat(schema) {
      let next = super.concat(schema);
      next.innerType = this.innerType;
      if (schema.innerType)
        next.innerType = next.innerType ? next.innerType.concat(schema.innerType) : schema.innerType;
      return next;
    }
    of(schema) {
      let next = this.clone();
      if (!isSchema(schema))
        throw new TypeError("`array.of()` sub-schema must be a valid yup schema not: " + printValue(schema));
      next.innerType = schema;
      next.spec = Object.assign({}, next.spec, {
        types: schema
      });
      return next;
    }
    length(length, message = array.length) {
      return this.test({
        message,
        name: "length",
        exclusive: true,
        params: {
          length
        },
        skipAbsent: true,
        test(value) {
          return value.length === this.resolve(length);
        }
      });
    }
    min(min, message) {
      message = message || array.min;
      return this.test({
        message,
        name: "min",
        exclusive: true,
        params: {
          min
        },
        skipAbsent: true,
        test(value) {
          return value.length >= this.resolve(min);
        }
      });
    }
    max(max, message) {
      message = message || array.max;
      return this.test({
        message,
        name: "max",
        exclusive: true,
        params: {
          max
        },
        skipAbsent: true,
        test(value) {
          return value.length <= this.resolve(max);
        }
      });
    }
    ensure() {
      return this.default(() => []).transform((val, original) => {
        if (this._typeCheck(val))
          return val;
        return original == null ? [] : [].concat(original);
      });
    }
    compact(rejector) {
      let reject = !rejector ? (v) => !!v : (v, i, a) => !rejector(v, i, a);
      return this.transform((values) => values != null ? values.filter(reject) : values);
    }
    describe(options) {
      const next = (options ? this.resolve(options) : this).clone();
      const base = super.describe(options);
      if (next.innerType) {
        var _innerOptions;
        let innerOptions = options;
        if ((_innerOptions = innerOptions) != null && _innerOptions.value) {
          innerOptions = Object.assign({}, innerOptions, {
            parent: innerOptions.value,
            value: innerOptions.value[0]
          });
        }
        base.innerType = next.innerType.describe(innerOptions);
      }
      return base;
    }
  };
  create$2.prototype = ArraySchema.prototype;
  function create$1(schemas) {
    return new TupleSchema(schemas);
  }
  var TupleSchema = class extends Schema {
    constructor(schemas) {
      super({
        type: "tuple",
        spec: {
          types: schemas
        },
        check(v) {
          const types = this.spec.types;
          return Array.isArray(v) && v.length === types.length;
        }
      });
      this.withMutation(() => {
        this.typeError(tuple.notType);
      });
    }
    _cast(inputValue, options) {
      const {
        types
      } = this.spec;
      const value = super._cast(inputValue, options);
      if (!this._typeCheck(value)) {
        return value;
      }
      let isChanged = false;
      const castArray = types.map((type, idx) => {
        const castElement = type.cast(value[idx], Object.assign({}, options, {
          path: `${options.path || ""}[${idx}]`
        }));
        if (castElement !== value[idx])
          isChanged = true;
        return castElement;
      });
      return isChanged ? castArray : value;
    }
    _validate(_value, options = {}, panic, next) {
      let itemTypes = this.spec.types;
      super._validate(_value, options, panic, (tupleErrors, value) => {
        var _options$originalValu2;
        if (!this._typeCheck(value)) {
          next(tupleErrors, value);
          return;
        }
        let tests = [];
        for (let [index, itemSchema] of itemTypes.entries()) {
          var _options$originalValu;
          tests[index] = itemSchema.asNestedTest({
            options,
            index,
            parent: value,
            parentPath: options.path,
            originalParent: (_options$originalValu = options.originalValue) != null ? _options$originalValu : _value
          });
        }
        this.runTests({
          value,
          tests,
          originalValue: (_options$originalValu2 = options.originalValue) != null ? _options$originalValu2 : _value,
          options
        }, panic, (innerTypeErrors) => next(innerTypeErrors.concat(tupleErrors), value));
      });
    }
    describe(options) {
      const next = (options ? this.resolve(options) : this).clone();
      const base = super.describe(options);
      base.innerType = next.spec.types.map((schema, index) => {
        var _innerOptions;
        let innerOptions = options;
        if ((_innerOptions = innerOptions) != null && _innerOptions.value) {
          innerOptions = Object.assign({}, innerOptions, {
            parent: innerOptions.value,
            value: innerOptions.value[index]
          });
        }
        return schema.describe(innerOptions);
      });
      return base;
    }
  };
  create$1.prototype = TupleSchema.prototype;
  function create(builder) {
    return new Lazy(builder);
  }
  var Lazy = class {
    constructor(builder) {
      this.type = "lazy";
      this.__isYupSchema__ = true;
      this.spec = void 0;
      this._resolve = (value, options = {}) => {
        let schema = this.builder(value, options);
        if (!isSchema(schema))
          throw new TypeError("lazy() functions must return a valid schema");
        if (this.spec.optional)
          schema = schema.optional();
        return schema.resolve(options);
      };
      this.builder = builder;
      this.spec = {
        meta: void 0,
        optional: false
      };
    }
    clone(spec) {
      const next = new Lazy(this.builder);
      next.spec = Object.assign({}, this.spec, spec);
      return next;
    }
    optionality(optional) {
      const next = this.clone({
        optional
      });
      return next;
    }
    optional() {
      return this.optionality(true);
    }
    resolve(options) {
      return this._resolve(options.value, options);
    }
    cast(value, options) {
      return this._resolve(value, options).cast(value, options);
    }
    asNestedTest(config) {
      let {
        key,
        index,
        parent,
        options
      } = config;
      let value = parent[index != null ? index : key];
      return this._resolve(value, Object.assign({}, options, {
        value,
        parent
      })).asNestedTest(config);
    }
    validate(value, options) {
      return this._resolve(value, options).validate(value, options);
    }
    validateSync(value, options) {
      return this._resolve(value, options).validateSync(value, options);
    }
    validateAt(path, value, options) {
      return this._resolve(value, options).validateAt(path, value, options);
    }
    validateSyncAt(path, value, options) {
      return this._resolve(value, options).validateSyncAt(path, value, options);
    }
    isValid(value, options) {
      return this._resolve(value, options).isValid(value, options);
    }
    isValidSync(value, options) {
      return this._resolve(value, options).isValidSync(value, options);
    }
    describe(options) {
      return options ? this.resolve(options).describe(options) : {
        type: "lazy",
        meta: this.spec.meta,
        label: void 0
      };
    }
    meta(...args) {
      if (args.length === 0)
        return this.spec.meta;
      let next = this.clone();
      next.spec.meta = Object.assign(next.spec.meta || {}, args[0]);
      return next;
    }
  };
  function setLocale(custom) {
    Object.keys(custom).forEach((type) => {
      Object.keys(custom[type]).forEach((method) => {
        locale[type][method] = custom[type][method];
      });
    });
  }
  function addMethod(schemaType, name, fn) {
    if (!schemaType || !isSchema(schemaType.prototype))
      throw new TypeError("You must provide a yup schema constructor function");
    if (typeof name !== "string")
      throw new TypeError("A Method name must be provided");
    if (typeof fn !== "function")
      throw new TypeError("Method function must be provided");
    schemaType.prototype[name] = fn;
  }

  // assets/js/alpine/stores/toast.js
  var toast_default = {
    toasts: [],
    addToast(message, type = "default", duration = 4e3, fadeDuration = 300) {
      const id = Date.now() + Math.random();
      this.toasts.push({ id, message, type, show: false });
      setTimeout(() => {
        this.updateToastVisibility(id, true);
      }, 100);
      setTimeout(() => {
        this.updateToastVisibility(id, false);
      }, duration);
      setTimeout(() => {
      }, duration + fadeDuration);
    },
    updateToastVisibility(id, visibility) {
      const toast = this.toasts.find((toast2) => toast2.id === id);
      if (toast) {
        toast.show = visibility;
      }
    },
    removeToast(id) {
      this.toasts = this.toasts.filter((toast) => toast.id !== id);
    }
  };

  // assets/js/alpine/stores/popup.js
  var popup_default = {
    open: false,
    content: ``,
    openPopup(content) {
      this.content = content;
      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          this.open = true;
        });
      });
    },
    closePopup() {
      this.open = false;
    }
  };

  // assets/js/alpine/stores/loader.js
  var loader_default = {
    isLoading: false,
    show() {
      this.isLoading = true;
    },
    hide() {
      this.isLoading = false;
    }
  };

  // assets/js/alpine/stores/wishlist.js
  var wishlist_default = {
    items: [],
    init() {
      const { addItemNonce, removeItemNonce, isLoggedIn } = window.wishlistData || {};
      this._addItemNonce = addItemNonce;
      this._removeItemNonce = removeItemNonce;
      window.addEventListener("handle_login_success", () => {
        sessionStorage.removeItem("wishlist");
      });
      if (!this.isUserLoggedIn())
        return;
      if (sessionStorage.getItem("wishlist")) {
        this.items = JSON.parse(sessionStorage.getItem("wishlist"));
      } else {
        this.fetchWishlist();
      }
      Alpine.effect(() => {
        sessionStorage.setItem("wishlist", JSON.stringify(this.items));
      });
    },
    addItem(id) {
      if (!this.isUserLoggedIn()) {
        Alpine.store("popup").openPopup(document.getElementById("login-form").innerHTML);
        return;
      }
      if (!this.items.includes(id)) {
        this.items.push(id);
        this.syncWithServer(id, "add");
      }
    },
    removeItem(id) {
      if (!this.isUserLoggedIn()) {
        Alpine.store("popup").openPopup(document.getElementById("login-form").innerHTML);
        return;
      }
      this.items = this.items.filter((itemId) => itemId !== id);
      this.syncWithServer(id, "remove");
    },
    fetchWishlist() {
      wp.ajax.send("get_wishlist", {
        success: (data2) => {
          this.items = Object.values(data2);
        },
        error: function(error2) {
          console.log("Error:", error2);
        }
      });
    },
    syncWithServer(id, action) {
      const wp_action = action === "add" ? "wishlist_add_item" : "wishlist_remove_item";
      const params = action === "add" ? {
        add_to_wishlist: id,
        nonce: this._addItemNonce
      } : {
        remove_from_wishlist: id,
        nonce: this._removeItemNonce
      };
      wp.ajax.post(wp_action, params).done((data2) => {
        this.items = Object.values(data2);
        if (action === "add") {
          Alpine.store("toast").addToast("Item added to your wishlist");
        } else if (action === "remove") {
          Alpine.store("toast").addToast("Item removed from your wishlist");
        }
      }).fail((error2) => {
        console.error("Error:", error2);
        if (action === "add") {
          this.items = this.items.filter((i) => i.id !== id);
        } else if (action === "remove") {
          this.items.push(id);
        }
      });
    },
    isUserLoggedIn() {
      return document.body.classList.contains("logged-in");
    }
  };

  // assets/js/alpine/stores/userAddress.js
  var MAX_ADDRESSES = 9;
  var DEFAULT_COUNTRY = "United States";
  var AJAX_ACTION = "save-address";
  var AUTOCOMPLETE_INIT_DELAY = 150;
  var GOOGLE_API_RETRY_DELAY = 500;
  var GOOGLE_API_MAX_RETRIES = 10;
  var DOM_RETRY_DELAY = 150;
  var DOM_MAX_RETRIES = 10;
  var POPUP_SELECTOR = "#popup-container";
  var AUTOCOMPLETE_WRAPPER_SELECTOR = ".address-autocomplete-wrapper";
  var ADDRESS_COMPONENT_MAP = {
    street_number: { field: "address", prefix: true },
    route: { field: "address", append: true },
    locality: { field: "city" },
    administrative_area_level_1: { field: "region", shortName: true },
    postal_code: { field: "postalCode" }
  };
  var userAddress_default = {
    addresses: [],
    countries: {},
    ajaxUrl: "",
    nonce: "",
    stopAdd: false,
    editAddress: null,
    isEditing: false,
    form: {
      title: "",
      buttonSaveLabel: "",
      action: ""
    },
    _inited: false,
    _autocompleteInstance: null,
    _googleApiRetries: 0,
    _domRetries: 0,
    init() {
      if (this._inited)
        return;
      const data2 = window.scriptData || {};
      this.addresses = Array.isArray(data2.addresses) ? data2.addresses : [];
      this.countries = data2.countries || {};
      this.ajaxUrl = data2.ajaxUrl || window.ajaxurl || "/wp-admin/admin-ajax.php";
      this.nonce = data2.nonce || "";
      this._inited = true;
      this.ensureOneDefault();
      this.checkMaxAddress();
    },
    ensureOneDefault() {
      if (this.addresses.length === 0)
        return;
      const hasDefault = this.addresses.some((addr) => this._isDefault(addr));
      if (!hasDefault) {
        this.addresses[0].default = true;
      }
    },
    checkMaxAddress() {
      this.stopAdd = this.addresses.length >= MAX_ADDRESSES;
    },
    _normalizeId(id) {
      return String(id);
    },
    _isDefault(address) {
      return address.default === true || address.default === 1 || address.default === "1";
    },
    _getEmptyAddress(id = null) {
      return {
        id: id || this.generateUniqueKey(),
        fname: "",
        lname: "",
        phone: "",
        address: "",
        address2: "",
        city: "",
        region: "",
        postalCode: "",
        country: DEFAULT_COUNTRY,
        default: false
      };
    },
    _setFormMode(action, title, buttonLabel) {
      this.form.action = action;
      this.form.title = title;
      this.form.buttonSaveLabel = buttonLabel;
    },
    _findAddressById(id) {
      const idStr = this._normalizeId(id);
      return this.addresses.find((addr) => this._normalizeId(addr.id) === idStr);
    },
    _findAddressIndex(id) {
      const idStr = this._normalizeId(id);
      return this.addresses.findIndex((addr) => this._normalizeId(addr.id) === idStr);
    },
    _updateDefaultFlags(addresses, defaultId) {
      const defaultIdStr = this._normalizeId(defaultId);
      addresses.forEach((addr) => {
        addr.default = this._normalizeId(addr.id) === defaultIdStr;
      });
    },
    generateUniqueKey() {
      const now = Date.now().toString();
      const random = Math.random().toString(36).substring(2, 15);
      return now + random;
    },
    startAdd() {
      this._setFormMode("add", "Add Address", "Add Address");
      this.editAddress = this._getEmptyAddress();
      this.isEditing = true;
      this._cleanupAutocomplete();
      this._googleApiRetries = 0;
      this._domRetries = 0;
      setTimeout(() => this.initAutocomplete(), AUTOCOMPLETE_INIT_DELAY);
    },
    startEdit(id) {
      this._setFormMode("edit", "Edit Address", "Update Address");
      const address = this._findAddressById(id);
      if (address) {
        this.editAddress = { ...address };
        this.isEditing = true;
        this._cleanupAutocomplete();
        this._googleApiRetries = 0;
        this._domRetries = 0;
        setTimeout(() => this.initAutocomplete(), AUTOCOMPLETE_INIT_DELAY);
      }
    },
    async save() {
      const addresses = [...this.addresses];
      const editId = this._normalizeId(this.editAddress.id);
      if (this.form.action === "edit") {
        const index = this._findAddressIndex(this.editAddress.id);
        if (index !== -1) {
          addresses[index] = { ...this.editAddress };
        }
      } else if (this.form.action === "add") {
        addresses.push({ ...this.editAddress });
      }
      if (this.editAddress.default) {
        this._updateDefaultFlags(addresses, editId);
      }
      await this.ajaxRequest(AJAX_ACTION, addresses);
      this.addresses = addresses;
      this.isEditing = false;
      this.checkMaxAddress();
    },
    async setDefault(id, syncToServer = false) {
      this._updateDefaultFlags(this.addresses, id);
      if (syncToServer) {
        await this.ajaxRequest(AJAX_ACTION, this.addresses);
      }
    },
    async remove(id) {
      const idStr = this._normalizeId(id);
      const addresses = this.addresses.filter((addr) => this._normalizeId(addr.id) !== idStr);
      await this.ajaxRequest(AJAX_ACTION, addresses);
      this.addresses = addresses;
      this.isEditing = false;
      Alpine.store("popup").closePopup();
      this.checkMaxAddress();
    },
    async delete(id) {
      return this.remove(id);
    },
    async _sendRequest(action, data2) {
      const response = await fetch(this.ajaxUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({
          action,
          nonce: this.nonce,
          data: JSON.stringify(data2)
        })
      });
      return await response.json();
    },
    async ajaxRequest(action, data2, options = {}) {
      const {
        showLoader = true,
        showToast = true,
        closePopup = true
      } = options;
      if (showLoader)
        Alpine.store("loader").show();
      try {
        const result = await this._sendRequest(action, data2);
        if (showLoader)
          Alpine.store("loader").hide();
        if (result.success) {
          if (showToast)
            Alpine.store("toast").addToast(result.data, "success");
          if (closePopup) {
            this.editAddress = this._getEmptyAddress();
            Alpine.store("popup").closePopup();
          }
          return result;
        } else {
          if (showToast)
            Alpine.store("toast").addToast(result.data, "error");
          throw new Error(result.data);
        }
      } catch (error2) {
        if (showLoader)
          Alpine.store("loader").hide();
        if (showToast)
          Alpine.store("toast").addToast("An error occurred", "error");
        console.error("AJAX error:", error2);
        throw error2;
      }
    },
    formatUSPhoneNumber() {
      let phone = this.editAddress?.phone || "";
      phone = phone.replace(/\D/g, "");
      if (phone.length > 10)
        phone = phone.slice(0, 10);
      if (phone.length >= 6) {
        this.editAddress.phone = `(${phone.slice(0, 3)}) ${phone.slice(3, 6)}-${phone.slice(6)}`;
      } else if (phone.length >= 3) {
        this.editAddress.phone = `(${phone.slice(0, 3)}) ${phone.slice(3)}`;
      } else {
        this.editAddress.phone = phone;
      }
    },
    async initAutocomplete() {
      this._cleanupAutocomplete();
      if (typeof google === "undefined" || !google.maps) {
        if (this._googleApiRetries < GOOGLE_API_MAX_RETRIES) {
          this._googleApiRetries++;
          setTimeout(() => this.initAutocomplete(), GOOGLE_API_RETRY_DELAY);
        } else {
          console.error("Google Maps API failed to load after max retries");
        }
        return;
      }
      const popup = document.querySelector(POPUP_SELECTOR);
      const wrapper = popup?.querySelector(AUTOCOMPLETE_WRAPPER_SELECTOR);
      if (!wrapper) {
        if (this._domRetries < DOM_MAX_RETRIES) {
          this._domRetries++;
          setTimeout(() => this.initAutocomplete(), DOM_RETRY_DELAY);
          return;
        }
        console.warn("Autocomplete wrapper not found in popup");
        return;
      }
      try {
        const { PlaceAutocompleteElement } = await google.maps.importLibrary("places");
        const placeAutocomplete = new PlaceAutocompleteElement({
          placeholder: "Start typing your address..."
        });
        placeAutocomplete.includedRegionCodes = ["us"];
        placeAutocomplete.addEventListener("gmp-select", async ({ placePrediction }) => {
          const place = placePrediction.toPlace();
          await this._handlePlaceSelectNew(place);
        });
        wrapper.innerHTML = "";
        wrapper.appendChild(placeAutocomplete);
        this._autocompleteInstance = placeAutocomplete;
        this._googleApiRetries = 0;
        this._domRetries = 0;
      } catch (err) {
        console.error("Places API autocomplete init error:", err);
      }
    },
    async _handlePlaceSelectNew(place) {
      if (!place || !this.editAddress) {
        console.warn("Invalid place or editAddress is null");
        return;
      }
      this._resetAddressFields();
      try {
        await place.fetchFields({ fields: ["addressComponents", "formattedAddress"] });
      } catch (e) {
        console.error("Failed to fetch place fields:", e);
        Alpine.store("toast")?.addToast("Failed to load address details", "error");
        return;
      }
      if (place.addressComponents?.length) {
        this._parseAddressComponentsNew(place.addressComponents);
      } else if (place.formattedAddress) {
        this.editAddress.address = place.formattedAddress;
      }
    },
    _parseAddressComponentsNew(components) {
      if (!this.editAddress)
        return;
      components.forEach((component) => {
        if (!component.types?.length)
          return;
        component.types.forEach((type) => {
          const mapping = ADDRESS_COMPONENT_MAP[type];
          if (!mapping)
            return;
          const value = mapping.shortName ? component.shortText || "" : component.longText || "";
          if (!value)
            return;
          if (mapping.append) {
            this.editAddress[mapping.field] += value;
          } else if (mapping.prefix) {
            this.editAddress[mapping.field] = value + " ";
          } else {
            this.editAddress[mapping.field] = value;
          }
        });
      });
      if (this.editAddress.address) {
        this.editAddress.address = this.editAddress.address.trim();
      }
    },
    _cleanupAutocomplete() {
      if (!this._autocompleteInstance)
        return;
      try {
        const popup = document.querySelector(POPUP_SELECTOR);
        const wrapper = popup?.querySelector(AUTOCOMPLETE_WRAPPER_SELECTOR);
        if (wrapper && this._autocompleteInstance.parentNode === wrapper) {
          wrapper.removeChild(this._autocompleteInstance);
        }
      } catch (e) {
        console.warn("Autocomplete cleanup error:", e);
      }
      this._autocompleteInstance = null;
    },
    _resetAddressFields() {
      if (!this.editAddress)
        return;
      this.editAddress.address = "";
      this.editAddress.city = "";
      this.editAddress.region = "";
      this.editAddress.postalCode = "";
    }
  };

  // assets/js/alpine/stores/index.js
  function registerStores() {
    Alpine.store("toast", toast_default);
    Alpine.store("popup", popup_default);
    Alpine.store("loader", loader_default);
    Alpine.store("wishlist", wishlist_default);
    Alpine.store("userAddress", userAddress_default);
  }

  // assets/js/alpine/directives/validate.js
  function registerValidationDirectives() {
    Alpine.directive("validate-field", (el, { expression }, { evaluateLater: evaluateLater2, effect: effect3 }) => {
      let getValidator = evaluateLater2(expression);
      effect3(() => {
        getValidator((errors) => {
          const { message, touched } = errors;
          if (touched && message) {
            el.classList.add("error");
          } else {
            el.classList.remove("error");
          }
        });
      });
    });
    Alpine.directive("password-eye", (el, { expression }, { evaluateLater: evaluateLater2, effect: effect3 }) => {
      let getValidator = evaluateLater2(expression);
      effect3(() => {
        getValidator((showPassword) => {
          if (showPassword) {
            el.innerHTML = `<svg class="cursor-pointer size-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>`;
          } else {
            el.innerHTML = `<svg class="cursor-pointer size-10"  xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>`;
          }
        });
      });
    });
    Alpine.directive("validate-icon", (el, { expression }, { evaluateLater: evaluateLater2, effect: effect3 }) => {
      let getValidator = evaluateLater2(expression);
      el.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="close w-10 h-10 text-red-600 bg-white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="tick w-10 h-10">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>`;
      const closeEl = el.querySelector(".close");
      const tickEl = el.querySelector(".tick");
      effect3(() => {
        getValidator((errors) => {
          const { message, touched } = errors;
          if (message) {
            closeEl.style.display = "block";
          } else {
            closeEl.style.display = "none";
          }
          if (touched && !message) {
            tickEl.style.display = "block";
          } else {
            tickEl.style.display = "none";
          }
        });
      });
    });
    Alpine.directive("validate-message", (el, { expression }, { evaluateLater: evaluateLater2, effect: effect3 }) => {
      let getValidator = evaluateLater2(expression);
      effect3(() => {
        getValidator((errors) => {
          const { message, touched } = errors;
          if (message) {
            el.innerHTML = message;
          } else {
            el.innerHTML = "";
          }
          if (touched && message) {
            el.style.display = "block";
          } else {
            el.style.display = "none";
          }
        });
      });
    });
  }

  // assets/js/BaseFormHandler.js
  var BaseFormHandler = class {
    constructor(formData, additionalData = {}) {
      this.formData = formData;
      this.additionalData = additionalData;
      this.errors = {};
      this.touched = {};
      this.notice = "";
      this.isFormSubmitting = false;
    }
    async validateForm(skipFields = []) {
      const fields = Object.keys(this.formData).filter((field) => !skipFields.includes(field));
      for (const field of fields) {
        await this.validateField(field);
      }
    }
    async validateField(field) {
      const yup = window.yup;
      const schema = this.getValidationSchema();
      try {
        await schema.validateAt(field, this.formData);
        delete this.errors[field];
      } catch (error2) {
        this.errors[field] = error2.message;
      }
      this.touched[field] = true;
    }
    getValidationSchema() {
      throw new Error("getValidationSchema method should be implemented in the subclass");
    }
    async handleSubmit(skipFields) {
      await this.validateForm(skipFields);
      if (Object.keys(this.errors).length > 0) {
        this.isFormSubmitting = false;
        return;
      }
      this.isFormSubmitting = true;
      window.wp.ajax.post(this.getApiEndpoint(), {
        ...this.formData,
        ...this.additionalData
      }).done((response) => {
        this.isFormSubmitting = false;
        this.notice = response.message;
        this.done(response);
        const event = new CustomEvent(`${this.getApiEndpoint()}_success`);
        window.dispatchEvent(event);
      }).fail((error2) => {
        this.notice = error2.message;
        this.isFormSubmitting = false;
      });
    }
  };

  // assets/js/handlers/LoginHandler.js
  var LoginHandler = class extends BaseFormHandler {
    getValidationSchema() {
      return window.yup.object().shape({
        password: window.yup.string().required("This field is required.").min(8, "Your password isn't valid."),
        email: window.yup.string().email("Your email address isn't valid.").required("This field is required.")
      });
    }
    done() {
      window.location.reload();
    }
    getApiEndpoint() {
      return "handle_login";
    }
  };

  // assets/js/alpine/components/forms/login.js
  var login_default = () => ({
    formData: {
      email: "",
      password: "",
      rememberme: false
    },
    isFormSubmitting: false,
    allowSubmit: false,
    errors: {},
    touched: {},
    woocommerceLoginNonce: window.authenicationData?.wooLoginNonce || "",
    notice: "",
    handler: null,
    init() {
      this.handler = new LoginHandler(this.formData, {
        woocommerceLoginNonce: this.woocommerceLoginNonce
      });
      this.$watch("handler.isFormSubmitting", (value) => this.isFormSubmitting = value);
      this.$watch("handler.errors", (value) => this.errors = value);
      this.$watch("handler.touched", (value) => this.touched = value);
      this.$watch("handler.notice", (value) => this.notice = value);
    },
    async handleSubmit() {
      await this.handler.handleSubmit(["rememberme"]);
    }
  });

  // assets/js/alpine/components/forms/signup.js
  var signup_default = () => ({
    formData: {
      firstName: "",
      lastName: "",
      email: "",
      password: "",
      agreeTOS: false,
      receiveOfferNews: false
    },
    messages: { message: 0 },
    isFormSubmitting: false,
    notice: "",
    errors: {},
    touched: {},
    signupNonce: window.authenicationData?.signupNonce || "",
    passedRequirements: [],
    passwordRequirements: {
      minLength: { regex: /.{8,}/, message: "At least 8 characters", code: "ERR_PASSWORD_MINLENGTH" },
      uppercase: { regex: /(?=.*[A-Z])/, message: "1 uppercase letter", code: "ERR_PASSWORD_UPPERCASE" },
      number: { regex: /(?=.*[0-9])/, message: "1 number", code: "ERR_PASSWORD_NUMBER" },
      lowercase: { regex: /(?=.*[a-z])/, message: "1 lowercase letter", code: "ERR_PASSWORD_LOWERCASE" }
    },
    async handleSubmit() {
      const token = await grecaptcha.execute(window.authenicationData.captchaSiteKey, { action: "signup" });
      await this.validateForm();
      if (Object.keys(this.errors).length > 0) {
        return;
      }
      this.isFormSubmitting = true;
      window.wp.ajax.post("handle_signup", {
        firstName: this.formData.firstName,
        lastName: this.formData.lastName,
        email: this.formData.email,
        password: this.formData.password,
        agreeTOS: this.formData.agreeTOS,
        receiveOfferNews: this.formData.receiveOfferNews,
        signupNonce: this.signupNonce,
        captchaToken: token
      }).done((response) => {
        console.log("Success:", response);
        this.notice = response.message;
        this.isFormSubmitting = false;
        window.location.reload();
      }).fail((error2) => {
        this.notice = error2.message;
        this.isFormSubmitting = false;
        console.error("Error:", error2);
      });
    },
    async validateForm() {
      const fields = Object.keys(this.formData);
      for (const field of fields) {
        if (field === "receiveOfferNews")
          continue;
        await this.validateField(field);
      }
    },
    async validateField(field) {
      const yup = window.yup;
      const schema = yup.object().shape({
        firstName: yup.string().required("This field is required.").matches(/^[A-Za-z]+$/, "Your name isn't valid."),
        lastName: yup.string().required("This field is required.").matches(/^[A-Za-z]+$/, "Your name isn't valid."),
        email: yup.string().email("Your email address isn't valid.").required("This field is required."),
        agreeTOS: yup.boolean().required("You must accept the Terms of Service.").oneOf([true], "You must accept the Terms of Service."),
        password: yup.string().required("This field is required.").test("password-complexity", "Your password does not meet the requirements.", (value) => {
          let passedRequirements = [];
          Object.entries(this.passwordRequirements).forEach(([key, requirement]) => {
            if (requirement.regex.test(value)) {
              passedRequirements.push(requirement.code);
            }
          });
          this.passedRequirements = passedRequirements;
          return passedRequirements.length === Object.keys(this.passwordRequirements).length;
        })
      });
      try {
        await schema.validateAt(field, this.formData);
        delete this.errors[field];
      } catch (error2) {
        this.errors[field] = error2.message;
      }
      this.touched[field] = true;
    }
  });

  // assets/js/alpine/components/forms/updateAccount.js
  var updateAccount_default = () => {
    const userData = window.accountData || {};
    const nonce = window.saveAccountDetailsNonce || "";
    const ajaxUrl = window.ajaxurl || "/wp-admin/admin-ajax.php";
    return {
      firstName: userData.firstName || "",
      lastName: userData.lastName || "",
      email: userData.email || "",
      password: "*********",
      saveAccountDetailsNonce: nonce,
      allowSubmit: false,
      errors: {},
      async handleSubmit() {
        await this.validateForm();
        if (Object.keys(this.errors).length > 0)
          return;
        this.ajaxSaveAccountDetails();
      },
      setAllowSubmit() {
        this.allowSubmit = true;
      },
      ajaxSaveAccountDetails() {
        this.$store.loader.show();
        const data2 = {
          action: "save_account_details",
          nonce: this.saveAccountDetailsNonce,
          firstName: this.firstName,
          lastName: this.lastName,
          email: this.email
        };
        fetch(ajaxUrl, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded"
          },
          body: new URLSearchParams(data2)
        }).then((response) => response.json()).then((data3) => {
          this.$store.loader.hide();
          if (data3.success) {
            this.$store.toast.addToast(data3.data, "success");
          } else {
            this.$store.toast.addToast("Error updating account details", "error");
          }
        }).catch((error2) => {
          console.error("Error:", error2);
          this.$store.loader.hide();
          this.$store.toast.addToast("AJAX request failed", "error");
        });
      },
      async validateForm() {
        const yup = window.yup;
        const schema = yup.object().shape({
          firstName: yup.string().required("First name is required."),
          lastName: yup.string().required("Last name is required."),
          email: yup.string().email("Invalid email address.").required("Email is required.")
        });
        this.errors = {};
        try {
          await schema.validate({
            firstName: this.firstName,
            lastName: this.lastName,
            email: this.email
          }, { abortEarly: false });
          this.allowSubmit = true;
          return true;
        } catch (err) {
          err.inner.forEach((error2) => {
            this.errors[error2.path] = error2.message;
          });
          this.allowSubmit = false;
          return false;
        }
      }
    };
  };

  // assets/js/alpine/components/forms/passwordChangeForm.js
  var passwordChangeForm_default = () => {
    const nonce = window.changePasswordNonce || "";
    const ajaxUrl = window.ajaxurl || "/wp-admin/admin-ajax.php";
    return {
      currentPassword: "",
      newPassword: "",
      confirmPassword: "",
      keepSignedIn: true,
      changePasswordNonce: nonce,
      errors: {},
      async handleSubmit() {
        await this.validateForm();
        if (Object.keys(this.errors).length > 0)
          return;
        this.$store.loader.show();
        const data2 = {
          action: "change_password",
          nonce: this.changePasswordNonce,
          currentPassword: this.currentPassword,
          pass1: this.newPassword,
          pass2: this.confirmPassword,
          keepSignedIn: this.keepSignedIn
        };
        fetch(ajaxUrl, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded"
          },
          body: new URLSearchParams(data2)
        }).then((response) => response.json()).then((data3) => {
          this.$store.loader.hide();
          if (data3.success) {
            this.$store.toast.addToast(data3.data, "success");
            this.$store.popup.closePopup();
          } else {
            this.$store.toast.addToast(data3.data, "error");
          }
        }).catch((error2) => {
          console.log("Error:", error2);
          this.$store.loader.hide();
          this.$store.toast.addToast("AJAX request failed", "error");
        });
      },
      async validateForm() {
        const yup = window.yup;
        const schema = yup.object().shape({
          currentPassword: yup.string().required("The current password is required."),
          newPassword: yup.string().min(8, "The new password must be at least 8 characters long.").required("The new password is required."),
          confirmPassword: yup.string().oneOf([yup.ref("newPassword"), null], "Passwords must match.").required("Confirming your new password is required.")
        });
        this.errors = {};
        try {
          await schema.validate({
            currentPassword: this.currentPassword,
            newPassword: this.newPassword,
            confirmPassword: this.confirmPassword
          }, { abortEarly: false });
          return true;
        } catch (err) {
          err.inner.forEach((error2) => {
            this.errors[error2.path] = error2.message;
          });
          return false;
        }
      }
    };
  };

  // assets/js/alpine/components/forms/index.js
  function registerFormComponents() {
    Alpine.data("login", login_default);
    Alpine.data("signup", signup_default);
    Alpine.data("updateAccount", updateAccount_default);
    Alpine.data("passwordChangeForm", passwordChangeForm_default);
  }

  // assets/js/alpine/components/account/index.js
  function registerAccountComponents() {
  }

  // assets/js/alpine/init.js
  window.Alpine = module_default;
  window.yup = index_esm_exports;
  registerStores();
  registerValidationDirectives();
  registerFormComponents();
  registerAccountComponents();
  if (window.location.hostname === "localhost" || window.location.hostname.includes("local")) {
    console.log("\u2705 Alpine.js initialization complete");
    console.log("\u{1F4E6} Stores:", Object.keys(module_default.store));
    console.log("\u{1F3A8} Components:", Object.keys(module_default._data || {}));
  }
  module_default.start();
})();
