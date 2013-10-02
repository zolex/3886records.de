(function(window) {

  var caughtError = false;
  var CanvasImage = function(canvas, image) {

    var width, height;

    this.image = image;
    width = this.image.width;
    height = this.image.height;

    this.element = canvas || document.createElement('canvas');
    this.element.style.width = width+'px';
    this.element.style.height = height+'px';
    this.element.width = width;
    this.element.height = height;
    this.context = this.element.getContext('2d');
    this.context.drawImage(this.image, 0, 0);

  };

  CanvasImage.prototype = {
    blur: function (strength) {
      this.context.globalAlpha = 0.5;
      for (var y = -strength; y <= strength; y += 2) {
        for (var x = -strength; x <= strength; x += 2) {
          this.context.drawImage(this.element, x, y);
          if (x>=0 && y>=0) {
            this.context.drawImage(this.element, -(x-1), -(y-1));
          }
        }
      }
      this.context.globalAlpha = 1.0;
    }
  };

  function imageMask(imageSrc, maskSrc, canvasWidth, canvasHeight, useRepeat, oncomplete) {

    var images = [
      new Image(),
      new Image()
    ];

    var canvas = [
      document.createElement('canvas'),
      document.createElement('canvas')
    ];

    var loadCount = 0;
    var loadTarget = images.length;

    function applyMask() {

      var srcWidth = canvasWidth;
      var srcHeight = canvasHeight;
      var targetWidth = canvasWidth;
      var targetHeight = canvasHeight;
      var ctx;
      var repeatPattern;

      canvas[0].width = srcWidth;
      canvas[0].height = srcHeight;

      canvas[1].width = targetWidth;
      canvas[1].height = targetHeight;

      ctx = [
        canvas[0].getContext('2d'),
        canvas[1].getContext('2d')
      ];

      if (!useRepeat) {

        ctx[0].drawImage(images[0], 0, 0);

      } else {

        repeatPattern = ctx[0].createPattern(images[0], 'repeat');
        ctx[0].fillStyle = repeatPattern;
        ctx[0].fillRect(0, 0, targetWidth, targetHeight);
        ctx[0].fillStyle = '';
      }

      ctx[1].drawImage(images[1], 0, 0);
      ctx[0].globalCompositeOperation = 'destination-in';
      ctx[0].drawImage(canvas[1], 0, 0);
      ctx[0].globalCompositeOperation = null;

      return canvas[0];
    }

    function imageLoaded() {

      this.onload = this.onerror = null;
      loadCount++;

      if (loadCount >= loadTarget) {

        try {

          console.log(imageSrc);
          oncomplete(applyMask().toDataURL('image/png'));

        } catch(e) {

          if (typeof console !== 'undefined' && typeof console.warn !== 'undefined') {
            console.warn('Unable to apply image mask, likely a security exception from offline (file://) use.', e);
            if (!caughtError && typeof console.info !== 'undefined') {
              console.info('Using static skin image as a workaround.');
            }
          }

          if (!caughtError) {

            oncomplete('image/ma-r90-body-skin.png');
            caughtError = true;

          } else {

            oncomplete();
          }
        }

        canvas = null;
        images = null;
      }
    }

    function init() {

      images[0].onload = images[0].onerror = imageLoaded;
      images[1].onload = images[1].onerror = imageLoaded;

      images[0].src = imageSrc;
      images[1].src = maskSrc;
    }

    init();

  }

  function TapeUI(oOptions) {

    var css = {

      playing: 'playing',
      stopped: 'stopped',
      ready: 'ready',
      dropin: 'dropin'
    };

    var data = {

      progress: 0
    };

    var controlHandler;
    var sound;
    var dom = {};
    var spoolWidth = 91;
    var borderMaxWidth = 76;
    var reelBoxWidth = 234;
    var tapeWidth = 480;
    var tapeHeight = parseInt(tapeWidth/1.6, 10);
    var blurCanvas;
    var maskCanvas;
    var maskCanvasLoaded;
    var maskContext;
    var maskImage;
    var maskVisible;
    var context;

    function getBackgroundURL(node) {

      var url;
      var cssprop = 'backgroundImage';

      if (node.currentStyle) {

        url = node.currentStyle[cssprop];

      } else if (document.defaultView && document.defaultView.getComputedStyle) {

        url = document.defaultView.getComputedStyle(node, '')[cssprop];

      } else {

        url = node.style[cssprop];

      }

      url = url.replace('url(', '').replace(')', '');
      url = url.replace(/\'/g, '').replace(/\"/g, '');

      return url;

    }

    function maskedImageReady(node, uri) {

      if (node && uri) {
        node.style.backgroundImage = uri;
      }

    }

    function createMaskedImage(node) {

      var imageSrc = getBackgroundURL(node),
          elementWidth = node.offsetWidth,
          elementHeight = node.offsetHeight,
          useRepeat = !!node.getAttribute('data-image-repeat'),
          maskSrc = node.getAttribute('data-mask-url');

      return imageMask(imageSrc, maskSrc, elementWidth, elementHeight, useRepeat, function(maskURI) {
        var uri = (maskURI ? 'url(' + maskURI + ')' : null);
        maskedImageReady(node, uri);
      });

    }

    function scaleWidth(w) {
      return (w * tapeWidth);
    }

    function scaleHeight(h) {
      return (h * tapeHeight);
    }

    function initMask(callback) {

      if (!dom.node.className.match(/cutout/i)) {
        return false;
      }

      maskCanvas = document.createElement('canvas');
      maskImage = new Image();
      maskCanvas.width = tapeWidth;
      maskCanvas.height = tapeHeight;

      maskImage.onload = function() {
        var ctx = maskCanvas.getContext('2d');
        ctx.drawImage(this, 0, 0);
        this.onload = null;
        maskCanvasLoaded = true;
        callback();
      };

      maskImage.src = '/img/ma-r90-mask.png';

    }

    function createBlurImage(callback) {

      var url = getBackgroundURL(document.getElementsByTagName('html')[0]);
      var image = new Image();
      image.onload = image.onerror = function() {

        if (this.width || this.height) {

          var canvasImage = new CanvasImage(dom.blurNode, this);
          dom.blurNode.style.backgroundImage = '';
          canvasImage.blur(2);
        }

        this.onload = this.onerror = null;

        if (callback) {

          callback();
        }
      }

      image.src = url;

    }

    var readyCompleteTimer = null;

    function readyComplete() {

      utils.css.add(dom.node, css.ready);
      utils.css.add(dom.node, css.dropin);

      if (readyCompleteTimer) {
        window.clearTimeout(readyCompleteTimer);
      }

      readyCompleteTimer = window.setTimeout(function() {
        utils.css.remove(dom.node, css.dropin);
      }, 1000);

    }

    function ready() {

      window.setTimeout(function() {
        readyComplete();
      }, 1);

    }

    function init() {

      var i, j;

      sound = oOptions.sound;
      dom = {
        node: oOptions.node,
        canvas: oOptions.node.querySelectorAll('.connecting-tape'),
        reels: oOptions.node.querySelectorAll('div.reel'),
        spokes: oOptions.node.querySelectorAll('div.spokes'),
        label: oOptions.node.querySelectorAll('div.label'),
        maskImages: oOptions.node.querySelectorAll('.image-mask'),
        blurNode: oOptions.node.querySelectorAll('.blur-image')
      }

      if (dom.canvas && dom.canvas.length) {
        dom.canvas = dom.canvas[0];
        initMask(function() {
          setProgress(0, true);
        });
      } else {
        dom.canvas = null;
      }

      if (dom.maskImages) {
        for (i=0, j=dom.maskImages.length; i<j; i++) {
          createMaskedImage(dom.maskImages[i]);
        }
      }

      if (dom.blurNode && dom.blurNode.length) {
        dom.blurNode = dom.blurNode[0];
        createBlurImage(ready);
      } else {
        dom.blurNode = null;
        ready();
      }

      controlHandler = new ControlHandler(dom, sound);

    }

    var reelStatus = [{
      borderWidth: null
    }, {
      borderWidth: null
    }];

    function setReelSize(reelCount, reel, size) {

      var newFrame = 0;
      size = Math.min(1, Math.max(0, size));
      var borderWidth = Math.floor(borderMaxWidth*size);
      var margin;

      if (reelStatus[reelCount].borderWidth !== borderWidth) {

        reelStatus[reelCount].borderWidth = borderWidth;
        reel.style.borderWidth = borderWidth + 'px';
        margin = -(Math.floor(spoolWidth/2) + borderWidth) + 'px';
        reel.style.marginLeft = margin;
        reel.style.marginTop = margin;
        newFrame = 1;
      }

      return newFrame;

    }

    function deg2rad(degrees) {

      return (Math.PI/180)*degrees;

    }

    var tapeCache = {
      leftReel: {
        left: null
      },
      rightReel: {
        left: null
      }
    };

    function drawConnectingTape(canvas, progress, forceUpdate) {

      var leftReelRadius = borderMaxWidth - (borderMaxWidth * progress);
      var rightReelRadius = (borderMaxWidth * progress);
      var bottomTapeOffset = scaleHeight(0.998);
      var leftReel = {
        left: Math.floor(scaleWidth(0.29) - (spoolWidth/2) - leftReelRadius) + 4,
        top: scaleHeight(0.42)
      };

      var guideRadius = 16;
      var rightReel = {
        left: Math.floor(scaleWidth(0.71) + (spoolWidth/2) + rightReelRadius) - 3,
        top: scaleHeight(0.42)
      };

      var leftGuide = {
        left: scaleWidth(0.11) - guideRadius,
        top: bottomTapeOffset - guideRadius*2
      };

      var rightGuide = {
        left: scaleWidth(0.89) - guideRadius,
        top: bottomTapeOffset
      };

      var bottomLeftPoint = {
        from: [leftGuide.left, leftGuide.top],
        to: [leftReel.left, leftReel.top]
      };

      var bottomRightPoint = {
        from: [rightGuide.left, rightGuide.top],
        to: [rightReel.left, rightReel.top]
      };

      var bottomMidPoint = {
        left: tapeWidth * 0.5,
        top: bottomTapeOffset
      };

      if (!forceUpdate && tapeCache.leftReel.left === leftReel.left && tapeCache.rightReel.left === rightReel.left) {
        return false;
      }

      tapeCache.leftReel.left = leftReel.left;
      tapeCache.rightReel.left = rightReel.left;

      var context = canvas.getContext('2d');

      canvas.width = tapeWidth;
      canvas.height = tapeHeight;
      context.beginPath();

      context.moveTo(bottomMidPoint.left, bottomMidPoint.top);
      context.lineTo(bottomLeftPoint.from[0] + guideRadius, bottomLeftPoint.from[1] + guideRadius*2);
      context.arc(bottomLeftPoint.from[0] + guideRadius, bottomLeftPoint.from[1] + guideRadius, guideRadius, deg2rad(90), deg2rad(180), false);
      context.lineTo(leftReel.left, leftReel.top);
      context.lineWidth = 0.5;
      context.moveTo(bottomMidPoint.left, bottomMidPoint.top);
      context.lineTo(bottomRightPoint.from[0] + guideRadius, bottomRightPoint.from[1]);
      context.arc(bottomRightPoint.from[0] + guideRadius, bottomRightPoint.from[1] - guideRadius, guideRadius, deg2rad(90), deg2rad(0), true); // -30 on last for curve effect
      context.lineTo(rightReel.left, rightReel.top);
      context.lineWidth = 1;
      context.stroke();

      if (maskCanvas) {

        context.globalCompositeOperation = 'destination-out';
        context.drawImage(maskCanvas, 0, 0);
        context.globalCompositeOperation = null;

        if (maskCanvasLoaded && !maskVisible) {

          maskVisible = true;
          canvas.style.visibility = 'visible';
        }
      }
    }

    function setReelSpeed(reel, speed) {

      // base speed plus a given amount based on speed (multiplier?)
    }

    function applyProgress(progress) {

      var newFrames = 0;

      setReelSpeed(dom.reels[0], progress);
      newFrames += setReelSize(0, dom.reels[0], 1-progress);
      newFrames += setReelSize(1, dom.reels[1], progress);

      return newFrames;
    }

    function setProgress(progress, forceUpdate) {

      var newFrames = 0;
      forceUpdate = forceUpdate || false;
      data.progress = progress;
      newFrames = applyProgress(progress);
      if ((newFrames || forceUpdate) && dom.canvas) {

        drawConnectingTape(dom.canvas, progress, forceUpdate);
      }
    }

    function start() {

      utils.css.remove(dom.node, css.stopped);
      utils.css.add(dom.node, css.playing);

    }

    function stop() {

      utils.css.remove(dom.node, css.playing);
      utils.css.add(dom.node, css.stopped);

    }

    return {

      refreshBlurImage: function(callback) {
        utils.css.remove(dom.node, css.dropin);
        utils.css.remove(dom.node, css.ready);
        return createBlurImage(function() {
          utils.css.add(dom.node, css.dropin);
          utils.css.add(dom.node, css.ready);
          readyComplete();
          if (callback) {
            callback(this);
          }
        });
      },
      dom: dom,
      init: init,
      setProgress: setProgress,
      start: start,
      stop: stop
      
    };

  }

  var tapeUIs = [];

  function resetTapeUIs() {

    for (var i=tapeUIs.length; i--;) {
      tapeUIs[i].setProgress(0);
    }

  }

  var ignoreNextClick = false;

  var utils = (function() {

    var addEventHandler = (typeof window.addEventListener !== 'undefined' ? function(o, evtName, evtHandler) {
      return o.addEventListener(evtName,evtHandler,false);
    } : function(o, evtName, evtHandler) {
      o.attachEvent('on'+evtName,evtHandler);
    });

    var removeEventHandler = (typeof window.removeEventListener !== 'undefined' ? function(o, evtName, evtHandler) {
      return o.removeEventListener(evtName,evtHandler,false);
    } : function(o, evtName, evtHandler) {
      return o.detachEvent('on'+evtName,evtHandler);
    });

    var classContains = function(o,cStr) {
      return (typeof(o.className)!=='undefined'?o.className.match(new RegExp('(\s|^)'+cStr+'(\s|$)')):false);
    };

    var addClass = function(o,cStr) {
      if (!o || !cStr || classContains(o,cStr)) {
        return false;
      }
      o.className = (o.className?o.className+' ':'')+cStr;
    };

    var removeClass = function(o,cStr) {
      if (!o || !cStr || classContains(o,cStr)) {
        return false;
      }
      o.className = o.className.replace(new RegExp('( '+cStr+')|('+cStr+')','g'),'');
    };

    return {
      css: {
        has: classContains,
        add: addClass,
        remove: removeClass
      },
      events: {
        add: addEventHandler,
        remove: removeEventHandler
      }
    }

  }());

  function ControlHandler(tapeUIDOM, soundObject) {

    var soundObject;
    var css, dom, events, eventMap, soundOK;
    var rewind_ffwd_offset = 0.033;

    dom = {
      oControls: null,
      play: null,
      rew: null,
      ffwd: null,
      stop: null
    };

    events = {
      mousedown: function(e) {
        // need <a>
        var target = e.target,
            className = e.target.className;
        if (soundOK && typeof eventMap[className] !== 'undefined') {
          eventMap[className](e);
          return events.stopEvent(e);
        }
      },
      stopEvent: function(e) {
        e.preventDefault();
        return false;
      },
      click: function(e) {
        if (ignoreNextClick) {
          ignoreNextClick = false;
          return events.stopEvent(e);
        }
        var target = e.target,
            className = e.target.className;
        if (typeof eventMap[className] == 'undefined') {
          soundObject.togglePause();
        } else {
          return events.mousedown(e);
        }
      }
    };

    eventMap = {
      'play': function(e) {
        soundObject.play();
      },
      'rew': function() {
        var newPosition; 
        if (soundObject.duration) {
          newPosition = Math.max(0, soundObject.position - soundObject.duration * rewind_ffwd_offset);
          soundObject.setPosition(newPosition);
        }
      },
      'ffwd': function() {
        var newPosition;
        if (soundObject.duration) {
          newPosition = Math.min(soundObject.duration, soundObject.position + soundObject.duration * rewind_ffwd_offset);
          soundObject.setPosition(newPosition);
        }
      },
      'stop': function() {
        soundObject.pause();
      }
    };

    function addEvents() {
      utils.events.add(dom.o, 'mousedown', events.mousedown);
      utils.events.add(tapeUIDOM.node, 'click', events.click);
    }

    function init() {
      soundOK = soundManager.ok();
      dom.o = tapeUIDOM.node.querySelector('.controls');
      dom.play = dom.o.querySelector('.play');
      dom.rewind = dom.o.querySelector('.rew');
      dom.ffwd = dom.o.querySelector('.ffwd');
      dom.stop = dom.o.querySelector('.stop');
      addEvents();
    }

    init();

  }

  function init() {

    var hasCanvas = (typeof document.createElement('canvas').getContext !== 'undefined');

    if (!hasCanvas) {
      return false;
    }

    var tapes, i;
    
    tapes = document.querySelectorAll('div.tape');

    for (i=0; i < tapes.length; i++) {

      if (soundManager.ok()) {

        var tUi = new TapeUI({
          node: tapes[i],
          sound: soundManager.createSound({
            id: i,
            url: tapes[i].getAttribute('data-url'),
            multiShot: false,
            whileplaying: function() {
                tapeUIs[this.id].setProgress(this.position/this.durationEstimate);
            },
            onplay: function () {
                tapeUIs[this.id].start();
            },
            onfinish: function() {
              tapeUIs[this.id].start();
            },
            onpause: function() {
              tapeUIs[this.id].stop();
            },
            onresume: function() {

              tapeUIs[this.id].start();
            }
          })
        });

        tUi.init();
        tapeUIs.push(tUi);
      }
    }

    resetTapeUIs();

  }

  soundManager.setup({
    url: '/sm2/',
    flashVersion: 9,
    useHighPerformance: true,
    preferFlash: false,
    html5PollingInterval: 50,
    onready: init,
    ontimeout: init
  });

  window.tapeUIs = tapeUIs;

}(window));